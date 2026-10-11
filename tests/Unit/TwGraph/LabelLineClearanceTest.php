<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\LabelLineClearance;

it('retains vertical label separation before clearing remaining lines horizontally', function (bool $blocked) {
    $geometry = static function (array $state) use ($blocked): array {
        $boxes = [
            ['id' => 'moving', 'owner' => 'moving', 'type' => 'label', 'xStart' => -$state['bridge'], 'xEnd' => 2 - $state['bridge'], 'yStart' => $state['stem'], 'yEnd' => $state['stem'] + 2],
            ['id' => 'fixed', 'owner' => 'fixed', 'type' => 'label', 'xStart' => 1, 'xEnd' => 3, 'yStart' => 1, 'yEnd' => 3],
        ];
        if ($blocked) {
            // A long line still needs horizontal clearance after separating the labels.
            $boxes[] = ['id' => 'line', 'owner' => 'fixed', 'type' => 'line', 'xStart' => 1, 'xEnd' => 1.25, 'yStart' => 0, 'yEnd' => 10000];
            // This line grows together with the stem: extending cannot escape it.
            $boxes[2]['yEnd'] += $state['stem'];
        }

        return $boxes;
    };
    $result = LabelLineClearance::resolve(['stem' => 0, 'bridge' => 0], $geometry, [[
        'owner' => 'moving', 'side' => 'left', 'vertical' => 1,
        'stem' => static fn ($state, $delta) => [...$state, 'stem' => $state['stem'] + $delta],
        'bridge' => static fn ($state, $delta) => [...$state, 'bridge' => $state['bridge'] + $delta],
    ]], 1);
    expect(LabelLineClearance::collisions($geometry($result)))->toBe([]);
    if ($blocked) {
        expect($result['stem'])->toBe(4.0)->and($result['bridge'])->toBeGreaterThan(0);
    } else {
        expect($result['stem'])->toBe(4.0)->and($result['bridge'])->toBe(0);
    }
})->with([false, true]);

it('discards stem candidates that introduce a new collision', function () {
    $geometry = static fn (array $state): array => [
        ['id' => 'moving', 'owner' => 'moving', 'type' => 'label', 'xStart' => -$state['bridge'], 'xEnd' => 2 - $state['bridge'], 'yStart' => $state['stem'], 'yEnd' => $state['stem'] + 2],
        ['id' => 'fixed', 'owner' => 'fixed', 'type' => 'label', 'xStart' => 1, 'xEnd' => 3, 'yStart' => 1, 'yEnd' => 3],
        // Connected geometry moves into the lane on every vertical candidate.
        ['id' => 'connected', 'owner' => 'fixed', 'type' => 'label', 'xStart' => 1, 'xEnd' => 3, 'yStart' => max(4, $state['stem']), 'yEnd' => max(4, $state['stem']) + 2],
    ];
    $result = LabelLineClearance::resolve(['stem' => 0, 'bridge' => 0], $geometry, [[
        'owner' => 'moving', 'side' => 'left', 'vertical' => 1,
        'stem' => static fn ($state, $delta) => [...$state, 'stem' => $state['stem'] + $delta],
        'bridge' => static fn ($state, $delta) => [...$state, 'bridge' => $state['bridge'] + $delta],
    ]], 1);

    expect($result['stem'])->toBe(0)
        ->and($result['bridge'])->toBeGreaterThan(0)
        ->and(LabelLineClearance::collisions($geometry($result)))->toBe([]);
});

it('places later owners without changing settled predecessor lengths', function () {
    $geometry = static function (array $state): array {
        $boxes = [['id' => 'trunk', 'owner' => 'trunk', 'type' => 'label',
            'xStart' => 0, 'xEnd' => 2, 'yStart' => 0, 'yEnd' => 2]];
        foreach ($state as $owner => $length) {
            $boxes[] = ['id' => $owner, 'owner' => $owner, 'type' => 'label',
                'xStart' => 0, 'xEnd' => 2, 'yStart' => $length, 'yEnd' => $length + 2];
        }

        return $boxes;
    };
    $state = ['main' => 0, 'extension1' => 0, 'extension2' => 0];
    $options = [];
    foreach (array_keys($state) as $owner) {
        $options[] = [
            'owner' => $owner, 'side' => 'left', 'vertical' => 1,
            'stem' => static fn ($candidate, $delta) => array_replace($candidate, [$owner => $candidate[$owner] + $delta]),
            'bridge' => static fn ($candidate, $delta) => $candidate,
        ];
    }
    $first = LabelLineClearance::resolveOrdered($state, $geometry, array_slice($options, 0, 2), 1, array_keys($state));
    $second = LabelLineClearance::resolveOrdered($state, $geometry, $options, 1, array_keys($state));

    expect($second['main'])->toBe($first['main'])
        ->and($second['extension1'])->toBe($first['extension1'])
        ->and($second['extension2'])->toBeGreaterThan($second['extension1'])
        ->and(LabelLineClearance::collisions($geometry($second)))->toBe([]);
});

it('removes obsolete vertical compensation after an unavoidable bridge extension', function () {
    $geometry = static fn (array $state): array => [
        ['id' => 'moving', 'owner' => 'moving', 'type' => 'label', 'xStart' => -$state['bridge'], 'xEnd' => 2 - $state['bridge'], 'yStart' => $state['stem'], 'yEnd' => $state['stem'] + 2],
        ['id' => 'fixed', 'owner' => 'fixed', 'type' => 'label', 'xStart' => 1, 'xEnd' => 3, 'yStart' => 1, 'yEnd' => 3],
        ['id' => 'line', 'owner' => 'fixed', 'type' => 'line', 'xStart' => 1, 'xEnd' => 1.25, 'yStart' => 0, 'yEnd' => 100],
    ];
    $options = [[
        'owner' => 'moving', 'side' => 'left', 'vertical' => 1,
        'stemLength' => static fn ($state) => $state['stem'],
        'bridgeLength' => static fn ($state) => $state['bridge'],
        'stem' => static fn ($state, $delta) => [...$state, 'stem' => $state['stem'] + $delta],
        'bridge' => static fn ($state, $delta) => [...$state, 'bridge' => $state['bridge'] + $delta],
    ]];
    $result = LabelLineClearance::resolveOrdered(['stem' => 0, 'bridge' => 0], $geometry, $options, 1, ['moving']);

    expect($result['stem'])->toBe(0)
        ->and($result['bridge'])->toBe(2.0)
        ->and(LabelLineClearance::collisions($geometry($result)))->toBe([]);
});
