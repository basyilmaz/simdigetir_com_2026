# Security Audit Validation Report
**Date:** 2026-03-15
**Scope:** Source code + live production passive probes (`https://simdigetir.com`)
**Method:** Read-only route/controller review, HTTP header inspection, unauthenticated access checks, benign auth throttling probes

---

## Executive Summary

The original security audit report is directionally correct, but not every item is accurate as written.

Confirmed production-impacting issues:

1. **Critical:** Public customer panel / IDOR exposure is real.
2. **High:** Core browser security headers are missing.
3. **Medium:** Login/register endpoints are not throttled in practice.
4. **Medium:** `X-Powered-By` exposes exact PHP runtime version.
5. **Medium:** Sanctum API tokens have no expiration.
6. **Medium:** API responses allow `Access-Control-Allow-Origin: *`.

Corrected / partial items:

1. `APP_DEBUG=true` is a localhost concern, **not** a live production issue.
2. `Content-Security-Policy` is **present**, but weak.
3. Unauthenticated API requests return:
   - `401 JSON` when `Accept: application/json` is sent
   - `302 -> /login` when browser/default headers are used

---

## Validation Matrix

| Original Finding | Validation Result | Notes |
|---|---|---|
| Public panel routes without auth | Confirmed | Real production issue |
| Debug mode open | Partially correct | Local only; production env is `APP_DEBUG=false` |
| Security headers missing | Confirmed with correction | CSP exists, but only `upgrade-insecure-requests` |
| Login/register throttling missing | Confirmed | Limiter defined, route binding missing |
| `X-Powered-By` exposed | Confirmed | Live shows `PHP/8.3.25` |
| Sanctum token expiration null | Confirmed | `config/sanctum.php` has `expiration => null` |
| Checkout/API CORS concern | Confirmed | Live API responds with `access-control-allow-origin: *` |
| API unauthorized redirect | Partially correct | Depends on `Accept` header |

---

## Confirmed Findings

### 1. Critical: Public customer panel and direct object access
**Code evidence**

- [routes/web.php](/C:/YazilimProjeler/simdigetir_com_2026/routes/web.php)
- [PanelController.php](/C:/YazilimProjeler/simdigetir_com_2026/app/Http/Controllers/PanelController.php)

Relevant behavior:

- `/musteri-panel`
- `/panel/customer/{user}`
- `/kurye-panel`
- `/panel/courier/{courier}`

These routes are not protected by `auth` middleware.

`PanelController::customerDashboard()` and `PanelController::courierDashboard()` do not perform any ownership or authorization checks before returning data.

**Live validation**

- `GET /musteri-panel` -> `200`
- `GET /panel/customer/1` -> `200`
- Rendered page contains customer data summary:
  - `Admin | Aktif Siparis: 0`

This confirms the issue is not theoretical.

**Risk amplifier**

The public customer panel HTML also includes:

- `meta name="robots" content="index, follow"`
- canonical URL for the panel route

That means a sensitive page can be indexed if discovered.

**Priority**

Immediate fix required. This is the most serious issue currently visible.

---

### 2. High: Missing browser security headers
**Live header probe**

`curl -I https://simdigetir.com/`

Observed:

- `strict-transport-security` -> absent
- `x-frame-options` -> absent
- `x-content-type-options` -> absent
- `referrer-policy` -> absent
- `permissions-policy` -> absent
- `content-security-policy` -> present
- `x-powered-by` -> present

**Important correction**

The original report said CSP was missing. That is not accurate on live.

Current CSP:

- `content-security-policy: upgrade-insecure-requests`

This is too weak to count as a complete CSP posture. It does not materially reduce XSS risk by itself.

---

### 3. Medium: Login/register throttling is not enforced
**Code evidence**

- [routes/api.php](/C:/YazilimProjeler/simdigetir_com_2026/routes/api.php)
- [AppServiceProvider.php](/C:/YazilimProjeler/simdigetir_com_2026/app/Providers/AppServiceProvider.php)

`auth-api` limiter is defined in code, but `login` and `register` routes do not use it.

**Live validation**

Seven consecutive invalid requests were sent to each endpoint:

- `POST /api/v1/auth/login`
- `POST /api/v1/auth/register`

Observed results:

- login: `422,422,422,422,422,422,422`
- register: `422,422,422,422,422,422,422`

No `429 Too Many Requests` was triggered.

This confirms the brute-force guard is missing in practice.

---

### 4. Medium: PHP runtime version disclosure
**Live validation**

Response header includes:

- `x-powered-by: PHP/8.3.25`

The original report mentioned a different PHP version. The exact version in the report is stale, but the exposure finding itself is correct.

---

### 5. Medium: Sanctum tokens do not expire
**Code evidence**

- [sanctum.php](/C:/YazilimProjeler/simdigetir_com_2026/config/sanctum.php)

Current value:

```php
'expiration' => null,
```

This means issued tokens remain valid until explicit revocation.

---

### 6. Medium: API responds with wildcard CORS
**Live validation**

API responses include:

- `access-control-allow-origin: *`

Observed on:

- `GET /api/v1/orders` with `Accept: application/json`
- invalid auth requests
- preflight `OPTIONS` request

This is broader than necessary for a production courier/order platform.

---

## Corrected / Non-Production Items

### 7. Debug mode
The original report is correct only for localhost development context.

**Production env check**

- [\.env.hostinger.production](/C:/YazilimProjeler/simdigetir_com_2026/.env.hostinger.production)

Observed:

- `APP_ENV=production`
- `APP_DEBUG=false`

So this is **not** a current live production finding.

---

### 8. API unauthorized behavior
This item needs nuance.

**Live behavior**

- `GET /api/v1/orders` without JSON `Accept` -> `302` to `/login`
- `GET /api/v1/orders` with `Accept: application/json` -> `401` JSON

This is not ideal API ergonomics, but it is not the top security risk compared with the public panel issue.

---

## Positive Checks

These controls were validated as working:

- `GET /admin` -> `302` to `/admin/login`
- `GET /.env` -> `404`
- `GET /.git/config` -> `403`
- `GET /composer.json` -> `404`
- `GET /vendor/composer/installed.json` -> `404`
- `GET /storage/logs/laravel.log` -> `404`
- `GET /phpinfo.php` -> `404`
- `GET /server-status` -> `404`

---

## Recommended Priority Order

### P0
1. Protect or remove public panel routes.
2. Add ownership/policy checks to customer/courier panel controllers.
3. Mark all private panel views `noindex, nofollow` immediately.

### P1
1. Add security headers:
   - `X-Frame-Options: DENY` or `SAMEORIGIN`
   - `X-Content-Type-Options: nosniff`
   - `Strict-Transport-Security`
   - `Referrer-Policy`
   - `Permissions-Policy`
   - stronger CSP
2. Bind `auth-api` throttling to login/register routes.
3. Disable `expose_php`.

### P2
1. Set Sanctum token expiration.
2. Restrict CORS to expected origins.
3. Normalize API unauthenticated behavior toward consistent JSON for API routes.

---

## Final Assessment

The original report identified the right highest-risk area: the public panel routes.

The production security picture is:

- **Not ready** from a security perspective until the panel exposure is closed.
- Otherwise structurally recoverable with a short hardening pass.

If fixes are implemented, the correct order is:

1. panel auth/policy lock
2. security headers
3. auth throttling
4. token/CORS hardening
