<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserEnrollment;

class UserEnrollmentPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'instructor';
    }

    public function view(User $user, UserEnrollment $enrollment)
    {
        return $user->role === 'instructor' && (int) $enrollment->course->user_id === (int) $user->id;
    }

    public function update(User $user, UserEnrollment $enrollment)
    {
        return $this->view($user, $enrollment);
    }
}


