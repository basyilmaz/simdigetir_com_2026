# Localhost UI UX Verification and Delivery Plan (2026-03-14)

## Scope

- Source audit: `docs/ops/localhost-ui-ux-audit-report-2026-03-14.md`
- Goal: verify localhost audit findings against code, identify real gaps, and define a controlled implementation order.
- Primary rule: frontend-facing landing behavior should be managed from backend modules wherever practical, especially the hero quote wizard.

## Verification Matrix

| Audit Item | Status | Evidence | Decision |
|---|---|---|---|
| Hero "Aninda Fiyat Hesapla" exists and is interactive | Confirmed | `resources/views/landing/sections/hero-instant-quote.blade.php` is included from `resources/views/landing/home.blade.php` | Keep and harden |
| Quote widget improves customer flow | Confirmed | Widget validates inputs, calls `/api/v1/quotes`, and exposes WhatsApp / call CTAs | Keep and harden |
| CTA flow is sequenced for conversion | Confirmed | Hero, service cards, footer, float button, and quote widget CTAs are wired and tracked in `resources/views/layouts/landing.blade.php` | Keep |
| Corporate forms render without JS / 500 issues | Confirmed | Corporate quote form remains active in `resources/views/landing/home.blade.php`; runtime smoke passed | Keep |
| Simulated courier status card creates platform feel | Confirmed | Existing hero terminal card remains in `resources/views/landing/home.blade.php` | Keep |
| Glassmorphism improvements are present | Confirmed | Controlled glass pass applied in `resources/views/layouts/landing.blade.php` | Keep and tune if needed |
| Preloader / app feel exists | Confirmed | Preloader is active in `resources/views/layouts/landing.blade.php` | Keep |
| Dark mode is stable | Confirmed | Theme token system and light/dark overrides exist in `resources/views/layouts/landing.blade.php` | Keep |
| No missing assets on localhost | Confirmed | `node scripts/qa/http-runtime-smoke.mjs` passed after runtime fix | Keep |
| DOM/render stability on localhost | Confirmed | Quality gate passed, mobile regression passed, no blocking runtime issue remains | Keep |
| Slider distracts user during quote input | Completed | Hero swiper now pauses on quote widget hover/focus via `landing:hero-quote-engage` / `landing:hero-quote-release` events | Keep |
| Backend manages quote wizard content and behavior | Completed | Quote widget content/options are now resolved from landing hero payload with config kept only for infra fallback / kill-switch behavior | Keep |
| Clear "Kurumsal Giris" CTA in header | Completed | Landing hero payload now exposes optional header B2B CTA config and shared layout renders it in header/offcanvas when enabled | Keep hidden by default |

## Key Gap Analysis

### 1. Backend-managed frontend gap

Current state:
- Hero copy is backend-manageable through `Modules/Landing`.
- Quote wizard business copy/options are still mostly config-driven:
  - `landing.quote_widget_enabled`
  - `landing.quote_widget.service_options`
  - `landing.quote_widget.base_amounts`
  - `landing.quote_widget.fallback_minutes`

Problem:
- This breaks the desired model of "backend controls frontend content".
- Non-technical admins cannot manage service labels, default amounts, helper texts, CTA labels, or widget visibility from the landing module.

Required target state:
- Hero section payload should own quote widget presentation/config.
- Frontend should render from `landingContent`.
- Config/env should remain only for technical kill-switch / timeout style infrastructure defaults.

### 2. Quote wizard UX control gap

Current state:
- Swiper now pauses on quote widget hover and focus.
- Quote widget dispatches explicit interaction lifecycle events consumed by hero swiper.

Result:
- Keyboard/mobile/form interaction no longer competes with autoplay while the user is engaged.

### 3. B2B entry point gap

Current state:
- Corporate page exists and supports lead flow.
- Header/offcanvas now support an optional backend-managed B2B CTA sourced from home hero payload.

Result:
- The portal entry point is now technically ready without forcing visibility before business rollout.

## Delivery Plan

### Slice 1: Backend-managed quote wizard

Scope:
- `Modules/Landing/app/Filament/Resources/LandingPageSectionResource.php`
- `Modules/Landing/app/Services/LandingContentResolver.php`
- `resources/views/landing/sections/hero-instant-quote.blade.php`
- related tests

Deliverables:
- Add hero payload fields for quote widget:
  - visibility
  - title
  - subtitle
  - pickup placeholder
  - dropoff placeholder
  - submit label
  - WhatsApp CTA label
  - call CTA label
  - service options
  - base amounts
  - fallback minutes
- Wire Landing admin form to edit these values.
- Resolve values through `landingContent`.
- Keep infra-only config for request timeout / emergency disable.

Acceptance criteria:
- Admin can change quote widget text/options from landing hero section.
- Home page reflects changes without code edits.
- Existing defaults remain safe when DB payload is absent.

### Slice 2: Quote interaction focus hardening

Scope:
- `resources/views/landing/home.blade.php`
- hero swiper script
- widget script

Deliverables:
- Pause hero autoplay while user interacts with quote widget.
- Resume autoplay after interaction ends.
- Prevent slide motion while user is typing.

Acceptance criteria:
- No slide change during focused widget interaction.
- Existing slider still works when user is not interacting.

### Slice 3: Header B2B portal CTA groundwork

Scope:
- landing header view logic
- landing backend-managed config/payload

Deliverables:
- Add optional header CTA config for:
  - label
  - URL
  - target behavior
  - enabled flag
- Keep hidden by default.

Acceptance criteria:
- CTA is invisible until enabled.
- When enabled, it renders without breaking existing header layout.

## Implementation Order

1. Slice 1: backend-managed quote wizard
2. Slice 2: slider focus/pause hardening
3. Slice 3: header B2B CTA groundwork
4. Re-run QA:
   - `./scripts/run-quality-gate.ps1`
   - `node scripts/qa/http-runtime-smoke.mjs`
   - `node scripts/qa/mobile-regression-check.mjs`

## Risks and Controls

- Risk: hero payload form becomes too large.
  - Control: keep fields grouped and use JSON only for repeatable service option structures where needed.
- Risk: widget config drift between DB payload and env config.
  - Control: define a strict precedence order: DB payload -> module defaults -> env infra fallback.
- Risk: slider pause logic regresses autoplay.
  - Control: add a minimal browser-facing interaction test and manual smoke on home hero.

## Recommendation

Start with Slice 1 immediately. It addresses the most important architectural concern: backend control over frontend behavior. Slice 2 should follow in the same release because it directly responds to the localhost audit feedback. Slice 3 is useful, but it is not a blocker for the current GO direction.

## Progress Log (2026-03-14)

1. Slice 1 completed:
   - hero quote widget content/options are now managed from landing payload
   - config remains as infra-level fallback / kill-switch only
2. Landing runtime and regression status:
   - `./scripts/run-quality-gate.ps1` passed
   - `node scripts/qa/http-runtime-smoke.mjs` passed
   - `node scripts/qa/mobile-regression-check.mjs` passed
3. Follow-on dependency work started outside this document:
   - `Modules/Checkout` session/finalize flow added
   - customer `phone + password` auth baseline added
   - order payment contract and shared proof model extended
4. Localhost plan closure status:
   - Slice 2: slider pause on widget focus/interaction completed
   - Slice 3: optional header B2B CTA groundwork completed
5. Related downstream slices completed after localhost verification:
   - `Modules/Checkout` now owns checkout session, finalize, public tracking, and customer portal flows
   - checkout card flow now continues into provider initiation instead of stopping at placeholder text
   - admin notification templates now have a Filament management screen with explicit placeholder guidance
6. Additional UI hardening completed:
   - standard landing pages now inherit the shared header B2B CTA even when they do not have their own DB-managed page payload
   - targeted landing regression and full quality gate passed after the layout/resolver hardening
7. Release readiness follow-up completed:
   - hostinger preflight strict env step no longer assumes Iyzico
   - payment-disabled environments now correctly pass with `PAYMENT_DEFAULT_PROVIDER=mockpay`
   - PAYTR merchant validation is reserved for the post-agreement activation phase
