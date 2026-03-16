# Pricing Backoffice Integration Plan - 2026-03-15

## Validated Current State

### Already present
- Admin pricing management exists in [PricingRuleResource.php](/C:/YazilimProjeler/simdigetir_com_2026/app/Filament/Resources/PricingRuleResource.php)
- Quote API and pricing domain already exist
- Checkout, order creation, tracking, and admin order visibility already work live
- Order state change action exists in list view and is now being exposed in order detail view as well

### Actual gaps
1. Landing hero quote widget still uses `Landing` payload service defaults/base amounts as UI/runtime fallback, not a fully explicit backoffice-driven service catalog.
2. Browser reports about missing checkout CTA are not consistently reproducible on current live, but the CTA clarity can still be strengthened.
3. Order operations UX was weaker on detail page than on list page.
4. Lead company visibility is semantically mixed: `contact` type may legitimately be blank, `corporate_quote` must not be blank.

## Delivery Goal
Move pricing and operational control toward an explicit backend-managed model without breaking the current working guest->checkout->order flow.

## Target Modules
- `Modules/Landing`
- `Modules/Checkout`
- `Pricing` domain / `PricingRuleResource`
- `OrderResource`
- `LeadResource`

Non-target modules for now:
- Ads platform
- Corporate API credentials/payment activation
- Courier mobile/runtime changes beyond order state visibility

## Phase Plan

### P0 - Operational UX Hardening
1. Expose `Durum Degistir` on order detail page as well as list page.
2. Re-validate live admin operability.
3. Keep `Lead` company display semantically clear for `corporate_quote` vs `contact`.

### P1 - Pricing Backoffice Source of Truth
1. Audit current quote widget inputs against quote API contract.
2. Separate:
   - marketing copy/config (`Landing`)
   - pricing computation source (`Pricing` domain)
3. Introduce backend-managed quote service catalog/preset source so the hero widget is not dependent on static/fallback service base amounts.
4. Ensure admin can change pricing behavior from pricing screens, not from landing-only JSON.

### P2 - Frontend Pricing Transparency
1. Make hero result CTA more explicit after quote success.
2. Show which service type is being quoted from backend data.
3. Add guard text when fallback pricing/default service assumptions are used.

### P3 - Integrated E2E Matrix
1. Guest quote -> checkout -> order -> admin order
2. Admin state transition -> customer portal reflection
3. B2B form -> lead list/detail with company visibility
4. Manipulated price payload -> server-side rejection

## Acceptance Criteria
- Pricing behavior changes in admin are reflected in quote results without landing-only hardcoded tuning.
- Order detail page allows operational state change directly.
- `corporate_quote` lead rows show `Firma`; missing values are flagged explicitly.
- E2E matrix passes on live/staging with browser evidence.

## Recommended Next Slice
Implement `P1 - Pricing Backoffice Source of Truth` first.

Reason:
- checkout is already working
- admin order actions are a local UX fix
- the larger structural gap is pricing ownership and module boundaries

## Progress Update

### Completed on 2026-03-15
1. `Order` detail page now exposes `Durum Degistir` in the header as well as list view.
2. Live production was updated and browser-verified for the admin order detail action.
3. A new pricing source-of-truth layer was introduced:
   - `PricingServiceCatalog` now resolves quote service options from active pricing rules
   - landing hero quote widget consumes pricing catalog service options before landing fallback payload
4. `PricingRuleResource` was upgraded to support user-friendly `service_base_price` rules:
   - service key
   - visible service label
   - base price in TL
   - fallback ETA minutes
   - default service flag
5. Checkout now carries `service_label` so downstream modules display human-readable service names instead of only the raw service key.

### Remaining follow-up
1. Couriers/support operational onboarding (real data rollout).
2. Optional: maps-key-required release mode enforcement on location-critical campaigns.

### Closure update (2026-03-17)
1. Live pricing baseline was applied because `service_base_price` rules were empty.
2. Live probes confirmed:
   - home quote widget now lists `Moto Kurye`, `Aracli Kurye`, `Yaya Kurye`
   - checkout summary carries service label in tokenized session flow
3. Admin pricing create/edit path remains available in Filament resource and is covered by:
   - `tests/Feature/PricingBackofficeIntegrationTest.php`
