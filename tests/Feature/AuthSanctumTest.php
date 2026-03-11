<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthSanctumTest extends TestCase
{
    public function test_login_returns_token(): void
    {
        $user = User::factory()->create([
            'email' => 'courier@simdigetir.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'courier@simdigetir.test',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['token', 'user' => ['id', 'name', 'email']],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'wrong@simdigetir.test',
            'password' => bcrypt('correct'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'wrong@simdigetir.test',
            'password' => 'incorrect',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/v1/auth/me');
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_protected_endpoints_reject_unauthenticated(): void
    {
        // Reset auth to simulate an unauthenticated request
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/v1/orders');
        $response->assertStatus(401);
    }

    public function test_protected_endpoint_without_json_header_does_not_throw_500(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->get('/api/v1/kpi/overview');

        $response->assertStatus(302)
            ->assertRedirect('/login');
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/v1/auth/logout');
        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_public_endpoints_work_without_auth(): void
    {
        // Health check should always be public
        $response = $this->getJson('/api/v1/ops/health');
        $response->assertOk()->assertJsonPath('success', true);
    }
}
