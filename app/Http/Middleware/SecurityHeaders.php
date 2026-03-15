<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "base-uri 'self'; frame-ancestors 'self'; object-src 'none'; upgrade-insecure-requests"
        );

        if ($this->isSecureRequest($request)) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        if ($this->shouldPreventIndexing($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }

    private function isSecureRequest(Request $request): bool
    {
        return $request->isSecure()
            || strtolower((string) $request->headers->get('X-Forwarded-Proto')) === 'https'
            || strtolower((string) $request->headers->get('Cf-Visitor')) === '{"scheme":"https"}';
    }

    private function shouldPreventIndexing(Request $request): bool
    {
        return $request->is(
            'admin',
            'admin/*',
            'checkout/*',
            'hesabim',
            'hesabim/*',
            'siparis-takip',
            'musteri-panel',
            'kurye-panel',
            'panel/*'
        );
    }
}
