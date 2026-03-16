param(
    [switch]$WhatIf
)

$ErrorActionPreference = "Stop"

$gitDir = Join-Path (Get-Location) ".git"
if (-not (Test-Path $gitDir)) {
    throw "No .git directory found in current path."
}

$patterns = @("desktop.ini", "Desktop.ini", "Thumbs.db", ".DS_Store")
$removed = @()

foreach ($pattern in $patterns) {
    $matches = Get-ChildItem -Path (Join-Path $gitDir "refs") -Recurse -Force -ErrorAction SilentlyContinue |
        Where-Object { $_.PSIsContainer -eq $false -and $_.Name -ieq $pattern }

    foreach ($match in $matches) {
        if ($WhatIf) {
            Write-Host "[whatif] $($match.FullName)"
            continue
        }

        try {
            attrib -s -h $match.FullName 2>$null | Out-Null
        } catch {
            # ignore attribute failures, continue to delete attempt
        }

        Remove-Item -Path $match.FullName -Force -ErrorAction SilentlyContinue
        if (-not (Test-Path $match.FullName)) {
            $removed += $match.FullName
        }
    }
}

if ($WhatIf) {
    Write-Host "[hygiene] WhatIf mode completed."
    exit 0
}

if ($removed.Count -eq 0) {
    Write-Host "[hygiene] No local git ref artifacts found."
    exit 0
}

Write-Host "[hygiene] Removed $($removed.Count) local git ref artifact file(s):"
$removed | ForEach-Object { Write-Host " - $_" }
