<?php

namespace App\Policies;

use App\Models\Content;
use App\Models\User;

class ContentPolicy
{
    public function view(User $user, Content $content)
    {
        // Admin can view all content
        if ($user->role === 'admin') {
            return true;
        }
        // Instructor can view their own content
        return $user->role === 'instructor' && (int) $content->subModule->module->course->user_id === (int) $user->id;
    }

    public function update(User $user, Content $content)
    {
        // Admin can update all content
        if ($user->role === 'admin') {
            return true;
        }
        // Instructor can update their own content
        return $user->role === 'instructor' && (int) $content->subModule->module->course->user_id === (int) $user->id;
    }

    public function delete(User $user, Content $content)
    {
        // Admin can delete all content
        if ($user->role === 'admin') {
            return true;
        }
        // Instructor can delete their own content
        return $user->role === 'instructor' && (int) $content->subModule->module->course->user_id === (int) $user->id;
    }
}


