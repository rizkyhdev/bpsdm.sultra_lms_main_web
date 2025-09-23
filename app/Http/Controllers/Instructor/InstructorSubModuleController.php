<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreSubModuleRequest;
use App\Http\Requests\Instructor\UpdateSubModuleRequest;
use App\Models\Module;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Kelas InstructorSubModuleController
 * Mengelola sub-modul di bawah modul milik instruktur.
 */
class InstructorSubModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'instructor']);
    }

    /**
     * Daftar sub-modul di bawah sebuah modul.
     * @param Request $request
     * @param int $moduleId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        $this->authorize('view', $module);

        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);

        $subs = SubModule::query()
            ->where('module_id', $module->id)
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->orderBy('urutan')
            ->paginate($perPage)
            ->appends($request->query());

        return view('instructor.submodules.index', compact('module', 'subs'));
    }

    /**
     * Tampilkan form pembuatan.
     * @param int $moduleId
     * @return \Illuminate\Http\Response
     */
    public function create($moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        $this->authorize('update', $module);
        return view('instructor.submodules.create', compact('module'));
    }

    /**
     * Simpan sub-modul di bawah modul.
     * @param StoreSubModuleRequest $request
     * @param int $moduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSubModuleRequest $request, $moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        $this->authorize('update', $module);

        try {
            $sub = new SubModule($request->validated());
            $sub->module_id = $module->id;
            $sub->save();
            Log::info('SubModule created', ['sub_module_id' => $sub->id, 'module_id' => $module->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.submodules.index', $module->id)->with('success', 'Sub-module created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create sub-module', ['module_id' => $module->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create sub-module.');
        }
    }

    /**
     * Tampilkan sub-modul.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sub = SubModule::with('module.course')->findOrFail($id);
        $this->authorize('view', $sub);
        return view('instructor.submodules.show', compact('sub'));
    }

    /**
     * Tampilkan form edit.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sub = SubModule::with('module.course')->findOrFail($id);
        $this->authorize('update', $sub);
        return view('instructor.submodules.edit', compact('sub'));
    }

    /**
     * Perbarui sub-modul.
     * @param UpdateSubModuleRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSubModuleRequest $request, $id)
    {
        $sub = SubModule::with('module.course')->findOrFail($id);
        $this->authorize('update', $sub);

        try {
            $sub->update($request->validated());
            Log::info('SubModule updated', ['sub_module_id' => $sub->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.submodules.show', $sub->id)->with('success', 'Sub-module updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update sub-module', ['sub_module_id' => $sub->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update sub-module.');
        }
    }

    /**
     * Hapus sub-modul.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $sub = SubModule::with(['module.course', 'contents', 'quizzes.questions'])->findOrFail($id);
        $this->authorize('delete', $sub);

        DB::beginTransaction();
        try {
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
            DB::commit();
            Log::info('SubModule deleted', ['sub_module_id' => $sub->id, 'instructor_id' => Auth::id()]);
            return redirect()->back()->with('success', 'Sub-module deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete sub-module', ['sub_module_id' => $sub->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete sub-module.');
        }
    }

    /**
     * Mengubah urutan sub-modul.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:sub_modules,id',
            'items.*.urutan' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $sub = SubModule::with('module.course')->find($item['id']);
                $this->authorize('update', $sub);
                $sub->urutan = $item['urutan'];
                $sub->save();
            }
            DB::commit();
            return response()->json(['message' => 'Order updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }
}


