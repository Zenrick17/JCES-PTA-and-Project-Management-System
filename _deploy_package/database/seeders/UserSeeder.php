<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrator Account
        User::create([
            'username' => 'admin',
            'password_hash' => Hash::make('password'),
            'plain_password' => 'password',
            'user_type' => 'administrator',
            'email' => 'admin@jcses.edu.ph',
            'phone' => '09123456789',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'is_active' => 1,
            'failed_login_attempts' => 0,
        ]);

        // Principal Account
        User::create([
            'username' => 'principal',
            'password_hash' => Hash::make('principal123'),
            'plain_password' => 'principal123',
            'user_type' => 'principal',
            'email' => 'principal@jcses.edu.ph',
            'phone' => '09234567890',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'is_active' => 1,
            'failed_login_attempts' => 0,
        ]);

        // Teacher Account
        User::create([
            'username' => 'teacher',
            'password_hash' => Hash::make('teacher123'),
            'plain_password' => 'teacher123',
            'user_type' => 'teacher',
            'email' => 'teacher@jcses.edu.ph',
            'phone' => '09345678901',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'is_active' => 1,
            'failed_login_attempts' => 0,
        ]);

        // Parent Account
        User::create([
            'username' => 'parent',
            'password_hash' => Hash::make('parent123'),
            'plain_password' => 'parent123',
            'user_type' => 'parent',
            'email' => 'parent@gmail.com',
            'phone' => '09456789012',
            'first_name' => 'Anna',
            'last_name' => 'Garcia',
            'is_active' => 1,
            'failed_login_attempts' => 0,
        ]);
    }
}
