<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\SubModule;
use App\Models\Content;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminSubModuleController extends Controller
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
     * Menampilkan daftar sub-modul dengan paginasi untuk modul tertentu.
     *
     * @param int $moduleId
     * @return \Illuminate\View\View
     */
    public function index($moduleId)
    {
        try {
            $module = Module::with('course')->findOrFail($moduleId);
            $subModules = SubModule::where('module_id', $moduleId)
                                   ->withCount(['contents', 'quizzes'])
                                   ->orderBy('urutan', 'asc')
                                   ->paginate(15);

            return view('admin.sub-modules.index', compact('module', 'subModules'));
        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data sub-modul.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat sub-modul baru.
     *
     * @param int $moduleId
     * @return \Illuminate\View\View
     */
    public function create($moduleId)
    {
        try {
            $module = Module::with('course')->findOrFail($moduleId);
            return view('admin.sub-modules.create', compact('module'));
        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form sub-modul.');
        }
    }

    /**
     * Menyimpan sub-modul yang baru dibuat ke dalam penyimpanan.
     *
     * @param Request $request
     * @param int $moduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $moduleId)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'urutan' => 'required|integer|min:1'
            ]);

            $validated['module_id'] = $moduleId;

            // Periksa apakah nomor urutan sudah ada
            $existingSubModule = SubModule::where('module_id', $moduleId)
                                         ->where('urutan', $validated['urutan'])
                                         ->first();

            if ($existingSubModule) {
                // Geser sub-modul yang ada untuk memberi ruang
                SubModule::where('module_id', $moduleId)
                         ->where('urutan', '>=', $validated['urutan'])
                         ->increment('urutan');
            }

            SubModule::create($validated);

            Log::info('Admin created new sub-module: ' . $validated['judul'] . ' for module ID: ' . $moduleId);
            return redirect()->route('admin.sub-modules.index', $moduleId)
                           ->with('success', 'Sub-modul berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat sub-modul.');
        }
    }

    /**
     * Menampilkan sub-modul tertentu dengan konten dan kuis.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $subModule = SubModule::with([
                'module.course',
                'contents',
                'quizzes.questions.answerOptions'
            ])->findOrFail($id);

            $stats = [
                'total_contents' => $subModule->contents->count(),
                'total_quizzes' => $subModule->quizzes->count(),
                'total_questions' => $subModule->quizzes->sum(function($quiz) {
                    return $quiz->questions->count();
                })
            ];

            return view('admin.sub-modules.show', compact('subModule', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data sub-modul.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit sub-modul tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $subModule = SubModule::with('module.course')->findOrFail($id);
            return view('admin.sub-modules.edit', compact('subModule'));
        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data sub-modul.');
        }
    }

    /**
     * Memperbarui sub-modul tertentu dalam penyimpanan.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $subModule = SubModule::findOrFail($id);
            $oldOrder = $subModule->urutan;

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'urutan' => 'required|integer|min:1'
            ]);

            // Tangani perubahan urutan
            if ($oldOrder != $validated['urutan']) {
                if ($oldOrder < $validated['urutan']) {
                    // Bergerak ke bawah - geser sub-modul antara posisi lama dan baru
                    SubModule::where('module_id', $subModule->module_id)
                             ->where('urutan', '>', $oldOrder)
                             ->where('urutan', '<=', $validated['urutan'])
                             ->decrement('urutan');
                } else {
                    // Bergerak ke atas - geser sub-modul antara posisi baru dan lama
                    SubModule::where('module_id', $subModule->module_id)
                             ->where('urutan', '>=', $validated['urutan'])
                             ->where('urutan', '<', $oldOrder)
                             ->increment('urutan');
                }
            }

            $subModule->update($validated);

            Log::info('Admin updated sub-module: ' . $subModule->judul);
            return redirect()->route('admin.sub-modules.index', $subModule->module_id)
                           ->with('success', 'Data sub-modul berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data sub-modul.');
        }
    }

    /**
     * Menghapus sub-modul tertentu dari penyimpanan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $subModule = SubModule::with(['contents', 'quizzes.questions.answerOptions'])->findOrFail($id);
            $moduleId = $subModule->module_id;
            $subModuleTitle = $subModule->judul;

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::transaction(function() use ($subModule) {
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

                // Atur ulang urutan sub-modul yang tersisa
                SubModule::where('module_id', $subModule->module_id)
                         ->where('urutan', '>', $subModule->urutan)
                         ->decrement('urutan');
            });

            Log::info('Admin deleted sub-module: ' . $subModuleTitle);
            return redirect()->route('admin.sub-modules.index', $moduleId)
                           ->with('success', 'Sub-modul berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus sub-modul.');
        }
    }

    /**
     * Metode Ajax untuk mengatur ulang urutan sub-modul.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'sub_module_id' => 'required|integer|exists:sub_modules,id',
                'new_order' => 'required|integer|min:1'
            ]);

            $subModule = SubModule::findOrFail($request->sub_module_id);
            $oldOrder = $subModule->urutan;
            $newOrder = $request->new_order;

            if ($oldOrder == $newOrder) {
                return response()->json(['success' => true, 'message' => 'Urutan tidak berubah']);
            }

            DB::transaction(function() use ($subModule, $oldOrder, $newOrder) {
                if ($oldOrder < $newOrder) {
                    // Bergerak ke bawah
                    SubModule::where('module_id', $subModule->module_id)
                             ->where('urutan', '>', $oldOrder)
                             ->where('urutan', '<=', $newOrder)
                             ->decrement('urutan');
                } else {
                    // Bergerak ke atas
                    SubModule::where('module_id', $subModule->module_id)
                             ->where('urutan', '>=', $newOrder)
                             ->where('urutan', '<', $oldOrder)
                             ->increment('urutan');
                }

                $subModule->update(['urutan' => $newOrder]);
            });

            Log::info('Admin reordered sub-module: ' . $subModule->judul . ' from ' . $oldOrder . ' to ' . $newOrder);
            return response()->json(['success' => true, 'message' => 'Urutan sub-modul berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@reorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }

    /**
     * Dapatkan sub-modul untuk permintaan AJAX (misalnya, untuk dropdown).
     *
     * @param int $moduleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubModules($moduleId)
    {
        try {
            $subModules = SubModule::where('module_id', $moduleId)
                                   ->orderBy('urutan', 'asc')
                                   ->get(['id', 'judul', 'urutan']);

            return response()->json($subModules);
        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@getSubModules: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data sub-modul'], 500);
        }
    }

    /**
     * Pengaturan ulang massal sub-modul menggunakan drag and drop.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkReorder(Request $request)
    {
        try {
            $request->validate([
                'sub_modules' => 'required|array',
                'sub_modules.*.id' => 'required|integer|exists:sub_modules,id',
                'sub_modules.*.urutan' => 'required|integer|min:1'
            ]);

            DB::transaction(function() use ($request) {
                foreach ($request->sub_modules as $item) {
                    SubModule::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
                }
            });

            Log::info('Admin bulk reordered sub-modules for module ID: ' . $request->module_id);
            return response()->json(['success' => true, 'message' => 'Urutan sub-modul berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminSubModuleController@bulkReorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }
}
