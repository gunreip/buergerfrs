<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('keeps the data driven datasets component stable when timeline tables are unavailable', function (): void {
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_timeline_chains')
        ->andReturnFalse();

    $component = new TwGraphDataDrivenDatasets();

    $component->mount();
    $component->randomDataset();
    $component->reloadDataset();
    $component->updatedSelectedHistoryId('not-a-valid-id');

    $view = $component->render();
    $html = $view->render();

    expect($component->timelineChainId)->toBeNull()
        ->and($component->selectedHistoryId)->toBeNull()
        ->and($component->datasetHistory)->toBe([])
        ->and($component->reloadTick)->toBe(2)
        ->and($html)->toContain('No timeline-chain datasets found')
        ->and($html)->toContain('Run the timeline-chain collector first')
        ->and($html)->toContain('Random dataset')
        ->and($html)->not->toContain('Reload ID #');
});

it('renders the data driven datasets page as a livewire component host', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.data-driven.datasets',
    ));

    expect($source)
        ->toContain('<livewire:translation-workbench.tw-graph.data-driven.datasets />');
});

it('keeps reload random and history controls wired in the datasets component view', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.tw-graph.data-driven.datasets',
    ));

    expect($source)
        ->toContain('wire:click="reloadDataset"')
        ->toContain("{{ __('Reload ID #:id', ['id' => \$mainRow['id']]) }}")
        ->toContain('wire:click="randomDataset"')
        ->toContain("{{ __('Random dataset') }}")
        ->toContain('wire:model.live="selectedHistoryId"')
        ->toContain('@foreach ($datasetHistory as $historyEntry)')
        ->toContain('{{ $historyEntry[\'label\'] }}');
});

it('selects a random timeline chain on mount and remembers it in dataset history', function (): void {
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_timeline_chains')
        ->andReturnTrue()
        ->byDefault();

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_chains')
        ->once()
        ->ordered()
        ->andReturn(new class {
            public function inRandomOrder(): self
            {
                return $this;
            }

            public function value(string $column): int
            {
                expect($column)->toBe('id');

                return 42;
            }
        });

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_chains')
        ->once()
        ->ordered()
        ->andReturn(new class {
            public function where(string $column, int $id): self
            {
                expect($column)->toBe('id')
                    ->and($id)->toBe(42);

                return $this;
            }

            public function first(array $columns): object
            {
                expect($columns)->toBe(['id', 'translation_key', 'chain_type', 'chain_status']);

                return (object) [
                    'id' => 42,
                    'translation_key' => 'ui.random.sample',
                    'chain_type' => 'bulk',
                    'chain_status' => 'active',
                ];
            }
        });

    $component = new TwGraphDataDrivenDatasets();

    $component->mount();

    expect($component->timelineChainId)->toBe(42)
        ->and($component->selectedHistoryId)->toBe('42')
        ->and($component->datasetHistory)->toBe([
            [
                'id' => 42,
                'label' => '#42 · Bulk · Active · ui.random.sample',
            ],
        ])
        ->and($component->reloadTick)->toBe(0);
});

it('keeps only the latest five dataset history entries and moves repeated selections to the front', function (): void {
    $rows = [];

    foreach (range(1, 6) as $id) {
        $rows[$id] = (object) [
            'id' => $id,
            'translation_key' => 'ui.sample.' . $id,
            'chain_type' => $id % 2 === 0 ? 'bulk' : 'single',
            'chain_status' => $id % 2 === 0 ? 'active' : 'inactive',
        ];
    }

    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_timeline_chains')
        ->andReturnTrue()
        ->byDefault();

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_chains')
        ->andReturnUsing(static fn() => new class($rows) {
            private int $id = 0;

            public function __construct(private readonly array $rows) {}

            public function where(string $column, int $id): self
            {
                expect($column)->toBe('id');

                $this->id = $id;

                return $this;
            }

            public function first(array $columns): ?object
            {
                expect($columns)->toBe(['id', 'translation_key', 'chain_type', 'chain_status']);

                return $this->rows[$this->id] ?? null;
            }
        });

    $component = new TwGraphDataDrivenDatasets();

    foreach (range(1, 6) as $id) {
        $component->updatedSelectedHistoryId((string) $id);
    }

    expect(array_column($component->datasetHistory, 'id'))->toBe([6, 5, 4, 3, 2])
        ->and($component->selectedHistoryId)->toBe('6')
        ->and($component->reloadTick)->toBe(6)
        ->and($component->datasetHistory[0]['label'])->toBe('#6 · Bulk · Active · ui.sample.6');

    $component->updatedSelectedHistoryId('4');
    $component->reloadDataset();

    expect(array_column($component->datasetHistory, 'id'))->toBe([4, 6, 5, 3, 2])
        ->and($component->selectedHistoryId)->toBe('4')
        ->and($component->reloadTick)->toBe(8)
        ->and($component->datasetHistory[0]['label'])->toBe('#4 · Bulk · Active · ui.sample.4');
});

it('ignores invalid history selections without changing the current dataset state', function (): void {
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_timeline_chains')
        ->andReturnTrue()
        ->byDefault();

    $component = new TwGraphDataDrivenDatasets();
    $component->timelineChainId = 42;
    $component->selectedHistoryId = '42';
    $component->reloadTick = 3;
    $component->datasetHistory = [
        [
            'id' => 42,
            'label' => '#42 · Bulk · Active · ui.random.sample',
        ],
    ];

    $component->updatedSelectedHistoryId('');
    $component->updatedSelectedHistoryId('0');
    $component->updatedSelectedHistoryId('not-a-number');

    expect($component->timelineChainId)->toBe(42)
        ->and($component->selectedHistoryId)->toBe('42')
        ->and($component->reloadTick)->toBe(3)
        ->and($component->datasetHistory)->toBe([
            [
                'id' => 42,
                'label' => '#42 · Bulk · Active · ui.random.sample',
            ],
        ]);
});

it('keeps random dataset reload deterministic when no random row exists', function (): void {
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_timeline_chains')
        ->andReturnTrue()
        ->byDefault();

    DB::shouldReceive('table')
        ->with('translation_workbench_timeline_chains')
        ->once()
        ->andReturn(new class {
            public function inRandomOrder(): self
            {
                return $this;
            }

            public function value(string $column): null
            {
                expect($column)->toBe('id');

                return null;
            }
        });

    $component = new TwGraphDataDrivenDatasets();
    $component->timelineChainId = 42;
    $component->selectedHistoryId = '42';
    $component->datasetHistory = [
        [
            'id' => 42,
            'label' => '#42 · Bulk · Active · ui.random.sample',
        ],
    ];

    $component->randomDataset();

    expect($component->timelineChainId)->toBeNull()
        ->and($component->selectedHistoryId)->toBeNull()
        ->and($component->reloadTick)->toBe(1)
        ->and($component->datasetHistory)->toBe([
            [
                'id' => 42,
                'label' => '#42 · Bulk · Active · ui.random.sample',
            ],
        ]);
});

it('renders the datasets data driven graph preview with dev details disabled by default', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::livewire.tw-graph.data-driven.datasets',
    ));

    expect($source)
        ->toContain("'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven'")
        ->toContain("'dev' => false")
        ->toContain("'coordinates' => false");
});

it('registers the data driven datasets livewire alias through the package provider', function (): void {
    expect(Livewire::exists('translation-workbench.tw-graph.data-driven.datasets'))->toBeTrue()
        ->and(app('livewire.factory')->resolveComponentClass('translation-workbench.tw-graph.data-driven.datasets'))
        ->toBe(TwGraphDataDrivenDatasets::class);
});
