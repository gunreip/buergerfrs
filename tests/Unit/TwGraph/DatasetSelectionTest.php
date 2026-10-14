<?php

use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataNew;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

it('loads an existing dataset by ID or critical selection and records it in the history', function (bool $critical, string $host) {
    Schema::shouldReceive('hasTable')->with('translation_workbench_timeline_chains')->andReturnTrue();
    $query = Mockery::mock(Builder::class);
    DB::shouldReceive('table')->with('translation_workbench_timeline_chains')->andReturn($query);
    $query->shouldReceive('where')->with('id', 1014)->andReturnSelf();
    $query->shouldReceive('exists')->once()->andReturnTrue();
    $query->shouldReceive('first')->andReturn((object) [
        'id' => 1014, 'translation_key' => 'example.key', 'chain_type' => 'bulk', 'chain_status' => 'active',
    ]);

    $component = new $host;
    $component->timelineChainId = 910;
    $component->datasetIdInput = '1014';
    if ($critical) {
        $component->updatedSelectedCriticalDatasetId('1014');
    } else {
        $component->loadDataset();
    }

    expect($component->selectedCriticalDatasetId)->toBe('1014')
        ->and($component->timelineChainId)->toBe(1014)
        ->and($component->selectedHistoryId)->toBe('1014')
        ->and($component->reloadTick)->toBe(1)
        ->and($component->datasetHistory[0]['id'])->toBe(1014);
    if ($component instanceof TranslationWorkbenchRawDataNew) {
        expect($component->timelineChainPreviewId)->toBe('1014');
        $component->reloadDataset();
        expect($component->reloadTick)->toBe(2)
            ->and($component->timelineChainPreviewId)->toBe('1014');
    }
})->with([false, true])->with([[TwGraphDataDrivenDatasets::class], [TranslationWorkbenchRawDataNew::class]]);

it('keeps the current dataset when the requested ID does not exist', function () {
    Schema::shouldReceive('hasTable')->andReturnTrue();
    $query = Mockery::mock(Builder::class);
    DB::shouldReceive('table')->andReturn($query);
    $query->shouldReceive('where')->with('id', 999999)->andReturnSelf();
    $query->shouldReceive('exists')->andReturnFalse();
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 910;
    $component->datasetIdInput = '999999';
    $component->loadDataset();

    expect($component->timelineChainId)->toBe(910)
        ->and($component->reloadTick)->toBe(0)
        ->and($component->getErrorBag()->has('datasetIdInput'))->toBeTrue();
});

it('rejects invalid dataset IDs before changing the selection', function (string $input) {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 910;
    $component->datasetIdInput = $input;
    expect(fn () => $component->loadDataset())->toThrow(ValidationException::class);
    expect($component->timelineChainId)->toBe(910)->and($component->reloadTick)->toBe(0);
})->with(['', '0', '-1', '1.5', 'abc']);

it('renders the Flux dataset selector with ID input history and reload', function () {
    view()->share('errors', new ViewErrorBag);
    $html = view('translation-workbench::livewire.tw-graph.data-driven.dataset-selector', [
        'mainRow' => ['id' => 910],
        'datasetHistory' => [['id' => 910, 'label' => '#910 · Example']],
        'errors' => new ViewErrorBag,
    ])->render();

    expect($html)->toContain('wire:submit="loadDataset"', 'wire:model="datasetIdInput"', 'wire:click="randomDataset"', 'wire:model.live="selectedHistoryId"', 'wire:click="reloadDataset"', 'wire:model.live="selectedCriticalDatasetId"', '#918', '#1014');
});

it('does not reload when the critical dataset selection is cleared', function () {
    $component = new TwGraphDataDrivenDatasets;
    $component->timelineChainId = 918;
    $component->updatedSelectedCriticalDatasetId(null);

    expect($component->timelineChainId)->toBe(918)
        ->and($component->reloadTick)->toBe(0);
});

it('opens a raw timeline row in the graph tab using the existing dataset selection', function () {
    Schema::shouldReceive('hasTable')->with('translation_workbench_timeline_chains')->andReturnTrue();
    $query = Mockery::mock(Builder::class);
    DB::shouldReceive('table')->with('translation_workbench_timeline_chains')->andReturn($query);
    $query->shouldReceive('where')->with('id', 853)->andReturnSelf();
    $query->shouldReceive('exists')->andReturnTrue();
    $query->shouldReceive('first')->andReturn((object) [
        'id' => 853, 'translation_key' => 'ui.save', 'chain_type' => 'moved', 'chain_status' => 'active',
    ]);
    $host = new TranslationWorkbenchRawDataNew;
    $host->activeTable = 'translation_workbench_timeline_chains';
    $host->showTimelineChainGraph(853);

    expect($host->timelineChainTab)->toBe('tw-graph')
        ->and($host->timelineChainPreviewId)->toBe('853')
        ->and($host->datasetIdInput)->toBe('853')
        ->and($host->selectedCriticalDatasetId)->toBe('853')
        ->and($host->datasetHistory[0]['id'])->toBe(853);
});

it('keeps the raw table visible if a requested timeline row no longer exists', function () {
    Schema::shouldReceive('hasTable')->andReturnTrue();
    $query = Mockery::mock(Builder::class);
    DB::shouldReceive('table')->andReturn($query);
    $query->shouldReceive('where')->with('id', 999999)->andReturnSelf();
    $query->shouldReceive('exists')->andReturnFalse();
    $host = new TranslationWorkbenchRawDataNew;
    $host->activeTable = 'translation_workbench_timeline_chains';
    $host->showTimelineChainGraph(999999);

    expect($host->timelineChainTab)->toBe('raw-data')
        ->and($host->timelineChainPreviewId)->toBe('auto')
        ->and($host->getErrorBag()->has('datasetIdInput'))->toBeTrue();
});
