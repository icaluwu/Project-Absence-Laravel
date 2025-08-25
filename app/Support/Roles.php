<?php

namespace App\Support;

use App\Models\User;

class Roles
{
    public static function usesSpatie(): bool
    {
        return in_array('Spatie\\Permission\\Traits\\HasRoles', class_uses_recursive(User::class));
    }

    public static function isAdmin(User $user): bool
    {
        if (self::usesSpatie()) {
            return $user->hasRole('Admin');
        }
        return ($user->role ?? null) === 'Admin';
    }

    public static function adminsQuery()
    {
        if (self::usesSpatie()) {
            return User::role('Admin');
        }
        return User::query()->where('role', 'Admin');
    }

    public static function isLastAdmin(User $user): bool
    {
        if (!self::isAdmin($user)) {
            return false;
        }
        // There must be at least 1 other Admin besides this user
        return !self::adminsQuery()->where('id', '!=', $user->id)->exists();
    }
}
