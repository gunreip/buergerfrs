<div data-raw-data-chain-preview>
@if ($mainRow)
    @include('translation-workbench::livewire.tw-graph.data-driven.loading-overlay')
    @php
        $mainRelatedKeys = collect($mainRow['related_translation_keys'] ?? [])
            ->filter()
            ->reject(static fn($key): bool => (string) $key === (string) ($mainRow['translation_key'] ?? ''))
            ->values();
        $mainEventSummary = collect($mainRow['timeline_event_summary'] ?? [])
            ->sortDesc()
            ->take(6);
        $mainLangValueSummary = collect($mainRow['lang_value_summary'] ?? []);
    @endphp

    {{-- @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-root') --}}
    {{-- @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-roots') --}}
    @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview')
    {{-- @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-focus') --}}
    <livewire:translation-workbench.tw-graph.data-driven.dataset-details
        :dataset-id="(int) $mainRow['id']"
        :key="'dataset-details-'.$mainRow['id']"
    />
@endif

{{-- @include('translation-workbench::livewire.raw-data.timeline-chains.samples') --}}

</div>
