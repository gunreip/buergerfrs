<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$pharPath = $projectRoot . '/tools/phpdoc/phpDocumentor.phar';
$configPath = $projectRoot . '/phpdoc.xml.dist';

if (! is_file($pharPath)) {
    fwrite(STDERR, "phpDocumentor PHAR not found at {$pharPath}\n");
    fwrite(STDERR, "Run: composer docs:phpdoc:install\n");
    exit(1);
}

if (! is_file($configPath)) {
    fwrite(STDERR, "Configuration file missing: {$configPath}\n");
    exit(1);
}

$cmd = escapeshellarg(PHP_BINARY)
    . ' ' . escapeshellarg($pharPath)
    . ' --config ' . escapeshellarg($configPath);

passthru($cmd, $exitCode);

exit((int) $exitCode);
