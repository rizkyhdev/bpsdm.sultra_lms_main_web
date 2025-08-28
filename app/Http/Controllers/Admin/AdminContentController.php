<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubModule;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminContentController extends Controller
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
     * Menampilkan daftar konten dengan paginasi untuk sub-modul tertentu.
     *
     * @param int $subModuleId
     * @return \Illuminate\View\View
     */
    public function index($subModuleId)
    {
        try {
            $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
            $contents = Content::where('sub_module_id', $subModuleId)
                               ->orderBy('urutan', 'asc')
                               ->paginate(15);

            return view('admin.contents.index', compact('subModule', 'contents'));
        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data konten.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat konten baru.
     *
     * @param int $subModuleId
     * @return \Illuminate\View\View
     */
    public function create($subModuleId)
    {
        try {
            $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
            return view('admin.contents.create', compact('subModule'));
        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form konten.');
        }
    }

    /**
     * Menyimpan konten yang baru dibuat ke dalam penyimpanan.
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
                'tipe' => 'required|in:text,pdf,video,audio,image',
                'file_path' => 'required_if:tipe,pdf,video,audio,image|file|max:102400', // 100MB max
                'urutan' => 'required|integer|min:1',
                'deskripsi' => 'nullable|string'
            ]);

            $validated['sub_module_id'] = $subModuleId;

            // Tangani upload file untuk konten non-teks
            if ($request->hasFile('file_path') && $validated['tipe'] !== 'text') {
                $file = $request->file('file_path');
                $fileName = time() . '_' . Str::slug($validated['judul']) . '.' . $file->getClientOriginalExtension();
                
                // Simpan file di direktori yang sesuai
                $filePath = $file->storeAs('contents/' . $subModuleId, $fileName, 'public');
                $validated['file_path'] = $filePath;
            } else {
                $validated['file_path'] = null;
            }

            // Periksa apakah nomor urutan sudah ada
            $existingContent = Content::where('sub_module_id', $subModuleId)
                                     ->where('urutan', $validated['urutan'])
                                     ->first();

            if ($existingContent) {
                // Geser konten yang ada untuk memberi ruang
                Content::where('sub_module_id', $subModuleId)
                       ->where('urutan', '>=', $validated['urutan'])
                       ->increment('urutan');
            }

            Content::create($validated);

            Log::info('Admin created new content: ' . $validated['judul'] . ' for sub-module ID: ' . $subModuleId);
            return redirect()->route('admin.contents.index', $subModuleId)
                           ->with('success', 'Konten berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat konten.');
        }
    }

    /**
     * Menampilkan detail konten tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $content = Content::with('subModule.module.course')->findOrFail($id);
            return view('admin.contents.show', compact('content'));
        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data konten.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit konten tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $content = Content::with('subModule.module.course')->findOrFail($id);
            return view('admin.contents.edit', compact('content'));
        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data konten.');
        }
    }

    /**
     * Memperbarui konten tertentu dalam penyimpanan.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $content = Content::findOrFail($id);
            $oldOrder = $content->urutan;
            $oldFilePath = $content->file_path;

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'tipe' => 'required|in:text,pdf,video,audio,image',
                'file_path' => 'nullable|file|max:102400', // 100MB max
                'urutan' => 'required|integer|min:1',
                'deskripsi' => 'nullable|string'
            ]);

            // Tangani upload file untuk konten non-teks
            if ($request->hasFile('file_path') && $validated['tipe'] !== 'text') {
                // Hapus file lama jika ada
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $file = $request->file('file_path');
                $fileName = time() . '_' . Str::slug($validated['judul']) . '.' . $file->getClientOriginalExtension();
                
                // Simpan file di direktori yang sesuai
                $filePath = $file->storeAs('contents/' . $content->sub_module_id, $fileName, 'public');
                $validated['file_path'] = $filePath;
            } elseif ($validated['tipe'] === 'text') {
                $validated['file_path'] = null;
                // Hapus file lama jika jenis konten berubah menjadi teks
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            } else {
                // Pertahankan path file yang ada jika tidak ada file baru yang diupload
                unset($validated['file_path']);
            }

            // Tangani perubahan urutan
            if ($oldOrder != $validated['urutan']) {
                if ($oldOrder < $validated['urutan']) {
                    // Bergerak ke bawah - geser konten antara posisi lama dan baru
                    Content::where('sub_module_id', $content->sub_module_id)
                           ->where('urutan', '>', $oldOrder)
                           ->where('urutan', '<=', $validated['urutan'])
                           ->decrement('urutan');
                } else {
                    // Bergerak ke atas - geser konten antara posisi baru dan lama
                    Content::where('sub_module_id', $content->sub_module_id)
                           ->where('urutan', '>=', $validated['urutan'])
                           ->where('urutan', '<', $oldOrder)
                           ->increment('urutan');
                }
            }

            $content->update($validated);

            Log::info('Admin updated content: ' . $content->judul);
            return redirect()->route('admin.contents.index', $content->sub_module_id)
                           ->with('success', 'Data konten berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data konten.');
        }
    }

    /**
     * Menghapus konten tertentu dari penyimpanan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $content = Content::findOrFail($id);
            $subModuleId = $content->sub_module_id;
            $contentTitle = $content->judul;

            // Hapus file terkait jika ada
            if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                Storage::disk('public')->delete($content->file_path);
            }

            $content->delete();

            // Atur ulang urutan konten yang tersisa
            Content::where('sub_module_id', $subModuleId)
                   ->where('urutan', '>', $content->urutan)
                   ->decrement('urutan');

            Log::info('Admin deleted content: ' . $contentTitle);
            return redirect()->route('admin.contents.index', $subModuleId)
                           ->with('success', 'Konten berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus konten.');
        }
    }

    /**
     * Mengunduh file konten.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        try {
            $content = Content::findOrFail($id);

            if (!$content->file_path || !Storage::disk('public')->exists($content->file_path)) {
                return back()->with('error', 'File tidak ditemukan.');
            }

            $fileName = basename($content->file_path);
            
            Log::info('Admin downloaded content file: ' . $fileName);
            return Storage::disk('public')->download($content->file_path, $fileName);

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@download: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file.');
        }
    }

    /**
     * Metode Ajax untuk mengatur ulang urutan konten.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'content_id' => 'required|integer|exists:contents,id',
                'new_order' => 'required|integer|min:1'
            ]);

            $content = Content::findOrFail($request->content_id);
            $oldOrder = $content->urutan;
            $newOrder = $request->new_order;

            if ($oldOrder == $newOrder) {
                return response()->json(['success' => true, 'message' => 'Urutan tidak berubah']);
            }

            DB::transaction(function() use ($content, $oldOrder, $newOrder) {
                if ($oldOrder < $newOrder) {
                    // Bergerak ke bawah
                    Content::where('sub_module_id', $content->sub_module_id)
                           ->where('urutan', '>', $oldOrder)
                           ->where('urutan', '<=', $newOrder)
                           ->decrement('urutan');
                } else {
                    // Bergerak ke atas
                    Content::where('sub_module_id', $content->sub_module_id)
                           ->where('urutan', '>=', $newOrder)
                           ->where('urutan', '<', $oldOrder)
                           ->increment('urutan');
                }

                $content->update(['urutan' => $newOrder]);
            });

            Log::info('Admin reordered content: ' . $content->judul . ' from ' . $oldOrder . ' to ' . $newOrder);
            return response()->json(['success' => true, 'message' => 'Urutan konten berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@reorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }

    /**
     * Pengaturan ulang massal konten menggunakan drag and drop.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkReorder(Request $request)
    {
        try {
            $request->validate([
                'contents' => 'required|array',
                'contents.*.id' => 'required|integer|exists:contents,id',
                'contents.*.urutan' => 'required|integer|min:1'
            ]);

            DB::transaction(function() use ($request) {
                foreach ($request->contents as $item) {
                    Content::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
                }
            });

            Log::info('Admin bulk reordered contents for sub-module ID: ' . $request->sub_module_id);
            return response()->json(['success' => true, 'message' => 'Urutan konten berhasil diubah']);

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@bulkReorder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah urutan'], 500);
        }
    }

    /**
     * Pratinjau konten (untuk konten teks atau buat pratinjau untuk file).
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function preview($id)
    {
        try {
            $content = Content::with('subModule.module.course')->findOrFail($id);
            
            // Untuk konten teks, tampilkan apa adanya
            if ($content->tipe === 'text') {
                return view('admin.contents.preview', compact('content'));
            }

            // Untuk konten file, periksa apakah file ada
            if (!$content->file_path || !Storage::disk('public')->exists($content->file_path)) {
                return back()->with('error', 'File tidak ditemukan.');
            }

            // Buat pratinjau berdasarkan jenis konten
            $previewData = $this->generatePreview($content);
            
            return view('admin.contents.preview', compact('content', 'previewData'));

        } catch (\Exception $e) {
            Log::error('Error in AdminContentController@preview: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat preview konten.');
        }
    }

    /**
     * Membuat pratinjau untuk berbagai jenis konten.
     *
     * @param Content $content
     * @return array
     */
    private function generatePreview(Content $content)
    {
        $previewData = [];

        switch ($content->tipe) {
            case 'pdf':
                $previewData['type'] = 'pdf';
                $previewData['url'] = Storage::disk('public')->url($content->file_path);
                break;
                
            case 'image':
                $previewData['type'] = 'image';
                $previewData['url'] = Storage::disk('public')->url($content->file_path);
                break;
                
            case 'video':
                $previewData['type'] = 'video';
                $previewData['url'] = Storage::disk('public')->url($content->file_path);
                $previewData['mime_type'] = Storage::disk('public')->mimeType($content->file_path);
                break;
                
            case 'audio':
                $previewData['type'] = 'audio';
                $previewData['url'] = Storage::disk('public')->url($content->file_path);
                $previewData['mime_type'] = Storage::disk('public')->mimeType($content->file_path);
                break;
                
            default:
                $previewData['type'] = 'unknown';
                break;
        }

        return $previewData;
    }
}
