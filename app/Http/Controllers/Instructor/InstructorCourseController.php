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


