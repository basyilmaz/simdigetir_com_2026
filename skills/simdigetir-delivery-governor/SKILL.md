---
name: simdigetir-delivery-governor
description: Govern controlled delivery for SimdiGetir with branch discipline, modular boundaries, quality-gate enforcement, version bump policy, and deploy-last sequencing. Use when planning or executing development where production deployment must be the final step.
---

# SimdiGetir Delivery Governor

Enforce a deterministic workflow for safe delivery.

## Workflow

1. Freeze and baseline
- Record current release reference (git commit/tag, `VERSION`, env profile checksum, DB backup reference).
- Do not start coding before baseline evidence exists.

2. Scope and priority
- Classify work as `P0`, `P1`, `P2`.
- Write acceptance criteria per item in testable terms.

3. Branch rule
- Create one branch per scope: `feat/<scope>` or `fix/<scope>`.
- Avoid mixed concerns in a single branch.

4. Modular boundary
- Keep changes inside target modules when possible.
- For shared contract changes, document reason and impact explicitly.

5. Mandatory gate after each change batch
- Run from repository root:
```powershell
./scripts/run-quality-gate.ps1
```
- If any check fails, fix before continuing.

6. Evidence logging
- Update an ops report with:
- scope
- files touched
- tests run
- pass/fail
- residual risks

7. Versioning policy
- Change `VERSION` on every GitHub push.
- Change `VERSION` again on production release cut.
- Keep release notes aligned with version changes.

8. Deploy-last rule
- Never deploy directly during implementation.
- Deploy only after release gate confirms `GO`.

## Non-Negotiables

- No production deploy with open `P0` or `P1`.
- No skipped quality gate.
- No undocumented schema or route contract change.
- No ad-hoc hotfix directly on `main`.

## Minimum Output Per Cycle

- Updated scope checklist
- Quality gate result
- Version increment
- Go/No-Go status (for release branches)

