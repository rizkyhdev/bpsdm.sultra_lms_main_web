<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class InstructorDashboardController
 *
 * Provides instructor dashboard metrics and per-course overviews.
 */
class InstructorDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'instructor']);
    }

    /**
     * Display the instructor dashboard metrics.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $instructorId = Auth::id();

        $coursesQuery = Course::query()
            ->where('user_id', $instructorId)
            ->withCount('modules')
            ->withCount(['userEnrollments as enrollments_count'])
            ->orderBy('id', 'desc');

        $totalCourses = (clone $coursesQuery)->count();
        $totalEnrollments = UserEnrollment::query()
            ->whereIn('course_id', (clone $coursesQuery)->pluck('id'))
            ->count();

        $recentActivity = QuizAttempt::query()
            ->whereIn('quiz_id', function ($q) use ($instructorId) {
                $q->select('quizzes.id')
                    ->from('quizzes')
                    ->join('sub_modules', 'quizzes.sub_module_id', '=', 'sub_modules.id')
                    ->join('modules', 'sub_modules.module_id', '=', 'modules.id')
                    ->join('courses', 'modules.course_id', '=', 'courses.id')
                    ->where('courses.user_id', $instructorId);
            })
            ->latest('completed_at')
            ->take(10)
            ->get();

        // Simple completion rate: completed enrollments over total enrollments
        $completedEnrollments = UserEnrollment::query()
            ->whereIn('course_id', (clone $coursesQuery)->pluck('id'))
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 2)
            : 0.0;

        return view('instructor.dashboard', [
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
            'completionRate' => $completionRate,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Display an overview for a single course owned by the instructor.
     *
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function courseOverview($courseId)
    {
        $course = Course::with([
            'modules.subModules.quizzes',
            'userEnrollments',
        ])->findOrFail($courseId);

        $this->authorize('view', $course);

        $enrollmentsCount = $course->userEnrollments()->count();
        $completedEnrollments = $course->userEnrollments()->where('status', 'completed')->count();
        $completionRate = $enrollmentsCount > 0
            ? round(($completedEnrollments / $enrollmentsCount) * 100, 2)
            : 0.0;

        // Basic quiz performance snapshot
        $quizIds = $course->modules->flatMap(function ($m) {
            return $m->subModules->flatMap(function ($s) {
                return $s->quizzes->pluck('id');
            });
        })->values();

        $attemptsSummary = QuizAttempt::query()
            ->selectRaw('quiz_id, COUNT(*) as attempts, AVG(nilai) as avg_score')
            ->whereIn('quiz_id', $quizIds)
            ->groupBy('quiz_id')
            ->orderBy('quiz_id')
            ->get();

        return view('instructor.course_overview', [
            'course' => $course,
            'enrollmentsCount' => $enrollmentsCount,
            'completionRate' => $completionRate,
            'attemptsSummary' => $attemptsSummary,
        ]);
    }
}


