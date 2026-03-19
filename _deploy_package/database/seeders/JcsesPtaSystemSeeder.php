<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JcsesPtaSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert default user roles
        DB::table('user_roles')->insert([
            [
                'role_name' => 'Administrator',
                'role_description' => 'Full system access with user management capabilities',
                'is_active' => true,
                'created_date' => now()
            ],
            [
                'role_name' => 'Principal',
                'role_description' => 'School leadership with oversight and reporting access',
                'is_active' => true,
                'created_date' => now()
            ],
            [
                'role_name' => 'Teacher',
                'role_description' => 'Limited administrative access for classroom-related functions',
                'is_active' => true,
                'created_date' => now()
            ],
            [
                'role_name' => 'Parent',
                'role_description' => 'Access to view projects and make contributions',
                'is_active' => true,
                'created_date' => now()
            ]
        ]);

        // Insert default permissions
        $permissions = [
            ['create_parent_account', 'Create new parent accounts', 'profile_management'],
            ['view_parent_profile', 'View parent profile information', 'profile_management'],
            ['edit_parent_profile', 'Edit parent profile information', 'profile_management'],
            ['enroll_student', 'Enroll new students', 'profile_management'],
            ['create_project', 'Create new PTA projects', 'project_management'],
            ['edit_project', 'Edit existing projects', 'project_management'],
            ['view_project_analytics', 'View project performance analytics', 'project_management'],
            ['process_payment', 'Process parent payments', 'payment_processing'],
            ['generate_receipt', 'Generate payment receipts', 'payment_processing'],
            ['view_financial_reports', 'View financial reports', 'payment_processing'],
            ['manage_user_accounts', 'Manage system user accounts', 'user_management'],
            ['view_security_logs', 'View security audit logs', 'user_management'],
            ['generate_reports', 'Generate system reports', 'reporting'],
            ['view_dashboard', 'View analytics dashboard', 'reporting']
        ];

        foreach ($permissions as $permission) {
            DB::table('user_permissions')->insert([
                'permission_name' => $permission[0],
                'permission_description' => $permission[1],
                'module_name' => $permission[2],
                'is_active' => true,
                'created_date' => now()
            ]);
        }

        // Insert default dashboard metrics
        $metrics = [
            ['Total Enrolled Students', 'enrollment', 0, 800, 'count', 'COUNT(studentID) WHERE enrollment_status = "active"'],
            ['Active Parent Accounts', 'enrollment', 0, 1200, 'count', 'COUNT(parentID) WHERE account_status = "active"'],
            ['Active PTA Projects', 'projects', 0, 10, 'count', 'COUNT(projectID) WHERE project_status IN ("active", "in_progress")'],
            ['Monthly Contributions', 'financial', 0, 50000, 'PHP', 'SUM(contribution_amount) WHERE contribution_date >= CURRENT_MONTH'],
            ['Parent Participation Rate', 'participation', 0, 75, 'percentage', 'Percentage of parents who made contributions in current period'],
            ['System Users', 'system', 0, 1220, 'count', 'COUNT(userID) WHERE is_active = TRUE']
        ];

        foreach ($metrics as $index => $metric) {
            DB::table('dashboard_metrics')->insert([
                'metric_name' => $metric[0],
                'metric_category' => $metric[1],
                'current_value' => $metric[2],
                'target_value' => $metric[3],
                'unit_of_measure' => $metric[4],
                'calculation_method' => $metric[5],
                'last_updated' => now(),
                'is_active' => true,
                'display_order' => $index
            ]);
        }
    }
}