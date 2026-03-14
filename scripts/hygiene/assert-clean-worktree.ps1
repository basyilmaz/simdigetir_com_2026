param(
    [switch]$IgnoreUntracked
)

$ErrorActionPreference = 'Stop'

$args = @('status', '--porcelain')
if ($IgnoreUntracked) {
    $args += '--untracked-files=no'
}

$status = & git @args

if ($LASTEXITCODE -ne 0) {
    Write-Error 'Git status check failed.'
    exit 2
}

if ($status) {
    Write-Host '[worktree] Repository is not clean. Resolve before starting a new task.'
    $status | Select-Object -First 100 | ForEach-Object { Write-Host "  $_" }
    exit 1
}

Write-Host '[worktree] Clean worktree confirmed.'

