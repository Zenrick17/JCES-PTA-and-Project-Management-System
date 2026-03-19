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
        // User Roles Table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('roleID');
            $table->string('role_name', 50)->unique();
            $table->text('role_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
        });

        // User Permissions Table
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id('permissionID');
            $table->string('permission_name', 100)->unique();
            $table->text('permission_description')->nullable();
            $table->string('module_name', 50);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
        });

        // Role Permissions Table (Many-to-Many relationship)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id('rolePermissionID');
            $table->unsignedBigInteger('roleID');
            $table->unsignedBigInteger('permissionID');
            $table->timestamp('granted_date')->useCurrent();
            $table->unsignedBigInteger('granted_by');
            
            $table->foreign('roleID')->references('roleID')->on('user_roles')->onDelete('cascade');
            $table->foreign('permissionID')->references('permissionID')->on('user_permissions')->onDelete('cascade');
            $table->unique(['roleID', 'permissionID'], 'unique_role_permission');
        });

        // User Role Assignments Table
        Schema::create('user_role_assignments', function (Blueprint $table) {
            $table->id('assignmentID');
            $table->unsignedBigInteger('userID');
            $table->unsignedBigInteger('roleID');
            $table->timestamp('assigned_date')->useCurrent();
            $table->unsignedBigInteger('assigned_by');
            $table->boolean('is_active')->default(true);
            
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('roleID')->references('roleID')->on('user_roles')->onDelete('cascade');
            
            $table->index('userID');
            $table->index('is_active');
        });

        // Security Audit Log Table
        Schema::create('security_audit_log', function (Blueprint $table) {
            $table->id('logID');
            $table->unsignedBigInteger('userID')->nullable();
            $table->string('action', 100);
            $table->string('table_affected', 50)->nullable();
            $table->integer('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id', 128)->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            
            $table->foreign('userID')->references('userID')->on('users')->onDelete('set null');
            
            $table->index('userID');
            $table->index('timestamp');
            $table->index('action');
        });

        // User Sessions Table
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('sessionID', 128)->primary();
            $table->unsignedBigInteger('userID');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('last_activity')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            
            $table->index('userID');
            $table->index('last_activity');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('security_audit_log');
        Schema::dropIfExists('user_role_assignments');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_roles');
    }
};