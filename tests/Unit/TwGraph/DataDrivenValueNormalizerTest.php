<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\ValueNormalizer;

it('normalizes integer lists by removing empty duplicate and invalid ids', function (): void {
    expect(ValueNormalizer::integerList([5, '5', '007', 0, -2, null, 'abc', 9]))
        ->toBe([5, 7, 9]);
});

it('returns an empty list for non array values', function (): void {
    expect(ValueNormalizer::integerList('5,7,9'))->toBe([])
        ->and(ValueNormalizer::integerList(null))->toBe([]);
});

it('preserves first occurrence order while removing duplicate numeric ids', function (): void {
    expect(ValueNormalizer::integerList(['10', 3, '003', 8, '10', 3, 11]))
        ->toBe([10, 3, 8, 11]);
});

it('drops values that cast to zero or negative ids', function (): void {
    expect(ValueNormalizer::integerList([false, true, '0', '-4', [], ' 12 ', 12.9]))
        ->toBe([1, 12]);
});

it('normalizes numeric strings floats and duplicate casts consistently', function (): void {
    expect(ValueNormalizer::integerList(['0012', 12.4, '12.9', '00013', 13, '14foo', 'foo14']))
        ->toBe([12, 13, 14]);
});
