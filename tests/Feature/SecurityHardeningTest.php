<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    public function test_public_pages_include_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertHeaderMissing('X-Powered-By')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeader('Content-Security-Policy', "base-uri 'self'; frame-ancestors 'self'; object-src 'none'; upgrade-insecure-requests");
    }

    public function test_private_routes_are_marked_noindex(): void
    {
        $response = $this->get('/hesabim/giris');

        $response->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_sanctum_token_expiration_is_finite(): void
    {
        $this->assertSame(1440, config('sanctum.expiration'));
    }

    public function test_api_cors_allows_known_origin_only(): void
    {
        $allowed = $this->withHeaders([
            'Origin' => 'http://localhost',
        ])->get('/api/v1/ops/health');

        $allowed->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost');

        $blocked = $this->withHeaders([
            'Origin' => 'https://evil.example',
        ])->get('/api/v1/ops/health');

        $blocked->assertOk()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }
}
