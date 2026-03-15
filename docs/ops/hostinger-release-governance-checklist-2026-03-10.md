# Hostinger Release Governance Checklist (2026-03-10)

## Scope

- Frontend and backend preflight verification before first Hostinger deployment.
- Decision gate: GO/NOGO.

## Inputs

- Production env template: `.env.hostinger.production.example`
- Preflight script: `scripts/run-hostinger-preflight.ps1`
- Strict env and live smoke script: `scripts/run-phase2-live-smoke.ps1`
- Atomic cutover script: `scripts/release/hostinger-atomic-cutover.sh`
- Post-cutover opcache reset script: `scripts/release/hostinger-opcache-reset.sh`
- Payments-off local gate wrapper: `scripts/release/run-hostinger-payments-off-gate.ps1`
- PAYTR activation local gate wrapper: `scripts/release/run-hostinger-paytr-activation-gate.ps1`
- Payments-off prep wrapper: `scripts/release/prepare-hostinger-payments-off-release.ps1`
- PAYTR activation prep wrapper: `scripts/release/prepare-hostinger-paytr-activation-release.ps1`
- Versioning policy: `docs/ops/versioning-policy.md`
- PAYTR cutover runbook: `docs/ops/hostinger-paytr-cutover-runbook-2026-03-15.md`

## Step 0: Versioning gate

1. Before GitHub push, bump app version:
   - `php scripts/version/bump-version.php --part=patch`
2. Commit and push `VERSION` with code changes.
3. Before live cutover, stamp runtime deploy version:
   - `php scripts/version/stamp-env-version.php --env=.env --channel=live`
4. Rebuild config cache:
   - `php artisan config:clear`
   - `php artisan config:cache`

## Step 1: Prepare production env file

1. Copy template:
   - `cp .env.hostinger.production.example .env.hostinger.production`
2. Fill all real credentials (DB, SMTP, PAYTR, Netgsm, Maps).
3. Keep:
   - `SMS_DEFAULT_PROVIDER=netgsm`
   - `NETGSM_SANDBOX=false`
4. Payment decision:
   - If payment is active: `PAYMENT_REQUIRED=true` and `PAYMENT_DEFAULT_PROVIDER=paytr`
   - If payment is not active: `PAYMENT_REQUIRED=false` and `PAYMENT_DEFAULT_PROVIDER=mockpay`
5. Do not activate `paytr` in production until:
   - commercial agreement is complete
   - real merchant keys exist
   - callback smoke path is validated

## Step 2: Local release preflight

Run from repository root:

```powershell
./scripts/release/prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production
```

For rehearsal without mutating `APP_VERSION`:

```powershell
./scripts/release/prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -SkipEnvStamp
```

Expected:

- Quality gate passes.
- Backend regression passes.
- Frontend regression passes.
- Strict env checklist passes.

Report output:

- `storage/app/qa/hostinger-preflight/<timestamp>/report.json`

## Step 3: Optional real API smoke

Use only when production-like endpoint is reachable and admin credentials are valid:

```powershell
./scripts/release/prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -RunApiSmoke
```

If you want SMS/email to be sent externally:

```powershell
./scripts/release/prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -RunApiSmoke -SendExternalNotifications
```

For rehearsal without env mutation:

```powershell
./scripts/release/prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -SkipEnvStamp -RunApiSmoke
```

## Step 4: Server cutover (mandatory opcache reset)

Run on Hostinger server after release folder is prepared:

```bash
cd /home/<cpanel_user>/domains/simdigetir.com/current
chmod +x scripts/release/hostinger-opcache-reset.sh scripts/release/hostinger-atomic-cutover.sh
TARGET_RELEASE=<release_folder_name> HOSTS="simdigetir.com,www.simdigetir.com" bash scripts/release/hostinger-atomic-cutover.sh
```

Expected:

- `current` symlink points to target release.
- `public_html/storage` points to `../current/storage/app/public`.
- Script output contains `opcache_reset=true` for each host.

## Step 5: Mode-specific post-cutover validation

Follow:

- `docs/ops/hostinger-paytr-cutover-runbook-2026-03-15.md`

Rule:

- current default release mode is `payments_off`
- `payments_on_paytr` must be a separate release decision

## Go / Nogo policy

GO only if all of the following are true:

- No failed step in preflight report.
- `final_decision` is `GO`.
- No open P0/P1 issue in release scope.

NOGO if any of the following is true:

- `Strict Env Checklist` fails.
- Mandatory tests fail.
- API smoke fails when it is part of release scope.

## Rollback readiness

- Keep previous release artifact/build available.
- Keep previous `.env` backup.
- Prepare DB backup before first production migration.
- Keep previous `TARGET_RELEASE` value ready for atomic cutover rollback.
