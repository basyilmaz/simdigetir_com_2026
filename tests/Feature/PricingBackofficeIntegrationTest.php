<?php

namespace Tests\Feature;

use App\Domain\Pricing\Services\PricingServiceCatalog;
use App\Filament\Resources\PricingRuleResource;
use App\Models\PricingRule;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Tests\TestCase;

class PricingBackofficeIntegrationTest extends TestCase
{
    public function test_pricing_service_catalog_prefers_active_service_base_price_rules(): void
    {
        PricingRule::query()->create([
            'name' => 'Servis Baz Fiyati - Moto',
            'rule_type' => PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE,
            'priority' => 20,
            'conditions' => ['service_type' => 'moto'],
            'effect' => [
                'type' => 'set_base_amount',
                'service_label' => 'Moto Kurye',
                'base_amount' => 27500,
                'fallback_minutes' => 40,
                'is_default' => true,
            ],
            'is_active' => true,
        ]);

        PricingRule::query()->create([
            'name' => 'Servis Baz Fiyati - Van',
            'rule_type' => PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE,
            'priority' => 30,
            'conditions' => ['service_type' => 'van'],
            'effect' => [
                'type' => 'set_base_amount',
                'service_label' => 'Aracli Kurye',
                'base_amount' => 49000,
                'fallback_minutes' => 75,
                'is_default' => false,
            ],
            'is_active' => true,
        ]);

        $catalog = app(PricingServiceCatalog::class)->getQuoteServiceOptions([
            ['value' => 'legacy', 'label' => 'Legacy', 'base_amount' => 10000, 'fallback_minutes' => 15],
        ]);

        $this->assertCount(2, $catalog);
        $this->assertSame('moto', $catalog[0]['value']);
        $this->assertSame('Moto Kurye', $catalog[0]['label']);
        $this->assertSame(27500, $catalog[0]['base_amount']);
        $this->assertSame('Aracli Kurye', $catalog[1]['label']);
    }

    public function test_landing_home_prefers_pricing_catalog_over_landing_quote_widget_fallback_options(): void
    {
        $page = LandingPage::create([
            'slug' => 'home',
            'title' => 'Home',
            'is_active' => true,
        ]);

        LandingPageSection::create([
            'page_id' => $page->id,
            'key' => 'hero',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
            'payload' => [
                'quote_widget_service_options' => [
                    [
                        'value' => 'legacy',
                        'label' => 'Legacy Widget Service',
                        'base_amount' => 99900,
                        'fallback_minutes' => 10,
                    ],
                ],
            ],
        ]);

        PricingRule::query()->create([
            'name' => 'Servis Baz Fiyati - Moto',
            'rule_type' => PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE,
            'priority' => 10,
            'conditions' => ['service_type' => 'moto'],
            'effect' => [
                'type' => 'set_base_amount',
                'service_label' => 'Panel Moto Kurye',
                'base_amount' => 26500,
                'fallback_minutes' => 42,
                'is_default' => true,
            ],
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Panel Moto Kurye');
        $response->assertDontSee('Legacy Widget Service');
        $response->assertSee('26500');
    }

    public function test_pricing_rule_resource_maps_service_base_price_fields_for_admin_form(): void
    {
        $data = PricingRuleResource::mutateFormDataBeforeSave([
            'name' => 'Servis Baz Fiyati - Moto',
            'rule_type' => PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE,
            'priority' => 10,
            'service_type_key' => 'moto',
            'service_label' => 'Moto Kurye',
            'service_base_price_try' => '275.50',
            'fallback_minutes' => 38,
            'service_is_default' => true,
            'is_active' => true,
        ]);

        $this->assertSame(['service_type' => 'moto'], $data['conditions']);
        $this->assertSame('Moto Kurye', $data['effect']['service_label']);
        $this->assertSame(27550, $data['effect']['base_amount']);
        $this->assertSame(38, $data['effect']['fallback_minutes']);
        $this->assertTrue($data['effect']['is_default']);

        $filled = PricingRuleResource::mutateFormDataBeforeFill($data);

        $this->assertSame('moto', $filled['service_type_key']);
        $this->assertSame('Moto Kurye', $filled['service_label']);
        $this->assertSame('275.50', $filled['service_base_price_try']);
        $this->assertSame(38, $filled['fallback_minutes']);
        $this->assertTrue($filled['service_is_default']);
    }
}
