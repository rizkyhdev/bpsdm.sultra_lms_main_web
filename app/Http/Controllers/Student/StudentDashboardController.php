<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\JpRecord;
use App\Models\Quiz;
use App\Models\UserEnrollment;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentDashboardController extends Controller
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
     * Menampilkan dashboard siswa.
     */
    public function index(): View
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // KPI counts
        $enrolledCount = $user->userEnrollments()->count();
        $inProgressCount = $user->userEnrollments()
            ->where('status', 'in_progress')
            ->whereNull('completed_at')
            ->count();
        $completedCount = $user->userEnrollments()
            ->whereNotNull('completed_at')
            ->count();
        $totalJp = $user->userEnrollments()
            ->whereNotNull('completed_at')
            ->with('course')
            ->get()
            ->sum(function ($enrollment) {
                return $enrollment->course->jp_value ?? 0;
            });

        // Mendapatkan kursus yang diikuti dengan progress
        $enrolledCourses = $user->userEnrollments()
            ->with(['course.modules.subModules', 'course.modules.subModules.userProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        // Menghitung progress pembelajaran untuk setiap kursus
        $courseProgress = [];
        foreach ($enrolledCourses as $enrollment) {
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

            $courseProgress[$course->id] = [
                'course' => $course,
                'total' => $totalSubModules,
                'completed' => $completedSubModules,
                'percentage' => $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0
            ];
        }

        // Mendapatkan JP yang terkumpul untuk tahun berjalan
        $jpAccumulated = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_earned');

        // Mendapatkan quiz yang akan datang (belum diikuti atau gagal)
        $upcomingQuizzes = Quiz::whereHas('subModule.module.course', function ($query) use ($user) {
            $query->whereHas('userEnrollments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('status', ['enrolled', 'in_progress'])
                  ->whereNull('completed_at');
            });
        })->whereDoesntHave('quizAttempts', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_passed', true);
        })->with(['subModule.module.course'])
        ->get();

        // Mendapatkan aktivitas terbaru (10 update progress terakhir)
        $recentActivities = $user->userProgress()
            ->with(['subModule.module.course'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Mendapatkan total JP yang diperoleh
        $totalJpEarned = $user->jpRecords()->sum('jp_earned');

        // Mendapatkan kursus yang sedang berlangsung (dimulai tapi belum selesai)
        $coursesInProgress = $enrolledCourses->filter(function ($enrollment) {
            return $enrollment->completed_at === null;
        });

        // Mendapatkan kursus yang sudah selesai
        $completedCourses = $enrolledCourses->filter(function ($enrollment) {
            return $enrollment->completed_at !== null;
        });

        // Pelatihan Tersedia: latest 9 courses where NOT exists enrollment for current user
        $enrolledCourseIds = $user->userEnrollments()->pluck('course_id')->toArray();
        
        $availableCourses = Course::whereNotIn('id', $enrolledCourseIds)
            ->withCount([
                'userEnrollments as enrollments_count',
                'subModules as contents_count'
            ])
            ->with(['modules.subModules'])
            ->latest()
            ->limit(9)
            ->get()
            ->map(function ($course) {
                // Calculate rating_avg from quiz_attempts.nilai
                $ratingAvg = \App\Models\QuizAttempt::whereHas('quiz.subModule.module.course', function ($q) use ($course) {
                    $q->where('id', $course->id);
                })->avg('nilai');
                
                // Derive difficulty from sub_module count
                $subModuleCount = $course->subModules()->count();
                if ($subModuleCount <= 4) {
                    $difficulty = 'Beginner';
                } elseif ($subModuleCount <= 9) {
                    $difficulty = 'Intermediate';
                } else {
                    $difficulty = 'Expert';
                }
                
                $course->rating_avg = round($ratingAvg ?? 0, 2);
                $course->difficulty = $difficulty;
                
                return $course;
            });

        return view('student.dashboard', compact(
            'enrolledCount',
            'inProgressCount',
            'completedCount',
            'totalJp',
            'courseProgress',
            'jpAccumulated',
            'upcomingQuizzes',
            'recentActivities',
            'totalJpEarned',
            'coursesInProgress',
            'completedCourses',
            'currentYear',
            'availableCourses'
        ));
    }

    /**
     * Mendapatkan data dashboard untuk permintaan AJAX.
     */
    public function getDashboardData(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->get('year', now()->year);

        // Mendapatkan record JP untuk tahun yang ditentukan
        $jpRecords = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        // Mendapatkan ringkasan JP bulanan
        $monthlyJp = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyJp[$month] = $user->jpRecords()
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('jp_earned');
        }

        return response()->json([
            'jpRecords' => $jpRecords,
            'monthlyJp' => $monthlyJp,
            'totalJp' => array_sum($monthlyJp)
        ]);
    }

    /**
     * Mendapatkan statistik pembelajaran.
     */
    public function getLearningStats()
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // Mendapatkan total kursus yang diikuti
        $totalCourses = $user->userEnrollments()->count();

        // Mendapatkan kursus yang selesai tahun ini
        $completedCoursesThisYear = $user->userEnrollments()
            ->whereYear('completed_at', $currentYear)
            ->count();

        // Mendapatkan total JP yang diperoleh tahun ini
        $jpThisYear = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_earned');

        // Mendapatkan rata-rata skor quiz
        $averageQuizScore = $user->quizAttempts()
            ->where('status', 'completed')
            ->avg('score');

        // Mendapatkan total waktu belajar (diperkirakan dari record progress)
        $totalStudyTime = $user->userProgress()
            ->where('is_completed', true)
            ->count() * 30; // Mengasumsikan 30 menit per sub-modul yang selesai

        return response()->json([
            'totalCourses' => $totalCourses,
            'completedCoursesThisYear' => $completedCoursesThisYear,
            'jpThisYear' => $jpThisYear,
            'averageQuizScore' => round($averageQuizScore, 2),
            'totalStudyTime' => $totalStudyTime
        ]);
    }
} 