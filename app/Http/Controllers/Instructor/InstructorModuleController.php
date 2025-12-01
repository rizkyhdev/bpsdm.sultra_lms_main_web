<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreModuleRequest;
use App\Http\Requests\Instructor\UpdateModuleRequest;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Kelas InstructorModuleController
 * Mengelola modul di bawah kursus milik instruktur.
 */
class InstructorModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Daftar modul dalam kursus milik instruktur.
     * @param Request $request
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('view', $course);

        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);

        $modules = Module::withCount('subModules')
            ->where('course_id', $course->id)
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->orderBy('urutan')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.modules.index', compact('course', 'modules'));
    }

    /**
     * Tampilkan form pembuatan.
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);
        return view('instructor.modules.create', compact('course'));
    }

    /**
     * Simpan modul di bawah kursus.
     * @param StoreModuleRequest $request
     * @param int $courseId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(StoreModuleRequest $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);

        try {
            $module = new Module($request->validated());
            $module->course_id = $course->id;
            $module->save();
            Log::info('Module created', ['module_id' => $module->id, 'course_id' => $course->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Module created successfully.', 'module' => $module]);
            }
            
            return redirect()->route('instructor.modules.index', $course->id)->with('success', 'Module created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create module', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create module.', 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            
            return redirect()->back()->withInput()->with('error', 'Failed to create module.');
        }
    }

    /**
     * Tampilkan modul.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $module = Module::with(['subModules' => function ($query) {
            $query->orderBy('urutan');
        }])->findOrFail($id);
        $this->authorize('view', $module);
        $subModules = $module->subModules;
        return view('instructor.modules.show', compact('module', 'subModules'));
    }

    /**
     * Get module data as JSON (for modal editing)
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('view', $module);
        return response()->json($module);
    }

    /**
     * Tampilkan form edit.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = Module::with(['course', 'subModules.contents'])->findOrFail($id);
        $this->authorize('update', $module);
        return view('instructor.modules.edit', compact('module'));
    }

    /**
     * Perbarui modul.
     * @param UpdateModuleRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateModuleRequest $request, $id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('update', $module);

        try {
            $module->update($request->validated());
            Log::info('Module updated', ['module_id' => $module->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Module updated successfully.', 'module' => $module]);
            }
            
            return redirect()->route('instructor.modules.show', $module->id)->with('success', 'Module updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update module', ['module_id' => $module->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update module.', 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            
            return redirect()->back()->withInput()->with('error', 'Failed to update module.');
        }
    }

    /**
     * Hapus modul.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $module = Module::with(['subModules.contents', 'subModules.quizzes.questions'])->findOrFail($id);
        $this->authorize('delete', $module);

        DB::beginTransaction();
        try {
            foreach ($module->subModules as $sub) {
                foreach ($sub->contents as $content) {
                    $content->delete();
                }
                foreach ($sub->quizzes as $quiz) {
                    foreach ($quiz->questions as $question) {
                        $question->answerOptions()->delete();
                        $question->delete();
                    }
                    $quiz->delete();
                }
                $sub->delete();
            }
            $module->delete();
            DB::commit();
            Log::info('Module deleted', ['module_id' => $module->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Module deleted successfully.']);
            }
            
            return redirect()->back()->with('success', 'Module deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete module', ['module_id' => $module->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete module.'], 422);
            }
            
            return redirect()->back()->with('error', 'Failed to delete module.');
        }
    }

    /**
     * Mengubah urutan modul berdasarkan kolom urutan.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:modules,id',
            'items.*.urutan' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $module = Module::find($item['id']);
                $this->authorize('update', $module);
                $module->urutan = $item['urutan'];
                $module->save();
            }
            DB::commit();
            return response()->json(['message' => 'Order updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }
}


