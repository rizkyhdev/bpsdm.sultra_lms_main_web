<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;

/**
 * Kelas InstructorProgressController
 * Mengagregasi dan menampilkan progres untuk kursus milik instruktur.
 */
class InstructorProgressController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor']);
    }

    /**
     * Agregasi progres tingkat kursus.
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $course = Course::with('modules.subModules')->findOrFail($courseId);
        $this->authorize('view', $course);

        $subModuleIds = $course->modules->flatMap(function ($m) {
            return $m->subModules->pluck('id');
        })->values();

        $total = UserProgress::whereIn('sub_module_id', $subModuleIds)->count();
        $completed = UserProgress::whereIn('sub_module_id', $subModuleIds)->where('is_completed', 1)->count();

        return view('instructor.progress.index', [
            'course' => $course,
            'total' => $total,
            'completed' => $completed,
        ]);
    }

    /**
     * Tampilkan progres seorang pengguna dalam sebuah kursus.
     * @param int $courseId
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    public function showUser($courseId, $userId)
    {
        $course = Course::with('modules.subModules')->findOrFail($courseId);
        $this->authorize('view', $course);

        $subModuleIds = $course->modules->flatMap(function ($m) {
            return $m->subModules->pluck('id');
        })->values();

        $progress = UserProgress::with('subModule')
            ->whereIn('sub_module_id', $subModuleIds)
            ->where('user_id', $userId)
            ->orderBy('sub_module_id')
            ->get();

        return view('instructor.progress.show_user', [
            'course' => $course,
            'userId' => $userId,
            'progress' => $progress,
        ]);
    }
}


