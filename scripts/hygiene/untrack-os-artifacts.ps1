param(
    [switch]$WhatIf
)

$ErrorActionPreference = 'Stop'

$patterns = '(^|/)(desktop\.ini|Desktop\.ini|Thumbs\.db|\.DS_Store)$'
$tracked = git ls-files | Where-Object { $_ -match $patterns }

if (-not $tracked -or $tracked.Count -eq 0) {
    Write-Host '[hygiene] No tracked OS artifacts found.'
    exit 0
}

Write-Host "[hygiene] Found $($tracked.Count) tracked OS artifact file(s)."

if ($WhatIf) {
    $tracked | Select-Object -First 200 | ForEach-Object { Write-Host " - $_" }
    if ($tracked.Count -gt 200) {
        Write-Host "[hygiene] ... truncated list, total: $($tracked.Count)"
    }
    exit 0
}

$batchSize = 200
for ($i = 0; $i -lt $tracked.Count; $i += $batchSize) {
    $end = [Math]::Min($i + $batchSize - 1, $tracked.Count - 1)
    $batch = $tracked[$i..$end]
    git rm --cached -- $batch | Out-Null
}

Write-Host "[hygiene] Removed $($tracked.Count) tracked OS artifact file(s) from git index."
Write-Host '[hygiene] Files remain on disk unless manually deleted.'

