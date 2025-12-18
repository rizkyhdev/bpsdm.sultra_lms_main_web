<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminModuleController extends Controller
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
     * Menampilkan daftar modul dengan paginasi untuk kursus tertentu.
     *
     * @param int $courseId
     * @return \Illuminate\View\View
     */
    public function index($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $modules = Module::where('course_id', $courseId)
                            ->withCount(['subModules'])
                            ->orderBy('urutan', 'asc')
                            ->paginate(15);

            return view('admin.modules.index', compact('course', 'modules'));
        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data modul.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat modul baru.
     *
     * @param int $courseId
     * @return \Illuminate\View\View
     */
    public function create($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            return view('admin.modules.create', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form modul.');
        }
    }

    /**
     * Menyimpan modul yang baru dibuat ke dalam penyimpanan.
     *
     * @param Request $request
     * @param int $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $courseId)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
        // If not provided, order will be set to last + 1
        'urutan' => 'nullable|integer|min:1'
            ]);

            $validated['course_id'] = $courseId;

      // Jika urutan tidak diisi, tempatkan di posisi terakhir + 1
      if (empty($validated['urutan'])) {
        $validated['urutan'] = (int) Module::where('course_id', $courseId)->max('urutan') + 1;
      } else {
        // Periksa apakah nomor urutan sudah ada dan geser bila perlu
        $existingModule = Module::where('course_id', $courseId)
                               ->where('urutan', $validated['urutan'])
                               ->first();

        if ($existingModule) {
          Module::where('course_id', $courseId)
                ->where('urutan', '>=', $validated['urutan'])
                ->increment('urutan');
        }
      }

            Module::create($validated);

            Log::info('Admin created new module: ' . $validated['judul'] . ' for course ID: ' . $courseId);
            return redirect()->route('admin.modules.index', $courseId)
                           ->with('success', 'Modul berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat modul.');
        }
    }

    /**
     * Menampilkan modul tertentu dengan sub-modul.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $module = Module::with([
                'course',
                'subModules.contents',
                'subModules.quizzes'
            ])->findOrFail($id);

            $stats = [
                'total_sub_modules' => $module->subModules->count(),
                'total_contents' => $module->subModules->sum(function($subModule) {
                    return $subModule->contents->count();
                }),
                'total_quizzes' => $module->subModules->sum(function($subModule) {
                    return $subModule->quizzes->count();
                })
            ];

            return view('admin.modules.show', compact('module', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data modul.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit modul tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $module = Module::with('course')->findOrFail($id);
            return view('admin.modules.edit', compact('module'));
        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data modul.');
        }
    }

    /**
     * Memperbarui modul tertentu dalam penyimpanan.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $module = Module::findOrFail($id);
            $oldOrder = $module->urutan;

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'urutan' => 'required|integer|min:1'
            ]);

            // Tangani perubahan urutan
            if ($oldOrder != $validated['urutan']) {
                if ($oldOrder < $validated['urutan']) {
                    // Bergerak ke bawah - geser modul antara posisi lama dan baru
                    Module::where('course_id', $module->course_id)
                          ->where('urutan', '>', $oldOrder)
                          ->where('urutan', '<=', $validated['urutan'])
                          ->decrement('urutan');
                } else {
                    // Bergerak ke atas - geser modul antara posisi baru dan lama
                    Module::where('course_id', $module->course_id)
                          ->where('urutan', '>=', $validated['urutan'])
                          ->where('urutan', '<', $oldOrder)
                          ->increment('urutan');
                }
            }

            $module->update($validated);

            Log::info('Admin updated module: ' . $module->judul);
            return redirect()->route('admin.modules.index', $module->course_id)
                           ->with('success', 'Data modul berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data modul.');
        }
    }

    /**
     * Menghapus modul tertentu dari penyimpanan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $module = Module::with(['subModules.contents', 'subModules.quizzes'])->findOrFail($id);
            $courseId = $module->course_id;
            $moduleTitle = $module->judul;

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::transaction(function() use ($module) {
                // Hapus sub-modul dan data terkait
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

                // Atur ulang urutan modul yang tersisa
                Module::where('course_id', $courseId)
                      ->where('urutan', '>', $module->urutan)
                      ->decrement('urutan');
            });

            Log::info('Admin deleted module: ' . $moduleTitle);
            return redirect()->route('admin.modules.index', $courseId)
                           ->with('success', 'Modul berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus modul.');
        }
    }

    /**
     * Metode Ajax untuk mengatur ulang urutan modul.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'module_id' => 'required|integer|exists:modules,id',
                'new_order' => 'required|integer|min:1'
            ]);

            $module = Module::findOrFail($request->module_id);
            $oldOrder = $module->urutan;
            $newOrder = $request->new_order;

            if ($oldOrder == $newOrder) {
                return response()->json(['success' => true, 'message' => 'Urutan tidak berubah']);
            }

            DB::transaction(function() use ($module, $oldOrder, $newOrder) {
                if ($oldOrder < $newOrder) {
                    // Bergerak ke bawah
                    Module::where('course_id', $module->course_id)
                          ->where('urutan', '>', $oldOrder)
                          ->where('urutan', '<=', $newOrder)
                          ->decrement('urutan');
                } else {
                    // Bergerak ke atas
                    Module::where('course_id', $module->course_id)
                          ->where('urutan', '>=', $newOrder)
                          ->where('urutan', '<', $oldOrder)
                          ->increment('urutan');
                }

                $module->update(['urutan' => $newOrder]);
            });

            Log::info('Admin reordered module: ' . $module->judul . ' from ' . $oldOrder . ' to ' . $newOrder);
            return response()->json(['success' => true, 'message' => 'Urutan modul berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@reorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }

    /**
     * Dapatkan modul untuk permintaan AJAX (misalnya, untuk dropdown).
     *
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModules($courseId)
    {
        try {
            $modules = Module::where('course_id', $courseId)
                            ->orderBy('urutan', 'asc')
                            ->get(['id', 'judul', 'urutan']);

            return response()->json($modules);
        } catch (\Exception $e) {
            Log::error('Error in AdminModuleController@getModules: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data modul'], 500);
        }
    }
}
