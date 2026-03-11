# Module Runtime Status (2026-03-10)

## Local Codebase Status

Module discovery (`php artisan module:list`) shows all modules enabled:

- AdsCore
- AdsGoogle
- AdsMeta
- Attribution
- Landing
- Leads
- Reporting
- Settings

Representative regression set executed:

```bash
php artisan test \
  tests/Feature/LandingDynamicContentTest.php \
  tests/Feature/LandingStandardPagesDynamicContentTest.php \
  tests/Feature/LandingPageBuilderFoundationTest.php \
  tests/Feature/AdsPlatformFoundationTest.php \
  tests/Feature/AdsSprint2GoogleIntegrationTest.php \
  tests/Feature/AdsSprint3MetaIntegrationTest.php \
  tests/Feature/AdsSprint4AttributionReportingTest.php \
  tests/Feature/AdsSprint5GovernanceHardeningTest.php \
  tests/Feature/FormSubmissionWorkflowEnhancementsTest.php \
  tests/Feature/DashboardWidgetsMetricsTest.php \
  tests/Feature/ReleaseP0ReadinessTest.php
```

Result: **38 tests passed, 166 assertions**.

## Live Runtime Status (`https://simdigetir.com`)

| Module | Local status | Live route probe | Live status |
|---|---|---|---|
| Landing | Enabled + tests pass | `/`, `/hizmetler`, `/kurumsal` => `200` | PARTIAL (content/version drift exists) |
| Leads | Enabled + tests pass | `/api/leads` => `405`, `/admin/leads` => `302` | WORKING |
| Settings | Enabled + tests pass | `/admin/manage-settings` => `302` | WORKING |
| AdsCore | Enabled + tests pass | `/admin/ad-campaigns` => `404` | NOT DEPLOYED/NOT ACTIVE LIVE |
| AdsGoogle | Enabled + tests pass | depends on AdsCore admin/API surface | NOT DEPLOYED/NOT ACTIVE LIVE |
| AdsMeta | Enabled + tests pass | depends on AdsCore admin/API surface | NOT DEPLOYED/NOT ACTIVE LIVE |
| Attribution | Enabled + tests pass | `/api/v1/*` family returns `404` | NOT DEPLOYED/NOT ACTIVE LIVE |
| Reporting | Enabled + tests pass | `/api/v1/kpi/overview` => `404` | NOT DEPLOYED/NOT ACTIVE LIVE |

## Summary

- **Local repository state:** module set is coherent and test-green.
- **Live runtime state (2026-03-10):** partial/older release appeared active.
- **Live runtime state (2026-03-11 post-deploy):** parity restored.

Post-deploy probes:

- `/admin/ad-campaigns` => `302`
- `/admin/ad-conversions` => `302`
- `/admin/landing-pages` => `302`
- `/admin/orders` => `302`
- `/admin/couriers` => `302`
- `POST /api/v1/auth/login` (`Accept: application/json`) => `422`
- `/api/v1/ops/health` (`Accept: application/json`) => `200`
- `/api/v1/kpi/overview` (`Accept: application/json`) => `401`

Decision update:

- **GO** (module runtime parity acceptable for production baseline).
