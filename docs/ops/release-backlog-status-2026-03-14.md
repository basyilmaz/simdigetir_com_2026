# SimdiGetir Release Backlog Status (2026-03-14)

## Scope

- Baseline: `release/2026-03-14-gate-go`
- Hedef: NOGO -> GO kapanis adimlari

## P0 Listesi

- API v1 contract parity kirik (404): `CLOSED`
- Admin panel role/access guard kirik: `CLOSED`
- Sprint regression test fail: `CLOSED`

## P1 Listesi

- Yasal dokuman route parity (`/cerez-politikasi`): `CLOSED`
- Preflight report quality gate skip: `CLOSED`
- Broken git refs (`desktop.ini`) warning: `CLOSED`

## P2 Listesi

- Worktree hygiene (tek release commit oncesi daginik degisiklikler): `CLOSED`
- Version/tag parity (`VERSION` vs latest tag): `CLOSED`

## Evidence

- Quality gate: `./scripts/run-quality-gate.ps1` -> PASS (142 tests, 732 assertions)
- Preflight: `storage/app/qa/hostinger-preflight/2026-03-14-070805/report.json` -> GO
- Broken ref check:
  - `Get-ChildItem .git/refs -Recurse -Force | ? Name -match 'desktop\\.ini'` -> `0`
- Rollback drill:
  - `docs/ops/release-rollback-drill-2026-03-14.md` -> PASS
- Release tag:
  - `v1.0.7` (after gate-close commit)
