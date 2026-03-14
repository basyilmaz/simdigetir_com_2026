<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksBackofficePermissions;

class LeadPolicy
{
    use ChecksBackofficePermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasAny($user, ['support.view', 'support.manage', 'reports.view']);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAny($user, ['support.manage']);
    }

    public function update(User $user): bool
    {
        return $this->hasAny($user, ['support.manage']);
    }

    public function delete(User $user): bool
    {
        return $this->hasAny($user, ['support.manage']);
    }

    public function deleteAny(User $user): bool
    {
        return $this->delete($user);
    }

    public function restore(User $user): bool
    {
        return $this->hasAny($user, ['support.manage']);
    }

    public function restoreAny(User $user): bool
    {
        return $this->restore($user);
    }

    public function forceDelete(User $user): bool
    {
        return $this->hasAny($user, ['support.manage']);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->forceDelete($user);
    }
}
