<?php

use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

it('distinguishes JavaScript serialization from translations inside its argument', function () {
    $directory = storage_path('framework/testing/js-scanner-'.uniqid());
    File::ensureDirectoryExists($directory);
    $path = $directory.'/example.blade.php';
    config(['translation-workbench.exclude_paths' => []]);

    try {
        File::put($path, <<<'BLADE'
@js($enabled)
@js($scrollTo)
@js($alreadyTranslatedLabel)
@js(['enabled' => true])
BLADE);
        $scanner = app(TranslationWorkbenchScanner::class);
        expect($scanner->scanFile($path))->toBeEmpty();

        File::put($path, <<<'BLADE'
@js(__('Copy label'))
@js(trans('Save label'))
@js(__($labelKey))
BLADE);
        $findings = $scanner->scanFile($path);
        expect($findings)->toHaveCount(3)
            ->and($findings->pluck('functionName')->all())->toBe(['__', 'trans', '__'])
            ->and($findings->pluck('sourceLine')->all())->toBe([1, 2, 3]);
    } finally {
        File::deleteDirectory($directory);
    }
});
