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
        Schema::create('content_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->decimal('progress_percentage', 5, 2)->default(0)->comment('Progress percentage 0-100');
            $table->integer('video_duration')->nullable()->comment('Video duration in seconds');
            $table->integer('watched_duration')->default(0)->comment('Total watched duration in seconds');
            $table->integer('current_position')->default(0)->comment('Current playback position in seconds');
            $table->integer('time_spent')->default(0)->comment('Total time spent in seconds');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('user_id');
            $table->index('content_id');
            $table->index('is_completed');
            
            // Prevent duplicate progress records
            $table->unique(['user_id', 'content_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_progress');
    }
};
