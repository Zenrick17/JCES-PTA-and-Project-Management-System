<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectContribution;
use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentContributionController extends Controller
{
    /**
     * Display the parent's contribution history.
     */
    public function index(Request $request)
    {
        $parentProfile = Auth::user()->parentProfile;

        if (!$parentProfile) {
            return redirect()->route('dashboard')
                ->with('error', 'Parent profile not found. Please contact administrator.');
        }

        $query = ProjectContribution::with(['project', 'processedBy'])
            ->where('parentID', $parentProfile->parentID)
            ->orderBy('contribution_date', 'desc');

        // Filter by payment status
        if ($request->has('status') && $request->status) {
            $query->where('payment_status', $request->status);
        }

        // Search by project name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('project', function($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%");
            });
        }

        $contributions = $query->paginate(15);

        // Calculate statistics
        $stats = [
            'total_contributions' => ProjectContribution::where('parentID', $parentProfile->parentID)
                ->where('payment_status', 'completed')
                ->count(),
            'total_amount' => ProjectContribution::where('parentID', $parentProfile->parentID)
                ->where('payment_status', 'completed')
                ->sum('contribution_amount'),
            'pending_amount' => ProjectContribution::where('parentID', $parentProfile->parentID)
                ->where('payment_status', 'pending')
                ->sum('contribution_amount'),
            'projects_supported' => ProjectContribution::where('parentID', $parentProfile->parentID)
                ->distinct('projectID')
                ->count('projectID'),
        ];

        return view('parent.contributions.index', compact('contributions', 'stats'));
    }

    /**
     * Show the form for creating a new contribution.
     */
    public function create()
    {
        $parentProfile = Auth::user()->parentProfile;

        if (!$parentProfile) {
            return redirect()->route('dashboard')
                ->with('error', 'Parent profile not found. Please contact administrator.');
        }

        // Get active projects that can receive contributions
        $projects = Project::whereIn('project_status', ['active', 'in_progress'])
            ->orderBy('project_name')
            ->get();

        // Calculate progress for each project
        foreach ($projects as $project) {
            $project->progress_percentage = $project->target_budget > 0
                ? min(100, round(($project->current_amount / $project->target_budget) * 100, 1))
                : 0;
            $project->remaining_amount = max(0, $project->target_budget - $project->current_amount);
        }

        return view('parent.contributions.create', compact('projects'));
    }

    /**
     * Store a newly created contribution in storage.
     */
    public function store(Request $request)
    {
        $parentProfile = Auth::user()->parentProfile;

        if (!$parentProfile) {
            return redirect()->route('dashboard')
                ->with('error', 'Parent profile not found. Please contact administrator.');
        }

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,projectID'],
            'contribution_amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,check,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Verify project is active
        $project = Project::whereIn('project_status', ['active', 'in_progress'])
            ->findOrFail($validated['project_id']);

        DB::beginTransaction();

        try {
            // Generate receipt number
            $year = date('Y');
            $lastReceipt = ProjectContribution::where('receipt_number', 'like', "RCT-{$year}-%")
                ->orderBy('receipt_number', 'desc')
                ->first();

            if ($lastReceipt) {
                $lastNumber = intval(substr($lastReceipt->receipt_number, -5));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $receiptNumber = sprintf("RCT-%s-%05d", $year, $newNumber);

            // Create contribution with pending status (requires admin verification)
            $contribution = ProjectContribution::create([
                'projectID' => $validated['project_id'],
                'parentID' => $parentProfile->parentID,
                'contribution_amount' => $validated['contribution_amount'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending', // Requires admin verification
                'contribution_date' => now(),
                'receipt_number' => $receiptNumber,
                'notes' => $validated['notes'],
                'processed_by' => null, // Will be set when admin verifies
            ]);

            DB::commit();

            return redirect()->route('parent.contributions.index')
                ->with('success', 'Contribution submitted successfully. It will be reviewed by the administrator.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to submit contribution. Please try again.');
        }
    }

    /**
     * Display the specified contribution receipt.
     */
    public function receipt($contributionID)
    {
        $parentProfile = Auth::user()->parentProfile;

        if (!$parentProfile) {
            abort(403, 'Unauthorized access.');
        }

        $contribution = ProjectContribution::with(['project', 'parent', 'processedBy'])
            ->where('parentID', $parentProfile->parentID)
            ->findOrFail($contributionID);

        return view('receipts.print', compact('contribution'));
    }

    /**
     * Display the parent payment page with projects requiring payment.
     */
    public function paymentIndex()
    {
        $parentProfile = Auth::user()->parentProfile;

        if (!$parentProfile) {
            // Auto-create parent profile using User data if it doesn't exist
            $user = Auth::user();
            $parentProfile = ParentProfile::create([
                'userID' => $user->userID,
                'first_name' => $user->first_name ?? 'Parent',
                'last_name' => $user->last_name ?? 'User',
                'email' => $user->email ?? 'parent' . $user->userID . '@example.com',
                'phone' => $user->phone ?? '0000000000',
                'password_hash' => $user->password_hash,
                'account_status' => 'active',
            ]);
        }

        // Get total number of active parents for calculating per-parent payment
        $totalParents = ParentProfile::where('account_status', 'active')->count();

        // Default to 1 to avoid division by zero
        $totalParents = $totalParents > 0 ? $totalParents : 1;

        // Get active projects
        $projects = Project::whereIn('project_status', ['active', 'in_progress'])
            ->orderBy('project_name')
            ->get();

        // Calculate per-parent payment for each project
        $paymentItems = [];
        foreach ($projects as $project) {
            // Check if parent has already paid for this project (completed payment)
            $existingPayment = ProjectContribution::where('projectID', $project->projectID)
                ->where('parentID', $parentProfile->parentID)
                ->where('payment_status', 'completed')
                ->first();

            // Check for pending payments
            $pendingPayment = ProjectContribution::where('projectID', $project->projectID)
                ->where('parentID', $parentProfile->parentID)
                ->where('payment_status', 'pending')
                ->first();

            // Calculate per-parent amount (total budget / number of parents)
            $perParentAmount = round($project->target_budget / $totalParents, 2);

            $paymentItems[] = [
                'project' => $project,
                'amount' => $perParentAmount,
                'is_paid' => $existingPayment !== null,
                'is_pending' => $pendingPayment !== null,
                'paid_amount' => $existingPayment ? $existingPayment->contribution_amount : 0,
                'pending_amount' => $pendingPayment ? $pendingPayment->contribution_amount : 0,
            ];
        }

        // Filter to only show unpaid/pending items
        $unpaidItems = array_filter($paymentItems, function($item) {
            return !$item['is_paid'];
        });

        return view('parent.payment.index', [
            'paymentItems' => $unpaidItems,
            'allItems' => $paymentItems,
            'totalParents' => $totalParents,
            'parentProfile' => $parentProfile,
        ]);
    }

    /**
     * Submit payment with receipt image upload.
     */
    public function submitPayment(Request $request)
    {
        $parentProfile = Auth::user()->parentProfile;

        if (!$parentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found.'
            ], 403);
        }

        $validated = $request->validate([
            'project_ids' => ['required', 'array'],
            'project_ids.*' => ['exists:projects,projectID'],
            'amounts' => ['required', 'array'],
            'payment_method' => ['required', 'in:gcash,maya'],
            'name' => ['required', 'string'],
            'contact' => ['required', 'string'],
            'address' => ['required', 'string'],
            'receipt_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        DB::beginTransaction();

        try {
            $contributions = [];

            foreach ($validated['project_ids'] as $index => $projectId) {
                // Generate receipt number
                $year = date('Y');
                $lastReceipt = ProjectContribution::where('receipt_number', 'like', "RCT-{$year}-%")
                    ->orderBy('receipt_number', 'desc')
                    ->first();

                if ($lastReceipt) {
                    $lastNumber = intval(substr($lastReceipt->receipt_number, -5));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $receiptNumber = sprintf("RCT-%s-%05d", $year, $newNumber);

                // Create contribution with pending status
                $contribution = ProjectContribution::create([
                    'projectID' => $projectId,
                    'parentID' => $parentProfile->parentID,
                    'contribution_amount' => $validated['amounts'][$index],
                    'payment_method' => $validated['payment_method'] === 'gcash' ? 'bank_transfer' : 'bank_transfer',
                    'payment_status' => 'pending',
                    'contribution_date' => now(),
                    'receipt_number' => $receiptNumber,
                    'notes' => "Paid via " . strtoupper($validated['payment_method']) . ". Contact: {$validated['contact']}, Address: {$validated['address']}",
                    'processed_by' => null,
                ]);

                $contributions[] = $contribution;
            }

            // Handle receipt image upload - save with contribution ID as filename
            if ($request->hasFile('receipt_image')) {
                $image = $request->file('receipt_image');
                $extension = $image->getClientOriginalExtension();

                // Use the first contribution's ID as the filename
                $firstContribution = $contributions[0];
                $filename = $firstContribution->contributionID . '.' . $extension;

                $image->storeAs('receipt_img', $filename, 'public');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment submitted successfully. It will be reviewed by the administrator.',
                'receipt_number' => $contributions[0]->receipt_number ?? null,
                'contribution_id' => $contributions[0]->contributionID ?? null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit payment. Please try again. ' . $e->getMessage()
            ], 500);
        }
    }
}
