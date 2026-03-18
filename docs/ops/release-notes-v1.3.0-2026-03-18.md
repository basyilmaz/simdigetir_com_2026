# Release Notes v1.3.0

Date: 2026-03-18
Scope: checkout/account/tracking customer surfaces, landing shell/forms, legal/FAQ rendering, selected admin UX copy surfaces

## Summary

This release refreshes the public customer journey without changing public route names or core order/checkout API contracts.

Primary goals:

- remove landing-level duplicated page script execution
- improve checkout, account, and tracking copy with admin-manageable content
- restore tracking event/proof visibility on the customer tracking page
- harden public legal pages against missing database content
- improve selected admin empty states and settings UX

## Included Changes

### Checkout / Customer Portal

- checkout entry, login, register, and tracking surfaces now read support/help copy from admin-managed settings
- customer registration requires explicit legal acceptance
- checkout shell uses corrected Turkish document language and clearer support links
- tracking page auto-refresh remains fetch-based and now updates:
  - summary
  - status timeline
  - courier events
  - delivery proofs

### Landing / Public Forms

- landing layout no longer renders pushed page scripts twice
- contact, corporate, and courier forms use inline feedback instead of blocking alert flows
- cookie banner and offcanvas shell orchestration remains stable under shared layout execution
- FAQ surface keeps semantic accordion behavior

### Legal Pages

- legal document controller now has safe known-slug fallback content for:
  - `/kvkk`
  - `/cerez-politikasi`
  - `/kullanim-kosullari`
- this prevents public 500 behavior when DB records are missing or temporarily unavailable
- legal document TOC rendering remains active when HTML headings are available

### Admin UX

- checkout copy blocks remain editable from settings
- landing/order/admin-facing UX enhancements from the current worktree are included in this train

## Validation

Automated validation:

- targeted tests:
  - `tests/Feature/PublicOrderTrackingTest.php`
  - `tests/Feature/LandingStandardPagesDynamicContentTest.php`
  - `tests/Feature/Sprint2FoundationTest.php`
- full gate:
  - `./scripts/run-quality-gate.ps1`

Result:

- `238 passed`

Browser smoke on local runtime:

- `/iletisim`
- `/siparis-takip`
- `/cerez-politikasi`
- `/kullanim-kosullari`

Observed result:

- pages rendered
- no console errors on checked pages

## Risk Notes

- this commit contains a broad but internally consistent UX/content/admin package already present in the current worktree
- no public route rename was introduced
- no core checkout session or order API contract was intentionally changed

## Release Decision

Status: release candidate ready for commit/push
