<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\MergeTrunkLabelClearance;
use Tests\TestCase;

uses(TestCase::class);

it('extends only the bridge and preserves authored stem and extension lengths', function (string $side, string $x, string $labelX) {
    $merges = [[
        'side' => $side, 'bridge_length' => '20rem', 'stem_length' => '5.75rem',
        'extension_count' => 1, 'extension_bridge_continuations' => [1 => '7rem'],
        'layout' => ['mergeBoundsDebug' => [[
            'id' => 'merge.labels', 'type' => 'clearance-label',
            'x' => $x, 'width' => '31rem', 'y' => '-12rem', 'height' => '18rem',
        ]]],
    ]];
    $bounds = [[
        'id' => 'trunk.label', 'type' => 'trunk-'.$side.'-label',
        'x' => $labelX, 'width' => '13rem', 'y' => '4rem', 'height' => '4rem',
    ]];
    $result = MergeTrunkLabelClearance::apply($merges, $bounds, '20rem', 3);
    expect($result[0]['bridge_length'])->toBe('29rem')
        ->and($result[0]['stem_length'])->toBe('5.75rem')
        ->and($result[0]['extension_bridge_length'])->toBe('20rem')
        ->and($result[0]['extension_bridge_continuations'])->toBe([1 => '7rem'])
        ->and($result[0]['layout']['appliedCompensations'][0])->toMatchArray([
            'baseValue' => '20rem', 'delta' => '9rem', 'effectiveValue' => '29rem', 'gap' => '3rem',
        ]);

    // Refreshed bounds move with the bridge; running the rule again adds nothing.
    $result[0]['layout']['mergeBoundsDebug'][0]['x'] = $side === 'left' ? '-50rem' : '19rem';
    expect(MergeTrunkLabelClearance::apply($result, $bounds, '20rem', 3))->toBe($result);
})->with([['left', '-41rem', '-16rem'], ['right', '10rem', '3rem']]);

it('leaves distant labels and opposite-side labels alone', function () {
    $merges = [['side' => 'left', 'layout' => ['mergeBoundsDebug' => [[
        'id' => 'merge.labels', 'type' => 'clearance-label',
        'x' => '-41rem', 'width' => '31rem', 'y' => '-12rem', 'height' => '18rem',
    ]]]]];
    $bounds = [
        ['id' => 'far', 'type' => 'trunk-left-label', 'x' => '-16rem', 'width' => '13rem', 'y' => '30rem', 'height' => '3rem'],
        ['id' => 'other', 'type' => 'trunk-right-label', 'x' => '3rem', 'width' => '13rem', 'y' => '4rem', 'height' => '4rem'],
    ];
    expect(MergeTrunkLabelClearance::apply($merges, $bounds, '20rem', 3))->toBe($merges);
});

it('clears central trunk stems and terminal labels on either side using individual merge labels', function (string $side, string $x, string $type, string $obstacleX, string $width, string $expected) {
    $merges = [['side' => $side, 'bridge_length' => '20rem', 'layout' => ['mergeBoundsDebug' => [[
        'id' => 'merge.source-label', 'type' => 'clearance-label',
        'x' => $x, 'width' => '25rem', 'y' => '2rem', 'height' => '3rem',
    ]]]]];
    $bounds = [['id' => 'trunk.center', 'type' => $type, 'x' => $obstacleX, 'width' => $width, 'y' => '0rem', 'height' => '6rem']];
    $result = MergeTrunkLabelClearance::apply($merges, $bounds, '20rem', 3);
    expect($result[0]['bridge_length'])->toBe($expected)
        ->and($result[0]['layout']['appliedCompensations'][0]['sources'][0]['against'])->toBe('merge.source-label');
})->with([
    ['left', '-23rem', 'strang', '-1rem', '2rem', '26rem'],
    ['right', '-2rem', 'strang', '-1rem', '2rem', '26rem'],
    ['left', '-23rem', 'trunk-terminal-label', '-6rem', '12rem', '31rem'],
    ['right', '-2rem', 'trunk-terminal-label', '-6rem', '12rem', '31rem'],
]);

it('ignores empty space in group bounds while still clearing a real stem', function () {
    $box = ['id' => 'group', 'type' => 'merge-start-stem',
        'x' => '-41rem', 'width' => '31rem', 'y' => '-12rem', 'height' => '18rem'];
    $merges = [['side' => 'left', 'bridge_length' => '20rem', 'layout' => ['mergeBoundsDebug' => [$box]]]];
    $labels = [['id' => 'trunk.label', 'type' => 'trunk-left-label',
        'x' => '-16rem', 'width' => '13rem', 'y' => '4rem', 'height' => '4rem']];
    expect(MergeTrunkLabelClearance::apply($merges, $labels, '20rem', 3))->toBe($merges);

    $merges[0]['layout']['mergeBoundsDebug'][] = array_replace($box, [
        'id' => 'stem', 'type' => 'clearance-line', 'x' => '-12rem', 'width' => '0.25rem',
    ]);
    expect(MergeTrunkLabelClearance::apply($merges, $labels, '20rem', 3)[0]['bridge_length'])->toBe('27.25rem');
});

it('does not extend a bridge to move its fixed trunk-side endpoint away from a label', function (string $side) {
    $merges = [['side' => $side, 'bridge_length' => '20rem', 'layout' => ['mergeBoundsDebug' => [[
        'id' => 'bridge', 'type' => 'merge-bridge', 'x' => '-22.75rem',
        'width' => '20rem', 'y' => '4rem', 'height' => '1.5rem',
    ]]]]];
    $labels = [['id' => 'trunk.label', 'type' => 'trunk-'.$side.'-label',
        'x' => '-24rem', 'width' => '24rem', 'y' => '5.75rem', 'height' => '3rem']];
    expect(MergeTrunkLabelClearance::apply($merges, $labels, '20rem', 3))->toBe($merges);
})->with(['left', 'right']);

it('keeps group bounds on their side of the trunk axis without requiring a trunk event', function (string $side, string $x, string $finalX) {
    $merges = [['side' => $side, 'bridge_length' => '20rem', 'stem_continuation' => [1 => []],
        'layout' => ['mergeBoundsDebug' => [[
            'id' => 'group', 'type' => 'merge-start-stem',
            'x' => $x, 'width' => '30rem', 'y' => '-40rem', 'height' => '20rem',
        ]]]]];
    $result = MergeTrunkLabelClearance::apply($merges, [], '20rem', 3, axisOnly: true);
    expect($result[0]['bridge_length'])->toBe('25rem')
        ->and($result[0]['stem_continuation'])->toBe([1 => []])
        ->and($result[0]['layout']['appliedCompensations'][0]['sources'][0]['constraint'])->toBe('trunk-axis');

    $result[0]['layout']['mergeBoundsDebug'][0]['x'] = $finalX;
    expect(MergeTrunkLabelClearance::apply($result, [], '20rem', 3, axisOnly: true))->toBe($result);
})->with([['left', '-28rem', '-33rem'], ['right', '-2rem', '3rem']]);
