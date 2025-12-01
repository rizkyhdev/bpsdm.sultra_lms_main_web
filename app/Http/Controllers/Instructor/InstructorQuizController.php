<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreQuizRequest;
use App\Http\Requests\Instructor\UpdateQuizRequest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SubModule;
use App\Models\Module;
use App\Models\Course;
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
     * Daftar semua kuis milik instruktur.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function indexAll(Request $request)
    {
        $user = Auth::user();
        
        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);

        // Get all quizzes from instructor's courses
        // Quizzes can be at course, module, or sub-module level
        $quizzes = Quiz::query()
            ->where(function ($query) use ($user) {
                // Quizzes directly associated with course
                $query->whereHas('course', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Quizzes associated with module (through module's course)
                ->orWhereHas('module.course', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Quizzes associated with sub-module (through sub-module's module's course)
                ->orWhereHas('subModule.module.course', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->with(['course', 'module.course', 'subModule.module.course'])
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.quizzes.index-all', compact('quizzes'));
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

        $quizzes = Quiz::withCount('questions')
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Quiz created successfully.', 'quiz' => $quiz]);
            }
            
            return redirect()->route('instructor.quizzes.index', $sub->id)->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create quiz', ['sub_module_id' => $sub->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create quiz.', 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            
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
     * Get quiz data as JSON (for modal editing)
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($id)
    {
        $quiz = Quiz::findOrFail($id);
        $this->authorize('view', $quiz);
        return response()->json($quiz);
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateQuizRequest $request, $id)
    {
        $quiz = Quiz::with('subModule.module.course')->findOrFail($id);
        $this->authorize('update', $quiz);

        try {
            $quiz->update($request->validated());
            Log::info('Quiz updated', ['quiz_id' => $quiz->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Quiz updated successfully.', 'quiz' => $quiz]);
            }
            
            return redirect()->route('instructor.quizzes.show', $quiz->id)->with('success', 'Quiz updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update quiz', ['quiz_id' => $quiz->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update quiz.', 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            
            return redirect()->back()->withInput()->with('error', 'Failed to update quiz.');
        }
    }

    /**
     * Hapus kuis.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
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
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Quiz deleted successfully.']);
            }
            
            return redirect()->back()->with('success', 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete quiz', ['quiz_id' => $quiz->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete quiz.'], 422);
            }
            
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

        // Get all attempts with user relationship
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with('user')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->paginate(15);

        // Calculate statistics
        $allAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->get();
        
        $totalAttempts = $allAttempts->count();
        $passedAttempts = $allAttempts->where('is_passed', true)->count();
        $failedAttempts = $allAttempts->where('is_passed', false)->count();
        $avgScore = $allAttempts->avg('nilai') ?? 0;
        $highestScore = $allAttempts->max('nilai') ?? 0;
        $lowestScore = $allAttempts->min('nilai') ?? 0;
        $passRate = $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 2) : 0.0;
        
        // Get unique users count
        $uniqueUsers = $allAttempts->unique('user_id')->count();
        
        // Calculate standard deviation
        $scores = $allAttempts->pluck('nilai')->toArray();
        $stdDev = 0;
        if (count($scores) > 1) {
            $variance = 0;
            foreach ($scores as $score) {
                $variance += pow($score - $avgScore, 2);
            }
            $stdDev = sqrt($variance / count($scores));
        }

        return view('instructor.quizzes.results', compact(
            'quiz', 
            'attempts', 
            'totalAttempts',
            'passedAttempts',
            'failedAttempts',
            'avgScore', 
            'highestScore',
            'lowestScore',
            'passRate',
            'uniqueUsers',
            'stdDev'
        ));
    }

    /**
     * Daftar kuis di bawah course.
     * @param Request $request
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function indexCourse(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('view', $course);

        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);

        $quizzes = Quiz::query()
            ->where('course_id', $course->id)
            ->whereNull('module_id')
            ->whereNull('sub_module_id')
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.quizzes.index-course', compact('course', 'quizzes'));
    }

    /**
     * Tampilkan form pembuatan quiz untuk course.
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function createCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);
        return view('instructor.quizzes.create-course', compact('course'));
    }

    /**
     * Simpan quiz untuk course.
     * @param StoreQuizRequest $request
     * @param int $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCourse(StoreQuizRequest $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);

        try {
            $quiz = new Quiz($request->validated());
            $quiz->course_id = $course->id;
            $quiz->module_id = null;
            $quiz->sub_module_id = null;
            $quiz->save();
            Log::info('Course quiz created', ['quiz_id' => $quiz->id, 'course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.quizzes.index-course', $course->id)->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create course quiz', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create quiz.');
        }
    }

    /**
     * Daftar kuis di bawah module.
     * @param Request $request
     * @param int $moduleId
     * @return \Illuminate\Http\Response
     */
    public function indexModule(Request $request, $moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        $this->authorize('view', $module);

        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);

        $quizzes = Quiz::query()
            ->where('module_id', $module->id)
            ->whereNull('sub_module_id')
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.quizzes.index-module', compact('module', 'quizzes'));
    }

    /**
     * Tampilkan form pembuatan quiz untuk module.
     * @param int $moduleId
     * @return \Illuminate\Http\Response
     */
    public function createModule($moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        $this->authorize('update', $module);
        return view('instructor.quizzes.create-module', compact('module'));
    }

    /**
     * Simpan quiz untuk module.
     * @param StoreQuizRequest $request
     * @param int $moduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeModule(StoreQuizRequest $request, $moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        $this->authorize('update', $module);

        try {
            $quiz = new Quiz($request->validated());
            $quiz->course_id = $module->course_id;
            $quiz->module_id = $module->id;
            $quiz->sub_module_id = null;
            $quiz->save();
            Log::info('Module quiz created', ['quiz_id' => $quiz->id, 'module_id' => $module->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.quizzes.index-module', $module->id)->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create module quiz', ['module_id' => $module->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create quiz.');
        }
    }
}


