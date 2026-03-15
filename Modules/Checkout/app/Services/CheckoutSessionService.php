<?php

namespace Modules\Checkout\Services;

use Illuminate\Support\Str;
use Modules\Checkout\Models\CheckoutSession;

class CheckoutSessionService
{
    public function create(array $attributes): CheckoutSession
    {
        return CheckoutSession::query()->create([
            'token' => $this->nextToken(),
            'customer_id' => $attributes['customer_id'] ?? null,
            'pricing_quote_id' => $attributes['pricing_quote_id'] ?? null,
            'status' => (string) ($attributes['status'] ?? 'draft'),
            'current_step' => (string) ($attributes['current_step'] ?? 'quote'),
            'payload' => (array) ($attributes['payload'] ?? []),
            'expires_at' => now()->addMinutes((int) config('checkout.session_ttl_minutes', 1440)),
        ]);
    }

    public function update(CheckoutSession $checkoutSession, array $attributes): CheckoutSession
    {
        $payload = array_replace_recursive(
            (array) ($checkoutSession->payload ?? []),
            (array) ($attributes['payload'] ?? [])
        );

        $checkoutSession->fill([
            'customer_id' => $attributes['customer_id'] ?? $checkoutSession->customer_id,
            'pricing_quote_id' => $attributes['pricing_quote_id'] ?? $checkoutSession->pricing_quote_id,
            'status' => $attributes['status'] ?? $checkoutSession->status,
            'current_step' => $attributes['current_step'] ?? $checkoutSession->current_step,
            'payload' => $payload,
            'expires_at' => now()->addMinutes((int) config('checkout.session_ttl_minutes', 1440)),
        ]);
        $checkoutSession->save();

        return $checkoutSession->refresh();
    }

    private function nextToken(): string
    {
        return Str::lower(Str::random(40));
    }
}
