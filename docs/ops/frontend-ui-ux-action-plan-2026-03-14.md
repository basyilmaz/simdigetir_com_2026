# Frontend UI UX Action Plan (2026-03-14)

## Source

- Audit input: `docs/ops/frontend-ui-ux-developer-audit-2026-03-14.md`
- Goal: Move landing-only experience to platform-like experience without breaking current production flow.

## Key Findings (Confirmed)

1. Current site quality is strong for landing page UX (responsive, visual quality, trust signals).
2. Core weakness is low interactivity (no instant quote, no visible product experience).
3. Customer flow is still mostly CTA to phone/WhatsApp; platform capabilities are not surfaced in UI.
4. Terminal-level mojibake görüntüsü ile dosya-byte seviyesindeki gerçek encoding birbirinden ayrıştırılmalı.
5. Audit rapor dosyaları byte seviyesinde UTF-8 (BOM'suz) durumda doğrulandı.

## Priority Backlog

| Priority | Work Item | Scope Boundary | Acceptance Criteria | Owner Skill |
|---|---|---|---|---|
| P0 | UTF-8 hygiene guardrail and verification | docs + landing views + hygiene script | UTF-8 BOM-free validated, mojibake token check active | `modular-safe-delivery` |
| P0 | Hero "Instant Quote" widget MVP | `Modules/Landing` + existing quote API contract | User enters pickup/dropoff, async quote + ETA visible in hero | `frontend-delivery` + `backend-delivery` |
| P1 | Glassmorphism visual pass (controlled) | landing CSS/components only | Cards/nav/forms get consistent glass tokens, mobile safe | `frontend-delivery` |
| P1 | CTA attribution upgrade | landing CTA buttons + analytics hooks | Clicks segmented by CTA type in analytics | `marketing-growth` |
| P2 | Customer dashboard lite discovery | separate module plan only | Route map + auth model + API map approved | `backend-delivery` |
| P2 | Live fleet map discovery | feature spike only | Data source, map stack, perf budget documented | `frontend-delivery` |

## Execution Plan (Deploy-Last)

1. Freeze scope to P0 only for first iteration.
2. Build and test Instant Quote widget MVP on staging.
3. Run mandatory gate: `./scripts/run-quality-gate.ps1`
4. Run runtime checks:
   - `node scripts/qa/http-runtime-smoke.mjs`
   - `node scripts/qa/mobile-regression-check.mjs`
5. GO/NOGO review with release checklist evidence.
6. Production deploy only after all gates are green.

## Immediate Actions (Start Now)

1. Keep audit/report files in UTF-8 no BOM and enforce mojibake guard in hygiene checks.
2. Open implementation spec for Hero Instant Quote:
   - input fields
   - validation and fallback behavior
   - quote API payload/response mapping
   - loading/error UI states
3. Define KPI baseline before launch:
   - CTA click-through rate
   - form lead conversion rate
   - quote widget usage rate

## Risks and Controls

- Risk: Visual redesign may regress mobile stability.
  - Control: mobile regression check must pass before merge.
- Risk: Quote API errors may reduce trust.
  - Control: deterministic fallback message and retry path.
- Risk: Scope creep (dashboard/map) delays value.
  - Control: keep first release to P0 only.

## Exit Criteria for This Plan

- P0 items complete and tested.
- No new P1/P2 work merged in same release.
- Release evidence recorded in `docs/ops/*checklist*` and QA report paths.

## Progress Log (2026-03-14)

1. P0 encoding verification started.
2. Four audit reports validated as UTF-8 BOM-free.
3. `scripts/hygiene/check-file-hygiene.php` updated with suspicious mojibake token detection.
4. Hero Instant Quote MVP implemented via `resources/views/landing/sections/hero-instant-quote.blade.php`.
5. Feature flag + runtime knobs added in `Modules/Landing/config/config.php` (`landing.quote_widget_*`).
6. Home hero wired to widget include and analytics events (`quote_widget_view`, `quote_submit_click`, `quote_success`, `quote_error`, CTA click events).
7. Regression coverage added in `tests/Feature/LandingDynamicContentTest.php` (widget enabled/disabled rendering).
8. Mandatory quality gate passed (`144 tests, 737 assertions`).
9. Runtime smoke scripts require a responsive local HTTP runtime; latest local attempt failed on request timeout / connection profile and is marked as environment follow-up before GO.
10. P1 controlled glassmorphism pass applied on landing surfaces (`header-main-bar`, `hero-card`, `service-card`, `feature-card`, `blog-card`, `.glass`, form controls) with mobile-safe blur fallback.
11. P1 CTA attribution upgrade added: `cta_click` event now tracks `cta_channel`, `cta_context`, `cta_label` and keeps existing `click_call/click_whatsapp` events.
12. Runtime-blocking fix applied in `Modules/Settings/app/Models/Setting.php`: fast DB reachability probe before settings query/cache read.
13. Runtime QA checks rerun successfully:
    - `node scripts/qa/http-runtime-smoke.mjs` (PASS)
    - `node scripts/qa/mobile-regression-check.mjs` (PASS)
