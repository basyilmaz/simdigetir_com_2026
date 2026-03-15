<?php

namespace Modules\Checkout\Services;

use App\Models\Order;

class PublicOrderTrackingService
{
    /**
     * @return array<string, mixed>|null
     */
    public function lookup(string $orderNo, string $phone): ?array
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if ($normalizedPhone === '') {
            return null;
        }

        $order = Order::query()
            ->with([
                'customer',
                'stateLogs' => fn ($query) => $query->orderBy('created_at'),
                'trackingEvents' => fn ($query) => $query->orderBy('created_at'),
                'orderProofs' => fn ($query) => $query->orderBy('created_at'),
            ])
            ->where('order_no', trim($orderNo))
            ->first();

        if (! $order || ! $this->matchesPhone($order, $normalizedPhone)) {
            return null;
        }

        return [
            'order' => [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'state' => $order->state,
                'payment_state' => $order->payment_state,
                'payment_method' => $order->payment_method,
                'total_amount' => (int) $order->total_amount,
                'total_amount_formatted' => $this->formatMoney((int) $order->total_amount, (string) $order->currency),
                'currency' => $order->currency,
                'pickup_name' => $order->pickup_name,
                'pickup_address' => $order->pickup_address,
                'dropoff_name' => $order->dropoff_name,
                'dropoff_address' => $order->dropoff_address,
                'created_at' => optional($order->created_at)?->toIso8601String(),
            ],
            'timeline' => $order->stateLogs->map(fn ($item) => [
                'from_state' => $item->from_state,
                'to_state' => $item->to_state,
                'reason' => $item->reason,
                'created_at' => optional($item->created_at)?->toIso8601String(),
            ])->values()->all(),
            'tracking_events' => $order->trackingEvents->map(fn ($item) => [
                'event_type' => $item->event_type,
                'note' => $item->note,
                'eta_seconds' => $item->eta_seconds,
                'lat' => $item->lat,
                'lng' => $item->lng,
                'created_at' => optional($item->created_at)?->toIso8601String(),
            ])->values()->all(),
            'proofs' => $order->orderProofs->map(fn ($item) => [
                'stage' => $item->stage,
                'proof_type' => $item->proof_type,
                'file_url' => $item->file_url,
                'created_at' => optional($item->created_at)?->toIso8601String(),
            ])->values()->all(),
        ];
    }

    private function matchesPhone(Order $order, string $normalizedPhone): bool
    {
        $phones = [
            $order->pickup_phone,
            $order->dropoff_phone,
            $order->customer?->phone,
        ];

        foreach ($phones as $phone) {
            if ($this->normalizePhone((string) $phone) === $normalizedPhone) {
                return true;
            }
        }

        return false;
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', trim($phone)) ?? '';
    }

    private function formatMoney(int $amount, string $currency): string
    {
        return number_format($amount / 100, 2, ',', '.').' '.strtoupper(trim($currency) !== '' ? $currency : 'TRY');
    }
}
