# Ads Release Governance Checklist

Date: 2026-02-28
Scope: AdsCore, AdsGoogle, AdsMeta, Attribution, Reporting

## Go / No-Go Checks

1. DB migration dry-run on staging succeeds:
   - `ad_adsets`, `ad_ads`, `ad_creatives`, `ad_daily_metrics`
2. Quality gate passes:
   - `./scripts/run-quality-gate.ps1`
3. Token health is green:
   - `php artisan ads:health-check --hours=48`
4. Queue health is green:
   - no dead-letter growth in last 24h
5. RBAC validation complete:
   - `ads.view`, `ads.manage`, `ads.publish`, `ads.report`
6. Conversion idempotency validated:
   - duplicate `external_id` does not resend
7. Reporting filters validated:
   - date/platform/campaign output correct

## Rollback Playbook

1. Stop workers that dispatch ads jobs.
2. Disable publish operations from admin role (`ads.publish` temporary revoke).
3. Rollback latest ads migrations:
   - `php artisan migrate:rollback --path=Modules/AdsCore/database/migrations`
4. Re-run seeders for permissions if needed:
   - `php artisan db:seed --class=RolePermissionSeeder`
5. Validate with:
   - `php artisan ads:health-check`
   - `php artisan test tests/Feature/AdsPlatformFoundationTest.php`

## Operational Alerts

- Trigger alert if `expiring_tokens > 0`.
- Trigger alert if `failed_syncs > 0` in 24h.
- Investigate and rerun failed pushes via queue after credential/token fix.
