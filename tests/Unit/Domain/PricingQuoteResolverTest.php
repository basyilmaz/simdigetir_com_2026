<?php

namespace Tests\Unit\Domain;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use PHPUnit\Framework\TestCase;

class PricingQuoteResolverTest extends TestCase
{
    public function test_resolver_applies_rules_in_priority_order_with_trace(): void
    {
        $resolver = new PricingQuoteResolver();

        $result = $resolver->resolve(
            context: [
                'base_amount' => 1000,
                'zone' => 'A',
                'hour' => 19,
            ],
            rules: [
                [
                    'id' => 2,
                    'name' => 'Zone A Surge',
                    'priority' => 20,
                    'is_active' => true,
                    'conditions' => ['zone' => 'A'],
                    'effect' => ['type' => 'add_surge', 'amount' => 200],
                ],
                [
                    'id' => 1,
                    'name' => 'Peak Hour Discount',
                    'priority' => 10,
                    'is_active' => true,
                    'conditions' => ['hour' => ['min' => 18, 'max' => 23]],
                    'effect' => ['type' => 'add_discount', 'amount' => 100],
                ],
            ]
        );

        $this->assertSame(1000, $result['subtotal_amount']);
        $this->assertSame(200, $result['surge_amount']);
        $this->assertSame(100, $result['discount_amount']);
        $this->assertSame(1100, $result['total_amount']);
        $this->assertCount(2, $result['applied_rules']);
        $this->assertSame('Peak Hour Discount', $result['applied_rules'][0]['rule_name']);
        $this->assertSame('Zone A Surge', $result['applied_rules'][1]['rule_name']);
    }

    public function test_resolver_skips_non_matching_conditions(): void
    {
        $resolver = new PricingQuoteResolver();

        $result = $resolver->resolve(
            context: [
                'base_amount' => 1000,
                'zone' => 'B',
            ],
            rules: [
                [
                    'id' => 10,
                    'name' => 'Only Zone A',
                    'priority' => 10,
                    'is_active' => true,
                    'conditions' => ['zone' => 'A'],
                    'effect' => ['type' => 'add_surge', 'amount' => 300],
                ],
            ]
        );

        $this->assertSame(1000, $result['total_amount']);
        $this->assertCount(0, $result['applied_rules']);
    }
}

