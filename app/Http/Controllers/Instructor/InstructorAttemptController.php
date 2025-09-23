<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\GradeEssayRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kelas InstructorAttemptController
 * Mengelola percobaan/attempt kuis pada kursus milik instruktur.
 */
class InstructorAttemptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Daftar attempt untuk sebuah kuis.
     * @param Request $request
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $quizId)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($quizId);
        $this->authorize('view', $quiz);

        $attempts = QuizAttempt::with('user')
            ->where('quiz_id', $quiz->id)
            ->latest('completed_at')
            ->paginate(15);

        return view('instructor.attempts.index', compact('quiz', 'attempts'));
    }

    /**
     * Tampilkan detail attempt.
     * @param int $attemptId
     * @return \Illuminate\Http\Response
     */
    public function show($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.subModule.module.course', 'userAnswers.question.answerOptions', 'user'])->findOrFail($attemptId);
        $this->authorize('view', $attempt);
        return view('instructor.attempts.show', compact('attempt'));
    }

    /**
     * Nilai pertanyaan esai dan hitung ulang skor total.
     * @param GradeEssayRequest $request
     * @param int $attemptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function gradeEssay(GradeEssayRequest $request, $attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.subModule.module.course', 'userAnswers.question'])->findOrFail($attemptId);
        $this->authorize('update', $attempt);

        $grades = $request->validated()['grades'];

        DB::beginTransaction();
        try {
            $totalScore = 0;
            $maxTotal = 0;

            foreach ($attempt->userAnswers as $answer) {
                /** @var Question $question */
                $question = $answer->question;
                $maxTotal += (int) $question->bobot;

                if ($question->tipe === 'essay') {
                    $score = isset($grades[$question->id]) ? (int) $grades[$question->id] : 0;
                    if ($score < 0) { $score = 0; }
                    if ($score > (int) $question->bobot) { $score = (int) $question->bobot; }
                    $answer->manual_score = $score;
                    $answer->save();
                    $totalScore += $score;
                } else {
                    // Auto-graded: assume correctness from is_correct
                    $totalScore += (int) ($answer->is_correct ? $question->bobot : 0);
                }
            }

            $attempt->nilai = $maxTotal > 0 ? round(($totalScore / $maxTotal) * 100, 2) : 0;
            $attempt->is_passed = $attempt->nilai >= $attempt->quiz->nilai_minimum;
            $attempt->save();

            DB::commit();
            return redirect()->route('instructor.attempts.show', $attempt->id)->with('success', 'Essay graded and score updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to grade essay.');
        }
    }
}


