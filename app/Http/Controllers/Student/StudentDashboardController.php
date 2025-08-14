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
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Display the student dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // Get enrolled courses with progress
        $enrolledCourses = $user->userEnrollments()
            ->with(['course.modules.subModules', 'course.modules.subModules.userProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('status', 'active')
            ->get();

        // Calculate learning progress for each course
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

        // Get JP accumulated for current year
        $jpAccumulated = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_value');

        // Get upcoming quizzes (not yet attempted or failed attempts)
        $upcomingQuizzes = Quiz::whereHas('subModule.module.course', function ($query) use ($user) {
            $query->whereHas('userEnrollments', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'active');
            });
        })->whereDoesntHave('quizAttempts', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'passed');
        })->with(['subModule.module.course'])
        ->get();

        // Get recent activities (last 10 progress updates)
        $recentActivities = $user->userProgress()
            ->with(['subModule.module.course'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get total JP earned
        $totalJpEarned = $user->jpRecords()->sum('jp_value');

        // Get courses in progress (started but not completed)
        $coursesInProgress = $enrolledCourses->filter(function ($enrollment) {
            return $enrollment->completed_at === null;
        });

        // Get completed courses
        $completedCourses = $enrolledCourses->filter(function ($enrollment) {
            return $enrollment->completed_at !== null;
        });

        return view('student.dashboard', compact(
            'courseProgress',
            'jpAccumulated',
            'upcomingQuizzes',
            'recentActivities',
            'totalJpEarned',
            'coursesInProgress',
            'completedCourses',
            'currentYear'
        ));
    }

    /**
     * Get dashboard data for AJAX requests.
     */
    public function getDashboardData(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->get('year', now()->year);

        // Get JP records for the specified year
        $jpRecords = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get monthly JP summary
        $monthlyJp = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyJp[$month] = $user->jpRecords()
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('jp_value');
        }

        return response()->json([
            'jpRecords' => $jpRecords,
            'monthlyJp' => $monthlyJp,
            'totalJp' => array_sum($monthlyJp)
        ]);
    }

    /**
     * Get learning statistics.
     */
    public function getLearningStats()
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // Get total courses enrolled
        $totalCourses = $user->userEnrollments()->count();

        // Get completed courses this year
        $completedCoursesThisYear = $user->userEnrollments()
            ->whereYear('completed_at', $currentYear)
            ->count();

        // Get total JP earned this year
        $jpThisYear = $user->jpRecords()
            ->whereYear('created_at', $currentYear)
            ->sum('jp_value');

        // Get average quiz score
        $averageQuizScore = $user->quizAttempts()
            ->where('status', 'completed')
            ->avg('score');

        // Get total study time (estimated from progress records)
        $totalStudyTime = $user->userProgress()
            ->where('is_completed', true)
            ->count() * 30; // Assuming 30 minutes per completed sub-module

        return response()->json([
            'totalCourses' => $totalCourses,
            'completedCoursesThisYear' => $completedCoursesThisYear,
            'jpThisYear' => $jpThisYear,
            'averageQuizScore' => round($averageQuizScore, 2),
            'totalStudyTime' => $totalStudyTime
        ]);
    }
} 