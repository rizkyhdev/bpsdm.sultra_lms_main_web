<?php


namespace Database\Seeders;
use Illuminate\Database\Seeder;


class RolesAndUsersSeeder extends Seeder
{
    /**
     * Jalankan seeder pengguna dan peran dasar.
     * - Buat 1 admin, 3 instructor, 3 fasilitator, 50 student
     */
    public function run()
    {
        // Admin default
        \App\Models\User::factory()->create([
            'nip' => '197801012005011001',
            'name' => 'Administrator BPSDM',
            'email' => 'admin@bpsdmsultra.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Administrator',
            'unit_kerja' => 'BPSDM Sultra',
            'role' => 'admin',
            'is_validated' => true,
        ]);

        // Instructors
        \App\Models\User::factory()->count(3)->create([
            'role' => 'instructor',
            'is_validated' => true,
        ]);

        // Fasilitator: gunakan role 'supervisor' (menyesuaikan enum di DB)
        \App\Models\User::factory()->count(3)->create([
            'role' => 'supervisor',
            'is_validated' => true,
        ]);

        // Students (acak validasi)
        \App\Models\User::factory()->count(50)->create()->each(function (\App\Models\User $u) {
            if ($u->role !== 'student') {
                $u->role = 'student';
            }
            $u->is_validated = (bool) random_int(0, 1);
            $u->save();
        });
    }
}


