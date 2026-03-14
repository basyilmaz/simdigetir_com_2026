<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Pricing\Services\DistanceEtaService;
use App\Domain\Pricing\Services\PricingQuoteResolver;
use App\Http\Controllers\Controller;
use App\Models\PricingQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PricingQuoteController extends Controller
{
    public function store(
        Request $request,
        PricingQuoteResolver $resolver,
        DistanceEtaService $distanceEtaService
    ): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'base_amount' => ['required', 'integer', 'min:0'],
            'zone' => ['nullable', 'string', 'max:40'],
            'hour' => ['nullable', 'integer', 'min:0', 'max:23'],
            'currency' => ['nullable', 'string', 'size:3'],
            'context' => ['nullable', 'array'],
            'pickup' => ['nullable', 'array'],
            'pickup.lat' => ['nullable', 'numeric'],
            'pickup.lng' => ['nullable', 'numeric'],
            'dropoff' => ['nullable', 'array'],
            'dropoff.lat' => ['nullable', 'numeric'],
            'dropoff.lng' => ['nullable', 'numeric'],
            'packages' => ['nullable', 'array'],
        ]);

        $distanceEstimate = $distanceEtaService->estimate(
            pickupLat: isset($validated['pickup']['lat']) ? (float) $validated['pickup']['lat'] : null,
            pickupLng: isset($validated['pickup']['lng']) ? (float) $validated['pickup']['lng'] : null,
            dropoffLat: isset($validated['dropoff']['lat']) ? (float) $validated['dropoff']['lat'] : null,
            dropoffLng: isset($validated['dropoff']['lng']) ? (float) $validated['dropoff']['lng'] : null,
        );

        $context = array_merge(
            (array) ($validated['context'] ?? []),
            [
                'base_amount' => (int) $validated['base_amount'],
                'zone' => $validated['zone'] ?? null,
                'hour' => $validated['hour'] ?? null,
                'currency' => strtoupper((string) ($validated['currency'] ?? 'TRY')),
                'distance_meters' => $distanceEstimate['distance_meters'] ?? null,
                'duration_seconds' => $distanceEstimate['duration_seconds'] ?? null,
            ]
        );

        $resolved = $resolver->resolveFromDatabase($context);
        $quoteNo = 'QTE'.now()->format('YmdHis').Str::upper(Str::random(5));

        $quote = PricingQuote::query()->create([
            'quote_no' => $quoteNo,
            'customer_id' => $validated['customer_id'] ?? null,
            'request_snapshot' => [
                'context' => $context,
                'pickup' => $validated['pickup'] ?? null,
                'dropoff' => $validated['dropoff'] ?? null,
                'packages' => $validated['packages'] ?? null,
            ],
            'resolved_rules' => $resolved['applied_rules'] ?? [],
            'subtotal_amount' => (int) ($resolved['subtotal_amount'] ?? 0),
            'discount_amount' => (int) ($resolved['discount_amount'] ?? 0),
            'surge_amount' => (int) ($resolved['surge_amount'] ?? 0),
            'total_amount' => (int) ($resolved['total_amount'] ?? 0),
            'currency' => (string) ($resolved['currency'] ?? 'TRY'),
            'expires_at' => now()->addMinutes(15),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $quote->id,
                'quote_no' => $quote->quote_no,
                'subtotal_amount' => $quote->subtotal_amount,
                'discount_amount' => $quote->discount_amount,
                'surge_amount' => $quote->surge_amount,
                'total_amount' => $quote->total_amount,
                'currency' => $quote->currency,
                'expires_at' => optional($quote->expires_at)->toISOString(),
                'applied_rules' => $quote->resolved_rules ?? [],
                'distance_meters' => $distanceEstimate['distance_meters'] ?? null,
                'duration_seconds' => $distanceEstimate['duration_seconds'] ?? null,
                'distance_source' => $distanceEstimate['source'] ?? null,
            ],
        ], 201);
    }
}
