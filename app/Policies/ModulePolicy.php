<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    public function view(User $user, Module $module)
    {
        return $user->role === 'instructor' && (int) $module->course->user_id === (int) $user->id;
    }

    public function update(User $user, Module $module)
    {
        return $this->view($user, $module);
    }

    public function delete(User $user, Module $module)
    {
        return $this->view($user, $module);
    }
}


