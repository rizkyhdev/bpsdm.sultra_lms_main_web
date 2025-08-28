<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminCourseController extends Controller
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
     * Menampilkan daftar kursus dengan paginasi dan pencarian.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Course::withCount(['modules', 'userEnrollments']);

            // Fungsi pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhere('bidang_kompetensi', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan rentang nilai JP
            if ($request->filled('jp_min')) {
                $query->where('jp_value', '>=', $request->jp_min);
            }

            if ($request->filled('jp_max')) {
                $query->where('jp_value', '<=', $request->jp_max);
            }

            // Filter berdasarkan bidang kompetensi
            if ($request->filled('bidang_kompetensi') && $request->bidang_kompetensi !== 'all') {
                $query->where('bidang_kompetensi', $request->bidang_kompetensi);
            }

            $courses = $query->orderBy('created_at', 'desc')
                            ->paginate(15);

            $bidangKompetensi = Course::distinct()->pluck('bidang_kompetensi');

            return view('admin.courses.index', compact('courses', 'bidangKompetensi'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data kursus.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat kursus baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Menyimpan kursus yang baru dibuat ke dalam penyimpanan.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'jp_value' => 'required|integer|min:1',
                'bidang_kompetensi' => 'required|string|max:255'
            ]);

            $course = Course::create($validated);

            Log::info('Admin created new course: ' . $course->judul);
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Kursus berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat kursus.');
        }
    }

    /**
     * Menampilkan kursus tertentu dengan modul dan statistik pendaftaran.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $course = Course::with([
                'modules.subModules.contents',
                'modules.subModules.quizzes',
                'userEnrollments.user'
            ])->findOrFail($id);

            $enrollmentStats = [
                'total_enrolled' => $course->userEnrollments->count(),
                'completed' => $course->userEnrollments->where('status', 'completed')->count(),
                'in_progress' => $course->userEnrollments->where('status', 'in_progress')->count(),
                'not_started' => $course->userEnrollments->where('status', 'not_started')->count(),
                'completion_rate' => $course->userEnrollments->count() > 0 
                    ? round(($course->userEnrollments->where('status', 'completed')->count() / $course->userEnrollments->count()) * 100, 2)
                    : 0
            ];

            $moduleStats = [
                'total_modules' => $course->modules->count(),
                'total_sub_modules' => $course->modules->sum(function($module) {
                    return $module->subModules->count();
                }),
                'total_contents' => $course->modules->sum(function($module) {
                    return $module->subModules->sum(function($subModule) {
                        return $subModule->contents->count();
                    });
                }),
                'total_quizzes' => $course->modules->sum(function($module) {
                    return $module->subModules->sum(function($subModule) {
                        return $subModule->quizzes->count();
                    });
                })
            ];

            return view('admin.courses.show', compact('course', 'enrollmentStats', 'moduleStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data kursus.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit kursus tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $course = Course::findOrFail($id);
            return view('admin.courses.edit', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data kursus.');
        }
    }

    /**
     * Memperbarui kursus tertentu dalam penyimpanan.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'jp_value' => 'required|integer|min:1',
                'bidang_kompetensi' => 'required|string|max:255'
            ]);

            $course->update($validated);

            Log::info('Admin updated course: ' . $course->judul);
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Data kursus berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data kursus.');
        }
    }

    /**
     * Menghapus kursus tertentu dari penyimpanan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);

            // Periksa apakah kursus memiliki pendaftaran
            if ($course->userEnrollments()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus kursus yang memiliki peserta terdaftar.');
            }

            $courseTitle = $course->judul;

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::transaction(function() use ($course) {
                // Hapus data terkait secara berurutan
                $course->modules->each(function($module) {
                    $module->subModules->each(function($subModule) {
                        // Hapus konten dan file terkait
                        $subModule->contents->each(function($content) {
                            if ($content->file_path && Storage::exists($content->file_path)) {
                                Storage::delete($content->file_path);
                            }
                            $content->delete();
                        });

                        // Hapus kuis dan data terkait
                        $subModule->quizzes->each(function($quiz) {
                            $quiz->questions->each(function($question) {
                                $question->answerOptions()->delete();
                                $question->delete();
                            });
                            $quiz->delete();
                        });

                        $subModule->delete();
                    });
                    $module->delete();
                });

                $course->delete();
            });

            Log::info('Admin deleted course: ' . $courseTitle);
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Kursus berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus kursus.');
        }
    }

    /**
     * Menduplikasi kursus dengan semua kontennya.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate($id)
    {
        try {
            $originalCourse = Course::with([
                'modules.subModules.contents',
                'modules.subModules.quizzes.questions.answerOptions'
            ])->findOrFail($id);

            DB::transaction(function() use ($originalCourse) {
                // Buat kursus baru
                $newCourse = $originalCourse->replicate();
                $newCourse->judul = $originalCourse->judul . ' (Copy)';
                $newCourse->save();

                // Duplikasi modul
                foreach ($originalCourse->modules as $module) {
                    $newModule = $module->replicate();
                    $newModule->course_id = $newCourse->id;
                    $newModule->save();

                    // Duplikasi sub-modul
                    foreach ($module->subModules as $subModule) {
                        $newSubModule = $subModule->replicate();
                        $newSubModule->module_id = $newModule->id;
                        $newSubModule->save();

                        // Duplikasi konten
                        foreach ($subModule->contents as $content) {
                            $newContent = $content->replicate();
                            $newContent->sub_module_id = $newSubModule->id;
                            
                            // Salin file jika ada
                            if ($content->file_path && Storage::exists($content->file_path)) {
                                $newFilePath = 'courses/' . $newCourse->id . '/' . basename($content->file_path);
                                Storage::copy($content->file_path, $newFilePath);
                                $newContent->file_path = $newFilePath;
                            }
                            
                            $newContent->save();
                        }

                        // Duplikasi kuis
                        foreach ($subModule->quizzes as $quiz) {
                            $newQuiz = $quiz->replicate();
                            $newQuiz->sub_module_id = $newSubModule->id;
                            $newQuiz->save();

                            // Duplikasi pertanyaan dan opsi jawaban
                            foreach ($quiz->questions as $question) {
                                $newQuestion = $question->replicate();
                                $newQuestion->quiz_id = $newQuiz->id;
                                $newQuestion->save();

                                foreach ($question->answerOptions as $answerOption) {
                                    $newAnswerOption = $answerOption->replicate();
                                    $newAnswerOption->question_id = $newQuestion->id;
                                    $newAnswerOption->save();
                                }
                            }
                        }
                    }
                }
            });

            Log::info('Admin duplicated course: ' . $originalCourse->judul);
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Kursus berhasil diduplikasi.');

        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@duplicate: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menduplikasi kursus.');
        }
    }

    /**
     * Menghasilkan laporan pendaftaran untuk kursus tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function enrollmentReport($id)
    {
        try {
            $course = Course::with([
                'userEnrollments.user',
                'userEnrollments.userProgress'
            ])->findOrFail($id);

            $enrollmentData = $course->userEnrollments->map(function($enrollment) {
                $progress = $enrollment->user->userProgress()
                    ->whereHas('subModule', function($query) use ($course) {
                        $query->whereHas('module', function($q) use ($course) {
                            $q->where('course_id', $course->id);
                        });
                    })->get();

                $totalSubModules = $course->modules->sum(function($module) {
                    return $module->subModules->count();
                });

                $completedSubModules = $progress->where('is_completed', true)->count();
                $progressPercentage = $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;

                return [
                    'user' => $enrollment->user,
                    'enrollment_date' => $enrollment->enrollment_date,
                    'status' => $enrollment->status,
                    'completed_at' => $enrollment->completed_at,
                    'progress_percentage' => $progressPercentage,
                    'completed_sub_modules' => $completedSubModules,
                    'total_sub_modules' => $totalSubModules
                ];
            });

            return view('admin.courses.enrollment-report', compact('course', 'enrollmentData'));
        } catch (\Exception $e) {
            Log::error('Error in AdminCourseController@enrollmentReport: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan pendaftaran.');
        }
    }
}
