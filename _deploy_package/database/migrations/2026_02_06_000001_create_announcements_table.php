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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcementID');
            $table->string('title', 255);
            $table->text('content');
            $table->enum('category', ['important', 'notice', 'update', 'event'])->default('notice');
            $table->enum('audience', ['everyone', 'parents', 'teachers', 'administrator', 'principal', 'supporting_staff', 'faculty'])->default('everyone');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('userID')->on('users')->onDelete('cascade');

            $table->index('category');
            $table->index('audience');
            $table->index('is_active');
            $table->index('published_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
