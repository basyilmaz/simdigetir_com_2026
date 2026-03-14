<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksBackofficePermissions;

class OrderPolicy
{
    use ChecksBackofficePermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasAny($user, ['orders.view', 'orders.manage']);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAny($user, ['orders.manage']);
    }

    public function update(User $user): bool
    {
        return $this->hasAny($user, ['orders.manage']);
    }

    public function delete(User $user): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

