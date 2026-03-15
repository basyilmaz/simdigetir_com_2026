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
            'phone' => '905551112233',
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

    public function test_register_creates_customer_with_phone_and_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Yeni Musteri',
            'phone' => '0555 123 45 67',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.phone', '905551234567');

        $this->assertDatabaseHas('users', [
            'name' => 'Yeni Musteri',
            'phone' => '905551234567',
            'email' => 'customer+905551234567@simdigetir.local',
        ]);
    }

    public function test_phone_login_returns_token(): void
    {
        User::factory()->create([
            'email' => 'phone@simdigetir.test',
            'phone' => '905551112244',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'phone' => '0555 111 22 44',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.phone', '905551112244');

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

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        User::factory()->create([
            'email' => 'ratelimit@simdigetir.test',
            'password' => bcrypt('correct-password'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->postJson('/api/v1/auth/login', [
                    'email' => 'ratelimit@simdigetir.test',
                    'password' => 'wrong-password',
                ])
                ->assertStatus(422);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->postJson('/api/v1/auth/login', [
                'email' => 'ratelimit@simdigetir.test',
                'password' => 'wrong-password',
            ])
            ->assertStatus(429);
    }

    public function test_register_is_rate_limited_after_too_many_attempts(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.11'])
                ->postJson('/api/v1/auth/register', [
                    'name' => 'Rate Limited User',
                    'phone' => '',
                    'password' => 'short',
                ])
                ->assertStatus(422);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.11'])
            ->postJson('/api/v1/auth/register', [
                'name' => 'Rate Limited User',
                'phone' => '',
                'password' => 'short',
            ])
            ->assertStatus(429);
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
