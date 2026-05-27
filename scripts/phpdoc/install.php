<?php

declare(strict_types=1);

$targetDir = __DIR__ . '/../../tools/phpdoc';
$targetFile = $targetDir . '/phpDocumentor.phar';
$downloadUrl = 'https://phpdoc.org/phpDocumentor.phar';

if (! is_dir($targetDir) && ! mkdir($targetDir, 0775, true) && ! is_dir($targetDir)) {
    fwrite(STDERR, "Failed to create directory: {$targetDir}\n");
    exit(1);
}

$context = stream_context_create([
    'http' => [
        'timeout' => 60,
        'follow_location' => 1,
        'user_agent' => 'buergerfrs-phpdoc-installer',
    ],
    'https' => [
        'timeout' => 60,
        'follow_location' => 1,
        'user_agent' => 'buergerfrs-phpdoc-installer',
    ],
]);

$binary = @file_get_contents($downloadUrl, false, $context);

if (! is_string($binary) || $binary === '') {
    fwrite(STDERR, "Failed to download phpDocumentor PHAR from {$downloadUrl}\n");
    exit(1);
}

if (file_put_contents($targetFile, $binary) === false) {
    fwrite(STDERR, "Failed to write PHAR file: {$targetFile}\n");
    exit(1);
}

@chmod($targetFile, 0755);

fwrite(STDOUT, "phpDocumentor installed: {$targetFile}\n");
