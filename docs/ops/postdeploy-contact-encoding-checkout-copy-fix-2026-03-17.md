# Postdeploy Contact Encoding + Checkout Copy Fix (2026-03-17)

## Objective

Close two live UX quality issues safely:

1. Mojibake/encoding corruption on contact page (`/iletisim`).
2. Checkout entry copy quality mismatch (Turkish text quality on `/checkout`).

## Release Summary

- Previous live symlink: `laravel_release_v1_0_15_poff_20260316_1`
- New live symlink: `laravel_release_v1_0_16_poff_20260317_1`
- `VERSION`: `1.0.16`
- Live `APP_VERSION`: `1.0.16-live.20260316214748`
- Release mode: `payments_off`

## Scope Deployed

Uploaded runtime files:

- `VERSION`
- `resources/views/landing/contact.blade.php`
- `Modules/Checkout/resources/views/index.blade.php`

Test-only alignment (local only, not uploaded):

- `tests/Feature/CheckoutPageTest.php`

## Preflight Gate Evidence

Executed:

- `scripts/release/prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production`

Result:

- Decision: `GO`
- Report: `storage/app/qa/hostinger-preflight/2026-03-17-004645/report.json`
- Full quality gate passed before cutover (`206 passed`).

## Cutover Execution

Server-side sequence:

1. Cloned current release to new release folder.
2. Uploaded targeted runtime files.
3. Ran runtime prep on new release:
   - `php scripts/version/stamp-env-version.php --env=.env --channel=live`
   - `php artisan config:clear`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
4. Ran atomic cutover + mandatory opcache reset:
   - `scripts/release/hostinger-atomic-cutover.sh`
   - `opcache_reset=true` for:
     - `simdigetir.com`
     - `www.simdigetir.com`

## Postdeploy Validation

### Runtime smoke (public pages)

- Report: `storage/app/qa/http-runtime-smoke/20260317004822-postdeploy-v1_0_16/report.json`
- Result: `7/7` page checks passed (`200`, OG image `.jpg`, footer branding check pass).

### Live API smoke

Executed:

- `scripts/run-phase2-live-smoke.ps1 -EnvFile .env.hostinger.production -StrictEnv -RunApiSmoke -ReleaseMode payments_off`

Result:

- Completed successfully.
- Smoke order created: `ORD202603170049174ZLQ5`
- Payment provider observed: `MockPay` (expected for `payments_off`).

### Targeted UX checks

Verified on live HTML:

- `/iletisim` contains:
  - `İletişim Kanalları`
  - `Mesaj Gönderin`
- `/checkout` contains:
  - `Siparişe Başla`
  - `Hesap Oluştur`
- Footer/version markers detected:
  - `castintech`
  - `v1.0.16-live.20260316214748`

## Warnings (Non-blocking)

- `PAYMENT_REQUIRED=false` (intentional for payments-off mode).
- `GOOGLE_MAPS_API_KEY` placeholder warning remains in strict env check; distance fallback remains active.

## Decision

- Postdeploy decision: `GO`
