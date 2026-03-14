<?php

namespace App\Domain\Pricing\Services;

use App\Domain\Pricing\Support\MoneyBreakdownCalculator;
use App\Models\PricingRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class PricingQuoteResolver
{
    public const ACTIVE_RULES_CACHE_KEY = 'pricing_rules.active.v1';

    public const ACTIVE_RULES_CACHE_TTL_SECONDS = 600;

    /**
     * @param  array<string, mixed>  $context
     * @param  iterable<int, array<string, mixed>>  $rules
     * @return array<string, mixed>
     */
    public function resolve(array $context, iterable $rules): array
    {
        $subtotal = (int) ($context['base_amount'] ?? 0);
        $discount = 0;
        $surge = 0;
        $forcedTotal = null;
        $applied = [];

        $orderedRules = collect($rules)
            ->filter(fn ($rule) => is_array($rule))
            ->sortBy([
                fn (array $rule) => (int) ($rule['priority'] ?? 999999),
                fn (array $rule) => (int) ($rule['id'] ?? 0),
            ])
            ->values();

        foreach ($orderedRules as $rule) {
            if (! $this->isRuleActive($rule)) {
                continue;
            }
            if (! $this->matchConditions($context, (array) ($rule['conditions'] ?? []))) {
                continue;
            }

            $effect = (array) ($rule['effect'] ?? []);
            $type = (string) ($effect['type'] ?? '');
            $amount = (int) ($effect['amount'] ?? 0);
            $value = (float) ($effect['value'] ?? 0);

            switch ($type) {
                case 'add_subtotal':
                    $subtotal += $amount;
                    break;
                case 'add_surge':
                    $surge += $amount;
                    break;
                case 'add_discount':
                    $discount += $amount;
                    break;
                case 'set_total':
                    $forcedTotal = $amount;
                    break;
                case 'multiply_total':
                    $currentTotal = max(0, $subtotal + $surge - $discount);
                    $forcedTotal = (int) round($currentTotal * $value);
                    break;
                default:
                    continue 2;
            }

            $applied[] = [
                'rule_id' => (int) ($rule['id'] ?? 0),
                'rule_name' => (string) ($rule['name'] ?? ''),
                'rule_type' => (string) ($rule['rule_type'] ?? ''),
                'priority' => (int) ($rule['priority'] ?? 0),
                'effect' => $effect,
            ];
        }

        $amounts = app(MoneyBreakdownCalculator::class)->calculate(
            subtotalAmount: $subtotal,
            discountAmount: $discount,
            surgeAmount: $surge,
            forcedTotal: $forcedTotal
        );

        return [
            'currency' => (string) ($context['currency'] ?? 'TRY'),
            'subtotal_amount' => $amounts['subtotal_amount'],
            'discount_amount' => $amounts['discount_amount'],
            'surge_amount' => $amounts['surge_amount'],
            'total_amount' => $amounts['total_amount'],
            'applied_rules' => $applied,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function resolveFromDatabase(array $context): array
    {
        $rules = Cache::remember(
            self::ACTIVE_RULES_CACHE_KEY,
            self::ACTIVE_RULES_CACHE_TTL_SECONDS,
            function (): array {
                $now = now();

                return PricingRule::query()
                    ->where('is_active', true)
                    ->where(function ($query) use ($now) {
                        $query->whereNull('active_from')->orWhere('active_from', '<=', $now);
                    })
                    ->where(function ($query) use ($now) {
                        $query->whereNull('active_until')->orWhere('active_until', '>=', $now);
                    })
                    ->get()
                    ->map(fn (PricingRule $rule) => $rule->toArray())
                    ->all();
            }
        );

        return $this->resolve($context, $rules);
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function isRuleActive(array $rule): bool
    {
        if (! (bool) ($rule['is_active'] ?? true)) {
            return false;
        }

        $now = now();
        $activeFrom = ! empty($rule['active_from']) ? Carbon::parse((string) $rule['active_from']) : null;
        $activeUntil = ! empty($rule['active_until']) ? Carbon::parse((string) $rule['active_until']) : null;

        if ($activeFrom && $activeFrom->gt($now)) {
            return false;
        }
        if ($activeUntil && $activeUntil->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $conditions
     */
    private function matchConditions(array $context, array $conditions): bool
    {
        foreach ($conditions as $field => $condition) {
            $value = $context[$field] ?? null;

            if (is_array($condition)) {
                if (array_key_exists('in', $condition) && is_array($condition['in'])) {
                    if (! in_array($value, $condition['in'], true)) {
                        return false;
                    }
                    continue;
                }

                if (array_key_exists('min', $condition) && is_numeric($condition['min'])) {
                    if (! is_numeric($value) || (float) $value < (float) $condition['min']) {
                        return false;
                    }
                }

                if (array_key_exists('max', $condition) && is_numeric($condition['max'])) {
                    if (! is_numeric($value) || (float) $value > (float) $condition['max']) {
                        return false;
                    }
                }

                continue;
            }

            if ($value !== $condition) {
                return false;
            }
        }

        return true;
    }
}
