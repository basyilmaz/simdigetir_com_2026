<?php

namespace App\Domain\Payments\Services;

class PaymentProviderRegistry
{
    public function defaultProvider(): string
    {
        return strtolower(trim((string) config('payments.default_provider', 'mockpay')));
    }

    public function defaultProviderLabel(): string
    {
        return $this->providerLabel($this->defaultProvider());
    }

    public function providerLabel(string $provider): string
    {
        return match (strtolower(trim($provider))) {
            'paytr' => 'PAYTR',
            'iyzico' => 'Iyzico',
            'mockpay' => 'MockPay',
            default => strtoupper(trim($provider)),
        };
    }

    public function supportsCardCheckout(string $provider): bool
    {
        return in_array(strtolower(trim($provider)), ['paytr', 'iyzico'], true);
    }

    public function isCardCheckoutReady(?string $provider = null): bool
    {
        $provider = strtolower(trim((string) ($provider ?: $this->defaultProvider())));

        if (! $this->supportsCardCheckout($provider)) {
            return false;
        }

        return match ($provider) {
            'paytr' => $this->hasFilledConfig("payments.providers.$provider", [
                'merchant_id',
                'merchant_key',
                'merchant_salt',
            ]),
            'iyzico' => $this->hasFilledConfig("payments.providers.$provider", [
                'api_key',
                'secret_key',
            ]),
            default => false,
        };
    }

    private function hasFilledConfig(string $prefix, array $keys): bool
    {
        foreach ($keys as $key) {
            if (trim((string) config("$prefix.$key", '')) === '') {
                return false;
            }
        }

        return true;
    }
}
