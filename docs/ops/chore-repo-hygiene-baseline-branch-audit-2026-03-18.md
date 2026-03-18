# Branch Audit: `origin/chore/repo-hygiene-baseline` (2026-03-18)

## Scope

Audited branch:
- `origin/chore/repo-hygiene-baseline`

Branch tip:
- `5ee8e658`
- `fix(ci): restore test baseline for auth, permissions, and api routes`

Current baseline for comparison:
- `origin/master`
- `d0f15959`

## Executive Decision

Decision:
- `MERGE = NO`
- `KEEP_ACTIVE = NO`
- `ARCHIVE = YES`

Reason:
1. The branch is not a lightweight repo-hygiene branch.
2. It contains an old auth/permission/API baseline snapshot.
3. Part of its intent is already present in `master`.
4. The remaining differences are outdated and would regress current production-ready behavior.

## Audit Method

1. Verified ancestry:
   - `git merge-base --is-ancestor origin/chore/repo-hygiene-baseline origin/master`
   - result: `no`
2. Reviewed branch-only change set using three-dot diff:
   - `git diff origin/master...origin/chore/repo-hygiene-baseline`
3. Reviewed direct content differences against current `master` for critical files:
   - auth controller
   - API routes
   - user model
   - bootstrap
   - settings model
   - composer manifest

## Branch-Only Change Set

Real branch payload since divergence:

- `Modules/Settings/app/Models/Setting.php`
- `VERSION`
- `app/Http/Controllers/Api/V1/AuthController.php`
- `app/Http/Controllers/Api/V1/OpsController.php`
- `app/Models/User.php`
- `bootstrap/app.php`
- `composer.json`
- `composer.lock`
- `config/permission.php`
- `database/migrations/2026_02_16_021341_create_permission_tables.php`
- `database/migrations/2026_02_18_132425_create_personal_access_tokens_table.php`
- `database/seeders/RolePermissionSeeder.php`
- `phpunit.xml`
- `resources/views/landing/location-neighborhood.blade.php`
- `routes/api.php`
- `routes/web.php`
- `tests/TestCase.php`

Branch scope size:
- `17 files changed`
- `820 insertions`
- `19 deletions`

## Findings

### 1. Commit message understates the real blast radius

Branch name and commit message suggest small CI cleanup:
- `chore/repo-hygiene-baseline`
- `fix(ci): restore test baseline for auth, permissions, and api routes`

Actual scope is not CI-only. It changes:
- auth runtime
- sanctum/bootstrap middleware
- permission config and migrations
- route surface
- user access model
- composer dependency contract

Assessment:
- The branch is mislabeled.
- It should not be merged blindly under a hygiene or CI heading.

### 2. Part of the branch is already absorbed by current `master`

These files are no longer materially different from `master` and therefore do not justify merging the branch:
- `config/permission.php`
- `database/seeders/RolePermissionSeeder.php`
- `database/migrations/2026_02_16_021341_create_permission_tables.php`
- `database/migrations/2026_02_18_132425_create_personal_access_tokens_table.php`
- `app/Http/Controllers/Api/V1/OpsController.php`
- `tests/TestCase.php`
- `phpunit.xml`
- `resources/views/landing/location-neighborhood.blade.php`

Assessment:
- The useful baseline pieces have already been integrated or recreated in `master`.
- There is no remaining merge value from these files.

### 3. `AuthController` in the branch is older and functionally weaker than `master`

Branch behavior:
- email-only login
- no register endpoint
- no phone normalization
- smaller user payload

Current `master` behavior:
- register endpoint exists
- login supports email or phone
- phone normalization exists
- user payload includes `phone`
- synthetic email fallback exists for phone-first register flow

Affected file:
- [AuthController.php](C:\YazilimProjeler\simdigetir_com_2026\app\Http\Controllers\Api\V1\AuthController.php)

Assessment:
- Merging this branch would reintroduce an older auth contract.
- This is a regression risk, not an improvement.

### 4. `routes/api.php` in the branch is a stubbed baseline and would regress live API breadth

Branch API shape:
- login
- health
- authenticated `me`
- authenticated `logout`
- stub `/orders` returning empty array
- stub `/kpi/overview` returning hardcoded zeros

Current `master` API shape:
- register/login/me/logout
- quotes
- payments callback/initiate/retry
- real order lifecycle routes
- dispatch routes
- courier routes
- finance routes
- notifications routes
- corporate/support routes
- real KPI controller

Affected file:
- [api.php](C:\YazilimProjeler\simdigetir_com_2026\routes\api.php)

Assessment:
- This branch contains an early scaffold, not the current operational API.
- Merge would be destructive unless manually carved apart.

### 5. `User` model on the branch is less secure than current `master`

Branch behavior:
- `canAccessPanel()` returns `true`
- no `phone` fillable
- no `is_active`
- no `is_active` cast

Current `master` behavior:
- panel access blocked when user inactive
- role-gated access for backoffice roles only
- `phone` and `is_active` fields supported

Affected file:
- [User.php](C:\YazilimProjeler\simdigetir_com_2026\app\Models\User.php)

Assessment:
- Merging branch content here would weaken admin access control.
- This is a security regression.

### 6. `bootstrap/app.php` on the branch misses current hardening

Branch adds:
- `api` route registration
- `statefulApi()`
- spatie permission middleware aliases
- CSRF exceptions for `api/*`

Current `master` keeps those and additionally adds:
- `SecurityHeaders` middleware append

Affected file:
- [app.php](C:\YazilimProjeler\simdigetir_com_2026\bootstrap\app.php)

Assessment:
- Branch content is an older subset.
- Current `master` supersedes it.

### 7. `Setting` model on the branch is superseded by a safer runtime guard in `master`

Branch improvement:
- cached lookup
- fallback on query failure

Current `master` includes that and adds:
- database reachability probe
- runtime fail-safe before cache/database read

Affected file:
- [Setting.php](C:\YazilimProjeler\simdigetir_com_2026\Modules\Settings\app\Models\Setting.php)

Assessment:
- Branch version is incomplete relative to current runtime hardening.

### 8. `routes/web.php` on the branch regresses legal-document delivery

Branch behavior:
- legal pages route to static `landing.kvkk` view

Current `master` uses richer legal/content-driven flow introduced later in the product lifecycle.

Affected file:
- [web.php](C:\YazilimProjeler\simdigetir_com_2026\routes\web.php)

Assessment:
- Branch reflects an older content model.
- Merge would move legal routing backwards.

### 9. `composer.json` and `composer.lock` on the branch are outdated

Branch contract:
- `laravel/sanctum`: `^4.0`
- `spatie/laravel-permission`: `*`
- missing current expanded module autoload map now used by the live codebase

Current `master`:
- stricter dependency pins
- expanded module autoload coverage for current modules

Affected file:
- [composer.json](C:\YazilimProjeler\simdigetir_com_2026\composer.json)

Assessment:
- Merging branch dependency state would risk dependency drift and autoload regressions.

## Net Interpretation

This branch looks like an early stabilization commit from March 14, 2026 that served as a temporary CI/auth/permission recovery baseline.

That baseline has since been overtaken by:
1. broader checkout/customer/runtime work
2. admin hardening
3. release train `v1.2.x`
4. security and routing improvements
5. dependency and module growth

So the branch is now historically interesting, but operationally obsolete.

## Merge / Keep / Archive Decision

### Merge

Decision:
- `NO`

Why:
1. It is not merge-clean conceptually.
2. It would regress auth, routes, legal delivery, access control, and dependency state.

### Keep as active branch

Decision:
- `NO`

Why:
1. Branch name is misleading.
2. The remaining unique content is not a forward path.
3. Active retention increases future accidental merge risk.

### Archive

Decision:
- `YES`

Recommended archival options:
1. Rename remote branch to:
   - `archive/ci-auth-permission-baseline-2026-03-14`
2. Or tag the commit:
   - `archive-ci-auth-permission-baseline-2026-03-14`
3. Then delete the misleading remote:
   - `origin/chore/repo-hygiene-baseline`

## Recommended Next Step

Safe next action:
1. Archive commit `5ee8e658`
2. Delete `origin/chore/repo-hygiene-baseline`
3. Keep no active development on top of this line

If any single idea from this branch is still desired, it should be reintroduced by targeted cherry-pick or manual patch onto current `master`, not by merging the branch wholesale.
