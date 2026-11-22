<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kurye alımı kapalı (Yılmaz kararı, 2026-11-09): sitede kurye başvurusuna giden hiçbir bağlantı,
 * form veya iş ilanı şeması görünmez; /kurye-basvuru 410 + noindex döner ve sitemap/llms.txt'te yer almaz.
 */
class KuryeAlimiKapaliTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_have_no_courier_application_link(): void
    {
        foreach (['/', '/hakkimizda', '/hizmetler', '/kurumsal', '/iletisim', '/sss'] as $yol) {
            $html = $this->get($yol)->assertStatus(200)->getContent();
            $this->assertStringNotContainsString('/kurye-basvuru', $html, $yol);
            $this->assertStringNotContainsString('Kurye Başvurusu', $html, $yol);
            $this->assertStringNotContainsString('JobPosting', $html, $yol);
        }
    }

    public function test_courier_application_page_is_gone_and_noindex(): void
    {
        $html = $this->get('/kurye-basvuru')->assertStatus(410)->getContent();
        $this->assertStringContainsString('noindex', $html);
        $this->assertStringNotContainsString('JobPosting', $html);
        $this->assertStringNotContainsString('submitCourierForm', $html);
    }

    public function test_sitemap_and_llms_do_not_list_courier_application(): void
    {
        $sitemap = $this->get('/sitemap.xml')->assertStatus(200)->getContent();
        $this->assertStringNotContainsString('/kurye-basvuru', $sitemap);
        $this->assertStringNotContainsString('/kurye-basvuru', file_get_contents(public_path('llms.txt')));
    }
}
