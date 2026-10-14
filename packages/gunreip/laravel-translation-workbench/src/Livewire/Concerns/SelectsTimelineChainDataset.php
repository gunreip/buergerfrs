<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait SelectsTimelineChainDataset
{
    public ?string $selectedCriticalDatasetId = null;

    public ?int $timelineChainId = null;

    /**
     * @var array<int, array{id: int, label: string}>
     */
    public array $datasetHistory = [];

    public ?string $selectedHistoryId = null;

    public string $datasetIdInput = '';

    public int $reloadTick = 0;

    protected function initializeDatasetSelection(): void
    {
        $this->timelineChainId ??= $this->randomTimelineChainId();
        $this->selectedHistoryId = $this->timelineChainId !== null ? (string) $this->timelineChainId : null;
        $this->datasetIdInput = $this->selectedHistoryId ?? '';
        $this->selectedCriticalDatasetId = isset(self::CRITICAL_DATASETS[$this->timelineChainId ?? 0])
            ? (string) $this->timelineChainId : null;
        $this->resetValidation('datasetIdInput');

        if ($this->timelineChainId !== null) {
            $this->rememberDatasetId($this->timelineChainId);
            $this->datasetSelectionChanged();
        }
    }

    public function randomDataset(): void
    {
        $this->timelineChainId = $this->randomTimelineChainId();
        $this->selectedHistoryId = $this->timelineChainId !== null ? (string) $this->timelineChainId : null;
        $this->datasetIdInput = $this->selectedHistoryId ?? '';
        $this->selectedCriticalDatasetId = isset(self::CRITICAL_DATASETS[$this->timelineChainId ?? 0])
            ? (string) $this->timelineChainId : null;
        $this->resetValidation('datasetIdInput');
        $this->reloadTick++;

        if ($this->timelineChainId !== null) {
            $this->rememberDatasetId($this->timelineChainId);
            $this->datasetSelectionChanged();
        }
    }

    public function loadDataset(): void
    {
        $this->validate(
            ['datasetIdInput' => ['required', 'integer', 'min:1']],
            [],
            ['datasetIdInput' => __('Dataset ID')],
        );

        $id = (int) $this->datasetIdInput;
        if (! Schema::hasTable('translation_workbench_timeline_chains')
            || ! DB::table('translation_workbench_timeline_chains')->where('id', $id)->exists()) {
            $this->addError('datasetIdInput', __('No dataset found for ID #:id.', ['id' => $id]));

            return;
        }

        $this->updatedSelectedHistoryId($id);
    }

    public function updatedSelectedCriticalDatasetId(mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->datasetIdInput = (string) $value;
        $this->loadDataset();
        $this->selectedCriticalDatasetId = isset(self::CRITICAL_DATASETS[$this->timelineChainId ?? 0])
            ? (string) $this->timelineChainId : null;
    }

    public function reloadDataset(): void
    {
        $this->reloadTick++;

        if ($this->timelineChainId !== null) {
            $this->rememberDatasetId($this->timelineChainId);
            $this->datasetSelectionChanged();
        }
    }

    public function updatedSelectedHistoryId(mixed $value): void
    {
        $id = (int) $value;

        if ($id > 0) {
            $this->timelineChainId = $id;
            $this->selectedHistoryId = (string) $id;
            $this->datasetIdInput = (string) $id;
            $this->selectedCriticalDatasetId = isset(self::CRITICAL_DATASETS[$id]) ? (string) $id : null;
            $this->resetValidation('datasetIdInput');
            $this->reloadTick++;
            $this->rememberDatasetId($id);
            $this->datasetSelectionChanged();
        }
    }

    private function randomTimelineChainId(): ?int
    {
        if (! Schema::hasTable('translation_workbench_timeline_chains')) {
            return null;
        }

        $id = DB::table('translation_workbench_timeline_chains')
            ->inRandomOrder()
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    private function rememberDatasetId(int $id): void
    {
        if (! Schema::hasTable('translation_workbench_timeline_chains')) {
            return;
        }

        $row = DB::table('translation_workbench_timeline_chains')
            ->where('id', $id)
            ->first(['id', 'translation_key', 'chain_type', 'chain_status']);

        if (! $row) {
            return;
        }

        $label = '#'.$id.' · '
            .str((string) $row->chain_type)->headline().' · '
            .str((string) $row->chain_status)->headline().' · '
            .(string) $row->translation_key;

        $this->datasetHistory = collect($this->datasetHistory)
            ->reject(static fn (array $entry): bool => (int) ($entry['id'] ?? 0) === $id)
            ->prepend([
                'id' => $id,
                'label' => $label,
            ])
            ->take(5)
            ->values()
            ->all();
    }

    protected function datasetSelectionChanged(): void
    {
        // Hosts may synchronize a separately lazy-loaded graph.
    }
}
