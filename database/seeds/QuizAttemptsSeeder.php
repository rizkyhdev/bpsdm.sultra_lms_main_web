<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use App\Models\AnswerOption;
use App\Models\Question;

class QuizAttemptsSeeder extends Seeder
{
    /**
     * Untuk tiap student dan kuis yang terkait dengan course yang diikuti,
     * buat 0–2 attempt; buat user_answers sesuai skor.
     */
    public function run()
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // ambil semua kuis dari submodul yang ada di course enrollments student
            $quizIds = Quiz::whereHas('subModule.module', function ($q) use ($student) {
                $q->whereHas('course.userEnrollments', function ($q2) use ($student) {
                    $q2->where('user_id', $student->id);
                });
            })->pluck('id')->all();

            foreach ($quizIds as $quizId) {
                $attempts = rand(0, 2);
                $attemptNumber = 1;
                for ($a = 0; $a < $attempts; $a++, $attemptNumber++) {
                    $started = Carbon::now()->subDays(rand(5, 180));

                    $attempt = factory(QuizAttempt::class)->create([
                        'user_id' => $student->id,
                        'quiz_id' => $quizId,
                        'attempt_number' => $attemptNumber,
                        'started_at' => $started,
                        'completed_at' => (clone $started)->addMinutes(rand(10, 60)),
                    ]);

                    // tentukan passing berdasarkan nilai_minimum
                    $quiz = Quiz::find($quizId);
                    $score = rand(50, 95);
                    $attempt->nilai = $score;
                    $attempt->is_passed = $score >= (int) $quiz->nilai_minimum;
                    $attempt->save();

                    // generate jawaban sesuai skor (proporsi benar)
                    $questions = Question::where('quiz_id', $quizId)->get();
                    $numQuestions = max(1, $questions->count());
                    $numCorrectTarget = (int) round(($score / 100) * $numQuestions);

                    $correctAssigned = 0;
                    foreach ($questions as $question) {
                        $options = AnswerOption::where('question_id', $question->id)->get();
                    $firstCorrect = $options->where('is_correct', true)->first();
                    $correctOptionId = $firstCorrect ? $firstCorrect->id : null;

                        $chooseCorrect = $correctAssigned < $numCorrectTarget;
                        $selectedOptionId = $chooseCorrect && $correctOptionId
                            ? $correctOptionId
                            : (($tmp = $options->where('is_correct', false)) && $tmp->count() > 0 ? $tmp->random(1)->first()->id : null);

                        if (!$selectedOptionId && $options->count() > 0) {
                            $selectedOptionId = $options->first()->id;
                        }

                        factory(UserAnswer::class)->create([
                            'quiz_attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                            'answer_option_id' => $selectedOptionId,
                            'created_at' => $attempt->started_at,
                        ]);

                        if ($chooseCorrect) {
                            $correctAssigned++;
                        }
                    }
                }
            }
        }
    }
}


