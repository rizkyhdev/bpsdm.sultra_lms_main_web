<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubModule;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminQuizController extends Controller
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
     * Menampilkan daftar kuis dengan paginasi untuk sub-modul tertentu.
     *
     * @param int $subModuleId
     * @return \Illuminate\View\View
     */
    public function index($subModuleId)
    {
        try {
            $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
            $quizzes = Quiz::where('sub_module_id', $subModuleId)
                           ->withCount(['questions', 'quizAttempts'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

            return view('admin.quizzes.index', compact('subModule', 'quizzes'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data kuis.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat kuis baru.
     *
     * @param int $subModuleId
     * @return \Illuminate\View\View
     */
    public function create($subModuleId)
    {
        try {
            $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
            return view('admin.quizzes.create', compact('subModule'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form kuis.');
        }
    }

    /**
     * Menyimpan kuis yang baru dibuat ke dalam penyimpanan.
     *
     * @param Request $request
     * @param int $subModuleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $subModuleId)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'nilai_minimum' => 'required|numeric|min:0|max:100',
                'max_attempts' => 'required|integer|min:1',
                'time_limit' => 'nullable|integer|min:1', // dalam menit
                'is_active' => 'boolean'
            ]);

            $validated['sub_module_id'] = $subModuleId;
            $validated['is_active'] = $request->has('is_active');

            Quiz::create($validated);

            Log::info('Admin created new quiz: ' . $validated['judul'] . ' for sub-module ID: ' . $subModuleId);
            return redirect()->route('admin.quizzes.index', $subModuleId)
                           ->with('success', 'Kuis berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat kuis.');
        }
    }

    /**
     * Menampilkan kuis tertentu dengan pertanyaan dan statistik.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $quiz = Quiz::with([
                'subModule.module.course',
                'questions.answerOptions',
                'quizAttempts.user'
            ])->findOrFail($id);

            $stats = [
                'total_questions' => $quiz->questions->count(),
                'total_attempts' => $quiz->quizAttempts->count(),
                'passed_attempts' => $quiz->quizAttempts->where('is_passed', true)->count(),
                'failed_attempts' => $quiz->quizAttempts->where('is_passed', false)->count(),
                'average_score' => $quiz->quizAttempts->count() > 0 
                    ? round($quiz->quizAttempts->avg('nilai'), 2) 
                    : 0,
                'pass_rate' => $quiz->quizAttempts->count() > 0 
                    ? round(($quiz->quizAttempts->where('is_passed', true)->count() / $quiz->quizAttempts->count()) * 100, 2)
                    : 0
            ];

            // Dapatkan percobaan terbaru
            $recentAttempts = $quiz->quizAttempts()
                                   ->with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->limit(10)
                                   ->get();

            return view('admin.quizzes.show', compact('quiz', 'stats', 'recentAttempts'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data kuis.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit kuis tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $quiz = Quiz::with('subModule.module.course')->findOrFail($id);
            return view('admin.quizzes.edit', compact('quiz'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data kuis.');
        }
    }

    /**
     * Memperbarui kuis tertentu dalam penyimpanan.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $quiz = Quiz::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'nilai_minimum' => 'required|numeric|min:0|max:100',
                'max_attempts' => 'required|integer|min:1',
                'time_limit' => 'nullable|integer|min:1', // dalam menit
                'is_active' => 'boolean'
            ]);

            $validated['is_active'] = $request->has('is_active');

            $quiz->update($validated);

            Log::info('Admin updated quiz: ' . $quiz->judul);
            return redirect()->route('admin.quizzes.index', $quiz->sub_module_id)
                           ->with('success', 'Data kuis berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data kuis.');
        }
    }

    /**
     * Menghapus kuis tertentu dari penyimpanan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $quiz = Quiz::with(['questions.answerOptions', 'quizAttempts'])->findOrFail($id);
            $subModuleId = $quiz->sub_module_id;
            $quizTitle = $quiz->judul;

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::transaction(function() use ($quiz) {
                // Hapus pertanyaan dan opsi jawaban
                $quiz->questions->each(function($question) {
                    $question->answerOptions()->delete();
                    $question->delete();
                });

                // Hapus percobaan kuis
                $quiz->quizAttempts()->delete();

                $quiz->delete();
            });

            Log::info('Admin deleted quiz: ' . $quizTitle);
            return redirect()->route('admin.quizzes.index', $subModuleId)
                           ->with('success', 'Kuis berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus kuis.');
        }
    }

    /**
     * Menampilkan hasil kuis dan percobaan.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function results($id)
    {
        try {
            $quiz = Quiz::with([
                'subModule.module.course',
                'quizAttempts.user'
            ])->findOrFail($id);

            $attempts = $quiz->quizAttempts()
                             ->with('user')
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);

            $summaryStats = [
                'total_attempts' => $quiz->quizAttempts->count(),
                'unique_users' => $quiz->quizAttempts->unique('user_id')->count(),
                'passed_attempts' => $quiz->quizAttempts->where('is_passed', true)->count(),
                'failed_attempts' => $quiz->quizAttempts->where('is_passed', false)->count(),
                'average_score' => $quiz->quizAttempts->count() > 0 
                    ? round($quiz->quizAttempts->avg('nilai'), 2) 
                    : 0,
                'highest_score' => $quiz->quizAttempts->max('nilai') ?? 0,
                'lowest_score' => $quiz->quizAttempts->min('nilai') ?? 0,
                'pass_rate' => $quiz->quizAttempts->count() > 0 
                    ? round(($quiz->quizAttempts->where('is_passed', true)->count() / $quiz->quizAttempts->count()) * 100, 2)
                    : 0
            ];

            // Distribusi skor
            $scoreRanges = [
                '0-20' => $quiz->quizAttempts->whereBetween('nilai', [0, 20])->count(),
                '21-40' => $quiz->quizAttempts->whereBetween('nilai', [21, 40])->count(),
                '41-60' => $quiz->quizAttempts->whereBetween('nilai', [41, 60])->count(),
                '61-80' => $quiz->quizAttempts->whereBetween('nilai', [61, 80])->count(),
                '81-100' => $quiz->quizAttempts->whereBetween('nilai', [81, 100])->count(),
            ];

            return view('admin.quizzes.results', compact('quiz', 'attempts', 'summaryStats', 'scoreRanges'));
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@results: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat hasil kuis.');
        }
    }

    /**
     * Mengekspor hasil kuis ke Excel/PDF.
     *
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportResults(Request $request, $id)
    {
        try {
            $quiz = Quiz::with([
                'subModule.module.course',
                'quizAttempts.user'
            ])->findOrFail($id);

            $format = $request->get('format', 'xlsx');
            
            if ($format === 'pdf') {
                $attempts = $quiz->quizAttempts()->with('user')->get();
                $pdf = PDF::loadView('admin.quizzes.export-pdf', compact('quiz', 'attempts'));
                return $pdf->download('quiz-results-' . Str::slug($quiz->judul) . '.pdf');
            } else {
                return Excel::download(new QuizResultsExport($quiz), 'quiz-results-' . Str::slug($quiz->judul) . '.xlsx');
            }
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@exportResults: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor hasil kuis.');
        }
    }

    /**
     * Toggle status aktif kuis.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->update(['is_active' => !$quiz->is_active]);

            $status = $quiz->is_active ? 'diaktifkan' : 'dinonaktifkan';
            Log::info('Admin ' . $status . ' quiz: ' . $quiz->judul);
            
            return back()->with('success', 'Status kuis berhasil ' . $status . '.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@toggleStatus: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengubah status kuis.');
        }
    }

    /**
     * Dapatkan statistik kuis untuk dashboard.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats($id)
    {
        try {
            $quiz = Quiz::with('quizAttempts')->findOrFail($id);

            $stats = [
                'total_attempts' => $quiz->quizAttempts->count(),
                'passed_attempts' => $quiz->quizAttempts->where('is_passed', true)->count(),
                'failed_attempts' => $quiz->quizAttempts->where('is_passed', false)->count(),
                'average_score' => $quiz->quizAttempts->count() > 0 
                    ? round($quiz->quizAttempts->avg('nilai'), 2) 
                    : 0,
                'pass_rate' => $quiz->quizAttempts->count() > 0 
                    ? round(($quiz->quizAttempts->where('is_passed', true)->count() / $quiz->quizAttempts->count()) * 100, 2)
                    : 0
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@getStats: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat statistik'], 500);
        }
    }

    /**
     * Menduplikasi kuis dengan semua pertanyaan dan opsi jawaban.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate($id)
    {
        try {
            $originalQuiz = Quiz::with('questions.answerOptions')->findOrFail($id);

            DB::transaction(function() use ($originalQuiz) {
                // Buat kuis baru
                $newQuiz = $originalQuiz->replicate();
                $newQuiz->judul = $originalQuiz->judul . ' (Copy)';
                $newQuiz->save();

                // Duplikasi pertanyaan dan opsi jawaban
                foreach ($originalQuiz->questions as $question) {
                    $newQuestion = $question->replicate();
                    $newQuestion->quiz_id = $newQuiz->id;
                    $newQuestion->save();

                    foreach ($question->answerOptions as $answerOption) {
                        $newAnswerOption = $answerOption->replicate();
                        $newAnswerOption->question_id = $newQuestion->id;
                        $newAnswerOption->save();
                    }
                }
            });

            Log::info('Admin duplicated quiz: ' . $originalQuiz->judul);
            return redirect()->route('admin.quizzes.index', $originalQuiz->sub_module_id)
                           ->with('success', 'Kuis berhasil diduplikasi.');

        } catch (\Exception $e) {
            Log::error('Error in AdminQuizController@duplicate: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menduplikasi kuis.');
        }
    }
}
