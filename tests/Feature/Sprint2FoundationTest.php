<?php

namespace Tests\Feature;

use App\Models\FormDefinition;
use App\Models\LegalDocument;
use App\Models\SitemapEntry;
use Modules\Landing\Models\LandingPage;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class Sprint2FoundationTest extends TestCase
{
    public function test_home_page_uses_admin_controlled_seo_meta_fields(): void
    {
        LandingPage::query()->create([
            'slug' => 'home',
            'title' => 'Anasayfa',
            'is_active' => true,
            'meta' => [
                'meta_title' => 'Custom Home Title',
                'meta_description' => 'Custom home description',
                'meta_keywords' => 'home, custom, seo',
                'robots' => 'noindex, follow',
                'canonical_url' => 'https://simdigetir.test/custom-home',
                'og_title' => 'Custom OG Home',
                'og_description' => 'Custom OG Description',
                'og_image' => 'https://simdigetir.test/og-home.png',
            ],
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
        $response->assertSee('<link rel="canonical" href="https://simdigetir.test/custom-home">', false);
        $response->assertSee('<meta property="og:title" content="Custom OG Home">', false);
        $response->assertSee('<meta name="twitter:title" content="Custom OG Home">', false);
    }

    public function test_dynamic_form_submission_is_validated_and_stored(): void
    {
        $definition = FormDefinition::query()->create([
            'key' => 'contact',
            'title' => 'Contact',
            'schema' => [
                'fields' => [
                    ['name' => 'name', 'type' => 'string', 'required' => true, 'max' => 120],
                    ['name' => 'phone', 'type' => 'string', 'required' => true, 'max' => 30],
                    ['name' => 'email', 'type' => 'email', 'required' => false, 'max' => 120],
                ],
            ],
            'target_type' => 'store_only',
            'rate_limit_per_minute' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/forms/contact/submit', [
            'name' => 'Test User',
            'phone' => '05321234567',
            'email' => 'test@example.com',
            'page_url' => 'https://simdigetir.test/iletisim',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('form_submissions', [
            'form_definition_id' => $definition->id,
            'status' => 'received',
        ]);
    }

    public function test_corporate_quote_form_submission_bootstraps_missing_builtin_definition(): void
    {
        FormDefinition::query()->where('key', 'corporate-quote')->delete();
        Lead::query()->delete();

        $response = $this->postJson('/api/forms/corporate-quote/submit', [
            'type' => 'corporate_quote',
            'name' => 'Bootstrap Corporate',
            'company_name' => 'Castintech',
            'phone' => '05320001122',
            'email' => 'corporate@example.com',
            'message' => 'Bootstrap check',
            'page_url' => 'https://simdigetir.test/kurumsal',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('form_definitions', [
            'key' => 'corporate-quote',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('leads', [
            'type' => 'corporate_quote',
            'name' => 'Bootstrap Corporate',
            'company_name' => 'Castintech',
        ]);
    }

    public function test_legal_document_is_versioned_and_publicly_rendered(): void
    {
        $document = LegalDocument::query()->create([
            'slug' => 'cerez-politikasi',
            'title' => 'Cerez Politikasi',
            'summary' => 'Ilk surum',
            'content' => '<p>v1</p>',
            'is_published' => true,
        ]);

        $this->get('/cerez-politikasi')
            ->assertOk()
            ->assertSee('Cerez Politikasi');

        $document->update([
            'summary' => 'Guncel surum',
            'content' => '<p>v2</p>',
        ]);

        $document->refresh();
        $this->assertSame(2, $document->version);
        $this->assertDatabaseHas('legal_document_versions', [
            'legal_document_id' => $document->id,
            'version' => 1,
        ]);
        $this->assertDatabaseHas('legal_document_versions', [
            'legal_document_id' => $document->id,
            'version' => 2,
        ]);
    }

    public function test_sitemap_uses_admin_overrides_and_includes_legal_pages(): void
    {
        SitemapEntry::query()->create([
            'path' => '/hakkimizda',
            'changefreq' => 'yearly',
            'priority' => 0.2,
            'is_active' => true,
        ]);

        LegalDocument::query()->create([
            'slug' => 'kullanim-kosullari',
            'title' => 'Kullanim Kosullari',
            'summary' => 'Yasal metin',
            'content' => '<p>Yasal metin</p>',
            'is_published' => true,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertSee('/hakkimizda', false);
        $response->assertSee('<changefreq>yearly</changefreq>', false);
        $response->assertSee('<priority>0.2</priority>', false);
        $response->assertSee('/kullanim-kosullari', false);
    }
}
