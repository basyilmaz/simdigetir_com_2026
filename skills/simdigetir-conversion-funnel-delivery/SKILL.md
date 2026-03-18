---
name: simdigetir-conversion-funnel-delivery
description: Deliver SimdiGetir quote-to-checkout conversion flow with guest-first ordering, map-assisted address input, real-time price preview, and one-page checkout continuity. Use when implementing or validating the customer funnel from landing to order creation.
---

# SimdiGetir Conversion Funnel Delivery

Optimize conversion without breaking current operations.

## Workflow

1. Funnel boundary
- Entry: landing hero quote widget
- Exit: order created + tracking entry visible
- Keep CTA fallback paths (WhatsApp, call) alive as safety net.

2. Hero quote hardening
- Keep quote widget always visible in hero (not hidden by slider states).
- Validate fields client-side and server-side.
- Provide deterministic error and retry states.

3. Address quality
- Integrate autocomplete with explicit fallback when external API fails.
- Never block checkout due to map provider issues.

4. Price preview continuity
- Show quote result and ETA clearly.
- Ensure "continue/order" action always exists.
- Keep tokenized checkout bridge healthy.

5. Checkout continuity
- Prefer guest checkout by default.
- Keep registration optional and non-blocking.
- Ensure payment mode rules (payments_off vs provider-active) are explicit.

6. Tracking and post-order
- Confirm order reference, tracking URL, and customer portal visibility.
- Confirm admin receives manageable order state.

7. Test and release evidence
- Run:
```powershell
./scripts/run-quality-gate.ps1
```
- Validate live smoke and strict env:
```powershell
powershell -ExecutionPolicy Bypass -File scripts/run-phase2-live-smoke.ps1 -EnvFile .env.hostinger.production -StrictEnv -ReleaseMode payments_off
```

## Acceptance Core

- User can complete quote -> checkout -> order without registration.
- No dead-end button in funnel.
- Admin can see and operate order state.
