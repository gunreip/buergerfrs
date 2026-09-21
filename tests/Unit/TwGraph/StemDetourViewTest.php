<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the original entry endpoint while routing outside on either side and direction', function ($side, $direction) {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="detour">
            <x-translation-workbench::ui.tw-graph.paths.stem-detour
                id="entry" :anchor-start="['x' => '3rem', 'y' => '7rem']"
                length="32rem" :side="$side" :direction="$direction"
                arc-radius="2rem" bridge-length="10rem" before-length="1rem" after-length="2rem"
                :node-label-right="['text' => ['CASE next']]" :dev-counter-start="4"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction'));
    $get = fn ($id) => AnchorRegistry::get('detour', 'entry.'.$id);
    $value = fn ($v) => BoundsRegistry::evaluateRemExpression($v);
    $sx = $side === 'right' ? 1 : -1;
    $sy = $direction === 'bottom-top' ? 1 : -1;
    expect($value($get('anchorNode-end')['x']))->toBe(3.0);
    expect($value($get('anchorNode-end')['y']))->toBe(7.0 + $sy * 32);
    expect($value($get('outward.anchorNode-end')['x']))->toBe(3.0 + $sx * 14);
    expect($value($get('stem.anchorNode-end')['x']))->toBe(3.0 + $sx * 14);
    expect($value($get('inward.anchorNode-end')['x']))->toBe(3.0);
    expect($get('anchorNode-end')['devCounterNext'])->toBe('9');
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom']);

it('rejects detours that cannot fit instead of changing their dimensions silently', function ($length, $radius, $bridge) {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="invalid-detour">
            <x-translation-workbench::ui.tw-graph.paths.stem-detour id="entry"
                :anchor-start="['x' => '0rem', 'y' => '0rem']"
                :length="$length" :arc-radius="$radius" :bridge-length="$bridge"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['length' => $length, 'radius' => $radius, 'bridge' => $bridge]))->toThrow(\Illuminate\View\ViewException::class);
})->with([['6rem', '2rem', '4rem'], ['32rem', '-1rem', '4rem'], ['32rem', '2rem', '-1rem']]);

it('preserves switch endpoints when a single CASE or DEFAULT entry uses a detour', function ($target) {
    $ends = [];
    foreach ([false, true] as $enabled) {
        $detour = $enabled ? ['side' => 'right', 'bridgeLength' => '10rem'] : null;
        $cases = [['key' => 'a', 'stemLength' => '32rem', 'actionLabel' => ['text' => ['Action']]]];
        $default = ['text' => ['Fallback'], 'stemLength' => '32rem'];
        if ($target === 'case') $cases[0]['entryDetour'] = $detour;
        else $default['entryDetour'] = $detour;
        Blade::render(<<<'BLADE'
            <x-translation-workbench::ui.tw-graph graph-id="switch-detour">
                <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                    id="switch" :cases="$cases" :case-default="$default"
                />
            </x-translation-workbench::ui.tw-graph>
        BLADE, compact('cases', 'default'));
        foreach (['case.a.entry.anchorNode-end', 'case.default.entry.anchorNode-end', 'anchorNode-end'] as $key) {
            $anchor = AnchorRegistry::get('switch-detour', 'switch.'.$key);
            $ends[(int) $enabled][$key] = array_map(fn ($axis) => BoundsRegistry::evaluateRemExpression($anchor[$axis]), ['x', 'y']);
        }
    }
    expect($ends[1])->toBe($ends[0]);
})->with(['case', 'default']);

it('documents four handmade detours with source boxes and the same vertical endpoint', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-stem-detour';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.paths.stem-detour'))->toBe(4);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(4);
    expect($html)->toContain('data-tw-graph-preview-tools');
    foreach (['left-up' => 25, 'right-up' => 25, 'left-down' => 5, 'right-down' => 5] as $key => $y) {
        $graph = 'idea-to-paper-paths-stem-detour-'.$key;
        $end = AnchorRegistry::get($graph, 'literature.paths.stem-detour.'.$key.'.anchorNode-end');
        expect(BoundsRegistry::evaluateRemExpression($end['x']))->toBe(0.0);
        expect(BoundsRegistry::evaluateRemExpression($end['y']))->toBe((float) $y);
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('Same axis');
        expect($canvas->textContent)->not->toContain('{--', ':anchor-start=', '=>');
    }
});

it('loads the stem detour documentation only when its paths tab is selected', function () {
    \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class)
        ->assertDontSee('id="idea-to-paper-paths-stem-detour-left-up"', false)
        ->set('tabs.main', 'idea-to-paper-paths')
        ->set('tabs.paths_index', 'idea-to-paper-paths-stem-detour')
        ->assertSee('id="idea-to-paper-paths-stem-detour-left-up"', false)
        ->assertSee('id="idea-to-paper-paths-stem-detour-right-down"', false)
        ->call('openReference', 'strang.flow-switch-case')
        ->assertSet('tabs.reference_strang', 'reference-flow-switch-case')
        ->assertDontSee('id="idea-to-paper-paths-stem-detour-left-up"', false);
});
