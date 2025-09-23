{{-- 
<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
--}}

<?php

use Illuminate\Database\Seeder;

/**
 * Cara menjalankan seeder (Laravel 5.x):
 *
 * php artisan migrate:fresh --seed
 *
 * Login admin default:
 * - Email: admin@bpsdmsultra.go.id
 * - Password: password
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder dalam urutan yang tepat.
     */
    public function run()
    {


        $this->call([
            RolesAndUsersSeeder::class,
            CoursesSeeder::class,
            ModulesAndSubModulesSeeder::class,
            ContentsSeeder::class,
            QuizzesSeeder::class,
            EnrollmentAndProgressSeeder::class,
            QuizAttemptsSeeder::class,
            CertificatesAndJpRecordsSeeder::class,
        ]);
    }
}


