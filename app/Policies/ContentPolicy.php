<?php

namespace App\Policies;

use App\Models\Content;
use App\Models\User;

class ContentPolicy
{
    public function view(User $user, Content $content)
    {
        return $user->role === 'instructor' && (int) $content->subModule->module->course->user_id === (int) $user->id;
    }

    public function update(User $user, Content $content)
    {
        return $this->view($user, $content);
    }

    public function delete(User $user, Content $content)
    {
        return $this->view($user, $content);
    }
}


