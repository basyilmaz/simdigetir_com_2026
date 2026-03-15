<?php

namespace App\Domain\Payments\Gateways;

use App\Domain\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Str;
use RuntimeException;

class PaytrGateway implements PaymentGateway
{
    public function initiate(int $amount, string $currency, array $context = []): array
    {
        $baseUrl = (string) config('payments.providers.paytr.base_url', 'https://www.paytr.com');
        $merchantId = (string) config('payments.providers.paytr.merchant_id', '');
        $merchantKey = (string) config('payments.providers.paytr.merchant_key', '');
        $merchantSalt = (string) config('payments.providers.paytr.merchant_salt', '');
        $sandbox = (bool) config('payments.providers.paytr.sandbox', true);

        if ($baseUrl === '' || $merchantId === '' || $merchantKey === '' || $merchantSalt === '') {
            throw new RuntimeException('PAYTR configuration is incomplete.');
        }

        $merchantOid = (string) ($context['order_no'] ?? ('PAYTR'.now()->format('YmdHis').Str::upper(Str::random(6))));
        $payload = [
            'merchant_id' => $merchantId,
            'merchant_oid' => $merchantOid,
            'payment_amount' => $amount,
            'currency' => strtoupper($currency),
            'callback_url' => (string) ($context['callback_url'] ?? url('/api/v1/payments/callback/paytr')),
        ];

        if ($sandbox) {
            return [
                'provider_reference' => $merchantOid,
                'payment_url' => rtrim($baseUrl, '/').'/odeme/guvenli/'.$merchantOid,
                'request_payload' => [
                    'mode' => 'paytr-sandbox',
                    'payload' => $payload,
                ],
            ];
        }

        throw new RuntimeException('PAYTR live integration is not completed yet.');
    }
}
