# Live Backend Admin Auth Smoke

Date: 2026-03-15
Environment: Production (`simdigetir.com`)
Validator: Codex

## Scope

Authenticated admin smoke executed against:

- `/admin`
- `/admin/orders`
- `/admin/orders/4`
- `/admin/leads`
- `/admin/leads/3`
- `/admin/payment-transactions`
- `/admin/notification-templates`

## Authentication

Validated with live super-admin account:

- Email: `admin@simdigetir.com`
- Role: `super-admin`

## Results

### Dashboard

Verified:

- page loads after login
- lead widget label is `Bugün Gelen Talepler`
- widget description is `Bekleyen yeni talepler: 3`
- sidebar label is `Bildirim Şablonları`

### Orders List

Verified:

- page title: `Siparişler`
- `Müşteri` column is populated
- latest rows show `Smoke Pickup`
- `Tutar` is rendered as `150,00 ₺`
- values are no longer displayed as inflated minor units

### Order Detail

Verified on `/admin/orders/4`:

- `Müşteri` field is visible
- `Müşteri İletişim` field is visible
- `Toplam Tutar` is `150,00 ₺`

### Leads List

Verified:

- page title: `Talepler`
- `Firma` column is present
- latest rows are `İletişim` leads and show empty company cells as expected for current data
- no misleading broken rendering was observed

### Lead Detail

Verified on `/admin/leads/3`:

- `Aktivite Akışı` renders cleanly
- no broken repeatable rows
- timeline items visible:
  - `Talep oluşturuldu`
  - `Kaynak bilgisi`
  - `Durum`

### Payment Transactions

Verified:

- page title: `Ödemeler`
- `Tutar` values render as `150,00 ₺`
- pending records display `Henüz işlenmedi`
- created/processed fallback wording is visible and consistent

### Notification Templates

Verified:

- page title: `Bildirim Şablonları`
- empty-state heading is shown
- helper CTA `Varsayılan Şablonları Hazırla` is visible
- explanatory empty-state copy is visible

## Outcome

Status: `PASS`

## Residual Notes

- Current live data contains only `contact` leads in the sampled latest rows, so `Firma` remains empty there by data shape, not by rendering defect.
- Payment provider remains `mockpay` because production is intentionally running in `payments_off` mode.
