<?php

namespace Database\Seeders;

use App\Models\SimulationQuestion;
use Illuminate\Database\Seeder;

/**
 * Bank soal Simulasi Uji Kompetensi: Pengadaan Barang/Jasa Pemerintah (PBJP),
 * diambil dari aplikasi simulasi ujian sebelumnya (quiz_app).
 *
 * Aman dijalankan berulang kali: soal dengan pertanyaan yang sama tidak diduplikasi.
 *   php artisan db:seed --class=SimulationQuestionSeeder
 */
class SimulationQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = json_decode(
            file_get_contents(database_path('seeders/data/simulasi_bank_soal_pbjp.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $created = 0;

        foreach ($questions as $question) {
            $created += (int) SimulationQuestion::firstOrCreate(
                ['pertanyaan' => $question['pertanyaan']],
                $question,
            )->wasRecentlyCreated;
        }

        $this->command?->info(sprintf('Bank soal: %d soal ditambahkan, %d sudah ada.', $created, count($questions) - $created));
    }
}
