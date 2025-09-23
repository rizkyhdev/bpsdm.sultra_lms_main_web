<?php

use Illuminate\Database\Seeder;
use App\Models\SubModule;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\AnswerOption;

class QuizzesSeeder extends Seeder
{
    /**
     * Untuk 60% submodul, buat kuis dengan 5–10 pertanyaan; tiap pertanyaan 4 opsi jawaban dan 1 benar.
     */
    public function run()
    {
        foreach (SubModule::all() as $sub) {
            if (mt_rand(1, 100) <= 60) {
                $quiz = factory(Quiz::class)->create([
                    'sub_module_id' => $sub->id,
                ]);

                $numQuestions = rand(5, 10);
                for ($q = 0; $q < $numQuestions; $q++) {
                    $question = factory(Question::class)->create([
                        'quiz_id' => $quiz->id,
                    ]);

                    // buat 4 opsi, set 1 benar
                    $correctIndex = rand(0, 3);
                    for ($i = 0; $i < 4; $i++) {
                        factory(AnswerOption::class)->create([
                            'question_id' => $question->id,
                            'is_correct' => $i === $correctIndex,
                        ]);
                    }
                }
            }
        }
    }
}


