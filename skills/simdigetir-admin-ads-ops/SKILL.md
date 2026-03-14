---
name: simdigetir-admin-ads-ops
description: Operate and validate SimdiGetir ad platform flows in admin, including Meta connection settings, conversion pipeline health, Pixel/CAPI wiring, and conversion status verification. Use when configuring or troubleshooting ad operations safely.
---

# SimdiGetir Admin Ads Ops

Run ad operations with traceable checks.

## Workflow

1. Connection integrity
- Validate ad platform connection record exists and status is active.
- Verify required identifiers are present (account IDs, pixel IDs, tokens).

2. Tracking integrity
- Confirm browser signal (`PageView`) is detected.
- Confirm server-side conversion pipeline writes conversion records.

3. Admin usability checks
- Verify list/create/edit flows for:
- connections
- campaigns
- conversions
- Check empty-state and validation behavior.

4. Delivery verification
- Trigger a controlled lead/conversion event.
- Confirm event lifecycle status transitions (`pending` -> `sent` or equivalent).

5. Operational hardening
- Rotate or refresh tokens before expiry.
- Document token expiry and owner.
- Ensure secrets are never committed to repository.

6. Incident response
- For 4xx/5xx failures, collect:
- failing endpoint
- payload shape
- timestamp
- error text
- Determine retry-safe vs blocking errors.

## Output Template

- Area checked
- Result (`pass`/`fail`)
- Evidence (screen/code/log)
- Impact
- Corrective action

