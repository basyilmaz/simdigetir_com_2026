param(
    [string]$Path = ".",
    [string]$EnvFile = ".env",
    [switch]$SkipQualityGate,
    [switch]$SkipStrictEnv,
    [switch]$RunApiSmoke,
    [switch]$SendExternalNotifications
)

$ErrorActionPreference = "Stop"
Set-Location $Path

$results = New-Object System.Collections.Generic.List[object]

function Add-Result {
    param(
        [string]$Step,
        [string]$Status,
        [int]$ExitCode,
        [double]$DurationSeconds,
        [string]$Message
    )

    $results.Add([pscustomobject]@{
        step = $Step
        status = $Status
        exit_code = $ExitCode
        duration_seconds = [Math]::Round($DurationSeconds, 2)
        message = $Message
    }) | Out-Null
}

function Invoke-Step {
    param(
        [string]$Name,
        [scriptblock]$Action
    )

    Write-Host ""
    Write-Host "== $Name =="
    $startedAt = Get-Date
    $exitCode = 0

    try {
        & $Action
        if ($null -ne $LASTEXITCODE) {
            $exitCode = [int]$LASTEXITCODE
        }
    } catch {
        $exitCode = if ($null -ne $LASTEXITCODE -and $LASTEXITCODE -ne 0) { [int]$LASTEXITCODE } else { 1 }
        $duration = ((Get-Date) - $startedAt).TotalSeconds
        Add-Result -Step $Name -Status "FAIL" -ExitCode $exitCode -DurationSeconds $duration -Message $_.Exception.Message
        return $false
    }

    $duration = ((Get-Date) - $startedAt).TotalSeconds
    if ($exitCode -eq 0) {
        Add-Result -Step $Name -Status "PASS" -ExitCode 0 -DurationSeconds $duration -Message "ok"
        return $true
    }

    Add-Result -Step $Name -Status "FAIL" -ExitCode $exitCode -DurationSeconds $duration -Message "exit code $exitCode"
    return $false
}

function Add-SkipResult {
    param([string]$Step, [string]$Message)
    Add-Result -Step $Step -Status "SKIP" -ExitCode 0 -DurationSeconds 0 -Message $Message
}

Write-Host "== Hostinger Preflight =="
Write-Host "Path: $((Get-Location).Path)"
Write-Host "EnvFile: $EnvFile"

$backendFilter = "AuthSanctumTest|Sprint3OrderLifecycleApiTest|Sprint3PaymentFlowApiTest|Sprint4CourierDispatchTest|Sprint5FinanceSupportCorporateTest|Sprint6HardeningAnalyticsTest"
$frontendFilter = "LandingDynamicContentTest|LandingStandardPagesDynamicContentTest|SeoTest|SmokeTest|LandingSeederTest|ReleaseP0ReadinessTest"

$go = $true

if ($SkipQualityGate) {
    Add-SkipResult -Step "Quality Gate" -Message "Skipped by flag"
} else {
    if (-not (Invoke-Step -Name "Quality Gate" -Action { & ./scripts/run-quality-gate.ps1 })) {
        $go = $false
    }
}

if (-not (Invoke-Step -Name "Backend Regression" -Action { & php artisan test --filter $backendFilter })) {
    $go = $false
}

if (-not (Invoke-Step -Name "Frontend Regression" -Action { & php artisan test --filter $frontendFilter })) {
    $go = $false
}

if ($SkipStrictEnv) {
    Add-SkipResult -Step "Strict Env Checklist" -Message "Skipped by flag"
} else {
    if ($RunApiSmoke) {
        if (-not (Invoke-Step -Name "Strict Env + API Smoke" -Action {
            & ./scripts/run-phase2-live-smoke.ps1 -EnvFile $EnvFile -StrictEnv -RunApiSmoke -SendExternalNotifications:$SendExternalNotifications
        })) {
            $go = $false
        }
    } else {
        if (-not (Invoke-Step -Name "Strict Env Checklist" -Action {
            & ./scripts/run-phase2-live-smoke.ps1 -EnvFile $EnvFile -StrictEnv
        })) {
            $go = $false
        }
    }
}

$reportRoot = Join-Path "storage/app/qa/hostinger-preflight" (Get-Date -Format "yyyy-MM-dd-HHmmss")
New-Item -Path $reportRoot -ItemType Directory -Force | Out-Null
$reportPath = Join-Path $reportRoot "report.json"
$report = [pscustomobject]@{
    generated_at = (Get-Date).ToString("o")
    env_file = $EnvFile
    run_api_smoke = [bool]$RunApiSmoke
    send_external_notifications = [bool]$SendExternalNotifications
    results = $results
    final_decision = $(if ($go) { "GO" } else { "NOGO" })
}
$report | ConvertTo-Json -Depth 8 | Set-Content -Path $reportPath -Encoding UTF8

Write-Host ""
Write-Host "== Preflight Summary =="
foreach ($item in $results) {
    Write-Host ("[{0}] {1} (exit={2}, {3}s) - {4}" -f $item.status, $item.step, $item.exit_code, $item.duration_seconds, $item.message)
}
Write-Host "Report: $reportPath"
Write-Host "Decision: $($report.final_decision)"

if (-not $go) {
    exit 1
}

exit 0

