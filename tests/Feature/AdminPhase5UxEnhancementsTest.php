<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentTransactionsRelationManager;
use App\Filament\Resources\CourierResource;
use App\Filament\Resources\CourierResource\RelationManagers\CourierDocumentsRelationManager;
use App\Filament\Resources\LeadResource\Pages\ViewLead;
use App\Filament\Resources\SupportTicketResource;
use App\Filament\Resources\SupportTicketResource\RelationManagers\MessagesRelationManager;
use Carbon\Carbon;
use Modules\Landing\Filament\Resources\LandingPageResource;
use Modules\Landing\Models\LandingPage;
use Modules\Leads\Models\Lead;
use Tests\TestCase;

class AdminPhase5UxEnhancementsTest extends TestCase
{
    public function test_landing_preview_path_mapping_is_resolved_for_known_slugs(): void
    {
        $this->assertSame('/', LandingPageResource::previewPathBySlug('home'));
        $this->assertSame('/hakkimizda', LandingPageResource::previewPathBySlug('about'));
        $this->assertSame('/hizmetler', LandingPageResource::previewPathBySlug('services'));
        $this->assertSame('/iletisim', LandingPageResource::previewPathBySlug('contact'));
        $this->assertSame('/ozel-sayfa', LandingPageResource::previewPathBySlug('ozel-sayfa'));
    }

    public function test_landing_seo_status_badge_logic_returns_expected_labels(): void
    {
        $complete = new LandingPage([
            'slug' => 'about',
            'meta' => [
                'meta_title' => 'Baslik',
                'meta_description' => 'Aciklama',
            ],
        ]);
        $partial = new LandingPage([
            'slug' => 'about',
            'meta' => [
                'meta_title' => 'Sadece Baslik',
            ],
        ]);
        $missing = new LandingPage([
            'slug' => 'about',
            'meta' => [],
        ]);

        $this->assertSame('Tam', LandingPageResource::seoStatusLabel($complete));
        $this->assertSame('Kısmi', LandingPageResource::seoStatusLabel($partial));
        $this->assertSame('Eksik', LandingPageResource::seoStatusLabel($missing));
    }

    public function test_lead_view_timeline_builder_contains_core_activity_items(): void
    {
        $lead = new Lead([
            'type' => 'contact',
            'name' => 'Test Lead',
            'phone' => '05550000000',
            'status' => 'contacted',
            'source' => 'google',
            'campaign' => 'marka',
            'notes' => 'Arama yapildi',
        ]);
        $lead->created_at = Carbon::parse('2026-03-01 10:00:00');
        $lead->updated_at = Carbon::parse('2026-03-01 12:00:00');

        $items = ViewLead::buildTimelineItems($lead);
        $events = collect($items)->pluck('event')->all();

        $this->assertContains('Talep oluşturuldu', $events);
        $this->assertContains('Kaynak bilgisi', $events);
        $this->assertContains('Durum', $events);
        $this->assertContains('Son güncelleme', $events);
    }

    public function test_order_support_and_courier_resources_register_phase5_relation_managers(): void
    {
        $this->assertContains(PaymentTransactionsRelationManager::class, OrderResource::getRelations());
        $this->assertContains(MessagesRelationManager::class, SupportTicketResource::getRelations());
        $this->assertContains(CourierDocumentsRelationManager::class, CourierResource::getRelations());
    }
}
