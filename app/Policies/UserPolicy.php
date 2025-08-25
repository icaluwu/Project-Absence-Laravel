<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the authenticated user can delete the given user.
     */
    public function delete(User $actor, User $target): Response
    {
        // Prevent self-delete via this management UI
        if ($actor->id === $target->id) {
            return Response::deny(__('admin.users.errors.self'));
        }

        // Block deletion of any Admin
        if (Roles::isAdmin($target)) {
            return Response::deny(__('admin.users.errors.admin_role'));
        }

        // Defensive: block deleting the last Admin (in case rules change later)
        if (Roles::isLastAdmin($target)) {
            return Response::deny(__('admin.users.errors.last_admin'));
        }

        return Response::allow();
    }
}
