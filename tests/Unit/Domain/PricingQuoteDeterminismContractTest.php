<?php

namespace Tests\Unit\Domain;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use PHPUnit\Framework\TestCase;

class PricingQuoteDeterminismContractTest extends TestCase
{
    public function test_same_input_produces_same_output_contract(): void
    {
        $resolver = new PricingQuoteResolver();

        $context = [
            'base_amount' => 1250,
            'zone' => 'A',
            'hour' => 20,
            'currency' => 'TRY',
        ];

        $rules = [
            [
                'id' => 11,
                'name' => 'Night Discount',
                'priority' => 10,
                'is_active' => true,
                'conditions' => ['hour' => ['min' => 18, 'max' => 23]],
                'effect' => ['type' => 'add_discount', 'amount' => 100],
            ],
            [
                'id' => 12,
                'name' => 'Zone A Surge',
                'priority' => 20,
                'is_active' => true,
                'conditions' => ['zone' => 'A'],
                'effect' => ['type' => 'add_surge', 'amount' => 200],
            ],
        ];

        $a = $resolver->resolve($context, $rules);
        $b = $resolver->resolve($context, $rules);

        $this->assertSame($a['total_amount'], $b['total_amount']);
        $this->assertSame($a['applied_rules'], $b['applied_rules']);
    }

    public function test_priority_rule_conflict_resolution_contract(): void
    {
        $resolver = new PricingQuoteResolver();

        $result = $resolver->resolve(
            ['base_amount' => 1000, 'currency' => 'TRY'],
            [
                [
                    'id' => 201,
                    'name' => 'Low Priority Set Total',
                    'priority' => 50,
                    'is_active' => true,
                    'conditions' => [],
                    'effect' => ['type' => 'set_total', 'amount' => 900],
                ],
                [
                    'id' => 200,
                    'name' => 'High Priority Surge',
                    'priority' => 10,
                    'is_active' => true,
                    'conditions' => [],
                    'effect' => ['type' => 'add_surge', 'amount' => 300],
                ],
            ]
        );

        // Deterministic order: priority 10 applied before priority 50.
        $this->assertSame('High Priority Surge', $result['applied_rules'][0]['rule_name']);
        $this->assertSame('Low Priority Set Total', $result['applied_rules'][1]['rule_name']);
        // Last set_total wins final output.
        $this->assertSame(900, $result['total_amount']);
    }
}

