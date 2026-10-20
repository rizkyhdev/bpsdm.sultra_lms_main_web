<?php

namespace App\Policies;

use App\Models\SimulationAttempt;
use App\Models\User;

class SimulationAttemptPolicy
{
    /**
     * Peserta hanya dapat melihat percobaan simulasinya sendiri.
     */
    public function view(User $user, SimulationAttempt $attempt): bool
    {
        return (int) $attempt->user_id === (int) $user->id;
    }

    /**
     * Peserta hanya dapat menjawab dan menyelesaikan percobaan simulasinya sendiri.
     */
    public function update(User $user, SimulationAttempt $attempt): bool
    {
        return $this->view($user, $attempt);
    }
}
