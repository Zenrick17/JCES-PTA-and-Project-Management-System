<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display the teacher dashboard.
     */
    public function index()
    {
        // Get recent announcements (prioritize important, then 3 most recent)
        $recentAnnouncements = \App\Models\Announcement::with('creator')
            ->active()
            ->published()
            ->forAudience('teachers')
            ->orderByRaw("CASE WHEN category = 'important' THEN 0 ELSE 1 END")
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        // Get upcoming schedules for teachers
        $upcomingSchedules = \App\Models\Schedule::active()
            ->upcoming()
            ->forRole('teacher')
            ->orderBy('scheduled_date', 'asc')
            ->limit(3)
            ->get();

        // Get active/ongoing projects
        $activeProjects = \App\Models\Project::whereIn('project_status', ['created', 'active', 'in_progress'])
            ->orderBy('start_date', 'desc')
            ->limit(3)
            ->get();

        // Dashboard statistics
        $stats = [
            'myStudents' => 32, // This would come from a students table in production
            'activeParents' => \App\Models\User::where('user_type', 'parent')
                ->where('is_active', true)
                ->count(),
            'upcomingEvents' => \App\Models\Schedule::active()
                ->upcoming()
                ->forRole('teacher')
                ->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year)
                ->count(),
            'proposedProjects' => \App\Models\Project::where('project_status', 'created')
                ->whereMonth('created_date', now()->month)
                ->whereYear('created_date', now()->year)
                ->count(),
        ];

        return view('teacher.dashboard', compact('recentAnnouncements', 'upcomingSchedules', 'activeProjects', 'stats'));
    }

    /**
     * Show the create account form.
     */
    public function createAccount()
    {
        return view('teacher.create-account');
    }

    /**
     * Store a newly created account.
     */
    public function storeAccount(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'in:parent,teacher,administrator,principal'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Generate username from first name + last name + random number
        $baseUsername = strtolower($request->first_name . $request->last_name);
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        DB::transaction(function () use ($request, $username) {
            $user = User::create([
                'username' => $username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_type' => $request->role,
                'phone' => $request->phone,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'plain_password' => $request->password,
                'is_active' => true,
            ]);

            if ($user->user_type === 'parent') {
                ParentProfile::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'userID' => $user->userID,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'password_hash' => $user->password_hash,
                        'account_status' => 'active',
                    ]
                );
            }
        });

        return redirect()->route('teacher.create-account')->with('success', 'Account created successfully! Username: ' . $username);
    }

    /**
     * Display the users page.
     */
    public function users(Request $request)
    {
        $query = User::notArchived();

        // Only show parent accounts for teachers
        $query->where('user_type', 'parent');

        // Apply search filter - searches across name, email, phone, and address (for parents)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  // Search in parent profile for address fields
                  ->orWhereHas('parentProfile', function($pq) use ($search) {
                      $pq->where('street_address', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Order by created_date (newest first)
        $query->orderBy('created_date', 'desc');

        // Get per page value for users (default 10)
        $usersPerPage = $request->input('users_per_page', 10);
        $usersPerPage = in_array($usersPerPage, [10, 25, 50, 100]) ? $usersPerPage : 10;

        $users = $query->paginate($usersPerPage);

        return view('teacher.users', compact('users'));
    }

    /**
     * Delete (archive) a user.
     */
    public function deleteUser($id)
    {
        try {
            $user = User::where('userID', $id)->firstOrFail();

            // Teachers can only archive parent accounts
            if ($user->user_type !== 'parent') {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only archive parent accounts.'
                ], 403);
            }

            // Prevent archiving the currently logged-in user
            if ($user->userID === Auth::user()->userID) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot archive your own account.'
                ], 422);
            }

            // Store user data for logging before archiving
            $oldData = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_type' => $user->user_type,
                'is_active' => $user->is_active,
                'is_archived' => $user->is_archived
            ];

            // Archive the user instead of deleting
            $user->is_archived = true;
            $user->is_active = false;
            $user->save();

            // Log the user archive activity
            \App\Models\SecurityAuditLog::logActivity(
                Auth::user()->userID,
                'archive_user_account',
                'users',
                $id,
                $oldData,
                ['is_archived' => true, 'is_active' => false],
                true,
                null
            );

            return response()->json([
                'success' => true,
                'message' => 'User archived successfully!'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user information.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Teachers can only edit parent accounts
        if ($user->user_type !== 'parent') {
            return response()->json(['success' => false, 'message' => 'You can only edit parent accounts'], 403);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->userID, 'userID')],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $updateData['password_hash'] = Hash::make($request->password);
            $updateData['plain_password'] = $request->password;
        }

        $user->update($updateData);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    /**
     * Display the teacher announcements page
     */
    public function announcements(Request $request)
    {
        $filter = $request->get('filter');

        $query = \App\Models\Announcement::with('creator')
            ->active()
            ->published()
            ->forAudience('teachers');

        // Date filtering
        if ($filter === 'Today') {
            $query->whereDate('created_at', today());
        } elseif ($filter === 'This Week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'This Month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        // Prioritize important announcements
        $announcements = $query
            ->orderByRaw("CASE WHEN category = 'important' THEN 0 ELSE 1 END")
            ->orderBy('published_at', 'desc')
            ->get();

        // Get upcoming schedules for teachers
        $upcomingSchedules = \App\Models\Schedule::active()
            ->upcoming()
            ->forRole('teacher')
            ->orderBy('scheduled_date', 'asc')
            ->limit(3)
            ->get();

        return view('teacher.announcement.index', compact('announcements', 'upcomingSchedules'));
    }

    /**
     * Display the teacher payments page with manual payment capability
     */
    public function payments(Request $request)
    {
        // Get all active parent profiles with linked user accounts, sorted alphabetically
        $parents = \App\Models\ParentProfile::with('user')
            ->where('account_status', 'active')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->join('users', 'parents.userID', '=', 'users.userID')
            ->orderBy('users.last_name')
            ->orderBy('users.first_name')
            ->select('parents.*')
            ->get()
            ->map(function($parentProfile) {
                $accountEmail = $parentProfile->user->email ?? null;
                $profileEmail = $parentProfile->email ?? null;

                return (object)[
                    'parentID' => $parentProfile->parentID,
                    'first_name' => $parentProfile->user->first_name ?? ($parentProfile->first_name ?? ''),
                    'last_name' => $parentProfile->user->last_name ?? ($parentProfile->last_name ?? ''),
                    'email' => $accountEmail ?: ($profileEmail ?? ''),
                    'search_email' => trim(implode(' ', array_filter([$accountEmail, $profileEmail]))),
                ];
            });

        // Get payment history
        $query = \App\Models\ProjectContribution::with(['project', 'parent.user', 'processedBy'])
            ->orderBy('contribution_date', 'desc');

        // Apply filters if present
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('parent.user', function($pq) use ($search) {
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

        $contributions = $query->paginate(15);

        return view('teacher.payment.index', compact('parents', 'contributions'));
    }

    /**
     * Get unpaid bills for a specific parent
     */
    public function getParentBills($parentId)
    {
        $parentProfile = \App\Models\ParentProfile::findOrFail($parentId);

        // Get total number of active parents for calculating per-parent payment
        $totalParents = \App\Models\ParentProfile::where('account_status', 'active')->count();
        $totalParents = $totalParents > 0 ? $totalParents : 1;

        // Get active projects
        $projects = \App\Models\Project::whereIn('project_status', ['active', 'in_progress'])
            ->orderBy('project_name')
            ->get();

        $billItems = [];
        foreach ($projects as $project) {
            // Check if parent already has an open or completed payment for this project
            $existingPayment = \App\Models\ProjectContribution::where('projectID', $project->projectID)
                ->where('parentID', $parentId)
                ->whereIn('payment_status', ['pending', 'completed'])
                ->orderByDesc('contribution_date')
                ->first();

            // Hide fully completed bills from manual payment list
            if ($existingPayment && $existingPayment->payment_status === 'completed') {
                continue;
            }

            // Calculate per-parent amount
            $perParentAmount = round($project->target_budget / $totalParents, 2);

            $isPending = $existingPayment && $existingPayment->payment_status === 'pending';

            $billItems[] = [
                'projectID' => $project->projectID,
                'project_name' => $project->project_name,
                'amount' => $isPending ? (float) $existingPayment->contribution_amount : $perParentAmount,
                'is_pending' => $isPending,
                'status_note' => $isPending ? 'Pending - waiting for approval' : null,
            ];
        }

        return response()->json([
            'success' => true,
            'bills' => $billItems
        ]);
    }

    /**
     * Submit manual payment for a parent
     */
    public function submitManualPayment(Request $request)
    {
        abort_unless(Auth::user()?->user_type === 'teacher', 403, 'Teacher access only.');

        $validated = $request->validate([
            'parent_id' => ['required', 'exists:parents,parentID'],
            'project_ids' => ['required', 'array'],
            'project_ids.*' => ['exists:projects,projectID'],
            'amounts' => ['required', 'array'],
            'amounts.*' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,gcash,maya'],
            'notes' => ['nullable', 'string'],
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        if (count($validated['project_ids']) !== count($validated['amounts'])) {
            return response()->json([
                'success' => false,
                'message' => 'Project and amount counts do not match.'
            ], 422);
        }

        $invalidProjectIds = \App\Models\Project::whereIn('projectID', $validated['project_ids'])
            ->whereNotIn('project_status', ['active', 'in_progress'])
            ->pluck('projectID');

        if ($invalidProjectIds->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected projects are not open for manual payment processing.'
            ], 422);
        }

        $existingOpenPayments = \App\Models\ProjectContribution::with('project')
            ->where('parentID', $validated['parent_id'])
            ->whereIn('projectID', $validated['project_ids'])
            ->whereIn('payment_status', ['pending', 'completed'])
            ->get();

        if ($existingOpenPayments->isNotEmpty()) {
            $projectNames = $existingOpenPayments
                ->pluck('project.project_name')
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            return response()->json([
                'success' => false,
                'message' => 'Manual payment cannot be recorded for: ' . ($projectNames ?: 'selected project(s)') . '. A pending or completed payment already exists.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $contributions = [];

            foreach ($validated['project_ids'] as $index => $projectId) {
                // Generate receipt number
                $year = date('Y');
                $lastReceipt = \App\Models\ProjectContribution::where('receipt_number', 'like', "RCT-{$year}-%")
                    ->orderBy('receipt_number', 'desc')
                    ->first();

                if ($lastReceipt) {
                    $lastNumber = intval(substr($lastReceipt->receipt_number, -5));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $receiptNumber = sprintf("RCT-%s-%05d", $year, $newNumber);

                // Create contribution with completed status (teacher manually verified)
                $contribution = \App\Models\ProjectContribution::create([
                    'projectID' => $projectId,
                    'parentID' => $validated['parent_id'],
                    'contribution_amount' => $validated['amounts'][$index],
                    'payment_method' => $validated['payment_method'] === 'cash' ? 'cash' : 'bank_transfer',
                    'payment_status' => 'completed', // Directly mark as completed since teacher verified
                    'contribution_date' => now(),
                    'receipt_number' => $receiptNumber,
                    'notes' => $validated['notes'] ?? "Paid via " . strtoupper($validated['payment_method']) . ". Manual payment processed by teacher.",
                    'processed_by' => Auth::id(),
                ]);

                $contributions[] = $contribution;

                // Update project current amount
                $project = \App\Models\Project::find($projectId);
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
                'message' => 'Failed to process payment: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
