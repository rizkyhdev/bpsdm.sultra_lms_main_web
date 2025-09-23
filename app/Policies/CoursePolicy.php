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
}


