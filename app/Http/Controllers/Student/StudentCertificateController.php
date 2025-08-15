<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentCertificateController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Display a listing of all earned certificates.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $query = $user->certificates()
            ->with(['course', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by year if provided
        if ($request->has('year') && $request->year !== '') {
            $query->whereYear('created_at', $request->year);
        }

        // Filter by course if provided
        if ($request->has('course_id') && $request->course_id !== '') {
            $query->where('course_id', $request->course_id);
        }

        $certificates = $query->paginate(12);
        
        // Get available years for filtering
        $availableYears = $user->certificates()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->reverse();

        // Get available courses for filtering
        $availableCourses = $user->certificates()
            ->with('course:id,judul')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->filter();

        // Get certificate statistics
        $totalCertificates = $user->certificates()->count();
        $totalJpEarned = $user->certificates()->sum('jp_value');
        $certificatesThisYear = $user->certificates()
            ->whereYear('created_at', now()->year)
            ->count();

        return view('student.certificates.index', compact(
            'certificates',
            'availableYears',
            'availableCourses',
            'totalCertificates',
            'totalJpEarned',
            'certificatesThisYear'
        ));
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate): View
    {
        $user = Auth::user();
        
        // Check if certificate belongs to user
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat mengakses sertifikat ini.');
        }

        // Check if user is enrolled in the course
        $enrollment = $user->userEnrollments()
            ->where('course_id', $certificate->course_id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus menyelesaikan kursus ini untuk mengakses sertifikat.');
        }

        // Get certificate with course and user details
        $certificate->load(['course.modules', 'user']);

        // Get course completion details
        $course = $certificate->course;
        $totalModules = $course->modules()->count();
        $completedModules = 0;

        foreach ($course->modules as $module) {
            $totalSubModules = $module->subModules()->count();
            $completedSubModules = $module->subModules()
                ->whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->count();

            if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules) {
                $completedModules++;
            }
        }

        $courseProgress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;

        // Get related certificates (same course, different users)
        $relatedCertificates = Certificate::where('course_id', $certificate->course_id)
            ->where('user_id', '!=', $user->id)
            ->with('user:id,name')
            ->limit(5)
            ->get();

        return view('student.certificates.show', compact(
            'certificate',
            'enrollment',
            'course',
            'courseProgress',
            'totalModules',
            'completedModules',
            'relatedCertificates'
        ));
    }

    /**
     * Download certificate as PDF.
     */
    public function download(Certificate $certificate): Response
    {
        $user = Auth::user();
        
        // Check if certificate belongs to user
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat mengunduh sertifikat ini.');
        }

        // Check if user is enrolled in the course
        $enrollment = $user->userEnrollments()
            ->where('course_id', $certificate->course_id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus menyelesaikan kursus ini untuk mengunduh sertifikat.');
        }

        try {
            // Load certificate with course and user details
            $certificate->load(['course', 'user']);

            // Generate PDF
            $pdf = PDF::loadView('student.certificates.pdf', compact('certificate'));

            // Set PDF properties
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            // Generate filename
            $filename = 'Sertifikat_' . $certificate->course->judul . '_' . $user->name . '_' . $certificate->created_at->format('Y-m-d') . '.pdf';

            // Return PDF download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            abort(500, 'Terjadi kesalahan saat menghasilkan sertifikat.');
        }
    }

    /**
     * View certificate as PDF in browser.
     */
    public function view(Certificate $certificate): Response
    {
        $user = Auth::user();
        
        // Check if certificate belongs to user
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat mengakses sertifikat ini.');
        }

        // Check if user is enrolled in the course
        $enrollment = $user->userEnrollments()
            ->where('course_id', $certificate->course_id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus menyelesaikan kursus ini untuk mengakses sertifikat.');
        }

        try {
            // Load certificate with course and user details
            $certificate->load(['course', 'user']);

            // Generate PDF
            $pdf = PDF::loadView('student.certificates.pdf', compact('certificate'));

            // Set PDF properties
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            // Return PDF view in browser
            return $pdf->stream('Sertifikat_' . $certificate->course->judul . '.pdf');

        } catch (\Exception $e) {
            abort(500, 'Terjadi kesalahan saat menghasilkan sertifikat.');
        }
    }

    /**
     * Get certificate data for AJAX requests.
     */
    public function getCertificateData(Certificate $certificate): JsonResponse
    {
        $user = Auth::user();
        
        // Check if certificate belongs to user
        if ($certificate->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengakses sertifikat ini.'
            ], 403);
        }

        // Load certificate with course and user details
        $certificate->load(['course.modules', 'user']);

        // Get course completion details
        $course = $certificate->course;
        $totalModules = $course->modules()->count();
        $completedModules = 0;

        foreach ($course->modules as $module) {
            $totalSubModules = $module->subModules()->count();
            $completedSubModules = $module->subModules()
                ->whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->count();

            if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules) {
                $completedModules++;
            }
        }

        $courseProgress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'certificate' => [
                    'id' => $certificate->id,
                    'certificate_number' => $certificate->certificate_number,
                    'score' => $certificate->score,
                    'jp_value' => $certificate->jp_value,
                    'created_at' => $certificate->created_at->format('d F Y'),
                    'course' => [
                        'id' => $course->id,
                        'judul' => $course->judul,
                        'deskripsi' => $course->deskripsi,
                        'bidang_kompetensi' => $course->bidang_kompetensi
                    ],
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'nip' => $user->nip,
                        'jabatan' => $user->jabatan,
                        'unit_kerja' => $user->unit_kerja
                    ]
                ],
                'course_progress' => [
                    'total_modules' => $totalModules,
                    'completed_modules' => $completedModules,
                    'percentage' => $courseProgress
                ]
            ]
        ]);
    }

    /**
     * Get certificate statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $user = Auth::user();
        
        // Get basic statistics
        $totalCertificates = $user->certificates()->count();
        $totalJpEarned = $user->certificates()->sum('jp_value');
        
        // Get certificates by year
        $certificatesByYear = $user->certificates()
            ->selectRaw('YEAR(created_at) as year, COUNT(*) as count, SUM(jp_value) as total_jp')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        // Get certificates by course
        $certificatesByCourse = $user->certificates()
            ->with('course:id,judul,bidang_kompetensi')
            ->get()
            ->groupBy('course.bidang_kompetensi')
            ->map(function ($group) {
                return [
                    'bidang_kompetensi' => $group->first()->course->bidang_kompetensi,
                    'count' => $group->count(),
                    'total_jp' => $group->sum('jp_value')
                ];
            })
            ->values();

        // Get recent certificates
        $recentCertificates = $user->certificates()
            ->with('course:id,judul')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_certificates' => $totalCertificates,
                    'total_jp_earned' => $totalJpEarned,
                    'certificates_this_year' => $user->certificates()->whereYear('created_at', now()->year)->count()
                ],
                'by_year' => $certificatesByYear,
                'by_course' => $certificatesByCourse,
                'recent' => $recentCertificates
            ]
        ]);
    }

    /**
     * Search certificates.
     */
    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->get('query');
        
        $certificates = $user->certificates()
            ->whereHas('course', function ($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('deskripsi', 'like', "%{$query}%")
                  ->orWhere('bidang_kompetensi', 'like', "%{$query}%");
            })
            ->orWhere('certificate_number', 'like', "%{$query}%")
            ->with(['course:id,judul,bidang_kompetensi'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }

    /**
     * Export certificates to CSV.
     */
    public function export(Request $request): Response
    {
        $user = Auth::user();
        
        $query = $user->certificates()
            ->with(['course', 'user']);

        // Apply filters
        if ($request->has('year') && $request->year !== '') {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->has('course_id') && $request->course_id !== '') {
            $query->where('course_id', $request->course_id);
        }

        $certificates = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV content
        $csvContent = "No,No Sertifikat,Kursus,Bidang Kompetensi,JP,Nilai,Tanggal\n";
        
        foreach ($certificates as $index => $certificate) {
            $csvContent .= sprintf(
                "%d,%s,%s,%s,%d,%.2f,%s\n",
                $index + 1,
                $certificate->certificate_number,
                $certificate->course->judul,
                $certificate->course->bidang_kompetensi,
                $certificate->jp_value,
                $certificate->score,
                $certificate->created_at->format('d/m/Y')
            );
        }

        // Return CSV download
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sertifikat_' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Validate certificate authenticity.
     */
    public function validateCertificate(Request $request): JsonResponse
    {
        $request->validate([
            'certificate_number' => 'required|string'
        ]);

        $certificateNumber = $request->get('certificate_number');
        
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->with(['course', 'user'])
            ->first();

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'certificate_number' => $certificate->certificate_number,
                'course' => [
                    'judul' => $certificate->course->judul,
                    'bidang_kompetensi' => $certificate->course->bidang_kompetensi
                ],
                'user' => [
                    'name' => $certificate->user->name,
                    'nip' => $certificate->user->nip
                ],
                'score' => $certificate->score,
                'jp_value' => $certificate->jp_value,
                'created_at' => $certificate->created_at->format('d F Y'),
                'is_valid' => true
            ]
        ]);
    }
} 