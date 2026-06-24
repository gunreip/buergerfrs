<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$errors = [];
$warnings = [];

$requiredFiles = [
    $projectRoot.'/README.md',
    $projectRoot.'/docs/index.md',
    $projectRoot.'/docs/artisan-commands.md',
];

foreach ($requiredFiles as $requiredFile) {
    if (! is_file($requiredFile)) {
        $errors[] = 'Required documentation file is missing: '.substr($requiredFile, strlen($projectRoot) + 1);
    }
}

$markdownFiles = [
    $projectRoot.'/README.md',
    $projectRoot.'/AGENT.md',
];

$docsIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($projectRoot.'/docs', FilesystemIterator::SKIP_DOTS),
);

foreach ($docsIterator as $file) {
    if (! $file->isFile() || strtolower($file->getExtension()) !== 'md') {
        continue;
    }

    if (str_contains($file->getPathname(), DIRECTORY_SEPARATOR.'phpdoc'.DIRECTORY_SEPARATOR)) {
        continue;
    }

    $markdownFiles[] = $file->getPathname();
}

foreach (array_unique($markdownFiles) as $markdownFile) {
    if (! is_file($markdownFile)) {
        continue;
    }

    $contents = (string) file_get_contents($markdownFile);

    preg_match_all('/\[[^\]]+\]\(([^)]+)\)/', $contents, $matches);

    foreach ($matches[1] ?? [] as $rawTarget) {
        $target = trim((string) $rawTarget, " <>\t\n\r\0\x0B");

        if ($target === '' || str_starts_with($target, '#') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $target)) {
            continue;
        }

        $target = rawurldecode(explode('#', $target, 2)[0]);
        $resolvedPath = dirname($markdownFile).DIRECTORY_SEPARATOR.$target;

        if (! file_exists($resolvedPath)) {
            $relativeFile = substr($markdownFile, strlen($projectRoot) + 1);
            $errors[] = "Broken Markdown link in {$relativeFile}: {$rawTarget}";
        }
    }
}

$command = escapeshellarg(PHP_BINARY).' '.escapeshellarg($projectRoot.'/artisan').' list --format=json';
exec($command, $artisanOutput, $artisanExitCode);

if ($artisanExitCode !== 0) {
    $errors[] = 'Unable to read the Artisan command list.';
} else {
    $artisanData = json_decode(implode("\n", $artisanOutput), true);
    $documentedCommands = is_file($projectRoot.'/docs/artisan-commands.md')
        ? (string) file_get_contents($projectRoot.'/docs/artisan-commands.md')
        : '';
    $prefixPattern = '/^(app:|html:|project:|reference:|system:|translations:|views:)/';

    foreach ($artisanData['commands'] ?? [] as $artisanCommand) {
        $name = (string) ($artisanCommand['name'] ?? '');

        if (! preg_match($prefixPattern, $name)) {
            continue;
        }

        if (! str_contains($documentedCommands, "`{$name}`")) {
            $errors[] = "Project Artisan command is undocumented: {$name}";
        }
    }
}

$phpDocIndex = $projectRoot.'/docs/phpdoc/index.html';

if (is_file($phpDocIndex)) {
    $phpDocTimestamp = filemtime($phpDocIndex) ?: 0;
    $latestSourceTimestamp = 0;
    $appIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($projectRoot.'/app', FilesystemIterator::SKIP_DOTS),
    );

    foreach ($appIterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
            $latestSourceTimestamp = max($latestSourceTimestamp, $file->getMTime());
        }
    }

    if ($latestSourceTimestamp > $phpDocTimestamp) {
        $warnings[] = 'Generated PHPDoc is older than the current app source. Run composer docs:phpdoc:public.';
    }
}

foreach ($warnings as $warning) {
    fwrite(STDOUT, "WARNING: {$warning}\n");
}

if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, "ERROR: {$error}\n");
    }

    exit(1);
}

fwrite(STDOUT, 'Documentation checks passed ('.count(array_unique($markdownFiles))." Markdown files checked).\n");
