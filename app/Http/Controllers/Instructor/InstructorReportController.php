<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Kelas InstructorReportController
 * Menyediakan laporan tingkat kursus dan kuis untuk instruktur.
 */
class InstructorReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Metrik laporan kursus.
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function courseReport($courseId)
    {
        $course = Course::with('modules.subModules.quizzes')->findOrFail($courseId);
        $this->authorize('view', $course);

        $quizIds = $course->modules->flatMap(function ($m) {
            return $m->subModules->flatMap(function ($s) {
                return $s->quizzes->pluck('id');
            });
        })->values();

        $attempts = QuizAttempt::whereIn('quiz_id', $quizIds);
        $avgScore = (clone $attempts)->avg('nilai');
        $attemptsCount = (clone $attempts)->count();

        return view('instructor.reports.course', compact('course', 'avgScore', 'attemptsCount'));
    }

    /**
     * Laporan kuis dengan analisis butir per-pertanyaan.
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function quizReport($quizId)
    {
        $quiz = Quiz::with(['subModule.module.course', 'questions'])->findOrFail($quizId);
        $this->authorize('view', $quiz);

        $questionStats = [];
        foreach ($quiz->questions as $q) {
            // Placeholder for item analysis: you may compute correct rates from user_answers
            $questionStats[$q->id] = [
                'question' => $q,
                'correct_rate' => null,
                'discrimination' => null,
            ];
        }

        return view('instructor.reports.quiz', compact('quiz', 'questionStats'));
    }

    /**
     * Ekspor laporan.
     * @param string $type
     * @param int $scopeId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export($type, $scopeId)
    {
        // Untuk ringkasnya, kembalikan string CSV. Kembangkan ke CSV/XLSX/PDF nyata bila perlu.
        if ($type === 'course') {
            $course = Course::findOrFail($scopeId);
            $this->authorize('view', $course);
            $csv = "Course Report: {$course->judul}\n";
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="course_report.csv"',
            ]);
        }
        if ($type === 'quiz') {
            $quiz = Quiz::with('subModule.module.course')->findOrFail($scopeId);
            $this->authorize('view', $quiz);
            $csv = "Quiz Report: {$quiz->judul}\n";
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="quiz_report.csv"',
            ]);
        }
        abort(404);
    }
}


