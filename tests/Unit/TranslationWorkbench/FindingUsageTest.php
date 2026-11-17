<?php

use Gunreip\TranslationWorkbench\Support\FindingUsage;

it('identifies the control at each captured call in a Blade table header', function () {
    $root = sys_get_temp_dir().'/finding-usage-'.uniqid();
    mkdir($root);
    $file = 'example.blade.php';
    $lines = [
        '<flux:table.column>',
        "<flux:callout.heading>{{ __('Text') }}</flux:callout.heading>",
        '<flux:tooltip>',
        "<flux:button>{{ __('Text') }}</flux:button>",
        "<x-translation-workbench::ui.common.tooltip-content :heading=\"__('Text')\" />",
        '</flux:tooltip>',
        '</flux:table.column>',
        "<span>{{ __('Text') }}</span>",
    ];
    try {
        file_put_contents("$root/$file", implode("\n", $lines));
        foreach ([1 => 'Table header', 3 => 'Button', 4 => 'Tooltip', 7 => null] as $line => $expected) {
            $finding = ['status' => 'active', 'path' => $file, 'raw_expression' => "__('Text')",
                'source_line' => $line + 1, 'source_column' => strpos($lines[$line], "__('Text')") + 1];
            expect(FindingUsage::forFinding($finding, $root))->toBe($expected);
        }
        expect(FindingUsage::forFinding(array_replace($finding, ['source_column' => 1]), $root))->toBeNull();
    } finally {
        unlink("$root/$file");
        rmdir($root);
    }
});
