<?php

namespace Modules\Leads\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Modules\AdsCore\Services\ConversionPipelineService;
use Modules\Attribution\Services\AttributionResolver;
use Modules\Leads\Http\Requests\StoreLeadRequest;
use Modules\Leads\Models\Lead;

class LeadApiController extends Controller
{
    public function __construct(
        private readonly ConversionPipelineService $conversionPipeline,
        private readonly AttributionResolver $attributionResolver
    ) {}

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

        $attribution = $this->attributionResolver->fromRequest($request);

        $lead = Lead::create([
            'type' => $request->input('type', 'contact'),
            'name' => $request->input('name'),
            'company_name' => $request->input('company_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
            'source' => $attribution['utm_source'] ?? null,
            'medium' => $attribution['utm_medium'] ?? null,
            'campaign' => $attribution['utm_campaign'] ?? null,
            'term' => $attribution['utm_term'] ?? null,
            'content' => $attribution['utm_content'] ?? null,
            'page_url' => $request->input('page_url'),
            'referrer' => $request->input('referrer'),
            'status' => 'new',
        ]);

        try {
            $this->conversionPipeline->captureLead($lead, [
                'gclid' => $attribution['gclid'] ?? null,
                'fbclid' => $attribution['fbclid'] ?? null,
                'external_id' => $attribution['external_id'] ?? null,
                'client_ip_address' => $request->ip(),
                'client_user_agent' => (string) $request->userAgent(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Lead conversion pipeline failed', [
                'lead_id' => $lead->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Talebiniz başarıyla alındı. En kısa sürede sizinle iletişime geçeceğiz.',
            'data' => [
                'id' => $lead->id,
            ],
        ], 201);
    }
}
