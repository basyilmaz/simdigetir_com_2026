---
name: simdigetir-release-gate
description: Execute SimdiGetir release GO/NOGO governance with deploy-last control, rollback readiness, tagged release discipline, and production smoke verification. Use before any staging-to-production promotion.
---

# SimdiGetir Release Gate

Apply a strict gate before production.

## Pre-Gate Inputs

- Release scope and changelog
- Current target branch commit SHA
- DB migration plan and rollback steps
- Environment diff checklist

## Gate Checklist

1. Quality
- Latest run of:
```powershell
./scripts/run-quality-gate.ps1
```
- Result must be green.

2. Defects
- Open `P0` = 0
- Open `P1` = 0

3. Parity
- Frontend-backend parity matrix updated.
- No unresolved `not_exposed` items that violate release scope.

4. Ops readiness
- Backup validated.
- Rollback command sequence validated.
- Monitoring/log access confirmed.

5. Version and release identity
- `VERSION` bumped for release.
- Git tag prepared (`vYYYY.MM.DD.N` or project standard).
- Release note includes known non-blocking risks.

## GO Decision

- Return `GO` only when all checklist items pass.
- If any item fails, return `NOGO` with blocking reasons.

## Production Rollout (Last Step Only)

1. Deploy during approved window.
2. Run smoke checks on core routes and admin critical paths.
3. Monitor error logs and conversion flow for the first 30-60 minutes.
4. If critical regression appears, execute rollback immediately.

