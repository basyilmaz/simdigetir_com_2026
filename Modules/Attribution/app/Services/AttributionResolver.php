<?php

namespace Modules\Attribution\Services;

use Illuminate\Http\Request;

class AttributionResolver
{
    public function fromRequest(Request $request): array
    {
        $source = $request->input('utm_source');
        $medium = $request->input('utm_medium');
        $campaign = $request->input('utm_campaign');
        $term = $request->input('utm_term');
        $content = $request->input('utm_content');
        $gclid = $request->input('gclid');
        $fbclid = $request->input('fbclid');

        if (($source === null || $source === '') && is_string($request->headers->get('referer'))) {
            $referer = strtolower($request->headers->get('referer', ''));
            if (str_contains($referer, 'google')) {
                $source = 'google';
            } elseif (str_contains($referer, 'facebook') || str_contains($referer, 'instagram')) {
                $source = 'meta';
            }
        }

        if (($medium === null || $medium === '') && ($gclid || $fbclid)) {
            $medium = 'cpc';
        }

        return [
            'utm_source' => $source,
            'utm_medium' => $medium,
            'utm_campaign' => $campaign,
            'utm_term' => $term,
            'utm_content' => $content,
            'gclid' => $gclid,
            'fbclid' => $fbclid,
            'external_id' => $request->input('external_id'),
        ];
    }
}
