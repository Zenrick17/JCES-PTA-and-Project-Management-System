<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup {--fresh : Drop all tables and start fresh} {--seed : Run seeders after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the JCSES-PTA database with migrations and seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('====================================================');
        $this->info('   JCSES-PTA Database Setup');
        $this->info('====================================================');
        $this->newLine();

        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database connection successful');
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed!');
            $this->error('  Make sure MySQL is running and credentials are correct in .env file');
            return 1;
        }

        $this->newLine();

        // Confirm if fresh migration
        if ($this->option('fresh')) {
            if (!$this->confirm('This will DROP ALL TABLES and recreate them. Continue?', false)) {
                $this->warn('Setup cancelled.');
                return 0;
            }
        }

        // Step 1: Run migrations
        $this->info('Step 1: Running migrations...');
        $this->newLine();
        
        if ($this->option('fresh')) {
            Artisan::call('migrate:fresh', ['--force' => true], $this->getOutput());
        } else {
            Artisan::call('migrate', ['--force' => true], $this->getOutput());
        }
        
        $this->newLine();
        $this->info('✓ Migrations completed successfully');
        $this->newLine();

        // Step 2: Create sessions table if needed
        $this->info('Step 2: Verifying sessions table...');
        if (!$this->tableExists('sessions')) {
            $this->warn('  Sessions table not found, creating it...');
            $this->createSessionsTable();
        }
        $this->info('✓ Sessions table verified');
        $this->newLine();

        // Step 3: Run seeders
        if ($this->option('seed') || $this->option('fresh')) {
            $this->info('Step 3: Seeding database...');
            $this->newLine();
            
            Artisan::call('db:seed', ['--force' => true], $this->getOutput());
            
            $this->newLine();
            $this->info('✓ Database seeding completed successfully');
            $this->newLine();
        }

        // Step 4: Clear caches
        $this->info('Step 4: Clearing application caches...');
        Artisan::call('optimize:clear', [], $this->getOutput());
        $this->info('✓ Caches cleared');
        $this->newLine();

        // Display summary
        $this->displaySummary();

        return 0;
    }

    /**
     * Check if a table exists in the database
     */
    protected function tableExists($tableName)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create sessions table manually
     */
    protected function createSessionsTable()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload LONGTEXT NOT NULL,
                last_activity INT NOT NULL,
                INDEX sessions_user_id_index (user_id),
                INDEX sessions_last_activity_index (last_activity)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Display setup summary
     */
    protected function displaySummary()
    {
        $this->info('====================================================');
        $this->info('   Setup Complete! 🎉');
        $this->info('====================================================');
        $this->newLine();
        
        $this->line('<fg=green>✓</> Database tables created');
        $this->line('<fg=green>✓</> Sessions table configured');
        
        if ($this->option('seed') || $this->option('fresh')) {
            $this->line('<fg=green>✓</> Test data seeded');
            $this->newLine();
            $this->info('Test Accounts Created:');
            $this->table(
                ['Role', 'Username', 'Password'],
                [
                    ['Administrator', 'admin', 'password'],
                    ['Principal', 'principal', 'principal123'],
                    ['Teacher', 'teacher', 'teacher123'],
                    ['Parent', 'parent', 'parent123'],
                ]
            );
        }
        
        $this->newLine();
        $this->info('Next Steps:');
        $this->line('1. Start the development server:');
        $this->line('   <fg=cyan>php artisan serve</>');
        $this->newLine();
        $this->line('2. Visit: <fg=cyan>http://127.0.0.1:8000</>');
        $this->newLine();
        $this->line('3. Login with any test account above');
        $this->newLine();
        
        $this->info('For more information, see TEST_ACCOUNTS.md');
        $this->newLine();
    }
}
