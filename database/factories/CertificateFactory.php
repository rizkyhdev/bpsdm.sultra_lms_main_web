<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = Course::factory()->create();
        $user = User::factory()->create(['role' => 'student']);
        $year = now()->year;
        $slug = $course->slug ?? Str::slug($course->judul ?? 'course');
        $filePath = "certificates/{$year}/{$slug}/{$user->id}.pdf";
        
        return [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'nomor_sertifikat' => 'CERT-' . strtoupper(Str::random(10)),
            'certificate_uid' => Str::uuid()->toString(),
            'issue_date' => now(),
            'file_path' => $filePath,
            'generated_at' => now(),
        ];
    }
}
