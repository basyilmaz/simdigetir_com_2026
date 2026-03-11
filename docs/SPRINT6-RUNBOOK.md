# Sprint 6 Runbook

## Go/No-Go Gate

- `php artisan test tests/Feature/Sprint6HardeningAnalyticsTest.php`
- `php artisan test tests/Feature/Sprint5FinanceSupportCorporateTest.php tests/Feature/Sprint4CourierDispatchTest.php tests/Feature/Sprint3PaymentFlowApiTest.php`
- `php artisan migrate:status` must show no pending migrations.
- `/api/v1/ops/health` must return `status=ok`.
- `/api/v1/kpi/overview` must return KPI payload without errors.

## Production Rollback Rehearsal

1. Take DB backup snapshot.
2. Deploy current release to staging.
3. Run all gate tests and smoke routes.
4. Simulate rollback:
5. Restore previous release artifact.
6. Run `php artisan optimize:clear`.
7. Verify `/api/v1/ops/health` and key order/payment flows.
8. Restore current release again and re-verify.

## Runtime Monitoring Checklist

- Watch failed jobs count from `/api/v1/ops/health`.
- Review `admin_audit_logs` for sensitive-field masking compliance.
- Track KPI trend from `/api/v1/kpi/overview` at least daily.
- Alert if quote/order/payment endpoints receive sustained `429` spikes.

## Ads Admin Bilgilendirme

- Admin panel usage page:
  `/admin/ads-platform-guide`
- Detailed operations doc:
  `docs/ops/ads-admin-calisma-bilgilendirmesi-2026-03-11.md`
- Ads release checklist:
  `docs/ops/ads-release-governance-checklist-2026-02-28.md`
