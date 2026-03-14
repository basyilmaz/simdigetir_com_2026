# SimdiGetir Release Baseline Evidence (2026-03-14)

## Release Identity

- Branch: `chore/repo-hygiene-baseline`
- Head (before release lock): `d08d2194`
- VERSION (before bump): `1.0.5`
- Existing tags: `v1.0.1`, `v1.0.2`, `v1.0.3`

## Governance Evidence

- Quality gate:
  - Command: `./scripts/run-quality-gate.ps1`
  - Result: PASS
- Preflight:
  - Command: `./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production`
  - Report: `storage/app/qa/hostinger-preflight/2026-03-14-063952/report.json`
  - Decision: `GO`
- Broken ref cleanup:
  - Check command: `Get-ChildItem .git/refs -Recurse -Force | ? Name -match 'desktop\\.ini'`
  - Result: `0`

## Notes

- DB backup reference production tarafinda operasyon penceresinde alinmalidir.
- Bu dokuman release gate icin baseline kaniti olarak tutulur.
