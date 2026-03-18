# Release Preflight Gate v1.4.0

Date: 2026-03-19
Env: `.env.hostinger.production`
Release mode: `payments_off`

## Command

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\release\run-hostinger-payments-off-gate.ps1 -EnvFile .env.hostinger.production
```

## Result

- Decision: `GO`
- Report:
  - `storage/app/qa/hostinger-preflight/2026-03-19-005332/report.json`

## Step Summary

- Quality Gate: `PASS`
- Backend Regression: `PASS`
- Frontend Regression: `PASS`
- Strict Env Checklist: `PASS`

## Operational Warnings

- `PAYMENT_REQUIRED=false`
  - expected for current payments-off release mode
  - card provider checks intentionally skipped
- `GOOGLE_MAPS_API_KEY` missing or placeholder
  - known operational backlog item
  - distance fallback remains active

## Decision Basis

- no open release-blocking failures in automated gates
- checkout/account/tracking parity train preserves public route and contract stability
- rollback remains available through existing Hostinger atomic cutover flow
