# Repo Hygiene Baseline (2026-03-18)

## Scope

This note records the first hygiene cleanup completed immediately after merging the `v1.2.1` release train into `master`.

## Issues Found

1. Local delivery machine worktree contained an untracked helper file:
   - `crop_logo.py`
2. The local `.git` directory had been polluted by Windows-generated `desktop.ini` files under:
   - `.git/refs/...`
   - `.git/logs/refs/...`
   - `.git/objects/...`
   - `.git/worktrees/...`

Impact:
1. `git branch -r` emitted broken ref warnings.
2. `git fetch origin --tags` initially failed with:
   - `fatal: bad object refs/desktop.ini`

## Actions Taken

1. Removed all `desktop.ini` artifacts from the local `.git` directory.
2. Re-ran `git fetch origin --tags` successfully.
3. Added root ignore rule for the local-only helper:
   - `/crop_logo.py`

## Result

1. Git remote operations recovered and merge to `master` completed successfully.
2. The local helper `crop_logo.py` no longer appears as a tracked/untracked repo change.
3. Remaining hygiene warning source is addressed for the current delivery machine baseline.

## Remaining Recommendation

To avoid recurrence on Windows delivery machines:
1. Keep `desktop.ini` and `Thumbs.db` ignore rules in the repository.
2. Avoid opening `.git` internals in file explorers that auto-create metadata files.
3. Run a quick hygiene check before every release train:
   - `git status --short`
   - `git branch -r`
   - `git fetch origin --tags`
