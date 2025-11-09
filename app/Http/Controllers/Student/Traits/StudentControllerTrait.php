<?php

namespace App\Http\Controllers\Student\Traits;

use App\Models\User;
use App\Models\UserEnrollment;
use Illuminate\Support\Facades\Auth;

trait StudentControllerTrait
{

   

    /**
     * Mendapatkan user yang terautentikasi.
     */
    protected function getCurrentUser(): User
    {
        return Auth::user();
    }

      public function index()
    {
        $user = $this->getCurrentUser();
        $name = $user->name;
        $email = $user->email;
        // $profilePicture = $user->profile_picture;
        return view('student.profile', compact('name')); // atau 'profile', sesuai file blade Anda
    }

    /**
     * Periksa apakah user sudah terdaftar dalam kursus.
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
     * Mendapatkan pendaftaran user untuk kursus.
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
     * Periksa apakah user sudah menyelesaikan kursus.
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
     * Mendapatkan JP user yang terkumpul untuk tahun tertentu.
     */
    protected function getUserJpForYear(int $year): int
    {
        $user = $this->getCurrentUser();
        
        return $user->jpRecords()
            ->whereYear('created_at', $year)
            ->sum('jp_value');
    }

    /**
     * Mendapatkan total JP user yang diperoleh.
     */
    protected function getUserTotalJp(): int
    {
        $user = $this->getCurrentUser();
        
        return $user->jpRecords()->sum('jp_value');
    }

    /**
     * Periksa apakah user dapat mengakses modul berdasarkan penyelesaian modul sebelumnya.
     */
    protected function canAccessModule(int $moduleId): bool
    {
        $user = $this->getCurrentUser();
        
        // Mendapatkan modul
        $module = \App\Models\Module::find($moduleId);
        if (!$module) {
            return false;
        }

        // Modul pertama selalu dapat diakses
        if ($module->urutan === 1) {
            return true;
        }

        // Mendapatkan modul sebelumnya
        $previousModule = \App\Models\Module::where('course_id', $module->course_id)
            ->where('urutan', '<', $module->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousModule) {
            return true;
        }

        // Periksa apakah modul sebelumnya sudah selesai
        $totalSubModules = $previousModule->subModules()->count();
        $completedSubModules = $previousModule->subModules()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        return $totalSubModules > 0 && $completedSubModules >= $totalSubModules;
    }

    /**
     * Periksa apakah user dapat mengakses sub-modul berdasarkan penyelesaian sub-modul sebelumnya.
     */
    protected function canAccessSubModule(int $subModuleId): bool
    {
        $user = $this->getCurrentUser();
        
        // Mendapatkan sub-modul
        $subModule = \App\Models\SubModule::find($subModuleId);
        if (!$subModule) {
            return false;
        }

        // Sub-modul pertama selalu dapat diakses
        if ($subModule->urutan === 1) {
            return true;
        }

        // Mendapatkan sub-modul sebelumnya
        $previousSubModule = \App\Models\SubModule::where('module_id', $subModule->module_id)
            ->where('urutan', '<', $subModule->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousSubModule) {
            return true;
        }

        // Periksa apakah sub-modul sebelumnya sudah selesai
        $progress = $previousSubModule->userProgress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->first();

        return $progress !== null;
    }

    /**
     * Periksa apakah user dapat mengakses konten berdasarkan penyelesaian konten sebelumnya.
     */
    protected function canAccessContent(int $contentId): bool
    {
        $user = $this->getCurrentUser();
        
        // Mendapatkan konten
        $content = \App\Models\Content::find($contentId);
        if (!$content) {
            return false;
        }

        // Konten pertama selalu dapat diakses
        if ($content->urutan === 1) {
            return true;
        }

        // Mendapatkan konten sebelumnya
        $previousContent = \App\Models\Content::where('sub_module_id', $content->sub_module_id)
            ->where('urutan', '<', $content->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if (!$previousContent) {
            return true;
        }

        // Periksa apakah konten sebelumnya sudah selesai
        $progress = $previousContent->userProgress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->first();

        return $progress !== null;
    }

    /**
     * Mendapatkan persentase progress kursus user.
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
     * Mendapatkan persentase progress modul user.
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
     * Mendapatkan persentase progress sub-modul user.
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
     * Periksa apakah user dapat mengambil quiz.
     */
    protected function canTakeQuiz(int $quizId): bool
    {
        $user = $this->getCurrentUser();
        
        $quiz = \App\Models\Quiz::find($quizId);
        if (!$quiz) {
            return false;
        }

        // Periksa apakah user sudah mencapai maksimal percobaan (hanya hitung percobaan yang selesai)
        $attemptCount = $user->quizAttempts()
            ->where('quiz_id', $quizId)
            ->whereNotNull('completed_at')
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return false;
        }

        // Periksa apakah user memiliki percobaan aktif (belum selesai)
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quizId)
            ->whereNull('completed_at')
            ->first();

        if ($activeAttempt) {
            return true; // Dapat melanjutkan percobaan yang ada
        }

        // Periksa apakah user sudah lulus quiz
        $passedAttempt = $user->quizAttempts()
            ->where('quiz_id', $quizId)
            ->where('is_passed', true)
            ->first();

        if ($passedAttempt) {
            return false; // Sudah lulus
        }

        return true;
    }

    /**
     * Memformat nilai JP dengan format yang tepat.
     */
    protected function formatJpValue(int $jpValue): string
    {
        return number_format($jpValue, 0, ',', '.') . ' JP';
    }

    /**
     * Memformat persentase dengan format yang tepat.
     */
    protected function formatPercentage(float $percentage): string
    {
        return number_format($percentage, 1) . '%';
    }

    /**
     * Mendapatkan statistik pembelajaran user.
     */
    protected function getUserLearningStats(): array
    {
        $user = $this->getCurrentUser();
        
        $totalCourses = $user->userEnrollments()->count();
        $completedCourses = $user->userEnrollments()->whereNotNull('completed_at')->count();
        $totalJp = $this->getUserTotalJp();
        $averageQuizScore = $user->quizAttempts()
            ->whereNotNull('completed_at')
            ->avg('nilai') ?? 0;

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'total_jp' => $totalJp,
            'average_quiz_score' => round($averageQuizScore, 2)
        ];
    }
} 