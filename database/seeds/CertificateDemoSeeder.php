<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\UserEnrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo instructor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@demo.com'],
            [
                'nip' => '1234567890123456',
                'name' => 'Demo Instructor',
                'password' => bcrypt('password'),
                'jabatan' => 'Widyaiswara',
                'unit_kerja' => 'BPSDM Sultra',
                'role' => 'instructor',
                'is_validated' => true,
            ]
        );

        // Create a demo course with slug
        $course = Course::firstOrCreate(
            ['slug' => 'demo-course-certificate'],
            [
                'judul' => 'Demo Course for Certificate',
                'deskripsi' => 'This is a demo course to test certificate generation.',
                'jp_value' => 20,
                'bidang_kompetensi' => 'Manajemen ASN',
                'user_id' => $instructor->id,
            ]
        );

        // Create a demo student
        $student = User::firstOrCreate(
            ['email' => 'student@demo.com'],
            [
                'nip' => '9876543210987654',
                'name' => 'Demo Student',
                'password' => bcrypt('password'),
                'jabatan' => 'Analis',
                'unit_kerja' => 'BPSDM Sultra',
                'role' => 'student',
                'is_validated' => true,
            ]
        );

        // Create a completed enrollment
        $enrollment = UserEnrollment::firstOrCreate(
            [
                'user_id' => $student->id,
                'course_id' => $course->id,
            ],
            [
                'enrollment_date' => now()->subMonths(2),
                'status' => 'completed',
                'completion_percent' => 100,
                'completed_at' => now()->subMonth(),
            ]
        );

        $this->command->info('Demo certificate data created:');
        $this->command->info("  - Instructor: {$instructor->email}");
        $this->command->info("  - Course: {$course->judul} (slug: {$course->slug})");
        $this->command->info("  - Student: {$student->email}");
        $this->command->info("  - Enrollment: {$enrollment->completion_percent}% complete");
        $this->command->info('');
        $this->command->info('You can now generate a certificate for this student by:');
        $this->command->info("  - Logging in as {$student->email}");
        $this->command->info("  - Visiting /courses/{$course->slug}/certificate");
    }
}
