# Live Backend Audit Validation

Date: 2026-03-15
Environment: Production (`simdigetir.com`)
Mode: `payments_off`
Validator: Codex

## Summary

The backend audit batch targeted by [live-backend-audit-report-2026-03-15.md](/C:/YazilimProjeler/simdigetir_com_2026/docs/ops/live-backend-audit-report-2026-03-15.md) is already active on production.

No new cutover was required during this validation pass.

Reason:
- Remote `current` already points to `laravel_release_v1_0_10_poff_20260315_1`
- Remote `.env` already contains `APP_VERSION=1.0.10-live.20260315165738`
- All targeted runtime files match local hashes exactly

## Verified Release State

- Active release symlink: `laravel_release_v1_0_10_poff_20260315_1`
- Runtime version: `v1.0.10-live.20260315165738`
- Payment mode: `PAYMENT_REQUIRED=false`, `PAYMENT_DEFAULT_PROVIDER=mockpay`
- Admin login page: `200`
- Homepage version marker: `v1.0.10-live.20260315165738`

## File Parity Verification

The following production files were hash-compared against local workspace files and all matched:

- `VERSION`
- `app/Filament/Resources/CourierResource.php`
- `app/Filament/Resources/LeadResource.php`
- `app/Filament/Resources/LeadResource/Pages/ViewLead.php`
- `app/Filament/Resources/NotificationTemplateResource.php`
- `app/Filament/Resources/NotificationTemplateResource/Pages/ListNotificationTemplates.php`
- `app/Filament/Resources/OrderResource.php`
- `app/Filament/Resources/OrderResource/RelationManagers/PaymentTransactionsRelationManager.php`
- `app/Filament/Resources/PaymentTransactionResource.php`
- `app/Filament/Resources/PricingRuleResource.php`
- `app/Filament/Resources/SupportTicketResource.php`
- `app/Filament/Widgets/StatsOverview.php`

## Live HTTP Validation

Verified:

- `GET /` -> `200`
- `GET /admin/login` -> `200`
- `GET /musteri-panel` -> `302 /hesabim`
- `GET /panel/customer/1` -> `302 /hesabim`
- `GET /kurye-panel` -> `302 /admin/login`
- `GET /panel/courier/1` -> `302 /admin/login`
- `GET /admin/orders` -> `302 /admin/login`
- `GET /admin/ad-campaigns` -> `302 /admin/login`
- `GET /api/v1/ops/health` -> `200`

## Live Data Probe

Production schema check confirmed:

- `orders` uses `pickup_name`, `dropoff_name`, `total_amount`
- `payment_transactions` uses `amount`, `processed_at`
- `leads` includes `company_name`

Latest sampled production records:

- Orders: most recent rows exist, `total_amount=15000`, state `pending_payment`
- Payment transactions: most recent rows exist, `amount=15000`, provider `mockpay`, `processed_at=null`
- Leads: most recent rows are `contact` leads and have empty `company_name`

Interpretation:

- The live order/payment samples are consistent with minor-unit storage and pending payment records.
- The deployed admin formatting fixes are required and active in code.
- The sampled leads do not contradict the audit fix. They are `contact` type rows, not corporate quote rows.

## Audit Findings Status

Validated as fixed on production code:

- Orders amount formatting fix is deployed
- Payments amount formatting fix is deployed
- Payments processed date fallback logic is deployed
- Orders customer visibility fallback logic is deployed
- Lead activity feed hardening is deployed
- Lead company display semantics fix is deployed
- Dashboard lead card label/description fix is deployed
- Empty-state guidance for Couriers, Pricing Rules, Support Tickets is deployed
- Notification Templates navigation and bootstrap UX fix is deployed

## Residual Risk

One acceptance gap remains:

- Authenticated admin UI was not manually exercised in browser during this pass because no admin credentials were provided in this turn.

This is not a deployment blocker for code parity, but it is still worth closing with a short authenticated smoke:

1. `/admin/orders`
2. `/admin/payment-transactions`
3. `/admin/leads`
4. `/admin/notification-templates`
5. dashboard widgets

## Decision

Status: `GO`

Rationale:

- Production runtime already matches the validated local batch
- Release version is active and visible
- Live route/security behavior is correct
- No parity drift was found between local target files and production current release
