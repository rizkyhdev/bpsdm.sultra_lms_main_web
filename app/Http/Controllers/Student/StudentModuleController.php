<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Module;
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
        // Accept multiple valid enrollment statuses: enrolled, in_progress, completed, or active
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses modul.');
        }

        // Mendapatkan modul dengan sub-modul
        $module->load(['course', 'subModules' => function ($query) {
            $query->orderBy('urutan');
        }]);

        // Menghitung progress modul berdasarkan progress sub-modul yang dihitung
        // dari konten & quiz (tidak mengandalkan kolom yang tidak ada di tabel user_progress)
        $totalSubModules = $module->subModules->count();
        $completedSubModules = 0;
        $moduleProgress = 0;

        foreach ($module->subModules as $subModule) {
            // Hitung progress sub‑modul secara dinamis
            $totalContents = $subModule->contents()->count();
            $completedContents = $subModule->contents()
                ->whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->count();

            $subModuleQuizzes = $subModule->quizzes;
            $totalQuizzes = $subModuleQuizzes->count();
            $passedQuizzes = 0;
            $allSubModuleQuizzesPassed = true;

            if ($totalQuizzes > 0) {
                foreach ($subModuleQuizzes as $quiz) {
                    if ($quiz->hasUserPassed($user->id)) {
                        $passedQuizzes++;
                    } else {
                        $allSubModuleQuizzesPassed = false;
                    }
                }
            }

            // Rumus sama dengan di controller sub‑modul/konten:
            $contentProgressPercentage = $totalContents > 0
                ? round(($completedContents / $totalContents) * 100, 2)
                : ($totalContents == 0 ? 100 : 0);

            $quizProgressPercentage = $totalQuizzes > 0
                ? round(($passedQuizzes / $totalQuizzes) * 100, 2)
                : ($totalQuizzes == 0 ? 100 : 0);

            if ($totalContents > 0 && $totalQuizzes > 0) {
                $calculatedProgress = round(($contentProgressPercentage + $quizProgressPercentage) / 2, 2);
            } elseif ($totalContents > 0) {
                $calculatedProgress = $contentProgressPercentage;
            } elseif ($totalQuizzes > 0) {
                $calculatedProgress = $quizProgressPercentage;
            } else {
                $calculatedProgress = 0;
            }

            // Sub‑modul dianggap selesai jika semua konten selesai & semua quiz lulus
            $contentsCompleted = $totalContents == 0 || ($totalContents > 0 && $completedContents >= $totalContents);
            $isSubModuleCompleted = $contentsCompleted && $allSubModuleQuizzesPassed;

            if ($isSubModuleCompleted) {
                $completedSubModules++;
            }

            $moduleProgress += $calculatedProgress;

            // Simpan nilai ini ke model untuk dipakai di view
            $subModule->calculated_progress_percentage = $calculatedProgress;
            $subModule->is_calculated_completed = $isSubModuleCompleted;
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

        // Check if current module is completed (all sub-modules are completed)
        $isModuleCompleted = $totalSubModules > 0 && $completedSubModules >= $totalSubModules;
        
        // If module is completed, check if course is completed
        if ($isModuleCompleted) {
            $this->checkCourseCompletion($user, $module->course_id);
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
            'isAccessible',
            'isModuleCompleted'
        ));
    }

    /**
     * Mark a module as completed.
     */
    public function markComplete(Module $module): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

        // Check if module has quiz and if user has passed it
        $moduleQuizzes = $module->quizzes;
        if ($moduleQuizzes->count() > 0) {
            $allModuleQuizzesPassed = true;
            foreach ($moduleQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allModuleQuizzesPassed = false;
                    break;
                }
            }
            
            if (!$allModuleQuizzesPassed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus lulus semua quiz pada modul ini terlebih dahulu.',
                    'quizzes_required' => true
                ], 400);
            }
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
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $module->course_id)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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
        // Check for enrollment with any valid status (not just active)
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