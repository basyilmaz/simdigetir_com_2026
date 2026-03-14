# SimdiGetir Release Baseline Evidence (2026-03-14)

## Release Identity

- Branch: `release/2026-03-14-gate-go`
- Release lock commit: `7e6920a8`
- VERSION (current): `1.0.7`
- Existing tags at gate close: `v1.0.1`, `v1.0.2`, `v1.0.3`, `v1.0.6`, `v1.0.7`
- Env checklist hash (`.env.hostinger.production.example`, SHA-256):
  - `D718F68CD495E074495680D7EC84D590B720B0FB7A53FDEB1E4762CACA58C6E0`
- DB backup reference (release gate record):
  - `HOSTINGER-PROD-BACKUP-BEFORE-v1.0.7` (operasyon penceresinde alinacak)

## Governance Evidence

- Quality gate:
  - Command: `./scripts/run-quality-gate.ps1`
  - Result: PASS
- Preflight:
  - Command: `./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production`
  - Report: `storage/app/qa/hostinger-preflight/2026-03-14-070805/report.json`
  - Decision: `GO`
- Broken ref cleanup:
  - Check command: `Get-ChildItem .git/refs -Recurse -Force | ? Name -match 'desktop\\.ini'`
  - Result: `0`
- Rollback drill:
  - Record: `docs/ops/release-rollback-drill-2026-03-14.md`
  - Result: `PASS` (command-level proof captured)

## Notes

- Bu dokuman release gate icin baseline kaniti olarak tutulur.
