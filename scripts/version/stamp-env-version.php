#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Usage:
 *   php scripts/version/stamp-env-version.php --env=.env --channel=live
 *   php scripts/version/stamp-env-version.php --env=.env --channel=live --no-timestamp
 */

$root = dirname(__DIR__, 2);
$versionFile = $root.DIRECTORY_SEPARATOR.'VERSION';
$envPath = '.env';
$channel = 'live';
$useTimestamp = true;

foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--env=')) {
        $envPath = trim(substr($arg, 6));
        continue;
    }

    if (str_starts_with($arg, '--channel=')) {
        $channel = strtolower(trim(substr($arg, 10)));
        continue;
    }

    if ($arg === '--no-timestamp') {
        $useTimestamp = false;
    }
}

$channel = preg_replace('/[^a-z0-9._-]/', '-', $channel) ?? 'live';
$channel = trim($channel, '-');
if ($channel === '') {
    $channel = 'live';
}

if (! is_file($versionFile)) {
    fwrite(STDERR, "VERSION file is missing.\n");
    exit(1);
}

$baseVersion = trim((string) file_get_contents($versionFile));
if (! preg_match('/^\d+\.\d+\.\d+$/', $baseVersion)) {
    fwrite(STDERR, "VERSION must be semantic format X.Y.Z. Current: {$baseVersion}\n");
    exit(1);
}

$absoluteEnvPath = str_starts_with($envPath, DIRECTORY_SEPARATOR)
    ? $envPath
    : $root.DIRECTORY_SEPARATOR.$envPath;

if (! is_file($absoluteEnvPath)) {
    fwrite(STDERR, "Env file not found: {$absoluteEnvPath}\n");
    exit(1);
}

$stamp = $baseVersion.'-'.$channel;
if ($useTimestamp) {
    $stamp .= '.'.date('YmdHis');
}

$contents = (string) file_get_contents($absoluteEnvPath);
if (preg_match('/^APP_VERSION=.*$/m', $contents) === 1) {
    $contents = preg_replace('/^APP_VERSION=.*$/m', "APP_VERSION={$stamp}", $contents);
} else {
    $contents .= (str_ends_with($contents, PHP_EOL) ? '' : PHP_EOL)."APP_VERSION={$stamp}".PHP_EOL;
}

file_put_contents($absoluteEnvPath, $contents);
fwrite(STDOUT, "Stamped {$envPath} with APP_VERSION={$stamp}\n");
