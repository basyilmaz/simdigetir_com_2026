# Phase 2 Live Smoke Automation

Script path: `scripts/run-phase2-live-smoke.ps1`

## 1) Required `.env` keys

- `PAYMENT_DEFAULT_PROVIDER`
- `IYZICO_API_KEY`
- `IYZICO_SECRET_KEY`
- `IYZICO_CALLBACK_SECRET`
- `IYZICO_SANDBOX`
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

Validation + API smoke (quote -> order -> payment initiate -> callback simulation):

```powershell
./scripts/run-phase2-live-smoke.ps1 -RunApiSmoke
```

Validation + API smoke + real SMS/email dispatch:

```powershell
./scripts/run-phase2-live-smoke.ps1 -RunApiSmoke -SendExternalNotifications
```

## 3) Notes

- `-SendExternalNotifications` sends real outbound SMS/email. Use dedicated test targets.
- If `IYZICO_SANDBOX=true` or `NETGSM_SANDBOX=true`, script warns you.
- Payment callback simulation is currently enabled for `iyzico` provider flow.
- Script reports all missing/placeholder keys in one run.
- For live smoke, `PAYMENT_DEFAULT_PROVIDER=mockpay` and `SMS_DEFAULT_PROVIDER=mock` are rejected.
