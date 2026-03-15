<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Couriers\Services\CourierOrderWorkflowService;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\CourierAvailability;
use App\Models\CourierDocument;
use App\Models\Order;
use App\Models\OrderAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class CourierController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:40'],
            'application_notes' => ['nullable', 'string'],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['required_with:documents', 'string', 'max:40'],
            'documents.*.file_url' => ['required_with:documents', 'string', 'max:500'],
        ]);

        $courier = Courier::query()->create([
            'user_id' => $validated['user_id'] ?? null,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'status' => 'pending',
            'application_notes' => $validated['application_notes'] ?? null,
        ]);

        foreach ((array) ($validated['documents'] ?? []) as $doc) {
            CourierDocument::query()->create([
                'courier_id' => $courier->id,
                'document_type' => $doc['document_type'],
                'file_url' => $doc['file_url'],
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'courier_id' => $courier->id,
                'status' => $courier->status,
            ],
        ], 201);
    }

    public function setAvailability(Request $request, Courier $courier): JsonResponse
    {
        $validated = $request->validate([
            'is_online' => ['required', 'boolean'],
            'zone' => ['nullable', 'string', 'max:80'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        if ($courier->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Sadece onayli kuryeler online olabilir.',
            ], 422);
        }

        $availability = CourierAvailability::query()->updateOrCreate(
            ['courier_id' => $courier->id],
            [
                'is_online' => (bool) $validated['is_online'],
                'zone' => $validated['zone'] ?? null,
                'lat' => $validated['lat'] ?? null,
                'lng' => $validated['lng'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $availability,
        ]);
    }

    public function tasks(Courier $courier): JsonResponse
    {
        $assignments = OrderAssignment::query()
            ->where('courier_id', $courier->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->with('order')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }

    public function accept(Request $request, Courier $courier, Order $order, CourierOrderWorkflowService $workflow): JsonResponse
    {
        $assignment = OrderAssignment::query()
            ->where('order_id', $order->id)
            ->where('courier_id', $courier->id)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Pending assignment bulunamadi.'], 404);
        }

        try {
            $updated = $workflow->accept($assignment, (string) ($request->input('note') ?? ''));
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $updated]);
    }

    public function reject(Request $request, Courier $courier, Order $order, CourierOrderWorkflowService $workflow): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $assignment = OrderAssignment::query()
            ->where('order_id', $order->id)
            ->where('courier_id', $courier->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->latest('id')
            ->first();

        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Assignment bulunamadi.'], 404);
        }

        try {
            $updated = $workflow->reject($assignment, $validated['reason']);
        } catch (RuntimeException|Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $updated]);
    }

    public function pickup(Request $request, Courier $courier, Order $order, CourierOrderWorkflowService $workflow): JsonResponse
    {
        $validated = $request->validate([
            'proof_type' => ['nullable', 'string', 'max:30'],
            'proof_value' => ['nullable', 'string', 'max:255'],
            'file_url' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        $assignment = OrderAssignment::query()
            ->where('order_id', $order->id)
            ->where('courier_id', $courier->id)
            ->where('status', 'accepted')
            ->latest('id')
            ->first();
        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Accepted assignment bulunamadi.'], 404);
        }

        try {
            $updatedOrder = $workflow->pickup(
                assignment: $assignment,
                proofType: $validated['proof_type'] ?? null,
                proofValue: $validated['proof_value'] ?? null,
                fileUrl: $validated['file_url'] ?? null,
                metadata: (array) ($validated['metadata'] ?? [])
            );
        } catch (RuntimeException|Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $updatedOrder]);
    }

    public function deliver(Request $request, Courier $courier, Order $order, CourierOrderWorkflowService $workflow): JsonResponse
    {
        $validated = $request->validate([
            'proof_type' => ['required', 'string', 'max:30'],
            'proof_value' => ['nullable', 'string', 'max:255'],
            'file_url' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        $assignment = OrderAssignment::query()
            ->where('order_id', $order->id)
            ->where('courier_id', $courier->id)
            ->where('status', 'accepted')
            ->latest('id')
            ->first();
        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Accepted assignment bulunamadi.'], 404);
        }

        try {
            $updatedOrder = $workflow->deliver(
                assignment: $assignment,
                proofType: $validated['proof_type'],
                proofValue: $validated['proof_value'] ?? null,
                fileUrl: $validated['file_url'] ?? null,
                metadata: (array) ($validated['metadata'] ?? [])
            );
        } catch (RuntimeException|Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $updatedOrder]);
    }

    public function earningsSummary(Courier $courier): JsonResponse
    {
        $completed = OrderAssignment::query()
            ->where('courier_id', $courier->id)
            ->where('status', 'completed')
            ->with('order')
            ->get();

        $orderCount = $completed->count();
        $gross = (int) $completed->sum(fn (OrderAssignment $a) => (int) ($a->order->total_amount ?? 0));

        return response()->json([
            'success' => true,
            'data' => [
                'courier_id' => $courier->id,
                'completed_orders' => $orderCount,
                'gross_earnings' => $gross,
                'currency' => 'TRY',
            ],
        ]);
    }
}
