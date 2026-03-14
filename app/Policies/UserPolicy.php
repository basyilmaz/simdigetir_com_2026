<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksBackofficePermissions;

class UserPolicy
{
    use ChecksBackofficePermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasAny($user, ['users.view', 'users.manage']);
    }

    public function view(User $user, User $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAny($user, ['users.manage']);
    }

    public function update(User $user, User $record): bool
    {
        return $this->hasAny($user, ['users.manage']);
    }

    public function delete(User $user, User $record): bool
    {
        if ($user->is($record)) {
            return false;
        }

        return $this->hasAny($user, ['users.manage']);
    }

    public function deleteAny(User $user): bool
    {
        return $this->hasAny($user, ['users.manage']);
    }
}
