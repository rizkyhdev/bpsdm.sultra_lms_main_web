<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Buat 10 kursus dengan konteks BPSDM Sultra (Bahasa Indonesia).
     */
    public function run()
    {
        $titles = [
            'Dasar-Dasar Pelayanan Publik',
            'Manajemen Kinerja ASN',
            'Kepemimpinan Operasional',
            'Perencanaan Program Diklat',
            'Dasar Pengadaan Barang/Jasa',
            'Etika Administrasi Pemerintahan',
            'Komunikasi Efektif di Instansi',
            'Pengelolaan Arsip Modern',
            'Pengantar Transformasi Digital',
            'Manajemen Risiko Sektor Publik',
        ];
        $bidang = ['Manajemen ASN', 'Pelayanan Publik', 'Kepemimpinan', 'Teknis Pemerintahan', 'Pengadaan Barang/Jasa'];

        foreach ($titles as $title) {
            \App\Models\Course::factory()->create([
                'judul' => $title,
                'deskripsi' => 'Pelatihan '.$title.' untuk meningkatkan kompetensi aparatur di lingkungan BPSDM Sultra.',
                'jp_value' => rand(8, 40),
                'bidang_kompetensi' => $bidang[array_rand($bidang)],
            ]);
        }
    }
}


