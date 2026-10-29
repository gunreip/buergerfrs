<?php

use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

it('captures option arguments separately without executing them or assigning the group key', function (string $arguments, array $expected) {
    $directory = storage_path('framework/testing/option-capture-'.uniqid());
    File::ensureDirectoryExists($directory);
    $path = $directory.'/example.blade.php';
    $bytes = '{{ \\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label('.$arguments.') }}';
    try {
        File::put($path, $bytes);
        $item = app(TranslationWorkbenchScanner::class)->scanFile($path)->sole();
        $capture = (new CopiedSourceCapture)->capture($item, $bytes, hash('sha256', $bytes), []);
        expect($capture['option_source'])->toBe($expected)
            ->and($capture['reason'])->toBe('special_call_requires_context')
            ->and($capture['status'])->toBe('pending_review')
            ->and($capture['raw_literal'])->toBeNull()
            ->and($capture['suggested_literal'])->toBeNull()
            ->and($capture['found_translation_key'])->toBeNull();
    } finally {
        File::deleteDirectory($directory);
    }
})->with([
    ['\'ui.document.types\', (string) $value, $label', [
        'owner_key' => 'ui.document.types', 'value_expression' => '(string) $value', 'fallback_expression' => '$label',
        'locale_expression' => null, 'fallback_literal' => null, 'state' => 'requires_option_value_evidence',
    ]],
    ['\'ui.document.types\', $document->type, $types[$document->type] ?? $document->type, $locale', [
        'owner_key' => 'ui.document.types', 'value_expression' => '$document->type', 'fallback_expression' => '$types[$document->type] ?? $document->type',
        'locale_expression' => '$locale', 'fallback_literal' => null, 'state' => 'requires_option_value_evidence',
    ]],
    ['\'ui.document.types\', \'a.b\', \'A document\'', [
        'owner_key' => 'ui.document.types', 'value_expression' => "'a.b'", 'fallback_expression' => "'A document'",
        'locale_expression' => null, 'fallback_literal' => 'A document', 'state' => 'requires_option_value_evidence',
    ]],
    ['\'ui.document.types\', throw new \\RuntimeException(\'Never execute\'), str($type)->headline()->toString()', [
        'owner_key' => 'ui.document.types', 'value_expression' => "throw new \\RuntimeException('Never execute')", 'fallback_expression' => 'str($type)->headline()->toString()',
        'locale_expression' => null, 'fallback_literal' => null, 'state' => 'requires_option_value_evidence',
    ]],
]);
