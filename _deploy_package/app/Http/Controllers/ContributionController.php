<?php

namespace App\Http\Controllers;

use App\Models\ParentProfile;
use App\Models\PaymentTransaction;
use App\Models\Project;
use App\Models\ProjectContribution;
use App\Mail\PaymentApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContributionController extends Controller
{
    private function resolveContributionsView(string $view): string
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->user_type === 'administrator') {
            return "administrator.payments.$view";
        }

        if ($user && $user->user_type === 'teacher') {
            return "teacher.payment.$view";
        }

        return "principal.contributions.$view";
    }

    public function index(Request $request)
    {
        // Get all active parent profiles for manual payment selection
        $parents = ParentProfile::with('user')
            ->where('account_status', 'active')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->join('users', 'parents.userID', '=', 'users.userID')
            ->orderBy('users.last_name')
            ->orderBy('users.first_name')
            ->select('parents.*')
            ->get();

        // Get payment history
        $query = ProjectContribution::with(['project', 'parent', 'processedBy'])
            ->orderBy('contribution_date', 'desc');

        // Apply filters if present
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('parent', function($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('project', function($prq) use ($search) {
                    $prq->where('project_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('payment_status', $request->status);
        }

        // Date range filter (for admin/principal)
        if ($request->has('date_range') && $request->date_range) {
            $now = now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('contribution_date', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('contribution_date', [
                        $now->startOfWeek()->toDateString(),
                        $now->copy()->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('contribution_date', $now->month)
                          ->whereYear('contribution_date', $now->year);
                    break;
                case 'this_year':
                    $query->whereYear('contribution_date', $now->year);
                    break;
            }
        }

        // School year filter (for admin/principal)
        if ($request->has('school_year') && $request->school_year) {
            $years = explode('-', $request->school_year);
            if (count($years) === 2) {
                $startYear = (int)$years[0];
                $endYear = (int)$years[1];
                $query->where(function($q) use ($startYear, $endYear) {
                    $q->whereYear('contribution_date', $startYear)
                      ->whereMonth('contribution_date', '>=', 6) // June onwards
                      ->orWhere(function($q2) use ($endYear) {
                          $q2->whereYear('contribution_date', $endYear)
                             ->whereMonth('contribution_date', '<=', 5); // Until May
                      });
                });
            }
        }

        // Calculate total contributions and amount
        $totalCount = (clone $query)->count();
        $totalAmount = (clone $query)->sum('contribution_amount');

        $contributions = $query->paginate(15);

        $projects = Project::orderBy('project_name')->get();
        $paymentMethods = ['cash', 'gcash', 'maya'];

        // Calculate payment per parent for each project (for admin/principal required column)
        $totalParents = ParentProfile::where('account_status', 'active')->count();
        $projectPayments = [];
        foreach ($projects as $project) {
            $paymentPerParent = $totalParents > 0 ? $project->target_budget / $totalParents : 0;
            $projectPayments[$project->projectID] = $paymentPerParent;
        }

        // Generate school year options (current and past 3 years)
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');
        $startSchoolYear = $currentMonth >= 6 ? $currentYear : $currentYear - 1;
        $schoolYears = [];
        for ($i = 0; $i < 4; $i++) {
            $sy = ($startSchoolYear - $i) . '-' . ($startSchoolYear - $i + 1);
            $schoolYears[] = $sy;
        }

        // Calculate statistics per active project
        $activeProjects = Project::whereIn('project_status', ['active', 'in_progress'])->get();
        $projectStats = [];
        foreach ($activeProjects as $project) {
            $projectQuery = ProjectContribution::where('projectID', $project->projectID);
            $projectStats[] = [
                'project_id' => $project->projectID,
                'project_name' => $project->project_name,
                'contribution_count' => $projectQuery->count(),
                'total_amount' => $projectQuery->sum('contribution_amount'),
                'target_budget' => $project->target_budget,
                'status' => $project->project_status,
            ];
        }

        return view($this->resolveContributionsView('index'), compact(
            'parents',
            'contributions',
            'projects',
            'paymentMethods',
            'totalCount',
            'totalAmount',
            'projectPayments',
            'schoolYears',
            'projectStats'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['required', 'integer'],
            'project_id' => ['required', 'integer'],
            'contribution_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,check,bank_transfer'],
            'contribution_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $receiptNumber = 'RCPT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $contributionDate = $validated['contribution_date'] ?? now();

        $contribution = ProjectContribution::create([
            'projectID' => $validated['project_id'],
            'parentID' => $validated['parent_id'],
            'contribution_amount' => $validated['contribution_amount'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'completed',
            'contribution_date' => $contributionDate,
            'receipt_number' => $receiptNumber,
            'notes' => $validated['notes'] ?? null,
            'processed_by' => Auth::user()->userID,
        ]);

        PaymentTransaction::create([
            'parentID' => $validated['parent_id'],
            'projectID' => $validated['project_id'],
            'contributionID' => $contribution->contributionID,
            'amount' => $validated['contribution_amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_status' => 'completed',
            'transaction_date' => $contributionDate,
            'receipt_number' => $receiptNumber,
            'reference_number' => null,
            'processed_by' => Auth::user()->userID,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, int $contributionID)
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:pending,completed,refunded'],
        ]);

        $contribution = ProjectContribution::where('contributionID', $contributionID)->firstOrFail();
        $previousStatus = $contribution->payment_status;
        $contribution->payment_status = $validated['payment_status'];

        if ($validated['payment_status'] === 'completed' && empty($contribution->receipt_number)) {
            $contribution->receipt_number = 'RCPT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        }

        $contribution->save();

        $transaction = PaymentTransaction::where('contributionID', $contributionID)->first();
        if ($transaction) {
            $transaction->transaction_status = $validated['payment_status'] === 'completed' ? 'completed' : $validated['payment_status'];
            $transaction->save();
        }

        if ($previousStatus !== 'completed' && $validated['payment_status'] === 'completed') {
            $contribution->loadMissing(['parent.user', 'project']);
            $recipientEmail = $contribution->parent?->email
                ?? $contribution->parent?->user?->email;

            if ($recipientEmail) {
                try {
                    Mail::to($recipientEmail)->send(new PaymentApproved($contribution));
                } catch (\Throwable $exception) {
                    Log::warning('Payment approval email failed to send.', [
                        'contributionID' => $contribution->contributionID,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->back();
    }

    public function receipt(int $contributionID)
    {
        $contribution = ProjectContribution::with(['project', 'parent', 'processedBy'])
            ->where('contributionID', $contributionID)
            ->firstOrFail();

        return view('receipts.print', compact('contribution'));
    }

    /**
     * Get unpaid bills for a specific parent (for manual payment)
     */
    public function getParentBills($parentId)
    {
        $parentProfile = ParentProfile::findOrFail($parentId);

        // Get total number of active parents for calculating per-parent payment
        $totalParents = ParentProfile::where('account_status', 'active')->count();
        $totalParents = $totalParents > 0 ? $totalParents : 1;

        // Get active projects only
        $projects = Project::whereIn('project_status', ['active', 'in_progress'])
            ->orderBy('project_name')
            ->get();

        $unpaidBills = [];
        foreach ($projects as $project) {
            // Check if parent has already paid for this project
            $existingPayment = ProjectContribution::where('projectID', $project->projectID)
                ->where('parentID', $parentId)
                ->where('payment_status', 'completed')
                ->first();

            // Skip if already paid
            if ($existingPayment) {
                continue;
            }

            // Calculate per-parent amount
            $perParentAmount = round($project->target_budget / $totalParents, 2);

            $unpaidBills[] = [
                'projectID' => $project->projectID,
                'project_name' => $project->project_name,
                'amount' => $perParentAmount,
            ];
        }

        return response()->json([
            'success' => true,
            'bills' => $unpaidBills
        ]);
    }

    /**
     * Submit manual payment for a parent
     */
    public function submitManualPayment(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['required', 'exists:parents,parentID'],
            'project_ids' => ['required', 'array'],
            'project_ids.*' => ['exists:projects,projectID'],
            'amounts' => ['required', 'array'],
            'payment_method' => ['required', 'in:cash,gcash,maya'],
            'notes' => ['nullable', 'string'],
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
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

                // Create contribution with completed status
                $contribution = ProjectContribution::create([
                    'projectID' => $projectId,
                    'parentID' => $validated['parent_id'],
                    'contribution_amount' => $validated['amounts'][$index],
                    'payment_method' => $validated['payment_method'] === 'cash' ? 'cash' : 'bank_transfer',
                    'payment_status' => 'completed',
                    'contribution_date' => now(),
                    'receipt_number' => $receiptNumber,
                    'notes' => $validated['notes'] ?? "Paid via " . strtoupper($validated['payment_method']) . ". Manual payment processed.",
                    'processed_by' => Auth::id(),
                ]);

                $contributions[] = $contribution;

                // Update project current amount
                $project = Project::find($projectId);
                if ($project) {
                    $project->current_amount += $validated['amounts'][$index];
                    $project->save();
                }
            }

            // Handle proof image upload
            if ($request->hasFile('proof_image')) {
                $image = $request->file('proof_image');
                $extension = $image->getClientOriginalExtension();

                // Use the first contribution's ID as the filename
                $firstContribution = $contributions[0];
                $filename = $firstContribution->contributionID . '.' . $extension;

                $image->storeAs('receipt_img', $filename, 'public');
            }

            DB::commit();

            foreach ($contributions as $contribution) {
                $contribution->loadMissing(['parent.user', 'project']);
                $recipientEmail = $contribution->parent?->email
                    ?? $contribution->parent?->user?->email;

                if (!$recipientEmail) {
                    continue;
                }

                try {
                    Mail::to($recipientEmail)->send(new PaymentApproved($contribution));
                } catch (\Throwable $exception) {
                    Log::warning('Manual payment email failed to send.', [
                        'contributionID' => $contribution->contributionID,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Manual payment processed successfully.',
                'receipt_number' => $contributions[0]->receipt_number ?? null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Manual payment submission failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
