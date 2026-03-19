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
        // Reports Table
        Schema::create('reports', function (Blueprint $table) {
            $table->id('reportID');
            $table->string('report_name', 200);
            $table->enum('report_type', ['participation', 'financial', 'project_analytics', 'custom', 'automated']);
            $table->text('report_description')->nullable();
            $table->json('parameters')->nullable();
            $table->timestamp('generated_date')->useCurrent();
            $table->unsignedBigInteger('generated_by');
            $table->string('file_path', 500)->nullable();
            $table->enum('file_format', ['pdf', 'excel', 'csv', 'html']);
            $table->boolean('is_scheduled')->default(false);
            $table->enum('schedule_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable();
            $table->timestamp('next_run_date')->nullable();
            
            $table->foreign('generated_by')->references('userID')->on('users')->onDelete('cascade');
            
            $table->index('report_type');
            $table->index('generated_date');
            $table->index(['is_scheduled', 'next_run_date']);
        });

        // Report Recipients Table
        Schema::create('report_recipients', function (Blueprint $table) {
            $table->id('recipientID');
            $table->unsignedBigInteger('reportID');
            $table->unsignedBigInteger('userID');
            $table->string('recipient_email', 150);
            $table->enum('delivery_method', ['email', 'download', 'both'])->default('email');
            $table->boolean('is_active')->default(true);
            $table->timestamp('added_date')->useCurrent();
            
            $table->foreign('reportID')->references('reportID')->on('reports')->onDelete('cascade');
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            
            $table->index('reportID');
            $table->index('is_active');
        });

        // Dashboard Metrics Table
        Schema::create('dashboard_metrics', function (Blueprint $table) {
            $table->id('metricID');
            $table->string('metric_name', 100);
            $table->enum('metric_category', ['enrollment', 'projects', 'financial', 'participation', 'system']);
            $table->decimal('current_value', 15, 2);
            $table->decimal('target_value', 15, 2)->nullable();
            $table->string('unit_of_measure', 20)->nullable();
            $table->text('calculation_method')->nullable();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            $table->index('metric_category');
            $table->index('is_active');
            $table->index('display_order');
        });

        // Report Execution Log Table
        Schema::create('report_execution_log', function (Blueprint $table) {
            $table->id('executionID');
            $table->unsignedBigInteger('reportID');
            $table->timestamp('execution_start')->useCurrent();
            $table->timestamp('execution_end')->nullable();
            $table->enum('execution_status', ['running', 'completed', 'failed', 'cancelled'])->default('running');
            $table->integer('record_count')->default(0);
            $table->integer('file_size_bytes')->default(0);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('executed_by')->nullable();
            
            $table->foreign('reportID')->references('reportID')->on('reports')->onDelete('cascade');
            $table->foreign('executed_by')->references('userID')->on('users')->onDelete('set null');
            
            $table->index('reportID');
            $table->index('execution_start');
            $table->index('execution_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_execution_log');
        Schema::dropIfExists('dashboard_metrics');
        Schema::dropIfExists('report_recipients');
        Schema::dropIfExists('reports');
    }
};