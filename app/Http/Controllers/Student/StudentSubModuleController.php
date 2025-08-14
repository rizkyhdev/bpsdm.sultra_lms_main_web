<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SubModule;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentSubModuleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Display the specified sub-module.
     */
    public function show(SubModule $subModule): View
    {
        $user = Auth::user();
        
        // Check if user is enrolled in the course
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses sub-modul.');
        }

        // Check if previous sub-modules are completed
        $isAccessible = $this->isSubModuleAccessible($user, $subModule);
        
        if (!$isAccessible) {
            abort(403, 'Anda harus menyelesaikan sub-modul sebelumnya terlebih dahulu.');
        }

        // Get sub-module with contents and user progress
        $subModule->load(['contents' => function ($query) {
            $query->orderBy('urutan');
        }, 'contents.userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);

        // Get user progress for this sub-module
        $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
        
        if (!$progress) {
            // Initialize progress if not exists
            $progress = $subModule->userProgress()->create([
                'user_id' => $user->id,
                'is_completed' => false,
                'progress_percentage' => 0,
                'started_at' => now()
            ]);
        }

        // Calculate content progress
        $totalContents = $subModule->contents->count();
        $completedContents = 0;
        $contentProgress = 0;

        foreach ($subModule->contents as $content) {
            $contentUserProgress = $content->userProgress()->where('user_id', $user->id)->first();
            
            if ($contentUserProgress && $contentUserProgress->is_completed) {
                $completedContents++;
            }
            
            if ($contentUserProgress) {
                $contentProgress += $contentUserProgress->progress_percentage;
            }
        }

        $overallProgress = $totalContents > 0 ? round($contentProgress / $totalContents, 2) : 0;
        $completionPercentage = $totalContents > 0 ? round(($completedContents / $totalContents) * 100, 2) : 0;

        // Get next and previous sub-modules
        $nextSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '>', $subModule->urutan)
            ->orderBy('urutan')
            ->first();

        $previousSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '<', $subModule->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Get module and course info
        $module = $subModule->module;
        $course = $module->course;

        // Check if sub-module can be marked as completed
        $canMarkComplete = $totalContents > 0 && $completedContents >= $totalContents;

        return view('student.sub-modules.show', compact(
            'subModule',
            'module',
            'course',
            'enrollment',
            'progress',
            'contents',
            'totalContents',
            'completedContents',
            'overallProgress',
            'completionPercentage',
            'nextSubModule',
            'previousSubModule',
            'canMarkComplete'
        ));
    }

    /**
     * Mark a sub-module as completed.
     */
    public function markComplete(SubModule $subModule): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        // Check if all contents are completed
        $contents = $subModule->contents;
        $totalContents = $contents->count();
        $completedContents = 0;

        foreach ($contents as $content) {
            $contentProgress = $content->userProgress()->where('user_id', $user->id)->first();
            
            if ($contentProgress && $contentProgress->is_completed) {
                $completedContents++;
            }
        }

        if ($totalContents > 0 && $completedContents < $totalContents) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus menyelesaikan semua konten terlebih dahulu.',
                'completed' => $completedContents,
                'total' => $totalContents
            ], 400);
        }

        try {
            // Update sub-module progress
            $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
            
            if ($progress) {
                $progress->update([
                    'is_completed' => true,
                    'progress_percentage' => 100,
                    'completed_at' => now()
                ]);
            }

            // Check if module is completed
            $this->checkModuleCompletion($user, $subModule->module_id);

            return response()->json([
                'success' => true,
                'message' => 'Sub-modul berhasil diselesaikan!',
                'sub_module_id' => $subModule->id,
                'completion_percentage' => 100
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan sub-modul.'
            ], 500);
        }
    }

    /**
     * Get sub-module progress details.
     */
    public function getProgress(SubModule $subModule): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $contents = $subModule->contents()->with(['userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->orderBy('urutan')->get();

        $progressData = [];
        $totalContents = $contents->count();
        $completedContents = 0;
        $totalProgress = 0;

        foreach ($contents as $content) {
            $progress = $content->userProgress()->where('user_id', $user->id)->first();
            $isCompleted = $progress ? $progress->is_completed : false;
            $progressPercentage = $progress ? $progress->progress_percentage : 0;
            
            if ($isCompleted) {
                $completedContents++;
            }
            
            $totalProgress += $progressPercentage;

            $progressData[] = [
                'content_id' => $content->id,
                'judul' => $content->judul,
                'jenis' => $content->jenis,
                'urutan' => $content->urutan,
                'is_completed' => $isCompleted,
                'progress_percentage' => $progressPercentage,
                'started_at' => $progress ? $progress->started_at : null,
                'completed_at' => $progress ? $progress->completed_at : null
            ];
        }

        $overallProgress = $totalContents > 0 ? round($totalProgress / $totalContents, 2) : 0;
        $completionPercentage = $totalContents > 0 ? round(($completedContents / $totalContents) * 100, 2) : 0;

        // Get sub-module progress
        $subModuleProgress = $subModule->userProgress()->where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'sub_module' => $subModule,
                'contents' => $progressData,
                'overall_progress' => $overallProgress,
                'completion_percentage' => $completionPercentage,
                'completed_contents' => $completedContents,
                'total_contents' => $totalContents,
                'sub_module_progress' => $subModuleProgress ? [
                    'is_completed' => $subModuleProgress->is_completed,
                    'progress_percentage' => $subModuleProgress->progress_percentage,
                    'started_at' => $subModuleProgress->started_at,
                    'completed_at' => $subModuleProgress->completed_at
                ] : null
            ]
        ]);
    }

    /**
     * Get sub-module navigation (previous/next sub-modules).
     */
    public function getNavigation(SubModule $subModule): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $nextSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '>', $subModule->urutan)
            ->orderBy('urutan')
            ->first();

        $previousSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '<', $subModule->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Check accessibility
        $isNextAccessible = false;
        if ($nextSubModule) {
            $isNextAccessible = $this->isSubModuleAccessible($user, $nextSubModule);
        }

        $isPreviousAccessible = $previousSubModule !== null;

        return response()->json([
            'success' => true,
            'data' => [
                'current_sub_module' => [
                    'id' => $subModule->id,
                    'judul' => $subModule->judul,
                    'urutan' => $subModule->urutan
                ],
                'next_sub_module' => $nextSubModule ? [
                    'id' => $nextSubModule->id,
                    'judul' => $nextSubModule->judul,
                    'urutan' => $nextSubModule->urutan,
                    'is_accessible' => $isNextAccessible
                ] : null,
                'previous_sub_module' => $previousSubModule ? [
                    'id' => $previousSubModule->id,
                    'judul' => $previousSubModule->judul,
                    'urutan' => $previousSubModule->urutan,
                    'is_accessible' => $isPreviousAccessible
                ] : null
            ]
        ]);
    }

    /**
     * Update sub-module progress.
     */
    public function updateProgress(SubModule $subModule, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $request->validate([
            'progress_percentage' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
            
            if (!$progress) {
                $progress = $subModule->userProgress()->create([
                    'user_id' => $user->id,
                    'is_completed' => false,
                    'progress_percentage' => 0,
                    'started_at' => now()
                ]);
            }

            $progress->update([
                'progress_percentage' => $request->progress_percentage,
                'is_completed' => $request->progress_percentage >= 100
            ]);

            if ($request->progress_percentage >= 100) {
                $progress->update(['completed_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress berhasil diperbarui.',
                'progress_percentage' => $request->progress_percentage,
                'is_completed' => $request->progress_percentage >= 100
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui progress.'
            ], 500);
        }
    }

    /**
     * Check if a sub-module is accessible to the user.
     */
    private function isSubModuleAccessible($user, SubModule $subModule): bool
    {
        if ($subModule->urutan === 1) {
            return true;
        }

        $previousSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '<', $subModule->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousSubModule) {
            return true;
        }

        $previousProgress = $previousSubModule->userProgress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->first();

        return $previousProgress !== null;
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