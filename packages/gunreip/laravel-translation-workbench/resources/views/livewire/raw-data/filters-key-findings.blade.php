{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-key-findings.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    {{-- Key Findings Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.key-findings.search', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Key Findings Relation Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.key-findings.relation-type', [
        'fieldClass' => 'md:col-span-2',
    ])

    {{-- Key Findings Status Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.key-findings.status', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('ID Filters') }}" />

<flux:field class="mt-2 grid gap-3 md:grid-cols-6">
    {{-- Key Findings ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.key-findings.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Key Findings Key Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.key-findings.key', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Key Findings Finding Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.key-findings.finding', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Key Findings Reset Filter --}}
    <flux:field class="md:col-span-3 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetKeyFindingsFilters"
            />
        </div>
    </flux:field>
</flux:field>
