<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class CertificateController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService
    ) {
    }

    /**
     * Generate a certificate for the authenticated user.
     *
     * @param Course $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Course $course)
    {
        $user = auth()->user();

        $this->authorize('downloadCertificate', $course);

        try {
            $result = $this->certificateService->ensureCertificate($user, $course);
            
            $signedUrl = $this->getSignedDownloadUrl($course);

            return response()->json([
                'success' => true,
                'message' => __('Certificate generated successfully.'),
                'download_url' => $signedUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Download the certificate PDF.
     *
     * @param Request $request
     * @param Course $course
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function download(Request $request, Course $course)
    {
        // if(!request()->hasValidSignature()) {
        //     abort(403, 'Invalid signature.');
        // }

        $user = auth()->user();

        $this->authorize('downloadCertificate', $course);

        try {
            $result = $this->certificateService->ensureCertificate($user, $course);
            
            $filePath = $result['path'];
            $disk = config('certificates.storage_disk');

            if (!Storage::disk($disk)->exists($filePath)) {
                abort(404, 'Certificate file not found.');
            }

            return Storage::disk($disk)->response($filePath, 'certificate.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="certificate-' . $course->slug . '.pdf"',
            ]);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
    }

    /**
     * Verify a certificate by UID.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */
    public function verify(string $uid)
    {
        $certificate = Certificate::where('certificate_uid', $uid)->first();

        if (!$certificate) {
            return view('certificates.verify', [
                'certificate' => null,
                'status' => 'not_found',
            ]);
        }

        $certificate->load(['user', 'course']);

        return view('certificates.verify', [
            'certificate' => $certificate,
            'status' => 'valid',
            'student_name' => $certificate->user->name,
            'course_title' => $certificate->course->judul ?? $certificate->course->title,
            'completion_date' => $certificate->issue_date?->format('d F Y'),
        ]);
    }

    /**
     * Preview certificate template (admin/instructor only).
     *
     * @param Course $course
     * @return \Illuminate\View\View
     */
    public function preview(Course $course)
    {
        $this->authorize('preview', $course);

        // Use a dummy user and enrollment for preview
        $user = auth()->user();
        $enrollment = $course->enrollments()
            ->where('user_id', $user->id)
            ->first();

        if (!$enrollment) {
            // Create a dummy enrollment for preview
            $enrollment = new \App\Models\UserEnrollment([
                'completion_percent' => 100,
                'completed_at' => now(),
            ]);
        }

        $uid = \Illuminate\Support\Str::uuid()->toString();
        $htmlData = $this->certificateService->pdfHtmlData($user, $course, $enrollment, $uid);

        return view('certificates.template', $htmlData);
    }

    /**
     * Get a signed download URL for the certificate.
     *
     * @param Course $course
     * @return string
     */
    protected function getSignedDownloadUrl(Course $course): string
    {
        $ttl = config('certificates.download_ttl_minutes', 30);

        return URL::temporarySignedRoute(
            'certificates.download',
            now()->addMinutes($ttl),
            ['course' => $course->slug]
        );
    }
}

