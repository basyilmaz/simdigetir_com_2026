param(
    [string]$Path = ".",
    [string]$EnvFile = ".env.hostinger.production",
    [switch]$RunApiSmoke,
    [switch]$SendExternalNotifications
)

$ErrorActionPreference = "Stop"
Set-Location $Path

Write-Host "== Hostinger PAYTR Activation Gate =="
Write-Host "Path: $((Get-Location).Path)"
Write-Host "EnvFile: $EnvFile"

& ./scripts/run-hostinger-preflight.ps1 `
    -EnvFile $EnvFile `
    -ReleaseMode payments_on_paytr `
    -RunApiSmoke:$RunApiSmoke `
    -SendExternalNotifications:$SendExternalNotifications

exit $LASTEXITCODE
