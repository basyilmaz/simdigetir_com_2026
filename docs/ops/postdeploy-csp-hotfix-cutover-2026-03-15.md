# Postdeploy CSP Hotfix Cutover - 2026-03-15

## Scope

- Release mode: `payments_off`
- Purpose: apply live CSP override hotfix and publish new live version stamp
- Release directory: `laravel_release_v1_0_9_poff_20260315_1`
- Previous live release: `laravel_release_v1_0_8_poff_20260315_3`
- New live version: `v1.0.9-live.20260315094020`

## Changes Applied

- Uploaded [public/.htaccess](/C:/YazilimProjeler/simdigetir_com_2026/public/.htaccess) to the new release
- Uploaded [VERSION](/C:/YazilimProjeler/simdigetir_com_2026/VERSION) with `1.0.9`
- Stamped remote `.env` with the new live version
- Rebuilt Laravel caches on the new release
- Performed atomic cutover and opcache reset for:
  - `simdigetir.com`
  - `www.simdigetir.com`

## Remote Cutover Result

- `current -> laravel_release_v1_0_9_poff_20260315_1`
- `opcache_reset=true` for both hosts
- Cutover status: `GO`

## Live Verification

### Version

- Footer now shows `Powered by castintech | v1.0.9-live.20260315094020`

### Security Headers

Validated on `https://simdigetir.com/`:

- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=()`
- `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`
- `Content-Security-Policy: base-uri 'self'; frame-ancestors 'self'; object-src 'none'; upgrade-insecure-requests`
- `X-Powered-By`: not present

### CORS

Validated on `https://simdigetir.com/api/v1/ops/health`:

- Allowed origin response remains `Access-Control-Allow-Origin: https://simdigetir.com`
- Non-allowed origin does not expand to wildcard

### Access Control

- `/musteri-panel` -> `302 /hesabim`
- `/panel/customer/1` -> `302 /hesabim`
- `/kurye-panel` -> `302 /admin/login`
- `/panel/courier/1` -> `302 /admin/login`

### Auth Throttling

Invalid login probe result:

- `422, 422, 422, 422, 422, 429`

## Residual Notes

- `payments_off` mode remains active
- `PAYMENT_REQUIRED=false`
- `PAYMENT_DEFAULT_PROVIDER=mockpay`
- PAYTR activation is still a separate future release

## Decision

- Release decision: `GO`
- Security hotfix status: `Completed`
