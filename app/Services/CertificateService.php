<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\UserEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Ensure a certificate exists for the user and course.
     * Returns the certificate path and UID.
     *
     * @param User $user
     * @param Course $course
     * @return array{path: string, uid: string}
     * @throws \Exception
     */
    public function ensureCertificate(User $user, Course $course): array
    {
        // Check if certificate already exists
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($certificate && $certificate->file_path && Storage::disk(config('certificates.storage_disk'))->exists($certificate->file_path)) {
            return [
                'path' => $certificate->file_path,
                'uid' => $certificate->certificate_uid ?? Str::uuid()->toString(),
            ];
        }

        // Get enrollment to check completion
        $enrollment = UserEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            throw new \Exception('User is not enrolled in this course.');
        }

        // Eligibility check should mirror policy:
        // - completion_percent == 100 OR status == 'completed'
        // - completed_at is not null
        $isCompletedByPercent = (int) ($enrollment->completion_percent ?? 0) === 100;
        $isCompletedByStatus = ($enrollment->status ?? null) === 'completed';

        if (!($isCompletedByPercent || $isCompletedByStatus) || !$enrollment->completed_at) {
            throw new \Exception('Course completion is not 100%.');
        }

        // Generate certificate
        $uid = Str::uuid()->toString();
        $storagePath = $this->buildStoragePath($course, $user);
        $htmlData = $this->pdfHtmlData($user, $course, $enrollment, $uid);

        // Generate PDF
        $pdf = Pdf::loadView('certificates.template', $htmlData);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('margin-top', 0.5);
        $pdf->setOption('margin-bottom', 0.5);
        $pdf->setOption('margin-left', 0.5);
        $pdf->setOption('margin-right', 0.5);
        $pdf->setOption('enable-remote', true);

        // Ensure directory exists
        $directory = dirname($storagePath);
        Storage::disk(config('certificates.storage_disk'))->makeDirectory($directory);

        // Save PDF
        Storage::disk(config('certificates.storage_disk'))->put($storagePath, $pdf->output());

        // Create or update certificate record
        if ($certificate) {
            $certificate->update([
                'certificate_uid' => $uid,
                'file_path' => $storagePath,
                'generated_at' => now(),
                'issue_date' => $enrollment->completed_at ?? now(),
            ]);
        } else {
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_uid' => $uid,
                'nomor_sertifikat' => 'CERT-' . strtoupper(Str::random(10)),
                'file_path' => $storagePath,
                'generated_at' => now(),
                'issue_date' => $enrollment->completed_at ?? now(),
            ]);
        }

        return [
            'path' => $storagePath,
            'uid' => $uid,
        ];
    }

    /**
     * Get HTML data for PDF generation.
     *
     * @param User $user
     * @param Course $course
     * @param UserEnrollment $enrollment
     * @param string $uid
     * @return array
     */
    public function pdfHtmlData(User $user, Course $course, UserEnrollment $enrollment, string $uid): array
    {
        // Eager-load modules once for competency listing on the certificate
        $course->loadMissing('modules');

        return [
            'student_name' => $user->name,
            'course_title' => $course->judul ?? $course->title,
            'instructor_name' => $course->owner?->name ?? '',
            'completion_date' => $enrollment->completed_at?->format('d F Y') ?? now()->format('d F Y'),
            'certificate_uid' => $uid,
            'issuer_name' => config('certificates.issuer_name'),
            'background_image' => config('certificates.background_image'),
            // Optional extra data for richer templates
            'jp_value' => $course->jp_value ?? null,
            'competencies' => $course->modules
                ? $course->modules->pluck('judul')->filter()->values()->all()
                : [],
            // Final score can be wired later if you add it to enrollments; keep nullable for now
            'final_score' => null,
        ];
    }

    /**
     * Build storage path for certificate.
     * Format: certificates/<year>/<course_slug>/<user_id>.pdf
     *
     * @param Course $course
     * @param User $user
     * @return string
     */
    public function buildStoragePath(Course $course, User $user): string
    {
        $year = now()->year;
        $slug = $course->slug ?? Str::slug($course->judul ?? 'course');
        
        return "certificates/{$year}/{$slug}/{$user->id}.pdf";
    }
}

