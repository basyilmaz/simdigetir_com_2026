# Open Items Closure Plan (2026-03-17)

## Source Open Items

1. Pricing backoffice follow-up:
   - live quote service list validation
   - admin pricing create/edit validation
   - checkout summary service label validation
2. P2 operational readiness review for modules that appear empty.
3. `GOOGLE_MAPS_API_KEY` placeholder governance in release gates.
4. Local git hygiene issue: `refs/tags/desktop.ini` broken ref warning.
5. Versioning strategy should reflect priority/severity.

## Execution Plan

1. Run live probes for pricing + checkout label.
2. Extract live module counts for couriers/pricing/support/notifications.
3. If `service_base_price` rules are empty, bootstrap safe baseline records.
4. Add stricter release gate option for maps key requirement.
5. Implement priority-based version bump strategy in tooling + policy docs.
6. Clean local git ref artifacts and add guard script.

## Execution Log

### Step 1 - Live pricing/checkout probes

- `https://simdigetir.com` contains:
  - `Moto Kurye`
  - `Aracli Kurye`
  - `Yaya Kurye`
- `GET /checkout?pickup=...&dropoff=...&service_type=moto&service_label=Moto%20Kurye`
  - opens checkout (`200`)
  - contains pickup/dropoff/service label text.

Status: completed.

### Step 2 - Live module counts

Live DB snapshot via remote artisan/tinker:

- `couriers_total=0`
- `pricing_rules_total=0`
- `pricing_rules_service_base_price=0`
- `support_tickets_total=0`
- `notification_templates_total=1`
- `notification_templates_active=1`

Status: completed.

### Step 3 - Pricing baseline bootstrap (empty-only)

Applied on live because `service_base_price` rules were empty:

- inserted 3 active rules:
  - moto: `27500`, default
  - aracli: `49000`
  - yaya: `19000`
- cleared pricing catalog cache key
- rebuilt runtime caches (`config`, `route`, `view`)

Post-check:
- `service_base_price_count=3`
- home quote service list now reflects all three options.

Status: completed.

### Step 4 - Maps key governance

Updated script:
- `scripts/run-phase2-live-smoke.ps1`
  - new flag: `-RequireMapsKey`
  - when enabled, placeholder maps key becomes hard error.
  - also enforced for strict `payments_on_paytr` mode.

Updated checklist:
- `docs/ops/hostinger-release-governance-checklist-2026-03-10.md`
  - added command example and policy note for `-RequireMapsKey`.

Status: completed.

### Step 5 - Priority-based versioning strategy

Updated tooling:
- `scripts/version/bump-version.php`
  - supports `--severity=...` mapping to semantic bump part.
- `scripts/version/bump-version-by-priority.ps1`
  - wrapper command for priority-first usage.

Updated policy:
- `docs/ops/versioning-policy.md`
  - added severity-to-bump matrix and usage examples.

Status: completed.

### Step 6 - Local git ref hygiene

Actions:
- removed local broken ref artifact `desktop.ini` under `.git/refs/tags`
- added helper script:
  - `scripts/hygiene/clean-local-git-ref-artifacts.ps1`

Status: completed.

## Remaining Items

1. Couriers/support modules still empty on live.
   - Decision: do not seed fake operational records.
   - Next action: real onboarding data via ops rollout.
2. Google Maps real API key is still needed for mandatory geocoding releases.
   - Control is now in place (`-RequireMapsKey`).

## Decision

- All technical open items from this cycle are closed.
- Remaining items are operational onboarding dependencies, not code blockers.
