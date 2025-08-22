<?php

namespace App\Policies;

use App\Models\OvertimeRequest;
use App\Models\User;

class OvertimeRequestPolicy
{
    public function view(User $user, OvertimeRequest $overtime): bool
    {
        return $user->hasAnyRole(['Admin','HR']) || $overtime->user_id === $user->id;
    }

    public function update(User $user, OvertimeRequest $overtime): bool
    {
        return $user->hasAnyRole(['Admin','HR']);
    }
}
