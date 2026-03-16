<?php

namespace App\Domain\Pricing\Services;

use App\Models\PricingRule;
use Illuminate\Support\Facades\Cache;

class PricingServiceCatalog
{
    public const SERVICE_BASE_PRICE_RULE_TYPE = 'service_base_price';

    public const SERVICE_CATALOG_CACHE_KEY = 'pricing_rules.service_catalog.v1';

    public const SERVICE_CATALOG_CACHE_TTL_SECONDS = 600;

    /**
     * @param  array<int, array<string, mixed>>  $fallbackOptions
     * @return array<int, array<string, mixed>>
     */
    public function getQuoteServiceOptions(array $fallbackOptions = []): array
    {
        $catalog = Cache::remember(
            self::SERVICE_CATALOG_CACHE_KEY,
            self::SERVICE_CATALOG_CACHE_TTL_SECONDS,
            fn (): array => $this->loadActiveServiceRules()
        );

        if ($catalog !== []) {
            return $catalog;
        }

        return $this->normalizeFallbackOptions($fallbackOptions);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fallbackOptions
     * @return array<string, string>
     */
    public function optionMapForSelect(array $fallbackOptions = []): array
    {
        return collect($this->getQuoteServiceOptions($fallbackOptions))
            ->mapWithKeys(fn (array $option): array => [
                (string) $option['value'] => (string) $option['label'],
            ])
            ->all();
    }

    public function resolveBaseAmountForService(string $serviceType, array $fallbackOptions = []): ?int
    {
        $serviceType = trim($serviceType);

        if ($serviceType === '') {
            return null;
        }

        $option = collect($this->getQuoteServiceOptions($fallbackOptions))
            ->first(fn (array $candidate): bool => (string) $candidate['value'] === $serviceType);

        if (! is_array($option)) {
            return null;
        }

        return (int) ($option['base_amount'] ?? 0);
    }

    public function resolveLabelForService(string $serviceType, array $fallbackOptions = []): ?string
    {
        $serviceType = trim($serviceType);

        if ($serviceType === '') {
            return null;
        }

        $option = collect($this->getQuoteServiceOptions($fallbackOptions))
            ->first(fn (array $candidate): bool => (string) $candidate['value'] === $serviceType);

        if (! is_array($option)) {
            return null;
        }

        return (string) ($option['label'] ?? $serviceType);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadActiveServiceRules(): array
    {
        $now = now();

        return PricingRule::query()
            ->where('rule_type', self::SERVICE_BASE_PRICE_RULE_TYPE)
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('active_from')->orWhere('active_from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('active_until')->orWhere('active_until', '>=', $now);
            })
            ->orderBy('priority')
            ->orderBy('id')
            ->get()
            ->map(function (PricingRule $rule): ?array {
                $serviceType = trim((string) data_get($rule->conditions, 'service_type', ''));
                $serviceLabel = trim((string) data_get($rule->effect, 'service_label', ''));
                $baseAmount = (int) data_get($rule->effect, 'base_amount', 0);
                $fallbackMinutes = max(1, (int) data_get($rule->effect, 'fallback_minutes', 45));
                $isDefault = (bool) data_get($rule->effect, 'is_default', false);

                if ($serviceType === '' || $serviceLabel === '' || $baseAmount <= 0) {
                    return null;
                }

                return [
                    'value' => $serviceType,
                    'label' => $serviceLabel,
                    'base_amount' => $baseAmount,
                    'fallback_minutes' => $fallbackMinutes,
                    'is_default' => $isDefault,
                    'priority' => (int) $rule->priority,
                ];
            })
            ->filter()
            ->sortBy(fn (array $option): string => sprintf(
                '%d-%06d-%s',
                $option['is_default'] ? 0 : 1,
                (int) ($option['priority'] ?? 999999),
                (string) ($option['label'] ?? '')
            ))
            ->values()
            ->map(function (array $option): array {
                unset($option['priority']);

                return $option;
            })
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $fallbackOptions
     * @return array<int, array<string, mixed>>
     */
    private function normalizeFallbackOptions(array $fallbackOptions): array
    {
        return collect($fallbackOptions)
            ->map(function ($option): ?array {
                if (! is_array($option)) {
                    return null;
                }

                $value = trim((string) ($option['value'] ?? ''));
                $label = trim((string) ($option['label'] ?? ''));

                if ($value === '' || $label === '') {
                    return null;
                }

                return [
                    'value' => $value,
                    'label' => $label,
                    'base_amount' => max(0, (int) ($option['base_amount'] ?? 0)),
                    'fallback_minutes' => max(1, (int) ($option['fallback_minutes'] ?? 45)),
                    'is_default' => (bool) ($option['is_default'] ?? false),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
