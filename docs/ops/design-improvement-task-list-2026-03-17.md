# Design Improvement Detailed Task List (2026-03-17)

## Legend

- Priority: `P0` blocker, `P1` high, `P2` enhancement
- Track: `A` conversion core, `B` design system, `C` motion/performance, `D` release/ops
- Status: `todo`, `in_progress`, `done`, `deferred`

## Task Board

| ID | Priority | Track | Task | Acceptance Criteria | Validation | Status |
|---|---|---|---|---|---|---|
| A-01 | P0 | A | Hero quote widget always visible and interaction-safe | Widget is not hidden by slider state and remains usable on mobile/desktop | Landing smoke + browser check | done |
| A-02 | P0 | A | Address autocomplete integration with fallback | Address suggestion works; fallback entry still allows order if provider unavailable | API fail simulation + manual UI test | done |
| A-03 | P0 | A | Real-time quote preview without dead-end | User sees price/eta and at least one deterministic continue path | Quote E2E scenario | done |
| A-04 | P0 | A | Guest-first one-page checkout continuity | Guest can complete quote -> checkout -> order without forced register | Checkout feature tests + live smoke | done |
| A-05 | P1 | A | Checkout copy/encoding normalization | No mojibake strings in checkout and contact flow | UTF-8 hygiene + page assertions | done |
| A-06 | P1 | A | CTA funnel instrumentation consistency | CTA events include channel/context/label and remain analyzable | event payload probe | done |
| B-01 | P1 | B | Design token refactor (colors/surfaces/glass) | Shared token usage on landing + checkout, no random hardcoded style drift | visual diff + code grep | done |
| B-02 | P1 | B | Typography hierarchy unification | Display/heading/body/caption consistently applied in primary pages | snapshot/manual typography check | done |
| B-03 | P2 | B | Service cards premium hover polish | Hover effects active without layout jitter | mobile + desktop regression | todo |
| B-04 | P2 | B | Footer trust row + consistency polish | Footer trust and brand elements render consistently | runtime smoke + visual check | todo |
| C-01 | P1 | C | GSAP ScrollTrigger phased integration | Motion works in scoped sections and does not block interactions | browser E2E + reduced-motion check | todo |
| C-02 | P1 | C | Reduced-motion accessibility fallback | `prefers-reduced-motion` disables non-critical animations | accessibility test pass | todo |
| C-03 | P2 | C | Lottie delivery micro-animation | Lottie assets load lazily and fail gracefully | network throttle check | todo |
| C-04 | P2 | C | Motion performance budget guard | Added JS/CSS budget within agreed thresholds | bundle diff + Lighthouse sample | todo |
| D-01 | P0 | D | Baseline evidence capture before each train | live ref, DB backup ref, env checksum recorded | ops doc update | todo |
| D-02 | P0 | D | Mandatory gate run for each merge batch | quality gate green before merge | `run-quality-gate` | todo |
| D-03 | P0 | D | Staging parity matrix execution | no unresolved parity P0/P1 in scope | parity report | todo |
| D-04 | P0 | D | Release gate + rollback rehearsal | rollback command tested before GO | release checklist evidence | todo |
| D-05 | P1 | D | Postdeploy monitoring pack | 30-60 min smoke and error dashboard checks logged | postdeploy report | todo |

## Train Mapping

### Train A (`v1.1.x`)

Must include:
- A-01, A-02, A-03, A-04, A-05, D-01, D-02, D-03, D-04

Optional if low-risk:
- A-06

### Train B (`v1.2.0`)

Must include:
- B-01, B-02, C-01, C-02, C-04, D-01, D-02, D-03, D-04, D-05

Optional:
- B-03, B-04, C-03

## Definition of Done

1. Acceptance criteria met for all non-deferred tasks in release scope.
2. No open P0/P1 task in release scope.
3. `./scripts/run-quality-gate.ps1` passes.
4. Runtime smoke and mobile regression pass.
5. Release note and version/tag prepared.

## Notes

Operational follow-ups (not code blockers):
1. Courier/support real data onboarding still required in live operations.
2. Real `GOOGLE_MAPS_API_KEY` should be mandatory for location-critical rollouts via `-RequireMapsKey`.
