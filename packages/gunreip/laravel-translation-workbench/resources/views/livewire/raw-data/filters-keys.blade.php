{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-keys.blade.php --}}

<flux:separator text="{{ __('Common Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-10">

    {{-- Row 1 --}}
    {{-- Keys Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.search', [
        'fieldClass' => 'col-span-4',
    ])

    {{-- Keys ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.id', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Keys Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.key-type', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Row 2 --}}
    {{-- Keys UI Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.ui', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Keys Dynamic Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.dynamic', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Keys Dynamic Multi Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.dynamic-multi', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Keys Scope Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.scope', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Keys Status Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.status', [
        'fieldClass' => 'col-span-2',
    ])
</flux:field>

<flux:separator text="{{ __('Segment Filters') }}" />

<flux:field class="mb-4 grid gap-3 md:grid-cols-10">

    {{-- Row 3 --}}
    {{-- Keys Category Filter --}}
    {{-- Keys Namespace Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.namespace', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Keys Group Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.group', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Keys Segment Domain Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.segment-domain', [
        'fieldClass' => 'col-span-4',
    ])

    {{-- Keys Segment Section Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.segment-section', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Keys Segment Context Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.segment-context', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Keys Segment Extra Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.segment-extra', [
        'fieldClass' => 'col-span-4',
    ])

    {{-- Keys Segment Name Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.segment-name', [
        'fieldClass' => 'col-span-3',
    ])

    {{-- Keys Suggested Key Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.suggested-key', [
        'fieldClass' => 'col-span-5',
    ])

    {{-- Dummy --}}
    <flux:field class="col-span-3">
        {{-- Dummy --}}
    </flux:field>

    {{-- Keys Translation Key Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.translation-key', [
        'fieldClass' => 'col-span-5',
    ])
</flux:field>

<flux:separator text="{{ __('Other Filters') }}" />

<flux:field class="mb-4 grid gap-3 md:grid-cols-10">
    {{-- Row 4 --}}
    {{-- Keys Reset Filter --}}
    {{-- Keys Review Status Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.keys.review-status', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Dummy --}}
    <flux:field class="col-span-6">
        {{-- Dummy --}}
    </flux:field>

    {{-- Keys Reset Filter --}}
    <flux:field class="col-span-2 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetKeysFilters"
            />
        </div>
    </flux:field>

</flux:field>

<flux:separator text="{{ __('Build filtered keys') }}" />

<flux:field class="mb-6 mt-4 grid gap-3 md:grid-cols-4">
    {{-- Suggested Key Build --}}
    @include('translation-workbench::livewire.raw-data.filters.build.suggested', [
        'fieldClass' => 'col-span-2',
    ])

    {{-- Translation Key Build --}}
    @include('translation-workbench::livewire.raw-data.filters.build.translation', [
        'fieldClass' => 'col-span-2',
    ])

</flux:field>
