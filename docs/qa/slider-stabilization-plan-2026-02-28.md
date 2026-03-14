# Slider Stabilization Plan - 2026-02-28

## Scope
- Home hero slider (`resources/views/landing/home.blade.php`)
- Slider image pipeline (fallback + responsive URL)
- Mobile/desktop slider behavior consistency

## Step-by-Step Plan
1. Inventory and baseline
- Identify all slider/carousel components and active image sources.
- Confirm runtime image URLs from rendered page.

2. Image integrity audit
- Verify slider image request status (`200`) in localhost runtime.
- Mark missing/broken images and prepare fallback list.

3. Hardening implementation
- Add deterministic fallback image for slider visual.
- Add `onerror` fallback to avoid broken hero visual.
- Add fixed intrinsic dimensions to reduce CLS.
- Guard Swiper init and improve autoplay behavior.

4. UX consistency
- Disable nav arrows on small screens where they overlap content.
- Keep swipe support and loop behavior.

5. Verification
- Re-check slider image URLs and status.
- Run quality gate and record outcome.

## Current Findings
- Slider image source detected: `http://127.0.0.1:8000/images/kuryeman.jpg?w=768`
- Runtime request status: `200`
- Missing slider image detected: `No`

## Applied Changes
- Added resolved+fallback image flow for slide-2 hero image.
- Added `onerror` fallback to `images/kuryeman.jpg`.
- Added intrinsic `width/height` for slider image.
- Added safer Swiper initialization and reduced-motion aware autoplay.
- Added visibility-aware autoplay stop/start.
- Hid slider arrows on `<=768px`.

## Acceptance Criteria
- Slider renders without broken image in all target breakpoints.
- Missing custom image still shows fallback visual.
- No JS errors when slider container is absent.
- Quality gate passes.
