<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;

beforeEach(function (): void {
    BoundsRegistry::forgetGraph('tw-graph-bounds-test');
});

it('stores canonical ids while keeping the rendered id', function (): void {
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'strang.branch-left.1.main.path.branch.bridge1',
        '-30rem',
        '12rem',
        '18rem',
        '1.5rem',
    );

    $left = BoundsRegistry::summary('tw-graph-bounds-test')['left'];

    expect($left['count'])->toBe(1)
        ->and($left['items'][0])->toMatchArray([
            'id' => 'strang.left.1.branch.bridge1',
            'renderId' => 'strang.branch-left.1.main.path.branch.bridge1',
            'side' => 'left',
            'x' => '-30rem',
            'y' => '12rem',
            'width' => '18rem',
            'height' => '1.5rem',
        ]);
});

it('replaces repeated rendered bounds instead of counting stale geometry twice', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.branch-left.1.main.path.branch.bridge1', '-30rem', '12rem', '18rem', '1.5rem');
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.branch-left.1.main.path.branch.bridge1', '-40rem', '14rem', '24rem', '1.5rem');

    $summary = BoundsRegistry::summary('tw-graph-bounds-test');
    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '2rem', '3rem');

    expect($summary['left']['count'])->toBe(1)
        ->and($summary['left']['items'][0])->toMatchArray([
            'id' => 'strang.left.1.branch.bridge1',
            'renderId' => 'strang.branch-left.1.main.path.branch.bridge1',
            'x' => '-40rem',
            'y' => '14rem',
            'width' => '24rem',
        ])
        ->and($metrics['minXRem'])->toBe(-40.0)
        ->and($metrics['maxXRem'])->toBe(0.0)
        ->and($metrics['widthRem'])->toBe(46.0);
});

it('calculates numeric canvas metrics from registered bounds and padding', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'left.branch', '-20rem', '4rem', '12rem', '8rem', 'left');
    BoundsRegistry::put('tw-graph-bounds-test', 'right.branch', '10rem', '14rem', '16rem', '10rem', 'right');

    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '2rem', '3rem');

    expect($metrics)->toMatchArray([
        'minXRem' => -20.0,
        'maxXRem' => 26.0,
        'originLeftRem' => 23.0,
        'widthRem' => 52.0,
        'minYRem' => 0.0,
        'maxYRem' => 24.0,
        'originBottomRem' => 2.0,
        'heightRem' => 28.0,
    ]);
});

it('keeps registered bounds isolated per graph id', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'left.branch', '-20rem', '4rem', '12rem', '8rem', 'left');
    BoundsRegistry::put('other-graph', 'right.branch', '10rem', '14rem', '16rem', '10rem', 'right');

    BoundsRegistry::forgetGraph('tw-graph-bounds-test');

    expect(BoundsRegistry::summary('tw-graph-bounds-test')['left']['count'])->toBe(0)
        ->and(BoundsRegistry::summary('other-graph')['right']['count'])->toBe(1);

    BoundsRegistry::forgetGraph('other-graph');
});

it('keeps equal canonical ids isolated by render id and graph id', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.branch-left.1.main.path.branch.bridge1', '-20rem', '4rem', '12rem', '1.5rem');
    BoundsRegistry::put('other-graph', 'strang.left.1.branch.bridge1', '-30rem', '8rem', '18rem', '1.5rem');
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.left.1.branch.bridge1', '-24rem', '5rem', '14rem', '1.5rem');

    $summary = BoundsRegistry::summary('tw-graph-bounds-test');
    $otherSummary = BoundsRegistry::summary('other-graph');

    expect($summary['left']['count'])->toBe(2)
        ->and(collect($summary['left']['items'])->pluck('id')->unique()->values()->all())->toBe([
            'strang.left.1.branch.bridge1',
        ])
        ->and(collect($summary['left']['items'])->pluck('renderId')->all())->toBe([
            'strang.branch-left.1.main.path.branch.bridge1',
            'strang.left.1.branch.bridge1',
        ])
        ->and($summary['left']['items'][1])->toMatchArray([
            'id' => 'strang.left.1.branch.bridge1',
            'renderId' => 'strang.left.1.branch.bridge1',
            'x' => '-24rem',
            'y' => '5rem',
            'width' => '14rem',
        ])
        ->and($otherSummary['left']['count'])->toBe(1)
        ->and($otherSummary['left']['items'][0])->toMatchArray([
            'id' => 'strang.left.1.branch.bridge1',
            'renderId' => 'strang.left.1.branch.bridge1',
            'x' => '-30rem',
            'y' => '8rem',
            'width' => '18rem',
        ]);

    BoundsRegistry::forgetGraph('other-graph');
});

it('exposes origin bottom and canvas height helpers from canvas metrics', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'trunk.center.1.bounds', '-1rem', '-3rem', '2rem', '11rem', 'center');

    expect(BoundsRegistry::originBottom('tw-graph-bounds-test', '2rem'))->toBe('calc((min(0rem, -3rem)) * -1 + 2rem)')
        ->and(BoundsRegistry::canvasHeight('tw-graph-bounds-test', '2rem'))->toBe('calc(max(0rem, calc(-3rem + 11rem)) - min(0rem, -3rem) + (2rem * 2))');
});

it('returns stable zero metrics for graphs without registered bounds', function (): void {
    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '2rem', '3rem');

    expect($metrics)->toMatchArray([
        'minX' => '0rem',
        'minXRem' => 0.0,
        'maxX' => '0rem',
        'maxXRem' => 0.0,
        'originLeft' => 'calc((0rem) * -1 + 3rem)',
        'originLeftRem' => 3.0,
        'width' => 'calc(0rem - 0rem + (3rem * 2))',
        'widthRem' => 6.0,
        'originBottom' => 'calc((0rem) * -1 + 2rem)',
        'originBottomRem' => 2.0,
        'height' => 'calc(0rem - 0rem + (2rem * 2))',
        'heightRem' => 4.0,
    ]);
});

it('infers sides from concise canonical element ids', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.left.1.branch.bridge1', '-30rem', '12rem', '18rem', '1.5rem');
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.right.1.rekey.target.bridge1', '12rem', '12rem', '18rem', '1.5rem');
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.trunk.center.1.bounds', '-1rem', '0rem', '2rem', '12rem');

    $summary = BoundsRegistry::summary('tw-graph-bounds-test');

    expect($summary['left']['items'][0]['side'])->toBe('left')
        ->and($summary['right']['items'][0]['side'])->toBe('right')
        ->and($summary['center']['items'][0]['side'])->toBe('center');
});

it('keeps css var expressions while evaluating the safe numeric parts', function (): void {
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'trunk.center.1.label',
        'calc(var(--tw-graph-x) - 4rem)',
        '2rem',
        '8rem',
        '4rem',
        'center',
    );

    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test');

    expect($metrics['minX'])->toBe('min(0rem, calc(var(--tw-graph-x) - 4rem))')
        ->and($metrics['maxX'])->toBe('max(0rem, calc(calc(var(--tw-graph-x) - 4rem) + 8rem))')
        ->and($metrics['minXRem'])->toBe(0.0)
        ->and($metrics['maxXRem'])->toBe(0.0)
        ->and($metrics['widthRem'])->toBe(4.0);
});

it('evaluates nested css min max calc expressions for numeric canvas metrics', function (): void {
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'nested.bounds',
        'calc(min(-6rem, -2rem) - 1rem)',
        'calc(max(2rem, 4rem) + 1rem)',
        'calc(max(8rem, 10rem) + 2rem)',
        'calc(min(6rem, 8rem) / 2)',
        'left',
    );

    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '2rem', '3rem');

    expect($metrics)->toMatchArray([
        'minXRem' => -7.0,
        'maxXRem' => 5.0,
        'originLeftRem' => 10.0,
        'widthRem' => 18.0,
        'minYRem' => 0.0,
        'maxYRem' => 8.0,
        'originBottomRem' => 2.0,
        'heightRem' => 12.0,
    ]);
});

it('honors explicit bound side ahead of inferred element side', function (): void {
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'strang.branch-left.1.main.path.branch.bridge1',
        '-20rem',
        '6rem',
        '12rem',
        '1.5rem',
        'center',
    );

    $summary = BoundsRegistry::summary('tw-graph-bounds-test');

    expect($summary['left']['count'])->toBe(0)
        ->and($summary['center']['count'])->toBe(1)
        ->and($summary['center']['items'][0]['id'])->toBe('strang.left.1.branch.bridge1');
});

it('uses separate horizontal padding for canvas width without changing vertical metrics', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'left.branch', '-10rem', '-2rem', '6rem', '8rem', 'left');
    BoundsRegistry::put('tw-graph-bounds-test', 'right.branch', '4rem', '6rem', '8rem', '4rem', 'right');

    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '2rem', '5rem');

    expect($metrics)->toMatchArray([
        'originLeftRem' => 15.0,
        'widthRem' => 32.0,
        'originBottomRem' => 4.0,
        'heightRem' => 16.0,
    ])
        ->and($metrics['originLeft'])->toBe('calc((min(0rem, -10rem, 4rem)) * -1 + 5rem)')
        ->and($metrics['originBottom'])->toBe('calc((min(0rem, -2rem, 6rem)) * -1 + 2rem)');
});

it('spans canvas metrics across left center and right registered bounds', function (): void {
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.left.1.branch.label', '-28rem', '12rem', '14rem', '9rem');
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.trunk.center.1.label', '-10rem', '-4rem', '20rem', '18rem');
    BoundsRegistry::put('tw-graph-bounds-test', 'strang.right.1.rekey.target.label', '18rem', '5rem', '16rem', '7rem');

    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '3rem', '4rem');
    $summary = BoundsRegistry::summary('tw-graph-bounds-test');

    expect($metrics)->toMatchArray([
        'minXRem' => -28.0,
        'maxXRem' => 34.0,
        'originLeftRem' => 32.0,
        'widthRem' => 70.0,
        'minYRem' => -4.0,
        'maxYRem' => 21.0,
        'originBottomRem' => 7.0,
        'heightRem' => 31.0,
    ])
        ->and($summary['left']['count'])->toBe(1)
        ->and($summary['center']['count'])->toBe(1)
        ->and($summary['right']['count'])->toBe(1);
});

it('evaluates nested calc arithmetic with subtraction multiplication and division', function (): void {
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'math.bounds',
        'calc((12rem - 4rem) / 2)',
        'calc(3rem * -1)',
        'calc(2rem * 3)',
        'calc((18rem - 6rem) / 3)',
        'center',
    );

    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '1rem', '1rem');

    expect($metrics)->toMatchArray([
        'minXRem' => 0.0,
        'maxXRem' => 10.0,
        'widthRem' => 12.0,
        'minYRem' => -3.0,
        'maxYRem' => 1.0,
        'originBottomRem' => 4.0,
        'heightRem' => 6.0,
    ]);
});

it('keeps multiple sub bounds for one strang visible in the canvas metrics', function (): void {
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'strang.branch-left.1.main.path.branch.bridge1.bounds',
        '-30rem',
        '12rem',
        '28rem',
        '1.5rem',
    );
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'strang.branch-left.1.main.path.branch.step.bounds',
        '-54rem',
        '15rem',
        '32rem',
        '10rem',
    );
    BoundsRegistry::put(
        'tw-graph-bounds-test',
        'strang.branch-left.1.main.path.branch.stem-labels.bounds',
        '-52rem',
        '25rem',
        '32rem',
        '18rem',
    );

    $summary = BoundsRegistry::summary('tw-graph-bounds-test');
    $metrics = BoundsRegistry::canvasMetrics('tw-graph-bounds-test', '2rem', '3rem');

    expect($summary['left']['count'])->toBe(3)
        ->and(collect($summary['left']['items'])->pluck('id')->all())->toBe([
            'strang.left.1.branch.bridge1.bounds',
            'strang.left.1.branch.step.bounds',
            'strang.left.1.branch.stem.labels.bounds',
        ])
        ->and($metrics)->toMatchArray([
            'minXRem' => -54.0,
            'maxXRem' => 0.0,
            'originLeftRem' => 57.0,
            'widthRem' => 60.0,
            'maxYRem' => 43.0,
            'heightRem' => 47.0,
        ]);
});
