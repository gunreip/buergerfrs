{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-dynamic-source-candidates.blade.php --}}

<flux:separator text="{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.search', [
        'fieldClass' => 'md:col-span-3',
    ])
</flux:field>

<flux:separator text="{{ __('ID filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.dynamic-source', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.key', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.finding', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('Review filters') }}" />

<flux:field class="mt-2 grid gap-3 md:grid-cols-6">
    @include(
        'translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.candidate-source-type',
        [
            'fieldClass' => 'md:col-span-2',
        ]
    )

    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.confidence', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.review-status', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.dynamic-source-candidates.status', [
        'fieldClass' => 'md:col-span-1',
    ])

    <flux:field class="md:col-span-1 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetDynamicSourceCandidatesFilters"
            />
        </div>
    </flux:field>
</flux:field>
