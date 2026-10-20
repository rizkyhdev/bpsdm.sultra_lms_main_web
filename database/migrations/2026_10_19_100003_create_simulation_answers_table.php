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
        Schema::create('simulation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_attempt_id')->constrained()->cascadeOnDelete();
            // Soal yang sudah dijawab tidak boleh dihapus dari bank soal.
            $table->foreignId('simulation_question_id')->constrained()->restrictOnDelete();
            $table->char('jawaban', 1);
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['simulation_attempt_id', 'simulation_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_answers');
    }
};
