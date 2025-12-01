<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $judul = $this->faker->randomElement([
            'Dasar-Dasar Pelayanan Publik',
            'Manajemen Kinerja ASN',
            'Kepemimpinan Operasional',
            'Perencanaan Program Diklat',
            'Dasar Pengadaan Barang/Jasa',
            'Etika Administrasi Pemerintahan',
        ]);
        
        return [
            'user_id' => User::factory()->create(['role' => 'instructor'])->id,
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'deskripsi' => $this->faker->paragraph(5),
            'jp_value' => $this->faker->numberBetween(10, 40),
            'bidang_kompetensi' => $this->faker->randomElement([
                'Manajemen ASN',
                'Pelayanan Publik',
                'Kepemimpinan',
                'Teknis Pemerintahan',
                'Pengadaan Barang/Jasa',
            ]),
        ];
    }
}
