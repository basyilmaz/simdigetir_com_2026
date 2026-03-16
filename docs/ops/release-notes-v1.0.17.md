# Release Notes v1.0.17 (2026-03-17)

## Scope

- Checkout entry and customer portal flow hardening.
- Landing quote-to-checkout resilience improvements.
- Admin/ops and pricing management parity updates.
- Tracking and receipt UX improvements.

## Key Changes

1. Landing and checkout
- Added resilient checkout bridge and fallback CTA from hero quote widget.
- Added secondary slide CTA to focus quote module.
- Improved checkout copy consistency and UX messaging.

2. Customer portal and tracking
- Added customer order receipt page and links from dashboard/detail.
- Added tracking auto-refresh controls and countdown.

3. Admin and operations
- Added/updated pricing, lead, order, payment, support and dashboard resources.
- Strengthened form submission/company mapping and pricing catalog defaults.

4. Branding/versioning
- Runtime footer/version aligned with castintech branding policy.
- Version bumped to `1.0.17` and live stamp applied.

## Live Deployment Summary

- Mode: `payments_off`
- Current release: `laravel_release_v1_0_17_poff_20260317_3`
- Live APP_VERSION: `1.0.17-live.20260316225723`
- Cutover: atomic symlink switch + mandatory opcache reset on both hosts.

## Validation Evidence

- Preflight report:
  - `storage/app/qa/hostinger-preflight/2026-03-17-015228/report.json`
- Postdeploy HTTP smoke:
  - `storage/app/qa/http-runtime-smoke/20260317-postdeploy-v1_0_17/report.json`
- Live smoke (strict env + API smoke):
  - order sample: `ORD20260317015837S9AVP`
  - payment provider: `MockPay`

## Known Warnings (non-blocking)

- `PAYMENT_REQUIRED=false` (intentional in payments-off release).
- `GOOGLE_MAPS_API_KEY` placeholder warning remains; distance fallback continues.

## Decision

- GO
