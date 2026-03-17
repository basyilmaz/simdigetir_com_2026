# Release Baseline Evidence v1.2.0 (2026-03-17)

## Scope

- Target release candidate: `v1.2.0`
- Branch: `feat/design-improvement-agents-orchestration-2026-03-17`
- Baseline tag before release prep: `v1.0.29`
- Baseline commit before release prep docs: `5e9bebd5`

## Runtime Baseline

- Last known live release note: `docs/ops/release-notes-v1.0.17.md`
- Last known live folder from note: `laravel_release_v1_0_17_poff_20260317_3`
- Current semantic version file target: `1.2.0`

## Environment Baseline

- `.env.hostinger.production` checksum (SHA256):
  - `824ABFF9CE6C514033CCB9D059ED1FD9A5E6C8D75F827CB1C61532C9443149CE`

## Database / Rollback Readiness

- Schema change in this scope: `none`
- DB migration required: `no`
- DB backup reference before cutover: `PENDING_BEFORE_CUTOVER`
- Reason:
  - no schema mutation exists in this release scope,
  - but release governance still requires a fresh pre-cutover backup id to be recorded on the server side.

## Release Scope Summary

- Theme-aware logo and favicon/runtime branding integration
- Raster favicon / PWA icon / OG image pack
- Landing motion and reduced-motion hardening
- Lottie courier micro-animation asset and lazy loader
- Motion performance budget guard automation
- QA smoke tooling fix for raster OG image validation

## Decision

- Baseline captured for release preparation: `YES`
- Production cutover permission from this document alone: `NO`
- Remaining operator-side requirement before cutover:
  - record actual DB backup reference
  - confirm previous release folder name for rollback target
