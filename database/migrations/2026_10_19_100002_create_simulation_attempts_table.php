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
        Schema::create('simulation_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('simulation_token_id')->constrained()->restrictOnDelete();
            // Urutan soal acak untuk percobaan ini (ID simulation_questions).
            $table->json('question_ids');
            $table->unsignedSmallInteger('jumlah_soal');
            $table->unsignedSmallInteger('durasi_menit');
            $table->timestamp('started_at');
            $table->timestamp('ends_at');
            $table->timestamp('completed_at')->nullable();
            // Jumlah jawaban benar (1 soal benar = 1 poin), diisi saat simulasi selesai.
            $table->unsignedSmallInteger('skor')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'completed_at']);
            $table->index(['completed_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_attempts');
    }
};
