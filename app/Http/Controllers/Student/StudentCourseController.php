<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentCourseController extends Controller
{
    /**
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Menampilkan daftar semua kursus yang tersedia.
     */
    public function index(Request $request): View
    {
        $query = Course::with(['modules.subModules', 'userEnrollments' => function ($query) {
            $query->where('user_id', Auth::id());
        }]);

        // Filter berdasarkan bidang kompetensi jika disediakan
        if ($request->has('bidang_kompetensi') && $request->bidang_kompetensi !== '') {
            $query->where('bidang_kompetensi', $request->bidang_kompetensi);
        }

        // Pencarian berdasarkan judul atau deskripsi
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Opsi pengurutan
        $sortBy = $request->get('sort_by', 'judul');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['judul', 'jp_value', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $courses = $query->paginate(12);
        $bidangKompetensi = Course::distinct()->pluck('bidang_kompetensi')->filter();

        return view('student.courses.index', compact('courses', 'bidangKompetensi'));
    }

    /**
     * Menampilkan kursus tertentu.
     */
    public function show(Course $course): View
    {
        $user = Auth::user();
        
        // Periksa apakah user sudah terdaftar
        $enrollment = $user->userEnrollments()
            ->where('course_id', $course->id)
            ->first();

        // Mendapatkan modul kursus dengan sub-modul
        $modules = $course->modules()
            ->with(['subModules' => function ($query) {
                $query->orderBy('urutan');
            }])
            ->orderBy('urutan')
            ->get();

        // Menghitung progress untuk setiap modul
        $moduleProgress = [];
        foreach ($modules as $module) {
            $totalSubModules = $module->subModules->count();
            $completedSubModules = 0;

            foreach ($module->subModules as $subModule) {
                $progress = $subModule->userProgress()
                    ->where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->first();
                
                if ($progress) {
                    $completedSubModules++;
                }
            }

            $moduleProgress[$module->id] = [
                'total' => $totalSubModules,
                'completed' => $completedSubModules,
                'percentage' => $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0
            ];
        }

        // Mendapatkan statistik kursus
        $totalStudents = $course->userEnrollments()->count();
        $completedStudents = $course->userEnrollments()->whereNotNull('completed_at')->count();
        $averageScore = $course->certificates()->avg('score') ?? 0;

        return view('student.courses.show', compact(
            'course',
            'enrollment',
            'modules',
            'moduleProgress',
            'totalStudents',
            'completedStudents',
            'averageScore'
        ));
    }

    /**
     * Mendaftarkan user yang terautentikasi ke dalam kursus.
     */
    public function enroll(Course $course): JsonResponse
    {
        $user = Auth::user();

        // Periksa apakah sudah terdaftar
        $existingEnrollment = $user->userEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            if ($existingEnrollment->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah terdaftar dalam kursus ini.'
                ], 400);
            } elseif ($existingEnrollment->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah menyelesaikan kursus ini.'
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            if ($existingEnrollment) {
                // Mengaktifkan kembali pendaftaran
                $existingEnrollment->update([
                    'status' => 'active',
                    'enrollment_date' => now(),
                    'completed_at' => null
                ]);
                $enrollment = $existingEnrollment;
            } else {
                // Membuat pendaftaran baru
                $enrollment = $user->userEnrollments()->create([
                    'course_id' => $course->id,
                    'enrollment_date' => now(),
                    'status' => 'active'
                ]);
            }

            // Inisialisasi record progress untuk semua sub-modul
            foreach ($course->modules as $module) {
                foreach ($module->subModules as $subModule) {
                    UserProgress::firstOrCreate([
                        'user_id' => $user->id,
                        'sub_module_id' => $subModule->id
                    ], [
                        'is_completed' => false,
                        'progress_percentage' => 0,
                        'started_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendaftar ke kursus: ' . $course->judul,
                'enrollment' => $enrollment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Menampilkan kursus yang diikuti oleh user (pembelajaran saya).
     */
    public function myLearning(Request $request): View
    {
        $user = Auth::user();
        $query = $user->userEnrollments()
            ->with(['course.modules.subModules', 'course.modules.subModules.userProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan penyelesaian
        if ($request->has('completion') && $request->completion !== '') {
            if ($request->completion === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->completion === 'in_progress') {
                $query->whereNull('completed_at');
            }
        }

        $enrollments = $query->orderBy('enrollment_date', 'desc')->paginate(10);

        // Menghitung progress untuk setiap pendaftaran
        $enrollmentProgress = [];
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $totalSubModules = 0;
            $completedSubModules = 0;

            foreach ($course->modules as $module) {
                foreach ($module->subModules as $subModule) {
                    $totalSubModules++;
                    $progress = $subModule->userProgress()
                        ->where('user_id', $user->id)
                        ->where('is_completed', true)
                        ->first();
                    
                    if ($progress) {
                        $completedSubModules++;
                    }
                }
            }

            $enrollmentProgress[$enrollment->id] = [
                'total' => $totalSubModules,
                'completed' => $completedSubModules,
                'percentage' => $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0
            ];
        }

        return view('student.courses.my-learning', compact('enrollments', 'enrollmentProgress'));
    }

    /**
     * Melacak progress pembelajaran untuk kursus tertentu.
     */
    public function trackProgress(Course $course): JsonResponse
    {
        $user = Auth::user();
        
        // Periksa apakah user sudah terdaftar
        $enrollment = $user->userEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kursus ini.'
            ], 404);
        }

        // Mendapatkan progress detail
        $modules = $course->modules()
            ->with(['subModules.userProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('urutan')
            ->get();

        $progressData = [];
        $totalSubModules = 0;
        $completedSubModules = 0;
        $totalJpEarned = 0;

        foreach ($modules as $module) {
            $moduleProgress = [
                'module_id' => $module->id,
                'judul' => $module->judul,
                'sub_modules' => []
            ];

            foreach ($module->subModules as $subModule) {
                $totalSubModules++;
                $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
                
                if ($progress && $progress->is_completed) {
                    $completedSubModules++;
                    $totalJpEarned += $course->jp_value / $totalSubModules; // JP proporsional
                }

                $moduleProgress['sub_modules'][] = [
                    'sub_module_id' => $subModule->id,
                    'judul' => $subModule->judul,
                    'is_completed' => $progress ? $progress->is_completed : false,
                    'progress_percentage' => $progress ? $progress->progress_percentage : 0,
                    'started_at' => $progress ? $progress->started_at : null,
                    'completed_at' => $progress ? $progress->completed_at : null
                ];
            }

            $progressData[] = $moduleProgress;
        }

        $overallProgress = $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;

        // Periksa apakah kursus sudah selesai
        if ($overallProgress >= 100 && $enrollment->status !== 'completed') {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'course' => $course,
                'enrollment' => $enrollment,
                'modules' => $progressData,
                'overall_progress' => $overallProgress,
                'total_sub_modules' => $totalSubModules,
                'completed_sub_modules' => $completedSubModules,
                'total_jp_earned' => round($totalJpEarned, 2)
            ]
        ]);
    }

    /**
     * Unenroll from a course.
     */
    public function unenroll(Course $course): JsonResponse
    {
        $user = Auth::user();
        
        $enrollment = $user->userEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kursus ini.'
            ], 404);
        }

        try {
            $enrollment->update(['status' => 'cancelled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil keluar dari kursus: ' . $course->judul
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat keluar dari kursus.'
            ], 500);
        }
    }

    /**
     * Get course recommendations based on user's learning history.
     */
    public function getRecommendations(): JsonResponse
    {
        $user = Auth::user();
        
        // Get user's completed courses
        $completedCourses = $user->userEnrollments()
            ->where('status', 'completed')
            ->with('course')
            ->get()
            ->pluck('course.bidang_kompetensi')
            ->filter()
            ->unique();

        // Get recommended courses in similar fields
        $recommendations = Course::whereIn('bidang_kompetensi', $completedCourses)
            ->whereDoesntHave('userEnrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('modules')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }
} 