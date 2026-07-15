<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;

class CompetitionPolicy
{
    public function update(User $user, Competition $competition): bool
    {
        return $competition->organizer_id === $user->id;
    }
}
