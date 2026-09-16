<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry;

it('resolves late return connections only for their graph and downstream paths', function (): void {
    AnchorRegistry::put('rail-a', 'outer.end', ['color' => 'zinc', 'returnColor' => 'cyan']);
    ReturnColorRegistry::rail('rail-a', 'outer.join', ['outer.next.true.stem', 'outer.last.true.stem'], ['outer.end']);
    ReturnColorRegistry::rail('rail-b', 'outer.join', ['other.true.stem'], []);
    ReturnColorRegistry::connect('rail-a', 'outer.join', 'green');
    expect(ReturnColorRegistry::resolve('rail-b'))->toBe([]);
    expect(ReturnColorRegistry::resolve('rail-a'))->toBe([
        'outer.next.true.stem' => 'green',
        'outer.last.true.stem' => 'green',
    ]);
    expect(AnchorRegistry::get('rail-a', 'outer.end'))->toMatchArray(['color' => 'zinc', 'returnColor' => 'green']);
    expect(ReturnColorRegistry::resolve('rail-a'))->toBe([]);
});

it('does not silently accept a missing return target', function (): void {
    ReturnColorRegistry::connect('rail-missing', 'missing', 'green');
    try {
        expect(fn () => ReturnColorRegistry::resolve('rail-missing'))->toThrow(InvalidArgumentException::class, 'Unknown IF return target');
    } finally {
        ReturnColorRegistry::forgetGraph('rail-missing');
    }
});
