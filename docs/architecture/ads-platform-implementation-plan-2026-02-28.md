# Ads Platform Implementation Plan (Google + Meta)

Date: 2026-02-28
Owner: Engineering
Status: Kickoff

## 1. Goal

Build a modular ad management platform that supports:
- Google Ads and Meta Ads account connections
- Campaign creation/update/pause
- Conversion tracking (web + server-side)
- Attribution and performance reporting
- Admin governance and auditability

## 2. Module Boundaries

Create new modules:
- `Modules/AdsCore`
- `Modules/AdsGoogle`
- `Modules/AdsMeta`
- `Modules/Attribution`
- `Modules/Reporting`

Do not couple business logic into `Landing` or `Leads` directly.

## 3. Phase Plan

### Phase 0: Foundation (Sprint 1)
- Create `AdsCore` schema and shared contracts.
- Add admin resources for:
  - connections
  - campaigns (draft state)
  - conversion events (read-only)
- Add secure token storage strategy.
- Add queue jobs skeleton for sync/push.

Acceptance:
- migrations up/down clean
- admin can create draft campaign record
- no external API call yet

### Phase 1: Google Integration (Sprint 2)
- Implement `GoogleAdsProvider`.
- OAuth connect/refresh flow.
- Create campaign and pause/resume flow.
- Conversion push for lead/order/payment events.

Acceptance:
- one happy-path campaign publish end-to-end
- conversion push idempotent by `external_id`

### Phase 2: Meta Integration (Sprint 3)
- Implement `MetaAdsProvider`.
- Meta account/pixel linkage.
- Campaign + adset + ad create/update/pause.
- CAPI server-side conversion events.

Acceptance:
- one happy-path campaign publish end-to-end
- CAPI events observable in logs

### Phase 3: Attribution + Reporting (Sprint 4)
- Build attribution ingestion:
  - `utm_*`, `gclid`, `fbclid`, click id references
- Build daily ETL for platform metrics.
- Add dashboard widgets and filters.

Acceptance:
- platform spend + lead + revenue + ROAS visible
- date/platform/campaign filters operational

### Phase 4: Governance + Hardening (Sprint 5)
- RBAC permissions:
  - `ads.view`, `ads.manage`, `ads.publish`, `ads.report`
- alerting for token expiry and sync failures
- retry/backoff and dead-letter handling
- release checklist and rollback playbook

Acceptance:
- operational alarms active
- go/no-go checklist documented

## 4. Data Model (Initial)

Core tables:
- `ad_connections`
- `ad_campaigns`
- `ad_adsets`
- `ad_ads`
- `ad_creatives`
- `ad_events`
- `ad_conversions`
- `ad_sync_logs`

Each record must include:
- `platform` enum (`google`, `meta`)
- `external_id` where applicable
- `status`
- `payload` json for provider-specific fields
- audit fields (`created_by`, `updated_by`) where needed

## 5. Integration Contracts

Define interface in `AdsCore`:
- `connect(array $credentials): ConnectionResult`
- `refreshToken(AdConnection $connection): TokenResult`
- `createCampaign(CampaignDTO $dto): PublishResult`
- `updateCampaign(string $externalId, CampaignDTO $dto): PublishResult`
- `pauseCampaign(string $externalId): PublishResult`
- `pushConversion(ConversionDTO $dto): ConversionResult`
- `fetchInsights(InsightsQuery $query): InsightsResult`

## 6. Security and Compliance

- Encrypt all access tokens at rest.
- Never log raw secrets.
- Hash PII fields for server-side conversion payloads.
- Add policy checks for every admin write action.

## 7. Testing Strategy

- Unit tests:
  - DTO mapping
  - provider contract behavior
  - idempotency keys
- Feature tests:
  - admin flows
  - queue jobs
  - conversion pipeline
- Integration tests:
  - mock gateways first
  - sandbox smoke tests after

Mandatory gate after each implementation batch:
- `./scripts/run-quality-gate.ps1`

## 8. Immediate Next Tasks (Start Now)

1. Scaffold `Modules/AdsCore` with migrations and models.
2. Add Filament resources for `ad_connections` and `ad_campaigns`.
3. Add provider interfaces and mock implementations.
4. Add first conversion event pipeline from existing lead submissions.
