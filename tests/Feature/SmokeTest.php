<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    /**
     * Test homepage accessibility
     */
    public function test_homepage_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test static pages accessibility
     */
    public function test_static_pages_return_200(): void
    {
        $pages = [
            '/hakkimizda',
            '/hizmetler',
            '/kurumsal',
            '/kurye-basvuru',
            '/iletisim',
            '/sss',
            '/kvkk',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200);
        }
    }

    /**
     * Test dynamic location pages accessibility
     */
    public function test_location_pages_return_200(): void
    {
        // District List
        $response = $this->get('/kurye');
        $response->assertStatus(200);

        // District Detail (Kadıköy is defined in config)
        $response = $this->get('/kurye/kadikoy');
        $response->assertStatus(200);
        $response->assertSee('Kadıköy');

        // Neighborhood Detail (Kadıköy -> Caferağa is defined in config)
        $response = $this->get('/kurye/kadikoy/caferaga');
        $response->assertStatus(200);
        $response->assertSee('Caferağa');
    }

    /**
     * Test sitemap accessibility
     */
    public function test_sitemap_returns_200(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
    }
}
