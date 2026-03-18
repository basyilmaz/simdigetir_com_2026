---
name: simdigetir-design-system-2-0
description: Apply SimdiGetir Design System 2.0 across landing and checkout with consistent dark-tech tokens, typography hierarchy, glassmorphism intensity, and mobile-safe component standards. Use when implementing visual system upgrades or fixing visual inconsistency between pages.
---

# SimdiGetir Design System 2.0

Use one visual language across landing, checkout, and admin-facing customer flows.

## Workflow

1. Token baseline
- Define or update CSS variables for:
- primary, accent, success, surface, glass, border, glow
- Keep existing brand constraints unless explicitly changed.

2. Typography baseline
- Enforce one shared hierarchy:
- display, heading, subheading, body, caption, mono
- Avoid per-page font drift.

3. Component pass
- Apply tokens to:
- header/nav
- hero blocks
- service cards
- forms and inputs
- CTA buttons
- footer

4. Contrast and readability
- Verify text/background contrast and focus states.
- Ensure placeholders, labels, helper text are readable on dark surfaces.

5. Mobile safety
- Keep 320-430 widths stable.
- Prevent horizontal overflow and clipped cards.

6. Evidence and gate
- Capture before/after screenshots.
- Run mandatory gate:
```powershell
./scripts/run-quality-gate.ps1
```
- Run UI checks:
```powershell
node scripts/qa/http-runtime-smoke.mjs
node scripts/qa/mobile-regression-check.mjs
```

## Non-Negotiables

- No mixed light/dark checkout unless explicitly scoped.
- No hardcoded random colors outside token system.
- No visual-only deploy without regression evidence.
