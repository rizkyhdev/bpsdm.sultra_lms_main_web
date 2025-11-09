<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentPelatihanController extends Controller
{
    /**
     * Display the list of enrolled courses with filters and sorting.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = $user->userEnrollments()
            ->with(['course.modules.subModules']);

        // Status filter
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search filter
        $search = $request->get('q');
        if ($search) {
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('enrollment_date', 'asc');
                break;
            case 'progress':
                // We'll calculate progress after fetching
                $query->orderBy('enrollment_date', 'desc');
                break;
            case 'title':
                $query->join('courses', 'user_enrollments.course_id', '=', 'courses.id')
                      ->orderBy('courses.judul', 'asc')
                      ->select('user_enrollments.*');
                break;
            case 'recent':
            default:
                $query->orderBy('enrollment_date', 'desc');
                break;
        }

        $enrollments = $query->get();

        // Calculate progress for each enrollment
        $enrollmentsWithProgress = $enrollments->map(function ($enrollment) use ($user) {
            $course = $enrollment->course;
            $totalSubModules = 0;
            $completedSubModules = 0;

            foreach ($course->modules as $module) {
                foreach ($module->subModules as $subModule) {
                    $totalSubModules++;
                    $progress = DB::table('user_progress')
                        ->where('user_id', $user->id)
                        ->where('sub_module_id', $subModule->id)
                        ->where('is_completed', true)
                        ->exists();
                    
                    if ($progress) {
                        $completedSubModules++;
                    }
                }
            }

            $progressPercent = $totalSubModules > 0 
                ? round(($completedSubModules / $totalSubModules) * 100, 2) 
                : 0;

            $enrollment->progress_percent = $progressPercent;
            $enrollment->total_sub_modules = $totalSubModules;
            $enrollment->completed_sub_modules = $completedSubModules;

            return $enrollment;
        });

        // Re-sort by progress if needed
        if ($sort === 'progress') {
            $enrollmentsWithProgress = $enrollmentsWithProgress->sortByDesc('progress_percent')->values();
        }

        return view('student.pelatihan.index', compact(
            'enrollmentsWithProgress',
            'status',
            'search',
            'sort'
        ));
    }
}

