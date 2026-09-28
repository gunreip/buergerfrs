<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the entry direction while selecting the exit arc and extension direction', function (string $side, string $direction, ?string $exit) {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="exit-direction-test">
            <x-translation-workbench::ui.tw-graph.parts.sideways
                id="route" :side="$side" :direction="$direction" :exit-direction="$exit"
                arc-radius="2rem" bridge-length="4rem" extension="1rem"
                :joint-arrow-end="true"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'exit'));
    $actualExit = $exit ?? $direction;
    $end = AnchorRegistry::get('exit-direction-test', 'route.anchorNode-end');
    $entrySign = $direction === 'top-bottom' ? -1 : 1;
    $exitSign = $actualExit === 'top-bottom' ? -1 : 1;
    expect(BoundsRegistry::evaluateRemExpression($end['x']))->toEqual($side === 'left' ? 8 : -8);
    expect(BoundsRegistry::evaluateRemExpression($end['y']))->toEqual(2 * $entrySign + 3 * $exitSign);
    expect($end['direction'])->toBe($actualExit);
    $arc = ($actualExit === 'top-bottom' ? 'north-' : 'south-').($side === 'left' ? 'east' : 'west');
    expect($html)->toContain('route.arc2-'.$arc, 'route.extension-stem1');
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([null, 'bottom-top', 'top-bottom']);
