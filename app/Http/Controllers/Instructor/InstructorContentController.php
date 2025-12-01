<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreContentRequest;
use App\Http\Requests\Instructor\UpdateContentRequest;
use App\Models\Content;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Kelas InstructorContentController
 * Mengelola konten dengan pengelolaan file di bawah sub-modul milik instruktur.
 */
class InstructorContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user && ($user->role === 'instructor' || $user->role === 'admin')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        });
    }

    /**
     * Daftar konten di bawah sub-modul.
     * @param Request $request
     * @param int $subModuleId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $subModuleId)
    {
        $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
        $this->authorize('view', $subModule);

        $q = trim($request->get('q'));
        $perPage = (int) $request->get('per_page', 15);
        $tipe = $request->get('tipe');

        $contents = Content::query()
            ->where('sub_module_id', $subModule->id)
            ->when($q, function ($query) use ($q) {
                $query->where('judul', 'like', "%$q%");
            })
            ->when($tipe, function ($query) use ($tipe) {
                $query->where('tipe', $tipe);
            })
            ->orderBy('urutan')
            ->paginate($perPage)
            ->appends($request->query());

        $filters = [
            'tipe' => $tipe,
        ];

        return view('instructor.contents.index', compact('subModule', 'contents', 'filters'));
    }

    /**
     * Tampilkan form pembuatan.
     * @param int $subModuleId
     * @return \Illuminate\Http\Response
     */
    public function create($subModuleId)
    {
        $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
        $this->authorize('update', $subModule);
        return view('instructor.contents.create', compact('subModule'));
    }

    /**
     * Simpan konten, termasuk upload file.
     * @param StoreContentRequest $request
     * @param int $subModuleId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(StoreContentRequest $request, $subModuleId)
    {
        $subModule = SubModule::with('module.course')->findOrFail($subModuleId);
        $this->authorize('update', $subModule);

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $content = new Content();
            $content->judul = $data['judul'];
            $content->tipe = $data['tipe'];
            $content->urutan = $data['urutan'];
            $content->sub_module_id = $subModule->id;
            $content->html_content = $data['html_content'] ?? null;
            $content->external_url = $data['external_url'] ?? null;
            $content->youtube_url = $data['youtube_url'] ?? null;
            $content->required_duration = $data['required_duration'] ?? null;

            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $path = $file->store('contents/' . date('Y/m/d'), 'public');
                $content->file_path = $path;
            }

            $content->save();
            DB::commit();
            Log::info('Content created', ['content_id' => $content->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Content created successfully.', 'content' => $content]);
            }
            
            return redirect()->route('instructor.contents.index', $subModule->id)->with('success', 'Content created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create content', ['sub_module_id' => $subModule->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create content.', 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            
            return redirect()->back()->withInput()->with('error', 'Failed to create content.');
        }
    }

    /**
     * Tampilkan konten.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $content = Content::with('subModule.module.course')->findOrFail($id);
        $this->authorize('view', $content);
        return view('instructor.contents.show', compact('content'));
    }

    /**
     * Get content data as JSON (for modal editing)
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($id)
    {
        $content = Content::findOrFail($id);
        $this->authorize('view', $content);
        return response()->json($content);
    }

    /**
     * Tampilkan form edit.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $content = Content::with('subModule.module.course')->findOrFail($id);
        $this->authorize('update', $content);
        return view('instructor.contents.edit', compact('content'));
    }

    /**
     * Perbarui konten dan opsional ganti file.
     * @param UpdateContentRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateContentRequest $request, $id)
    {
        $content = Content::with('subModule.module.course')->findOrFail($id);
        $this->authorize('update', $content);

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $content->judul = $data['judul'];
            $content->tipe = $data['tipe'];
            $content->urutan = $data['urutan'];
            $content->html_content = $data['html_content'] ?? null;
            $content->external_url = $data['external_url'] ?? null;
            $content->youtube_url = $data['youtube_url'] ?? null;
            $content->required_duration = $data['required_duration'] ?? null;

            if ($request->hasFile('file_path')) {
                if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                    Storage::disk('public')->delete($content->file_path);
                }
                $file = $request->file('file_path');
                $path = $file->store('contents/' . date('Y/m/d'), 'public');
                $content->file_path = $path;
            }

            $content->save();
            DB::commit();
            Log::info('Content updated', ['content_id' => $content->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Content updated successfully.', 'content' => $content]);
            }
            
            return redirect()->route('instructor.contents.show', $content->id)->with('success', 'Content updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update content', ['content_id' => $content->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update content.', 'errors' => ['general' => [$e->getMessage()]]], 422);
            }
            
            return redirect()->back()->withInput()->with('error', 'Failed to update content.');
        }
    }

    /**
     * Hapus konten beserta filenya.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $content = Content::with('subModule.module.course')->findOrFail($id);
        $this->authorize('delete', $content);

        DB::beginTransaction();
        try {
            if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                Storage::disk('public')->delete($content->file_path);
            }
            $content->delete();
            DB::commit();
            Log::info('Content deleted', ['content_id' => $content->id, 'instructor_id' => Auth::id()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Content deleted successfully.']);
            }
            
            return redirect()->back()->with('success', 'Content deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete content', ['content_id' => $content->id, 'error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete content.'], 422);
            }
            
            return redirect()->back()->with('error', 'Failed to delete content.');
        }
    }

    /**
     * Unduh file konten jika ada.
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        $content = Content::with('subModule.module.course')->findOrFail($id);
        $this->authorize('view', $content);

        if (!$content->file_path || !Storage::disk('public')->exists($content->file_path)) {
            return redirect()->back()->with('error', 'File not available.');
        }

        return Storage::disk('public')->download($content->file_path);
    }

    /**
     * Mengubah urutan konten.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:contents,id',
            'items.*.urutan' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $content = Content::with('subModule.module.course')->find($item['id']);
                $this->authorize('update', $content);
                $content->urutan = $item['urutan'];
                $content->save();
            }
            DB::commit();
            return response()->json(['message' => 'Order updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }

    /**
     * Serve PDF file for viewing (with proper authorization and headers).
     * @param Content $content
     * @return BinaryFileResponse
     */
    public function viewPdf(Content $content): BinaryFileResponse
    {
        // Check authorization
        $this->authorize('view', $content);

        // Check if content is a PDF and has a file
        if ($content->tipe !== 'pdf' || !$content->file_path) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        // Check if file exists in public disk (where content files are stored)
        if (!Storage::disk('public')->exists($content->file_path)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        // Get file path and size
        $filePath = Storage::disk('public')->path($content->file_path);
        $fileSize = Storage::disk('public')->size($content->file_path);
        $safeFileName = str_replace(['"', "\n", "\r"], ['_', ' ', ' '], $content->judul . '.pdf');

        // Prepare headers following LMS best practices
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $safeFileName . '"',
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes', // Support range requests for better performance with pdf.js
            'Cache-Control' => 'private, max-age=3600', // Cache for 1 hour, but private (requires auth)
            'X-Content-Type-Options' => 'nosniff', // Security header to prevent MIME type sniffing
        ];

        // Support range requests for large PDFs (improves performance with pdf.js)
        $range = request()->header('Range');
        if ($range) {
            // Parse range header (e.g., "bytes=0-1023")
            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $start = (int) $matches[1];
                $end = $matches[2] ? (int) $matches[2] : $fileSize - 1;
                $length = $end - $start + 1;

                $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $fileSize);
                $headers['Content-Length'] = $length;

                // Return partial content response
                $response = response()->file($filePath, $headers);
                $response->setStatusCode(206); // Partial Content
                return $response;
            }
        }

        // Return full file response
        return response()->file($filePath, $headers);
    }
}


