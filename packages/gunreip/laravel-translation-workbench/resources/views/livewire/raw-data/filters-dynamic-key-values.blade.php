{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-dynamic-key-values.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.search', [
        'fieldClass' => 'md:col-span-3',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.key', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.status', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.locale', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.source', [
        'fieldClass' => 'md:col-span-2',
    ])
    {{-- </flux:field> --}}

    {{-- <flux:field class="mb-4 grid gap-3 md:grid-cols-6"> --}}
    @include('translation-workbench::livewire.raw-data.filters.dynamic-key-values.value-key', [
        'fieldClass' => 'md:col-span-2',
    ])

    <flux:field class="md:col-span-4 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetDynamicKeyValuesFilters"
            />
        </div>
    </flux:field>
</flux:field>
