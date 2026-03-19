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
        // Payment Transactions Table
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id('paymentID');
            $table->unsignedBigInteger('parentID');
            $table->unsignedBigInteger('projectID');
            $table->unsignedBigInteger('contributionID');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer']);
            $table->enum('transaction_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('transaction_date')->useCurrent();
            $table->string('receipt_number', 50)->unique();
            $table->string('reference_number', 100)->nullable();
            $table->unsignedBigInteger('processed_by');
            $table->text('notes')->nullable();
            
            $table->foreign('parentID')->references('parentID')->on('parents')->onDelete('cascade');
            $table->foreign('projectID')->references('projectID')->on('projects')->onDelete('cascade');
            $table->foreign('contributionID')->references('contributionID')->on('project_contributions')->onDelete('cascade');
            
            $table->index('parentID');
            $table->index('projectID');
            $table->index('transaction_date');
            $table->index('receipt_number');
            $table->index('transaction_status');
        });

        // Payment Receipts Table
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id('receiptID');
            $table->unsignedBigInteger('paymentID');
            $table->string('receipt_number', 50)->unique();
            $table->text('receipt_content');
            $table->timestamp('generated_date')->useCurrent();
            $table->unsignedBigInteger('generated_by');
            $table->boolean('email_sent')->default(false);
            $table->integer('print_count')->default(0);
            
            $table->foreign('paymentID')->references('paymentID')->on('payment_transactions')->onDelete('cascade');
            
            $table->index('receipt_number');
            $table->index('generated_date');
        });

        // Refunds Table
        Schema::create('refunds', function (Blueprint $table) {
            $table->id('refundID');
            $table->unsignedBigInteger('paymentID');
            $table->decimal('refund_amount', 10, 2);
            $table->text('refund_reason');
            $table->enum('refund_status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->timestamp('requested_date')->useCurrent();
            $table->timestamp('processed_date')->nullable();
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('notes')->nullable();
            
            $table->foreign('paymentID')->references('paymentID')->on('payment_transactions')->onDelete('cascade');
            
            $table->index('refund_status');
            $table->index('requested_date');
        });

        // Financial Reconciliation Table
        Schema::create('financial_reconciliations', function (Blueprint $table) {
            $table->id('reconciliationID');
            $table->string('reconciliation_period', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_system_amount', 12, 2);
            $table->decimal('total_bank_amount', 12, 2);
            $table->decimal('discrepancy_amount', 12, 2)->default(0.00);
            $table->enum('reconciliation_status', ['pending', 'completed', 'discrepancy_found'])->default('pending');
            $table->timestamp('reconciled_date')->useCurrent();
            $table->unsignedBigInteger('reconciled_by');
            $table->text('notes')->nullable();
            
            $table->index('reconciliation_period');
            $table->index('reconciled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reconciliations');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payment_transactions');
    }
};