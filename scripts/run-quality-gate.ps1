param(
    [string]$Path = "."
)

$ErrorActionPreference = "Stop"
Set-Location $Path

Write-Host "[1/3] Detect changed PHP files..."
$changedTracked = git diff --name-only -- '*.php'
$changedStaged = git diff --cached --name-only -- '*.php'
$changedUntracked = git ls-files --others --exclude-standard -- '*.php'

$changed = @($changedTracked + $changedStaged + $changedUntracked) |
    Where-Object { $_ -and ($_ -notlike 'vendor/*') } |
    Sort-Object -Unique

if (-not $changed) {
    Write-Host "No changed PHP files detected by git diff."
} else {
    Write-Host "[2/3] PHP lint changed files..."
    foreach ($file in $changed) {
        if (Test-Path $file) {
            php -l $file | Out-Host
        }
    }
}

Write-Host "[3/3] Run test suite..."
php artisan test
if ($LASTEXITCODE -ne 0) {
    throw "Test suite failed."
}

Write-Host "Quality gate passed."
