<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Parents Table
        Schema::create('parents', function (Blueprint $table) {
            $table->id('parentID');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('phone', 20);
            $table->string('street_address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('barangay', 100)->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->string('password_hash', 255);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('last_login')->nullable();
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->unsignedBigInteger('userID')->unique()->nullable();
            
            $table->index('email');
            $table->index('phone');
            $table->index(['last_name', 'first_name']);
        });

        // Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->id('studentID');
            $table->string('student_name', 150);
            $table->string('grade_level', 20);
            $table->string('section', 50)->nullable();
            $table->enum('enrollment_status', ['active', 'transferred', 'graduated', 'dropped'])->default('active');
            $table->string('academic_year', 20);
            $table->date('enrollment_date');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('grade_level');
            $table->index('academic_year');
            $table->index('enrollment_status');
        });

        // Parent-Student Relationships Table
        Schema::create('parent_student_relationships', function (Blueprint $table) {
            $table->id('relationshipID');
            $table->unsignedBigInteger('parentID');
            $table->unsignedBigInteger('studentID');
            $table->enum('relationship_type', ['mother', 'father', 'guardian', 'grandparent', 'sibling', 'other']);
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamp('created_date')->useCurrent();
            
            $table->foreign('parentID')->references('parentID')->on('parents')->onDelete('cascade');
            $table->foreign('studentID')->references('studentID')->on('students')->onDelete('cascade');
            $table->unique(['parentID', 'studentID'], 'unique_parent_student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student_relationships');
        Schema::dropIfExists('students');
        Schema::dropIfExists('parents');
    }
};