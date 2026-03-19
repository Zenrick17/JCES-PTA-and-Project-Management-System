<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id('milestoneID');
            $table->unsignedBigInteger('projectID');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->date('target_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('projectID')->references('projectID')->on('projects')->onDelete('cascade');
            $table->index('projectID');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
