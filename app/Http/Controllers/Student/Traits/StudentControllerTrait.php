<?php

namespace App\Http\Controllers\Student\Traits;

use App\Models\User;
use App\Models\UserEnrollment;
use Illuminate\Support\Facades\Auth;

trait StudentControllerTrait
{
    /**
     * Get the authenticated user.
     */
    protected function getCurrentUser(): User
    {
        return Auth::user();
    }

    /**
     * Check if user is enrolled in a course.
     */
    protected function isUserEnrolled(int $courseId): bool
    {
        $user = $this->getCurrentUser();
        
        return $user->userEnrollments()
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get user enrollment for a course.
     */
    protected function getUserEnrollment(int $courseId): ?UserEnrollment
    {
        $user = $this->getCurrentUser();
        
        return $user->userEnrollments()
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Check if user has completed a course.
     */
    protected function hasUserCompletedCourse(int $courseId): bool
    {
        $user = $this->getCurrentUser();
        
        return $user->userEnrollments()
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get user's JP accumulated for a specific year.
     */
    protected function getUserJpForYear(int $year): int
    {
        $user = $this->getCurrentUser();
        
        return $user->jpRecords()
            ->whereYear('created_at', $year)
            ->sum('jp_value');
    }

    /**
     * Get user's total JP earned.
     */
    protected function getUserTotalJp(): int
    {
        $user = $this->getCurrentUser();
        
        return $user->jpRecords()->sum('jp_value');
    }

    /**
     * Check if user can access a module based on completion of previous modules.
     */
    protected function canAccessModule(int $moduleId): bool
    {
        $user = $this->getCurrentUser();
        
        // Get the module
        $module = \App\Models\Module::find($moduleId);
        if (!$module) {
            return false;
        }

        // First module is always accessible
        if ($module->urutan === 1) {
            return true;
        }

        // Get previous module
        $previousModule = \App\Models\Module::where('course_id', $module->course_id)
            ->where('urutan', '<', $module->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousModule) {
            return true;
        }

        // Check if previous module is completed
        $totalSubModules = $previousModule->subModules()->count();
        $completedSubModules = $previousModule->subModules()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        return $totalSubModules > 0 && $completedSubModules >= $totalSubModules;
    }

    /**
     * Check if user can access a sub-module based on completion of previous sub-modules.
     */
    protected function canAccessSubModule(int $subModuleId): bool
    {
        $user = $this->getCurrentUser();
        
        // Get the sub-module
        $subModule = \App\Models\SubModule::find($subModuleId);
        if (!$subModule) {
            return false;
        }

        // First sub-module is always accessible
        if ($subModule->urutan === 1) {
            return true;
        }

        // Get previous sub-module
        $previousSubModule = \App\Models\SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '<', $subModule->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousSubModule) {
            return true;
        }

        // Check if previous sub-module is completed
        $progress = $previousSubModule->userProgress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->first();

        return $progress !== null;
    }

    /**
     * Check if user can access content based on completion of previous content.
     */
    protected function canAccessContent(int $contentId): bool
    {
        $user = $this->getCurrentUser();
        
        // Get the content
        $content = \App\Models\Content::find($contentId);
        if (!$content) {
            return false;
        }

        // First content is always accessible
        if ($content->urutan === 1) {
            return true;
        }

        // Get previous content
        $previousContent = \App\Models\Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '<', $content->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousContent) {
            return true;
        }

        // Check if previous content is completed
        $progress = $previousContent->userProgress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->first();

        return $progress !== null;
    }

    /**
     * Get user's course progress percentage.
     */
    protected function getUserCourseProgress(int $courseId): float
    {
        $user = $this->getCurrentUser();
        
        $course = \App\Models\Course::find($courseId);
        if (!$course) {
            return 0;
        }

        $totalSubModules = 0;
        $completedSubModules = 0;

        foreach ($course->modules as $module) {
            foreach ($module->subModules as $subModule) {
                $totalSubModules++;
                $progress = $subModule->userProgress()
                    ->where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->first();
                
                if ($progress) {
                    $completedSubModules++;
                }
            }
        }

        return $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;
    }

    /**
     * Get user's module progress percentage.
     */
    protected function getUserModuleProgress(int $moduleId): float
    {
        $user = $this->getCurrentUser();
        
        $module = \App\Models\Module::find($moduleId);
        if (!$module) {
            return 0;
        }

        $totalSubModules = $module->subModules()->count();
        $completedSubModules = $module->subModules()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        return $totalSubModules > 0 ? round(($completedSubModules / $totalSubModules) * 100, 2) : 0;
    }

    /**
     * Get user's sub-module progress percentage.
     */
    protected function getUserSubModuleProgress(int $subModuleId): float
    {
        $user = $this->getCurrentUser();
        
        $subModule = \App\Models\SubModule::find($subModuleId);
        if (!$subModule) {
            return 0;
        }

        $totalContents = $subModule->contents()->count();
        $completedContents = $subModule->contents()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        return $totalContents > 0 ? round(($completedContents / $totalContents) * 100, 2) : 0;
    }

    /**
     * Check if user can take a quiz.
     */
    protected function canTakeQuiz(int $quizId): bool
    {
        $user = $this->getCurrentUser();
        
        $quiz = \App\Models\Quiz::find($quizId);
        if (!$quiz) {
            return false;
        }

        // Check if user has reached max attempts
        $attemptCount = $user->quizAttempts()
            ->where('quiz_id', $quizId)
            ->where('status', '!=', 'in_progress')
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return false;
        }

        // Check if user has active attempt
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quizId)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            return true; // Can continue existing attempt
        }

        // Check if user has passed the quiz
        $passedAttempt = $user->quizAttempts()
            ->where('quiz_id', $quizId)
            ->where('status', 'passed')
            ->first();

        if ($passedAttempt) {
            return false; // Already passed
        }

        return true;
    }

    /**
     * Format JP value with proper formatting.
     */
    protected function formatJpValue(int $jpValue): string
    {
        return number_format($jpValue, 0, ',', '.') . ' JP';
    }

    /**
     * Format percentage with proper formatting.
     */
    protected function formatPercentage(float $percentage): string
    {
        return number_format($percentage, 1) . '%';
    }

    /**
     * Get user's learning statistics.
     */
    protected function getUserLearningStats(): array
    {
        $user = $this->getCurrentUser();
        
        $totalCourses = $user->userEnrollments()->count();
        $completedCourses = $user->userEnrollments()->whereNotNull('completed_at')->count();
        $totalJp = $this->getUserTotalJp();
        $averageQuizScore = $user->quizAttempts()
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'total_jp' => $totalJp,
            'average_quiz_score' => round($averageQuizScore, 2)
        ];
    }
} 