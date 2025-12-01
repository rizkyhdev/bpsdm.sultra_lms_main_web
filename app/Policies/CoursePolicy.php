<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'instructor';
    }

    public function view(User $user, Course $course)
    {
        return $user->role === 'instructor' && (int) $course->user_id === (int) $user->id;
    }

    public function create(User $user)
    {
        return $user->role === 'instructor';
    }

    public function update(User $user, Course $course)
    {
        return $this->view($user, $course);
    }

    public function delete(User $user, Course $course)
    {
        return $this->view($user, $course);
    }

    /**
     * Determine if the user can download a certificate for this course.
     */
    public function downloadCertificate(User $user, Course $course): bool
    {
        // Check if user is enrolled
        $enrollment = \App\Models\UserEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return false;
        }

        // Check if completion is 100% and completed_at is not null
        return $enrollment->completion_percent == 100 && $enrollment->completed_at !== null;
    }

    /**
     * Determine if the user can preview the certificate template.
     */
    public function preview(User $user, Course $course): bool
    {
        // Allow instructors and admins
        return $user->role === 'instructor' || $user->role === 'admin';
    }
}


