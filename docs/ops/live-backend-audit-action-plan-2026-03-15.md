# Live Backend Audit Action Plan

**Date:** 2026-03-15
**Source audit:** `docs/ops/live-backend-audit-report-2026-03-15.md`
**Scope:** Admin backend operational defects reported on live (`/admin`)
**Planning mode:** Fix planning only. No production behavior change in this document.

## 1. Purpose

This plan converts the live backend audit findings into an execution order with:

- verified vs unverified findings
- module boundaries
- P0/P1/P2 priority
- test and release gates

## 2. Verification Summary

The report was compared against current code, not accepted blindly.

### Confirmed in code

1. **Order amount formatting defect**
   - File: `app/Filament/Resources/OrderResource.php`
   - Current code prints integer amount directly:
   - `number_format((int) $state) . ' TL'`
   - If DB stores cents/kurus, live output will be 100x too large.

2. **Payment amount formatting defect**
   - File: `app/Filament/Resources/PaymentTransactionResource.php`
   - Same direct integer formatting issue exists there too.

3. **Order detail customer visibility is weak**
   - File: `app/Filament/Resources/OrderResource.php`
   - Detail form exposes only `customer_id`.
   - Even if relation exists, admin UX is not sufficient.

4. **Dashboard lead counter mismatch is structurally real**
   - File: `app/Filament/Widgets/StatsOverview.php`
   - Dashboard widget uses `today()` lead count.
   - Sidebar badge in `LeadResource` uses `status = new`.
   - These are different semantics, so mismatch is expected.

5. **Payment transaction date gap is structurally plausible**
   - File: `app/Filament/Resources/PaymentTransactionResource.php`
   - List uses `processed_at`.
   - In `payments_off` + `mockpay` flow, pending/mock rows may never set `processed_at`.

6. **Lead activity feed risk is real enough to treat as fix-target**
   - File: `app/Filament/Resources/LeadResource/Pages/ViewLead.php`
   - Timeline is assembled manually through `RepeatableEntry`.
   - The report's "blank/repeated rows" complaint is consistent with a render-state issue worth direct reproduction.

### Needs reproduction before coding

1. **Order list customer column fully blank**
   - File uses `customer.name` and `customer.email`.
   - This may be caused by:
     - null `customer_id` in live records
     - relation hydration issue
     - missing eager load
     - legacy data before customer binding existed

2. **Lead list company column fully blank**
   - File already defines `company_name`.
   - This is likely not a table definition bug.
   - Root cause is more likely:
     - live data missing
     - write pipeline not filling `company_name`
     - list records coming from lead types where company is naturally empty

3. **Empty modules**
   - Kuryeler / Fiyat Kurallari / Destek Talepleri / Bildirim Sablonlari being empty may be:
     - true no-data state
     - admin onboarding gap
     - missing seed/setup
   - This is not automatically a code defect.

### Not a defect under current release mode

1. **`mockpay` on live**
   - Current production release is intentionally `payments_off`.
   - Real gateway activation is deferred until PAYTR commercial activation.
   - This remains an operational note, not a code bug, unless payment activation scope starts.

## 3. Module Boundaries

### Primary target modules

- `app/Filament/Resources/OrderResource.php`
- `app/Filament/Resources/PaymentTransactionResource.php`
- `app/Filament/Resources/LeadResource.php`
- `app/Filament/Resources/LeadResource/Pages/ViewLead.php`
- `app/Filament/Widgets/StatsOverview.php`

### Secondary validation targets

- `app/Models/Order.php`
- `app/Models/PaymentTransaction.php`
- lead intake flow under `Modules/Leads`
- admin widgets touching lead counters

### Non-target areas for this batch

- public checkout UX
- customer portal
- courier mobile flows
- PAYTR activation

## 4. Priority Plan

## P0 - Admin operational blockers

These directly impact daily order, lead, and finance operations.

1. **Orders: customer visibility fix**
   - Goal:
     - list must show a usable customer identity
     - detail page must show customer name/email/phone, not only ID
   - Required checks:
     - null-customer legacy rows
     - fallback display when record has no bound customer
     - possible eager loading

2. **Orders: money formatting fix**
   - Goal:
     - `total_amount` must render as human currency consistently in list/detail/export
   - Rule to freeze:
     - amounts are stored in kurus/cents and displayed divided by 100

3. **Payments: money formatting + date clarity**
   - Goal:
     - payment amounts display correctly
     - "Islem Tarihi" should not look broken for pending/mock rows
   - Candidate resolution:
     - keep `processed_at` as true processing time
     - add fallback label or fallback display to `created_at`
     - make pending/mock state explicit

4. **Leads: activity feed repair**
   - Goal:
     - lead detail page must render a clean deterministic timeline
   - Approach:
     - reproduce with fixture data
     - replace fragile repeatable rendering if needed

## P1 - Data consistency and reporting correctness

1. **Leads: company column root-cause fix**
   - Goal:
     - determine whether this is missing data, wrong write path, or display bug
   - Only after root cause:
     - fix intake mapping or admin display fallback

2. **Dashboard vs badge semantics**
   - Goal:
     - remove misleading mismatch
   - Options:
     - align both to same query
     - or relabel dashboard widget so it no longer conflicts with sidebar badge meaning

3. **CSV/export parity**
   - Orders export should use same corrected amount format.
   - Payments export should use same corrected amount format.
   - Leads export should include `company_name` if operationally required.

## P2 - Operational readiness / empty module review

1. **Couriers module**
   - determine whether zero couriers is expected or broken onboarding

2. **Pricing rules module**
   - determine if pricing is DB-rule driven or code/config driven
   - if pricing rules page is intended for ops, seed/minimum records may be required

3. **Support tickets module**
   - determine if flow is unlaunched or broken

4. **Notification templates module**
   - verify whether "empty" is acceptable after template auto-bootstrap strategy
   - verify Turkish label/encoding normalization

## 5. Execution Order

1. Reproduce P0 defects locally with targeted fixtures/tests.
2. Fix `OrderResource` and `PaymentTransactionResource` together to keep money contract consistent.
3. Fix `ViewLead` activity timeline rendering.
4. Re-test admin list/detail pages with targeted feature coverage.
5. Only then move to P1 data consistency items.
6. Treat P2 as ops readiness review, not automatic coding work.

## 6. Test Strategy

### Required targeted tests

1. `OrderResource` admin rendering
   - customer relation present
   - customer relation missing fallback
   - amount display formatting

2. `PaymentTransactionResource`
   - amount display formatting
   - pending/mock transaction date behavior

3. `LeadResource/ViewLead`
   - company field rendering
   - activity timeline rendering with:
     - source/campaign
     - notes
     - updated_at != created_at
     - sparse/null values

4. `StatsOverview`
   - if semantics are changed, widget and badge expectations must be locked by tests

### Mandatory gate after code changes

```powershell
./scripts/run-quality-gate.ps1
```

## 7. Release Gate

### GO only if

- no open P0 defects remain
- targeted admin regression tests are green
- full quality gate is green
- admin smoke confirms:
  - orders customer visibility
  - correct amount formatting
  - lead activity feed rendering
  - payment date behavior is understandable

### NOGO if

- admin list/detail still hides customer identity
- monetary amounts still render 100x inflated
- lead activity view still breaks or repeats blank rows

## 8. Recommended First Delivery Batch

**Batch A (P0 only):**

1. Order customer visibility
2. Order money formatting
3. Payment money formatting + transaction date clarity
4. Lead activity feed repair

This is the correct first implementation slice because it removes the most damaging live admin defects without pulling PAYTR or unrelated frontend flows into scope.
