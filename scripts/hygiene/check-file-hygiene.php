<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
chdir($root);

$args = $argv;
array_shift($args);

if ($args === []) {
    fwrite(STDERR, usage());
    exit(2);
}

$mode = $args[0];

try {
    $files = match ($mode) {
        '--staged' => gitFileList(['diff', '--cached', '--name-only', '--diff-filter=ACMR']),
        '--range' => getFilesFromRange($args),
        '--files-from-stdin' => getFilesFromStdin(),
        default => throw new InvalidArgumentException('Unknown option: '.$mode),
    };
} catch (Throwable $e) {
    fwrite(STDERR, "[hygiene] ".$e->getMessage().PHP_EOL);
    fwrite(STDERR, usage());
    exit(2);
}

$files = normalizeFileList($files);

if ($files === []) {
    fwrite(STDOUT, "[hygiene] No files to validate.".PHP_EOL);
    exit(0);
}

$forbiddenBasenames = [
    'desktop.ini',
    'thumbs.db',
    '.ds_store',
];

$forbiddenPathPrefixes = [
    'vendor/',
    'node_modules/',
];

$textRegex = '/(\.blade\.php|\.php|\.phtml|\.js|\.mjs|\.cjs|\.ts|\.tsx|\.jsx|\.css|\.scss|\.sass|\.less|\.html|\.md|\.txt|\.xml|\.json|\.yml|\.yaml|\.sql|\.sh|\.ps1|\.ini|\.csv|\.env(\..+)?|\.vue)$/i';
$explicitTextFiles = [
    '.editorconfig',
    '.gitattributes',
    '.gitignore',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'phpunit.xml',
    'vite.config.js',
    'tailwind.config.js',
    'postcss.config.js',
    'VERSION',
];

// Common mojibake markers seen when UTF-8 content is decoded with legacy encodings.
$suspiciousMojibakeMarkers = [
    "\u{00C3}", // broken UTF-8 lead sequence
    "\u{00C4}", // broken UTF-8 lead sequence
    "\u{00C5}", // broken UTF-8 lead sequence
    "\u{00E2}\u{20AC}", // broken punctuation sequence
    "\u{011F}\u{0178}", // broken emoji-like sequence
];

$violations = [];

foreach ($files as $file) {
    $normalized = str_replace('\\', '/', $file);
    $basename = strtolower(basename($normalized));

    if (in_array($basename, $forbiddenBasenames, true)) {
        $violations[] = "Forbidden OS artifact staged: {$normalized}";
        continue;
    }

    foreach ($forbiddenPathPrefixes as $prefix) {
        if (str_starts_with(strtolower($normalized), $prefix)) {
            $violations[] = "Forbidden path staged: {$normalized}";
            continue 2;
        }
    }

    if (!is_file($file)) {
        continue;
    }

    $lower = strtolower($normalized);
    $isText = in_array($lower, $explicitTextFiles, true) || preg_match($textRegex, $lower) === 1;

    if (!$isText) {
        continue;
    }

    $fh = fopen($file, 'rb');
    if ($fh === false) {
        $violations[] = "Unable to read file: {$normalized}";
        continue;
    }

    $prefix = fread($fh, 3);
    fclose($fh);

    if ($prefix === "\xEF\xBB\xBF") {
        $violations[] = "UTF-8 BOM detected (must be BOM-less): {$normalized}";
    }

    $contents = file_get_contents($file);
    if ($contents === false) {
        $violations[] = "Unable to read file for line-ending check: {$normalized}";
        continue;
    }

    if (str_contains($contents, "\r\n")) {
        $violations[] = "CRLF detected (must be LF): {$normalized}";
    }

    foreach ($suspiciousMojibakeMarkers as $marker) {
        if (str_contains($contents, $marker)) {
            $escapedMarker = addcslashes($marker, "\0..\37\177..\377");
            $violations[] = "Suspicious mojibake marker '{$escapedMarker}' detected: {$normalized}";
            break;
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, "[hygiene] Violations found:".PHP_EOL);
    foreach ($violations as $violation) {
        fwrite(STDERR, " - {$violation}".PHP_EOL);
    }
    fwrite(STDERR, PHP_EOL."[hygiene] Fix suggestions:".PHP_EOL);
    fwrite(STDERR, " - Remove forbidden artifacts (desktop.ini, Thumbs.db, .DS_Store).".PHP_EOL);
    fwrite(STDERR, " - Save files as UTF-8 without BOM.".PHP_EOL);
    fwrite(STDERR, " - Convert line endings to LF.".PHP_EOL);
    fwrite(STDERR, " - Fix mojibake/corrupted characters before commit.".PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "[hygiene] Passed for ".count($files)." file(s).".PHP_EOL);
exit(0);

function getFilesFromRange(array $args): array
{
    if (count($args) !== 3) {
        throw new InvalidArgumentException('Usage for --range: --range <from> <to>');
    }

    return gitFileList(['diff', '--name-only', '--diff-filter=ACMR', $args[1], $args[2]]);
}

function getFilesFromStdin(): array
{
    $stdin = stream_get_contents(STDIN);
    if ($stdin === false || trim($stdin) === '') {
        return [];
    }

    $lines = preg_split('/\r\n|\r|\n/', trim($stdin)) ?: [];
    return array_values(array_filter($lines, static fn ($line): bool => trim($line) !== ''));
}

function gitFileList(array $command): array
{
    $cmd = 'git '.implode(' ', array_map('escapeshellarg', $command)).' 2>&1';
    $output = [];
    $code = 0;
    exec($cmd, $output, $code);

    if ($code !== 0) {
        throw new RuntimeException("Git command failed: {$cmd}".PHP_EOL.implode(PHP_EOL, $output));
    }

    return array_values(array_filter($output, static fn ($line): bool => trim($line) !== ''));
}

function normalizeFileList(array $files): array
{
    $normalized = [];
    foreach ($files as $file) {
        $trimmed = trim((string) $file);
        if ($trimmed === '') {
            continue;
        }
        $normalized[] = $trimmed;
    }

    return array_values(array_unique($normalized));
}

function usage(): string
{
    return <<<TXT
Usage:
  php scripts/hygiene/check-file-hygiene.php --staged
  php scripts/hygiene/check-file-hygiene.php --range <from> <to>
  git diff --name-only <range> | php scripts/hygiene/check-file-hygiene.php --files-from-stdin

TXT;
}
