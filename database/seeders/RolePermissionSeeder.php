<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'landing.manage',
            'orders.manage',
            'orders.view',
            'dispatch.manage',
            'couriers.manage',
            'couriers.view',
            'finance.manage',
            'finance.view',
            'support.manage',
            'support.view',
            'settings.manage',
            'reports.view',
            'users.view',
            'users.manage',
            'ads.view',
            'ads.manage',
            'ads.publish',
            'ads.report',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $roleMap = [
            'super-admin' => $permissions,
            'admin' => [
                'landing.manage',
                'orders.manage',
                'dispatch.manage',
                'couriers.manage',
                'finance.view',
                'support.manage',
                'settings.manage',
                'reports.view',
                'users.view',
                'users.manage',
                'ads.view',
                'ads.manage',
                'ads.publish',
                'ads.report',
            ],
            'operations' => [
                'orders.manage',
                'dispatch.manage',
                'couriers.view',
                'support.manage',
                'reports.view',
                'ads.view',
            ],
            'finance' => [
                'finance.manage',
                'finance.view',
                'reports.view',
                'ads.report',
            ],
            'courier' => [
                'orders.view',
                'couriers.view',
            ],
            'support' => [
                'support.manage',
                'support.view',
                'orders.view',
                'ads.view',
            ],
        ];

        foreach ($roleMap as $roleName => $assignedPermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($assignedPermissions);
        }
    }
}
