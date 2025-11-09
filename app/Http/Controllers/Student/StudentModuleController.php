<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentModuleController extends Controller
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
     * Menampilkan modul tertentu.
     */
    public function show(Module $module): View
    {
        $user = Auth::user();
        
        // Periksa apakah user sudah terdaftar dalam kursus
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses modul.');
        }

        // Mendapatkan modul dengan sub-modul dan progress user
        $module->load(['course', 'subModules' => function ($query) {
            $query->orderBy('urutan');
        }, 'subModules.userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);

        // Menghitung progress modul
        $totalSubModules = $module->subModules->count();
        $completedSubModules = 0;
        $moduleProgress = 0;

        foreach ($module->subModules as $subModule) {
            $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
            
            if ($progress && $progress->is_completed) {
                $completedSubModules++;
            }
            
            if ($progress) {
                $moduleProgress += $progress->progress_percentage;
            }
        }

        $overallProgress = $totalSubModules > 0 ? round($moduleProgress / $totalSubModules, 2) : 0;
        $completionPercentage = $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;

        // Mendapatkan modul berikutnya dan sebelumnya
        $nextModule = Module::where('course_id', $module->course_id)
            ->where('urutan', '>', $module->urutan)
            ->orderBy('urutan')
            ->first();

        $previousModule = Module::where('course_id', $module->course_id)
            ->where('urutan', '<', $module->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Periksa apakah modul dapat diakses (modul sebelumnya selesai atau modul pertama)
        $isAccessible = $module->urutan === 1 || $previousModule === null;
        
        if (!$isAccessible && $previousModule) {
            $previousModuleProgress = $previousModule->subModules()
                ->whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->count();
            
            $previousModuleTotal = $previousModule->subModules()->count();
            $isAccessible = $previousModuleProgress >= $previousModuleTotal;
        }

        if (!$isAccessible) {
            abort(403, 'Anda harus menyelesaikan modul sebelumnya terlebih dahulu.');
        }

        return view('student.modules.show', compact(
            'module',
            'enrollment',
            'totalSubModules',
            'completedSubModules',
            'overallProgress',
            'completionPercentage',
            'nextModule',
            'previousModule',
            'isAccessible'
        ));
    }

    /**
     * Mark a module as completed.
     */
    public function markComplete(Module $module): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        // Check if all sub-modules are completed
        $subModules = $module->subModules;
        $totalSubModules = $subModules->count();
        $completedSubModules = 0;

        foreach ($subModules as $subModule) {
            $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
            
            if ($progress && $progress->is_completed) {
                $completedSubModules++;
            }
        }

        if ($completedSubModules < $totalSubModules) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus menyelesaikan semua sub-modul terlebih dahulu.',
                'completed' => $completedSubModules,
                'total' => $totalSubModules
            ], 400);
        }

        try {
            // Mark module as completed by updating all sub-module progress
            foreach ($subModules as $subModule) {
                $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
                
                if ($progress) {
                    $progress->update([
                        'is_completed' => true,
                        'progress_percentage' => 100,
                        'completed_at' => now()
                    ]);
                }
            }

            // Check if course is completed
            $this->checkCourseCompletion($user, $module->course_id);

            return response()->json([
                'success' => true,
                'message' => 'Modul berhasil diselesaikan!',
                'module_id' => $module->id,
                'completion_percentage' => 100
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan modul.'
            ], 500);
        }
    }

    /**
     * Get module progress details.
     */
    public function getProgress(Module $module): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $subModules = $module->subModules()->with(['userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->orderBy('urutan')->get();

        $progressData = [];
        $totalSubModules = $subModules->count();
        $completedSubModules = 0;
        $totalProgress = 0;

        foreach ($subModules as $subModule) {
            $progress = $subModule->userProgress()->where('user_id', $user->id)->first();
            $isCompleted = $progress ? $progress->is_completed : false;
            $progressPercentage = $progress ? $progress->progress_percentage : 0;
            
            if ($isCompleted) {
                $completedSubModules++;
            }
            
            $totalProgress += $progressPercentage;

            $progressData[] = [
                'sub_module_id' => $subModule->id,
                'judul' => $subModule->judul,
                'urutan' => $subModule->urutan,
                'is_completed' => $isCompleted,
                'progress_percentage' => $progressPercentage,
                'started_at' => $progress ? $progress->started_at : null,
                'completed_at' => $progress ? $progress->completed_at : null
            ];
        }

        $overallProgress = $totalSubModules > 0 ? round($totalProgress / $totalSubModules, 2) : 0;
        $completionPercentage = $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'module' => $module,
                'sub_modules' => $progressData,
                'overall_progress' => $overallProgress,
                'completion_percentage' => $completionPercentage,
                'completed_sub_modules' => $completedSubModules,
                'total_sub_modules' => $totalSubModules
            ]
        ]);
    }

    /**
     * Get module navigation (previous/next modules).
     */
    public function getNavigation(Module $module): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $nextModule = Module::where('course_id', $module->course_id)
            ->where('urutan', '>', $module->urutan)
            ->orderBy('urutan')
            ->first();

        $previousModule = Module::where('course_id', $module->course_id)
            ->where('urutan', '<', $module->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        // Check accessibility
        $isNextAccessible = false;
        if ($nextModule) {
            $isNextAccessible = $this->isModuleAccessible($user, $nextModule);
        }

        $isPreviousAccessible = $previousModule !== null;

        return response()->json([
            'success' => true,
            'data' => [
                'current_module' => [
                    'id' => $module->id,
                    'judul' => $module->judul,
                    'urutan' => $module->urutan
                ],
                'next_module' => $nextModule ? [
                    'id' => $nextModule->id,
                    'judul' => $nextModule->judul,
                    'urutan' => $nextModule->urutan,
                    'is_accessible' => $isNextAccessible
                ] : null,
                'previous_module' => $previousModule ? [
                    'id' => $previousModule->id,
                    'judul' => $previousModule->judul,
                    'urutan' => $previousModule->urutan,
                    'is_accessible' => $isPreviousAccessible
                ] : null
            ]
        ]);
    }

    /**
     * Check if a module is accessible to the user.
     */
    private function isModuleAccessible($user, Module $module): bool
    {
        if ($module->urutan === 1) {
            return true;
        }

        $previousModule = Module::where('course_id', $module->course_id)
            ->where('urutan', '<', $module->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousModule) {
            return true;
        }

        $previousModuleProgress = $previousModule->subModules()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        $previousModuleTotal = $previousModule->subModules()->count();

        return $previousModuleProgress >= $previousModuleTotal;
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