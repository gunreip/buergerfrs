{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-shared-key-candidates.blade.php --}}

<flux:separator text="{{ __('Shared Key Candidate Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-8">
    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.search', [
        'fieldClass' => 'md:col-span-4',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.finding-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.key-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.matched-key-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.literal', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.status', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.confidence', [
        'fieldClass' => 'md:col-span-2',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.min-review-count', [
        'fieldClass' => 'md:col-span-1',
    ])

    @include('translation-workbench::livewire.raw-data.filters.shared-key-candidates.min-finding-count', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('Translation Key Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-7">
    @include(
        'translation-workbench::livewire.raw-data.filters.shared-key-candidates.current-translation-key',
        [
            'fieldClass' => 'md:col-span-3',
        ]
    )

    @include(
        'translation-workbench::livewire.raw-data.filters.shared-key-candidates.suggested-shared-translation-key',
        [
            'fieldClass' => 'md:col-span-3',
        ]
    )

    <flux:field class="md:col-span-1 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetSharedKeyCandidatesFilters"
            />
        </div>
    </flux:field>
</flux:field>
