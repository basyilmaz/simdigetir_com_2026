#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Usage:
 *   php scripts/version/bump-version.php --part=patch
 *   php scripts/version/bump-version.php --part=minor
 *   php scripts/version/bump-version.php --part=major
 *   php scripts/version/bump-version.php --severity=p1
 *   php scripts/version/bump-version.php --severity=breaking
 */

$root = dirname(__DIR__, 2);
$versionFile = $root.DIRECTORY_SEPARATOR.'VERSION';
$part = null;
$partExplicitlyProvided = false;
$severity = null;
$severityMap = [
    'p0' => 'patch',
    'p1' => 'minor',
    'p2' => 'patch',
    'p3' => 'patch',
    'hotfix' => 'patch',
    'security' => 'patch',
    'feature' => 'minor',
    'breaking' => 'major',
    'chore' => 'patch',
];

foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--part=')) {
        $part = strtolower(trim(substr($arg, 7)));
        $partExplicitlyProvided = true;
        continue;
    }

    if (str_starts_with($arg, '--severity=')) {
        $severity = strtolower(trim(substr($arg, 11)));
    }
}

if ($severity !== null && ! array_key_exists($severity, $severityMap)) {
    fwrite(STDERR, "Invalid --severity value. Use: ".implode(' | ', array_keys($severityMap))."\n");
    exit(1);
}

if ($part === null) {
    $part = $severity !== null ? $severityMap[$severity] : 'patch';
}

if (! in_array($part, ['patch', 'minor', 'major'], true)) {
    fwrite(STDERR, "Invalid --part value. Use: patch | minor | major\n");
    exit(1);
}

if (! is_file($versionFile)) {
    file_put_contents($versionFile, "1.0.0\n");
}

$currentRaw = trim((string) file_get_contents($versionFile));
if (! preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $currentRaw, $matches)) {
    fwrite(STDERR, "VERSION must be semantic format X.Y.Z. Current: {$currentRaw}\n");
    exit(1);
}

$major = (int) $matches[1];
$minor = (int) $matches[2];
$patch = (int) $matches[3];

switch ($part) {
    case 'major':
        $major++;
        $minor = 0;
        $patch = 0;
        break;
    case 'minor':
        $minor++;
        $patch = 0;
        break;
    default:
        $patch++;
        break;
}

$newVersion = "{$major}.{$minor}.{$patch}";
file_put_contents($versionFile, $newVersion.PHP_EOL);

$envFiles = [
    $root.DIRECTORY_SEPARATOR.'.env.example',
    $root.DIRECTORY_SEPARATOR.'.env.hostinger.production.example',
];

foreach ($envFiles as $envFile) {
    if (! is_file($envFile)) {
        continue;
    }

    $contents = (string) file_get_contents($envFile);
    if (preg_match('/^APP_VERSION=.*$/m', $contents) === 1) {
        $contents = preg_replace('/^APP_VERSION=.*$/m', "APP_VERSION={$newVersion}", $contents);
    } else {
        $contents .= (str_ends_with($contents, PHP_EOL) ? '' : PHP_EOL)."APP_VERSION={$newVersion}".PHP_EOL;
    }

    file_put_contents($envFile, $contents);
}

$metadata = [];
if ($severity !== null) {
    $metadata[] = "severity={$severity}";
}
if ($partExplicitlyProvided) {
    $metadata[] = "part_override={$part}";
}
$metadataSuffix = $metadata === [] ? '' : ' ('.implode(', ', $metadata).')';

fwrite(STDOUT, "Version bumped: {$currentRaw} -> {$newVersion}{$metadataSuffix}\n");
