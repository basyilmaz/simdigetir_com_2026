<?php

namespace Modules\Leads\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Leads\Http\Requests\StoreLeadRequest;
use Modules\Leads\Models\Lead;

class LeadApiController extends Controller
{
    /**
     * Store a new lead (public endpoint)
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        // Rate limiting: 5 requests per minute per IP
        $key = 'lead-submit:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Çok fazla istek gönderildi. Lütfen {$seconds} saniye bekleyin.",
                'error_code' => 'RATE_LIMIT_EXCEEDED',
            ], 429);
        }
        
        RateLimiter::hit($key, 60);

        $lead = Lead::create([
            'type' => $request->input('type', 'contact'),
            'name' => $request->input('name'),
            'company_name' => $request->input('company_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
            'source' => $request->input('utm_source'),
            'medium' => $request->input('utm_medium'),
            'campaign' => $request->input('utm_campaign'),
            'term' => $request->input('utm_term'),
            'content' => $request->input('utm_content'),
            'page_url' => $request->input('page_url'),
            'referrer' => $request->input('referrer'),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Talebiniz başarıyla alındı. En kısa sürede sizinle iletişime geçeceğiz.',
            'data' => [
                'id' => $lead->id,
            ],
        ], 201);
    }
}
