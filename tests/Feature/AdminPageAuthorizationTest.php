<?php

namespace Tests\Feature;

use App\Filament\Pages\AdsPlatformGuide;
use App\Filament\Pages\ManageSettings;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Tests\TestCase;

class AdminPageAuthorizationTest extends TestCase
{
    public function test_manage_settings_page_access_is_limited_to_settings_manage_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $operations = User::factory()->create();
        $operations->assignRole('operations');

        $support = User::factory()->create();
        $support->assignRole('support');

        $this->actingAs($admin);
        $this->assertTrue(ManageSettings::canAccess());

        $this->actingAs($operations);
        $this->assertFalse(ManageSettings::canAccess());

        $this->actingAs($support);
        $this->assertFalse(ManageSettings::canAccess());
    }

    public function test_ads_platform_guide_access_is_limited_to_ads_permissions_or_super_admin(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $operations = User::factory()->create();
        $operations->assignRole('operations');

        $support = User::factory()->create();
        $support->assignRole('support');

        $finance = User::factory()->create();
        $finance->assignRole('finance');

        $courier = User::factory()->create();
        $courier->assignRole('courier');

        $this->actingAs($superAdmin);
        $this->assertTrue(AdsPlatformGuide::canAccess());

        $this->actingAs($admin);
        $this->assertTrue(AdsPlatformGuide::canAccess());

        $this->actingAs($operations);
        $this->assertTrue(AdsPlatformGuide::canAccess());

        $this->actingAs($support);
        $this->assertTrue(AdsPlatformGuide::canAccess());

        $this->actingAs($finance);
        $this->assertTrue(AdsPlatformGuide::canAccess());

        $this->actingAs($courier);
        $this->assertFalse(AdsPlatformGuide::canAccess());
    }
}
