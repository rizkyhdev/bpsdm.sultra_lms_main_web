<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreQuizRequest;
use App\Http\Requests\Instructor\UpdateQuizRequest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Kelas InstructorQuizController
 * Mengelola kuis di bawah sub-modul milik instruktur.
 */
class InstructorQuizController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Daftar kuis di bawah sub-modul.
     * @param Request $request
     * @param int $subModuleId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $subModuleId)
    {
        $sub = SubModule::with('module.course')->findOrFail($subModuleId);
        $this->authorize('view', $sub);

        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);

        $quizzes = Quiz::query()
            ->where('sub_module_id', $sub->id)
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.quizzes.index', compact('sub', 'quizzes'));
    }

    /**
     * Tampilkan form pembuatan.
     * @param int $subModuleId
     * @return \Illuminate\Http\Response
     */
    public function create($subModuleId)
    {
        $sub = SubModule::with('module.course')->findOrFail($subModuleId);
        $this->authorize('update', $sub);
        return view('instructor.quizzes.create', compact('sub'));
    }

    /**
     * Simpan kuis.
     * @param StoreQuizRequest $request
     * @param int $subModuleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreQuizRequest $request, $subModuleId)
    {
        $sub = SubModule::with('module.course')->findOrFail($subModuleId);
        $this->authorize('update', $sub);

        try {
            $quiz = new Quiz($request->validated());
            $quiz->sub_module_id = $sub->id;
            $quiz->save();
            Log::info('Quiz created', ['quiz_id' => $quiz->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.quizzes.index', $sub->id)->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create quiz', ['sub_module_id' => $sub->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create quiz.');
        }
    }

    /**
     * Tampilkan kuis beserta statistik dasar.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quiz = Quiz::with(['subModule.module.course', 'questions'])->findOrFail($id);
        $this->authorize('view', $quiz);

        $attemptsCount = QuizAttempt::where('quiz_id', $quiz->id)->count();
        $avgScore = QuizAttempt::where('quiz_id', $quiz->id)->avg('nilai');

        return view('instructor.quizzes.show', compact('quiz', 'attemptsCount', 'avgScore'));
    }

    /**
     * Tampilkan form edit kuis.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($id);
        $this->authorize('update', $quiz);
        return view('instructor.quizzes.edit', compact('quiz'));
    }

    /**
     * Perbarui kuis.
     * @param UpdateQuizRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateQuizRequest $request, $id)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($id);
        $this->authorize('update', $quiz);

        try {
            $quiz->update($request->validated());
            Log::info('Quiz updated', ['quiz_id' => $quiz->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.quizzes.show', $quiz->id)->with('success', 'Quiz updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update quiz', ['quiz_id' => $quiz->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update quiz.');
        }
    }

    /**
     * Hapus kuis.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $quiz = Quiz::with(['subModule.module.course', 'questions'])->findOrFail($id);
        $this->authorize('delete', $quiz);

        DB::beginTransaction();
        try {
            foreach ($quiz->questions as $q) {
                $q->answerOptions()->delete();
                $q->delete();
            }
            $quiz->delete();
            DB::commit();
            Log::info('Quiz deleted', ['quiz_id' => $quiz->id, 'instructor_id' => Auth::id()]);
            return redirect()->back()->with('success', 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete quiz', ['quiz_id' => $quiz->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete quiz.');
        }
    }

    /**
     * Daftar hasil dengan statistik agregat.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function results($id)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($id);
        $this->authorize('view', $quiz);

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->latest('completed_at')
            ->paginate(15);
        $avgScore = QuizAttempt::where('quiz_id', $quiz->id)->avg('nilai');
        $passRate = QuizAttempt::where('quiz_id', $quiz->id)->where('is_passed', 1)->count();
        $totalAttempts = QuizAttempt::where('quiz_id', $quiz->id)->count();
        $passRate = $totalAttempts > 0 ? round(($passRate / $totalAttempts) * 100, 2) : 0.0;

        return view('instructor.quizzes.results', compact('quiz', 'attempts', 'avgScore', 'passRate'));
    }
}


