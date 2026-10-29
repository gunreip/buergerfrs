<?php

use App\Console\Commands\SyncDynamicTranslationKeys;
use App\Console\Commands\TranslationsAuditCode;
use App\Console\Commands\TranslationsGenerateLiteralDiffs;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\ArrayInput;
use Tests\TestCase;

uses(TestCase::class);

it('includes package views and PHP sources in the default scan', function () {
    $paths = (array) config('translation-workbench.paths');
    expect($paths)->toContain('packages/gunreip/laravel-translation-workbench/resources/views', 'packages/gunreip/laravel-translation-workbench/src');
    $files = collect(app(TranslationWorkbenchScanner::class)->scannableFiles())->map(fn ($file) => $file->getPathname());
    expect($files)->toContain(base_path('packages/gunreip/laravel-translation-workbench/src/Support/TwGraph/TestFailureDetails.php'))
        ->toContain(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/diagnostics.blade.php'));
});

it('shares exclusions patterns and deduplication across all translation scan consumers', function () {
    $relative = 'storage/framework/testing/scan-paths-'.uniqid();
    $root = base_path($relative);
    File::ensureDirectoryExists($root.'/excluded');
    try {
        foreach (['source.php', 'view.blade.php', 'excluded/hidden.php', 'xxx-parked.php', 'notes.txt'] as $file) {
            File::put($root.'/'.$file, "<?php __('Scan fixture');");
        }
        config(['translation-workbench.paths' => [$relative, $relative.'/source.php'],
            'translation-workbench.exclude_paths' => [$relative.'/excluded'],
            'translation-workbench.ignored_filename_contains' => ['xxx'],
            'translation-workbench.file_patterns' => ['*.php']]);
        $scanner = app(TranslationWorkbenchScanner::class);
        $normalize = fn ($files) => collect($files)->map(fn ($file) => is_string($file) ? $file : $file->getRealPath())->sort()->values()->all();
        $expected = [$root.'/source.php', $root.'/view.blade.php'];
        expect($normalize($scanner->scannableFiles()))->toBe($expected);
        expect($scanner->scanFile($root.'/excluded/hidden.php'))->toBeEmpty();
        expect($scanner->scanFile($root.'/source.php'))->toHaveCount(1);

        foreach ([TranslationsAuditCode::class, TranslationsGenerateLiteralDiffs::class, SyncDynamicTranslationKeys::class] as $class) {
            $command = app($class);
            $method = new ReflectionMethod($class, 'scannableFiles');
            $files = $class === TranslationsAuditCode::class ? $method->invoke($command) : $method->invoke($command, config('translation-workbench.paths'));
            expect($normalize($files))->toBe($expected);
        }
        expect($normalize($scanner->scannableFiles([$relative.'/view.blade.php'])))->toBe([$root.'/view.blade.php']);
        expect($scanner->scannableFiles([$relative.'/excluded/hidden.php', $relative.'/notes.txt', $relative.'/xxx-parked.php']))->toBe([]);
    } finally {
        File::deleteDirectory($root);
    }
});

it('uses central defaults unless explicit command paths are supplied', function (string $class) {
    config(['translation-workbench.paths' => ['resources/views', 'packages/example/src']]);
    $command = app($class);
    $property = new ReflectionProperty(Command::class, 'input');
    $method = new ReflectionMethod($class, 'scanPaths');
    $property->setValue($command, new ArrayInput([], $command->getDefinition()));
    expect($method->invoke($command))->toBe(['resources/views', 'packages/example/src']);
    $property->setValue($command, new ArrayInput(['--paths' => ' app, packages/example/src,app '], $command->getDefinition()));
    expect($method->invoke($command))->toBe(['app', 'packages/example/src']);
})->with([[TranslationsGenerateLiteralDiffs::class], [SyncDynamicTranslationKeys::class]]);

it('excludes archived directories from discovery direct scans and absence reconciliation', function () {
    expect(config('translation-workbench.exclude_directory_names'))->toBe(['_old', '-old']);
    $relative = 'storage/framework/testing/archive-paths-'.uniqid();
    $root = base_path($relative);
    $active = ['live.php', 'nested/old-tools/active.php', 'nested/_older/active.php', 'nested/_old.php'];
    $archived = ['_old/hidden.php', 'nested/_old/hidden.blade.php', 'nested/-old/deeper/hidden.php'];
    try {
        foreach ([...$active, ...$archived] as $file) {
            File::ensureDirectoryExists(dirname($root.'/'.$file));
            File::put($root.'/'.$file, "<?php __('Archive fixture');");
        }
        config(['translation-workbench.paths' => [$relative], 'translation-workbench.exclude_paths' => [],
            'translation-workbench.ignored_filename_contains' => [], 'translation-workbench.file_patterns' => ['*.php']]);
        $scanner = app(TranslationWorkbenchScanner::class);
        $paths = fn ($files) => collect($files)->map(fn ($file) => $file->getPathname())->sort()->values()->all();
        $expected = collect($active)->map(fn ($file) => $root.'/'.$file)->sort()->values()->all();
        expect($paths($scanner->scannableFiles()))->toBe($expected);

        foreach ($archived as $file) {
            expect($scanner->scannableFiles([$relative.'/'.$file]))->toBe([])
                ->and($scanner->scanFile($root.'/'.$file))->toBeEmpty();
        }
        foreach ([TranslationsAuditCode::class, TranslationsGenerateLiteralDiffs::class, SyncDynamicTranslationKeys::class] as $class) {
            $command = app($class);
            $method = new ReflectionMethod($class, 'scannableFiles');
            $files = $class === TranslationsAuditCode::class ? $method->invoke($command) : $method->invoke($command, [$relative]);
            expect(collect($files)->map(fn ($file) => is_string($file) ? $file : $file->getPathname())->sort()->values()->all())->toBe($expected);
        }

        $missingArchive = $relative.'/nested/_old/missing.php';
        $missingActive = $relative.'/nested/missing.php';
        $manifest = $scanner->scanWithManifest([$relative], [$missingArchive, $missingActive]);
        expect($manifest['items'])->toHaveCount(count($active))
            ->and($manifest['missing_paths'])->toBe([$missingActive])
            ->and($manifest['reconciliation_paths'])->not->toContain($missingArchive)
            ->and(array_keys($manifest['file_hashes']))->not->toContain($relative.'/_old/hidden.php');

        $onlyArchive = $scanner->scanWithManifest([$relative.'/nested/_old'], [$missingArchive]);
        expect($onlyArchive['items'])->toBeEmpty()
            ->and($onlyArchive['scanned_directories'])->toBe([])
            ->and($onlyArchive['reconciliation_paths'])->toBe([]);
    } finally {
        File::deleteDirectory($root);
    }
});

it('applies configured archived directory names without hardcoded duplicate rules', function () {
    $relative = 'storage/framework/testing/archive-config-'.uniqid();
    $root = base_path($relative);
    try {
        foreach (['_old/a.php', 'retired/a.php'] as $file) {
            File::ensureDirectoryExists(dirname($root.'/'.$file));
            File::put($root.'/'.$file, "<?php __('Configured archive');");
        }
        config(['translation-workbench.exclude_directory_names' => ['retired'],
            'translation-workbench.exclude_paths' => [], 'translation-workbench.ignored_filename_contains' => []]);
        $scanner = app(TranslationWorkbenchScanner::class);
        expect($scanner->scanFile($root.'/_old/a.php'))->toHaveCount(1)
            ->and($scanner->scanFile($root.'/retired/a.php'))->toBeEmpty();
    } finally {
        File::deleteDirectory($root);
    }
});
