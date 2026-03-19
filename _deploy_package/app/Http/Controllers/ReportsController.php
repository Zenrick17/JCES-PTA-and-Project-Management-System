<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectContribution;
use App\Models\PaymentTransaction;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Services\DashboardMetricService;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    private function resolveReportsView(string $view): string
    {
        $user = auth()->user();

        if ($user && $user->user_type === 'administrator') {
            return "administrator.reports.$view";
        }

        return "principal.reports.$view";
    }

    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Get recent activity stats
        $recentActivityCount = SecurityAuditLog::where('timestamp', '>=', now()->subDays(7))->count();
        $failedActionsCount = SecurityAuditLog::where('timestamp', '>=', now()->subDays(7))
            ->where('success', false)->count();
        $uniqueUsersCount = SecurityAuditLog::where('timestamp', '>=', now()->subDays(7))
            ->distinct('userID')->count();
        $topActions = SecurityAuditLog::getActionStats(7);

        $latestReconciliation = DB::table('financial_reconciliations as fr')
            ->leftJoin('users as u', 'u.userID', '=', 'fr.reconciled_by')
            ->select(
                'fr.reconciliation_period',
                'fr.start_date',
                'fr.end_date',
                'fr.reconciliation_status',
                'fr.reconciled_date',
                'fr.discrepancy_amount',
                DB::raw("CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as reconciled_by_name")
            )
            ->orderByDesc('fr.end_date')
            ->first();

        return view($this->resolveReportsView('index'), compact(
            'recentActivityCount',
            'failedActionsCount', 
            'uniqueUsersCount',
            'topActions',
            'latestReconciliation'
        ));
    }

    /**
     * Display payments and contribution history report.
     */
    public function paymentsReport(Request $request)
    {
        $payments = ProjectContribution::with(['parent', 'project'])
            ->orderBy('contribution_date', 'desc')
            ->paginate(9)
            ->appends($request->all());

        $recentContributions = ProjectContribution::with(['parent', 'project'])
            ->orderBy('contribution_date', 'desc')
            ->take(10)
            ->get();

        return view($this->resolveReportsView('payments'), compact(
            'payments',
            'recentContributions'
        ));
    }

    /**
     * Display user activity logs.
     */
    public function activityLogs(Request $request)
    {
        $filters = $request->only(['user_id', 'action', 'date_from', 'date_to', 'ip_address', 'success']);
        $logs = SecurityAuditLog::getActivityLogs($filters, 20);
        
        // Get all users for filter dropdown
        $users = User::select('userID', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();

        return view($this->resolveReportsView('activity-logs'), compact('logs', 'users', 'filters'));
    }

    /**
     * Display security logs.
     */
    public function securityLogs(Request $request)
    {
        $filters = $request->only(['user_id', 'action', 'date_from', 'date_to', 'ip_address']);
        
        // Security-specific filters - focus on authentication and authorization events
        $securityActions = ['login', 'logout', 'failed_login', 'password_change', 'account_locked', 'permission_denied'];
        
        $query = SecurityAuditLog::with('user')
            ->whereIn('action', $securityActions)
            ->selectRaw('MAX(logID) as logID, userID, action, ip_address, session_id, timestamp, success, error_message')
            ->groupBy('userID', 'action', 'ip_address', 'session_id', 'timestamp', 'success', 'error_message')
            ->orderBy('timestamp', 'desc');

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('userID', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', 'like', '%' . $filters['action'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('timestamp', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('timestamp', '<=', $filters['date_to']);
        }

        if (!empty($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        $logs = $query->paginate(20);
        
        // Get all users for filter dropdown
        $users = User::select('userID', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();

        return view($this->resolveReportsView('security-logs'), compact('logs', 'users', 'filters'));
    }

    /**
     * Display user activity statistics.
     */
    public function userActivity(Request $request)
    {
        $days = $request->get('days', 30);
        
        // Get user activity statistics
        $userStats = SecurityAuditLog::getUserActivityStats($days);
        $actionStats = SecurityAuditLog::getActionStats($days);
        
        // Get daily activity data for chart
        $dailyActivity = SecurityAuditLog::where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('DATE(timestamp) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get login patterns
        $loginPatterns = SecurityAuditLog::where('action', 'login')
            ->where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('HOUR(timestamp) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view($this->resolveReportsView('user-activity'), compact(
            'userStats',
            'actionStats', 
            'dailyActivity',
            'loginPatterns',
            'days'
        ));
    }

    /**
     * Display enrollment statistics.
     */
    public function enrollmentStats(Request $request)
    {
        $academicYear = $request->get('academic_year');

        $studentsQuery = Student::query();
        if (!empty($academicYear)) {
            $studentsQuery->where('academic_year', $academicYear);
        }

        $totalStudents = (clone $studentsQuery)->count();
        $activeStudents = (clone $studentsQuery)->where('enrollment_status', 'active')->count();
        $activeRate = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 2) : 0;

        $enrollmentByYear = Student::selectRaw('academic_year, COUNT(*) as total')
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'desc')
            ->get();

        $enrollmentByGrade = Student::selectRaw('grade_level, COUNT(*) as total')
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->get();

        $academicYears = Student::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return view($this->resolveReportsView('enrollment'), compact(
            'totalStudents',
            'activeStudents',
            'activeRate',
            'enrollmentByYear',
            'enrollmentByGrade',
            'academicYears',
            'academicYear'
        ));
    }

    /**
     * Display parent participation report.
     */
    public function participationReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $projectId = $request->get('project_id');

        $contributionsQuery = ProjectContribution::with(['project', 'parent'])
            ->whereBetween('contribution_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('payment_status', 'completed');

        if (!empty($projectId)) {
            $contributionsQuery->where('projectID', $projectId);
        }

        $totalContributionAmount = (clone $contributionsQuery)->sum('contribution_amount');
        $totalContributionCount = (clone $contributionsQuery)->count();
        $uniqueParents = (clone $contributionsQuery)->distinct('parentID')->count('parentID');

        $contributions = $contributionsQuery
            ->orderBy('contribution_date', 'desc')
            ->paginate(20)
            ->appends($request->all());

        $projects = Project::orderBy('project_name')->get();

        return view($this->resolveReportsView('participation'), compact(
            'contributions',
            'projects',
            'dateFrom',
            'dateTo',
            'projectId',
            'totalContributionAmount',
            'totalContributionCount',
            'uniqueParents'
        ));
    }

    /**
     * Display project analytics.
     */
    public function projectAnalytics(Request $request)
    {
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $projectsQuery = Project::query();

        if (!empty($status)) {
            $projectsQuery->where('project_status', $status);
        }

        if (!empty($dateFrom)) {
            $projectsQuery->whereDate('start_date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $projectsQuery->whereDate('start_date', '<=', $dateTo);
        }

        $totalProjects = (clone $projectsQuery)->count();
        $completedProjects = (clone $projectsQuery)->where('project_status', 'completed')->count();
        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0;
        $totalTargetBudget = (clone $projectsQuery)->sum('target_budget');
        $totalCurrentAmount = (clone $projectsQuery)->sum('current_amount');

        $projects = $projectsQuery
            ->orderBy('start_date', 'desc')
            ->paginate(15)
            ->appends($request->all());

        $statusOptions = ['created', 'active', 'in_progress', 'completed', 'archived', 'cancelled'];

        return view($this->resolveReportsView('project-analytics'), compact(
            'projects',
            'totalProjects',
            'completedProjects',
            'completionRate',
            'totalTargetBudget',
            'totalCurrentAmount',
            'status',
            'dateFrom',
            'dateTo',
            'statusOptions'
        ));
    }

    /**
     * Display financial summary report.
     */
    public function financialSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $paymentMethod = $request->get('payment_method');

        $transactionsQuery = PaymentTransaction::with(['project', 'parent'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('transaction_status', 'completed');

        if (!empty($paymentMethod)) {
            $transactionsQuery->where('payment_method', $paymentMethod);
        }

        $totalAmount = (clone $transactionsQuery)->sum('amount');
        $totalTransactions = (clone $transactionsQuery)->count();

        $methodTotals = PaymentTransaction::selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('transaction_status', 'completed')
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get();

        $projectTotals = PaymentTransaction::select('projectID', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('transaction_status', 'completed')
            ->groupBy('projectID')
            ->orderBy('total', 'desc')
            ->get();
        $projectTotals->load('project');

        $transactions = $transactionsQuery
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->appends($request->all());

        $paymentMethods = ['cash', 'check', 'bank_transfer'];

        return view($this->resolveReportsView('financial-summary'), compact(
            'transactions',
            'dateFrom',
            'dateTo',
            'paymentMethod',
            'totalAmount',
            'totalTransactions',
            'methodTotals',
            'projectTotals',
            'paymentMethods'
        ));
    }

    /**
     * Display KPI dashboard.
     */
    public function dashboardMetrics(Request $request, DashboardMetricService $dashboardMetricService)
    {
        $days = (int) $request->get('days', 30);
        $metrics = $dashboardMetricService->getKpis($days);

        return view($this->resolveReportsView('kpi-dashboard'), compact('metrics', 'days'));
    }

    /**
     * Export activity logs.
     */
    public function exportLogs(Request $request)
    {
        $filters = $request->only(['user_id', 'action', 'date_from', 'date_to', 'ip_address', 'success']);
        
        $logs = SecurityAuditLog::with('user')
            ->when(!empty($filters['user_id']), function ($query) use ($filters) {
                return $query->where('userID', $filters['user_id']);
            })
            ->when(!empty($filters['action']), function ($query) use ($filters) {
                return $query->where('action', 'like', '%' . $filters['action'] . '%');
            })
            ->when(!empty($filters['date_from']), function ($query) use ($filters) {
                return $query->whereDate('timestamp', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($query) use ($filters) {
                return $query->whereDate('timestamp', '<=', $filters['date_to']);
            })
            ->when(!empty($filters['ip_address']), function ($query) use ($filters) {
                return $query->where('ip_address', $filters['ip_address']);
            })
            ->when(!empty($filters['success']), function ($query) use ($filters) {
                return $query->where('success', $filters['success'] === 'true');
            })
            ->orderBy('timestamp', 'desc')
            ->limit(10000) // Limit for performance
            ->get();

        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Timestamp',
                'User',
                'Action', 
                'Table Affected',
                'Record ID',
                'IP Address',
                'User Agent',
                'Success',
                'Error Message'
            ]);

            // Add data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->timestamp->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User',
                    $log->action,
                    $log->table_affected,
                    $log->record_id,
                    $log->ip_address,
                    $log->user_agent,
                    $log->success ? 'Yes' : 'No',
                    $log->error_message
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export financial transactions.
     */
    public function exportFinancialSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $paymentMethod = $request->get('payment_method');

        $transactions = PaymentTransaction::with(['project', 'parent'])
            ->whereBetween('transaction_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('transaction_status', 'completed')
            ->when(!empty($paymentMethod), function ($query) use ($paymentMethod) {
                return $query->where('payment_method', $paymentMethod);
            })
            ->orderBy('transaction_date', 'desc')
            ->limit(10000)
            ->get();

        $filename = 'financial_summary_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Transaction Date',
                'Parent',
                'Project',
                'Amount',
                'Payment Method',
                'Receipt Number',
                'Status',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    optional($transaction->transaction_date)->format('Y-m-d H:i:s'),
                    $transaction->parent ? $transaction->parent->first_name . ' ' . $transaction->parent->last_name : 'Unknown Parent',
                    $transaction->project ? $transaction->project->project_name : 'Unknown Project',
                    $transaction->amount,
                    $transaction->payment_method,
                    $transaction->receipt_number,
                    $transaction->transaction_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
