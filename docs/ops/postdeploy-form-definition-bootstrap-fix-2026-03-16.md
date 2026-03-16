# Postdeploy Form Definition Bootstrap Fix (2026-03-16)

## Objective

Make `/api/forms/corporate-quote/submit` permanently operational without relying on frontend fallback to `/api/leads`.

## Root Cause

- Route existed.
- `form_definitions` row for `corporate-quote` could be missing in live DB.
- Controller returned `404 Form tanimi bulunamadi`.

## Permanent Fix

- Added builtin defaults catalog:
  - `app/Support/FormDefinitionDefaults.php`
- Updated form submit controller:
  - if a builtin key (`contact`, `corporate-quote`) is missing, auto `updateOrCreate` definition
  - then continue normal validation + submission flow
- Updated seeder to reuse the same defaults source:
  - `database/seeders/Sprint2FoundationSeeder.php`

## Release Summary

- Previous live symlink: `laravel_release_v1_0_14_poff_20260316_1`
- New live symlink: `laravel_release_v1_0_15_poff_20260316_1`
- Live runtime version: `1.0.15-live.20260316205055`

## Gate Evidence

- Release prepare + preflight decision: `GO`
- Report:
  - `storage/app/qa/hostinger-preflight/2026-03-16-234945/report.json`

## Deployment Notes

- Uploaded files:
  - `VERSION`
  - `app/Support/FormDefinitionDefaults.php`
  - `app/Http/Controllers/FormSubmissionController.php`
  - `database/seeders/Sprint2FoundationSeeder.php`
- Ran:
  - `php artisan migrate --force`
  - `php artisan db:seed --class='Database\\Seeders\\Sprint2FoundationSeeder' --force`
  - `php scripts/version/stamp-env-version.php --env=.env --channel=live`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- Atomic cutover + opcache reset:
  - `simdigetir.com` => `opcache_reset=true`
  - `www.simdigetir.com` => `opcache_reset=true`

## Live Validation

### 1) Direct Forms Endpoint (No Fallback)

- Request: `POST https://simdigetir.com/api/forms/corporate-quote/submit`
- Result: `201`
- Payload response:
  - `success=true`
  - `submission_id` returned

### 2) DB Verification

- Latest lead snapshot:
  - `6|DirectForm 1773694282990|Castintech Direct`
- Confirms `company_name` persisted via direct forms endpoint path.

### 3) Checkout CTA Regression Check

- Browser probe result:
  - `ctaVisible=true`
  - `fallbackVisible=true`

### 4) Runtime Smoke

- Report:
  - `storage/app/qa/http-runtime-smoke/2026-03-16-postdeploy-v1_0_15/report.json`
- Result:
  - `7/7` pages `200`
  - no runtime blocker

## Decision

- Postdeploy decision: `GO`
