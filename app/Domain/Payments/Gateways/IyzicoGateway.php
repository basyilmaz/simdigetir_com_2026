<?php

namespace App\Domain\Payments\Gateways;

use App\Domain\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class IyzicoGateway implements PaymentGateway
{
    public function initiate(int $amount, string $currency, array $context = []): array
    {
        $baseUrl = (string) config('payments.providers.iyzico.base_url');
        $apiKey = (string) config('payments.providers.iyzico.api_key');
        $secretKey = (string) config('payments.providers.iyzico.secret_key');
        $sandbox = (bool) config('payments.providers.iyzico.sandbox', true);

        if ($baseUrl === '' || $apiKey === '' || $secretKey === '') {
            throw new RuntimeException('Iyzico configuration is incomplete.');
        }

        $conversationId = 'IYZ'.now()->format('YmdHis').Str::upper(Str::random(6));
        $payload = [
            'conversationId' => $conversationId,
            'price' => number_format($amount / 100, 2, '.', ''),
            'paidPrice' => number_format($amount / 100, 2, '.', ''),
            'currency' => strtoupper($currency),
            'basketId' => (string) ($context['order_no'] ?? 'basket-'.$conversationId),
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => (string) ($context['callback_url'] ?? url('/api/v1/payments/callback/iyzico')),
        ];

        // This is an initial scaffold. In sandbox mode we avoid hard-failing external environments.
        if ($sandbox) {
            return [
                'provider_reference' => $conversationId,
                'payment_url' => rtrim($baseUrl, '/').'/mock/checkout/'.$conversationId,
                'request_payload' => [
                    'mode' => 'iyzico-sandbox',
                    'payload' => $payload,
                ],
            ];
        }

        $path = '/payment/iyzipos/checkoutform/initialize/auth/ecom';
        $pkIyziRandom = (string) now()->timestamp;
        $response = Http::timeout(10)
            ->withHeaders($this->buildHeaders(
                apiKey: $apiKey,
                secretKey: $secretKey,
                path: $path,
                payload: $payload,
                random: $pkIyziRandom
            ))
            ->post(rtrim($baseUrl, '/').$path, $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Iyzico initiate request failed.');
        }

        $body = (array) $response->json();
        $token = (string) ($body['token'] ?? '');
        if ($token === '') {
            throw new RuntimeException('Iyzico response token missing.');
        }

        return [
            'provider_reference' => $conversationId,
            'payment_url' => rtrim($baseUrl, '/').'/payment/checkoutform/'.$token,
            'request_payload' => [
                'mode' => 'iyzico-live',
                'payload' => $payload,
                'response' => $body,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, string>
     */
    private function buildHeaders(string $apiKey, string $secretKey, string $path, array $payload, string $random): array
    {
        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
        $hashedData = base64_encode(hash('sha256', $random.$path.$payloadJson.$secretKey, true));
        $authorization = base64_encode($apiKey.':'.$random.':'.$hashedData);

        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-iyzi-rnd' => $random,
            'Authorization' => 'IYZWSv2 '.$authorization,
        ];
    }
}
