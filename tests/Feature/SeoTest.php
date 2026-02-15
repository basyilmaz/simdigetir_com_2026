<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoTest extends TestCase
{
    /**
     * Test home page loads and contains SEO tags.
     */
    public function test_home_page_loads_with_seo_tags()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SimdiGetir');
        $response->assertSee('name="description"', false);
        $response->assertSee('application/ld+json', false);
    }

    /**
     * Test locations index page loads.
     */
    public function test_locations_index_loads()
    {
        $response = $this->get('/kurye');

        $response->assertStatus(200);
        $response->assertSee('İstanbul Kurye Hizmet Bölgeleri');
        $response->assertSee('Avrupa Yakası');
        $response->assertSee('Anadolu Yakası');
    }

    /**
     * Test a specific district page (e.g., Sisli).
     */
    public function test_district_page_loads()
    {
        $response = $this->get('/kurye/sisli');

        $response->assertStatus(200);
        $response->assertSee('Şişli Kurye');
        $response->assertSee('Mecidiyeköy'); // Neighboorhood in Sisli
    }

    /**
     * Test a specific neighborhood page (e.g., Sisli -> Mecidiyekoy).
     */
    public function test_neighborhood_page_loads()
    {
        $response = $this->get('/kurye/sisli/mecidiyekoy');

        $response->assertStatus(200);
        $response->assertSee('Mecidiyeköy Kurye Hizmeti');
        $response->assertSee('Şişli');
    }

    /**
     * Test footer contains location links.
     */
    public function test_footer_contains_location_links()
    {
        $response = $this->get('/');

        $response->assertSee('Hizmet Bölgeleri');
        $response->assertSee('/kurye/sisli');
        $response->assertSee('/kurye/besiktas');
        $response->assertSee('/kurye/kadikoy');
    }

    /**
     * Test sitemap.xml is accessible.
     */
    public function test_sitemap_accessible()
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }
}
