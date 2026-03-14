param(
    [switch]$SkipGitConfig
)

$ErrorActionPreference = 'Stop'

$repoRoot = Resolve-Path (Join-Path $PSScriptRoot '..\..')
Set-Location $repoRoot

Write-Host "[hooks] Repository root: $repoRoot"

git config core.hooksPath .githooks
Write-Host "[hooks] core.hooksPath => .githooks"

if (-not $SkipGitConfig) {
    git config core.autocrlf false
    git config core.eol lf
    Write-Host "[hooks] core.autocrlf => false"
    Write-Host "[hooks] core.eol => lf"
}

if (Get-Command bash -ErrorAction SilentlyContinue) {
    bash -lc 'if command -v git >/dev/null 2>&1; then REPO_ROOT="$(git rev-parse --show-toplevel)"; cd "$REPO_ROOT" || exit 0; if [ -f .githooks/pre-commit ] && [ -f .githooks/pre-push ]; then chmod +x .githooks/pre-commit .githooks/pre-push; fi; fi' | Out-Null
    Write-Host "[hooks] Hook executable check completed."
} else {
    Write-Host "[hooks] bash not found; skip chmod step."
}

Write-Host "[hooks] Done. Next: commit and push as usual."
