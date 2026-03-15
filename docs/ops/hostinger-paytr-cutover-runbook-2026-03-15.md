# Hostinger PAYTR Cutover Runbook (2026-03-15)

## Purpose

This runbook separates two different release states:

1. `payments_off`
   - current safe release mode
   - checkout works
   - card payment is not commercially active
   - `PAYMENT_REQUIRED=false`
   - `PAYMENT_DEFAULT_PROVIDER=mockpay`
2. `payments_on_paytr`
   - post-agreement activation mode
   - PAYTR merchant credentials are filled
   - `PAYMENT_REQUIRED=true`
   - `PAYMENT_DEFAULT_PROVIDER=paytr`

Do not mix these two modes in one release decision.

## Current Recommended Mode

As of `2026-03-15`, the recommended production mode is:

- `payments_off`

Reason:

- product flow, checkout, tracking, portal, SMS, and admin controls are ready
- PAYTR commercial setup and real callback verification are not completed yet

## Mode A: Payments Off Cutover

### Preconditions

- `./scripts/run-quality-gate.ps1` => PASS
- `./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production -ReleaseMode payments_off` => `GO`
- `.env.hostinger.production` contains:
  - `PAYMENT_REQUIRED=false`
  - `PAYMENT_DEFAULT_PROVIDER=mockpay`
  - `SMS_DEFAULT_PROVIDER=netgsm`
  - `NETGSM_SANDBOX=false`
- DB backup exists
- previous release artifact exists
- previous `.env` backup exists

### Local Commands

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production
```

Repeat run without another semantic version bump:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump
```

Rehearsal without changing semantic version or `APP_VERSION`:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -SkipEnvStamp
```

### Server Commands

```bash
cd /home/<cpanel_user>/domains/simdigetir.com
cp current/.env .env.backup.$(date +%Y%m%d%H%M%S)
cp -R current ../release_backups/current_$(date +%Y%m%d%H%M%S)
cd current
php artisan migrate --force
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod +x scripts/release/hostinger-opcache-reset.sh scripts/release/hostinger-atomic-cutover.sh
TARGET_RELEASE=<release_folder_name> HOSTS="simdigetir.com,www.simdigetir.com" bash scripts/release/hostinger-atomic-cutover.sh
```

### Post-Cutover Smoke

Check all of the following:

1. `/` loads with `200`
2. `/checkout/{token}` flow opens from hero quote
3. `/siparis-takip` opens
4. `/hesabim/giris` opens
5. `/admin/login` opens
6. NETGSM-backed notification flow still works in admin/runtime logs
7. Footer version changed to the new runtime version

### GO

GO only if:

- no preflight failure
- no migration failure
- opcache reset succeeds for both hosts
- post-cutover smoke is clean

### NOGO

NOGO if:

- payment mode accidentally changes to `PAYTR` without commercial activation
- checkout returns `500`
- admin panel breaks
- version/footer stays stale after cutover and opcache reset

## Mode B: PAYTR Activation Cutover

Use this mode only after PAYTR agreement is completed.

### Extra Preconditions

- real values exist for:
  - `PAYTR_MERCHANT_ID`
  - `PAYTR_MERCHANT_KEY`
  - `PAYTR_MERCHANT_SALT`
  - `PAYTR_CALLBACK_SECRET`
- callback contract is confirmed
- test transaction path is available
- settlement/refund operational owner is defined

### Required Env State

- `PAYMENT_REQUIRED=true`
- `PAYMENT_DEFAULT_PROVIDER=paytr`
- `PAYTR_SANDBOX=false`

### Required Local Validation

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production
powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -RunApiSmoke
```

Rehearsal without env mutation:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\release\prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -SkipEnvStamp -RunApiSmoke
```

Expected:

- strict env passes with PAYTR merchant keys
- payment initiation works
- callback validation path is confirmed

### Post-Cutover PAYTR Smoke

Check all of the following:

1. create a checkout session
2. finalize an order with `card + prepaid`
3. use `Kart odemesine gec`
4. confirm provider initiation returns PAYTR URL/session
5. confirm callback/webhook reaches application
6. confirm order/payment state moves out of pending as expected

### NOGO

NOGO if any of the following is true:

- PAYTR initiation works only in sandbox assumptions
- callback secret/signature is not verified
- order remains permanently stuck in pending payment
- reconciliation/finance team cannot identify transaction records

## Rollback Rule

Rollback immediately if any cutover introduces:

- homepage `500`
- checkout `500`
- admin login failure
- broken migrations
- broken symlink/cutover
- stale asset/runtime version after opcache reset

### Rollback Command Shape

```bash
cd /home/<cpanel_user>/domains/simdigetir.com/current
TARGET_RELEASE=<previous_release_folder_name> HOSTS="simdigetir.com,www.simdigetir.com" bash scripts/release/hostinger-atomic-cutover.sh
```

If rollback includes env regression:

- restore previous `.env`
- clear/cache config again

## Final Decision Rule

Current release recommendation:

- release now with `payments_off`
- activate PAYTR in a separate controlled release after agreement and smoke proof
