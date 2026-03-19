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
        // Add foreign key relationships between subsystems
        
        // Link parents table to users table
        Schema::table('parents', function (Blueprint $table) {
            $table->foreign('userID')->references('userID')->on('users')->onDelete('set null');
        });

        // Add foreign keys for created_by fields in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('created_by')->references('userID')->on('users')->onDelete('restrict');
        });

        // Add foreign keys for processed_by fields
        Schema::table('project_contributions', function (Blueprint $table) {
            $table->foreign('processed_by')->references('userID')->on('users')->onDelete('set null');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->foreign('processed_by')->references('userID')->on('users')->onDelete('restrict');
        });

        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->foreign('generated_by')->references('userID')->on('users')->onDelete('restrict');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->foreign('requested_by')->references('userID')->on('users')->onDelete('restrict');
            $table->foreign('processed_by')->references('userID')->on('users')->onDelete('set null');
        });

        Schema::table('financial_reconciliations', function (Blueprint $table) {
            $table->foreign('reconciled_by')->references('userID')->on('users')->onDelete('restrict');
        });

        Schema::table('project_updates', function (Blueprint $table) {
            $table->foreign('updated_by')->references('userID')->on('users')->onDelete('restrict');
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->foreign('granted_by')->references('userID')->on('users')->onDelete('restrict');
        });

        Schema::table('user_role_assignments', function (Blueprint $table) {
            $table->foreign('assigned_by')->references('userID')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraints
        Schema::table('user_role_assignments', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropForeign(['granted_by']);
        });

        Schema::table('project_updates', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
        });

        Schema::table('financial_reconciliations', function (Blueprint $table) {
            $table->dropForeign(['reconciled_by']);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['processed_by']);
        });

        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->dropForeign(['generated_by']);
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
        });

        Schema::table('project_contributions', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropForeign(['userID']);
        });
    }
};