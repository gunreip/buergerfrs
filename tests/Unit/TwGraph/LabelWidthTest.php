<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\LabelWidth;
use Tests\TestCase;

uses(TestCase::class);

it('uses the longest line regardless of its position', function (int $position) {
    $lines = array_fill(0, 5, 'Short');
    $lines[$position] = str_repeat('a', 40);
    expect(LabelWidth::resolve(['text' => $lines])['width'])->toBe('halfLong');
})->with([0, 1, 2, 3, 4]);

it('respects explicit label widths', function (array $explicit) {
    $label = ['text' => ['Short', str_repeat('a', 100)], ...$explicit];
    expect(LabelWidth::resolve($label))->toBe($label);
})->with([[['width' => 'default']], [['width' => 'half']], [['long' => true]], [['halfLong' => true]]]);

it('caps automatic width and handles structured lines and embedded newlines', function () {
    expect(LabelWidth::resolve(['text' => ['Short', str_repeat('a', 100)]])['width'])->toBe('long')
        ->and(LabelWidth::resolve(['text' => [['ordinal' => ['number' => 1, 'suffix' => 'st'], 'text' => 'sample:'], str_repeat('a', 40)]])['width'])->toBe('halfLong')
        ->and(LabelWidth::resolve(['text' => "Short\n".str_repeat('a', 40)])['width'])->toBe('halfLong');
});

it('preserves resolved width and text in side label geometry', function () {
    $label = LabelWidth::resolve(['text' => ['Short', str_repeat('a', 40)]]);
    $method = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RenderPreviewBuilder::class, 'labelsFromSideMap');
    $resolved = $method->invoke(null, ['left' => $label]);
    expect($resolved)->toBe([['text' => $label['text'], 'width' => 'halfLong', 'side' => 'left']]);
});

it('resolves nested preview labels without changing geometry or source data', function () {
    $lines = ['Source', str_repeat('a', 50)];
    $preview = [
        'node_labels' => [1 => ['left' => $lines, 'right' => ['text' => $lines, 'width' => 'half']]],
        'extension_node_labels' => [1 => ['start' => ['text' => $lines], 2 => ['right' => $lines, 'long' => true]]],
        'stem_continuation' => [1 => ['length' => '8rem', 'left' => $lines]],
        'step' => ['stepLabel' => ['text' => $lines]],
        'branch_extension' => [['props' => ['endLabel' => ['text' => $lines]]]],
        'source' => ['text' => $lines, 'left' => $lines],
    ];
    $resolved = LabelWidth::preview($preview);
    expect($resolved['node_labels'][1]['left']['width'])->toBe('long')
        ->and($resolved['node_labels'][1]['right']['width'])->toBe('half')
        ->and($resolved['extension_node_labels'][1]['start']['width'])->toBe('long')
        ->and($resolved['extension_node_labels'][1][2]['right']['long'])->toBeTrue()
        ->and($resolved['stem_continuation'][1]['left']['width'])->toBe('long')
        ->and($resolved['stem_continuation'][1]['length'])->toBe('8rem')
        ->and($resolved['step']['stepLabel']['width'])->toBe('long')
        ->and($resolved['branch_extension'][0]['props']['endLabel']['width'])->toBe('long')
        ->and($resolved['source'])->toBe($preview['source']);
});
