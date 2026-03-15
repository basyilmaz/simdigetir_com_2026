# Hero Instant Quote MVP Spec (2026-03-14)

## Scope

- Frontend surface: landing hero section (`/`).
- Backend contract: existing quote endpoint only (no breaking contract change).
- Non-goals for MVP:
  - Payment step
  - Full order creation wizard
  - Customer login/dashboard

## UX Flow

1. User enters pickup and dropoff addresses.
2. User optionally selects service type (default: moto kurye).
3. User clicks `Fiyat Hesapla`.
4. UI shows:
   - estimated price range
   - estimated delivery time
   - confidence/fallback notice (if map distance unavailable)
5. CTA options shown after successful quote:
   - `WhatsApp ile Devam Et`
   - `Beni Arayın`

## UI Components

1. Pickup input (`text` + autocomplete ready slot)
2. Dropoff input (`text` + autocomplete ready slot)
3. Package/service selector (`select`)
4. Submit button with loading state
5. Quote result card
6. Error and fallback messages

## API Contract Mapping (MVP)

- Request mapping:
  - `pickup_address` <- pickup input
  - `dropoff_address` <- dropoff input
  - `service_type` <- selector
- Response mapping:
  - `estimated_total` -> main price label
  - `estimated_duration_minutes` -> ETA label
  - `distance_km` (if available) -> detail text
  - fallback indicator -> `Mesafe servisi geçici olarak yaklaşık hesaplama kullanıyor.`

## Validation Rules

1. Pickup required (min 5 chars).
2. Dropoff required (min 5 chars).
3. Pickup and dropoff cannot be identical.
4. Disable submit while loading.
5. Timeout > 8s should show retry state.

## Error Handling

1. `422`: show field validation hints under inputs.
2. `429`: show rate-limit message + 30s retry suggestion.
3. `5xx` / network: show generic fallback and keep form values.
4. Unknown: log to console + show friendly message.

## Analytics Events

1. `quote_widget_view`
2. `quote_submit_click`
3. `quote_success`
4. `quote_error`
5. `quote_cta_whatsapp_click`
6. `quote_cta_call_click`

## Accessibility and Mobile

1. Keyboard tab order must be logical.
2. `aria-live="polite"` for quote result updates.
3. Button/input tap targets minimum 44px height on mobile.
4. Mobile layout must keep result card above fold after submit.

## Delivery Plan

1. Implement feature behind setting flag (`landing.quote_widget_enabled`).
2. Test:
   - `./scripts/run-quality-gate.ps1`
   - `node scripts/qa/http-runtime-smoke.mjs`
   - `node scripts/qa/mobile-regression-check.mjs`
3. Release only after GO checklist passes.
