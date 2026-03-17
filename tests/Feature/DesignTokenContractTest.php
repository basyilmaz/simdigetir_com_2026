<?php

namespace Tests\Feature;

use App\Models\PricingQuote;
use Modules\Checkout\Models\CheckoutSession;
use Tests\TestCase;

class DesignTokenContractTest extends TestCase
{
    public function test_home_page_renders_shared_design_tokens(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="sg-design-tokens"', false);
        $response->assertSee('--sg-brand-primary', false);
        $response->assertSee('--sg-glass-surface-strong', false);
        $response->assertSee('--sg-font-display', false);
        $response->assertSee('--sg-type-display-xl', false);
        $response->assertSee('font-family: var(--sg-font-body);', false);
        $response->assertSee('font-size: var(--sg-type-display-xl);', false);
        $response->assertSee('font-size: var(--sg-type-caption);', false);
    }

    public function test_checkout_pages_use_shared_design_tokens_for_surfaces(): void
    {
        $entryResponse = $this->get('/checkout');
        $entryResponse->assertOk();
        $entryResponse->assertSee('id="sg-design-tokens"', false);
        $entryResponse->assertSee('var(--sg-surface-page-light)', false);

        $quote = PricingQuote::query()->create([
            'quote_no' => 'QTE-TOKEN-001',
            'request_snapshot' => [],
            'resolved_rules' => [],
            'subtotal_amount' => 8400,
            'discount_amount' => 0,
            'surge_amount' => 0,
            'total_amount' => 8400,
            'currency' => 'TRY',
            'expires_at' => now()->addMinutes(15),
        ]);

        $session = CheckoutSession::query()->create([
            'token' => 'checkout-token-style-001',
            'pricing_quote_id' => $quote->id,
            'status' => 'draft',
            'current_step' => 'quote',
            'payload' => [
                'service_type' => 'moto',
                'pickup' => ['address' => 'Besiktas'],
                'dropoff' => ['address' => 'Sisli'],
            ],
            'expires_at' => now()->addHour(),
        ]);

        $wizardResponse = $this->get('/checkout/'.$session->token);
        $wizardResponse->assertOk();
        $wizardResponse->assertSee('var(--sg-surface-page-dark)', false);
        $wizardResponse->assertSee('var(--sg-card-dark)', false);
        $wizardResponse->assertSee('var(--sg-font-body)', false);
        $wizardResponse->assertSee('var(--sg-type-display-lg)', false);
        $wizardResponse->assertSee('var(--sg-type-caption)', false);
    }
}
