<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksBackofficePermissions;

class AdminAuditLogPolicy
{
    use ChecksBackofficePermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasAny($user, ['reports.view']);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }
}
