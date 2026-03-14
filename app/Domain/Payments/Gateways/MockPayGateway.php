<?php

namespace App\Domain\Payments\Gateways;

use App\Domain\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Str;

class MockPayGateway implements PaymentGateway
{
    public function initiate(int $amount, string $currency, array $context = []): array
    {
        $providerReference = 'PAY'.now()->format('YmdHis').Str::upper(Str::random(6));

        return [
            'provider_reference' => $providerReference,
            'payment_url' => url('/pay/'.$providerReference),
            'request_payload' => [
                'mode' => 'mock',
                'amount' => $amount,
                'currency' => $currency,
                'context' => $context,
            ],
        ];
    }
}

