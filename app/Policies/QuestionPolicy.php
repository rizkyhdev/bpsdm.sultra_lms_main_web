<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function view(User $user, Question $question)
    {
        return $user->role === 'instructor' && (int) $question->quiz->subModule->module->course->user_id === (int) $user->id;
    }

    public function update(User $user, Question $question)
    {
        return $this->view($user, $question);
    }

    public function delete(User $user, Question $question)
    {
        return $this->view($user, $question);
    }
}


