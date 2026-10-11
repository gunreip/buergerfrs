<?php

use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('uses the installed Flux badge shade and opacity for opaque path surfaces', function (string $color) {
    $html = Blade::render('<flux:badge :color="$color">Label</flux:badge>', compact('color'));
    preg_match('/(?<![\w:-])bg-'.preg_quote($color, '/').'-(\d+)\/(\d+)/', $html, $light);
    preg_match('/dark:bg-'.preg_quote($color, '/').'-(\d+)\/(\d+)/', $html, $dark);
    expect($light)->not->toBeEmpty()->and($dark)->not->toBeEmpty();
    expect(TranslationWorkbenchColorPalette::surfaceRgb($color))
        ->toContain("var(--color-{$color}-{$light[1]})")->toContain(")) {$light[2]}%")
        ->toContain('var(--tw-graph-protocol-label-mask-bg)');
    expect(TranslationWorkbenchColorPalette::darkSurfaceRgb($color))
        ->toContain("var(--color-{$color}-{$dark[1]})")->toContain(")) {$dark[2]}%");
})->with(['zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal',
    'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose']);

it('retains unknown-color fallbacks and the distinct solid line palette', function () {
    expect(TranslationWorkbenchColorPalette::surfaceRgb(null, '1 2 3'))->toBe('1 2 3')
        ->and(TranslationWorkbenchColorPalette::darkSurfaceRgb('unknown', '4 5 6'))->toBe('4 5 6')
        ->and(TranslationWorkbenchColorPalette::rgb('sky'))->toBe('14 165 233');
});
