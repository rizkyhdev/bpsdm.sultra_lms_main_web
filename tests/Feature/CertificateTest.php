<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\UserEnrollment;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function student_can_download_certificate_when_completion_is_100_percent()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $enrollment = UserEnrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'completion_percent' => 100,
            'completed_at' => now(),
            'status' => 'completed',
        ]);

        $this->actingAs($student);

        $response = $this->postJson(route('certificates.generate', $course->slug));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'download_url',
            ]);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    /** @test */
    public function student_cannot_download_certificate_when_completion_is_less_than_100_percent()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $enrollment = UserEnrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'completion_percent' => 75,
            'completed_at' => null,
            'status' => 'in_progress',
        ]);

        $this->actingAs($student);

        $response = $this->postJson(route('certificates.generate', $course->slug));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    /** @test */
    public function student_cannot_download_certificate_when_not_enrolled()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $this->actingAs($student);

        $response = $this->postJson(route('certificates.generate', $course->slug));

        $response->assertStatus(403);
    }

    /** @test */
    public function existing_certificate_is_reused_on_subsequent_downloads()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $enrollment = UserEnrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'completion_percent' => 100,
            'completed_at' => now(),
            'status' => 'completed',
        ]);

        $certificateService = app(CertificateService::class);
        $certificateService->ensureCertificate($student, $course);

        $firstCertificate = Certificate::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        $firstPath = $firstCertificate->file_path;
        $firstUid = $firstCertificate->certificate_uid;

        // Generate again
        $certificateService->ensureCertificate($student, $course);

        $secondCertificate = Certificate::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertEquals($firstPath, $secondCertificate->file_path);
        $this->assertEquals($firstUid, $secondCertificate->certificate_uid);
    }

    /** @test */
    public function certificate_verification_shows_valid_status()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $certificate = Certificate::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response = $this->get(route('certificates.verify', $certificate->certificate_uid));

        $response->assertStatus(200)
            ->assertViewIs('certificates.verify')
            ->assertViewHas('status', 'valid')
            ->assertSee($student->name)
            ->assertSee($course->judul);
    }

    /** @test */
    public function certificate_verification_shows_not_found_for_invalid_uid()
    {
        $response = $this->get(route('certificates.verify', 'invalid-uid'));

        $response->assertStatus(200)
            ->assertViewIs('certificates.verify')
            ->assertViewHas('status', 'not_found');
    }

    /** @test */
    public function admin_can_preview_certificate_template()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::factory()->create([
            'slug' => 'test-course',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('certificates.preview', $course->slug));

        $response->assertStatus(200)
            ->assertViewIs('certificates.template');
    }

    /** @test */
    public function instructor_can_preview_certificate_template()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $this->actingAs($instructor);

        $response = $this->get(route('certificates.preview', $course->slug));

        $response->assertStatus(200)
            ->assertViewIs('certificates.template');
    }

    /** @test */
    public function student_cannot_preview_certificate_template()
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create([
            'slug' => 'test-course',
        ]);

        $this->actingAs($student);

        $response = $this->get(route('certificates.preview', $course->slug));

        $response->assertStatus(403);
    }

    /** @test */
    public function signed_url_is_required_for_download()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $course = Course::factory()->create([
            'user_id' => $instructor->id,
            'slug' => 'test-course',
        ]);

        $enrollment = UserEnrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'completion_percent' => 100,
            'completed_at' => now(),
            'status' => 'completed',
        ]);

        $this->actingAs($student);

        // Try to access without signed URL
        $response = $this->get(route('certificates.download', $course->slug));

        $response->assertStatus(403);
    }
}
