{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-dynamic-sources.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.search', [
        'fieldClass' => 'md:col-span-3',
    ])

    <flux:field class="md:col-span-3 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetDynamicSourcesFilters"
            />
        </div>
    </flux:field>
</flux:field>

<flux:separator text="{{ __('ID filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.key', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.finding', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.discovery', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('Classification filters') }}" />

<flux:field class="mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.classification', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.cardinality', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.origin', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.source-type', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.confidence', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-sources.status', [
        'fieldClass' => 'md:col-span-1',
    ])

</flux:field>
