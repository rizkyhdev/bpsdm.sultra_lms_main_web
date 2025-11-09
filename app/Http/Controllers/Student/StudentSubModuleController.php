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
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Menampilkan sub-modul tertentu.
     */
    public function show(SubModule $subModule): View
    {
        $user = Auth::user();
        
        // Periksa apakah user sudah terdaftar dalam kursus
        // Accept multiple valid enrollment statuses: enrolled, in_progress, completed, or active
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses sub-modul.');
        }

        // Periksa apakah sub-modul sebelumnya sudah selesai
        $isAccessible = $this->isSubModuleAccessible($user, $subModule);
        
        if (!$isAccessible) {
            abort(403, 'Anda harus menyelesaikan sub-modul sebelumnya terlebih dahulu.');
        }

        // Mendapatkan sub-modul dengan konten dan progress user
        $subModule->load(['contents' => function ($query) {
            $query->orderBy('urutan');
        }, 'contents.userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);

        // Mendapatkan progress user untuk sub-modul ini
        $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
        
        if (!$progress) {
            // Inisialisasi progress jika tidak ada
            $progress = $subModule->userProgress()->create([
                'user_id' => $user->id,
                'is_completed' => false,
                'progress_percentage' => 0,
                'started_at' => now()
            ]);
        }

        // Get contents collection for the view
        $contents = $subModule->contents;

        // Menghitung progress konten
        $totalContents = $contents->count();
        $completedContents = 0;
        $contentProgress = 0;

        foreach ($contents as $content) {
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

        // Mendapatkan sub-modul berikutnya dan sebelumnya
        $nextSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '>', $subModule->urutan)
            ->orderBy('urutan')
            ->first();

        $previousSubModule = SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '<', $subModule->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Mendapatkan info modul dan kursus
        $module = $subModule->module;
        $course = $module->course;

        // Check if sub-module has quiz and if user has passed it
        $subModuleQuizzes = $subModule->quizzes;
        $allSubModuleQuizzesPassed = true;
        if ($subModuleQuizzes->count() > 0) {
            foreach ($subModuleQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allSubModuleQuizzesPassed = false;
                    break;
                }
            }
        }

        // Periksa apakah sub-modul dapat ditandai sebagai selesai
        $canMarkComplete = $totalContents > 0 && $completedContents >= $totalContents && $allSubModuleQuizzesPassed;
        
        // Check if sub-module is completed (all contents are completed and all quizzes passed)
        $isSubModuleCompleted = $totalContents > 0 && $completedContents >= $totalContents && $allSubModuleQuizzesPassed;
        
        // If sub-module is completed, check if module is completed
        if ($isSubModuleCompleted && $progress && !$progress->is_completed) {
            // Auto-mark sub-module as complete if all contents are done and all quizzes passed
            $progress->update([
                'is_completed' => true,
                'progress_percentage' => 100,
                'completed_at' => now()
            ]);
            $progress->refresh();
        }
        
        // Check if module is completed (all sub-modules are completed)
        if ($isSubModuleCompleted) {
            $this->checkModuleCompletion($user, $subModule->module_id);
        }
        
        // Get next module if there's no next sub-module
        $nextModule = null;
        if (!$nextSubModule) {
            $nextModule = \App\Models\Module::where('course_id', $module->course_id)
                ->where('urutan', '>', $module->urutan)
                ->orderBy('urutan')
                ->first();
        }

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
            'canMarkComplete',
            'isSubModuleCompleted',
            'nextModule',
            'subModuleQuizzes',
            'allSubModuleQuizzesPassed'
        ));
    }

    /**
     * Mark a sub-module as completed.
     */
    public function markComplete(SubModule $subModule): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

        // Check if sub-module has quiz and if user has passed it
        $subModuleQuizzes = $subModule->quizzes;
        if ($subModuleQuizzes->count() > 0) {
            $allQuizzesPassed = true;
            foreach ($subModuleQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allQuizzesPassed = false;
                    break;
                }
            }
            
            if (!$allQuizzesPassed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus lulus semua quiz pada sub-modul ini terlebih dahulu.',
                    'quizzes_required' => true
                ], 400);
            }
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
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

        // Check if module has quiz and if user has passed it
        $moduleQuizzes = $module->quizzes;
        $allModuleQuizzesPassed = true;
        if ($moduleQuizzes->count() > 0) {
            foreach ($moduleQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allModuleQuizzesPassed = false;
                    break;
                }
            }
        }

        if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules && $allModuleQuizzesPassed) {
            // Module is completed, check if course is completed
            $this->checkCourseCompletion($user, $module->course_id);
        }
    }

    /**
     * Check if course is completed and update enrollment status.
     */
    private function checkCourseCompletion($user, $courseId): void
    {
        // Check for enrollment with any valid status
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

            // Check if module has quiz and if user has passed it
            $moduleQuizzes = $module->quizzes;
            $allModuleQuizzesPassed = true;
            if ($moduleQuizzes->count() > 0) {
                foreach ($moduleQuizzes as $quiz) {
                    if (!$quiz->hasUserPassed($user->id)) {
                        $allModuleQuizzesPassed = false;
                        break;
                    }
                }
            }

            if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules && $allModuleQuizzesPassed) {
                $completedModules++;
            }
        }

        // Check if course has quiz and if user has passed it
        $courseQuizzes = $course->quizzes;
        $allCourseQuizzesPassed = true;
        if ($courseQuizzes->count() > 0) {
            foreach ($courseQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allCourseQuizzesPassed = false;
                    break;
                }
            }
        }

        if ($completedModules >= $totalModules && $allCourseQuizzesPassed) {
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
        // Check if JP record already exists for this course and year
        $currentYear = now()->year;
        $existingJpRecord = $user->jpRecords()
            ->where('course_id', $course->id)
            ->where('tahun', $currentYear)
            ->first();

        if (!$existingJpRecord) {
            $user->jpRecords()->create([
                'course_id' => $course->id,
                'jp_earned' => $course->jp_value ?? 0,
                'tahun' => $currentYear,
                'recorded_at' => now()
            ]);
        }
    }
} 