<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Auto-authenticate a super-admin user for API tests.
     * Individual tests can override this by calling Sanctum::actingAs() themselves.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions so tests behave like production.
        $this->artisan('db:seed', ['--class' => RolePermissionSeeder::class]);

        $user = User::factory()->create(['name' => 'Test Admin', 'email' => 'testadmin@simdigetir.test']);
        $user->assignRole('super-admin');

        Sanctum::actingAs($user, ['*']);
    }
}
