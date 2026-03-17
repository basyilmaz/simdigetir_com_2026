<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReleaseP0ReadinessTest extends TestCase
{
    public function test_home_renders_services_features_stats_and_testimonials_with_defaults(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertGreaterThanOrEqual(3, $this->countMatches('/<div class="service-card">/i', $html));
        $this->assertGreaterThanOrEqual(4, $this->countMatches('/<div class="feature-card">/i', $html));
        $this->assertGreaterThanOrEqual(3, $this->countMatches('/<div class="testimonial-slide">/i', $html));

        preg_match_all('/data-count="(\d+)"/i', $html, $matches);
        $counts = array_map('intval', $matches[1] ?? []);
        $this->assertGreaterThanOrEqual(4, count($counts));
        $this->assertNotContains(0, $counts, 'Stats data-count values should not be zero.');
    }

    public function test_services_page_has_core_sections(): void
    {
        $response = $this->get('/hizmetler');

        $response->assertStatus(200);
        $response->assertSee('id="motorlu-kurye"', false);
        $response->assertSee('id="acil-kurye"', false);
        $response->assertSee('id="aracli-kurye"', false);
    }

    public function test_home_uses_raster_og_image_and_footer_branding(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertSame(1, preg_match('/<meta\s+property="og:image"\s+content="([^"]+)"/i', $html, $ogImageMatch));
        $ogImage = strtolower((string) ($ogImageMatch[1] ?? ''));

        $this->assertStringEndsWith('.png', $ogImage);
        $this->assertStringNotContainsString('.svg', $ogImage);
        $this->assertStringContainsString('https://castintech.com', $html);
        $this->assertStringContainsString('castintech', strtolower($html));
        $this->assertStringContainsString('v'.config('app.version'), $html);
        $this->assertStringContainsString('/siparis-takip', $html);
        $this->assertStringContainsString('/hesabim/giris', $html);
    }

    private function countMatches(string $pattern, string $subject): int
    {
        preg_match_all($pattern, $subject, $matches);

        return count($matches[0] ?? []);
    }
}
