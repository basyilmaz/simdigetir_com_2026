# Google Ads Live Hit Verification (2026-03-17)

## Scope

Verify that production (`https://simdigetir.com`) sends live Google Ads traffic for:

- Ads ID: `AW-17989545006`

## Method

1. Checked live HTML for loader and config lines.
2. Executed headless browser network capture (Playwright) on production home page.
3. Filtered requests/responses for Google Ads and collect endpoints.

## Evidence

### Loader and config are present in live HTML

- `https://www.googletagmanager.com/gtag/js?id=AW-17989545006`
- `gtag('config', 'AW-17989545006');`

### Live hit requests observed

Observed successful (`200`) requests for the same Ads account:

- `https://googleads.g.doubleclick.net/pagead/viewthroughconversion/17989545006/...`
- `https://www.google.com/ccm/collect?...&tid=AW-17989545006...`
- `https://www.google.com/pagead/1p-user-list/17989545006/...`

Also observed app event payload forwarding:

- `en=page_view`
- `en=quote_widget_view`

## Decision

`GO` for Google Ads base tag live hit visibility on production.

## Note for Ads UI (manual check)

In Google Ads UI:

1. Go to `Tools > Conversions > Diagnostics`.
2. Open Tag Assistant for `simdigetir.com`.
3. Validate the same ID appears: `AW-17989545006`.
4. Confirm recent activity window shows incoming hits/events.
