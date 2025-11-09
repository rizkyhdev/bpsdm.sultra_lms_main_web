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
        $this->middleware(['auth', 'role:instructor']);
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

        $subs = SubModule::withCount(['contents', 'quizzes'])
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
            return redirect()->route('instructor.sub_modules.index', $module->id)->with('success', 'Sub-module created successfully.');
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
        $subModule = SubModule::with(['module.course', 'contents' => function ($query) {
            $query->orderBy('urutan');
        }, 'quizzes'])->findOrFail($id);
        $this->authorize('view', $subModule);
        
        $contents = $subModule->contents;
        $quizzes = $subModule->quizzes;
        
        // Calculate progress summary
        $progressSummary = [
            'avg_completion' => 0,
            'participants' => 0,
            'completed' => 0,
        ];
        
        return view('instructor.submodules.show', compact('subModule', 'contents', 'quizzes', 'progressSummary'));
    }

    /**
     * Tampilkan form edit.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subModule = SubModule::with(['module.course', 'contents', 'quizzes.questions.answerOptions'])->findOrFail($id);
        $this->authorize('update', $subModule);
        return view('instructor.sub_modules.edit', compact('subModule'));
    }

    /**
     * Perbarui sub-modul.
     * @param UpdateSubModuleRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSubModuleRequest $request, $id)
    {
        $sub = SubModule::with(['module.course', 'contents', 'quizzes.questions.answerOptions'])->findOrFail($id);
        $this->authorize('update', $sub);

        DB::beginTransaction();
        try {
            // Update sub-module basic info
            $sub->update($request->only(['judul', 'deskripsi', 'urutan']));

            // Handle contents
            if ($request->has('contents') && is_array($request->contents)) {
                $existingContentIds = $sub->contents->pluck('id')->toArray();
                $submittedContentIds = [];

                foreach ($request->contents as $contentIndex => $contentData) {
                    $contentId = $contentData['id'] ?? null;

                    if ($contentId && \App\Models\Content::find($contentId)) {
                        // Update existing content
                        $content = \App\Models\Content::find($contentId);
                        $content->judul = $contentData['judul'] ?? '';
                        $content->tipe = $contentData['tipe'] ?? 'text';
                        $content->urutan = $contentData['urutan'] ?? 1;
                        $content->html_content = $contentData['html_content'] ?? null;
                        $content->external_url = $contentData['external_url'] ?? null;
                        $content->youtube_url = $contentData['youtube_url'] ?? null;
                        $content->required_duration = $contentData['required_duration'] ?? null;

                        // Handle file upload
                        if ($request->hasFile("contents.{$contentIndex}.file_path")) {
                            $file = $request->file("contents.{$contentIndex}.file_path");
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
                        $content->sub_module_id = $sub->id;
                        $content->judul = $contentData['judul'] ?? '';
                        $content->tipe = $contentData['tipe'] ?? 'text';
                        $content->urutan = $contentData['urutan'] ?? 1;
                        $content->html_content = $contentData['html_content'] ?? null;
                        $content->external_url = $contentData['external_url'] ?? null;
                        $content->youtube_url = $contentData['youtube_url'] ?? null;
                        $content->required_duration = $contentData['required_duration'] ?? null;

                        // Handle file upload
                        if ($request->hasFile("contents.{$contentIndex}.file_path")) {
                            $file = $request->file("contents.{$contentIndex}.file_path");
                            if ($file && $file->isValid()) {
                                $path = $file->store('contents/' . date('Y/m/d'), 'public');
                                $content->file_path = $path;
                            }
                        }

                        $content->save();
                        $submittedContentIds[] = $content->id;
                    }
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
            if ($request->has('quizzes') && is_array($request->quizzes)) {
                $existingQuizIds = $sub->quizzes->pluck('id')->toArray();
                $submittedQuizIds = [];

                foreach ($request->quizzes as $quizIndex => $quizData) {
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
                        $quiz->sub_module_id = $sub->id;
                        $quiz->judul = $quizData['judul'] ?? '';
                        $quiz->deskripsi = $quizData['deskripsi'] ?? '';
                        $quiz->nilai_minimum = $quizData['nilai_minimum'] ?? 0;
                        $quiz->max_attempts = $quizData['max_attempts'] ?? 3;
                        $quiz->save();
                        $submittedQuizIds[] = $quiz->id;
                    }

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

            DB::commit();
            Log::info('SubModule updated', ['sub_module_id' => $sub->id, 'instructor_id' => Auth::id()]);
            return redirect()->route('instructor.sub_modules.show', $sub->id)->with('success', 'Sub-module updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update sub-module', ['sub_module_id' => $sub->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update sub-module: ' . $e->getMessage());
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


