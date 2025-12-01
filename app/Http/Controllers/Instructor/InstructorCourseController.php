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

        // Clean and filter empty nested arrays before validation
        // This preserves original indices to maintain file upload key alignment
        $modules = $request->input('modules', []);
        $cleanedModules = [];
        
        foreach ($modules as $moduleIndex => $moduleData) {
            // Skip empty modules (missing required fields: judul or urutan)
            if (empty($moduleData['judul']) || empty($moduleData['urutan'])) {
                continue;
            }
            
            $cleanedModule = $moduleData;
            
            // Clean sub_modules - preserve original indices
            if (isset($moduleData['sub_modules']) && is_array($moduleData['sub_modules'])) {
                $cleanedSubModules = [];
                foreach ($moduleData['sub_modules'] as $subModuleIndex => $subModuleData) {
                    // Skip empty sub_modules (missing required fields: judul or urutan)
                    if (empty($subModuleData['judul']) || empty($subModuleData['urutan'])) {
                        continue;
                    }
                    
                    $cleanedSubModule = $subModuleData;
                    
                    // Clean contents - preserve original indices
                    if (isset($subModuleData['contents']) && is_array($subModuleData['contents'])) {
                        $cleanedContents = [];
                        foreach ($subModuleData['contents'] as $contentIndex => $contentData) {
                            // Skip empty contents (missing required fields: judul, tipe, or urutan)
                            if (empty($contentData['judul']) || empty($contentData['tipe']) || empty($contentData['urutan'])) {
                                continue;
                            }
                            // Preserve original index for file upload keys
                            $cleanedContents[$contentIndex] = $contentData;
                        }
                        $cleanedSubModule['contents'] = $cleanedContents;
                    }
                    
                    // Clean quizzes - preserve original indices
                    if (isset($subModuleData['quizzes']) && is_array($subModuleData['quizzes'])) {
                        $cleanedQuizzes = [];
                        foreach ($subModuleData['quizzes'] as $quizIndex => $quizData) {
                            // Skip empty quizzes (missing required fields: judul, nilai_minimum, or max_attempts)
                            if (empty($quizData['judul']) || empty($quizData['nilai_minimum']) || empty($quizData['max_attempts'])) {
                                continue;
                            }
                            // Preserve original index
                            $cleanedQuizzes[$quizIndex] = $quizData;
                        }
                        $cleanedSubModule['quizzes'] = $cleanedQuizzes;
                    }
                    
                    // Preserve original index for file upload keys
                    $cleanedSubModules[$subModuleIndex] = $cleanedSubModule;
                }
                $cleanedModule['sub_modules'] = $cleanedSubModules;
            }
            
            // Preserve original index for file upload keys
            $cleanedModules[$moduleIndex] = $cleanedModule;
        }
        
        // Merge cleaned modules back into request (preserving indices)
        $request->merge(['modules' => $cleanedModules]);

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
                'modules.*.sub_modules.*.judul' => 'required|string|max:255',
                'modules.*.sub_modules.*.deskripsi' => 'nullable|string',
                'modules.*.sub_modules.*.urutan' => 'required|integer|min:1',
                'modules.*.sub_modules.*.contents' => 'nullable|array',
                'modules.*.sub_modules.*.contents.*.judul' => 'required|string|max:255',
                'modules.*.sub_modules.*.contents.*.tipe' => 'required|in:text,html,pdf,video,audio,image,link,youtube',
                'modules.*.sub_modules.*.contents.*.urutan' => 'required|integer|min:1',
                'modules.*.sub_modules.*.contents.*.html_content' => 'nullable|string|required_if:modules.*.sub_modules.*.contents.*.tipe,html,text',
                'modules.*.sub_modules.*.contents.*.external_url' => 'nullable|url|required_if:modules.*.sub_modules.*.contents.*.tipe,link',
                'modules.*.sub_modules.*.contents.*.youtube_url' => 'nullable|url|required_if:modules.*.sub_modules.*.contents.*.tipe,youtube',
                'modules.*.sub_modules.*.contents.*.required_duration' => 'nullable|integer|min:1|required_if:modules.*.sub_modules.*.contents.*.tipe,youtube',
                'modules.*.sub_modules.*.contents.*.file_path' => 'nullable|file|max:102400',
                'modules.*.sub_modules.*.quizzes' => 'nullable|array',
                'modules.*.sub_modules.*.quizzes.*.judul' => 'required|string|max:255',
                'modules.*.sub_modules.*.quizzes.*.deskripsi' => 'nullable|string',
                'modules.*.sub_modules.*.quizzes.*.nilai_minimum' => 'required|numeric|min:0|max:100',
                'modules.*.sub_modules.*.quizzes.*.max_attempts' => 'required|integer|min:1',
                'modules.*.sub_modules.*.quizzes.*.questions' => 'nullable|array',
                'modules.*.sub_modules.*.quizzes.*.questions.*.pertanyaan' => 'required|string',
                'modules.*.sub_modules.*.quizzes.*.questions.*.tipe' => 'required|in:multiple_choice,true_false,essay',
                'modules.*.sub_modules.*.quizzes.*.questions.*.bobot' => 'required|integer|min:1',
                'modules.*.sub_modules.*.quizzes.*.questions.*.urutan' => 'nullable|integer|min:1',
                'modules.*.sub_modules.*.quizzes.*.questions.*.answer_options' => 'required_if:modules.*.sub_modules.*.quizzes.*.questions.*.tipe,multiple_choice,true_false|array|min:2|max:5',
                'modules.*.sub_modules.*.quizzes.*.questions.*.answer_options.*.teks_jawaban' => 'required|string',
                'modules.*.sub_modules.*.quizzes.*.questions.*.answer_options.*.is_correct' => 'nullable|boolean',
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
                                $content->required_duration = $contentData['required_duration'] ?? null;

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
                        
                        // Create quizzes for this sub-module
                        if (isset($subModuleData['quizzes']) && is_array($subModuleData['quizzes']) && count($subModuleData['quizzes']) > 0) {
                            foreach ($subModuleData['quizzes'] as $quizIndex => $quizData) {
                                $quiz = new \App\Models\Quiz();
                                $quiz->sub_module_id = $subModule->id;
                                $quiz->judul = $quizData['judul'] ?? '';
                                $quiz->deskripsi = $quizData['deskripsi'] ?? '';
                                $quiz->nilai_minimum = $quizData['nilai_minimum'] ?? 0;
                                $quiz->max_attempts = $quizData['max_attempts'] ?? 3;
                                $quiz->save();
                                
                                Log::info('Quiz created', ['quiz_id' => $quiz->id, 'sub_module_id' => $subModule->id]);
                                
                                // Create questions for this quiz
                                if (isset($quizData['questions']) && is_array($quizData['questions']) && count($quizData['questions']) > 0) {
                                    foreach ($quizData['questions'] as $questionIndex => $questionData) {
                                        $question = new \App\Models\Question();
                                        $question->quiz_id = $quiz->id;
                                        $question->pertanyaan = $questionData['pertanyaan'] ?? '';
                                        $question->tipe = $questionData['tipe'] ?? 'multiple_choice';
                                        $question->bobot = $questionData['bobot'] ?? 1;
                                        $question->urutan = $questionData['urutan'] ?? ($questionIndex + 1);
                                        $question->save();
                                        
                                        Log::info('Question created', ['question_id' => $question->id, 'quiz_id' => $quiz->id]);
                                        
                                        // Create answer options for this question
                                        if (isset($questionData['answer_options']) && is_array($questionData['answer_options']) && count($questionData['answer_options']) > 0) {
                                            $correctCount = 0;
                                            foreach ($questionData['answer_options'] as $optionIndex => $optionData) {
                                                $answerOption = new \App\Models\AnswerOption();
                                                $answerOption->question_id = $question->id;
                                                $answerOption->teks_jawaban = $optionData['teks_jawaban'] ?? '';
                                                $answerOption->is_correct = isset($optionData['is_correct']) && $optionData['is_correct'] == '1' ? true : false;
                                                $answerOption->save();
                                                
                                                if ($answerOption->is_correct) {
                                                    $correctCount++;
                                                }
                                                
                                                Log::info('Answer option created', ['answer_option_id' => $answerOption->id, 'question_id' => $question->id]);
                                            }
                                            
                                            // Validate that exactly one answer is correct for multiple choice
                                            if ($question->tipe === 'multiple_choice' && $correctCount !== 1) {
                                                throw new \Exception("Question '{$question->pertanyaan}' must have exactly one correct answer. Found {$correctCount} correct answers.");
                                            }
                                            
                                            // Validate minimum 2 options
                                            if (count($questionData['answer_options']) < 2) {
                                                throw new \Exception("Question '{$question->pertanyaan}' must have at least 2 answer options.");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            Log::info('Course created via wizard', ['course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.show', $course)
                ->with('success', 'Course created successfully with all modules, sub-modules, contents, and quizzes.');
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
            return redirect()->route('instructor.courses.show', $course)
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create course', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create course.');
        }
    }

    /**
     * Tampilkan detail kursus tertentu.
     * @param Course $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $course->load(['modules.subModules.contents', 'modules.subModules.quizzes'])
            ->loadCount(['userEnrollments as enrollments_count']);

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
        $course = Course::with(['modules.subModules.contents', 'modules.subModules.quizzes.questions.answerOptions'])->findOrFail($id);
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
            return redirect()->route('instructor.courses.show', $course)
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update course', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update course.');
        }
    }

    /**
     * Update course via wizard (with modules, sub-modules, contents, and quizzes).
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWizard(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('update', $course);

        // Clean and filter empty nested arrays before validation
        $modules = $request->input('modules', []);
        $cleanedModules = [];
        
        foreach ($modules as $moduleIndex => $moduleData) {
            // Skip empty modules (missing required fields: judul or urutan)
            if (empty($moduleData['judul']) || empty($moduleData['urutan'])) {
                continue;
            }
            
            $cleanedModule = $moduleData;
            
            // Clean sub_modules - preserve original indices
            if (isset($moduleData['sub_modules']) && is_array($moduleData['sub_modules'])) {
                $cleanedSubModules = [];
                foreach ($moduleData['sub_modules'] as $subModuleIndex => $subModuleData) {
                    // Skip empty sub_modules (missing required fields: judul or urutan)
                    if (empty($subModuleData['judul']) || empty($subModuleData['urutan'])) {
                        continue;
                    }
                    
                    $cleanedSubModule = $subModuleData;
                    
                    // Clean contents - preserve original indices
                    if (isset($subModuleData['contents']) && is_array($subModuleData['contents'])) {
                        $cleanedContents = [];
                        foreach ($subModuleData['contents'] as $contentIndex => $contentData) {
                            // Skip empty contents (missing required fields: judul, tipe, or urutan)
                            if (empty($contentData['judul']) || empty($contentData['tipe']) || empty($contentData['urutan'])) {
                                continue;
                            }
                            $cleanedContents[$contentIndex] = $contentData;
                        }
                        $cleanedSubModule['contents'] = $cleanedContents;
                    }
                    
                    // Clean quizzes - preserve original indices
                    if (isset($subModuleData['quizzes']) && is_array($subModuleData['quizzes'])) {
                        $cleanedQuizzes = [];
                        foreach ($subModuleData['quizzes'] as $quizIndex => $quizData) {
                            // Skip empty quizzes (missing required fields: judul, nilai_minimum, or max_attempts)
                            if (empty($quizData['judul']) || empty($quizData['nilai_minimum']) || empty($quizData['max_attempts'])) {
                                continue;
                            }
                            $cleanedQuizzes[$quizIndex] = $quizData;
                        }
                        $cleanedSubModule['quizzes'] = $cleanedQuizzes;
                    }
                    
                    $cleanedSubModules[$subModuleIndex] = $cleanedSubModule;
                }
                $cleanedModule['sub_modules'] = $cleanedSubModules;
            }
            
            $cleanedModules[$moduleIndex] = $cleanedModule;
        }
        
        // Merge cleaned modules back into request
        $request->merge(['modules' => $cleanedModules]);

        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'jp_value' => 'required|integer|min:1',
                'bidang_kompetensi' => 'required|string',
                'modules' => 'required|array|min:1',
                'modules.*.id' => 'nullable|integer|exists:modules,id',
                'modules.*.judul' => 'required|string|max:255',
                'modules.*.deskripsi' => 'nullable|string',
                'modules.*.urutan' => 'required|integer|min:1',
                'modules.*.sub_modules' => 'nullable|array',
                'modules.*.sub_modules.*.id' => 'nullable|integer|exists:sub_modules,id',
                'modules.*.sub_modules.*.judul' => 'required|string|max:255',
                'modules.*.sub_modules.*.deskripsi' => 'nullable|string',
                'modules.*.sub_modules.*.urutan' => 'required|integer|min:1',
                'modules.*.sub_modules.*.contents' => 'nullable|array',
                'modules.*.sub_modules.*.contents.*.id' => 'nullable|integer|exists:contents,id',
                'modules.*.sub_modules.*.contents.*.judul' => 'required|string|max:255',
                'modules.*.sub_modules.*.contents.*.tipe' => 'required|in:text,html,pdf,video,audio,image,link,youtube',
                'modules.*.sub_modules.*.contents.*.urutan' => 'required|integer|min:1',
                'modules.*.sub_modules.*.contents.*.html_content' => 'nullable|string|required_if:modules.*.sub_modules.*.contents.*.tipe,html,text',
                'modules.*.sub_modules.*.contents.*.external_url' => 'nullable|url|required_if:modules.*.sub_modules.*.contents.*.tipe,link',
                'modules.*.sub_modules.*.contents.*.youtube_url' => 'nullable|url|required_if:modules.*.sub_modules.*.contents.*.tipe,youtube',
                'modules.*.sub_modules.*.contents.*.required_duration' => 'nullable|integer|min:1|required_if:modules.*.sub_modules.*.contents.*.tipe,youtube',
                'modules.*.sub_modules.*.contents.*.file_path' => 'nullable|file|max:102400',
                'modules.*.sub_modules.*.quizzes' => 'nullable|array',
                'modules.*.sub_modules.*.quizzes.*.id' => 'nullable|integer|exists:quizzes,id',
                'modules.*.sub_modules.*.quizzes.*.judul' => 'required|string|max:255',
                'modules.*.sub_modules.*.quizzes.*.deskripsi' => 'nullable|string',
                'modules.*.sub_modules.*.quizzes.*.nilai_minimum' => 'required|numeric|min:0|max:100',
                'modules.*.sub_modules.*.quizzes.*.max_attempts' => 'required|integer|min:1',
                'modules.*.sub_modules.*.quizzes.*.questions' => 'nullable|array',
                'modules.*.sub_modules.*.quizzes.*.questions.*.id' => 'nullable|integer|exists:questions,id',
                'modules.*.sub_modules.*.quizzes.*.questions.*.pertanyaan' => 'required|string',
                'modules.*.sub_modules.*.quizzes.*.questions.*.tipe' => 'required|in:multiple_choice,true_false,essay',
                'modules.*.sub_modules.*.quizzes.*.questions.*.bobot' => 'required|integer|min:1',
                'modules.*.sub_modules.*.quizzes.*.questions.*.urutan' => 'nullable|integer|min:1',
                'modules.*.sub_modules.*.quizzes.*.questions.*.answer_options' => 'required_if:modules.*.sub_modules.*.quizzes.*.questions.*.tipe,multiple_choice,true_false|array|min:2|max:5',
                'modules.*.sub_modules.*.quizzes.*.questions.*.answer_options.*.teks_jawaban' => 'required|string',
                'modules.*.sub_modules.*.quizzes.*.questions.*.answer_options.*.is_correct' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in update wizard', [
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
            // Update course
            $course->judul = $request->judul;
            $course->deskripsi = $request->deskripsi;
            $course->jp_value = $request->jp_value;
            $course->bidang_kompetensi = $request->bidang_kompetensi;
            $course->save();

            Log::info('Course updated', ['course_id' => $course->id]);

            // Get existing module IDs to track what to delete
            $existingModuleIds = $course->modules->pluck('id')->toArray();
            $submittedModuleIds = [];

            // Update or create modules, sub-modules, contents, and quizzes
            foreach ($request->modules as $moduleIndex => $moduleData) {
                $moduleId = $moduleData['id'] ?? null;
                
                if ($moduleId && \App\Models\Module::find($moduleId)) {
                    // Update existing module
                    $module = \App\Models\Module::find($moduleId);
                    $module->judul = $moduleData['judul'] ?? '';
                    $module->deskripsi = $moduleData['deskripsi'] ?? '';
                    $module->urutan = $moduleData['urutan'] ?? ($moduleIndex + 1);
                    $module->save();
                    $submittedModuleIds[] = $moduleId;
                } else {
                    // Create new module
                    $module = new \App\Models\Module();
                    $module->course_id = $course->id;
                    $module->judul = $moduleData['judul'] ?? '';
                    $module->deskripsi = $moduleData['deskripsi'] ?? '';
                    $module->urutan = $moduleData['urutan'] ?? ($moduleIndex + 1);
                    $module->save();
                    $submittedModuleIds[] = $module->id;
                }

                Log::info('Module updated/created', ['module_id' => $module->id, 'module_index' => $moduleIndex]);

                if (isset($moduleData['sub_modules']) && is_array($moduleData['sub_modules']) && count($moduleData['sub_modules']) > 0) {
                    // Get existing sub-module IDs to track what to delete
                    $existingSubModuleIds = $module->subModules->pluck('id')->toArray();
                    $submittedSubModuleIds = [];
                    
                    foreach ($moduleData['sub_modules'] as $subModuleIndex => $subModuleData) {
                        $subModuleId = $subModuleData['id'] ?? null;
                        
                        if ($subModuleId && \App\Models\SubModule::find($subModuleId)) {
                            // Update existing sub-module
                            $subModule = \App\Models\SubModule::find($subModuleId);
                            $subModule->judul = $subModuleData['judul'] ?? '';
                            $subModule->deskripsi = $subModuleData['deskripsi'] ?? '';
                            $subModule->urutan = $subModuleData['urutan'] ?? ($subModuleIndex + 1);
                            $subModule->save();
                            $submittedSubModuleIds[] = $subModuleId;
                        } else {
                            // Create new sub-module
                            $subModule = new \App\Models\SubModule();
                            $subModule->module_id = $module->id;
                            $subModule->judul = $subModuleData['judul'] ?? '';
                            $subModule->deskripsi = $subModuleData['deskripsi'] ?? '';
                            $subModule->urutan = $subModuleData['urutan'] ?? ($subModuleIndex + 1);
                            $subModule->save();
                            $submittedSubModuleIds[] = $subModule->id;
                        }

                        Log::info('SubModule updated/created', ['sub_module_id' => $subModule->id, 'module_id' => $module->id]);

                        // Handle contents
                        if (isset($subModuleData['contents']) && is_array($subModuleData['contents']) && count($subModuleData['contents']) > 0) {
                            $existingContentIds = $subModule->contents->pluck('id')->toArray();
                            $submittedContentIds = [];
                            
                            foreach ($subModuleData['contents'] as $contentIndex => $contentData) {
                                $contentId = $contentData['id'] ?? null;
                                
                                if ($contentId && \App\Models\Content::find($contentId)) {
                                    // Update existing content
                                    $content = \App\Models\Content::find($contentId);
                                    $content->judul = $contentData['judul'] ?? '';
                                    $content->tipe = $contentData['tipe'] ?? 'text';
                                    $content->urutan = $contentData['urutan'] ?? ($contentIndex + 1);
                                    $content->html_content = $contentData['html_content'] ?? null;
                                    $content->external_url = $contentData['external_url'] ?? null;
                                    $content->youtube_url = $contentData['youtube_url'] ?? null;
                                    $content->required_duration = $contentData['required_duration'] ?? null;

                                    // Handle file upload
                                    $fileKey = "modules.{$moduleIndex}.sub_modules.{$subModuleIndex}.contents.{$contentIndex}.file_path";
                                    if ($request->hasFile($fileKey)) {
                                        $file = $request->file($fileKey);
                                        if ($file && $file->isValid()) {
                                            $path = $file->store('contents/' . date('Y/m/d'), 'public');
                                            $content->file_path = $path;
                                        }
                                    }

                                    $content->save();
                                    $submittedContentIds[] = $contentId;
                                } else {
                                    // Create new content
                                    $content = new \App\Models\Content();
                                    $content->sub_module_id = $subModule->id;
                                    $content->judul = $contentData['judul'] ?? '';
                                    $content->tipe = $contentData['tipe'] ?? 'text';
                                    $content->urutan = $contentData['urutan'] ?? ($contentIndex + 1);
                                    $content->html_content = $contentData['html_content'] ?? null;
                                    $content->external_url = $contentData['external_url'] ?? null;
                                    $content->youtube_url = $contentData['youtube_url'] ?? null;
                                    $content->required_duration = $contentData['required_duration'] ?? null;

                                    // Handle file upload
                                    $fileKey = "modules.{$moduleIndex}.sub_modules.{$subModuleIndex}.contents.{$contentIndex}.file_path";
                                    if ($request->hasFile($fileKey)) {
                                        $file = $request->file($fileKey);
                                        if ($file && $file->isValid()) {
                                            $path = $file->store('contents/' . date('Y/m/d'), 'public');
                                            $content->file_path = $path;
                                        }
                                    }

                                    $content->save();
                                    $submittedContentIds[] = $content->id;
                                }
                                
                                Log::info('Content updated/created', ['content_id' => $content->id, 'sub_module_id' => $subModule->id]);
                            }
                            
                            // Delete contents that are no longer in the form
                            $contentsToDelete = array_diff($existingContentIds, $submittedContentIds);
                            foreach ($contentsToDelete as $contentId) {
                                $content = \App\Models\Content::find($contentId);
                                if ($content) {
                                    $content->delete();
                                }
                            }
                        }
                        
                        // Handle quizzes
                        if (isset($subModuleData['quizzes']) && is_array($subModuleData['quizzes']) && count($subModuleData['quizzes']) > 0) {
                            $existingQuizIds = $subModule->quizzes->pluck('id')->toArray();
                            $submittedQuizIds = [];
                            
                            foreach ($subModuleData['quizzes'] as $quizIndex => $quizData) {
                                $quizId = $quizData['id'] ?? null;
                                
                                if ($quizId && \App\Models\Quiz::find($quizId)) {
                                    // Update existing quiz
                                    $quiz = \App\Models\Quiz::find($quizId);
                                    $quiz->judul = $quizData['judul'] ?? '';
                                    $quiz->deskripsi = $quizData['deskripsi'] ?? '';
                                    $quiz->nilai_minimum = $quizData['nilai_minimum'] ?? 0;
                                    $quiz->max_attempts = $quizData['max_attempts'] ?? 3;
                                    $quiz->save();
                                    $submittedQuizIds[] = $quizId;
                                } else {
                                    // Create new quiz
                                    $quiz = new \App\Models\Quiz();
                                    $quiz->sub_module_id = $subModule->id;
                                    $quiz->judul = $quizData['judul'] ?? '';
                                    $quiz->deskripsi = $quizData['deskripsi'] ?? '';
                                    $quiz->nilai_minimum = $quizData['nilai_minimum'] ?? 0;
                                    $quiz->max_attempts = $quizData['max_attempts'] ?? 3;
                                    $quiz->save();
                                    $submittedQuizIds[] = $quiz->id;
                                }
                                
                                Log::info('Quiz updated/created', ['quiz_id' => $quiz->id, 'sub_module_id' => $subModule->id]);
                                
                                // Handle questions
                                if (isset($quizData['questions']) && is_array($quizData['questions']) && count($quizData['questions']) > 0) {
                                    $existingQuestionIds = $quiz->questions->pluck('id')->toArray();
                                    $submittedQuestionIds = [];
                                    
                                    foreach ($quizData['questions'] as $questionIndex => $questionData) {
                                        $questionId = $questionData['id'] ?? null;
                                        
                                        if ($questionId && \App\Models\Question::find($questionId)) {
                                            // Update existing question
                                            $question = \App\Models\Question::find($questionId);
                                            $question->pertanyaan = $questionData['pertanyaan'] ?? '';
                                            $question->tipe = $questionData['tipe'] ?? 'multiple_choice';
                                            $question->bobot = $questionData['bobot'] ?? 1;
                                            $question->urutan = $questionData['urutan'] ?? ($questionIndex + 1);
                                            $question->save();
                                            $submittedQuestionIds[] = $questionId;
                                            
                                            // Delete existing answer options
                                            $question->answerOptions()->delete();
                                        } else {
                                            // Create new question
                                            $question = new \App\Models\Question();
                                            $question->quiz_id = $quiz->id;
                                            $question->pertanyaan = $questionData['pertanyaan'] ?? '';
                                            $question->tipe = $questionData['tipe'] ?? 'multiple_choice';
                                            $question->bobot = $questionData['bobot'] ?? 1;
                                            $question->urutan = $questionData['urutan'] ?? ($questionIndex + 1);
                                            $question->save();
                                            $submittedQuestionIds[] = $question->id;
                                        }
                                        
                                        Log::info('Question updated/created', ['question_id' => $question->id, 'quiz_id' => $quiz->id]);
                                        
                                        // Handle answer options
                                        if (isset($questionData['answer_options']) && is_array($questionData['answer_options']) && count($questionData['answer_options']) > 0) {
                                            $correctCount = 0;
                                            foreach ($questionData['answer_options'] as $optionIndex => $optionData) {
                                                $answerOption = new \App\Models\AnswerOption();
                                                $answerOption->question_id = $question->id;
                                                $answerOption->teks_jawaban = $optionData['teks_jawaban'] ?? '';
                                                $answerOption->is_correct = isset($optionData['is_correct']) && $optionData['is_correct'] == '1' ? true : false;
                                                $answerOption->save();
                                                
                                                if ($answerOption->is_correct) {
                                                    $correctCount++;
                                                }
                                                
                                                Log::info('Answer option created', ['answer_option_id' => $answerOption->id, 'question_id' => $question->id]);
                                            }
                                            
                                            // Validate that exactly one answer is correct for multiple choice
                                            if ($question->tipe === 'multiple_choice' && $correctCount !== 1) {
                                                throw new \Exception("Question '{$question->pertanyaan}' must have exactly one correct answer. Found {$correctCount} correct answers.");
                                            }
                                            
                                            // Validate minimum 2 options
                                            if (count($questionData['answer_options']) < 2) {
                                                throw new \Exception("Question '{$question->pertanyaan}' must have at least 2 answer options.");
                                            }
                                        }
                                    }
                                    
                                    // Delete questions that are no longer in the form
                                    $questionsToDelete = array_diff($existingQuestionIds, $submittedQuestionIds);
                                    foreach ($questionsToDelete as $questionId) {
                                        $question = \App\Models\Question::find($questionId);
                                        if ($question) {
                                            $question->answerOptions()->delete();
                                            $question->delete();
                                        }
                                    }
                                }
                            }
                            
                            // Delete quizzes that are no longer in the form
                            $quizzesToDelete = array_diff($existingQuizIds, $submittedQuizIds);
                            foreach ($quizzesToDelete as $quizId) {
                                $quiz = \App\Models\Quiz::find($quizId);
                                if ($quiz) {
                                    foreach ($quiz->questions as $question) {
                                        $question->answerOptions()->delete();
                                        $question->delete();
                                    }
                                    $quiz->delete();
                                }
                            }
                        }
                    }
                    
                    // Delete sub-modules that are no longer in the form
                    $subModulesToDelete = array_diff($existingSubModuleIds, $submittedSubModuleIds);
                    foreach ($subModulesToDelete as $subModuleId) {
                        $subModuleToDelete = \App\Models\SubModule::find($subModuleId);
                        if ($subModuleToDelete) {
                            // Delete contents
                            foreach ($subModuleToDelete->contents as $content) {
                                $content->delete();
                            }
                            // Delete quizzes and questions
                            foreach ($subModuleToDelete->quizzes as $quiz) {
                                foreach ($quiz->questions as $question) {
                                    $question->answerOptions()->delete();
                                    $question->delete();
                                }
                                $quiz->delete();
                            }
                            $subModuleToDelete->delete();
                        }
                    }
                }
            }
            
            // Delete modules that are no longer in the form
            $modulesToDelete = array_diff($existingModuleIds, $submittedModuleIds);
            foreach ($modulesToDelete as $moduleId) {
                $moduleToDelete = \App\Models\Module::find($moduleId);
                if ($moduleToDelete) {
                    foreach ($moduleToDelete->subModules as $subModule) {
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
                    $moduleToDelete->delete();
                }
            }

            DB::commit();
            Log::info('Course updated via wizard', ['course_id' => $course->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.courses.show', $course)
                ->with('success', 'Course updated successfully with all modules, sub-modules, contents, and quizzes.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update course via wizard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage() . ' Please check the logs for more details.');
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
            return redirect()->route('instructor.courses.show', $newCourse)
                ->with('success', 'Course duplicated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate course', ['course_id' => $course->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to duplicate course.');
        }
    }
}


