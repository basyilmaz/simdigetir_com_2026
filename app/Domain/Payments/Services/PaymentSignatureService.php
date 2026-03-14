<?php

namespace App\Domain\Payments\Services;

class PaymentSignatureService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function sign(string $provider, array $payload): string
    {
        $raw = $this->canonicalPayload($provider, $payload);

        return hash_hmac('sha256', $raw, $this->secret($provider));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function verify(string $provider, array $payload, ?string $signature): bool
    {
        if ($signature === null || $signature === '') {
            return false;
        }

        $expected = $this->sign($provider, $payload);

        return hash_equals($expected, $signature);
    }

    private function secret(string $provider): string
    {
        return (string) config('payments.providers.'.$provider.'.secret', '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function canonicalPayload(string $provider, array $payload): string
    {
        $provider = strtolower(trim($provider));
        $reference = (string) ($payload['provider_reference'] ?? $payload['conversationId'] ?? $payload['paymentId'] ?? '');
        $status = (string) ($payload['status'] ?? $payload['paymentStatus'] ?? $payload['result'] ?? '');
        $amount = (string) ($payload['amount'] ?? $payload['paidPrice'] ?? '');

        return match ($provider) {
            'iyzico' => implode('|', [$reference, $status, $amount]),
            default => implode('|', [$reference, $status, $amount]),
        };
    }
}
