param(
    [ValidateSet("P0", "P1", "P2", "P3", "HOTFIX", "SECURITY", "FEATURE", "BREAKING", "CHORE")]
    [string]$Priority = "P2",
    [ValidateSet("patch", "minor", "major")]
    [string]$PartOverride = ""
)

$ErrorActionPreference = "Stop"

$severity = $Priority.Trim().ToLowerInvariant()
$args = @("scripts/version/bump-version.php", "--severity=$severity")

if (-not [string]::IsNullOrWhiteSpace($PartOverride)) {
    $args += "--part=$PartOverride"
}

& php @args
exit $LASTEXITCODE
