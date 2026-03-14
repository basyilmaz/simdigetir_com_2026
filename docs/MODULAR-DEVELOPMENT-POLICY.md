# Modular Development Policy

## Goal

Prevent cross-module regressions while accelerating delivery.

## Rules

- Define target module(s) before editing.
- Do not modify unrelated module internals.
- Keep shared-layer edits explicit and small.
- Run `scripts/run-quality-gate.ps1` after code changes.

## Minimum Verification

- Changed PHP files pass lint.
- `php artisan test` passes.
- Regression risk is documented in PR/commit notes.
