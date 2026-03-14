<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksBackofficePermissions
{
    /**
     * @param  array<int, string>  $permissions
     */
    protected function hasAny(User $user, array $permissions): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasAnyPermission($permissions);
    }
}

