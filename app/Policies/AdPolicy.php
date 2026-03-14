<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksBackofficePermissions;

class AdPolicy
{
    use ChecksBackofficePermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasAny($user, ['ads.view', 'ads.manage', 'ads.report', 'ads.publish']);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasAny($user, ['ads.manage', 'ads.publish']);
    }

    public function update(User $user): bool
    {
        return $this->create($user);
    }

    public function delete(User $user): bool
    {
        return $this->hasAny($user, ['ads.manage']);
    }

    public function deleteAny(User $user): bool
    {
        return $this->delete($user);
    }
}
