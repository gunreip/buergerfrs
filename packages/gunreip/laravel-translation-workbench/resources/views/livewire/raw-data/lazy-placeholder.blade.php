{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/lazy-placeholder.blade.php --}}

@php
    $tableName = (string) ($tableName ?? __('Raw data table'));
@endphp

<div class="mt-4">
    <x-ui.loading.lazy-indicator
        target="activeTable"
        text="{{ __('Loading :table...', ['table' => $tableName]) }}"
    />
</div>
