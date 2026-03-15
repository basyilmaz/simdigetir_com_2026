# Postdeploy Payments-Off Cutover Report (2026-03-15)

## Scope

- Target runtime: `https://simdigetir.com`
- Release mode: `payments_off`
- Deployment date: `2026-03-15`
- Goal: deploy the new customer checkout, tracking, portal, SMS, and PAYTR-ready codebase without activating live card payments

## Release State

- Previous live symlink target: `laravel_release_v1_0_7`
- New live symlink target: `laravel_release_v1_0_7_poff_20260315_1`
- Live runtime version: `APP_VERSION=1.0.7-live.20260315001735`

Remote production env confirmation:

- `PAYMENT_REQUIRED=false`
- `PAYMENT_DEFAULT_PROVIDER=mockpay`
- `SMS_DEFAULT_PROVIDER=netgsm`
- `NETGSM_SANDBOX=false`

## Deployment Steps Executed

1. Opened new release directory on Hostinger by copying current live release.
2. Overlaid updated runtime code from local workspace:
   - `app`
   - `Modules`
   - `config`
   - `database`
   - `resources`
   - `routes`
   - `scripts`
   - `composer.json`
   - `modules_statuses.json`
   - `VERSION`
3. Ran remote release preparation:
   - `composer install --no-dev --optimize-autoloader`
   - `php artisan optimize:clear`
   - `php artisan migrate --force`
   - `php scripts/version/stamp-env-version.php --env=.env --channel=live`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
   - `php artisan optimize`
4. Ran atomic cutover:
   - switched `current` symlink to `laravel_release_v1_0_7_poff_20260315_1`
   - synced `public/` into `public_html`
   - executed opcache reset for:
     - `simdigetir.com`
     - `www.simdigetir.com`

## Evidence

Pre-deploy gate:

- `storage/app/qa/hostinger-preflight/2026-03-15-030622/report.json`
- Result: `GO`

Post-deploy HTTP smoke:

- `storage/app/qa/http-runtime-smoke/2026-03-15-postdeploy/report.json`
- Result summary:
  - `/` -> `200`
  - `/hakkimizda` -> `200`
  - `/hizmetler` -> `200`
  - `/kurumsal` -> `200`
  - `/iletisim` -> `200`
  - `/sss` -> `200`
  - `/kurye-basvuru` -> `200`
  - `og:image` -> `https://simdigetir.com/images/og-default.jpg`

Post-deploy mobile regression:

- `storage/app/qa/mobile-regression/2026-03-15-postdeploy/report.json`
- Result summary:
  - audited viewports: `375`, `390`, `768`, `1024`
  - all audited pages returned `200`
  - `hasHorizontalOverflow=false` on audited results
  - no broken images detected

Additional live probes:

- `GET /siparis-takip` -> `200`
- `GET /hesabim/giris` -> `200`
- `GET /admin/login` -> `200`
- `POST /api/v1/auth/login` with empty JSON -> `422`
- `GET /api/v1/ops/health` -> `200`
- `GET /api/v1/kpi/overview` -> `302`
- `GET /admin/orders` -> `302`
- `GET /admin/ad-campaigns` -> `302`

Checkout smoke:

- `POST /api/v1/checkout-sessions` -> `201`
- `GET /checkout/{token}` for created session -> `200`

Branding/version smoke:

- footer contains `Powered by castintech`
- footer contains runtime version `v1.0.7-live.20260315001735`
- public HTML contains updated phone/WhatsApp number:
  - `0551 356 72 92`
  - `905513567292`

## Database Changes Applied

Applied migrations:

- `2026_03_14_205000_add_phone_to_users_table`
- `2026_03_14_210000_create_checkout_sessions_table`
- `2026_03_14_220000_add_checkout_payment_fields_to_orders_table`
- `2026_03_14_221000_create_order_proofs_table`

## Decision

- Postdeploy decision: `GO`

Reason:

- public landing pages are healthy
- admin/API parity is restored
- checkout/tracking/customer portal routes are live
- SMS runtime remains in `netgsm` mode
- payment mode remains intentionally disabled (`mockpay`)

## Known Remaining Risk

1. PAYTR is not commercially activated yet.
   - This release is intentionally `payments_off`.
   - Live PAYTR initiation/callback smoke remains a separate release.
2. Mobile regression report still lists some off-screen decorative/marquee elements as offenders.
   - Current measurement still reports `hasHorizontalOverflow=false`.
   - Treat as UI polish, not release blocker.

## Rollback Reference

If rollback is needed:

- previous live folder remains available: `laravel_release_v1_0_7`
- atomic rollback command shape:

```bash
cd /home/u473759453/domains/simdigetir.com
TARGET_RELEASE=laravel_release_v1_0_7 HOSTS="simdigetir.com,www.simdigetir.com" bash laravel_release_v1_0_7_poff_20260315_1/scripts/release/hostinger-atomic-cutover.sh
```
