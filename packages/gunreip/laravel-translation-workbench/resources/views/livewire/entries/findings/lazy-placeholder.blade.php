{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/lazy-placeholder.blade.php --}}

@php
    $label = (string) ($label ?? __('Tab content'));
@endphp

<div class="mt-4">
    <x-ui.loading.lazy-indicator
        target="findingsActiveTab"
        text="{{ __('Loading :label...', ['label' => $label]) }}"
    />
</div>
