{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/results-timeline-chains.blade.php --}}

@php
    $sampleRows = collect($timelineChainSampleRows ?? []);
    $mainRow = $timelineChainMainRow ?? null;
    $rootRows = collect($timelineChainRootRows ?? []);
    $originRows = collect($timelineChainOriginRows ?? []);
@endphp

@if ($mainRow)
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

    @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-root')
    @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-roots')
    @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview')
    @include('translation-workbench::livewire.raw-data.timeline-chains.canonical-focus')
@endif

@include('translation-workbench::livewire.raw-data.timeline-chains.samples')
