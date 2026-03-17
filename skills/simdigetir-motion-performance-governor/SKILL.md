---
name: simdigetir-motion-performance-governor
description: Govern premium motion rollout (GSAP/Lottie/interactive effects) with strict performance budgets, reduced-motion accessibility, and regression-safe release sequencing for SimdiGetir. Use when adding animation-heavy UI enhancements.
---

# SimdiGetir Motion Performance Governor

Add premium motion without harming speed and usability.

## Workflow

1. Motion scope contract
- Define animation targets per section.
- Classify each effect:
- must-have
- optional
- experimental

2. Performance budget
- Keep net JS increase bounded.
- Remove redundant libraries before adding new ones.
- Define fallback for low-power/mobile devices.

3. Accessibility
- Respect `prefers-reduced-motion`.
- Disable non-essential motion for reduced-motion users.
- Keep keyboard and form interactions stable.

4. Progressive activation
- Activate motion in phases:
- hero first
- cards/sections second
- decorative effects last
- Use feature flags for risky motion blocks.

5. Regression checks
- Validate no layout shift/horizontal overflow.
- Validate no interaction lock on forms and CTA buttons.

6. Release gate
- Mandatory gate:
```powershell
./scripts/run-quality-gate.ps1
```
- Runtime checks:
```powershell
node scripts/qa/http-runtime-smoke.mjs
node scripts/qa/mobile-regression-check.mjs
```

## Non-Negotiables

- No animation that blocks checkout interaction.
- No production rollout without reduced-motion fallback.
- No large dependency add without measurable value.
