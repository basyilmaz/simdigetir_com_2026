# Versioning Policy

## Rule

- `VERSION` must change on every GitHub push.
- `APP_VERSION` must change on every live deployment.

## GitHub Push Flow

1. Bump semantic version before commit/push:

```bash
php scripts/version/bump-version.php --part=patch
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
- `APP_VERSION=1.0.1-live.20260310234511`

This guarantees each live rollout has a unique runtime version even if code commit is the same.

## Display Source

- App reads `config('app.version')`.
- Source priority:
  1. `APP_VERSION` in `.env`
  2. `VERSION` file fallback
