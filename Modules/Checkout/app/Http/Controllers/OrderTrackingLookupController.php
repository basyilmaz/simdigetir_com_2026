<?php

namespace Modules\Checkout\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Checkout\Services\PublicOrderTrackingService;

class OrderTrackingLookupController extends Controller
{
    public function show(Request $request, PublicOrderTrackingService $trackingService): JsonResponse
    {
        $validated = $request->validate([
            'order_no' => ['required', 'string', 'max:40'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $tracking = $trackingService->lookup(
            orderNo: (string) $validated['order_no'],
            phone: (string) $validated['phone']
        );

        if (! $tracking) {
            return response()->json([
                'success' => false,
                'message' => 'Siparis kaydi bulunamadi veya telefon dogrulamasi eslesmedi.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tracking,
        ]);
    }
}
