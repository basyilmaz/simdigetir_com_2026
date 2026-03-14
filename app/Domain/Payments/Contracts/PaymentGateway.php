<?php

namespace App\Domain\Payments\Contracts;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{
     *   provider_reference:string,
     *   payment_url:string,
     *   request_payload:array<string,mixed>
     * }
     */
    public function initiate(int $amount, string $currency, array $context = []): array;
}

