# Release Preflight Gate v1.2.0 (2026-03-17)

## Command

```powershell
./scripts/release/run-hostinger-payments-off-gate.ps1 -EnvFile .env.hostinger.production
```

## Evidence

- Preflight report:
  - `storage/app/qa/hostinger-preflight/2026-03-17-193409/report.json`

## Result Summary

1. Quality Gate
- `PASS`

2. Backend Regression
- `PASS`

3. Frontend Regression
- `PASS`

4. Strict Env Checklist
- `PASS`

5. Final decision
- `GO`

## Non-Blocking Warnings

- `PAYMENT_REQUIRED=false`
  - intentional for `payments_off` release mode
- `GOOGLE_MAPS_API_KEY` missing/placeholder
  - fallback mode remains active
  - maps-required release must use `-RequireMapsKey`

## Rollback Readiness

- Rollback mechanism for this release remains atomic symlink cutover.
- Canonical command from runbook:

```bash
TARGET_RELEASE=<previous_release_folder_name> HOSTS="simdigetir.com,www.simdigetir.com" bash scripts/release/hostinger-atomic-cutover.sh
```

- Current blocker to mark rollback rehearsal fully closed:
  - actual previous release folder must be confirmed on Hostinger during cutover window
  - fresh DB backup reference must be recorded at cutover time

## Decision

- Preflight gate status: `GO`
- Production cutover status from this document: `PENDING_FINAL_OPS_STEP`
