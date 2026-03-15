param(
    [string]$Path = ".",
    [string]$EnvFile = ".env.hostinger.production"
)

$ErrorActionPreference = "Stop"
Set-Location $Path

Write-Host "== Hostinger Payments-Off Gate =="
Write-Host "Path: $((Get-Location).Path)"
Write-Host "EnvFile: $EnvFile"

& ./scripts/run-hostinger-preflight.ps1 -EnvFile $EnvFile -ReleaseMode payments_off
exit $LASTEXITCODE
