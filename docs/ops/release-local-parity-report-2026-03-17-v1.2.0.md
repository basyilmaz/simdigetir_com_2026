# Release Local Parity Report v1.2.0 (2026-03-17)

## Scope

Validate release-scope frontend/runtime parity for the `v1.2.0` candidate before Hostinger cutover.

Validated surfaces:

- landing branding and favicon links
- logo light/dark selection
- hero and section motion behavior
- courier CTA lottie asset path
- PWA / manifest icon references
- public page smoke reachability

## Evidence

1. HTTP runtime smoke
- Report:
  - `storage/app/qa/http-runtime-smoke/20260317-192357-v1_2_0_local/report.json`
- Result:
  - `7/7` pages passed
  - failed pages: `0`

2. Mobile regression
- Report:
  - `storage/app/qa/mobile-regression/20260317-192357-v1_2_0_local/report.json`
- Result:
  - samples: `28`
  - horizontal overflow findings: `0`
  - broken image findings: `0`

3. Motion performance budget
- Report:
  - `storage/app/qa/motion-budget/latest/report.json`
- Result:
  - pass: `true`
  - motion payload: `687.07 KB`
  - `LCP`: `6144 ms`
  - `CLS`: `0.0243`

## Tooling Adjustment Included In Scope

- `scripts/qa/http-runtime-smoke.mjs` was updated to accept raster OG assets:
  - `.jpg`
  - `.jpeg`
  - `.png`
  - `.webp`

Reason:

- release scope intentionally uses PNG OG assets,
- the previous smoke rule only allowed `.jpg`, which would create a false-negative gate failure.

## Decision

- Local release-scope parity: `GO`
- Staging/live cutover status: `PENDING`
- No unresolved local parity blocker was found inside the current release scope.
