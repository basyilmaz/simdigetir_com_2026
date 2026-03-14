<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Dispatch\Services\DispatchService;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function autoAssign(Order $order, DispatchService $dispatch): JsonResponse
    {
        $assignment = $dispatch->autoAssign($order, auth()->id());

        return response()->json([
            'success' => $assignment !== null,
            'data' => $assignment,
        ], $assignment ? 201 : 422);
    }

    public function manualAssign(Request $request, Order $order, DispatchService $dispatch): JsonResponse
    {
        $validated = $request->validate([
            'courier_id' => ['required', 'integer', 'exists:couriers,id'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $courier = Courier::query()->findOrFail((int) $validated['courier_id']);
        $assignment = $dispatch->manualAssign($order, $courier, $validated['reason'], auth()->id());

        return response()->json([
            'success' => true,
            'data' => $assignment,
        ], 201);
    }

    public function reassignOverdue(Request $request, DispatchService $dispatch): JsonResponse
    {
        $validated = $request->validate([
            'sla_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
        ]);

        $count = $dispatch->reassignOverdue((int) ($validated['sla_minutes'] ?? 15), auth()->id());

        return response()->json([
            'success' => true,
            'data' => [
                'reassigned_count' => $count,
            ],
        ]);
    }
}

