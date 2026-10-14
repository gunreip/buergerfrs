{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview.blade.php --}}

{{-- @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-authoring') --}}
{{-- Keep the supplementary callouts available on the standalone dataset page. --}}
@include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.tw-graph-data-driven', ['showSupplementaryCallouts' => false, 'enableMeasuredLabelLayout' => true])
