<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$sourceDir = $projectRoot . '/docs/phpdoc';
$targetDir = $projectRoot . '/public/docs/phpdoc';

if (! is_dir($sourceDir)) {
    fwrite(STDERR, "Source docs directory missing: {$sourceDir}\n");
    fwrite(STDERR, "Run: composer docs:phpdoc\n");
    exit(1);
}

$removeTree = static function (string $path) use (&$removeTree): void {
    if (! file_exists($path)) {
        return;
    }

    if (is_file($path) || is_link($path)) {
        @unlink($path);

        return;
    }

    $entries = scandir($path);

    if ($entries === false) {
        return;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $removeTree($path . DIRECTORY_SEPARATOR . $entry);
    }

    @rmdir($path);
};

$copyTree = static function (string $from, string $to) use (&$copyTree): void {
    if (! is_dir($to) && ! mkdir($to, 0775, true) && ! is_dir($to)) {
        throw new RuntimeException("Failed to create directory: {$to}");
    }

    $entries = scandir($from);

    if ($entries === false) {
        throw new RuntimeException("Failed to scan directory: {$from}");
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $source = $from . DIRECTORY_SEPARATOR . $entry;
        $target = $to . DIRECTORY_SEPARATOR . $entry;

        if (is_dir($source)) {
            $copyTree($source, $target);

            continue;
        }

        if (! copy($source, $target)) {
            throw new RuntimeException("Failed to copy file: {$source}");
        }
    }
};

$removeTree($targetDir);

try {
    $copyTree($sourceDir, $targetDir);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}

fwrite(STDOUT, "Published PHPDoc to {$targetDir}\n");
