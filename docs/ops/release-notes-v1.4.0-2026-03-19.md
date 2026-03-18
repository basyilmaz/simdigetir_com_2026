# Release Notes v1.4.0

Date: 2026-03-19
Scope: checkout/account/tracking parity with landing shell, checkout wizard parity, shared support shell rollout

## Summary

This release aligns the public transactional surfaces with the main landing experience without changing public route names or core checkout/order contracts.

Primary goals:

- remove the "separate product" feel from checkout/account/tracking pages
- move public checkout/account surfaces onto a shared landing-aligned shell
- keep design-token parity across transactional views
- preserve existing checkout session, auth, and payment flow contracts

## Included Changes

### P0 / P1 Public Surface Parity

- added shared public checkout shell component:
  - `Modules/Checkout/resources/views/components/layouts/public.blade.php`
- moved these public views onto the shared shell:
  - `/siparis-takip`
  - `/hesabim/giris`
  - `/hesabim/kayit`
  - `/hesabim`
  - order detail / receipt pages
  - `/checkout`
- customer-facing pages now use the same header, theme toggle, offcanvas navigation, and footer/version layer as landing
- support/help content remains dynamic via existing settings-backed content resolver flow

### P2 Checkout Wizard Parity

- `/checkout/{token}` wizard now uses the same public shell instead of the isolated mini layout
- wizard top section was rebuilt as landing-aligned hero/support cards
- support channels are injected from `CheckoutContentResolver`
- wizard styles were normalized back to shared design-token surfaces:
  - `var(--sg-card-dark)`
  - `var(--sg-card-dark-muted)`
  - `var(--sg-card-dark-soft)`
  - shared typography and spacing tokens

### Contracts Preserved

- no public route rename
- no checkout session schema change
- no order finalize contract change
- no payment method contract change
- no tracking endpoint contract change

## Validation

Automated validation:

- targeted suites:
  - `tests/Feature/CheckoutPageTest.php`
  - `tests/Feature/CustomerPortalTest.php`
  - `tests/Feature/PublicOrderTrackingTest.php`
  - `tests/Feature/DesignTokenContractTest.php`
- full gate:
  - `./scripts/run-quality-gate.ps1`

Result:

- `239 passed`

## Risk Notes

- local browser smoke for tokenized checkout runtime was partially limited by a local MySQL-down environment; automated feature coverage remained green
- release scope is intentionally contained to `Modules/Checkout` public surfaces plus related feature tests
- no backend schema or public API migration is bundled in this train

## Release Decision

Status: release candidate ready for commit, push, and controlled Hostinger cutover
