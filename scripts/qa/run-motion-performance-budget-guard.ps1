param(
    [string]$Path = ".",
    [string]$BaseUrl = "http://127.0.0.1:8000",
    [switch]$Serve,
    [switch]$WriteBaseline
)

$ErrorActionPreference = "Stop"
Set-Location $Path

$serverProcess = $null

try {
    if ($Serve.IsPresent) {
        Write-Host "[motion-budget] Starting local app server on 127.0.0.1:8000..."
        $serverProcess = Start-Process -FilePath "php" -ArgumentList @("artisan", "serve", "--host=127.0.0.1", "--port=8000") -PassThru
        Start-Sleep -Seconds 4
    }

    $env:BASE_URL = $BaseUrl
    $args = @("scripts/qa/motion-performance-budget-guard.mjs")
    if ($WriteBaseline.IsPresent) {
        $args += "--write-baseline"
    }

    Write-Host "[motion-budget] Running guard..."
    node @args
    if ($LASTEXITCODE -ne 0) {
        throw "Motion performance budget guard failed."
    }

    Write-Host "[motion-budget] Guard passed."
}
finally {
    if ($null -ne $serverProcess -and -not $serverProcess.HasExited) {
        Write-Host "[motion-budget] Stopping local app server..."
        Stop-Process -Id $serverProcess.Id -Force
    }
}
