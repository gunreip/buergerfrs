{{-- resources/views/components/admin/partials/activity-log/table/⚡active-filter-badges.blade.php --}}

@if ($hasActiveActivityLogFilters)
    @if ($activityLogTab !== 'all')
        <flux:badge
            color="{{ $activeActivityLogTabCard['color'] ?? 'sky' }}"
            variant="subtle"
        >
            {{ __('admin.client_list.table.type') }}: {{ $activeActivityLogTabCard['label'] ?? str($activityLogTab)->headline() }}
        </flux:badge>
    @endif

    @if (trim($search ?? '') !== '')
        <flux:badge
            color="sky"
            variant="subtle"
        >
            {{ __('ui.actions.search') }}: {{ $search }}
        </flux:badge>
    @endif

    @if ($dateRangeFilter !== 'all')
        <flux:badge
            color="emerald"
            variant="subtle"
        >
            {{ __('Timespan') }}: {{ $activeDateRangeCard['label'] ?? $dateRangeFilter }}
        </flux:badge>
    @endif

    @if ($dateTimeFrom !== '')
        <flux:badge
            color="emerald"
            variant="subtle"
        >
            {{ __('From') }}: {{ str_replace('T', ' ', $dateTimeFrom) }}
        </flux:badge>
    @endif

    @if ($dateTimeTo !== '')
        <flux:badge
            color="emerald"
            variant="subtle"
        >
            {{ __('To') }}: {{ str_replace('T', ' ', $dateTimeTo) }}
        </flux:badge>
    @endif

    @if ($logNameFilter !== 'all')
        <flux:badge
            color="blue"
            variant="subtle"
        >
            {{ __('Log') }}: {{ $logNameFilter }}
        </flux:badge>
    @endif

    @if ($eventFilter !== 'all')
        <flux:badge
            color="purple"
            variant="subtle"
        >
            {{ __('admin.translation_list.modal_history.event') }}: {{ $eventFilter }}
        </flux:badge>
    @endif

    @if ($subjectTypeFilter !== 'all')
        <flux:badge
            color="amber"
            variant="subtle"
        >
            {{ __('Subject') }}: {{ class_basename($subjectTypeFilter) }}
        </flux:badge>
    @endif

    @if ($causerTypeFilter !== 'all')
        <flux:badge
            color="green"
            variant="subtle"
        >
            {{ __('Causer') }}: {{ class_basename($causerTypeFilter) }}
        </flux:badge>
    @endif
@endif
