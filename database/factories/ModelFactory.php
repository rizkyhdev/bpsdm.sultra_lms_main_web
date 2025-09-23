t<?php

/*
|--------------------------------------------------------------------------
| Model Factories (Laravel 5.x style)
|--------------------------------------------------------------------------
|
| Ini adalah definisi factory legacy untuk Laravel 5.x. Gunakan
| factory(Model::class)->create() untuk membuat data dummy realistis.
| Komentar dalam Bahasa Indonesia sesuai permintaan.
|
*/

use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;

$factory->define(App\Models\User::class, function (Faker $faker) {
    // Nama Indonesia
    $faker = FakerFactory::create('id_ID');
    // Catatan: skema enum saat ini: admin, instructor, student, supervisor
    $roles = ['admin', 'instructor', 'student', 'supervisor'];
    return [
        'nip' => (string) $faker->unique()->numerify('################'),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('password'),
        'jabatan' => $faker->randomElement(['Analis', 'Widyaiswara', 'Pengelola Diklat', 'Staff']),
        'unit_kerja' => $faker->randomElement(['BPSDM Sultra', 'Sekretariat', 'Bidang Pengembangan Kompetensi', 'UPTD Diklat']),
        'role' => $faker->randomElement($roles),
        'is_validated' => $faker->boolean(80),
        'remember_token' => Str::random(10),
        'created_at' => $faker->dateTimeBetween('-2 years', '-1 months'),
        'updated_at' => $faker->dateTimeBetween('-1 months', 'now'),
    ];
});

$factory->define(App\Models\Course::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $bidang = ['Manajemen ASN', 'Pelayanan Publik', 'Kepemimpinan', 'Teknis Pemerintahan', 'Pengadaan Barang/Jasa'];
    return [
        'judul' => $faker->randomElement([
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
        ]),
        'deskripsi' => $faker->paragraph(3, true),
        'jp_value' => $faker->numberBetween(8, 40),
        'bidang_kompetensi' => $faker->randomElement($bidang),
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\Module::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    return [
        'course_id' => function () {
            return factory(App\Models\Course::class)->create()->id;
        },
        'judul' => 'Modul: '.$faker->sentence(3),
        'deskripsi' => $faker->paragraph,
        'urutan' => 1,
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\SubModule::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    return [
        'module_id' => function () {
            return factory(App\Models\Module::class)->create()->id;
        },
        'judul' => 'Submodul: '.$faker->sentence(3),
        'deskripsi' => $faker->paragraph,
        'urutan' => 1,
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\Content::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $types = ['text', 'pdf', 'video', 'audio', 'image'];
    $type = $faker->randomElement($types);
    $pathBase = 'storage/app/public/contents/';
    $fileMap = [
        'text' => $pathBase.$faker->slug.'.html',
        'pdf' => $pathBase.$faker->slug.'.pdf',
        'video' => $pathBase.$faker->slug.'.mp4',
        'audio' => $pathBase.$faker->slug.'.mp3',
        'image' => $pathBase.$faker->slug.'.jpg',
    ];
    return [
        'sub_module_id' => function () {
            return factory(App\Models\SubModule::class)->create()->id;
        },
        'judul' => 'Materi: '.$faker->sentence(3),
        'tipe' => $type,
        'file_path' => $fileMap[$type],
        'urutan' => 1,
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\Quiz::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    return [
        'sub_module_id' => function () {
            return factory(App\Models\SubModule::class)->create()->id;
        },
        'judul' => 'Kuis: '.$faker->sentence(3),
        'deskripsi' => $faker->paragraph,
        'nilai_minimum' => $faker->randomFloat(1, 60, 80),
        'max_attempts' => $faker->numberBetween(1, 3),
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\Question::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $types = ['multiple_choice', 'true_false'];
    return [
        'quiz_id' => function () {
            return factory(App\Models\Quiz::class)->create()->id;
        },
        'pertanyaan' => rtrim($faker->sentence(8), '.').'?',
        'tipe' => $faker->randomElement($types),
        'bobot' => $faker->numberBetween(1, 5),
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\AnswerOption::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    return [
        'question_id' => function () {
            return factory(App\Models\Question::class)->create()->id;
        },
        'teks_jawaban' => $faker->sentence(6),
        'is_correct' => false,
        'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
        'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
    ];
});

$factory->define(App\Models\UserEnrollment::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $status = ['enrolled', 'in_progress', 'completed'];
    $enrolledAt = $faker->dateTimeBetween('-18 months', '-1 months');
    $completed = $faker->boolean(20);
    return [
        'user_id' => function () {
            return factory(App\Models\User::class)->create(['role' => 'student'])->id;
        },
        'course_id' => function () {
            return factory(App\Models\Course::class)->create()->id;
        },
        'enrollment_date' => $enrolledAt,
        'status' => $completed ? 'completed' : $faker->randomElement(['enrolled', 'in_progress']),
        'completed_at' => $completed ? $faker->dateTimeBetween($enrolledAt, 'now') : null,
        'created_at' => $enrolledAt,
        'updated_at' => $faker->dateTimeBetween($enrolledAt, 'now'),
    ];
});

$factory->define(App\Models\UserProgress::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $completed = $faker->boolean(50);
    $start = $faker->dateTimeBetween('-12 months', '-1 months');
    return [
        'user_id' => function () {
            return factory(App\Models\User::class)->create(['role' => 'student'])->id;
        },
        'sub_module_id' => function () {
            return factory(App\Models\SubModule::class)->create()->id;
        },
        'is_completed' => $completed,
        'completed_at' => $completed ? $faker->dateTimeBetween($start, 'now') : null,
        'created_at' => $start,
        'updated_at' => $faker->dateTimeBetween($start, 'now'),
    ];
});

$factory->define(App\Models\QuizAttempt::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $start = $faker->dateTimeBetween('-12 months', '-1 weeks');
    $completed = $faker->boolean(80);
    return [
        'user_id' => function () {
            return factory(App\Models\User::class)->create(['role' => 'student'])->id;
        },
        'quiz_id' => function () {
            return factory(App\Models\Quiz::class)->create()->id;
        },
        'nilai' => $faker->randomFloat(1, 50, 95),
        'is_passed' => false,
        'attempt_number' => 1,
        'started_at' => $start,
        'completed_at' => $completed ? $faker->dateTimeBetween($start, 'now') : null,
        'created_at' => $start,
        'updated_at' => $faker->dateTimeBetween($start, 'now'),
    ];
});

$factory->define(App\Models\UserAnswer::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    return [
        'quiz_attempt_id' => function () {
            return factory(App\Models\QuizAttempt::class)->create()->id;
        },
        'question_id' => function () {
            return factory(App\Models\Question::class)->create()->id;
        },
        'answer_option_id' => function (array $attr) {
            $questionId = isset($attr['question_id']) ? $attr['question_id'] : factory(App\Models\Question::class)->create()->id;
            $option = App\Models\AnswerOption::where('question_id', $questionId)->inRandomOrder()->first();
            if (!$option) {
                $option = factory(App\Models\AnswerOption::class)->create(['question_id' => $questionId]);
            }
            return $option->id;
        },
        'created_at' => $faker->dateTimeBetween('-12 months', 'now'),
        'updated_at' => $faker->dateTimeBetween('-12 months', 'now'),
    ];
});

$factory->define(App\Models\Certificate::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $issue = $faker->dateTimeBetween('-10 months', 'now');
    return [
        'user_id' => function () {
            return factory(App\Models\User::class)->create(['role' => 'student'])->id;
        },
        'course_id' => function () {
            return factory(App\Models\Course::class)->create()->id;
        },
        'nomor_sertifikat' => 'CERT-'.date('Y', $issue->getTimestamp()).'-'.$faker->unique()->numerify('######'),
        'issue_date' => $issue,
        'file_path' => 'storage/app/public/certificates/'.$faker->uuid.'.pdf',
        'created_at' => $issue,
        'updated_at' => $faker->dateTimeBetween($issue, 'now'),
    ];
});

$factory->define(App\Models\JpRecord::class, function (Faker $faker) {
    $faker = FakerFactory::create('id_ID');
    $recorded = $faker->dateTimeBetween('-10 months', 'now');
    return [
        'user_id' => function () {
            return factory(App\Models\User::class)->create(['role' => 'student'])->id;
        },
        'course_id' => function () {
            return factory(App\Models\Course::class)->create()->id;
        },
        'jp_earned' => $faker->numberBetween(8, 40),
        'tahun' => (int) $recorded->format('Y'),
        'recorded_at' => $recorded,
        'created_at' => $recorded,
        'updated_at' => $faker->dateTimeBetween($recorded, 'now'),
    ];
});


