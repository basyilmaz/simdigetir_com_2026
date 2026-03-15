# Frontend + Backend GO/NOGO Matrix (2026-03-10)

## Scope

- Target runtime: `https://simdigetir.com`
- Validation date: **10 March 2026**
- Goal: verify frontend/backend parity before Hostinger production cutover.

## Evidence

- HTTP runtime smoke:
  - `storage/app/qa/http-runtime-smoke/2026-03-10/report.json`
- Mobile regression (375/390/768/1024):
  - `storage/app/qa/mobile-regression/2026-03-10/report.json`
  - screenshots: `storage/app/qa/mobile-regression/2026-03-10/*.png`
- Local module/feature regression:
  - `php artisan test` subset (38 tests, all pass)
- Route snapshots:
  - `storage/app/qa/admin-routes-2026-03-10.txt`
  - `storage/app/qa/api-v1-routes-2026-03-10.txt`

## Frontend Page Matrix (Live)

| Page | HTTP 200 | Mobile 375/390/768/1024 | OG image JPG | Phone updated to `0551 356 72 92` | Decision |
|---|---|---|---|---|---|
| `/` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |
| `/hakkimizda` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |
| `/hizmetler` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |
| `/kurumsal` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |
| `/iletisim` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |
| `/sss` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |
| `/kurye-basvuru` | PASS | PASS | FAIL (`og-default.svg`) | FAIL (old number active) | NOGO |

## Backend Parity Matrix (Live vs Local)

| Capability | Local code/test expectation | Live probe | Decision |
|---|---|---|---|
| Admin login | Exists | `/admin/login` => `200` | GO |
| Admin resources (orders/couriers/payments/support etc.) | Present in local route snapshot | `/admin/orders` => `404`, `/admin/couriers` => `404`, `/admin/payment-transactions` => `404` | NOGO |
| Ads admin resources | Present in local route snapshot | `/admin/ad-campaigns` => `404`, `/admin/ad-conversions` => `404` | NOGO |
| API v1 auth/ops/kpi | Present in local route snapshot | `/api/v1/auth/login` => `404`, `/api/v1/ops/health` => `404`, `/api/v1/kpi/overview` => `404` | NOGO |
| Leads API (legacy endpoint) | Exists | `/api/leads` => `405` (method not allowed on GET, route exists) | GO |
| Dynamic form endpoints | Expected by frontend forms | `/api/forms/lead/submit` => `404` | NOGO |
| Sitemap | Must be reachable | `/sitemap.xml` => `200` | GO |

## Global Release Decision

- **Initial decision (2026-03-10): NOGO**

P0 blockers:

1. Live `og:image` still SVG (`og-default.svg`), not JPG/PNG.
2. Live backend route set is not at parity with local release scope (`/api/v1/*` and major admin resources return `404`).
3. Live frontend still serves old phone/whatsapp number, not `0551 356 72 92`.

## Post-Deploy Recheck (2026-03-11)

- Runtime revalidated after live parity deployment.
- Updated decision: **GO**

### Post-deploy evidence

- HTTP smoke report:
  - `storage/app/qa/http-runtime-smoke/2026-03-11-postdeploy/report.json`
- Mobile regression report:
  - `storage/app/qa/mobile-regression/2026-03-11-postdeploy/report.json`

### Recheck summary

| Check | Result |
|---|---|
| Public pages (`/`, `/hakkimizda`, `/hizmetler`, `/kurumsal`, `/iletisim`, `/sss`, `/kurye-basvuru`) | PASS (200) |
| `og:image` format | PASS (`og-default.jpg`) |
| Phone/WhatsApp rollout | PASS (`0551 356 72 92` / `905513567292`) |
| API auth probe (`POST /api/v1/auth/login` with `Accept: application/json`) | PASS (422 validation) |
| KPI/OPS probes (`/api/v1/ops/health`, `/api/v1/kpi/overview` with `Accept: application/json`) | PASS (200 / 401) |
| Admin resource probes (`/admin/orders`, `/admin/couriers`, `/admin/ad-campaigns`) | PASS (302 login redirect) |
| Footer version stamp | PASS (`v1.0.1-live.20260310211153`) |

## Required Actions Before GO

1. Deploy latest build from tested codebase to Hostinger (frontend + backend together).
2. Rebuild route/config caches on server:
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan route:cache`
3. Verify `og:image` points to JPG/PNG on all pages.
4. Verify number rollout (`0551 356 72 92`) on all public pages.
5. Re-run:
   - `node scripts/qa/http-runtime-smoke.mjs`
   - `node scripts/qa/mobile-regression-check.mjs`
   - backend API probes for `/api/v1/*`.

## Live Parity Action List (Deploy Order + Commands + Retest Checklist)

### A) Deploy Order

1. Freeze release scope (no new feature merge during parity window).
2. Bump version and lock release commit.
3. Run local quality gate and release smoke.
4. Deploy backend code to Hostinger app path.
5. Sync `public/` output to web root.
6. Run migrate + cache rebuild on server.
7. Run live probes (frontend + backend + admin).
8. If all pass, switch decision to GO.

### B) Command Set

#### 1) Local release preparation (repo root)

```powershell
php scripts/version/bump-version.php --part=patch
./scripts/run-quality-gate.ps1
```

Optional (live URL smoke from local):

```powershell
$env:BASE_URL="https://simdigetir.com"
$env:OUT_DIR="storage/app/qa/http-runtime-smoke/next-live-check"
node scripts/qa/http-runtime-smoke.mjs

$env:BASE_URL="https://simdigetir.com"
$env:OUT_DIR="storage/app/qa/mobile-regression/next-live-check"
node scripts/qa/mobile-regression-check.mjs
```

#### 2) Server deploy (Hostinger SSH)

```bash
cd /home/<cpanel_user>/domains/simdigetir.com/current
git fetch --all
git checkout <release_commit_sha>
git pull --ff-only origin <release_branch>

composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force

php scripts/version/stamp-env-version.php --env=.env --channel=live
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

If web root uses copied `public/` files, use atomic cutover script:

```bash
cd /home/<cpanel_user>/domains/simdigetir.com/current
chmod +x scripts/release/hostinger-opcache-reset.sh scripts/release/hostinger-atomic-cutover.sh
TARGET_RELEASE=<release_folder_name> HOSTS="simdigetir.com,www.simdigetir.com" bash scripts/release/hostinger-atomic-cutover.sh
```

`hostinger-atomic-cutover.sh` now includes mandatory post-cutover opcache reset for all configured hosts.

#### 3) Live parity probes

```bash
# Core pages
curl -I https://simdigetir.com/
curl -I https://simdigetir.com/hizmetler
curl -I https://simdigetir.com/kurumsal
curl -I https://simdigetir.com/iletisim
curl -I https://simdigetir.com/sss
curl -I https://simdigetir.com/kurye-basvuru

# Backend/API parity (must not be 404)
curl -s -o /dev/null -w "%{http_code}\n" -X POST https://simdigetir.com/api/v1/auth/login -H "Content-Type: application/json" -d "{}"
curl -s -o /dev/null -w "%{http_code}\n" https://simdigetir.com/api/v1/ops/health
curl -s -o /dev/null -w "%{http_code}\n" https://simdigetir.com/api/v1/kpi/overview
curl -s -o /dev/null -w "%{http_code}\n" https://simdigetir.com/admin/orders
curl -s -o /dev/null -w "%{http_code}\n" https://simdigetir.com/admin/ad-campaigns

# OG image + phone checks
curl -s https://simdigetir.com/ | grep -i 'property="og:image"'
curl -s https://simdigetir.com/ | grep -E "0551 356 72 92|905513567292"
```

### C) Retest Checklist (GO Criteria)

| Check | Expected |
|---|---|
| All 7 public pages | HTTP `200` |
| `og:image` on all pages | `.jpg` or `.png` (not `.svg`) |
| Phone/WhatsApp content | `0551 356 72 92` / `905513567292` visible |
| `/api/v1/auth/login` | `422` or `401` (but not `404`) |
| `/api/v1/ops/health` and `/api/v1/kpi/overview` | `200` or `401/403` (but not `404`) |
| `/admin/orders`, `/admin/ad-campaigns` | `302` (login redirect) or `200`, not `404` |
| `./scripts/run-quality-gate.ps1` | PASS |
| HTTP runtime smoke report | no failures |
| Mobile regression report | no failures |

### D) Rollback Trigger

Rollback immediately if any of the following occurs:

1. Any core page returns non-200.
2. Any parity endpoint still returns `404`.
3. New deploy breaks admin route visibility.
4. Smoke report contains P0/P1 parity failures.
