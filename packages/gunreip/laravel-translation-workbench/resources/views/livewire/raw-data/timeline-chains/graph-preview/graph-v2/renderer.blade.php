{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/renderer.blade.php --}}

{{-- Active graph protocol renderer. --}}
<x-ui.tw-graph-protocol
    class="mb-14 mt-12 pb-12"
    graph-id="tw-graph-v2-path-protocol-preview"
    :protocol="data_get($graphV2, 'protocol', [])"
    :direction="data_get($graphV2, 'trunkDirection', 'bottom-top')"
    :dev="true"
/>
