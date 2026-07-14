{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/summary.blade.php --}}

<flux:field class="grid gap-4 xl:grid-cols-3">
    @include('translation-workbench::livewire.entries.summary-card', [
        'title' => __('Finding kinds'),
        'icon' => 'scan-search',
        'rows' => $findingKindCounts,
    ])

    @include('translation-workbench::livewire.entries.summary-card', [
        'title' => __('Key types'),
        'icon' => 'key-round',
        'rows' => $keyTypeCounts,
    ])

    @include('translation-workbench::livewire.entries.summary-card', [
        'title' => __('Locale roles'),
        'icon' => 'languages',
        'rows' => $localeRoleCounts,
    ])

    @include('translation-workbench::livewire.entries.summary-card', [
        'title' => __('Locales'),
        'icon' => 'globe',
        'rows' => $localeCounts,
    ])

    @include('translation-workbench::livewire.entries.summary-card', [
        'title' => __('Timeline events'),
        'icon' => 'activity',
        'rows' => $timelineEventCounts,
    ])
</flux:field>
