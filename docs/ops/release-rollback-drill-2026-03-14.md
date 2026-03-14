# SimdiGetir Release Rollback Drill (2026-03-14)

## Scope

- Release branch: `release/2026-03-14-gate-go`
- Target release: `v1.0.7`
- Previous stable release: `v1.0.6`
- Goal: release-governance rollback readiness proof before GO decision.

## Commands Executed

1. Previous release ref validation
- `git rev-list -n 1 v1.0.6`
- Result: `7e6920a859b2c8534c8549aee099272a662f000f`

2. Route contract visibility check (non-destructive)
- `php artisan route:list --name=api.v1 --no-ansi`
- Result: `PASS` (35 API v1 routes visible)

3. Migration status on isolated sqlite (non-production)
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=<repo>/database/database.sqlite`
- `php artisan migrate:status --no-ansi`
- Result: `PASS` (migrations listed as ran in local test db)

4. Rollback command rehearsal (no data change)
- `php artisan migrate:rollback --step=1 --pretend --no-ansi`
- Result: `PASS` (SQL preview generated, no actual rollback applied)

## Rollback Procedure (Production Window)

1. Put app in maintenance mode.
2. Restore DB snapshot `HOSTINGER-PROD-BACKUP-BEFORE-v1.0.7`.
3. Checkout previous stable tag `v1.0.6`.
4. Run `composer install --no-dev --optimize-autoloader`.
5. Run `php artisan optimize:clear` and `php artisan config:cache`.
6. Exit maintenance mode and run smoke checks.

## Decision

- Rollback readiness: `PASS`
- Constraint: production DB restore step is executed only in live maintenance window.
