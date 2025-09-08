<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminQuestionController extends Controller
{
    /**
     * Membuat instance controller baru.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar pertanyaan dengan paginasi untuk kuis tertentu.
     *
     * @param int $quizId
     * @return \Illuminate\View\View
     */
    public function index($quizId)
    {
        try {
            $quiz = Quiz::with('subModule.module.course')->findOrFail($quizId);
            $questions = Question::where('quiz_id', $quizId)
                                 ->withCount('answerOptions')
                                 ->orderBy('urutan', 'asc')
                                 ->paginate(15);

            return view('admin.questions.index', compact('quiz', 'questions'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pertanyaan.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat pertanyaan baru.
     *
     * @param int $quizId
     * @return \Illuminate\View\View
     */
    public function create($quizId)
    {
        try {
            $quiz = Quiz::with('subModule.module.course')->findOrFail($quizId);
            return view('admin.questions.create', compact('quiz'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form pertanyaan.');
        }
    }

    /**
     * Menyimpan pertanyaan yang baru dibuat dengan opsi jawaban.
     *
     * @param Request $request
     * @param int $quizId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $quizId)
    {
        try {
            $validated = $request->validate([
                'pertanyaan' => 'required|string',
                'tipe' => 'required|in:multiple_choice,true_false,essay',
                'bobot' => 'required|integer|min:1',
                'urutan' => 'required|integer|min:1',
                'answer_options' => 'required_if:tipe,multiple_choice,true_false|array|min:2',
                'answer_options.*.teks_jawaban' => 'required_if:tipe,multiple_choice,true_false|string|max:500',
                'answer_options.*.is_correct' => 'required_if:tipe,multiple_choice,true_false|boolean',
                'answer_options.*.penjelasan' => 'nullable|string|max:1000'
            ]);

            $validated['quiz_id'] = $quizId;

            // Periksa apakah nomor urutan sudah ada
            $existingQuestion = Question::where('quiz_id', $quizId)
                                       ->where('urutan', $validated['urutan'])
                                       ->first();

            if ($existingQuestion) {
                // Geser pertanyaan yang ada untuk memberi ruang
                Question::where('quiz_id', $quizId)
                        ->where('urutan', '>=', $validated['urutan'])
                        ->increment('urutan');
            }

            DB::transaction(function() use ($validated) {
                // Buat pertanyaan
                $question = Question::create([
                    'quiz_id' => $validated['quiz_id'],
                    'pertanyaan' => $validated['pertanyaan'],
                    'tipe' => $validated['tipe'],
                    'bobot' => $validated['bobot'],
                    'urutan' => $validated['urutan']
                ]);

                // Buat opsi jawaban untuk pertanyaan pilihan ganda dan benar/salah
                if (in_array($validated['tipe'], ['multiple_choice', 'true_false']) && isset($validated['answer_options'])) {
                    foreach ($validated['answer_options'] as $optionData) {
                        AnswerOption::create([
                            'question_id' => $question->id,
                            'teks_jawaban' => $optionData['teks_jawaban'],
                            'is_correct' => $optionData['is_correct'],
                            'penjelasan' => $optionData['penjelasan'] ?? null
                        ]);
                    }
                }
            });

            Log::info('Admin created new question for quiz ID: ' . $quizId);
            return redirect()->route('admin.questions.index', $quizId)
                           ->with('success', 'Pertanyaan berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat pertanyaan.');
        }
    }

    /**
     * Menampilkan pertanyaan tertentu dengan opsi.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $question = Question::with([
                'quiz.subModule.module.course',
                'answerOptions'
            ])->findOrFail($id);

            return view('admin.questions.show', compact('question'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pertanyaan.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit pertanyaan tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $question = Question::with([
                'quiz.subModule.module.course',
                'answerOptions'
            ])->findOrFail($id);

            return view('admin.questions.edit', compact('question'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pertanyaan.');
        }
    }

    /**
     * Memperbarui pertanyaan tertentu dan opsi.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $question = Question::findOrFail($id);
            $oldOrder = $question->urutan;

            $validated = $request->validate([
                'pertanyaan' => 'required|string',
                'tipe' => 'required|in:multiple_choice,true_false,essay',
                'bobot' => 'required|integer|min:1',
                'urutan' => 'required|integer|min:1',
                'answer_options' => 'required_if:tipe,multiple_choice,true_false|array|min:2',
                'answer_options.*.teks_jawaban' => 'required_if:tipe,multiple_choice,true_false|string|max:500',
                'answer_options.*.is_correct' => 'required_if:tipe,multiple_choice,true_false|boolean',
                'answer_options.*.penjelasan' => 'nullable|string|max:1000'
            ]);

            // Tangani perubahan urutan
            if ($oldOrder != $validated['urutan']) {
                if ($oldOrder < $validated['urutan']) {
                    // Bergerak ke bawah - geser pertanyaan antara posisi lama dan baru
                    Question::where('quiz_id', $question->quiz_id)
                            ->where('urutan', '>', $oldOrder)
                            ->where('urutan', '<=', $validated['urutan'])
                            ->decrement('urutan');
                } else {
                    // Bergerak ke atas - geser pertanyaan antara posisi baru dan lama
                    Question::where('quiz_id', $question->quiz_id)
                            ->where('urutan', '>=', $validated['urutan'])
                            ->where('urutan', '<', $oldOrder)
                            ->increment('urutan');
                }
            }

            DB::transaction(function() use ($question, $validated) {
                // Perbarui pertanyaan
                $question->update([
                    'pertanyaan' => $validated['pertanyaan'],
                    'tipe' => $validated['tipe'],
                    'bobot' => $validated['bobot'],
                    'urutan' => $validated['urutan']
                ]);

                // Tangani opsi jawaban
                if (in_array($validated['tipe'], ['multiple_choice', 'true_false']) && isset($validated['answer_options'])) {
                    // Hapus opsi jawaban yang ada
                    $question->answerOptions()->delete();

                    // Buat opsi jawaban baru
                    foreach ($validated['answer_options'] as $optionData) {
                        AnswerOption::create([
                            'question_id' => $question->id,
                            'teks_jawaban' => $optionData['teks_jawaban'],
                            'is_correct' => $optionData['is_correct'],
                            'penjelasan' => $optionData['penjelasan'] ?? null
                        ]);
                    }
                } elseif ($validated['tipe'] === 'essay') {
                    // Hapus opsi jawaban untuk pertanyaan esai
                    $question->answerOptions()->delete();
                }
            });

            Log::info('Admin updated question ID: ' . $question->id);
            return redirect()->route('admin.questions.index', $question->quiz_id)
                           ->with('success', 'Data pertanyaan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data pertanyaan.');
        }
    }

    /**
     * Menghapus pertanyaan tertentu.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $question = Question::with('answerOptions')->findOrFail($id);
            $quizId = $question->quiz_id;
            $questionOrder = $question->urutan;

            // Hapus opsi jawaban terlebih dahulu
            $question->answerOptions()->delete();
            $question->delete();

            // Atur ulang urutan pertanyaan yang tersisa
            Question::where('quiz_id', $quizId)
                    ->where('urutan', '>', $questionOrder)
                    ->decrement('urutan');

            Log::info('Admin deleted question ID: ' . $id);
            return redirect()->route('admin.questions.index', $quizId)
                           ->with('success', 'Pertanyaan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus pertanyaan.');
        }
    }

    /**
     * Metode Ajax untuk mengatur ulang urutan pertanyaan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'question_id' => 'required|integer|exists:questions,id',
                'new_order' => 'required|integer|min:1'
            ]);

            $question = Question::findOrFail($request->question_id);
            $oldOrder = $question->urutan;
            $newOrder = $request->new_order;

            if ($oldOrder == $newOrder) {
                return response()->json(['success' => true, 'message' => 'Urutan tidak berubah']);
            }

            DB::transaction(function() use ($question, $oldOrder, $newOrder) {
                if ($oldOrder < $newOrder) {
                    // Bergerak ke bawah
                    Question::where('quiz_id', $question->quiz_id)
                            ->where('urutan', '>', $oldOrder)
                            ->where('urutan', '<=', $newOrder)
                            ->decrement('urutan');
                } else {
                    // Bergerak ke atas
                    Question::where('quiz_id', $question->quiz_id)
                            ->where('urutan', '>=', $newOrder)
                            ->where('urutan', '<', $oldOrder)
                            ->increment('urutan');
                }

                $question->update(['urutan' => $newOrder]);
            });

            Log::info('Admin reordered question ID: ' . $question->id . ' from ' . $oldOrder . ' to ' . $newOrder);
            return response()->json(['success' => true, 'message' => 'Urutan pertanyaan berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@reorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }

    /**
     * Pengaturan ulang massal pertanyaan menggunakan drag and drop.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkReorder(Request $request)
    {
        try {
            $request->validate([
                'questions' => 'required|array',
                'questions.*.id' => 'required|integer|exists:questions,id',
                'questions.*.urutan' => 'required|integer|min:1'
            ]);

            DB::transaction(function() use ($request) {
                foreach ($request->questions as $item) {
                    Question::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
                }
            });

            Log::info('Admin bulk reordered questions for quiz ID: ' . $request->quiz_id);
            return response()->json(['success' => true, 'message' => 'Urutan pertanyaan berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@bulkReorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }

    /**
     * Menduplikasi pertanyaan dengan opsi jawaban.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate($id)
    {
        try {
            $originalQuestion = Question::with('answerOptions')->findOrFail($id);
            $quiz = $originalQuestion->quiz;

            // Dapatkan nomor urutan berikutnya yang tersedia
            $nextOrder = Question::where('quiz_id', $quiz->id)->max('urutan') + 1;

            DB::transaction(function() use ($originalQuestion, $quiz, $nextOrder) {
                // Buat pertanyaan baru
                $newQuestion = $originalQuestion->replicate();
                $newQuestion->quiz_id = $quiz->id;
                $newQuestion->urutan = $nextOrder;
                $newQuestion->pertanyaan = $originalQuestion->pertanyaan . ' (Copy)';
                $newQuestion->save();

                // Duplikasi opsi jawaban
                foreach ($originalQuestion->answerOptions as $answerOption) {
                    $newAnswerOption = $answerOption->replicate();
                    $newAnswerOption->question_id = $newQuestion->id;
                    $newAnswerOption->save();
                }
            });

            Log::info('Admin duplicated question ID: ' . $originalQuestion->id);
            return redirect()->route('admin.questions.index', $quiz->id)
                           ->with('success', 'Pertanyaan berhasil diduplikasi.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@duplicate: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menduplikasi pertanyaan.');
        }
    }

    /**
     * Dapatkan pertanyaan untuk permintaan AJAX (misalnya, untuk dropdown).
     *
     * @param int $quizId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestions($quizId)
    {
        try {
            $questions = Question::where('quiz_id', $quizId)
                                 ->orderBy('urutan', 'asc')
                                 ->get(['id', 'pertanyaan', 'tipe', 'urutan']);

            return response()->json($questions);
        } catch (\Exception $e) {
            Log::error('Error in AdminQuestionController@getQuestions: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data pertanyaan'], 500);
        }
    }

    /**
     * Validasi data pertanyaan sebelum menyimpan.
     *
     * @param array $data
     * @return bool
     */
    private function validateQuestionData($data)
    {
        // Untuk pertanyaan pilihan ganda dan benar/salah, pastikan setidaknya ada satu jawaban yang benar
        if (in_array($data['tipe'], ['multiple_choice', 'true_false'])) {
            if (!isset($data['answer_options']) || count($data['answer_options']) < 2) {
                return false;
            }

            $hasCorrectAnswer = false;
            foreach ($data['answer_options'] as $option) {
                if (isset($option['is_correct']) && $option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                return false;
            }
        }

        return true;
    }
}
