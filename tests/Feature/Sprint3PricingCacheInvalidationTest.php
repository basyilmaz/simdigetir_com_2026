<?php

namespace Tests\Feature;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use App\Models\PricingRule;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class Sprint3PricingCacheInvalidationTest extends TestCase
{
    public function test_pricing_rule_updates_invalidate_active_rules_cache(): void
    {
        Cache::forget(PricingQuoteResolver::ACTIVE_RULES_CACHE_KEY);

        $rule = PricingRule::query()->create([
            'name' => 'Cache Test Rule',
            'rule_type' => 'zone',
            'priority' => 10,
            'conditions' => ['zone' => 'A'],
            'effect' => ['type' => 'add_surge', 'amount' => 200],
            'is_active' => true,
        ]);

        $resolver = app(PricingQuoteResolver::class);
        $withRule = $resolver->resolveFromDatabase([
            'base_amount' => 1000,
            'zone' => 'A',
            'currency' => 'TRY',
        ]);
        $this->assertGreaterThan(0, count($withRule['applied_rules']));

        // Triggers observer -> cache forget
        $rule->update(['is_active' => false]);

        $withoutRule = $resolver->resolveFromDatabase([
            'base_amount' => 1000,
            'zone' => 'A',
            'currency' => 'TRY',
        ]);
        $this->assertCount(0, $withoutRule['applied_rules']);
    }
}

