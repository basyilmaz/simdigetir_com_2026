param(
    [string]$Path = ".",
    [string]$EnvFile = ".env",
    [switch]$RunApiSmoke,
    [switch]$SendExternalNotifications,
    [switch]$StrictEnv,
    [ValidateSet("auto", "payments_off", "payments_on_paytr")]
    [string]$ReleaseMode = "auto"
)

$ErrorActionPreference = "Stop"
Set-Location $Path
$validationErrors = New-Object System.Collections.Generic.List[string]
$validationWarnings = New-Object System.Collections.Generic.List[string]

function Read-EnvFile {
    param([string]$FilePath)

    if (-not (Test-Path $FilePath)) {
        throw "Env file not found: $FilePath"
    }

    $map = @{}
    foreach ($line in Get-Content $FilePath) {
        $trimmed = $line.Trim()
        if ($trimmed -eq "" -or $trimmed.StartsWith("#")) {
            continue
        }

        $parts = $trimmed -split "=", 2
        if ($parts.Count -ne 2) {
            continue
        }

        $key = $parts[0].Trim()
        $value = $parts[1].Trim().Trim("'").Trim('"')
        $map[$key] = $value
    }

    return $map
}

function Get-ConfigValue {
    param(
        [hashtable]$EnvMap,
        [string]$Name
    )

    if ($EnvMap.ContainsKey($Name)) {
        return [string]$EnvMap[$Name]
    }

    $processValue = [Environment]::GetEnvironmentVariable($Name, "Process")
    if ($processValue) {
        return [string]$processValue
    }

    return ""
}

function Test-IsPlaceholder {
    param([string]$Value)

    if ($null -eq $Value) { return $true }

    $normalized = $Value.Trim().ToLowerInvariant()
    if ($normalized -eq "") { return $true }

    $known = @(
        "null", "none", "changeme", "change_me", "replace_me", "placeholder",
        "your_api_key", "your_secret", "your_password", "xxx", "***"
    )
    if ($known -contains $normalized) { return $true }
    if ($normalized.Contains("placeholder")) { return $true }
    if ($normalized.Contains("replace_with")) { return $true }
    if ($normalized.Contains("replace-with")) { return $true }
    if ($normalized.Contains("example")) { return $true }
    if ($normalized.Contains("change")) { return $true }

    return $false
}

function Convert-ToBoolean {
    param(
        [string]$Value,
        [bool]$Default = $false
    )

    if ([string]::IsNullOrWhiteSpace($Value)) {
        return $Default
    }

    switch ($Value.Trim().ToLowerInvariant()) {
        "1" { return $true }
        "true" { return $true }
        "yes" { return $true }
        "on" { return $true }
        "0" { return $false }
        "false" { return $false }
        "no" { return $false }
        "off" { return $false }
        default { return $Default }
    }
}

function Assert-EnvValue {
    param(
        [hashtable]$EnvMap,
        [string]$Name,
        [switch]$AllowEmpty,
        [switch]$WarnOnly
    )

    $value = Get-ConfigValue -EnvMap $EnvMap -Name $Name
    if ($AllowEmpty) {
        Write-Host "[ok] $Name = (optional)"
        return $value
    }

    if (Test-IsPlaceholder -Value $value) {
        if ($WarnOnly) {
            Add-ValidationWarning -Message "[env] $Name is missing or placeholder."
            Write-Host "[~] $Name"
            return ""
        }

        $validationErrors.Add("[env] $Name is missing or placeholder.") | Out-Null
        Write-Host "[x] $Name"
        return ""
    }

    Write-Host "[ok] $Name"
    return $value
}

function Add-ValidationWarning {
    param([string]$Message)
    $validationWarnings.Add($Message) | Out-Null
    Write-Warning $Message
}

function Get-PaymentProviderLabel {
    param([string]$Provider)

    switch ($Provider.Trim().ToLowerInvariant()) {
        "paytr" { return "PAYTR" }
        "iyzico" { return "Iyzico" }
        "mockpay" { return "MockPay" }
        "mock" { return "MockPay" }
        "none" { return "No Payment Provider" }
        default { return $Provider }
    }
}

function Assert-PaymentProviderConfig {
    param(
        [hashtable]$EnvMap,
        [string]$Provider,
        [bool]$StrictValidation
    )

    $normalizedProvider = $Provider.Trim().ToLowerInvariant()
    switch ($normalizedProvider) {
        "paytr" {
            $script:paymentCallbackSecret = Assert-EnvValue -EnvMap $EnvMap -Name "PAYTR_CALLBACK_SECRET" -WarnOnly:(!$StrictValidation)
            $script:paymentSandboxFlag = Get-ConfigValue -EnvMap $EnvMap -Name "PAYTR_SANDBOX"
            $null = Assert-EnvValue -EnvMap $EnvMap -Name "PAYTR_MERCHANT_ID" -WarnOnly:(!$StrictValidation)
            $null = Assert-EnvValue -EnvMap $EnvMap -Name "PAYTR_MERCHANT_KEY" -WarnOnly:(!$StrictValidation)
            $null = Assert-EnvValue -EnvMap $EnvMap -Name "PAYTR_MERCHANT_SALT" -WarnOnly:(!$StrictValidation)
            if ($script:paymentSandboxFlag.ToLowerInvariant() -eq "true") {
                Add-ValidationWarning -Message "PAYTR_SANDBOX=true. Live smoke icin false olmasi onerilir."
            }
        }
        "iyzico" {
            $script:paymentCallbackSecret = Assert-EnvValue -EnvMap $EnvMap -Name "IYZICO_CALLBACK_SECRET" -WarnOnly:(!$StrictValidation)
            $script:paymentSandboxFlag = Get-ConfigValue -EnvMap $EnvMap -Name "IYZICO_SANDBOX"
            $null = Assert-EnvValue -EnvMap $EnvMap -Name "IYZICO_API_KEY" -WarnOnly:(!$StrictValidation)
            $null = Assert-EnvValue -EnvMap $EnvMap -Name "IYZICO_SECRET_KEY" -WarnOnly:(!$StrictValidation)
            if ($script:paymentSandboxFlag.ToLowerInvariant() -eq "true") {
                Add-ValidationWarning -Message "IYZICO_SANDBOX=true. Live smoke icin false olmasi onerilir."
            }
        }
        default {
            if ($StrictValidation) {
                $validationErrors.Add("[env] Unsupported PAYMENT_DEFAULT_PROVIDER in strict mode: $Provider") | Out-Null
            } else {
                Add-ValidationWarning -Message "PAYMENT_DEFAULT_PROVIDER=$Provider. Provider-specific payment checks skipped."
            }
        }
    }
}

function Finalize-ValidationStep {
    if ($validationWarnings.Count -gt 0) {
        Write-Host ""
        Write-Host "Warnings:"
        foreach ($w in $validationWarnings) {
            Write-Host " - $w"
        }
    }

    if ($validationErrors.Count -gt 0) {
        Write-Host ""
        Write-Host "Errors:"
        foreach ($e in $validationErrors) {
            Write-Host " - $e"
        }
        throw "Env validation failed. Fix listed keys and rerun."
    }
}

function Invoke-JsonRequest {
    param(
        [ValidateSet("GET", "POST")]
        [string]$Method,
        [string]$Url,
        [hashtable]$Body = @{},
        [hashtable]$Headers = @{}
    )

    $jsonBody = $null
    if ($Method -eq "POST") {
        $jsonBody = ($Body | ConvertTo-Json -Depth 20)
    }

    return Invoke-RestMethod -Method $Method -Uri $Url -Headers $Headers -Body $jsonBody -ContentType "application/json"
}

Write-Host "== Phase 2 Live Smoke =="
$envMap = Read-EnvFile -FilePath $EnvFile
$strictValidation = $StrictEnv -or $RunApiSmoke -or $SendExternalNotifications

Write-Host "[1/3] .env checklist validation"
Write-Host " - strict mode: $strictValidation"
Write-Host " - release mode: $ReleaseMode"
$paymentDefaultProvider = Assert-EnvValue -EnvMap $envMap -Name "PAYMENT_DEFAULT_PROVIDER" -WarnOnly:(!$strictValidation)
$smsDefaultProvider = Assert-EnvValue -EnvMap $envMap -Name "SMS_DEFAULT_PROVIDER" -WarnOnly:(!$strictValidation)
$mailMailer = Assert-EnvValue -EnvMap $envMap -Name "MAIL_MAILER"
$paymentsRequired = Convert-ToBoolean -Value (Get-ConfigValue -EnvMap $envMap -Name "PAYMENT_REQUIRED") -Default $true
Write-Host " - payments required: $paymentsRequired"

$paymentProvider = $paymentDefaultProvider.Trim().ToLowerInvariant()
$smsProvider = $smsDefaultProvider.Trim().ToLowerInvariant()
$mailer = $mailMailer.Trim().ToLowerInvariant()
$paymentProviderLabel = Get-PaymentProviderLabel -Provider $paymentProvider
$releaseModeNormalized = $ReleaseMode.Trim().ToLowerInvariant()

$paymentCallbackSecret = ""
$paymentSandboxFlag = ""

if ($releaseModeNormalized -eq "payments_off") {
    if ($paymentsRequired) {
        $validationErrors.Add("[env] ReleaseMode=payments_off requires PAYMENT_REQUIRED=false.") | Out-Null
    }

    if ($paymentProvider -ne "mockpay" -and $paymentProvider -ne "mock" -and $paymentProvider -ne "none") {
        $validationErrors.Add("[env] ReleaseMode=payments_off requires PAYMENT_DEFAULT_PROVIDER=mockpay.") | Out-Null
    }
}

if ($releaseModeNormalized -eq "payments_on_paytr") {
    if (-not $paymentsRequired) {
        $validationErrors.Add("[env] ReleaseMode=payments_on_paytr requires PAYMENT_REQUIRED=true.") | Out-Null
    }

    if ($paymentProvider -ne "paytr") {
        $validationErrors.Add("[env] ReleaseMode=payments_on_paytr requires PAYMENT_DEFAULT_PROVIDER=paytr.") | Out-Null
    }
}

if (-not $paymentsRequired) {
    if ($paymentProvider -eq "mockpay" -or $paymentProvider -eq "mock" -or $paymentProvider -eq "none") {
        Add-ValidationWarning -Message "PAYMENT_REQUIRED=false. Card provider kontrolleri atlandi."
    } elseif ($strictValidation) {
        $validationErrors.Add("[env] PAYMENT_DEFAULT_PROVIDER should be 'mockpay' when PAYMENT_REQUIRED=false.") | Out-Null
    } else {
        Add-ValidationWarning -Message "PAYMENT_REQUIRED=false ancak PAYMENT_DEFAULT_PROVIDER=$paymentProvider."
    }
} else {
    Assert-PaymentProviderConfig -EnvMap $envMap -Provider $paymentProvider -StrictValidation:$strictValidation
}

$netgsmUser = ""
$netgsmPass = ""
$netgsmHeader = ""
$netgsmSandbox = Get-ConfigValue -EnvMap $envMap -Name "NETGSM_SANDBOX"

if ($smsProvider -eq "netgsm") {
    $netgsmUser = Assert-EnvValue -EnvMap $envMap -Name "NETGSM_USERNAME" -WarnOnly:(!$strictValidation)
    $netgsmPass = Assert-EnvValue -EnvMap $envMap -Name "NETGSM_PASSWORD" -WarnOnly:(!$strictValidation)
    $netgsmHeader = Assert-EnvValue -EnvMap $envMap -Name "NETGSM_HEADER" -WarnOnly:(!$strictValidation)
    if ($netgsmSandbox.ToLowerInvariant() -eq "true") {
        Add-ValidationWarning -Message "NETGSM_SANDBOX=true. Live smoke icin false olmasi onerilir."
    }
} elseif ($strictValidation) {
    $validationErrors.Add("[env] SMS_DEFAULT_PROVIDER must be 'netgsm' in strict mode.") | Out-Null
} else {
    Add-ValidationWarning -Message "SMS_DEFAULT_PROVIDER=$smsProvider. API smoke strict modda netgsm beklenir."
}

$mailHost = Assert-EnvValue -EnvMap $envMap -Name "MAIL_HOST" -WarnOnly:(!$strictValidation)
$mailPort = Assert-EnvValue -EnvMap $envMap -Name "MAIL_PORT" -WarnOnly:(!$strictValidation)
$mailUser = Assert-EnvValue -EnvMap $envMap -Name "MAIL_USERNAME" -WarnOnly:(!$strictValidation)
$mailPass = Assert-EnvValue -EnvMap $envMap -Name "MAIL_PASSWORD" -WarnOnly:(!$strictValidation)
$mailFrom = Assert-EnvValue -EnvMap $envMap -Name "MAIL_FROM_ADDRESS"

if ($strictValidation -and ($mailer -eq "log" -or $mailer -eq "array")) {
    $validationErrors.Add("[env] MAIL_MAILER must be a real provider in strict mode.") | Out-Null
}

$mapsKey = Get-ConfigValue -EnvMap $envMap -Name "GOOGLE_MAPS_API_KEY"
if (Test-IsPlaceholder -Value $mapsKey) {
    Add-ValidationWarning -Message "GOOGLE_MAPS_API_KEY missing/placeholder. Distance fallback aktif olur."
} else {
    Write-Host "[ok] GOOGLE_MAPS_API_KEY"
}

Finalize-ValidationStep

Write-Host "[2/3] Checklist tamamlandi."

if (-not $RunApiSmoke) {
    Write-Host "[3/3] API smoke atlandi. Calistirmak icin: -RunApiSmoke"
    exit 0
}

Write-Host "[3/3] API smoke akisi basliyor..."

$baseUrl = Assert-EnvValue -EnvMap $envMap -Name "LIVE_SMOKE_BASE_URL"
$smokeEmail = Assert-EnvValue -EnvMap $envMap -Name "LIVE_SMOKE_ADMIN_EMAIL"
$smokePassword = Assert-EnvValue -EnvMap $envMap -Name "LIVE_SMOKE_ADMIN_PASSWORD"
$smsTarget = Get-ConfigValue -EnvMap $envMap -Name "LIVE_SMOKE_SMS_TARGET"
$emailTarget = Get-ConfigValue -EnvMap $envMap -Name "LIVE_SMOKE_EMAIL_TARGET"
Finalize-ValidationStep

$loginResponse = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/auth/login" -Body @{
    email = $smokeEmail
    password = $smokePassword
    device_name = "phase2-live-smoke"
}

if (-not $loginResponse.success -or -not $loginResponse.data.token) {
    throw "Auth login failed in smoke flow."
}

$token = [string]$loginResponse.data.token
$headers = @{
    Authorization = "Bearer $token"
    Accept = "application/json"
}

$quote = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/quotes" -Headers $headers -Body @{
    base_amount = 15000
    zone = "istanbul-central"
    hour = 14
    currency = "TRY"
    pickup = @{ lat = 41.0082; lng = 28.9784 }
    dropoff = @{ lat = 41.015137; lng = 28.97953 }
}
if (-not $quote.success -or -not $quote.data.id) {
    throw "Quote creation failed."
}

$order = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/orders" -Headers $headers -Body @{
    pricing_quote_id = $quote.data.id
    pickup = @{
        name = "Smoke Pickup"
        phone = "05320000000"
        address = "Istanbul Pickup Address"
        lat = 41.0082
        lng = 28.9784
    }
    dropoff = @{
        name = "Smoke Dropoff"
        phone = "05320000001"
        address = "Istanbul Dropoff Address"
        lat = 41.015137
        lng = 28.97953
    }
    packages = @(
        @{
            package_type = "document"
            quantity = 1
            declared_value_amount = 1000
        }
    )
}
if (-not $order.success -or -not $order.data.id) {
    throw "Order creation failed."
}

$provider = $paymentDefaultProvider
$initPayment = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/payments/initiate" -Headers $headers -Body @{
    provider = $provider
    order_id = $order.data.id
}
if (-not $initPayment.success -or -not $initPayment.data.provider_reference) {
    throw "Payment initiation failed."
}

if ($provider -eq "iyzico") {
    $callbackPayload = @{
        provider_reference = [string]$initPayment.data.provider_reference
        status = "succeeded"
        amount = [int]$initPayment.data.amount
        payload = @{
            source = "phase2-live-smoke"
        }
    }
    $raw = "$($callbackPayload.provider_reference)|$($callbackPayload.status)|$($callbackPayload.amount)"
    $hmac = New-Object System.Security.Cryptography.HMACSHA256
    $hmac.Key = [Text.Encoding]::UTF8.GetBytes($paymentCallbackSecret)
    $sigBytes = $hmac.ComputeHash([Text.Encoding]::UTF8.GetBytes($raw))
    $signature = ($sigBytes | ForEach-Object { $_.ToString("x2") }) -join ""

    $callbackHeaders = @{
        "X-Payment-Signature" = $signature
        Accept = "application/json"
    }

    $callback = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/payments/callback/$provider" -Headers $callbackHeaders -Body $callbackPayload
    if (-not $callback.success) {
        throw "Payment callback failed."
    }
} elseif ($provider -eq "paytr") {
    Add-ValidationWarning -Message "PAYTR callback smoke henuz otomatik degil. Payment initiation dogrulandi, callback simulasyonu atlandi."
}

if ($SendExternalNotifications) {
    if ((Test-IsPlaceholder -Value $smsTarget) -or (Test-IsPlaceholder -Value $emailTarget)) {
        throw "LIVE_SMOKE_SMS_TARGET and LIVE_SMOKE_EMAIL_TARGET are required when -SendExternalNotifications is used."
    }

    $eventKey = "phase2_live_smoke_" + (Get-Date -Format "yyyyMMddHHmmss")

    $null = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/notifications/templates/upsert" -Headers $headers -Body @{
        event_key = $eventKey
        channel = "sms"
        body = "Smoke SMS: order {order_no}"
        is_active = $true
        variables = @("order_no")
    }
    $null = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/notifications/templates/upsert" -Headers $headers -Body @{
        event_key = $eventKey
        channel = "email"
        subject = "Smoke Email"
        body = "Smoke email: order {order_no}"
        is_active = $true
        variables = @("order_no")
    }

    $dispatch = Invoke-JsonRequest -Method POST -Url "$baseUrl/api/v1/notifications/dispatch" -Headers $headers -Body @{
        event_key = $eventKey
        targets = @(
            @{ channel = "sms"; target = $smsTarget },
            @{ channel = "email"; target = $emailTarget }
        )
        context = @{
            order_no = [string]$order.data.order_no
        }
    }

    if (-not $dispatch.success -or [int]$dispatch.data.count -lt 2) {
        throw "Notification dispatch failed."
    }
}

Write-Host "Live smoke completed."
Write-Host "Order: $($order.data.order_no)"
Write-Host "Payment Provider: $paymentProviderLabel"
Write-Host "Payment URL: $($initPayment.data.payment_url)"
Write-Host "Notifications: " + ($(if ($SendExternalNotifications) { "sent" } else { "skipped" }))
