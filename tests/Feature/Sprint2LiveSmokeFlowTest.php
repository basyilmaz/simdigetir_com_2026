<?php

namespace Tests\Feature;

use Database\Seeders\Sprint2FoundationSeeder;
use Tests\TestCase;

class Sprint2LiveSmokeFlowTest extends TestCase
{
    public function test_sprint2_seeded_records_exist_and_public_flow_works(): void
    {
        $this->seed(Sprint2FoundationSeeder::class);

        $this->assertDatabaseHas('form_definitions', ['key' => 'contact', 'is_active' => true]);
        $this->assertDatabaseHas('form_definitions', ['key' => 'corporate-quote', 'is_active' => true]);
        $this->assertDatabaseHas('legal_documents', ['slug' => 'cerez-politikasi', 'is_published' => true]);
        $this->assertDatabaseHas('legal_documents', ['slug' => 'kullanim-kosullari', 'is_published' => true]);

        $this->postJson('/api/forms/contact/submit', [
            'type' => 'contact',
            'name' => 'Smoke User',
            'phone' => '05321234567',
            'email' => 'smoke@example.com',
            'subject' => 'Smoke',
            'message' => 'Sprint 2 flow smoke test',
            'page_url' => 'https://simdigetir.test/iletisim',
        ])->assertStatus(201)->assertJsonPath('success', true);

        $this->get('/cerez-politikasi')->assertOk();
        $this->get('/kullanim-kosullari')->assertOk();

        $sitemap = $this->get('/sitemap.xml')->assertOk();
        $sitemap->assertSee('/cerez-politikasi', false);
        $sitemap->assertSee('/kullanim-kosullari', false);
    }
}

