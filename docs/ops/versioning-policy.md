# Versioning Policy

## Core Rule

- `VERSION` must change on every GitHub push.
- `APP_VERSION` must change on every live deployment.

## Severity-Based Numbering Strategy

Use severity to choose bump level by default:

| Priority / Severity | Typical Scope | Default Bump |
|---|---|---|
| `P0`, `hotfix`, `security` | live blocker, urgent production fix | `patch` |
| `P1`, `feature` | high-impact feature slice, user-visible capability expansion | `minor` |
| `P2`, `P3`, `chore` | incremental improvements, maintenance, docs/tests | `patch` |
| `breaking` | backward-incompatible API/DB/contract change | `major` |

Notes:
- If work is mixed, choose the highest-impact class.
- `--part` can override automatic severity mapping when explicitly needed.

## GitHub Push Flow

1. Bump semantic version before commit/push.

Priority-aware command:

```bash
powershell -ExecutionPolicy Bypass -File scripts/version/bump-version-by-priority.ps1 -Priority P1
```

Direct command:

```bash
php scripts/version/bump-version.php --severity=p1
```

Explicit override example:

```bash
php scripts/version/bump-version.php --severity=p1 --part=patch
```

2. Commit these files together:
   - `VERSION`
   - `.env.example`
   - `.env.hostinger.production.example`

3. Push to GitHub.

CI enforcement:
- Workflow `quality-gate.yml` fails if `VERSION` is not changed in push diff.

## Live Deployment Flow

Run on server before cache build:

```bash
php scripts/version/stamp-env-version.php --env=.env --channel=live
php artisan config:clear
php artisan config:cache
```

Example stamped value:
- `APP_VERSION=1.0.17-live.20260316225723`

This guarantees each live rollout has a unique runtime version even if code commit is the same.

## Display Source

- App reads `config('app.version')`.
- Source priority:
  1. `APP_VERSION` in `.env`
  2. `VERSION` file fallback
