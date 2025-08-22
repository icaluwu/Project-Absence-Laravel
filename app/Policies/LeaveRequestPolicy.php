<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function view(User $user, LeaveRequest $leave): bool
    {
        return $user->hasAnyRole(['Admin','HR']) || $leave->user_id === $user->id;
    }

    public function update(User $user, LeaveRequest $leave): bool
    {
        return $user->hasAnyRole(['Admin','HR']);
    }
}
