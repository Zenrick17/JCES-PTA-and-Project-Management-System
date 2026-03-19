<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Expand enum to include both old and new values temporarily
        DB::statement("ALTER TABLE schedules MODIFY COLUMN visibility ENUM('everyone', 'administrator', 'principal', 'teacher', 'teachers', 'parent', 'parents', 'staff', 'supporting_staff', 'faculty') DEFAULT 'everyone'");

        DB::statement("ALTER TABLE announcements MODIFY COLUMN audience ENUM('everyone', 'parents', 'teachers', 'administrator', 'principal', 'staff', 'supporting_staff', 'faculty') DEFAULT 'everyone'");

        // Step 2: Update old values to new values
        DB::table('schedules')
            ->where('visibility', 'teacher')
            ->update(['visibility' => 'teachers']);

        DB::table('schedules')
            ->where('visibility', 'parent')
            ->update(['visibility' => 'parents']);

        DB::table('schedules')
            ->where('visibility', 'staff')
            ->update(['visibility' => 'supporting_staff']);

        DB::table('announcements')
            ->where('audience', 'staff')
            ->update(['audience' => 'supporting_staff']);

        // Step 3: Set final enum values (remove old singular values)
        DB::statement("ALTER TABLE announcements MODIFY COLUMN audience ENUM('everyone', 'parents', 'teachers', 'administrator', 'principal', 'supporting_staff', 'faculty') DEFAULT 'everyone'");

        DB::statement("ALTER TABLE schedules MODIFY COLUMN visibility ENUM('everyone', 'administrator', 'principal', 'teachers', 'parents', 'supporting_staff', 'faculty') DEFAULT 'everyone'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE announcements MODIFY COLUMN audience ENUM('everyone', 'parents', 'teachers', 'administrator', 'principal') DEFAULT 'everyone'");

        DB::statement("ALTER TABLE schedules MODIFY COLUMN visibility ENUM('everyone', 'administrator', 'principal', 'teacher', 'parent') DEFAULT 'everyone'");
    }
};
