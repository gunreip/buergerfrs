{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-event-types.blade.php --}}

<flux:separator text="{{ __('Common filters') }}" />

{{-- grid-cols-7 keeps the reset button aligned at the far right. --}}
<flux:field class="mt-2 grid gap-3 md:grid-cols-7">
    {{-- Event Types Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.event-types.search', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Event Types ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.event-types.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Event Types Key Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.event-types.key', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Event Types Label Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.event-types.label', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Event Types Category Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.event-types.category', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Event Types Severity Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.event-types.severity', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Event Types Reset Filter --}}
    <flux:field class="md:col-span-3 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetEventTypesFilters"
            />
        </div>
    </flux:field>
</flux:field>
