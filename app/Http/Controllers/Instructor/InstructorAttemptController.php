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
        $attempt = QuizAttempt::with([
            'quiz.subModule.module.course', 
            'quiz.questions.answerOptions',
            'userAnswers.question.answerOptions', 
            'userAnswers.answerOption', 
            'user'
        ])->findOrFail($attemptId);
        $this->authorize('view', $attempt);

        // Prepare items array for the view
        // Get all questions for the quiz to ensure we show all questions, even if not answered
        $allQuestions = $attempt->quiz->questions()->with('answerOptions')->orderBy('urutan')->get();
        $userAnswersByQuestionId = $attempt->userAnswers->keyBy('question_id');
        
        $items = [];
        foreach ($allQuestions as $question) {
            $userAnswer = $userAnswersByQuestionId->get($question->id);
            $correctOption = $question->answerOptions->where('is_correct', true)->first();
            
            // Determine if answer is correct
            $isCorrect = false;
            $jawaban = '';
            
            if ($question->tipe === 'essay') {
                // For essay questions, get the answer text from answerOption if available
                // Essay answers might be stored in answerOption->teks_jawaban
                if ($userAnswer && $userAnswer->answerOption) {
                    $jawaban = $userAnswer->answerOption->teks_jawaban;
                } elseif ($userAnswer) {
                    // Check if there's a text answer stored elsewhere
                    $jawaban = 'No answer provided';
                } else {
                    $jawaban = 'No answer provided';
                }
                // Check if manually graded (manual_score column might exist)
                $graded = $userAnswer && isset($userAnswer->manual_score) && $userAnswer->manual_score !== null;
                $score = $graded ? $userAnswer->manual_score : null;
            } else {
                // For multiple choice/true-false, check if selected answer is correct
                if ($userAnswer && $userAnswer->answerOption && $correctOption) {
                    $isCorrect = $userAnswer->answerOption->id === $correctOption->id;
                    $jawaban = $userAnswer->answerOption->teks_jawaban;
                } elseif ($userAnswer && $userAnswer->answerOption) {
                    $jawaban = $userAnswer->answerOption->teks_jawaban;
                    $isCorrect = false; // No correct option or answer doesn't match
                } else {
                    $jawaban = 'No answer selected';
                    $isCorrect = false;
                }
            }
            
            $items[] = [
                'question_id' => $question->id,
                'pertanyaan' => $question->pertanyaan,
                'tipe' => $question->tipe,
                'bobot' => $question->bobot,
                'jawaban' => $jawaban,
                'is_correct' => $isCorrect,
                'graded' => $userAnswer && isset($userAnswer->manual_score) && $userAnswer->manual_score !== null,
                'score' => ($userAnswer && isset($userAnswer->manual_score)) ? $userAnswer->manual_score : null,
            ];
        }

        return view('instructor.attempts.show', compact('attempt', 'items'));
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


