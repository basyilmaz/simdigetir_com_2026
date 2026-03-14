# Project Agent Rules

## Primary Engineering Constraints

- Maintain strict modular boundaries. Prefer changes under `Modules/<ModuleName>`.
- Avoid touching unrelated modules when implementing a task.
- Any shared contract change (routes, DTOs, DB schema, helpers) must be minimal and justified.
- After every code change, run the quality gate before declaring completion.

## Mandatory Post-Change Test Gate

Run from repository root:

```powershell
./scripts/run-quality-gate.ps1
```

Gate requirements:
- Lint changed PHP files.
- Run `php artisan test`.
- Report failures and fix before closure.

## Skill Usage

Use skill: `modular-safe-delivery` for feature work, bug fixes, and refactors.

Use skill: `frontend-delivery` for landing/UI/responsive/accessibility/conversion-facing frontend work.

Use skill: `backend-delivery` for API/domain/model/migration/queue/backend integration work.

Use skill: `marketing-growth` for SEO, funnel, campaign, and conversion optimization planning/execution.

Use skill: `qa-test-automation` for test planning, regression scope definition, and post-change verification.

Use skill: `release-governance` for go/no-go gates, release checklist execution, and rollback readiness checks.

Use skill: `db-migration-safety` for schema/data migration planning and safe rollout/rollback validation.
