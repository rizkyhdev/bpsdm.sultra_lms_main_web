<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StudentContentController extends Controller
{
    /**
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Menampilkan konten tertentu.
     */
    public function show(Content $content): View
    {
        $user = Auth::user();
        
        // Periksa apakah user sudah terdaftar dalam kursus
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses konten.');
        }

        // Periksa apakah konten sebelumnya sudah selesai
        $isAccessible = $this->isContentAccessible($user, $content);
        
        if (!$isAccessible) {
            abort(403, 'Anda harus menyelesaikan konten sebelumnya terlebih dahulu.');
        }

        // Mendapatkan progress user untuk konten ini
        $progress = $content->userProgress()->where('user_id', $user->id)->first();
        
        if (!$progress) {
            // Inisialisasi progress jika tidak ada
            $progress = $content->userProgress()->create([
                'user_id' => $user->id,
                'is_completed' => false,
                'progress_percentage' => 0,
                'started_at' => now()
            ]);
        }

        // Mendapatkan konten dengan info sub-modul, modul, dan kursus
        $content->load(['subModule.module.course']);

        // Mendapatkan konten berikutnya dan sebelumnya
        $nextContent = Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '>', $content->urutan)
            ->orderBy('urutan')
            ->first();

        $previousContent = Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '<', $content->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Mendapatkan progress sub-modul
        $subModuleProgress = $content->subModule->userProgress()->where('user_id', $user->id)->first();

        // Periksa apakah konten dapat ditandai sebagai selesai
        $canMarkComplete = $progress->progress_percentage >= 100;

        return view('student.contents.show', compact(
            'content',
            'enrollment',
            'progress',
            'nextContent',
            'previousContent',
            'subModuleProgress',
            'canMarkComplete'
        ));
    }

    /**
     * Track content viewing progress.
     */
    public function trackProgress(Content $content, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $request->validate([
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'time_spent' => 'nullable|numeric|min:0',
            'current_position' => 'nullable|numeric|min:0'
        ]);

        try {
            $progress = $content->userProgress()->where('user_id', $user->id)->first();
            
            if (!$progress) {
                $progress = $content->userProgress()->create([
                    'user_id' => $user->id,
                    'is_completed' => false,
                    'progress_percentage' => 0,
                    'started_at' => now()
                ]);
            }

            $isCompleted = $request->progress_percentage >= 100;
            
            $progress->update([
                'progress_percentage' => $request->progress_percentage,
                'is_completed' => $isCompleted,
                'time_spent' => $request->time_spent ?? $progress->time_spent,
                'current_position' => $request->current_position ?? $progress->current_position
            ]);

            if ($isCompleted) {
                $progress->update(['completed_at' => now()]);
            }

            // Check if sub-module is completed
            $this->checkSubModuleCompletion($user, $content->sub_module_id);

            return response()->json([
                'success' => true,
                'message' => 'Progress berhasil diperbarui.',
                'progress_percentage' => $request->progress_percentage,
                'is_completed' => $isCompleted,
                'time_spent' => $request->time_spent ?? $progress->time_spent
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui progress.'
            ], 500);
        }
    }

    /**
     * Mark content as completed.
     */
    public function markComplete(Content $content): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        try {
            $progress = $content->userProgress()->where('user_id', $user->id)->first();
            
            if (!$progress) {
                $progress = $content->userProgress()->create([
                    'user_id' => $user->id,
                    'is_completed' => false,
                    'progress_percentage' => 0,
                    'started_at' => now()
                ]);
            }

            $progress->update([
                'is_completed' => true,
                'progress_percentage' => 100,
                'completed_at' => now()
            ]);

            // Check if sub-module is completed
            $this->checkSubModuleCompletion($user, $content->sub_module_id);

            return response()->json([
                'success' => true,
                'message' => 'Konten berhasil diselesaikan!',
                'content_id' => $content->id,
                'completion_percentage' => 100
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan konten.'
            ], 500);
        }
    }

    /**
     * Download content file (for PDF, documents, etc.).
     */
    public function download(Content $content): Response
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengunduh konten.');
        }

        // Check if content has a file
        if (!$content->file_path || !Storage::exists($content->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Track download progress
        $progress = $content->userProgress()->where('user_id', $user->id)->first();
        
        if (!$progress) {
            $progress = $content->userProgress()->create([
                'user_id' => $user->id,
                'is_completed' => false,
                'progress_percentage' => 0,
                'started_at' => now()
            ]);
        }

        // Update progress to indicate content was accessed
        if ($progress->progress_percentage < 25) {
            $progress->update(['progress_percentage' => 25]);
        }

        // Return file download
        return Storage::download($content->file_path, $content->judul . '.' . pathinfo($content->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Stream video content.
     */
    public function streamVideo(Content $content): Response
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses video.');
        }

        // Check if content is a video
        if ($content->jenis !== 'video' || !$content->file_path || !Storage::exists($content->file_path)) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Track video access
        $progress = $content->userProgress()->where('user_id', $user->id)->first();
        
        if (!$progress) {
            $progress = $content->userProgress()->create([
                'user_id' => $user->id,
                'is_completed' => false,
                'progress_percentage' => 0,
                'started_at' => now()
            ]);
        }

        // Update progress to indicate video was accessed
        if ($progress->progress_percentage < 10) {
            $progress->update(['progress_percentage' => 10]);
        }

        // Get file info
        $filePath = Storage::path($content->file_path);
        $fileSize = Storage::size($content->file_path);
        $fileName = basename($content->file_path);

        // Check if range header is present for video streaming
        $range = request()->header('Range');
        
        if ($range) {
            $ranges = array_map('intval', explode('-', substr($range, 6)));
            $offset = $ranges[0];
            $length = $ranges[1] - $ranges[0] + 1;
            
            $headers = [
                'Content-Range' => 'bytes ' . $offset . '-' . ($offset + $length - 1) . '/' . $fileSize,
                'Accept-Ranges' => 'bytes',
                'Content-Length' => $length,
                'Content-Type' => 'video/mp4',
            ];
            
            return response()->file($filePath, $headers);
        }

        // Return full video file
        return response()->file($filePath, [
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $fileSize,
            'Content-Type' => 'video/mp4',
        ]);
    }

    /**
     * Get content progress details.
     */
    public function getProgress(Content $content): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $progress = $content->userProgress()->where('user_id', $user->id)->first();

        if (!$progress) {
            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $content,
                    'progress' => null,
                    'is_completed' => false,
                    'progress_percentage' => 0
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'content' => $content,
                'progress' => [
                    'is_completed' => $progress->is_completed,
                    'progress_percentage' => $progress->progress_percentage,
                    'time_spent' => $progress->time_spent,
                    'current_position' => $progress->current_position,
                    'started_at' => $progress->started_at,
                    'completed_at' => $progress->completed_at
                ],
                'is_completed' => $progress->is_completed,
                'progress_percentage' => $progress->progress_percentage
            ]
        ]);
    }

    /**
     * Get content navigation (previous/next contents).
     */
    public function getNavigation(Content $content): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $content->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $nextContent = Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '>', $content->urutan)
            ->orderBy('urutan')
            ->first();

        $previousContent = Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '<', $content->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Check accessibility
        $isNextAccessible = false;
        if ($nextContent) {
            $isNextAccessible = $this->isContentAccessible($user, $nextContent);
        }

        $isPreviousAccessible = $previousContent !== null;

        return response()->json([
            'success' => true,
            'data' => [
                'current_content' => [
                    'id' => $content->id,
                    'judul' => $content->judul,
                    'jenis' => $content->jenis,
                    'urutan' => $content->urutan
                ],
                'next_content' => $nextContent ? [
                    'id' => $nextContent->id,
                    'judul' => $nextContent->judul,
                    'jenis' => $nextContent->jenis,
                    'urutan' => $nextContent->urutan,
                    'is_accessible' => $isNextAccessible
                ] : null,
                'previous_content' => $previousContent ? [
                    'id' => $previousContent->id,
                    'judul' => $previousContent->judul,
                    'jenis' => $previousContent->jenis,
                    'urutan' => $previousContent->urutan,
                    'is_accessible' => $isPreviousAccessible
                ] : null
            ]
        ]);
    }

    /**
     * Check if a content is accessible to the user.
     */
    private function isContentAccessible($user, Content $content): bool
    {
        if ($content->urutan === 1) {
            return true;
        }

        $previousContent = Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '<', $content->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousContent) {
            return true;
        }

        $previousProgress = $previousContent->userProgress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->first();

        return $previousProgress !== null;
    }

    /**
     * Check if sub-module is completed and update progress.
     */
    private function checkSubModuleCompletion($user, $subModuleId): void
    {
        $subModule = \App\Models\SubModule::find($subModuleId);
        
        if (!$subModule) {
            return;
        }

        $totalContents = $subModule->contents()->count();
        $completedContents = $subModule->contents()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        if ($totalContents > 0 && $completedContents >= $totalContents) {
            // Sub-module is completed, check if module is completed
            $this->checkModuleCompletion($user, $subModule->module_id);
        }
    }

    /**
     * Check if module is completed and update progress.
     */
    private function checkModuleCompletion($user, $moduleId): void
    {
        $module = \App\Models\Module::find($moduleId);
        
        if (!$module) {
            return;
        }

        $totalSubModules = $module->subModules()->count();
        $completedSubModules = $module->subModules()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules) {
            // Module is completed, check if course is completed
            $this->checkCourseCompletion($user, $module->course_id);
        }
    }

    /**
     * Check if course is completed and update enrollment status.
     */
    private function checkCourseCompletion($user, $courseId): void
    {
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return;
        }

        $course = $enrollment->course;
        $totalModules = $course->modules()->count();
        $completedModules = 0;

        foreach ($course->modules as $module) {
            $totalSubModules = $module->subModules()->count();
            $completedSubModules = $module->subModules()
                ->whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->count();

            if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules) {
                $completedModules++;
            }
        }

        if ($completedModules >= $totalModules) {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Create JP record for course completion
            $this->createJpRecord($user, $course);
        }
    }

    /**
     * Create JP record when course is completed.
     */
    private function createJpRecord($user, $course): void
    {
        // Check if JP record already exists for this course
        $existingJpRecord = $user->jpRecords()
            ->where('course_id', $course->id)
            ->first();

        if (!$existingJpRecord) {
            $user->jpRecords()->create([
                'course_id' => $course->id,
                'jp_value' => $course->jp_value,
                'earned_at' => now()
            ]);
        }
    }
} 