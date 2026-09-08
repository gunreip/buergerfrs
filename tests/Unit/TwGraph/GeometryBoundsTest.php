<?php

use Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds;

it('builds bounds from a single point without min max expressions', function (): void {
    expect(GeometryBounds::fromPoints([
        ['x' => '4rem', 'y' => '6rem'],
    ]))->toBe([
        'minX' => '4rem',
        'maxX' => '4rem',
        'minY' => '6rem',
        'maxY' => '6rem',
        'left' => 'calc(4rem + calc(0rem * -1))',
        'bottom' => 'calc(6rem + calc(0rem * -1))',
        'width' => 'calc(4rem + calc(4rem * -1))',
        'height' => 'calc(6rem + calc(6rem * -1))',
    ]);
});

it('builds padded css bounds from multiple points', function (): void {
    expect(GeometryBounds::fromPoints([
        ['x' => '-8rem', 'y' => '2rem'],
        ['x' => '4rem', 'y' => '10rem'],
    ], '1rem'))->toBe([
        'minX' => 'min(-8rem, 4rem)',
        'maxX' => 'max(-8rem, 4rem)',
        'minY' => 'min(2rem, 10rem)',
        'maxY' => 'max(2rem, 10rem)',
        'left' => 'calc(min(-8rem, 4rem) + calc(1rem * -1))',
        'bottom' => 'calc(min(2rem, 10rem) + calc(1rem * -1))',
        'width' => 'calc(calc(max(-8rem, 4rem) + calc(min(-8rem, 4rem) * -1)) + calc(1rem + 1rem))',
        'height' => 'calc(calc(max(2rem, 10rem) + calc(min(2rem, 10rem) * -1)) + calc(1rem + 1rem))',
    ]);
});

it('ignores invalid or blank point coordinates and falls back to zero', function (): void {
    expect(GeometryBounds::fromPoints([
        ['x' => '', 'y' => null],
        ['x' => null, 'y' => ''],
    ]))->toMatchArray([
        'minX' => '0rem',
        'maxX' => '0rem',
        'minY' => '0rem',
        'maxY' => '0rem',
    ]);
});

it('uses valid coordinates from partially invalid point rows', function (): void {
    expect(GeometryBounds::fromPoints([
        ['x' => '-4rem', 'y' => null],
        ['x' => '', 'y' => '9rem'],
        ['x' => '6rem', 'y' => ''],
    ], '0.5rem'))->toMatchArray([
        'minX' => 'min(-4rem, 6rem)',
        'maxX' => 'max(-4rem, 6rem)',
        'minY' => '9rem',
        'maxY' => '9rem',
        'left' => 'calc(min(-4rem, 6rem) + calc(0.5rem * -1))',
        'bottom' => 'calc(9rem + calc(0.5rem * -1))',
    ]);
});
