param(
    [string]$Path = ".",
    [string]$EnvFile = ".env.hostinger.production",
    [ValidateSet("patch", "minor", "major")]
    [string]$VersionPart,
    [ValidateSet("p0", "p1", "p2", "p3", "hotfix", "security", "feature", "breaking", "chore")]
    [string]$VersionSeverity = "p2",
    [switch]$SkipVersionBump,
    [switch]$NoTimestamp,
    [switch]$SkipEnvStamp
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

Write-Host "== Prepare Hostinger Payments-Off Release =="
Write-Host "Path: $((Get-Location).Path)"
Write-Host "EnvFile: $EnvFile"

if (-not $SkipVersionBump) {
    $versionArgs = @("scripts/version/bump-version.php", "--severity=$VersionSeverity")
    if ($PSBoundParameters.ContainsKey("VersionPart") -and -not [string]::IsNullOrWhiteSpace($VersionPart)) {
        $versionArgs += "--part=$VersionPart"
    }

    Invoke-CheckedCommand -Label "Version Bump" -Action {
        & php @versionArgs
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

Invoke-CheckedCommand -Label "Payments-Off Gate" -Action {
    & powershell -ExecutionPolicy Bypass -File .\scripts\release\run-hostinger-payments-off-gate.ps1 -EnvFile $EnvFile
}

Write-Host ""
Write-Host "Payments-off release preparation completed."
