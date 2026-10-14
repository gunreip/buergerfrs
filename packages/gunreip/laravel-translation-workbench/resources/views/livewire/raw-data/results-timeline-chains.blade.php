<div wire:loading wire:target="loadDataset,randomDataset,reloadDataset,selectedHistoryId,selectedCriticalDatasetId">
    @include('translation-workbench::livewire.tw-graph.data-driven.loading-overlay', ['trackGraphLoading' => false])
</div>

{{-- Dataset selection is shared with the standalone data-driven dataset page. --}}
<flux:callout class="mt-4" color="zinc" icon="flask-conical">
    <flux:callout.heading>{{ __('Timeline chain graph preview dataset') }}</flux:callout.heading>
    @include('translation-workbench::livewire.tw-graph.data-driven.dataset-selector', [
        'mainRow' => $timelineChainId ? ['id' => $timelineChainId] : null,
    ])
</flux:callout>

<livewire:translation-workbench.raw-data-chain-preview
    :preview-id="$timelineChainPreviewId"
    :key="'raw-data-chain-preview-'.$timelineChainPreviewId.'-'.$reloadTick"
    lazy="on-load"
/>
