# Postdeploy Monitoring Pack v1.2.1 (2026-03-17)

## Scope

This report closes `D-05` for the `v1.2.1` hotfix train after production cutover on `https://simdigetir.com`.

Release identifiers:
- Git commit: `70d61796`
- Git tag: `v1.2.1`
- Active runtime version: `v1.2.1-live.20260317172159`
- Active release directory: `/home/u473759453/domains/simdigetir.com/laravel_release_v1_2_1_poff_20260317_1`

Previous live rollback target:
- `/home/u473759453/domains/simdigetir.com/laravel_release_v1_2_0_poff_20260317_1`

Latest env backup created during cutover:
- `.env.backup.20260317172204`

## Deployment Summary

The `v1.2.1` train was deployed as a clean release artifact instead of reusing the dirty server-side git worktree.

Applied release flow:
1. `v1.2.1` artifact generated from git tag.
2. New clean Hostinger release directory created.
3. `current/.env` copied into the new release.
4. `current/storage` synced into the new release.
5. Composer production install completed on server.
6. Config/route/view caches rebuilt.
7. Atomic cutover executed.
8. Mandatory post-cutover opcache reset succeeded for:
   - `simdigetir.com`
   - `www.simdigetir.com`

## Monitoring Window

Evidence captured on `March 17, 2026` immediately after cutover and again after the `v1.2.1` OG-image hotfix cutover.

This pack records:
- runtime version confirmation,
- public route smoke,
- strict production env smoke,
- asset/meta verification,
- log tail verification,
- rollback references.

## Public Runtime Smoke

Verified responses:
- `https://simdigetir.com/` => `200`
- `https://simdigetir.com/hesabim/giris` => `200`
- `https://simdigetir.com/admin/login` => `200`
- `https://simdigetir.com/siparis-takip` => `200`
- `https://simdigetir.com/hizmetler` => `200`

Runtime content verification:
- footer version renders `v1.2.1-live.20260317172159`
- footer still renders `Powered by castintech`
- `og:image` now resolves to `https://simdigetir.com/images/og-banner.png`
- branded assets respond with `200`:
  - `/images/logo-light.png`
  - `/images/logo-dark.png`
  - `/images/favicon-32x32.png`
  - `/images/og-banner.png`

## Strict Env Smoke

Command:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\run-phase2-live-smoke.ps1 -EnvFile .env.hostinger.production -StrictEnv -ReleaseMode payments_off
```

Result:
- env validation => pass
- release mode => `payments_off`
- payment mode => `PAYMENT_REQUIRED=false`
- payment provider => `mockpay`
- sms provider => `netgsm`

Warnings intentionally still present:
- `PAYMENT_REQUIRED=false. Card provider kontrolleri atlandi.`
- `GOOGLE_MAPS_API_KEY missing/placeholder. Distance fallback aktif olur.`

These warnings are operationally known and are not blockers for the current `payments_off` release mode.

## Log Check

Server-side checks:
- `php artisan --version` on active release => `Laravel Framework 11.48.0`
- recent `storage/logs/laravel.log` tail contains no entries referencing the active release path `laravel_release_v1_2_1_poff_20260317_1`
- recent log tail contains no `16:` or `17:` timestamped application errors after the final cutover window

Observed historical error signature in tail:
- previous `components.design-tokens` error entries belong to old release path:
  - `laravel_release_v1_0_28_poff_20260317_1`

Interpretation:
- no fresh application error evidence was found for the current `v1.2.1` runtime in the inspected tail window
- historical old-release errors remain in the shared log file, but they are not evidence of current runtime failure

## Risk Register

Open operational follow-ups, not release blockers:
1. `GOOGLE_MAPS_API_KEY` is still placeholder; location-critical release gates should use `-RequireMapsKey`.
2. `payments_on_paytr` remains a separate future release after real merchant credentials and callback validation.
3. Courier/support live modules still depend on real production data onboarding.

## GO / NOGO

Decision: `GO`

Reason:
- active runtime version matches deployed hotfix
- critical public/admin/customer pages return `200`
- opcache reset succeeded on both hosts
- strict env smoke passed for the chosen release mode
- no current-release error signature was observed in the inspected log tail

## Rollback Command Shape

If rollback is required, the immediate previous release target is:
- `laravel_release_v1_2_0_poff_20260317_1`

Command:

```bash
cd /home/u473759453/domains/simdigetir.com/current
TARGET_RELEASE=laravel_release_v1_2_0_poff_20260317_1 HOSTS="simdigetir.com,www.simdigetir.com" bash scripts/release/hostinger-atomic-cutover.sh
```
