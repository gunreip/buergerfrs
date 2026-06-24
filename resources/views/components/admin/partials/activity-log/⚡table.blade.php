{{-- resources/views/components/admin/partials/activity-log/⚡table.blade.php --}}

@php
    $activityLogs = $activityLogs ?? null;

    $jsonHasData = static function (mixed $value): bool {
        if ($value === null) {
            return false;
        }

        if (is_array($value)) {
            return $value !== [];
        }

        $stringValue = trim((string) $value);

        if ($stringValue === '' || $stringValue === '[]' || $stringValue === '{}' || $stringValue === 'null') {
            return false;
        }

        $decoded = json_decode($stringValue, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return true;
        }

        return is_array($decoded) ? $decoded !== [] : $decoded !== null;
    };

    $currentPageActivityLogs = collect($activityLogs?->items() ?? []);
    $tableEntryCreatedAtValues = $currentPageActivityLogs
        ->pluck('created_at')
        ->filter()
        ->sortBy(
            static fn(mixed $value): string => $value instanceof \DateTimeInterface
                ? $value->format('Y-m-d H:i:s.u')
                : (string) $value,
        )
        ->values();

    $tableEntryFirstCreatedAt = $tableEntryCreatedAtValues->first();
    $tableEntryLastCreatedAt = $tableEntryCreatedAtValues->last();

    $dateRangeFilter = $dateRangeFilter ?? 'all';
    $dateTimeFrom = trim((string) ($dateTimeFrom ?? ''));
    $dateTimeTo = trim((string) ($dateTimeTo ?? ''));
    $timeFrom = trim((string) ($timeFrom ?? ''));
    $timeTo = trim((string) ($timeTo ?? ''));
    $datePickerMinDate = $datePickerMinDate ?? null;
    $datePickerMaxDate = $datePickerMaxDate ?? null;
    $timePickersDisabled = (bool) ($timePickersDisabled ?? true);
    $timeToDisabled = (bool) ($timeToDisabled ?? true);
    $timeToMinTime = trim((string) ($timeToMinTime ?? '00:00'));
    $timeFromInterval = (int) ($timeFromInterval ?? 30);
    $timeToInterval = (int) ($timeToInterval ?? 30);
    $logNameFilter = $logNameFilter ?? 'all';
    $eventFilter = $eventFilter ?? 'all';
    $subjectTypeFilter = $subjectTypeFilter ?? 'all';
    $causerTypeFilter = $causerTypeFilter ?? 'all';
    $dateRangeCards = $dateRangeCards ?? [];
    $activityLogTab = $activityLogTab ?? 'all';
    $activityLogTabCards = $activityLogTabCards ?? [];

    $activeDateRangeCard = collect($dateRangeCards)->firstWhere('key', $dateRangeFilter);
    $activeActivityLogTabCard = collect($activityLogTabCards)->firstWhere('key', $activityLogTab);

    $hasActiveActivityLogFilters =
        trim($search ?? '') !== '' ||
        $activityLogTab !== 'all' ||
        $dateRangeFilter !== 'all' ||
        $dateTimeFrom !== '' ||
        $dateTimeTo !== '' ||
        $logNameFilter !== 'all' ||
        $eventFilter !== 'all' ||
        $subjectTypeFilter !== 'all' ||
        $causerTypeFilter !== 'all';
@endphp

<flux:card class="mt-6">

    <div class="flex w-full items-start justify-between gap-3">
        <div class="min-w-0 flex-1">

            <x-ui.headers.card
                class="mb-3"
                :title="__('Activity log entries')"
                :description="__(
                    'Basic activity_log information. Detailed properties and attribute changes will be shown in a modal in a later step.',
                )"
            >
                {{-- Activity Log Date/Time Range Filters --}}
                @include('components.admin.partials.activity-log.table.⚡date-time-controls')
            </x-ui.headers.card>

        </div>
    </div>

    {{-- Activity Log Pagination Top --}}
    @include('components.admin.partials.activity-log.table.⚡pagination-meta')

    {{-- Activity Log Tabs --}}
    @if (count($activityLogTabCards) > 0)
        <flux:tabs
            class="my-3 px-4"
            wire:model.live="activityLogTab"
        >
            @foreach ($activityLogTabCards as $activityLogTabCard)
                <flux:tab
                    name="{{ $activityLogTabCard['key'] }}"
                    icon="{{ $activityLogTabCard['icon'] ?? 'circle-stack' }}"
                >
                    <span class="inline-flex items-center gap-2">
                        <span>{{ $activityLogTabCard['label'] }}</span>

                        <flux:badge
                            size="sm"
                            variant="subtle"
                            color="{{ $activityLogTabCard['color'] ?? 'zinc' }}"
                        >
                            {{ number_format((int) $activityLogTabCard['count']) }}
                        </flux:badge>
                    </span>
                </flux:tab>
            @endforeach
        </flux:tabs>
    @endif

    {{-- Activity Log Table Content (varies based on selected tab: All, App, Artisan, System) --}}
    @include(match ($activityLogTab) {
            'app' => 'components.admin.partials.activity-log.table.⚡app',
            'artisan' => 'components.admin.partials.activity-log.table.⚡artisan',
            'system' => 'components.admin.partials.activity-log.table.⚡system',
            default => 'components.admin.partials.activity-log.table.⚡all',
        })

    {{-- Activity Log Pagination Bottom --}}
    @if ($activityLogs?->hasPages())
        {{-- Pagination --}}
        <div class="mt-2">
            <x-ui.table.pagination
                :paginator="$activityLogs"
                scroll-to="#activity-log-table"
            />
        </div>
    @endif

</flux:card>
