<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

it('loads an existing dataset by ID or critical selection and records it in the history', function (bool $critical) {
    Schema::shouldReceive('hasTable')->with('translation_workbench_timeline_chains')->andReturnTrue();
    $query = Mockery::mock(\Illuminate\Database\Query\Builder::class);
    DB::shouldReceive('table')->with('translation_workbench_timeline_chains')->andReturn($query);
    $query->shouldReceive('where')->with('id', 1014)->andReturnSelf();
    $query->shouldReceive('exists')->once()->andReturnTrue();
    $query->shouldReceive('first')->once()->andReturn((object) [
        'id' => 1014, 'translation_key' => 'example.key', 'chain_type' => 'bulk', 'chain_status' => 'active',
    ]);

    $component = new TwGraphDataDrivenDatasets;
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
})->with([false, true]);

it('keeps the current dataset when the requested ID does not exist', function () {
    Schema::shouldReceive('hasTable')->andReturnTrue();
    $query = Mockery::mock(\Illuminate\Database\Query\Builder::class);
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
    view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    $html = view('translation-workbench::livewire.tw-graph.data-driven.dataset-selector', [
        'mainRow' => ['id' => 910],
        'datasetHistory' => [['id' => 910, 'label' => '#910 · Example']],
        'errors' => new \Illuminate\Support\ViewErrorBag,
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
