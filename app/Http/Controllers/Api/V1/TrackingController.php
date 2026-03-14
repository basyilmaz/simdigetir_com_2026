<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTrackingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function store(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
            'event_type' => ['required', 'string', 'max:40'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'eta_seconds' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);

        $event = OrderTrackingEvent::query()->create([
            'order_id' => $order->id,
            'courier_id' => $validated['courier_id'] ?? null,
            'event_type' => $validated['event_type'],
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
            'eta_seconds' => $validated['eta_seconds'] ?? null,
            'note' => $validated['note'] ?? null,
            'metadata' => $validated['metadata'] ?? [],
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $event,
        ], 201);
    }
}

