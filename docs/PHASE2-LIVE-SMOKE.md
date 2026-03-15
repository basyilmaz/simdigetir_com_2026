# Phase 2 Live Smoke Automation

Script path: `scripts/run-phase2-live-smoke.ps1`

## 1) Required `.env` keys

- `PAYMENT_DEFAULT_PROVIDER`
- `PAYMENT_REQUIRED`
- `SMS_DEFAULT_PROVIDER`
- `NETGSM_USERNAME`
- `NETGSM_PASSWORD`
- `NETGSM_HEADER`
- `NETGSM_SANDBOX`
- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`
- `GOOGLE_MAPS_API_KEY` (optional but recommended)

Provider-specific payment keys:

- If `PAYMENT_DEFAULT_PROVIDER=paytr`
  - `PAYTR_MERCHANT_ID`
  - `PAYTR_MERCHANT_KEY`
  - `PAYTR_MERCHANT_SALT`
  - `PAYTR_CALLBACK_SECRET`
  - `PAYTR_SANDBOX`
- If `PAYMENT_DEFAULT_PROVIDER=iyzico`
  - `IYZICO_API_KEY`
  - `IYZICO_SECRET_KEY`
  - `IYZICO_CALLBACK_SECRET`
  - `IYZICO_SANDBOX`
- If `PAYMENT_REQUIRED=false`
  - `PAYMENT_DEFAULT_PROVIDER=mockpay` is expected in strict mode

For API smoke run:
- `LIVE_SMOKE_BASE_URL`
- `LIVE_SMOKE_ADMIN_EMAIL`
- `LIVE_SMOKE_ADMIN_PASSWORD`

For external SMS/email send:
- `LIVE_SMOKE_SMS_TARGET`
- `LIVE_SMOKE_EMAIL_TARGET`

## 2) Usage

Only `.env` validation:

```powershell
./scripts/run-phase2-live-smoke.ps1
```

Recommended explicit mode validation:

```powershell
./scripts/run-phase2-live-smoke.ps1 -ReleaseMode payments_off
# or
./scripts/release/run-hostinger-payments-off-gate.ps1 -EnvFile .env.hostinger.production
# or
./scripts/release/prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump
# or
./scripts/release/prepare-hostinger-payments-off-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -SkipEnvStamp
```

Validation + API smoke (quote -> order -> payment initiate -> callback simulation):

```powershell
./scripts/run-phase2-live-smoke.ps1 -RunApiSmoke
```

For PAYTR activation:

```powershell
./scripts/run-phase2-live-smoke.ps1 -ReleaseMode payments_on_paytr -RunApiSmoke
# or
./scripts/release/run-hostinger-paytr-activation-gate.ps1 -EnvFile .env.hostinger.production -RunApiSmoke
# or
./scripts/release/prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -RunApiSmoke
# or
./scripts/release/prepare-hostinger-paytr-activation-release.ps1 -EnvFile .env.hostinger.production -SkipVersionBump -SkipEnvStamp -RunApiSmoke
```

Validation + API smoke + real SMS/email dispatch:

```powershell
./scripts/run-phase2-live-smoke.ps1 -RunApiSmoke -SendExternalNotifications
```

## 3) Notes

- `-SendExternalNotifications` sends real outbound SMS/email. Use dedicated test targets.
- If provider sandbox mode is enabled (`PAYTR_SANDBOX=true` or `IYZICO_SANDBOX=true`) or `NETGSM_SANDBOX=true`, script warns you.
- Payment callback simulation is currently enabled for `iyzico` provider flow only.
- For `paytr`, payment initiation is validated and callback simulation is intentionally skipped until the live callback contract is completed.
- Script reports all missing/placeholder keys in one run.
- For strict live smoke with active payments, `PAYMENT_DEFAULT_PROVIDER` must point to a real supported provider (`paytr` or `iyzico`).
- If `PAYMENT_REQUIRED=false`, strict mode expects `PAYMENT_DEFAULT_PROVIDER=mockpay`.
- `-ReleaseMode payments_off` and `-ReleaseMode payments_on_paytr` add an explicit operator-level guard on top of env validation.
