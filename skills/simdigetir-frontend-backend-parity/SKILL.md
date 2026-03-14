---
name: simdigetir-frontend-backend-parity
description: Validate parity between frontend user journeys and backend capabilities for SimdiGetir. Use when checking whether backend modules, APIs, and business flows are fully represented in UI flows and when producing GO/NOGO parity matrices.
---

# SimdiGetir Frontend-Backend Parity

Build a route-to-route and flow-to-flow parity audit.

## Workflow

1. Inventory backend capabilities
- Extract API routes, auth model, and core module operations.
- Group by domain: orders, courier, tracking, payments, corporate, leads, ads.

2. Inventory frontend entry points
- Map landing routes, forms, CTA paths, panel links, and admin screens.
- Distinguish public pages vs authenticated panels.

3. Build parity matrix
- For each backend capability assign one status:
- `covered`
- `partial`
- `not_exposed`
- `blocked`

4. Validate with deterministic evidence
- Code references for each mapping.
- Runtime checks for critical URLs and actions.

5. Surface risk class
- `P0`: security/data exposure mismatch.
- `P1`: promised capability missing in UI.
- `P2`: content/UX inconsistencies.

6. Produce GO/NOGO statement
- `GO` only if no parity-related `P0/P1`.
- Otherwise `NOGO` with exact remediation list.

## Output Template

- Backend capability
- Frontend mapping
- Status
- Evidence (file/route)
- Risk class
- Required action

