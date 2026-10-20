<?php

namespace App\Services;

use App\Models\SimulationAnswer;
use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use App\Models\SimulationToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Logika Simulasi Uji Kompetensi (CAT): token sesi, percobaan, jawaban, dan penilaian.
 *
 * Waktu pengerjaan dicatat di server (ends_at), sehingga tidak bisa dimanipulasi dari
 * browser dan tetap berjalan meskipun peserta menutup halaman.
 */
class SimulationService
{
    /**
     * Buat token sesi baru dan nonaktifkan token sebelumnya (hanya satu token aktif).
     */
    public function generateToken(string $sesi): SimulationToken
    {
        return DB::transaction(function () use ($sesi) {
            SimulationToken::active()->update(['is_active' => false]);

            return SimulationToken::create([
                'kode' => $this->uniqueTokenCode(),
                'sesi' => $sesi,
                'is_active' => true,
            ]);
        });
    }

    /**
     * Percobaan peserta yang masih berjalan; percobaan yang waktunya habis diselesaikan dulu.
     */
    public function ongoingAttemptFor(User $user): ?SimulationAttempt
    {
        $attempt = $user->simulationAttempts()->whereNull('completed_at')->latest('id')->first();

        if ($attempt?->isExpired()) {
            $this->finish($attempt);

            return null;
        }

        return $attempt;
    }

    /**
     * Mulai percobaan baru dengan soal acak dari bank soal.
     */
    public function start(User $user, SimulationToken $token): SimulationAttempt
    {
        $questionIds = SimulationQuestion::inRandomOrder()
            ->limit(config('simulasi.jumlah_soal'))
            ->pluck('id')
            ->all();

        $durasi = config('simulasi.durasi_menit');
        $now = now();

        return $user->simulationAttempts()->create([
            'simulation_token_id' => $token->id,
            'question_ids' => $questionIds,
            'jumlah_soal' => count($questionIds),
            'durasi_menit' => $durasi,
            'started_at' => $now,
            'ends_at' => $now->copy()->addMinutes($durasi),
        ]);
    }

    /**
     * Simpan (atau ganti) jawaban peserta untuk satu soal.
     */
    public function saveAnswer(SimulationAttempt $attempt, SimulationQuestion $question, string $jawaban): void
    {
        // Upsert atomik: aman dari klik ganda / permintaan yang tiba bersamaan.
        SimulationAnswer::upsert([[
            'simulation_attempt_id' => $attempt->id,
            'simulation_question_id' => $question->id,
            'jawaban' => $jawaban,
            'is_correct' => $jawaban === $question->kunci_jawaban,
        ]], ['simulation_attempt_id', 'simulation_question_id'], ['jawaban', 'is_correct', 'updated_at']);
    }

    /**
     * Selesaikan percobaan dan hitung skor (1 soal benar = 1 poin). Aman dipanggil berulang kali.
     */
    public function finish(SimulationAttempt $attempt): SimulationAttempt
    {
        return DB::transaction(function () use ($attempt) {
            $attempt = SimulationAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();

            if ($attempt->isFinished()) {
                return $attempt;
            }

            $attempt->update([
                'skor' => $attempt->answers()->where('is_correct', true)->count(),
                // Percobaan yang diselesaikan setelah waktu habis tercatat selesai tepat saat waktu habis.
                'completed_at' => now()->min($attempt->ends_at),
            ]);

            return $attempt;
        });
    }

    /**
     * Selesaikan semua percobaan yang waktunya sudah habis tetapi belum diselesaikan peserta.
     */
    public function finishExpired(): void
    {
        SimulationAttempt::expired()->lazyById()->each(fn (SimulationAttempt $attempt) => $this->finish($attempt));
    }

    private function uniqueTokenCode(): string
    {
        do {
            $kode = strtoupper(bin2hex(random_bytes(3)));
        } while (SimulationToken::where('kode', $kode)->exists());

        return $kode;
    }
}
