#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Usage:
 *   php scripts/version/bump-version.php --part=patch
 *   php scripts/version/bump-version.php --part=minor
 *   php scripts/version/bump-version.php --part=major
 */

$root = dirname(__DIR__, 2);
$versionFile = $root.DIRECTORY_SEPARATOR.'VERSION';
$part = 'patch';

foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--part=')) {
        $part = strtolower(trim(substr($arg, 7)));
    }
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

fwrite(STDOUT, "Version bumped: {$currentRaw} -> {$newVersion}\n");
