<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Enroll the authenticated user in a course.
     */
    public function store(Course $course): RedirectResponse
    {
        $user = Auth::user();

        // Check if already enrolled
        $enrollment = UserEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            UserEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_date' => now(),
                'status' => 'in_progress',
            ]);
        }

        // Remove from wishlist if exists
        if ($user->wishlists()->where('course_id', $course->id)->exists()) {
            $user->wishlists()->detach($course->id);
        }

        // Redirect to course outline (assuming route exists)
        return redirect()->route('student.courses.show', $course->id)
            ->with('success', 'Berhasil mendaftar ke pelatihan.');
    }
}

