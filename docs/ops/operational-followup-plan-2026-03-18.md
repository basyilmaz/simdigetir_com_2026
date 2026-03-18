# Operational Follow-up Plan (2026-03-18)

## Purpose

This plan separates post-release operational work from the already shipped `v1.2.x` release train so the next engineering cycle starts on a clean scope.

## Backlog

| ID | Priority | Area | Task | Acceptance Criteria | Target Release Mode |
|---|---|---|---|---|---|
| O-01 | P1 | Maps | Install real `GOOGLE_MAPS_API_KEY` and enable maps-required gate | Production env has valid key, address autocomplete uses live provider, `-RequireMapsKey` gate passes | `payments_off` or later |
| O-02 | P0 | Payments | Complete PAYTR merchant integration | Merchant credentials stored securely, callback verified in sandbox, checkout card flow works end-to-end, rollback path documented | `payments_on_paytr` |
| O-03 | P1 | Operations | Onboard real courier/support production data | Courier queue, support queue, and related admin views show operational records and empty-state guidance is no longer primary runtime state | runtime ops |
| O-04 | P2 | Repo Hygiene | Remove stray local files and broken remote refs | `git status` clean on delivery machine, no broken `desktop.ini` ref warnings from git commands | next dev train |

## Execution Order

1. O-04 repo hygiene baseline
2. O-01 maps key installation and gate enablement
3. O-02 PAYTR sandbox to live rollout
4. O-03 live operations data onboarding

## Detailed Notes

### O-01 Maps Key

Required steps:
1. Obtain production-grade API key with billing enabled.
2. Install key in production `.env`.
3. Re-run:
   - `./scripts/run-phase2-live-smoke.ps1 -EnvFile .env.hostinger.production -StrictEnv -RequireMapsKey`
4. Verify quote widget uses provider-backed autocomplete and still has manual fallback.

### O-02 PAYTR

Required steps:
1. Add real merchant credentials to env and secret store.
2. Validate callback URL and signature handling on Hostinger.
3. Run checkout card-payment integration scenario with sandbox credentials first.
4. Only after sandbox pass, prepare a dedicated `payments_on_paytr` release checklist and rollback note.

### O-03 Courier/Support Data

Required steps:
1. Seed or onboard minimum viable live operational records.
2. Validate dispatch, proof, support ticket, and notification views against real data.
3. Confirm empty-state UX is still safe when no records exist.

### O-04 Repo Hygiene

Required steps:
1. Decide whether `crop_logo.py` belongs in the repo.
2. If it belongs, move it into a deliberate tooling path and document usage.
3. If not, remove it from the delivery machine worktree.
4. Clean broken `desktop.ini` refs from `.git/refs/remotes`.

## Exit Criteria

This follow-up plan is considered closed when:
1. `GOOGLE_MAPS_API_KEY` is live and maps-required gate passes.
2. PAYTR sandbox and live readiness evidence is documented.
3. Courier/support modules are populated with real operational data.
4. Delivery machine worktree is clean before the next release train starts.
