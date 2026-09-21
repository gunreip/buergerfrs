<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ComponentInventory;

it('preserves branches and skipped layers while ignoring comments and PHP examples', function () {
    $directory = sys_get_temp_dir().'/graph-inventory-'.bin2hex(random_bytes(8));
    mkdir($directory.'/strang/_old', 0777, true);
    mkdir($directory.'/primitives', 0777, true);
    $files = [
        'strang/root' => <<<'BLADE'
{{-- <x-translation-workbench::ui.tw-graph.parts.fake /> --}}
@php
$example = '<x-translation-workbench::ui.tw-graph.parts.example />';
@endphp
@if ($enabled)
<x-translation-workbench::ui.tw-graph.strang.child />
@endif
<x-translation-workbench::ui.tw-graph.primitives.line />
BLADE,
        'strang/child' => '<x-translation-workbench::ui.tw-graph.primitives.line />',
        'strang/_old/retired' => '<x-translation-workbench::ui.tw-graph.primitives.line />',
        'primitives/line' => '<span></span>',
    ];
    try {
        foreach ($files as $name => $source) {
            file_put_contents($directory.'/'.$name.'.blade.php', $source);
        }
        $inventory = new ComponentInventory($directory);
        $rows = $inventory->rows(false, 'strang.root');
        expect($rows)->toHaveCount(2)
            ->and(array_column($rows[0]['cells']['strang'], 'name'))->toBe(['strang.root', 'strang.child'])
            ->and($rows[0]['cells']['paths'])->toBe([])
            ->and($rows[0]['cells']['parts'])->toBe([])
            ->and($rows[0]['cells']['segments'])->toBe([])
            ->and($rows[0]['cells']['strang'][1]['conditions'])->toBe(['@if (line 5)'])
            ->and($rows[1]['cells']['primitives'][0]['conditions'])->toBe([])
            ->and($inventory->roots())->not->toContain('strang._old.retired')
            ->and($inventory->roots(true))->toBe(['strang._old.retired'])
            ->and($inventory->rows(false, '../../.env'))->toBe([]);
        file_put_contents($directory.'/strang/child.blade.php', '<x-translation-workbench::ui.tw-graph.strang.root />');
        $cyclic = new ComponentInventory($directory);
        expect($cyclic->rows(false, 'strang.root')[0]['note'])->toBe('Recursive call');
    } finally {
        foreach (array_keys($files) as $name) {
            unlink($directory.'/'.$name.'.blade.php');
        }
        rmdir($directory.'/strang/_old');
        rmdir($directory.'/strang');
        rmdir($directory.'/primitives');
        rmdir($directory);
    }
});
