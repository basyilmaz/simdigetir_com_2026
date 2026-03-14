<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\OrderStateLog;
use App\Models\PricingQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'state' => ['nullable', 'string', 'max:40'],
            'payment_state' => ['nullable', 'string', 'max:40'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $query = Order::query()->with('packages')->latest('id');

        if (! empty($validated['customer_id'])) {
            $query->where('customer_id', (int) $validated['customer_id']);
        }
        if (! empty($validated['state'])) {
            $query->where('state', $validated['state']);
        }
        if (! empty($validated['payment_state'])) {
            $query->where('payment_state', $validated['payment_state']);
        }
        if (! empty($validated['from'])) {
            $query->whereDate('created_at', '>=', $validated['from']);
        }
        if (! empty($validated['to'])) {
            $query->whereDate('created_at', '<=', $validated['to']);
        }
        if (! empty($validated['q'])) {
            $term = trim((string) $validated['q']);
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('order_no', 'like', '%'.$term.'%')
                    ->orWhere('pickup_name', 'like', '%'.$term.'%')
                    ->orWhere('dropoff_name', 'like', '%'.$term.'%')
                    ->orWhere('pickup_phone', 'like', '%'.$term.'%')
                    ->orWhere('dropoff_phone', 'like', '%'.$term.'%');
            });
        }

        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'pricing_quote_id' => ['nullable', 'integer', 'exists:pricing_quotes,id'],
            'scheduled_at' => ['nullable', 'date'],
            'vehicle_type' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'array'],
            'distance_meters' => ['nullable', 'integer', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'pickup' => ['required', 'array'],
            'pickup.name' => ['nullable', 'string', 'max:255'],
            'pickup.phone' => ['nullable', 'string', 'max:30'],
            'pickup.address' => ['required', 'string', 'max:1000'],
            'pickup.lat' => ['nullable', 'numeric'],
            'pickup.lng' => ['nullable', 'numeric'],
            'dropoff' => ['required', 'array'],
            'dropoff.name' => ['nullable', 'string', 'max:255'],
            'dropoff.phone' => ['nullable', 'string', 'max:30'],
            'dropoff.address' => ['required', 'string', 'max:1000'],
            'dropoff.lat' => ['nullable', 'numeric'],
            'dropoff.lng' => ['nullable', 'numeric'],
            'packages' => ['nullable', 'array'],
            'packages.*.package_type' => ['nullable', 'string', 'max:40'],
            'packages.*.quantity' => ['nullable', 'integer', 'min:1'],
            'packages.*.weight_grams' => ['nullable', 'integer', 'min:0'],
            'packages.*.length_cm' => ['nullable', 'integer', 'min:0'],
            'packages.*.width_cm' => ['nullable', 'integer', 'min:0'],
            'packages.*.height_cm' => ['nullable', 'integer', 'min:0'],
            'packages.*.declared_value_amount' => ['nullable', 'integer', 'min:0'],
            'packages.*.description' => ['nullable', 'string', 'max:255'],
            'packages.*.metadata' => ['nullable', 'array'],
            'subtotal_amount' => ['nullable', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'surge_amount' => ['nullable', 'integer', 'min:0'],
            'total_amount' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'price_breakdown' => ['nullable', 'array'],
        ]);

        $quote = null;
        if (! empty($validated['pricing_quote_id'])) {
            $quote = PricingQuote::query()->find($validated['pricing_quote_id']);
        }

        $amounts = $quote
            ? [
                'subtotal_amount' => (int) $quote->subtotal_amount,
                'discount_amount' => (int) $quote->discount_amount,
                'surge_amount' => (int) $quote->surge_amount,
                'total_amount' => (int) $quote->total_amount,
                'currency' => (string) $quote->currency,
                'price_breakdown' => ['source' => 'quote', 'quote_no' => $quote->quote_no],
            ]
            : [
                'subtotal_amount' => (int) ($validated['subtotal_amount'] ?? 0),
                'discount_amount' => (int) ($validated['discount_amount'] ?? 0),
                'surge_amount' => (int) ($validated['surge_amount'] ?? 0),
                'total_amount' => (int) ($validated['total_amount'] ?? 0),
                'currency' => strtoupper((string) ($validated['currency'] ?? 'TRY')),
                'price_breakdown' => (array) ($validated['price_breakdown'] ?? []),
            ];

        $order = DB::transaction(function () use ($validated, $amounts) {
            $order = Order::query()->create([
                'customer_id' => $validated['customer_id'] ?? null,
                'order_no' => $this->nextOrderNo(),
                'state' => OrderState::Draft->value,
                'payment_state' => 'pending',
                'pickup_name' => $validated['pickup']['name'] ?? null,
                'pickup_phone' => $validated['pickup']['phone'] ?? null,
                'pickup_address' => $validated['pickup']['address'],
                'pickup_lat' => $validated['pickup']['lat'] ?? null,
                'pickup_lng' => $validated['pickup']['lng'] ?? null,
                'dropoff_name' => $validated['dropoff']['name'] ?? null,
                'dropoff_phone' => $validated['dropoff']['phone'] ?? null,
                'dropoff_address' => $validated['dropoff']['address'],
                'dropoff_lat' => $validated['dropoff']['lat'] ?? null,
                'dropoff_lng' => $validated['dropoff']['lng'] ?? null,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'distance_meters' => $validated['distance_meters'] ?? null,
                'duration_seconds' => $validated['duration_seconds'] ?? null,
                'vehicle_type' => $validated['vehicle_type'] ?? null,
                'notes' => $validated['notes'] ?? [],
                'subtotal_amount' => $amounts['subtotal_amount'],
                'discount_amount' => $amounts['discount_amount'],
                'surge_amount' => $amounts['surge_amount'],
                'total_amount' => $amounts['total_amount'],
                'currency' => $amounts['currency'],
                'price_breakdown' => $amounts['price_breakdown'],
            ]);

            foreach ((array) ($validated['packages'] ?? []) as $item) {
                OrderPackage::query()->create([
                    'order_id' => $order->id,
                    'package_type' => $item['package_type'] ?? null,
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'weight_grams' => $item['weight_grams'] ?? null,
                    'length_cm' => $item['length_cm'] ?? null,
                    'width_cm' => $item['width_cm'] ?? null,
                    'height_cm' => $item['height_cm'] ?? null,
                    'declared_value_amount' => $item['declared_value_amount'] ?? null,
                    'description' => $item['description'] ?? null,
                    'metadata' => $item['metadata'] ?? null,
                ]);
            }

            OrderStateLog::query()->create([
                'order_id' => $order->id,
                'from_state' => null,
                'to_state' => OrderState::Draft->value,
                'actor_type' => 'system',
                'actor_id' => null,
                'reason' => 'order_created',
                'metadata' => ['source' => 'api_v1'],
                'created_at' => now(),
            ]);

            return $order;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'state' => $order->state,
                'payment_state' => $order->payment_state,
                'total_amount' => $order->total_amount,
                'currency' => $order->currency,
            ],
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load('packages');

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function timeline(Order $order): JsonResponse
    {
        $items = OrderStateLog::query()
            ->where('order_id', $order->id)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'current_state' => $order->state,
                'timeline' => $items,
            ],
        ]);
    }

    public function transition(Request $request, Order $order, OrderStateTransitionService $service): JsonResponse
    {
        $validated = $request->validate([
            'to_state' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);

        try {
            $toState = OrderState::from($validated['to_state']);
            $updated = $service->transition(
                order: $order,
                toState: $toState,
                actorType: 'api',
                actorId: null,
                reason: $validated['reason'] ?? null,
                metadata: (array) ($validated['metadata'] ?? [])
            );
        } catch (InvalidOrderTransitionException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order transition islemi basarisiz.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $updated->id,
                'order_no' => $updated->order_no,
                'state' => $updated->state,
                'payment_state' => $updated->payment_state,
            ],
        ]);
    }

    private function nextOrderNo(): string
    {
        return 'ORD'.now()->format('YmdHis').Str::upper(Str::random(5));
    }
}
