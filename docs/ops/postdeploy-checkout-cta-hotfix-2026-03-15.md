# Postdeploy Checkout CTA Hotfix Report - 2026-03-15

**Date:** 2026-03-15 22:46 Europe/Istanbul  
**Release:** `laravel_release_v1_0_12_poff_20260315_1`  
**Live Version:** `v1.0.12-live.20260315194536`

## Scope
Fixed the misleading checkout success CTA for completed `cash_on_delivery` orders.

## Root Cause
The checkout success view always rendered the button text `Kart odemesine gec` in static HTML, even when the button was hidden for non-card orders.

This caused the cash-on-delivery success state to expose a misleading payment CTA in snapshots and UI surfaces.

## Code Changes
- [show.blade.php](/C:/YazilimProjeler/simdigetir_com_2026/Modules/Checkout/resources/views/show.blade.php)
  - button label is now rendered only when the finalized order is truly a pending card payment
  - client-side sync now clears button text when card payment CTA should not be shown
- [CheckoutPageTest.php](/C:/YazilimProjeler/simdigetir_com_2026/tests/Feature/CheckoutPageTest.php)
  - added regression test for completed `cash_on_delivery` checkout session

## Validation
### Local
- `php artisan test tests/Feature/CheckoutPageTest.php` -> PASS
- `./scripts/run-quality-gate.ps1` -> PASS
- suite result: `198 passed / 1052 assertions`

### Release Governance
- `prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production` -> GO
- semantic version bumped: `1.0.11 -> 1.0.12`

### Production
Verified after cutover:
- homepage version contains `v1.0.12-live.20260315194536`
- `current -> laravel_release_v1_0_12_poff_20260315_1`
- completed cash checkout page:
  - status `200`
  - `Siparisi takip et` present
  - `>Kart odemesine gec<` not present in rendered HTML

## Decision
**GO**

The misleading checkout success CTA is closed in production.
