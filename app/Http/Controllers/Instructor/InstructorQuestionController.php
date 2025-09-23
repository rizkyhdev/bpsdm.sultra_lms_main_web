<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreQuestionRequest;
use App\Http\Requests\Instructor\UpdateQuestionRequest;
use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Kelas InstructorQuestionController
 * Mengelola pertanyaan dan opsi jawaban pada kuis milik instruktur.
 */
class InstructorQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'instructor']);
    }

    /**
     * Daftar pertanyaan untuk sebuah kuis.
     * @param Request $request
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $quizId)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($quizId);
        $this->authorize('view', $quiz);

        $perPage = (int) $request->get('per_page', 15);
        $questions = Question::where('quiz_id', $quiz->id)
            ->orderBy('urutan')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.questions.index', compact('quiz', 'questions'));
    }

    /**
     * Tampilkan form pembuatan.
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function create($quizId)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($quizId);
        $this->authorize('update', $quiz);
        return view('instructor.questions.create', compact('quiz'));
    }

    /**
     * Simpan pertanyaan dan opsi secara atomik.
     * @param StoreQuestionRequest $request
     * @param int $quizId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreQuestionRequest $request, $quizId)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($quizId);
        $this->authorize('update', $quiz);

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $question = new Question();
            $question->quiz_id = $quiz->id;
            $question->pertanyaan = $data['pertanyaan'];
            $question->tipe = $data['tipe'];
            $question->bobot = $data['bobot'];
            $question->urutan = $data['urutan'];
            $question->save();

            if (in_array($question->tipe, ['multiple_choice', 'true_false']) && !empty($data['answer_options'])) {
                $correctCount = 0;
                foreach ($data['answer_options'] as $opt) {
                    $option = new AnswerOption();
                    $option->question_id = $question->id;
                    $option->teks_jawaban = $opt['teks_jawaban'];
                    $option->is_correct = !empty($opt['is_correct']) ? 1 : 0;
                    if ($option->is_correct) { $correctCount++; }
                    $option->save();
                }
                if ($question->tipe === 'multiple_choice' && $correctCount !== 1) {
                    throw new \RuntimeException('Multiple choice must have exactly one correct option.');
                }
                if ($question->tipe === 'true_false' && $correctCount !== 1) {
                    throw new \RuntimeException('True/False must have exactly one correct option.');
                }
            }

            DB::commit();
            Log::info('Question created', ['question_id' => $question->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.questions.index', $quiz->id)->with('success', 'Question created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create question', ['quiz_id' => $quiz->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan pertanyaan.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::with(['quiz.subModule.module.course', 'answerOptions'])->findOrFail($id);
        $this->authorize('view', $question);
        return view('instructor.questions.show', compact('question'));
    }

    /**
     * Tampilkan form edit.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = Question::with(['quiz.subModule.module.course', 'answerOptions'])->findOrFail($id);
        $this->authorize('update', $question);
        return view('instructor.questions.edit', compact('question'));
    }

    /**
     * Perbarui pertanyaan dan sinkronisasi opsi.
     * @param UpdateQuestionRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateQuestionRequest $request, $id)
    {
        $question = Question::with(['quiz.subModule.module.course', 'answerOptions'])->findOrFail($id);
        $this->authorize('update', $question);

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $question->pertanyaan = $data['pertanyaan'];
            $question->tipe = $data['tipe'];
            $question->bobot = $data['bobot'];
            $question->urutan = $data['urutan'];
            $question->save();

            if (in_array($question->tipe, ['multiple_choice', 'true_false'])) {
                $question->answerOptions()->delete();
                $correctCount = 0;
                foreach ($data['answer_options'] as $opt) {
                    $option = new AnswerOption();
                    $option->question_id = $question->id;
                    $option->teks_jawaban = $opt['teks_jawaban'];
                    $option->is_correct = !empty($opt['is_correct']) ? 1 : 0;
                    if ($option->is_correct) { $correctCount++; }
                    $option->save();
                }
                if ($question->tipe === 'multiple_choice' && $correctCount !== 1) {
                    throw new \RuntimeException('Multiple choice must have exactly one correct option.');
                }
                if ($question->tipe === 'true_false' && $correctCount !== 1) {
                    throw new \RuntimeException('True/False must have exactly one correct option.');
                }
            } else {
                // Essay: ensure no options remain
                $question->answerOptions()->delete();
            }

            DB::commit();
            Log::info('Question updated', ['question_id' => $question->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.questions.show', $question->id)->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update question', ['question_id' => $question->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    /**
     * Hapus pertanyaan.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $question = Question::with(['quiz.subModule.module.course'])->findOrFail($id);
        $this->authorize('delete', $question);

        DB::beginTransaction();
        try {
            $question->answerOptions()->delete();
            $question->delete();
            DB::commit();
            Log::info('Question deleted', ['question_id' => $question->id, 'instructor_id' => Auth::id()]);
            return redirect()->back()->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete question', ['question_id' => $question->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete question.');
        }
    }

    /**
     * Mengubah urutan pertanyaan.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:questions,id',
            'items.*.urutan' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $question = Question::with('quiz.subModule.module.course')->find($item['id']);
                $this->authorize('update', $question);
                $question->urutan = $item['urutan'];
                $question->save();
            }
            DB::commit();
            return response()->json(['message' => 'Order updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }
}


