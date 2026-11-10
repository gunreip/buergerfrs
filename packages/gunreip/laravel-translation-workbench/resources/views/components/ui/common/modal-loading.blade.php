{{-- Mount directly inside flux:modal. Explicit targets exclude background polling. --}}
@props(['target', 'text' => __('Loading…')])

<x-ui.loading.lazy-indicator
    :overlay="true"
    role="status"
    :target="$target"
    :text="$text"
    data-workbench-modal-loading
/>
