<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreCourseRequest;
use App\Http\Requests\Instructor\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Kelas InstructorCourseController
 * Mengelola kursus yang dimiliki oleh instruktur.
 */
class InstructorCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Daftar kursus milik instruktur dengan pencarian dan paginasi.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);

        $q = trim($request->get('q'));
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'desc');
        $perPage = (int) $request->get('per_page', 15);

        $query = Course::query()
            ->where('user_id', Auth::id())
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('judul', 'like', "%$q%")
                        ->orWhere('deskripsi', 'like', "%$q%")
                        ->orWhere('bidang_kompetensi', 'like', "%$q%");
                });
            })
            ->withCount(['modules', 'userEnrollments'])
            ->orderBy($sort, $dir);

        $courses = $query->paginate($perPage)->appends($request->query());

        return view('instructor.courses.index', compact('courses'));
    }

    /**
     * Tampilkan form pembuatan.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Course::class);
        return view('instructor.courses.create');
    }

    /**
     * Tampilkan wizard pembuatan course lengkap dengan modules, sub-modules, dan contents.
     * @return \Illuminate\Http\Response
     */
    public function createWizard()
    {
        $this->authorize('create', Course::class);
        return view('instructor.courses.create-wizard');
    }

    /**
     * Simpan course lengkap dengan modules, sub-modules, dan contents melalui wizard.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWizard(Request $request)
    {
        $this->authorize('create', Course::class);

        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'jp_value' => 'required|integer|min:1',
                'bidang_kompetensi' => 'required|string',
                'modules' => 'required|array|min:1',
                'modules.*.judul' => 'required|string|max:255',
                'modules.*.deskripsi' => 'nullable|string',
                'modules.*.urutan' => 'required|integer|min:1',
                'modules.*.sub_modules' => 'nullable|array',
                'modules.*.sub_modules.*.judul' => 'required_with:modules.*.sub_modules|string|max:255',
                'modules.*.sub_modules.*.deskripsi' => 'nullable|string',
                'modules.*.sub_modules.*.urutan' => 'required_with:modules.*.sub_modules|integer|min:1',
                'modules.*.sub_modules.*.contents' => 'nullable|array',
                'modules.*.sub_modules.*.contents.*.judul' => 'required_with:modules.*.sub_modules.*.contents|string|max:255',
                'modules.*.sub_modules.*.contents.*.tipe' => 'required_with:modules.*.sub_modules.*.contents|in:text,html,pdf,video,audio,image,link,youtube',
                'modules.*.sub_modules.*.contents.*.urutan' => 'required_with:modules.*.sub_modules.*.contents|integer|min:1',
                'modules.*.sub_modules.*.contents.*.html_content' => 'nullable|string|required_if:modules.*.sub_modules.*.contents.*.tipe,html,text',
                'modules.*.sub_modules.*.contents.*.external_url' => 'nullable|url|required_if:modules.*.sub_modules.*.contents.*.tipe,link',
                'modules.*.sub_modules.*.contents.*.youtube_url' => 'nullable|url|required_if:modules.*.sub_modules.*.contents.*.tipe,youtube',
                'modules.*.sub_modules.*.contents.*.file_path' => 'nullable|file|max:102400',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in wizard', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['_token'])
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed. Please check the form for errors.');
        }

        DB::beginTransaction();
        try {
            // Log received data for debugging
            Log::info('Wizard submission received', [
                'modules_count' => count($request->modules ?? []),
                'has_modules' => $request->has('modules'),
                'modules_data' => $request->modules,
                'all_request_keys' => array_keys($request->all())
            ]);

            // Create course
            $course = new Course();
            $course->judul = $request->judul;
            $course->deskripsi = $request->deskripsi;
            $course->jp_value = $request->jp_value;
            $course->bidang_kompetensi = $request->bidang_kompetensi;
            $course->user_id = Auth::id();
            $course->save();

            Log::info('Course created', ['course_id' => $course->id]);

            // Create modules, sub-modules, and contents
            foreach ($request->modules as $moduleIndex => $moduleData) {
                $subModuleIndex = 0;
                $module = new \App\Models\Module();
                $module->course_id = $course->id;
                $module->judul = $moduleData['judul'] ?? '';
                $module->deskripsi = $moduleData['deskripsi'] ?? '';
                $module->urutan = $moduleData['urutan'] ?? ($moduleIndex + 1);
                $module->save();

                Log::info('Module created', ['module_id' => $module->id, 'module_index' => $moduleIndex]);

                if (isset($moduleData['sub_modules']) && is_array($moduleData['sub_modules']) && count($moduleData['sub_modules']) > 0) {
                    foreach ($moduleData['sub_modules'] as $subModuleIndex => $subModuleData) {
                        $subModule = new \App\Models\SubModule();
                        $subModule->module_id = $module->id;
                        $subModule->judul = $subModuleData['judul'] ?? '';
                        $subModule->deskripsi = $subModuleData['deskripsi'] ?? '';
                        $subModule->urutan = $subModuleData['urutan'] ?? ($subModuleIndex + 1);
                        $subModule->save();

                        Log::info('SubModule created', ['sub_module_id' => $subModule->id, 'module_id' => $module->id]);

                        if (isset($subModuleData['contents']) && is_array($subModuleData['contents']) && count($subModuleData['contents']) > 0) {
                            foreach ($subModuleData['contents'] as $contentIndex => $contentData) {
                                $content = new \App\Models\Content();
                                $content->sub_module_id = $subModule->id;
                                $content->judul = $contentData['judul'] ?? '';
                                $content->tipe = $contentData['tipe'] ?? 'text';
                                $content->urutan = $contentData['urutan'] ?? ($contentIndex + 1);
                                $content->html_content = $contentData['html_content'] ?? null;
                                $content->external_url = $contentData['external_url'] ?? null;
                                $content->youtube_url = $contentData['youtube_url'] ?? null;

                                // Handle file upload - access from request files array
                                $fileKey = "modules.{$moduleIndex}.sub_modules.{$subModuleIndex}.contents.{$contentIndex}.file_path";
                                if ($request->hasFile($fileKey)) {
                                    $file = $request->file($fileKey);
                                    if ($file && $file->isValid()) {
                                        $path = $file->store('contents/' . date('Y/m/d'), 'public');
                                        $content->file_path = $path;
                                    }
                                }

                                $content->save();
                                Log::info('Content created', ['content_id' => $content->id, 'sub_module_id' => $subModule->id]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            Log::info('Course created via wizard', ['course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.show', $course->id)
                ->with('success', 'Course created successfully with all modules, sub-modules, and contents.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create course via wizard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create course: ' . $e->getMessage() . ' Please check the logs for more details.');
        }
    }

    /**
     * Simpan kursus baru yang dimiliki instruktur saat ini.
     * @param StoreCourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCourseRequest $request)
    {
        $this->authorize('create', Course::class);

        try {
            $course = new Course($request->validated());
            $course->user_id = Auth::id();
            $course->save();

            Log::info('Course created by instructor', ['course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.show', $course->id)
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create course', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create course.');
        }
    }

    /**
     * Tampilkan detail kursus tertentu.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::with(['modules.subModules.contents', 'modules.subModules.quizzes'])
            ->withCount(['userEnrollments as enrollments_count'])
            ->findOrFail($id);

        $this->authorize('view', $course);

        return view('instructor.courses.show', compact('course'));
    }

    /**
     * Tampilkan form untuk mengedit kursus.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('update', $course);
        return view('instructor.courses.edit', compact('course'));
    }

    /**
     * Perbarui kursus.
     * @param UpdateCourseRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('update', $course);

        try {
            $course->update($request->validated());
            Log::info('Course updated by instructor', ['course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.show', $course->id)
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update course', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update course.');
        }
    }

    /**
     * Hapus kursus dan semua struktur turunannya.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $course = Course::with(['modules.subModules.contents', 'modules.subModules.quizzes.questions'])
            ->findOrFail($id);
        $this->authorize('delete', $course);

        DB::beginTransaction();
        try {
            foreach ($course->modules as $module) {
                foreach ($module->subModules as $subModule) {
                    // Delete contents
                    foreach ($subModule->contents as $content) {
                        $content->delete();
                    }
                    // Delete quizzes and questions
                    foreach ($subModule->quizzes as $quiz) {
                        foreach ($quiz->questions as $question) {
                            $question->answerOptions()->delete();
                            $question->delete();
                        }
                        $quiz->delete();
                    }
                    $subModule->delete();
                }
                $module->delete();
            }

            $course->delete();
            DB::commit();
            Log::info('Course deleted by instructor', ['course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.index')->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete course', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete course.');
        }
    }

    /**
     * Duplikasi kursus beserta hierarkinya di bawah kepemilikan instruktur.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate($id)
    {
        $course = Course::with(['modules.subModules.contents', 'modules.subModules.quizzes.questions.answerOptions'])
            ->findOrFail($id);
        $this->authorize('view', $course);

        DB::beginTransaction();
        try {
            $newCourse = $course->replicate(['user_id']);
            $newCourse->judul = $course->judul . ' (Copy)';
            $newCourse->user_id = Auth::id();
            $newCourse->push();

            foreach ($course->modules as $module) {
                $newModule = $module->replicate(['course_id']);
                $newModule->course_id = $newCourse->id;
                $newModule->push();

                foreach ($module->subModules as $subModule) {
                    $newSub = $subModule->replicate(['module_id']);
                    $newSub->module_id = $newModule->id;
                    $newSub->push();

                    foreach ($subModule->contents as $content) {
                        $newContent = $content->replicate(['sub_module_id']);
                        $newContent->sub_module_id = $newSub->id;
                        $newContent->push();
                    }

                    foreach ($subModule->quizzes as $quiz) {
                        $newQuiz = $quiz->replicate(['sub_module_id']);
                        $newQuiz->sub_module_id = $newSub->id;
                        $newQuiz->push();

                        foreach ($quiz->questions as $question) {
                            $newQuestion = $question->replicate(['quiz_id']);
                            $newQuestion->quiz_id = $newQuiz->id;
                            $newQuestion->push();

                            foreach ($question->answerOptions as $opt) {
                                $newOpt = $opt->replicate(['question_id']);
                                $newOpt->question_id = $newQuestion->id;
                                $newOpt->push();
                            }
                        }
                    }
                }
            }

            DB::commit();
            Log::info('Course duplicated by instructor', ['source_course_id' => $course->id, 'new_course_id' => $newCourse->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.show', $newCourse->id)
                ->with('success', 'Course duplicated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate course', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to duplicate course.');
        }
    }
}


