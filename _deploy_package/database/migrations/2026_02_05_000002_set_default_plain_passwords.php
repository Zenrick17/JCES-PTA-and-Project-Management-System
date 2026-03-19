<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This sets a default password for all users who don't have a plain_password set.
     */
    public function up(): void
    {
        // Set default password for users without plain_password
        DB::table('users')
            ->whereNull('plain_password')
            ->orWhere('plain_password', '')
            ->update(['plain_password' => 'password123']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse - passwords would be lost
    }
};
