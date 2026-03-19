<?php

namespace Database\Seeders;

use App\Models\ParentProfile;
use App\Models\Project;
use App\Models\ProjectContribution;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaymentSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing parents and projects
        $parents = ParentProfile::all();
        $projects = Project::all();

        if ($parents->isEmpty()) {
            $this->command->info('No parents found. Creating sample parents...');
            $this->createSampleParents();
            $parents = ParentProfile::all();
        }

        if ($projects->isEmpty()) {
            $this->command->info('No projects found. Creating sample projects...');
            $this->createSampleProjects();
            $projects = Project::all();
        }

        $paymentMethods = ['cash', 'gcash', 'bank_transfer'];
        $paymentStatuses = ['completed', 'pending', 'failed'];

        // Sample data with Filipino names
        $sampleNames = [
            ['first_name' => 'Anna', 'last_name' => 'Garcia', 'phone' => '09456789012', 'city' => 'Davao City'],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'phone' => '09123456789', 'city' => 'Manila City'],
            ['first_name' => 'Jose', 'last_name' => 'Reyes', 'phone' => '09234567890', 'city' => 'Quezon City'],
            ['first_name' => 'Elena', 'last_name' => 'Cruz', 'phone' => '09345678901', 'city' => 'Cebu City'],
            ['first_name' => 'Roberto', 'last_name' => 'Dela Cruz', 'phone' => '09456789012', 'city' => 'Makati City'],
            ['first_name' => 'Carmen', 'last_name' => 'Fernandez', 'phone' => '09567890123', 'city' => 'Pasig City'],
            ['first_name' => 'Pedro', 'last_name' => 'Mendoza', 'phone' => '09678901234', 'city' => 'Taguig City'],
            ['first_name' => 'Rosa', 'last_name' => 'Villanueva', 'phone' => '09789012345', 'city' => 'Paranaque City'],
            ['first_name' => 'Antonio', 'last_name' => 'Ramos', 'phone' => '09890123456', 'city' => 'Las Pinas City'],
            ['first_name' => 'Lucia', 'last_name' => 'Torres', 'phone' => '09901234567', 'city' => 'Muntinlupa City'],
            ['first_name' => 'Miguel', 'last_name' => 'Gonzales', 'phone' => '09112345678', 'city' => 'Caloocan City'],
            ['first_name' => 'Teresa', 'last_name' => 'Aquino', 'phone' => '09223456789', 'city' => 'Valenzuela City'],
            ['first_name' => 'Ricardo', 'last_name' => 'Bautista', 'phone' => '09334567890', 'city' => 'Malabon City'],
            ['first_name' => 'Sofia', 'last_name' => 'Castillo', 'phone' => '09445678901', 'city' => 'Navotas City'],
            ['first_name' => 'Fernando', 'last_name' => 'Jimenez', 'phone' => '09556789012', 'city' => 'San Juan City'],
            ['first_name' => 'Isabel', 'last_name' => 'Morales', 'phone' => '09667890123', 'city' => 'Mandaluyong City'],
            ['first_name' => 'Carlos', 'last_name' => 'Navarro', 'phone' => '09778901234', 'city' => 'Marikina City'],
            ['first_name' => 'Patricia', 'last_name' => 'Ocampo', 'phone' => '09889012345', 'city' => 'Pasay City'],
            ['first_name' => 'Daniel', 'last_name' => 'Pascual', 'phone' => '09990123456', 'city' => 'Batangas City'],
            ['first_name' => 'Gloria', 'last_name' => 'Quizon', 'phone' => '09101234567', 'city' => 'Laguna'],
            ['first_name' => 'Manuel', 'last_name' => 'Rivera', 'phone' => '09212345678', 'city' => 'Cavite City'],
            ['first_name' => 'Angelica', 'last_name' => 'Salazar', 'phone' => '09323456789', 'city' => 'Bulacan'],
            ['first_name' => 'Eduardo', 'last_name' => 'Tan', 'phone' => '09434567890', 'city' => 'Pampanga'],
            ['first_name' => 'Victoria', 'last_name' => 'Uy', 'phone' => '09545678901', 'city' => 'Tarlac City'],
            ['first_name' => 'Benjamin', 'last_name' => 'Vera', 'phone' => '09656789012', 'city' => 'Zambales'],
            ['first_name' => 'Cristina', 'last_name' => 'Wong', 'phone' => '09767890123', 'city' => 'Bataan'],
            ['first_name' => 'Andres', 'last_name' => 'Yap', 'phone' => '09878901234', 'city' => 'Nueva Ecija'],
            ['first_name' => 'Maricel', 'last_name' => 'Zamora', 'phone' => '09989012345', 'city' => 'Pangasinan'],
            ['first_name' => 'Lorenzo', 'last_name' => 'Abella', 'phone' => '09190123456', 'city' => 'La Union'],
            ['first_name' => 'Beatriz', 'last_name' => 'Borja', 'phone' => '09291234567', 'city' => 'Ilocos Norte'],
            ['first_name' => 'Ramon', 'last_name' => 'Castro', 'phone' => '09392345678', 'city' => 'Ilocos Sur'],
            ['first_name' => 'Diana', 'last_name' => 'Domingo', 'phone' => '09493456789', 'city' => 'Baguio City'],
            ['first_name' => 'Felipe', 'last_name' => 'Espino', 'phone' => '09594567890', 'city' => 'Dagupan City'],
            ['first_name' => 'Grace', 'last_name' => 'Francisco', 'phone' => '09695678901', 'city' => 'San Fernando'],
            ['first_name' => 'Henry', 'last_name' => 'Garcia', 'phone' => '09796789012', 'city' => 'Angeles City'],
        ];

        // Ensure we have enough parents
        foreach ($sampleNames as $sample) {
            $existingParent = ParentProfile::where('first_name', $sample['first_name'])
                ->where('last_name', $sample['last_name'])
                ->first();

            if (!$existingParent) {
                ParentProfile::create([
                    'first_name' => $sample['first_name'],
                    'last_name' => $sample['last_name'],
                    'email' => strtolower($sample['first_name'] . '.' . $sample['last_name']) . '@example.com',
                    'phone' => $sample['phone'],
                    'city' => $sample['city'],
                    'account_status' => 'active',
                    'password_hash' => bcrypt('password123'),
                    'created_date' => now(),
                ]);
            }
        }

        // Refresh parents list
        $parents = ParentProfile::all();

        // Generate 35 sample contributions
        $baseDate = Carbon::now();
        $contributions = [];

        for ($i = 0; $i < 35; $i++) {
            $parent = $parents->random();
            $project = $projects->random();
            $status = $paymentStatuses[array_rand($paymentStatuses)];
            $method = $paymentMethods[array_rand($paymentMethods)];

            // Distribute dates: some today, some this week, some this month, some this year
            $daysAgo = match(true) {
                $i < 5 => 0, // Today
                $i < 12 => rand(1, 6), // This week
                $i < 22 => rand(7, 30), // This month
                default => rand(31, 365), // This year
            };

            $contributionDate = $baseDate->copy()->subDays($daysAgo)->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

            // Amount based on project or random
            $amounts = [150, 200, 250, 300, 350, 400, 500];
            $amount = $status === 'failed' ? 0 : ($status === 'pending' ? $amounts[array_rand($amounts)] / 2 : $amounts[array_rand($amounts)]);

            $receiptNumber = 'TXN' . $contributionDate->format('YmdHis') . str_pad($i, 3, '0', STR_PAD_LEFT);

            ProjectContribution::create([
                'projectID' => $project->projectID,
                'parentID' => $parent->parentID,
                'contribution_amount' => $amount,
                'payment_method' => $method,
                'payment_status' => $status,
                'contribution_date' => $contributionDate,
                'receipt_number' => $receiptNumber,
                'notes' => $status === 'completed' ? 'Payment received' : ($status === 'pending' ? 'Awaiting payment' : 'Payment failed'),
                'processed_by' => 1,
            ]);
        }

        $this->command->info('Created 35 sample payment contributions.');
    }

    private function createSampleParents(): void
    {
        $sampleParents = [
            ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'email' => 'juan.delacruz@email.com', 'phone' => '09171234567'],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'maria.santos@email.com', 'phone' => '09181234567'],
            ['first_name' => 'Pedro', 'last_name' => 'Garcia', 'email' => 'pedro.garcia@email.com', 'phone' => '09191234567'],
            ['first_name' => 'Ana', 'last_name' => 'Reyes', 'email' => 'ana.reyes@email.com', 'phone' => '09201234567'],
            ['first_name' => 'Jose', 'last_name' => 'Cruz', 'email' => 'jose.cruz@email.com', 'phone' => '09211234567'],
        ];

        foreach ($sampleParents as $parent) {
            ParentProfile::create([
                'first_name' => $parent['first_name'],
                'last_name' => $parent['last_name'],
                'email' => $parent['email'],
                'phone' => $parent['phone'],
                'account_status' => 'active',
                'password_hash' => bcrypt('password123'),
                'created_date' => now(),
            ]);
        }
    }

    private function createSampleProjects(): void
    {
        $sampleProjects = [
            ['project_name' => 'Fun Run for a Cause', 'description' => 'Annual fun run event for school fundraising', 'target_budget' => 50000],
            ['project_name' => 'Fundraising Projects', 'description' => 'General fundraising for school activities', 'target_budget' => 30000],
            ['project_name' => 'Community and Parent Involvement', 'description' => 'Programs to enhance parent-school engagement', 'target_budget' => 20000],
            ['project_name' => 'School Supplies Drive', 'description' => 'Collecting school supplies for students in need', 'target_budget' => 15000],
            ['project_name' => 'Classroom Enhancement', 'description' => 'Improving classroom facilities and equipment', 'target_budget' => 40000],
        ];

        foreach ($sampleProjects as $project) {
            Project::create([
                'project_name' => $project['project_name'],
                'description' => $project['description'],
                'goals' => 'Achieve target funding and community participation',
                'target_budget' => $project['target_budget'],
                'current_amount' => 0,
                'start_date' => now()->subMonths(1),
                'target_completion_date' => now()->addMonths(3),
                'project_status' => 'active',
                'created_by' => 1,
                'created_date' => now(),
            ]);
        }
    }
}
