{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters-timeline-events.blade.php --}}

<flux:separator text="{{ __('Timeline Event Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    {{-- Timeline Events Search Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.search', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Timeline Events ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Timeline Events Event Type Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.event-type', [
        'fieldClass' => 'md:col-span-2',
    ])
</flux:field>

<flux:separator text="{{ __('ID Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-6">
    {{-- Timeline Events Key ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.key-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Timeline Events Finding ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.finding-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Timeline Events Review ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.review-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Timeline Events Created By User ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.created-by-user-id', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Timeline Events Event Type ID Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.event-type-id', [
        'fieldClass' => 'md:col-span-1',
    ])
</flux:field>

<flux:separator text="{{ __('Date/Time Filters') }}" />

<flux:field class="mb-4 mt-2 grid gap-3 md:grid-cols-8">
    {{-- Timeline Events Created Range Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.created-range', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Timeline Events Changed Range Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.changed-range', [
        'fieldClass' => 'md:col-span-3',
    ])

    {{-- Timeline Events Time Span Filter --}}
    @include('translation-workbench::livewire.raw-data.filters.timeline-events.time-span', [
        'fieldClass' => 'md:col-span-1',
    ])

    {{-- Timeline Events Reset Filter --}}
    <flux:field class="md:col-span-1 md:justify-self-end">
        <div class="flex h-full items-end justify-end pt-6">
            <x-ui.button.reset
                label="{{ __('Reset') }}"
                wire:click="resetTimelineEventsFilters"
            />
        </div>
    </flux:field>
</flux:field>
