<?php

namespace Tests\Feature;

use App\Domain\Notifications\Support\NotificationTemplateCatalog;
use App\Filament\Resources\CourierResource;
use App\Filament\Resources\NotificationTemplateResource;
use App\Filament\Resources\PricingRuleResource;
use App\Filament\Resources\SupportTicketResource;
use App\Models\NotificationTemplate;
use ReflectionClass;
use Tests\TestCase;

class LiveBackendAdminAuditP2OpsReadinessTest extends TestCase
{
    public function test_ops_resources_expose_empty_state_guidance(): void
    {
        $this->assertSame('Henüz kurye kaydı yok', CourierResource::emptyStateHeading());
        $this->assertStringContainsString('başvuru', CourierResource::emptyStateDescription());

        $this->assertSame('Henüz fiyat kuralı tanımlanmadı', PricingRuleResource::emptyStateHeading());
        $this->assertStringContainsString('dinamik fiyat kuralları', PricingRuleResource::emptyStateDescription());

        $this->assertSame('Henüz destek talebi yok', SupportTicketResource::emptyStateHeading());
        $this->assertStringContainsString('manuel talep', SupportTicketResource::emptyStateDescription());

        $this->assertSame('Henüz bildirim şablonu yok', NotificationTemplateResource::emptyStateHeading());
        $this->assertStringContainsString('tek tıkla', NotificationTemplateResource::emptyStateDescription());
    }

    public function test_notification_template_resource_bootstrap_is_idempotent_and_catalog_driven(): void
    {
        $expectedCount = count(NotificationTemplateCatalog::defaultSmsTemplates());

        $this->assertSame($expectedCount, NotificationTemplateResource::bootstrapCatalogTemplates());
        $this->assertSame($expectedCount, NotificationTemplate::query()->count());

        $this->assertSame($expectedCount, NotificationTemplateResource::bootstrapCatalogTemplates());
        $this->assertSame($expectedCount, NotificationTemplate::query()->count());
    }

    public function test_notification_template_resource_uses_fixed_turkish_navigation_labels(): void
    {
        $reflection = new ReflectionClass(NotificationTemplateResource::class);

        $navigationLabel = $reflection->getProperty('navigationLabel');
        $navigationLabel->setAccessible(true);

        $pluralModelLabel = $reflection->getProperty('pluralModelLabel');
        $pluralModelLabel->setAccessible(true);

        $this->assertSame('Bildirim Şablonları', $navigationLabel->getValue());
        $this->assertSame('Bildirim Şablonları', $pluralModelLabel->getValue());
    }
}
