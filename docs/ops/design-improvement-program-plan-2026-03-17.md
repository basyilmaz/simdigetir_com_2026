# SimdiGetir Design Improvement Program Plan (2026-03-17)

## Version Decision

Decision: target `v1.2.0` release train, not a single `v1.1.0` jump.

Reason:
1. Report scope is not a single feature. It changes funnel architecture, visual system, motion stack, and performance strategy together.
2. `v1.1.0` is suitable for conversion-core only, but full report scope is larger.
3. Safer model:
- `v1.1.x`: conversion-core stabilization
- `v1.2.0`: design system + motion/performance pack with gated rollout

## Program Objective

Move from "landing brochure + partial funnel" to "high-conversion product experience" without breaking current live operations.

## Skill Stack (Program Specific)

New project skills created for this program:
1. `skills/simdigetir-design-system-2-0/SKILL.md`
2. `skills/simdigetir-conversion-funnel-delivery/SKILL.md`
3. `skills/simdigetir-motion-performance-governor/SKILL.md`

Mandatory existing governance skills:
1. `simdigetir-delivery-governor`
2. `simdigetir-release-gate`
3. `simdigetir-frontend-backend-parity`
4. `qa-test-automation`
5. `release-governance`
6. `modular-safe-delivery`

## Release Train

### Train A - `v1.1.x` (Conversion Core)

Scope:
1. Hero quote widget always-visible and conversion-safe behavior.
2. Address autocomplete integration + fallback path.
3. Guest-first checkout continuity.
4. Dead-end removal in quote -> checkout bridge.
5. Turkish encoding/label quality hardening.

Gate to close Train A:
1. `P0=0`, `P1=0`
2. Quote -> checkout -> order path passes live smoke.
3. Admin receives operable orders (state transitions + customer relation).

### Train B - `v1.2.0` (Design + Motion + Performance)

Scope:
1. Design System 2.0 token unification (landing + checkout).
2. Controlled GSAP/interaction motion rollout with reduced-motion fallback.
3. Premium card and section interactions under performance budget.
4. Live trust badges and conversion signal polishing.

Gate to close Train B:
1. No mobile overflow/layout regression.
2. No interaction lock for forms/buttons.
3. Performance budget within agreed threshold.

## Workstream Breakdown

1. WS-A Funnel Core
- Owner skills: conversion-funnel-delivery + backend-delivery
- Output: stable quote, checkout continuity, guest flow

2. WS-B Design System
- Owner skills: design-system-2-0 + frontend-delivery
- Output: tokenized visual consistency and typography hierarchy

3. WS-C Motion and Performance
- Owner skills: motion-performance-governor + frontend-delivery
- Output: premium motion with strict safeguards

4. WS-D Release and Operations
- Owner skills: delivery-governor + release-gate
- Output: evidence-driven GO/NOGO and rollback readiness

## Detailed Execution Sequence (Deploy Last)

1. Freeze and baseline
- Record live version, current tag, current release symlink, DB backup ref.

2. Train A implementation on branch set
- One branch per workstream, no mixed concern commits.

3. Train A test gates
- `./scripts/run-quality-gate.ps1`
- `node scripts/qa/http-runtime-smoke.mjs`
- `node scripts/qa/mobile-regression-check.mjs`
- strict env smoke for release mode

4. Train A staging/UAT and GO
- Execute parity matrix and business flow checks.
- If GO: release as `v1.1.x`.

5. Train B implementation
- Apply design token and motion changes incrementally behind flags where needed.

6. Train B performance and accessibility gate
- Verify reduced-motion behavior.
- Verify no conversion drop in smoke/probe metrics.

7. Final release gate
- `P0/P1=0`
- quality gate green
- rollback proven
- release note complete

8. Production rollout (last step)
- atomic cutover
- opcache reset
- postdeploy smoke (30-60 min monitoring)

## Risk Controls

1. Maps dependency risk
- Keep fallback mode alive.
- For location-critical release, enforce:
```powershell
powershell -ExecutionPolicy Bypass -File scripts/run-phase2-live-smoke.ps1 -EnvFile .env.hostinger.production -StrictEnv -RequireMapsKey -ReleaseMode payments_off
```

2. Motion regression risk
- Start with critical sections only.
- Disable non-critical motion on low-power/reduced-motion contexts.

3. Scope creep risk
- Train A and Train B separated.
- No Train B work in Train A release branch.

## Exit Criteria

Program can be marked complete only when:
1. Train A released and stable on live.
2. Train B released with performance/accessibility gates green.
3. All plan items in detailed task list are closed or explicitly deferred with owner/date.
