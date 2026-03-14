<?php

namespace App\Domain\Pricing\Support;

class MoneyBreakdownCalculator
{
    /**
     * @return array{subtotal_amount:int,discount_amount:int,surge_amount:int,total_amount:int}
     */
    public function calculate(int $subtotalAmount, int $discountAmount, int $surgeAmount, ?int $forcedTotal = null): array
    {
        $subtotal = max(0, $subtotalAmount);
        $discount = max(0, $discountAmount);
        $surge = max(0, $surgeAmount);

        $total = $forcedTotal ?? max(0, $subtotal + $surge - $discount);
        $total = max(0, $total);

        return [
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'surge_amount' => $surge,
            'total_amount' => $total,
        ];
    }
}

