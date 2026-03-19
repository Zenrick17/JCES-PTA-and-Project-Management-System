<?php

namespace App\Services;

use App\Models\ParentProfile;
use App\Models\PaymentTransaction;
use App\Models\Project;
use App\Models\ProjectContribution;
use App\Models\Student;

class DashboardMetricService
{
    public function getKpis(int $days = 30): array
    {
        $dateFrom = now()->subDays($days);

        $totalStudents = Student::count();
        $activeStudents = Student::where('enrollment_status', 'active')->count();
        $activeRate = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 2) : 0;

        $totalParents = ParentProfile::where('account_status', 'active')->count();
        $participatingParents = ProjectContribution::where('payment_status', 'completed')
            ->where('contribution_date', '>=', $dateFrom)
            ->distinct('parentID')
            ->count('parentID');
        $participationRate = $totalParents > 0 ? round(($participatingParents / $totalParents) * 100, 2) : 0;

        $totalContributions = ProjectContribution::where('payment_status', 'completed')
            ->where('contribution_date', '>=', $dateFrom)
            ->sum('contribution_amount');

        $totalPayments = PaymentTransaction::where('transaction_status', 'completed')
            ->where('transaction_date', '>=', $dateFrom)
            ->sum('amount');

        $totalProjects = Project::count();
        $activeProjects = Project::whereIn('project_status', ['active', 'in_progress'])->count();
        $completedProjects = Project::where('project_status', 'completed')->count();
        $projectCompletionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0;

        return [
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'activeRate' => $activeRate,
            'totalParents' => $totalParents,
            'participationRate' => $participationRate,
            'totalContributions' => $totalContributions,
            'totalPayments' => $totalPayments,
            'activeProjects' => $activeProjects,
            'projectCompletionRate' => $projectCompletionRate,
        ];
    }
}
