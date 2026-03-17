# Release Notes v1.2.0 (2026-03-17)

## Scope

- Theme-aware branding refresh for light and dark mode
- Raster favicon, PWA icon, Apple touch icon, and OG image pack
- Landing motion hardening with reduced-motion-safe behavior
- Courier CTA lottie micro-animation with lazy loading and graceful fallback
- Motion performance budget guard and baseline evidence
- QA smoke tooling adjustment for raster OG validation

## Key Release Changes

1. Branding
- new light and dark logo assets are wired into the shared logo component
- favicon runtime sync follows current theme
- manifest icon references now point to real sized PNG assets

2. Frontend motion and UX
- hero and testimonial motion respect reduced-motion contexts
- GSAP phased motion remains guarded
- courier CTA uses lazy-loaded lottie with fallback state handling

3. QA / release tooling
- quality gate remains mandatory
- local parity evidence captured
- Hostinger payments-off preflight passed

## Status

- Semantic target version: `1.2.0`
- Preflight status: `GO`
- Production cutover: `NOT_EXECUTED_YET`

## Remaining Final Ops Steps

1. Record fresh DB backup reference on Hostinger
2. Confirm previous release folder for rollback target
3. Execute atomic cutover
4. Run postdeploy monitoring pack (`D-05`)
