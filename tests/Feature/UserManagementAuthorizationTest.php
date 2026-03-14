<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Panel;
use Tests\TestCase;

class UserManagementAuthorizationTest extends TestCase
{
    public function test_admin_can_manage_users_but_support_cannot(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $support = User::factory()->create();
        $targetUser = User::factory()->create();

        $admin->assignRole('admin');
        $support->assignRole('support');

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertTrue($admin->can('create', User::class));
        $this->assertTrue($admin->can('update', $targetUser));
        $this->assertTrue($admin->can('delete', $targetUser));
        $this->assertFalse($admin->can('delete', $admin));

        $this->assertFalse($support->can('viewAny', User::class));
        $this->assertFalse($support->can('create', User::class));
    }

    public function test_panel_access_requires_active_backoffice_role(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $panel = new Panel();

        $operationsUser = User::factory()->create(['is_active' => true]);
        $operationsUser->assignRole('operations');
        $this->assertTrue($operationsUser->canAccessPanel($panel));

        $inactiveAdmin = User::factory()->create(['is_active' => false]);
        $inactiveAdmin->assignRole('admin');
        $this->assertFalse($inactiveAdmin->canAccessPanel($panel));

        $courierRoleUser = User::factory()->create(['is_active' => true]);
        $courierRoleUser->assignRole('courier');
        $this->assertFalse($courierRoleUser->canAccessPanel($panel));
    }
}
