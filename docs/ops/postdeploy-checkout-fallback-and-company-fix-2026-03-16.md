# Postdeploy Checkout Fallback + Company Name Fix (2026-03-16)

## Scope

This deployment closes two live blockers:

1. Hero quote flow had no resilient bridge to checkout when dynamic session open fails.
2. Corporate lead submissions could lose `company_name` when form schema omitted that field.

## Release Summary

- Previous live symlink: `laravel_release_v1_0_13_poff_20260315_1`
- New live symlink: `laravel_release_v1_0_14_poff_20260316_1`
- Live runtime version: `1.0.14-live.20260316202739`
- Release mode: `payments_off`

## Gate Evidence Before Cutover

- Release prepare + preflight:
  - `powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production`
- Result: `Decision: GO`
- Preflight report:
  - `storage/app/qa/hostinger-preflight/2026-03-16-232430/report.json`

## Deployed Runtime Files

- `resources/views/landing/sections/hero-instant-quote.blade.php`
- `Modules/Checkout/app/Http/Controllers/CheckoutPageController.php`
- `app/Http/Controllers/FormSubmissionController.php`
- `app/Domain/Pricing/Services/PricingServiceCatalog.php`
- Plus related checkout/admin/pricing runtime files already in release scope

## Server Cutover

- Atomic cutover command executed on host:
  - `TARGET_RELEASE=laravel_release_v1_0_14_poff_20260316_1 HOSTS="simdigetir.com,www.simdigetir.com" BASE_DIR=/home/u473759453/domains/simdigetir.com bash scripts/release/hostinger-atomic-cutover.sh`
- `opcache_reset=true` for:
  - `simdigetir.com`
  - `www.simdigetir.com`

## Postdeploy Validation

### 1) Runtime HTTP Smoke

- Report:
  - `storage/app/qa/http-runtime-smoke/2026-03-16-postdeploy-v1_0_14/report.json`
- Result:
  - 7/7 pages `200`
  - OG image check `PASS` (`.jpg`)
  - Footer brand check `PASS` on home

### 2) Mobile Regression

- Report:
  - `storage/app/qa/mobile-regression/2026-03-16-postdeploy-v1_0_14/report.json`
- Result:
  - 28/28 viewport-page samples `PASS`
  - No horizontal overflow blocker
  - No broken image blocker

### 3) Checkout Bridge Verification (Live)

- Request:
  - `GET /checkout?pickup=Besiktas%20Meydan&dropoff=Sisli%20Merkez&service_type=moto&service_label=Moto%20Kurye`
- Result:
  - `302` redirect to tokenized session `/checkout/{token}`
- Followed page contains:
  - `SimdiGetir Checkout`
  - `Besiktas Meydan`
  - `Sisli Merkez`
  - `Moto Kurye`

### 4) Browser E2E Probe (Live)

- Playwright probe result:
  - `quote_result_visible = true`
  - `ctaVisible = true`
  - `fallbackVisible = true`

### 5) Corporate Company Name Persistence (Live)

- Public lead submit executed with:
  - `type=corporate_quote`
  - `company_name=Castintech QA`
- API result: `201 success`
- Live DB check on latest lead:
  - `id|name|company_name = 5|E2E Corp 1773693114979|Castintech QA`

## Decision

- Postdeploy decision: `GO`
- No rollback trigger observed.
