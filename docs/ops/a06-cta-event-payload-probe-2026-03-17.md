# A-06 CTA Event Payload Probe (2026-03-17)

## Scope

Task: `A-06 | CTA funnel instrumentation consistency`

Objective:
1. Ensure CTA events carry a common analyzable payload contract.
2. Keep quote-widget CTA events aligned with global CTA tracking.

## Payload Contract

All CTA events are standardized around:
1. `cta_channel`
2. `cta_context`
3. `cta_label`
4. `cta_href` (where link-based context exists)

Quote funnel events also include:
1. `service_type`
2. `quote_no`
3. `checkout_path` (for checkout routing actions)

## Instrumented Events

1. `cta_click`
2. `click_call`
3. `click_whatsapp`
4. `quote_start_checkout_click`
5. `quote_cta_whatsapp_click`
6. `quote_cta_call_click`

## Probe Method

Automated page-level probe:
1. Feature test validates payload key visibility in rendered JS contract.
2. Assertion file: `tests/Feature/LandingDynamicContentTest.php`
3. Probe test: `test_home_contains_cta_funnel_instrumentation_payload_contract`

Command:

```powershell
php artisan test --filter=LandingDynamicContentTest
```

## Result

Probe status: `PASS` (payload contract present and event calls include payload objects).
