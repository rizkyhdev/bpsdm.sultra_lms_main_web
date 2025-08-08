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
        Schema::create('jp_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('jp_earned');
            $table->integer('tahun');
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            // Add indexes for frequently searched columns
            $table->index('user_id');
            $table->index('course_id');
            $table->index('tahun');
            $table->index('recorded_at');
            
            // Add unique constraint to prevent duplicate JP records for the same user-course-year combination
            $table->unique(['user_id', 'course_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jp_records');
    }
}; 