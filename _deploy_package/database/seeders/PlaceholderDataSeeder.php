<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlaceholderDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users or create if needed
        $userIds = [];

        // Get or create Teacher
        $teacher = DB::table('users')->where('email', 'teacher@jcses.edu.ph')->first();
        if (!$teacher) {
            $userIds['teacher'] = DB::table('users')->insertGetId([
                'username' => 'rsantos',
                'first_name' => 'Roberto',
                'last_name' => 'Santos',
                'email' => 'teacher@jcses.edu.ph',
                'phone' => '09171234567',
                'password_hash' => Hash::make('password'),
                'user_type' => 'teacher',
                'is_active' => true,
                'created_date' => now(),
            ]);
        } else {
            $userIds['teacher'] = $teacher->userID;
        }

        // Get or create Principal
        $principal = DB::table('users')->where('email', 'principal@jcses.edu.ph')->first();
        if (!$principal) {
            $userIds['principal'] = DB::table('users')->insertGetId([
                'username' => 'etorres',
                'first_name' => 'Elena',
                'last_name' => 'Torres',
                'email' => 'principal@jcses.edu.ph',
                'phone' => '09215678901',
                'password_hash' => Hash::make('password'),
                'user_type' => 'principal',
                'is_active' => true,
                'created_date' => now(),
            ]);
        } else {
            $userIds['principal'] = $principal->userID;
        }

        // Get existing parents
        $parent1 = DB::table('parents')->where('email', 'parent1@gmail.com')->first();
        $parent2 = DB::table('parents')->where('email', 'parent2@gmail.com')->first();
        $parent3 = DB::table('parents')->where('email', 'parent3@gmail.com')->first();

        $parentIds = [];

        if (!$parent1) {
            // Create parent 1
            $userId1 = DB::table('users')->insertGetId([
                'username' => 'creyes',
                'first_name' => 'Carmen',
                'last_name' => 'Reyes',
                'email' => 'parent1@gmail.com',
                'phone' => '09182345678',
                'password_hash' => Hash::make('password'),
                'user_type' => 'parent',
                'is_active' => true,
                'created_date' => now(),
            ]);

            $parentIds[0] = DB::table('parents')->insertGetId([
                'first_name' => 'Carmen',
                'last_name' => 'Reyes',
                'email' => 'parent1@gmail.com',
                'phone' => '09182345678',
                'street_address' => '456 Main Avenue',
                'city' => 'Quezon City',
                'barangay' => 'Barangay 1',
                'zipcode' => '1100',
                'password_hash' => Hash::make('password'),
                'account_status' => 'active',
                'userID' => $userId1,
                'created_date' => now(),
            ]);
        } else {
            $parentIds[0] = $parent1->parentID;
        }

        if (!$parent2) {
            // Create parent 2
            $userId2 = DB::table('users')->insertGetId([
                'username' => 'mfernandez',
                'first_name' => 'Miguel',
                'last_name' => 'Fernandez',
                'email' => 'parent2@gmail.com',
                'phone' => '09193456789',
                'password_hash' => Hash::make('password'),
                'user_type' => 'parent',
                'is_active' => true,
                'created_date' => now(),
            ]);

            $parentIds[1] = DB::table('parents')->insertGetId([
                'first_name' => 'Miguel',
                'last_name' => 'Fernandez',
                'email' => 'parent2@gmail.com',
                'phone' => '09193456789',
                'street_address' => '789 Sunset Boulevard',
                'city' => 'Manila',
                'barangay' => 'Barangay 2',
                'zipcode' => '1000',
                'password_hash' => Hash::make('password'),
                'account_status' => 'active',
                'userID' => $userId2,
                'created_date' => now(),
            ]);
        } else {
            $parentIds[1] = $parent2->parentID;
        }

        if (!$parent3) {
            // Create parent 3
            $userId3 = DB::table('users')->insertGetId([
                'username' => 'smendoza',
                'first_name' => 'Sofia',
                'last_name' => 'Mendoza',
                'email' => 'parent3@gmail.com',
                'phone' => '09204567890',
                'password_hash' => Hash::make('password'),
                'user_type' => 'parent',
                'is_active' => true,
                'created_date' => now(),
            ]);

            $parentIds[2] = DB::table('parents')->insertGetId([
                'first_name' => 'Sofia',
                'last_name' => 'Mendoza',
                'email' => 'parent3@gmail.com',
                'phone' => '09204567890',
                'street_address' => '321 Park Lane',
                'city' => 'Makati',
                'barangay' => 'Barangay 3',
                'zipcode' => '1200',
                'password_hash' => Hash::make('password'),
                'account_status' => 'active',
                'userID' => $userId3,
                'created_date' => now(),
            ]);
        } else {
            $parentIds[2] = $parent3->parentID;
        }

        // Create Projects
        $projectIds = [];

        $projectIds[0] = DB::table('projects')->insertGetId([
            'project_name' => 'School Library Renovation',
            'description' => 'Renovation and improvement of the school library facilities',
            'goals' => 'Modernize library space and add more reading materials',
            'target_budget' => 50000.00,
            'current_amount' => 0,
            'start_date' => '2025-09-01',
            'target_completion_date' => '2026-06-30',
            'project_status' => 'active',
            'created_by' => $userIds['principal'],
            'created_date' => now(),
            'updated_date' => now(),
        ]);

        $projectIds[1] = DB::table('projects')->insertGetId([
            'project_name' => 'Computer Laboratory Equipment',
            'description' => 'Purchase of new computers and equipment for the computer laboratory',
            'goals' => 'Upgrade computer lab with modern equipment',
            'target_budget' => 75000.00,
            'current_amount' => 0,
            'start_date' => '2025-10-01',
            'target_completion_date' => '2026-05-31',
            'project_status' => 'active',
            'created_by' => $userIds['principal'],
            'created_date' => now(),
            'updated_date' => now(),
        ]);

        $projectIds[2] = DB::table('projects')->insertGetId([
            'project_name' => 'Sports Equipment Fund',
            'description' => 'Acquisition of sports equipment for physical education classes',
            'goals' => 'Provide quality sports equipment for students',
            'target_budget' => 30000.00,
            'current_amount' => 0,
            'start_date' => '2025-11-01',
            'target_completion_date' => '2026-04-30',
            'project_status' => 'active',
            'created_by' => $userIds['principal'],
            'created_date' => now(),
            'updated_date' => now(),
        ]);

        // Create Contributions and Payments
        $payments = [
            ['parent' => 0, 'project' => 0, 'amount' => 1500, 'status' => 'completed', 'date' => '2025-10-15'],
            ['parent' => 1, 'project' => 0, 'amount' => 800, 'status' => 'pending', 'date' => '2025-10-20'],
            ['parent' => 2, 'project' => 0, 'amount' => 1200, 'status' => 'pending', 'date' => '2025-10-25'],
            ['parent' => 0, 'project' => 1, 'amount' => 2000, 'status' => 'completed', 'date' => '2025-11-05'],
            ['parent' => 1, 'project' => 1, 'amount' => 950, 'status' => 'pending', 'date' => '2025-11-10'],
            ['parent' => 2, 'project' => 1, 'amount' => 1100, 'status' => 'pending', 'date' => '2025-11-15'],
            ['parent' => 0, 'project' => 2, 'amount' => 1800, 'status' => 'completed', 'date' => '2025-12-01'],
            ['parent' => 1, 'project' => 2, 'amount' => 650, 'status' => 'pending', 'date' => '2025-12-05'],
            ['parent' => 2, 'project' => 2, 'amount' => 900, 'status' => 'pending', 'date' => '2025-12-10'],
        ];

        foreach ($payments as $payment) {
            $parentId = $parentIds[$payment['parent']];
            $projectId = $projectIds[$payment['project']];
            $receiptNum = 'REC-' . strtoupper(uniqid());

            // Create contribution first
            $contributionId = DB::table('project_contributions')->insertGetId([
                'projectID' => $projectId,
                'parentID' => $parentId,
                'contribution_amount' => $payment['amount'],
                'payment_method' => 'cash',
                'payment_status' => $payment['status'],
                'contribution_date' => $payment['date'],
                'receipt_number' => $receiptNum,
                'processed_by' => $userIds['teacher'],
            ]);

            // Create payment transaction
            DB::table('payment_transactions')->insert([
                'parentID' => $parentId,
                'projectID' => $projectId,
                'contributionID' => $contributionId,
                'amount' => $payment['amount'],
                'payment_method' => 'cash',
                'transaction_status' => $payment['status'],
                'transaction_date' => $payment['date'],
                'receipt_number' => $receiptNum,
                'reference_number' => 'REF-' . strtoupper(uniqid()),
                'processed_by' => $userIds['teacher'],
            ]);

            // Update project current amount if payment is completed
            if ($payment['status'] === 'completed') {
                DB::table('projects')
                    ->where('projectID', $projectId)
                    ->increment('current_amount', $payment['amount']);
            }
        }

        $this->command->info('✓ Placeholder data seeded successfully!');
        $this->command->info('  - Created/Used ' . count($parentIds) . ' parent profiles');
        $this->command->info('  - Created ' . count($projectIds) . ' projects');
        $this->command->info('  - Created ' . count($payments) . ' payment transactions');
    }
}

