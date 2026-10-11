<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

it('renders paired trunk labels on their explicitly supplied sides', function () {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="paired-labels">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="paired"
                :stem-count="1"
                :node-labels="[1 => [
                    ['text' => 'Event on left', 'side' => 'left'],
                    ['text' => 'Sample on right', 'side' => 'right'],
                ]]"
            />
        </x-translation-workbench::ui.tw-graph>
        BLADE);

    expect($html)->toContain('label.left.1', 'label.right.2', 'Event on left', 'Sample on right')
        ->not->toContain('label.right.1');
});

beforeEach(function () {
    Schema::shouldReceive('hasTable')->byDefault()->andReturnFalse();
});

it('renders the selected timeline using the shared canvas and preview diagnostics', function (array $findingIds, bool $dev) {
    $html = view('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven', [
        'mainRow' => [
            'id' => 901, 'chain_type' => 'single', 'chain_status' => 'active',
            'translation_key' => 'welcome.message', 'root_key_id' => 15,
            'key_ids' => [15], 'finding_ids' => $findingIds, 'review_ids' => [],
            'timeline_event_ids' => [], 'lang_value_ids' => [],
            'related_translation_keys' => ['welcome.message'], 'timeline_event_summary' => [],
        ],
        'rootRows' => collect([
            ['timestamp' => '2026-10-07 12:00:00', 'role' => 'root', 'branch' => 'Root', 'event' => 'key_created', 'state' => 'active'],
        ]),
        'originRows' => collect(),
        'dev' => $dev,
    ])->render();

    expect($html)->toContain('data-tw-graph-preview-tools', 'timeline-chain-901-data-preview', 'previewDev', 'previewBoxes')
        ->not->toContain('twGraphDataDrivenDev', 'px-24 py-12', '#5486');
    if ($dev && $findingIds !== []) {
        expect($html)->toContain('#'.$findingIds[0]);
    } else {
        expect($html)->not->toContain('Finding information sample');
    }
})->with([
    'chain without findings' => [[], true],
    'finding belongs to selected chain' => [[907, 908], true],
    'initially disabled diagnostics' => [[907], false],
]);

it('renders shared trunk attachments as dots and ordinary unlabeled nodes as arrows', function () {
    $classify = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RenderPreviewBuilder::class, 'classifyAndEliminateEmptyTrunkStems');
    $result = $classify->invoke(null, [1 => '4rem', 2 => '4rem'], [], [1], [], true);
    expect($result['path_lengths'][1])->toBe(['length' => '4rem', 'nodeEndDot' => true]);

    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="junction-test">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="junction"
                :stem-count="2"
                :stem-lengths="$lengths"
            />
        </x-translation-workbench::ui.tw-graph>
        BLADE, ['lengths' => [1 => $result['path_lengths'][1], 2 => '4rem']]);

    expect($html)->toContain('junction.paths.trunk.path.1.node.end', 'junction.paths.trunk.path.2.end.joint-arrow')
        ->not->toContain('junction.paths.trunk.path.1.end.joint-arrow');
});
