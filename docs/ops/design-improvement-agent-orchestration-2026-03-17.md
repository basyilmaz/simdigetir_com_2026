# Design Improvement Agent Orchestration (2026-03-17)

## Objective

Execute `docs/ops/design-improvement-program-plan-2026-03-17.md` and `docs/ops/design-improvement-task-list-2026-03-17.md` with deterministic agent ownership, strict gates, and deploy-last discipline.

## Agent Roster

1. `simdigetir-delivery-governor`
- Role: program governance, branch discipline, execution order.

2. `project-tracking`
- Role: status tracking, completion %, done/partial/missing classification.

3. `simdigetir-conversion-funnel-delivery`
- Role: Train A conversion core tasks (`A-*`).

4. `simdigetir-frontend-backend-parity`
- Role: UI/API parity validation and dead-end detection.

5. `simdigetir-design-system-2-0`
- Role: Train B design token and typography unification (`B-*`).

6. `simdigetir-motion-performance-governor`
- Role: Train B motion and performance guardrail tasks (`C-*`).

7. `simdigetir-release-gate`
- Role: GO/NOGO evidence, rollback rehearsal, release readiness.

8. `simdigetir-admin-ads-ops`
- Role: Ads/funnel instrumentation verification for conversion visibility (`A-06` and postdeploy checks).

## Task-to-Agent Matrix

### Track A (Conversion Core)

- `A-01` -> conversion-funnel-delivery + frontend-backend-parity
- `A-02` -> conversion-funnel-delivery + backend-delivery
- `A-03` -> conversion-funnel-delivery + frontend-backend-parity
- `A-04` -> conversion-funnel-delivery + backend-delivery
- `A-05` -> conversion-funnel-delivery + frontend-delivery
- `A-06` -> admin-ads-ops + conversion-funnel-delivery

### Track B (Design System)

- `B-01` -> design-system-2-0 + frontend-delivery
- `B-02` -> design-system-2-0 + frontend-delivery
- `B-03` -> design-system-2-0
- `B-04` -> design-system-2-0

### Track C (Motion/Performance)

- `C-01` -> motion-performance-governor + frontend-delivery
- `C-02` -> motion-performance-governor
- `C-03` -> motion-performance-governor
- `C-04` -> motion-performance-governor + qa-test-automation

### Track D (Release/Ops)

- `D-01` -> delivery-governor + project-tracking
- `D-02` -> delivery-governor + qa-test-automation
- `D-03` -> frontend-backend-parity + qa-test-automation
- `D-04` -> release-gate + release-governance
- `D-05` -> release-gate + project-tracking

## Execution Order

1. Bootstrap governance and baseline
- Agents: delivery-governor, project-tracking
- Tasks: `D-01`

2. Train A implementation and closure
- Agents: conversion-funnel-delivery, frontend-backend-parity (+ backend/frontend as needed)
- Tasks: `A-01..A-05` then `A-06`

3. Train A mandatory gates
- Agents: delivery-governor, release-gate
- Tasks: `D-02`, `D-03`, `D-04`

4. Train B implementation and closure
- Agents: design-system-2-0, motion-performance-governor
- Tasks: `B-01..B-04`, `C-01..C-04`

5. Train B mandatory gates
- Agents: delivery-governor, release-gate
- Tasks: `D-02`, `D-03`, `D-04`, `D-05`

6. Production rollout (last)
- Agent: release-gate
- Requirement: `P0=0`, `P1=0`, quality gate green, rollback rehearsal green.

## Standard Gate Commands

```powershell
./scripts/run-quality-gate.ps1
node scripts/qa/http-runtime-smoke.mjs
node scripts/qa/mobile-regression-check.mjs
```

Location-critical release command (maps required):

```powershell
powershell -ExecutionPolicy Bypass -File scripts/run-phase2-live-smoke.ps1 -EnvFile .env.hostinger.production -StrictEnv -RequireMapsKey -ReleaseMode payments_off
```

## Operational Rule

No deploy or merge batch is approved unless `project-tracking` report shows:
1. scope completion state by task id,
2. no open `P0/P1` in release scope,
3. corresponding gate evidence links.