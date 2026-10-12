<?php

use App\Console\Commands\SyncDynamicTranslationKeys;
use App\Console\Commands\TranslationsAuditCode;
use App\Console\Commands\TranslationsGenerateLiteralDiffs;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
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
    $property = new ReflectionProperty(Illuminate\Console\Command::class, 'input');
    $method = new ReflectionMethod($class, 'scanPaths');
    $property->setValue($command, new ArrayInput([], $command->getDefinition()));
    expect($method->invoke($command))->toBe(['resources/views', 'packages/example/src']);
    $property->setValue($command, new ArrayInput(['--paths' => ' app, packages/example/src,app '], $command->getDefinition()));
    expect($method->invoke($command))->toBe(['app', 'packages/example/src']);
})->with([[TranslationsGenerateLiteralDiffs::class], [SyncDynamicTranslationKeys::class]]);
