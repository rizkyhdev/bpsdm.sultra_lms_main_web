<?php

namespace App\Policies;

use App\Models\SubModule;
use App\Models\User;

class SubModulePolicy
{
    public function view(User $user, SubModule $sub)
    {
        return $user->role === 'instructor' && (int) $sub->module->course->user_id === (int) $user->id;
    }

    public function update(User $user, SubModule $sub)
    {
        return $this->view($user, $sub);
    }

    public function delete(User $user, SubModule $sub)
    {
        return $this->view($user, $sub);
    }
}


