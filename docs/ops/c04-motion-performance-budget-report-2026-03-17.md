# C-04 Motion Performance Budget Guard Report (2026-03-17)

## Scope

Task: `C-04 | Motion performance budget guard`

Acceptance target:
1. JS/CSS/motion payload budgets are guarded by script.
2. Bundle diff is measurable against a baseline.
3. Lighthouse-style browser performance sample is captured for release evidence.

## Added Guard Automation

1. `scripts/qa/motion-performance-budget-guard.mjs`
- Crawls landing page asset references.
- Measures script/css/motion/lottie asset sizes.
- Enforces absolute and baseline-delta budget thresholds.
- Captures browser timing sample (`FCP`, `LCP`, `CLS`, `long-task total`).
- Writes report: `storage/app/qa/motion-budget/latest/report.json`

2. `scripts/qa/run-motion-performance-budget-guard.ps1`
- Optional local serve wrapper.
- Runs guard in controlled flow with `-Serve` and optional `-WriteBaseline`.

3. Baseline file:
- `docs/ops/budgets/motion-performance-budget-baseline.json`

## Budget Thresholds

1. Absolute budgets
- `maxScriptKb`: `1200`
- `maxStylesheetKb`: `260`
- `maxMotionKb`: `750`
- `maxLottieJsonKb`: `120`
- `maxLcpMs`: `8000`
- `maxCls`: `0.2`

2. Delta budgets (baseline diff)
- `maxScriptDeltaKb`: `120`
- `maxStylesheetDeltaKb`: `30`
- `maxMotionDeltaKb`: `120`
- `maxLottieJsonDeltaKb`: `30`

## Execution Evidence

Commands run:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/qa/run-motion-performance-budget-guard.ps1 -Serve -WriteBaseline
powershell -ExecutionPolicy Bypass -File scripts/qa/run-motion-performance-budget-guard.ps1 -Serve
```

Final report (`storage/app/qa/motion-budget/latest/report.json`) summary:

1. Budget result: `PASS`
2. Totals:
- `scriptKb`: `936.12`
- `stylesheetKb`: `190.79`
- `motionKb`: `687.07`
- `lottieJsonKb`: `36.74`
3. Bundle diff:
- `scriptKbDelta`: `-0.01`
- `stylesheetKbDelta`: `0`
- `motionKbDelta`: `0`
- `lottieJsonKbDelta`: `0`
4. Lighthouse-style browser sample:
- `FCP`: `4748 ms`
- `LCP`: `6244 ms`
- `CLS`: `0.0243`
- `Long Task Total`: `740 ms`
- `DOMContentLoaded`: `4849.2 ms`
- `Load Event End`: `5515.7 ms`

## GO/NOGO for C-04

Decision: `GO`

Reason:
1. Budget guard is automated and repeatable.
2. Baseline + delta model is active.
3. Current train changes are within thresholds and produce a passing report.
