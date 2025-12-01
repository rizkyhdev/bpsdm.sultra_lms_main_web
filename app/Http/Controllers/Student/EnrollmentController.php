<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserEnrollment;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    /**
     * Enroll the authenticated user in a course.
     */
    public function store(Course $course): RedirectResponse
    {
        $user = Auth::user();
        $nowUtc = CarbonImmutable::now('UTC');

        // Check enrollment window
        if (!$course->canEnroll($nowUtc)) {
            $status = $course->scheduleStatus($nowUtc);
            $reason = match ($status) {
                Course::SCHEDULE_STATUS_BEFORE_START => 'enrollment_not_open',
                Course::SCHEDULE_STATUS_AFTER_END => 'enrollment_closed',
                default => 'enrollment_not_available',
            };

            $relativeTime = match ($status) {
                Course::SCHEDULE_STATUS_BEFORE_START => $course->start_date_time?->diffForHumans($nowUtc),
                Course::SCHEDULE_STATUS_AFTER_END => $course->end_date_time?->diffForHumans($nowUtc),
                default => null,
            };

            // Log blocked enrollment attempt
            Log::info('Enrollment blocked', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'reason' => $reason,
                'status' => $status,
                'relative_time' => $relativeTime,
                'now_utc' => $nowUtc->toIso8601String(),
            ]);

            $message = match ($status) {
                Course::SCHEDULE_STATUS_BEFORE_START => __('schedule.enrollment_opens_in', ['time' => $relativeTime ?? '']),
                Course::SCHEDULE_STATUS_AFTER_END => __('schedule.enrollment_closed_ago', ['time' => $relativeTime ?? '']),
                default => __('Enrollment is not available at this time.'),
            };

            return redirect()->back()->with('error', $message);
        }

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

