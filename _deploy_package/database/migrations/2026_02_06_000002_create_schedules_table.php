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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('scheduleID');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->dateTime('scheduled_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('category', ['meeting', 'event', 'maintenance', 'academic', 'review', 'other'])->default('other');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('visibility', ['everyone', 'administrator', 'principal', 'teachers', 'parents', 'supporting_staff', 'faculty'])->default('everyone');
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('created_by')->references('userID')->on('users')->onDelete('cascade');

            $table->index('scheduled_date');
            $table->index('category');
            $table->index('priority');
            $table->index('visibility');
            $table->index('is_active');
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
