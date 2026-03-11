# Hostinger Release Governance Checklist (2026-03-10)

## Scope

- Frontend and backend preflight verification before first Hostinger deployment.
- Decision gate: GO/NOGO.

## Inputs

- Production env template: `.env.hostinger.production.example`
- Preflight script: `scripts/run-hostinger-preflight.ps1`
- Strict env and live smoke script: `scripts/run-phase2-live-smoke.ps1`
- Versioning policy: `docs/ops/versioning-policy.md`

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
2. Fill all real credentials (DB, SMTP, Iyzico, Netgsm, Maps).
3. Keep:
   - `SMS_DEFAULT_PROVIDER=netgsm`
   - `NETGSM_SANDBOX=false`
4. Payment decision:
   - If payment is active: `PAYMENT_REQUIRED=true` and `PAYMENT_DEFAULT_PROVIDER=iyzico`
   - If payment is not active: `PAYMENT_REQUIRED=false` and `PAYMENT_DEFAULT_PROVIDER=mockpay`

## Step 2: Local release preflight

Run from repository root:

```powershell
./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production
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
./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production -RunApiSmoke
```

If you want SMS/email to be sent externally:

```powershell
./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production -RunApiSmoke -SendExternalNotifications
```

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
