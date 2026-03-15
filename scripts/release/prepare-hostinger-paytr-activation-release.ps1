param(
    [string]$Path = ".",
    [string]$EnvFile = ".env.hostinger.production",
    [ValidateSet("patch", "minor", "major")]
    [string]$VersionPart = "patch",
    [switch]$SkipVersionBump,
    [switch]$NoTimestamp,
    [switch]$SkipEnvStamp,
    [switch]$RunApiSmoke,
    [switch]$SendExternalNotifications
)

$ErrorActionPreference = "Stop"
Set-Location $Path

function Invoke-CheckedCommand {
    param(
        [string]$Label,
        [scriptblock]$Action
    )

    Write-Host ""
    Write-Host "== $Label =="

    & $Action

    if ($LASTEXITCODE -ne 0) {
        throw "$Label failed with exit code $LASTEXITCODE"
    }
}

Write-Host "== Prepare Hostinger PAYTR Activation Release =="
Write-Host "Path: $((Get-Location).Path)"
Write-Host "EnvFile: $EnvFile"

if (-not $SkipVersionBump) {
    Invoke-CheckedCommand -Label "Version Bump" -Action {
        & php scripts/version/bump-version.php --part=$VersionPart
    }
} else {
    Write-Host ""
    Write-Host "== Version Bump =="
    Write-Host "Skipped by flag."
}

if (-not $SkipEnvStamp) {
    $stampArgs = @(
        "scripts/version/stamp-env-version.php",
        "--env=$EnvFile",
        "--channel=live"
    )

    if ($NoTimestamp) {
        $stampArgs += "--no-timestamp"
    }

    Invoke-CheckedCommand -Label "Stamp Env Version" -Action {
        & php @stampArgs
    }
} else {
    Write-Host ""
    Write-Host "== Stamp Env Version =="
    Write-Host "Skipped by flag."
}

Invoke-CheckedCommand -Label "Config Clear" -Action {
    & php artisan config:clear
}

Invoke-CheckedCommand -Label "PAYTR Activation Gate" -Action {
    & powershell -ExecutionPolicy Bypass -File .\scripts\release\run-hostinger-paytr-activation-gate.ps1 `
        -EnvFile $EnvFile `
        -RunApiSmoke:$RunApiSmoke `
        -SendExternalNotifications:$SendExternalNotifications
}

Write-Host ""
Write-Host "PAYTR activation release preparation completed."
