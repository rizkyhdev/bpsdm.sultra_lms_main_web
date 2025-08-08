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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('answer_option_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Add indexes for frequently searched columns
            $table->index('quiz_attempt_id');
            $table->index('question_id');
            $table->index('answer_option_id');
            
            // Add unique constraint to prevent duplicate answers for the same question in the same attempt
            $table->unique(['quiz_attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
}; 