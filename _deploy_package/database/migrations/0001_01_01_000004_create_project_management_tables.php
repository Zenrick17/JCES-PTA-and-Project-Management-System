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
        // Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id('projectID');
            $table->string('project_name', 200);
            $table->text('description');
            $table->text('goals');
            $table->decimal('target_budget', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0.00);
            $table->date('start_date');
            $table->date('target_completion_date');
            $table->date('actual_completion_date')->nullable();
            $table->enum('project_status', ['created', 'active', 'in_progress', 'completed', 'archived', 'cancelled'])->default('created');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('project_status');
            $table->index('created_date');
            $table->index('current_amount');
            $table->index(['start_date', 'target_completion_date']);
        });

        // Project Contributions Table
        Schema::create('project_contributions', function (Blueprint $table) {
            $table->id('contributionID');
            $table->unsignedBigInteger('projectID');
            $table->unsignedBigInteger('parentID');
            $table->decimal('contribution_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer']);
            $table->enum('payment_status', ['pending', 'completed', 'refunded'])->default('pending');
            $table->timestamp('contribution_date')->useCurrent();
            $table->string('receipt_number', 50)->unique()->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            
            $table->foreign('projectID')->references('projectID')->on('projects')->onDelete('cascade');
            $table->foreign('parentID')->references('parentID')->on('parents')->onDelete('cascade');
            
            $table->index('projectID');
            $table->index('parentID');
            $table->index('contribution_date');
            $table->index('contribution_amount');
        });

        // Project Updates Table
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id('updateID');
            $table->unsignedBigInteger('projectID');
            $table->string('update_title', 200);
            $table->text('update_description');
            $table->string('milestone_achieved', 200)->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->timestamp('update_date')->useCurrent();
            $table->unsignedBigInteger('updated_by');
            
            $table->foreign('projectID')->references('projectID')->on('projects')->onDelete('cascade');
            
            $table->index('projectID');
            $table->index('update_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_updates');
        Schema::dropIfExists('project_contributions');
        Schema::dropIfExists('projects');
    }
};