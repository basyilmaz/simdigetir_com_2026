<?php

namespace Tests\Unit\Domain;

use App\Domain\Pricing\Support\MoneyBreakdownCalculator;
use PHPUnit\Framework\TestCase;

class MoneyBreakdownCalculatorTest extends TestCase
{
    public function test_calculates_totals_with_non_negative_guard(): void
    {
        $calc = new MoneyBreakdownCalculator();

        $result = $calc->calculate(
            subtotalAmount: 1000,
            discountAmount: 1200,
            surgeAmount: 50
        );

        $this->assertSame(1000, $result['subtotal_amount']);
        $this->assertSame(1200, $result['discount_amount']);
        $this->assertSame(50, $result['surge_amount']);
        $this->assertSame(0, $result['total_amount']);
    }

    public function test_forced_total_overrides_formula(): void
    {
        $calc = new MoneyBreakdownCalculator();
        $result = $calc->calculate(1000, 100, 200, 777);

        $this->assertSame(777, $result['total_amount']);
    }
}

