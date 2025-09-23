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
 * Kelas InstructorDashboardController
 *
 * Menyediakan metrik dashboard instruktur dan ringkasan per kursus.
 */
class InstructorDashboardController extends Controller
{
    /**
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Menampilkan metrik dashboard instruktur.
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
        $ownedCourseIds = (clone $coursesQuery)->pluck('id');
        $totalEnrollments = UserEnrollment::query()
            ->whereIn('course_id', $ownedCourseIds)
            ->count();

        $instructorQuizIds = QuizAttempt::query()
            ->select('quizzes.id')
            ->from('quizzes')
            ->join('sub_modules', 'quizzes.sub_module_id', '=', 'sub_modules.id')
            ->join('modules', 'sub_modules.module_id', '=', 'modules.id')
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->where('courses.user_id', $instructorId)
            ->distinct()
            ->pluck('quizzes.id');

        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->whereIn('quiz_id', $instructorQuizIds)
            ->latest('completed_at')
            ->take(10)
            ->get();

        // Tingkat penyelesaian sederhana: jumlah enrollment selesai dibanding total enrollment
        $completedEnrollments = UserEnrollment::query()
            ->whereIn('course_id', $ownedCourseIds)
            ->where('status', 'completed')
            ->count();
        $completionRate = $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 2)
            : 0.0;

        // Rata-rata nilai kuis untuk kuis milik instruktur
        $avgQuizScore = QuizAttempt::query()
            ->whereIn('quiz_id', $instructorQuizIds)
            ->avg('nilai');
        $avgQuizScore = $avgQuizScore ? round((float) $avgQuizScore, 2) : 0.0;

        // Enrollments terbaru pada kursus milik instruktur
        $recentEnrollments = UserEnrollment::with(['user', 'course'])
            ->whereIn('course_id', $ownedCourseIds)
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('instructor.dashboard', [
            'metrics' => [
                'total_courses' => $totalCourses,
                'total_enrollments' => $totalEnrollments,
                'avg_completion' => $completionRate,
                'avg_quiz_score' => $avgQuizScore,
            ],
            'recentEnrollments' => $recentEnrollments,
            'recentAttempts' => $recentAttempts,
        ]);
    }

    /**
     * Menampilkan ringkasan untuk satu kursus milik instruktur.
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

        // Gambaran kinerja kuis dasar
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


