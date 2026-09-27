<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Teknik SEO / GEO — Dalga 2 (2026-09-27).
 *
 * Denetim (castagent vault/clients/simdigetir/denetim/2026-09-site-denetimi.md §4–§6):
 * anasayfa başlığı Türkçe karaktersizdi, 35 mahalle H1'i yineleniyordu ("Merkez Kurye Hizmeti"),
 * sitemap'te 259 URL'nin lastmod'u her gün "bugün"dü, Organization bilgisi zayıftı, llms.txt yoktu.
 */
class SeoGeoTest extends TestCase
{
    public function test_home_title_and_description_use_turkish_characters(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertStringContainsString('<title>SimdiGetir - İstanbul Hızlı ve Güvenilir Moto Kurye Hizmeti</title>', $html);
        $this->assertStringNotContainsString('Hizli ve Guvenilir', $html);
        $this->assertStringNotContainsString('Hizli ve guvenilir', $html);
    }

    public function test_neighborhood_h1_includes_district_name(): void
    {
        $this->get('/kurye/sisli/mecidiyekoy')->assertStatus(200)
            ->assertSee('Mecidiyeköy, Şişli Kurye Hizmeti', false);
    }

    public function test_sitemap_has_unique_urls_and_no_blanket_today_lastmod(): void
    {
        $xml = $this->get('/sitemap.xml')->assertStatus(200)->getContent();

        preg_match_all('#<loc>([^<]+)</loc>#', $xml, $locs);
        $this->assertGreaterThan(250, count($locs[1]));
        $this->assertSame(count($locs[1]), count(array_unique($locs[1])), 'Sitemap URL yinelemesi');

        preg_match_all('#<lastmod>(\d{4}-\d{2}-\d{2})</lastmod>#', $xml, $dates);
        foreach ($dates[1] as $date) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);
        }
        // Mahalle sayfaları tek bir dosya tarihini paylaşır (şablon + ilçe verisi) — "her URL farklı bugün" değil
        preg_match_all('#<loc>[^<]+/kurye/[^/<]+/[^<]+</loc>\s*<lastmod>([^<]+)</lastmod>#', $xml, $nb);
        $this->assertNotEmpty($nb[1]);
        $this->assertCount(1, array_unique($nb[1]));
    }

    public function test_home_structured_data_describes_business_without_invented_fields(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertStringContainsString('"areaServed":{"@type":"City","name":"İstanbul"}', $html);
        $this->assertStringContainsString('"@type":"ContactPoint"', $html);
        $this->assertStringContainsString('"logo":', $html);
        $this->assertStringContainsString('"name":"Araçlı Kurye"', $html);
        $this->assertStringNotContainsString('priceRange', $html);
        $this->assertStringNotContainsString('"streetAddress"', $html);
    }

    public function test_llms_txt_and_robots_for_ai_search(): void
    {
        $llms = file_get_contents(public_path('llms.txt'));
        $this->assertStringStartsWith('# SimdiGetir', $llms);
        $this->assertStringContainsString('+90 551 356 72 92', $llms);
        $this->assertStringContainsString('39 ilçe', $llms);

        $robots = file_get_contents(public_path('robots.txt'));
        foreach (['GPTBot', 'OAI-SearchBot', 'PerplexityBot', 'ClaudeBot', 'Google-Extended', 'Bingbot'] as $bot) {
            $this->assertStringContainsString("User-agent: {$bot}", $robots);
        }
        $this->assertStringContainsString('Sitemap: https://simdigetir.com/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
    }

    public function test_indexnow_key_file_and_dry_run(): void
    {
        $this->assertFileExists(public_path('7e5c755ba185174c6edf63cae475906a.txt'));
        $this->artisan('seo:indexnow', ['--dry-run' => true])->assertExitCode(0);
    }

    public function test_footer_and_navigation_use_turkish_characters(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        foreach (['Tüm hakları saklıdır', 'Araçlı Kurye', 'Şişli Kurye', 'Kurye Çağır', 'Hakkımızda', 'İletişim'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        foreach (['Tum haklari', 'Aracli Kurye', 'Kurye Cagir', '>Hakkimizda<', '>Iletisim<'] as $ascii) {
            $this->assertStringNotContainsString($ascii, $html);
        }
    }
}
