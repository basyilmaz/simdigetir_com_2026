# Customer Order Flow Revision Plan (2026-03-14)

## Purpose

- This plan defines the end-to-end customer ordering flow for SimdiGetir.
- It is directly linked to `docs/ops/localhost-ui-ux-verification-plan-2026-03-14.md`.
- The existing localhost plan remains valid, but its hero quote wizard work must now be treated as the first dependency slice of the full customer order journey.

## Target Customer Journey

1. Visitor enters the site as guest or logged-in customer.
2. Visitor enters pickup address, dropoff address, and shipment type.
3. System calculates price and ETA.
4. Visitor reviews the quote and clicks `Siparis Ver`.
5. If guest, system moves into customer authentication/registration.
6. Customer enters sender and recipient details.
7. Customer chooses whether sender and recipient are the same person.
8. Customer selects payment method:
   - credit card
   - bank transfer
   - cash
9. If cash is selected, customer selects payment timing:
   - pay on pickup
   - pay on delivery
10. Order is created and enters dispatchable workflow.
11. Customer receives SMS updates:
   - courier assigned / on the way if needed
   - courier picked up the package
   - package delivered
12. Courier uploads pickup proof image when item is collected.
13. Courier uploads delivery proof image when item is delivered.
14. Customer can track order status from the frontend portal.

## Current Truth From Code

### Already Present

- Public quote API exists: `POST /api/v1/quotes`
- Quote engine resolves price from pricing rules and distance estimate.
- Authenticated order creation exists: `POST /api/v1/orders`
- Card payment initiation exists through `PaymentController`.
- Courier delivery proof exists through `CourierController@deliver`.
- Generic SMS/email notification infrastructure exists through `NotificationOrchestrator`.
- Landing hero quote widget exists on localhost.

### Missing Or Incomplete

- Guest cannot create an order today because `/api/v1/orders` is behind `auth:sanctum`.
- Public/self-service customer registration endpoint does not exist.
- Customer auth is email/password only; there is no phone-first auth or OTP flow.
- `users` table has no `phone` field, which conflicts with SMS-first delivery operations.
- Cash and bank transfer payment flows do not exist as first-class order/payment contracts.
- Pickup proof upload does not exist in courier workflow.
- SMS dispatch is generic/manual; there are no automatic order lifecycle triggers wired to order events/transitions.
- Customer panel routes exist, but they are demo-style and not protected as a real customer portal.
- Hero quote widget is still mostly config-driven, not backend-managed from `Modules/Landing`.

## Architectural Position

The current system has working domain building blocks, but not a real checkout orchestration layer.

That means we should not patch this only in Blade or only in controllers. We need a controlled revision across:

- `Modules/Landing`
- a new customer checkout/orchestration module
- core order/payment domain contracts
- courier proof workflow
- notification automation

## Recommended Module Boundaries

### 1. `Modules/Landing`

Responsibility:
- Marketing entry
- Hero quote wizard rendering
- Backend-managed wizard labels, options, steps, and CTA behavior

Must own:
- quote widget copy
- service type options
- wizard step labels
- guest/register prompts
- portal CTA visibility

Must not own:
- order business rules
- payment state machine
- customer identity persistence

### 2. New `Modules/Checkout`

Responsibility:
- Guest-to-order orchestration
- Multi-step checkout state
- Customer-facing order wizard API/web surface

Must own:
- checkout session or draft session state
- wizard step validation
- guest to auth handoff
- sender/recipient form logic
- payment option selection
- frontend portal order creation flow

Reason:
- This avoids bloating `Modules/Landing` with operational logic.
- It also keeps marketing UI separate from order orchestration.

### 3. Core Order Domain (`app/Domain/Orders`, `app/Models/Order`)

Responsibility:
- Final persisted order contract
- Order state machine
- order-level metadata needed by dispatch, finance, support

Needs revision for:
- payment method
- payment timing
- payer role
- checkout snapshot
- pickup proof support

### 4. Core Payment Domain (`app/Domain/Payments`, `PaymentController`)

Responsibility:
- Payment initiation and callback handling

Needs revision for:
- offline payment methods as first-class flows
- bank transfer pending/reconcile path
- cash collection model

### 5. Courier Workflow (`CourierController`, `CourierOrderWorkflowService`)

Responsibility:
- accept, pickup, deliver

Needs revision for:
- pickup proof upload
- better delivery and pickup media handling
- possible mandatory proof rules based on shipment type

### 6. Notifications Domain

Responsibility:
- Automated order lifecycle SMS and email dispatch

Needs revision for:
- transition-driven notification triggers
- customer phone normalization
- admin-manageable event templates for pickup and delivery status

## Key Product Decisions

### A. Authentication Model

Approved for Phase 1:
- Start with phone + password.
- Leave OTP for Phase 2.

Reason:
- Your flow is operationally phone-centric.
- SMS notifications are mandatory.
- Pure email/password-only auth is not aligned with this use case.

Practical rollout:
- Phase 1: add `phone` as required customer identity field and use phone + password login/register.
- Phase 2: add OTP login/register and optionally make password secondary.

### B. Quote To Order Strategy

Recommendation:
- Do not create abandoned `orders` for every quote attempt.

Use this model:
- quote is created in `pricing_quotes`
- checkout state is stored in a `checkout session`
- final `order` is created only after auth + sender/recipient + payment selection

Reason:
- cleaner operations
- less abandoned order noise
- simpler admin views

### C. Proof Model

Approved for Phase 1:
- Start directly with a unified `order_proofs` table.

Implementation direction:
- use `stage = pickup|delivery`
- keep proof types such as `photo|signature|otp`
- migrate current delivery proof behavior into the unified structure

### D. Payment Model

Recommendation:
- Treat payment as two layers:
  - `payment_method`: card | bank_transfer | cash
  - `payment_timing`: prepaid | pickup | delivery

Examples:
- card => prepaid
- bank transfer => prepaid_pending_confirmation
- cash at pickup => cash + pickup
- cash at delivery => cash + delivery

Approved for Phase 1:
- Cash: only `Teslimatta Ode`
- Bank transfer: admin reconcile flow only

Phase 1 allowed combinations:
- `card + prepaid`
- `bank_transfer + prepaid_pending_confirmation`
- `cash + delivery`

## Gap Matrix

| Area | Current | Required |
|---|---|---|
| Hero quote widget | Exists, mostly config-driven | Fully backend-managed from `Modules/Landing` |
| Guest checkout | Missing | Checkout session + auth handoff |
| Customer registration | Missing | Public customer registration/login flow |
| Customer phone | Missing in `users` | Required field and normalized storage |
| Sender/recipient model | Partially covered by pickup/dropoff fields | Full wizard semantics with same-person toggles |
| Card payment | Exists | Keep and integrate into wizard |
| Cash payment | Missing | Add payer timing and payer role logic |
| Bank transfer | Missing as customer flow | Add pending confirmation + admin reconcile UX |
| Pickup proof | Missing | Add upload requirement on pickup |
| Delivery proof | Exists | Keep and expose in portal/admin |
| SMS automation | Infra exists only | Auto-trigger on order lifecycle events |
| Customer portal | Demo-level | Real authenticated portal and tracking |

## Delivery Slices

### Slice 0: Architecture Freeze And Contract Design

Goal:
- Freeze the final contract before UI expansion.

Outputs:
- API contract for checkout session
- order field additions
- payment method/timing decision
- proof model decision
- SMS event list

Phase 1 approved decisions:
- Auth: phone + password
- Proof model: unified `order_proofs`
- Cash: delivery only
- Bank transfer: admin reconcile

Acceptance:
- No UI coding before contract is approved.

### Slice 1: Backend-managed hero quote wizard

Dependency:
- This is the current `localhost-ui-ux-verification-plan` Slice 1 and stays first.

Goal:
- Make the hero quote widget fully admin-manageable from `Modules/Landing`.

Outputs:
- wizard labels/options in landing payload
- service types managed from backend
- quote widget behavior driven by landing content

Acceptance:
- Non-technical admin can manage the hero quote entry state.

### Slice 2: Checkout session and wizard shell

Goal:
- Add a real multi-step checkout layer without persisting final orders too early.

Outputs:
- new `Modules/Checkout`
- checkout session create/update/finalize endpoints
- steps:
  - quote
  - auth/register
  - sender/recipient
  - payment
  - confirmation

Acceptance:
- Guest can reach confirmation step without creating broken orders.

### Slice 3: Customer identity and auth revision

Goal:
- Make customer identity usable for SMS and future portal access.

Outputs:
- add `phone` to users
- add normalized customer identity rules
- public register/login flow
- plan for OTP activation

Acceptance:
- New customer can register and continue checkout without admin help.

### Slice 4: Order contract and payment extension

Goal:
- Make final order persistence match real operations.

Outputs:
- order fields for payment method/timing/payer role
- checkout snapshot or equivalent structured metadata
- card, cash, and bank transfer paths
- admin reconcile path for bank transfer

Acceptance:
- All requested payment options can be represented without notes-field hacks.

### Slice 5: Courier proof workflow revision

Goal:
- Capture proof at both collection and delivery.

Outputs:
- pickup proof upload
- delivery proof remains supported
- proof files visible to admin and customer portal where needed

Acceptance:
- Courier cannot complete required pickup/delivery stages without proof when rules require it.

### Slice 6: SMS lifecycle automation

Goal:
- Make status updates automatic and template-driven.

Outputs:
- order lifecycle notification event map
- automatic dispatch on:
  - order created
  - courier assigned
  - pickup completed
  - delivery completed
- NETGSM-backed template use

Acceptance:
- No manual notification API call is required for core customer SMS updates.

### Slice 7: Customer portal and order tracking UI

Goal:
- Turn the current frontend from lead collection into operational self-service.

Outputs:
- authenticated customer portal
- orders list
- order detail
- tracking timeline
- proof visibility where appropriate

Acceptance:
- Customer can see active and completed orders without admin intervention.

## Data Model Revisions

### Required

- `users.phone`
- customer phone normalization policy
- order payment fields:
  - `payment_method`
  - `payment_timing`
  - `payer_role`
- checkout snapshot or structured equivalent
- unified `order_proofs` table with at least:
  - `order_id`
  - `courier_id`
  - `stage`
  - `proof_type`
  - `proof_value`
  - `file_url`
  - `metadata`
  - `created_at`

### Strongly Recommended

- saved customer addresses in later phase

### Can Wait

- advanced address book
- multiple recipients
- coupon/promo logic

## Notification Event Map

Recommended event keys:

- `orders.checkout_started`
- `orders.order_created`
- `orders.courier_assigned`
- `orders.pickup_completed`
- `orders.delivery_completed`
- `orders.payment_pending_bank_transfer`

Minimum SMS coverage for your requested scenario:

- pickup completed
- delivery completed

Recommended additional SMS coverage:

- order confirmed
- courier assigned / approaching pickup

Phase 1 baseline:
- `orders.order_created`
- `orders.pickup_completed`
- `orders.delivery_completed`
- `orders.payment_pending_bank_transfer`

## Dependency On `localhost-ui-ux-verification-plan`

That plan stays active, but its scope changes:

- old Slice 1 becomes foundation for this new program
- old Slice 2 remains valid and should be executed after the wizard becomes multi-step aware
- old Slice 3 is still optional and should come after portal/auth decisions

So the dependency chain is:

1. backend-managed hero quote widget
2. checkout session contract
3. customer auth and identity
4. order/payment revision
5. pickup/delivery proof workflow
6. SMS automation
7. customer portal

## Recommended Execution Order

1. Approve final customer flow contract and payment/proof model.
2. Complete backend-managed quote wizard in `Modules/Landing`.
3. Build `Modules/Checkout` multi-step session flow.
4. Add customer phone-first registration/auth.
5. Extend order and payment contracts.
6. Add pickup proof and delivery proof parity.
7. Wire SMS automation to lifecycle transitions.
8. Build real customer portal/tracking UI.

## Phase 1 Frozen Scope

This scope is now approved for implementation:

1. Hero quote widget becomes fully backend-managed from `Modules/Landing`.
2. New `Modules/Checkout` orchestrates guest-to-auth-to-order multi-step flow.
3. Customer registration/login uses `phone + password`.
4. Order payment contract supports:
   - card prepaid
   - bank transfer pending admin reconcile
   - cash on delivery
5. Proof system uses unified `order_proofs` from day one.
6. SMS automation covers order created, pickup completed, delivery completed, and bank transfer pending instructions.
7. Customer portal comes after checkout and lifecycle contracts are stable.

## First Technical Execution Batch

Recommended first implementation batch:

1. `Modules/Landing`
   - move quote widget content/options fully into landing payload
   - keep config only for emergency disable and infra timeout
2. `Modules/Checkout`
   - define checkout session contract
   - build step shells: quote, auth, sender-recipient, payment, confirm
3. Customer auth contract
   - add `users.phone`
   - add public register/login by phone + password
4. Order/payment schema revision
   - add payment method/timing/payer fields
   - add checkout snapshot structure
5. Proof schema revision
   - create `order_proofs`
   - migrate delivery proof flow to unified proof layer

This is the minimum safe batch before customer-facing payment and courier media UX can be considered reliable.

## GO / NOGO For Starting Implementation

### GO if:

- payment method model is approved
- proof model direction is approved
- auth direction is approved
- `Modules/Landing` vs `Modules/Checkout` boundary is approved

### NOGO if:

- we try to build UI before checkout contract is frozen
- we keep email-only auth while expecting SMS-first operations
- we keep proof logic only at delivery stage
- we keep cash/bank transfer as informal notes instead of explicit contracts

## Recommendation

Do not treat this as a pure frontend enhancement anymore.

This is now a product-flow revision program with one marketing dependency:
- `localhost-ui-ux-verification-plan` Slice 1

My recommendation is to start with contract approval and then execute Slices 1 to 3 as a single controlled batch. That gives us the right foundation before touching payment, proof, and SMS automation.

## Progress Log (2026-03-14)

1. Slice 1 completed:
   - hero quote widget is now backend-managed from `Modules/Landing`
   - landing admin can control widget copy, labels, service types, base amounts, and fallback ETA values
2. Slice 2 foundation completed:
   - `Modules/Checkout` created
   - checkout session endpoints added:
     - `POST /api/v1/checkout-sessions`
     - `GET /api/v1/checkout-sessions/{token}`
     - `PATCH /api/v1/checkout-sessions/{token}`
     - `POST /api/v1/checkout-sessions/{token}/finalize`
3. Slice 3 completed for Phase 1 baseline:
   - `users.phone` added
   - public `phone + password` register/login enabled
4. Slice 4 backend contract completed for Phase 1 baseline:
   - `orders` extended with `payment_method`, `payment_timing`, `payer_role`, `checkout_snapshot`
   - checkout finalize flow now supports:
     - `card + prepaid`
     - `bank_transfer + prepaid`
     - `cash + delivery`
   - bank transfer reconcile now promotes order state to `paid` or `failed`
5. Slice 5 backend foundation completed:
   - unified `order_proofs` table added
   - courier pickup and delivery flows now write to shared proof model
6. Automated verification status:
   - targeted checkout / courier / finance / schema tests passed
   - full `./scripts/run-quality-gate.ps1` passed
   - sqlite migration status check confirms new migrations are present but still pending on the persistent local sqlite file until explicitly applied there
7. Remaining Phase 1 items:
   - sender/recipient same-person UX semantics on checkout UI
   - SMS lifecycle automation
   - customer portal UI and tracking screens
8. Slice 6 completed:
   - SMS lifecycle automation wired for:
     - `orders.order_created`
     - `orders.payment_pending_bank_transfer`
     - `orders.pickup_completed`
     - `orders.delivery_completed`
   - bank transfer SMS now injects admin-managed payment instruction placeholders
9. Slice 7 completed:
   - public tracking page added: `GET /siparis-takip`
   - customer portal login/dashboard/detail flow added:
     - `GET /hesabim/giris`
     - `GET /hesabim`
     - `GET /hesabim/siparisler/{orderNo}`
10. Card payment step hardened:
    - checkout finalize no longer stops at placeholder messaging
    - `POST /api/v1/checkout-sessions/{token}/payments/initiate` now opens the real provider initiation step
    - checkout success state exposes `Kart odemesine gec` CTA and can resume a pending payment attempt
    - payment provider assumptions were generalized so checkout no longer hardcodes Iyzico-specific readiness logic
    - PAYTR sandbox/scaffold gateway and env placeholders were added for post-agreement activation
11. Admin usability completed for notification templates:
    - Filament `Bildirim Sablonlari` resource added
    - known lifecycle events now show placeholder explanations and default body guidance in admin
12. Remaining work after this batch:
    - live PAYTR credential smoke and callback verification after agreement
    - real `PAYTR_MERCHANT_ID`, `PAYTR_MERCHANT_KEY`, `PAYTR_MERCHANT_SALT`, and `PAYTR_CALLBACK_SECRET` are still absent until commercial setup is completed
13. Localhost-linked landing hardening completed:
    - hero slider now pauses while the user interacts with the quote widget
    - optional header/offcanvas B2B CTA is now backend-managed from landing hero payload
14. Customer portal dashboard polish completed:
    - `/hesabim` now supports query-driven `state` and `search` filters
    - active/delivered/failed style filtering can be applied without affecting other customers
    - dashboard toolbar and quick filter chips persist current search context
15. Release/preflight hardening completed for the PAYTR decision:
    - `scripts/run-phase2-live-smoke.ps1` is now provider-aware instead of assuming Iyzico
    - strict env validation now accepts `mockpay` when `PAYMENT_REQUIRED=false`
    - when PAYTR is selected later, strict env validation will check PAYTR merchant keys instead of Iyzico fields
    - current local Hostinger preflight result is `GO` with payment disabled and PAYTR reserved for post-agreement activation

## Current Remaining Work

1. Commercial activation step:
   - complete PAYTR agreement
   - fill real merchant credentials
   - enable `PAYMENT_REQUIRED=true`
2. Post-agreement smoke:
   - run strict env + API smoke against PAYTR-enabled environment
   - verify real callback contract and finalize webhook normalization
3. Deployment execution:
   - staging/live parity confirmation
   - production cutover checklist:
     - `docs/ops/hostinger-paytr-cutover-runbook-2026-03-15.md`
