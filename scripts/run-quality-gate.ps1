param(
    [string]$Path = "."
)

$ErrorActionPreference = "Stop"
Set-Location $Path

Write-Host "[1/4] Detect changed files..."
$changedTracked = git diff --name-only
$changedStaged = git diff --cached --name-only
$changedUntracked = git ls-files --others --exclude-standard

$changed = @($changedTracked + $changedStaged + $changedUntracked) |
    Where-Object { $_ -and ($_ -notlike 'vendor/*') -and ($_ -notlike 'node_modules/*') } |
    Sort-Object -Unique

if (-not $changed -or $changed.Count -eq 0) {
    Write-Host "No changed files detected by git diff."
}
else {
    Write-Host "[2/4] Run hygiene checks on changed files..."
    $changedText = ($changed -join "`n")
    $changedText | php scripts/hygiene/check-file-hygiene.php --files-from-stdin
    if ($LASTEXITCODE -ne 0) {
        throw "Hygiene check failed."
    }
}

$changedPhp = @($changed | Where-Object { $_ -like '*.php' })

if (-not $changedPhp -or $changedPhp.Count -eq 0) {
    Write-Host "[3/4] No changed PHP files detected by git diff."
}
else {
    Write-Host "[3/4] PHP lint changed files..."
    foreach ($file in $changedPhp) {
        if (Test-Path $file) {
            php -l $file | Out-Host
        }
    }
}

Write-Host "[4/4] Run test suite..."
php artisan test
if ($LASTEXITCODE -ne 0) {
    throw "Test suite failed."
}

Write-Host "Quality gate passed."