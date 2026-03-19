<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing users
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run the user seeder first.');
            return;
        }

        $actions = [
            'login',
            'logout', 
            'failed_login',
            'create_user',
            'update_user',
            'delete_user',
            'view_dashboard',
            'view_reports',
            'export_data',
            'password_change',
            'permission_denied'
        ];

        $ipAddresses = [
            '192.168.1.100',
            '192.168.1.101', 
            '192.168.1.102',
            '10.0.0.1',
            '172.16.0.1',
            '127.0.0.1'
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ];

        // Create logs for the past 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Create 5-15 random activities per day
            $activitiesPerDay = rand(5, 15);
            
            for ($j = 0; $j < $activitiesPerDay; $j++) {
                $user = $users->random();
                $action = $actions[array_rand($actions)];
                $timestamp = $date->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59));
                
                // Determine success based on action
                $success = true;
                $errorMessage = null;
                
                if ($action === 'failed_login' || $action === 'permission_denied') {
                    $success = false;
                    $errorMessage = $action === 'failed_login' ? 'Invalid credentials' : 'Insufficient permissions';
                }

                $oldValues = null;
                $newValues = null;
                $tableAffected = null;
                $recordId = null;

                // Add some realistic data for certain actions
                if (str_contains($action, 'user')) {
                    $tableAffected = 'users';
                    $recordId = $user->userID;
                    
                    if ($action === 'update_user') {
                        $oldValues = [
                            'email' => $user->email,
                            'user_type' => $user->user_type
                        ];
                        $newValues = [
                            'email' => $user->email,
                            'user_type' => $user->user_type
                        ];
                    }
                }

                SecurityAuditLog::create([
                    'userID' => $user->userID,
                    'action' => $action,
                    'table_affected' => $tableAffected,
                    'record_id' => $recordId,
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'session_id' => 'sess_' . substr(md5(rand()), 0, 32),
                    'timestamp' => $timestamp,
                    'success' => $success,
                    'error_message' => $errorMessage
                ]);
            }
        }

        $this->command->info('Activity logs seeded successfully!');
    }
}
