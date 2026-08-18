{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview.blade.php --}}

{{--
    TODO TW-GRAPH cleanup:
    Old graph-preview DEV files were moved to
    livewire/raw-data/timeline-chains/old/graph-preview.
    Keep these includes inactive until graph-v2 protocol validation is done.

    @include('translation-workbench::livewire.raw-data.timeline-chains.old.graph-preview.data-trunk')
    @include('translation-workbench::livewire.raw-data.timeline-chains.old.graph-preview.data-merge')
    @include('translation-workbench::livewire.raw-data.timeline-chains.old.graph-preview.graph-v0')
    @include('translation-workbench::livewire.raw-data.timeline-chains.old.graph-preview.graph-v1')
--}}
@include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2')
{{--
    TODO TW-GRAPH cleanup:
    @include('translation-workbench::livewire.raw-data.timeline-chains.old.graph-preview.data-samples')
--}}
