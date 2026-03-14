<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksBackofficePermissions;

class LandingContentPolicy
{
    use ChecksBackofficePermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasAny($user, ['landing.manage', 'settings.manage']);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAny($user, ['landing.manage']);
    }

    public function update(User $user): bool
    {
        return $this->hasAny($user, ['landing.manage', 'settings.manage']);
    }

    public function delete(User $user): bool
    {
        return $this->hasAny($user, ['landing.manage']);
    }

    public function deleteAny(User $user): bool
    {
        return $this->delete($user);
    }
}

