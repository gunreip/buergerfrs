<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataChainPreview;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataNew;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders only a loading placeholder for the chain preview in the table request', function () {
    view()->share('errors', new ViewErrorBag);
    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });
    $html = Blade::render('@include("translation-workbench::livewire.raw-data.results-timeline-chains")', [
        'timelineChainPreviewId' => '1014', 'timelineChainId' => 1014,
        'datasetHistory' => [], 'reloadTick' => 0,
    ]);

    expect($html)->toContain('data-raw-data-chain-loading')
        ->not->toContain('data-raw-data-chain-preview')
        ->and(implode("\n", $queries))->not->toContain('translation_workbench_timeline_chains');
});

it('loads the preview independently and handles an empty chain inventory', function () {
    Livewire::withoutLazyLoading()
        ->test(TranslationWorkbenchRawDataChainPreview::class, ['previewId' => '1014'])
        ->assertSet('timelineChainPreviewId', '1014')
        ->assertSeeHtml('data-raw-data-chain-preview')
        ->assertDontSeeHtml('data-raw-data-chain-loading');
});

it('keeps the raw table and lazy graph in separate selectable sub-tabs', function () {
    Livewire::test(TranslationWorkbenchRawDataNew::class)
        ->call('setActiveTable', 'translation_workbench_timeline_chains')
        ->assertSet('timelineChainTab', 'raw-data')
        ->assertViewHas('rows')
        ->assertDontSeeHtml('data-raw-data-chain-loading')
        ->set('timelineChainTab', 'tw-graph')
        ->assertViewMissing('rows')
        ->assertViewMissing('summary')
        ->assertSeeHtml('data-raw-data-chain-loading')
        ->set('timelineChainTab', 'raw-data')
        ->assertViewHas('rows')
        ->assertDontSeeHtml('data-raw-data-chain-loading');
});

it('applies measured collision adjustments in the lazy preview and ignores stale dataset requests', function () {
    $preview = new TranslationWorkbenchRawDataChainPreview;
    $preview->mount('1014');
    $preview->extendMeasuredLabelLayout(1014, 0, 'merges.0.bridge_length', 3.0, 0, 'horizontal');

    expect($preview->timelineChainId)->toBe(1014)
        ->and($preview->horizontalLabelLayout['increments']['merges.0.bridge_length'])->toBe(3.0)
        ->and($preview->horizontalLabelLayout['passes'])->toBe(1);

    $state = $preview->horizontalLabelLayout;
    $preview->extendMeasuredLabelLayout(918, 0, 'merges.0.bridge_length', 9.0, 1, 'horizontal');
    expect($preview->horizontalLabelLayout)->toBe($state);
});
