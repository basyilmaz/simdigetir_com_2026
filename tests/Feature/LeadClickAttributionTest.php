<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Form ↔ reklam tıklaması eşleşmesi — Dalga 4 (2026-09-27).
 *
 * Site formları Lead'e yalnız utm yazıyordu; reklam dönüşüm hattı (ConversionPipelineService) form yolundan
 * hiç çağrılmıyordu. Kurye başvurusu ise /api/leads üzerinden reklam dönüşüm kaydına girebiliyordu.
 * Tıklama kimlikleri yalnız pazarlama onayıyla (Dalga 1 bandı) işlenir.
 */
class LeadClickAttributionTest extends TestCase
{
    private function contactPayload(array $extra = []): array
    {
        return array_merge([
            'type' => 'contact',
            'name' => 'Test Kişi',
            'phone' => '05550000000',
            'message' => 'Deneme mesajı',
            'page_url' => 'https://simdigetir.com/iletisim',
        ], $extra);
    }

    public function test_consented_form_submission_records_ad_event_with_click_ids(): void
    {
        $this->postJson('/api/forms/contact/submit', $this->contactPayload([
            'gclid' => 'gclid-dalga4',
            'gbraid' => 'gbraid-dalga4',
            'consent_marketing' => true,
        ]))->assertStatus(201);

        $event = DB::table('ad_events')->where('gclid', 'gclid-dalga4')->first();
        $this->assertNotNull($event, 'Onaylı form reklam olayı açmalı');
        $this->assertSame('gbraid-dalga4', json_decode($event->payload, true)['gbraid'] ?? null);
        $this->assertDatabaseHas('ad_conversions', ['ad_event_id' => $event->id, 'platform' => 'google', 'status' => 'pending']);
    }

    public function test_form_without_marketing_consent_creates_lead_but_no_ad_event(): void
    {
        $this->postJson('/api/forms/contact/submit', $this->contactPayload([
            'gclid' => 'gclid-onaysiz',
        ]))->assertStatus(201);

        $this->assertDatabaseHas('leads', ['name' => 'Test Kişi', 'type' => 'contact']);
        $this->assertSame(0, DB::table('ad_events')->count());
    }

    public function test_courier_application_never_becomes_ad_conversion(): void
    {
        $this->postJson('/api/leads', [
            'type' => 'courier_application',
            'name' => 'Kurye Aday',
            'phone' => '05551112233',
            'message' => 'Başvuru',
            'gclid' => 'gclid-kurye',
        ])->assertStatus(201);

        // Kurye başvurusu artık kaydediliyor (önceden her başvuru 422 ile reddediliyordu)
        $this->assertDatabaseHas('leads', ['type' => 'courier_apply', 'name' => 'Kurye Aday']);
        $this->assertSame(0, DB::table('ad_events')->count());
    }

    public function test_forms_send_click_ids_only_through_consent_helper(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();
        $this->assertStringContainsString('window.sgTiklamaKimlikleri = function', $html);
        $this->assertStringContainsString("if (!(window.sgOnay && window.sgOnay.pazarlama)) return {};", $html);
        $this->assertStringContainsString("window.sgTiklamaKimlikleri() : {}", $html);

        foreach (['/iletisim', '/kurumsal'] as $page) {
            $this->get($page)->assertStatus(200)->assertSee('window.sgTiklamaKimlikleri() : {}', false);
        }
        // Kurye başvuru formu reklam kimliği göndermez
        $this->get('/kurye-basvuru')->assertStatus(200)->assertDontSee('window.sgTiklamaKimlikleri() : {}', false);
    }
}
