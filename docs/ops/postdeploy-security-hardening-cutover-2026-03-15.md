# Postdeploy Security Hardening Cutover Report
**Date:** 2026-03-15
**Mode:** `payments_off`
**Target:** `https://simdigetir.com`

## Release State

- Previous live release: `laravel_release_v1_0_7_poff_20260315_1`
- New live release: `laravel_release_v1_0_8_poff_20260315_3`
- Live footer/runtime version: `v1.0.8-live.20260315090927`

## Scope Deployed

Security hardening batch deployed:

1. public panel route lock-down
2. auth throttling on login/register
3. global security headers
4. `X-Powered-By` removal
5. Sanctum token expiration
6. restricted CORS origin policy

## Production Env Confirmation

Verified on remote `current/.env`:

- `APP_VERSION=1.0.8-live.20260315090927`
- `PAYMENT_REQUIRED=false`
- `PAYMENT_DEFAULT_PROVIDER=mockpay`
- `SANCTUM_TOKEN_EXPIRATION=1440`
- `CORS_ALLOWED_ORIGINS=https://simdigetir.com`

## Postdeploy Validation

### Security Behavior

- `GET /musteri-panel` -> `302 /hesabim`
- `GET /panel/customer/1` -> `302 /hesabim`
- `GET /kurye-panel` -> `302 /admin/login`
- `GET /panel/courier/1` -> `302 /admin/login`

Live auth throttle validation:

- 6 consecutive invalid `POST /api/v1/auth/login`
- result: `422,422,422,422,422,429`

This confirms throttling is active on production.

### Live Header Validation

Observed on `/`:

- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=()`
- `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`
- `X-Powered-By`: absent

Observed on API:

- `Access-Control-Allow-Origin: https://simdigetir.com`
- `Access-Control-Allow-Credentials: true`

### Runtime Smoke

HTTP smoke report:

- [report.json](/C:/YazilimProjeler/simdigetir_com_2026/storage/app/qa/http-runtime-smoke/2026-03-15-security-postdeploy/report.json)
- all audited public pages -> `200`
- `og:image` remains JPG
- footer branding remains present

Mobile regression report:

- [report.json](/C:/YazilimProjeler/simdigetir_com_2026/storage/app/qa/mobile-regression/2026-03-15-security-postdeploy/report.json)
- audited pages returned `200`
- `hasHorizontalOverflow=false`
- no broken images detected

## Operational Notes

Two non-blocking operational issues were observed during remote cutover:

1. stdin script execution inserted a BOM marker before `set -e`
2. `public_html/storage` symlink refresh reported permission denial

Neither blocked release activation:

- live symlink switched successfully
- application served new runtime version
- security behavior validated on production

## Residual Risk

`Content-Security-Policy` on live is still weaker than intended.

Expected application value:

- `base-uri 'self'; frame-ancestors 'self'; object-src 'none'; upgrade-insecure-requests`

Observed live value:

- `upgrade-insecure-requests`

This means:

- CSP is present
- but a server/platform layer is overriding or reducing the app-defined value

Treat this as the remaining follow-up hardening item, not a rollback trigger.

## Decision

- Postdeploy decision: `GO`

Reason:

- critical public panel exposure is closed
- brute-force resistance improved
- live version updated
- security headers materially improved
- CORS narrowed to production origin
- no functional regression in public/runtime smoke
