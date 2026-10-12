<?php

use Gunreip\TranslationWorkbench\Support\DatasetLabelContext;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('forwards information menus through the component chain to each visible label', function (string $component, int $expectedMenus) {
    $label = ['text' => 'Information label', 'contextMenu' => DatasetLabelContext::menu(['type' => 'finding', 'id' => 137])];
    $render = fn (array $label) => Blade::render('<x-translation-workbench::ui.tw-graph graph-id="context-forwarding">'.$component.'</x-translation-workbench::ui.tw-graph>', compact('label'));
    $html = $render($label);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-flux-context]')->length)->toBe($expectedMenus)
        ->and($html)->toContain('tw-graph-dataset-details', '137', 'More information: right-click');
    $plain = $render(['text' => 'Information label']);
    expect($plain)->not->toContain('data-flux-context', 'More information: right-click');
})->with([
    'segment start' => ['<x-translation-workbench::ui.tw-graph.segments.start :segment="[\'id\' => \'sample.start\', \'startLabel\' => $label]" />', 1],
    'segment end' => ['<x-translation-workbench::ui.tw-graph.segments.end :segment="[\'id\' => \'sample.end\', \'endLabel\' => $label]" />', 1],
    'segment step' => ['<x-translation-workbench::ui.tw-graph.segments.step :segment="[\'id\' => \'sample.step\', \'stepLabel\' => $label]" />', 1],
    'bridge label without interactive mask' => ['<x-translation-workbench::ui.tw-graph.segments.label-bridge id="sample.bridge" :label="$label" />', 1],
    'part start' => ['<x-translation-workbench::ui.tw-graph.parts.start id="sample.start" :start-label="$label" />', 1],
    'part end' => ['<x-translation-workbench::ui.tw-graph.parts.end id="sample.end" :end-label="$label" />', 1],
    'trunk start and end' => ['<x-translation-workbench::ui.tw-graph.strang.trunk :start-label="$label" :end-label="$label" :path-count="1" />', 2],
    'branch step' => ['<x-translation-workbench::ui.tw-graph.strang.branch-left :step="[\'stepLabel\' => $label]" />', 1],
    'branch extension step and end' => ['<x-translation-workbench::ui.tw-graph.paths.branch-extension :step="[\'stepLabel\' => $label]" :end-label="$label" />', 2],
]);
