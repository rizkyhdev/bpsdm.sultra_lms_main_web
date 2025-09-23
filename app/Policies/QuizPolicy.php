<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    public function view(User $user, Quiz $quiz)
    {
        return $user->role === 'instructor' && (int) $quiz->subModule->module->course->user_id === (int) $user->id;
    }

    public function update(User $user, Quiz $quiz)
    {
        return $this->view($user, $quiz);
    }

    public function delete(User $user, Quiz $quiz)
    {
        return $this->view($user, $quiz);
    }
}


