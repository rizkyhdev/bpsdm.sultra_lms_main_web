<?php

namespace App\Policies;

use App\Models\QuizAttempt;
use App\Models\User;

class QuizAttemptPolicy
{
    public function view(User $user, QuizAttempt $attempt)
    {
        return $user->role === 'instructor' && (int) $attempt->quiz->subModule->module->course->user_id === (int) $user->id;
    }

    public function update(User $user, QuizAttempt $attempt)
    {
        return $this->view($user, $attempt);
    }
}


