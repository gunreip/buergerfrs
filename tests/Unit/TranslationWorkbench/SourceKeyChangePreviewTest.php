<?php

use Gunreip\TranslationWorkbench\Support\SourceKeyChangePreview;

it('changes only the captured key argument and preserves quoting and other arguments', function () {
    $bytes = '{{ __("ui.type", ["example" => "ui.type"]) }}';
    $finding = ['id' => 1, 'source_line' => 1, 'source_column' => 4, 'raw_expression' => '__("ui.type", ["example" => "ui.type"])'];
    $service = new SourceKeyChangePreview;
    $result = $service->preview($bytes, hash('sha256', $bytes), 'sample.blade.php', [$finding], 'ui.type', 'ui.field.type');
    expect($result['proposed_bytes'])->toBe('{{ __("ui.field.type", ["example" => "ui.type"]) }}')
        ->and($result['edits'][0]['finding'])->toBe(1);
    expect(fn () => $service->preview($bytes.' ', hash('sha256', $bytes), 'sample.blade.php', [$finding], 'ui.type', 'ui.field.type'))->toThrow(RuntimeException::class);
    expect(fn () => $service->preview($bytes, hash('sha256', $bytes), 'sample.blade.php', [$finding, $finding], 'ui.type', 'ui.field.type'))->toThrow(RuntimeException::class);
    $finding['source_column']++;
    expect(fn () => $service->preview($bytes, hash('sha256', $bytes), 'sample.blade.php', [$finding], 'ui.type', 'ui.field.type'))->toThrow(RuntimeException::class);
});

it('preserves directive wrappers and multiple occurrences including multibyte prefixes', function () {
    $bytes = "ä @lang('ui.type')\n{{ __('ui.type') }}";
    $findings = [
        ['id' => 1, 'source_line' => 1, 'source_column' => 4, 'raw_expression' => "@lang('ui.type')"],
        ['id' => 2, 'source_line' => 2, 'source_column' => 4, 'raw_expression' => "__('ui.type')"],
    ];
    $result = (new SourceKeyChangePreview)->preview($bytes, hash('sha256', $bytes), 'sample.blade.php', $findings, 'ui.type', 'ui.field.type');
    expect($result['proposed_bytes'])->toBe("ä @lang('ui.field.type')\n{{ __('ui.field.type') }}");
});

it('rejects commented occurrences', function () {
    $bytes = "{{-- __('ui.type') --}}";
    expect(fn () => (new SourceKeyChangePreview)->preview($bytes, hash('sha256', $bytes), 'sample.blade.php', [
        ['id' => 1, 'source_line' => 1, 'source_column' => 6, 'raw_expression' => "__('ui.type')"],
    ], 'ui.type', 'ui.field.type'))->toThrow(RuntimeException::class);
});
