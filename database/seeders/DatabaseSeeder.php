<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\RolesAndUsersSeeder;
use Database\Seeders\CoursesSeeder;
use Database\Seeders\ModulesAndSubModulesSeeder;
use Database\Seeders\ContentsSeeder;
use Database\Seeders\QuizzesSeeder;
use Database\Seeders\EnrollmentAndProgressSeeder;
use Database\Seeders\QuizAttemptsSeeder;
use Database\Seeders\CertificatesAndJpRecordsSeeder;


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
            SimulationQuestionSeeder::class,
        ]);
    }
}


