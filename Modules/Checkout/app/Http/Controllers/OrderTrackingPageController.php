<?php

namespace Modules\Checkout\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Modules\Checkout\Services\CheckoutContentResolver;
use Modules\Checkout\Services\PublicOrderTrackingService;

class OrderTrackingPageController extends Controller
{
    public function show(
        Request $request,
        PublicOrderTrackingService $trackingService,
        CheckoutContentResolver $contentResolver
    ): View
    {
        $lookupSubmitted = $request->filled('order_no') || $request->filled('phone');
        $tracking = null;
        $lookupError = null;

        if ($lookupSubmitted) {
            $validator = Validator::make($request->only('order_no', 'phone'), [
                'order_no' => ['required', 'string', 'max:40'],
                'phone' => ['required', 'string', 'max:30'],
            ]);

            if ($validator->fails()) {
                $lookupError = $validator->errors()->first();
            } else {
                $tracking = $trackingService->lookup(
                    orderNo: (string) $request->string('order_no'),
                    phone: (string) $request->string('phone')
                );

                if (! $tracking) {
                    $lookupError = 'Siparis kaydi bulunamadi veya telefon dogrulamasi eslesmedi.';
                }
            }
        }

        return view('checkout::tracking', [
            'lookupSubmitted' => $lookupSubmitted,
            'lookupError' => $lookupError,
            'tracking' => $tracking,
            'prefillOrderNo' => (string) $request->query('order_no', ''),
            'prefillPhone' => (string) $request->query('phone', ''),
            'pageCopy' => $contentResolver->trackingCopy(),
            'support' => $contentResolver->supportChannels(),
        ]);
    }
}
