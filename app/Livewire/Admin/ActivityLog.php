<?php

// app/Livewire/Admin/ActivityLog.php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $logNameFilter = 'all';

    public string $eventFilter = 'all';

    public string $subjectTypeFilter = 'all';

    public string $causerTypeFilter = 'all';

    public string $dateRangeFilter = 'all';

    public string $activityLogTab = 'all';

    /**
     * @var array{start: string|null, end: string|null, preset?: string|null}
     */
    public array $customDateRange = [
        'start' => null,
        'end' => null,
        'preset' => null,
    ];

    public ?string $timeFrom = null;

    public ?string $timeTo = null;

    public int $timeFromInterval = 30;

    public int $timeToInterval = 30;

    public int $perPage = 25;

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public bool $activityLogModalOpen = false;

    public ?int $selectedActivityLogId = null;

    public ?int $nextActivityLogId = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $selectedActivityLog = null;

    /**
     * Render the activity log overview page.
     */
    public function render(): View
    {
        return view('components.admin.⚡activity-log', [
            'activityLogs' => $this->activityLogQuery()->paginate($this->perPage),
            'summary' => $this->summary(),
            'search' => $this->search,
            'logNameFilter' => $this->logNameFilter,
            'eventFilter' => $this->eventFilter,
            'subjectTypeFilter' => $this->subjectTypeFilter,
            'causerTypeFilter' => $this->causerTypeFilter,
            'dateRangeFilter' => $this->dateRangeFilter,
            'activityLogTab' => $this->activityLogTab,
            'activityLogTabCards' => $this->activityLogTabCards(),
            'customDateRange' => $this->customDateRange,
            'timeFrom' => $this->timeFrom,
            'timeTo' => $this->timeTo,
            'dateTimeFrom' => $this->dateTimeFilterBoundary('from')?->format('Y-m-d H:i'),
            'dateTimeTo' => $this->dateTimeFilterBoundary('to')?->format('Y-m-d H:i'),
            'datePickerMinDate' => $this->datePickerMinDate(),
            'datePickerMaxDate' => $this->datePickerMaxDate(),
            'timePickersDisabled' => ! $this->hasCompleteCustomDateRange(),
            'timeToDisabled' => ! $this->hasCompleteCustomDateRange()
                || $this->normalizedTimeFilter($this->timeFrom) === null,
            'timeFromInterval' => $this->timeFromIntervalMinutes(),
            'timeToInterval' => $this->timeToIntervalMinutes(),
            'timeToMinTime' => $this->timeToMinTime(),
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'dateRangeCards' => $this->dateRangeCards(),
            'logNameCards' => $this->logNameCards(),
            'logNameOptions' => $this->distinctOptions('log_name'),
            'eventOptions' => $this->distinctOptions('event'),
            'subjectTypeOptions' => $this->distinctOptions('subject_type'),
            'causerTypeOptions' => $this->distinctOptions('causer_type'),
            'activityLogModalOpen' => $this->activityLogModalOpen,
            'selectedActivityLogId' => $this->selectedActivityLogId,
            'nextActivityLogId' => $this->nextActivityLogId,
            'selectedActivityLog' => $this->selectedActivityLog,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLogNameFilter(): void
    {
        $this->resetPage();
    }

    public function updatedEventFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSubjectTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCauserTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateRangeFilter(): void
    {
        if (! in_array($this->dateRangeFilter, $this->allowedDateRangeFilters(), true)) {
            $this->dateRangeFilter = 'all';
        }

        $this->resetPage();
    }

    public function updatedActivityLogTab(): void
    {
        if (! in_array($this->activityLogTab, $this->allowedActivityLogTabs(), true)) {
            $this->activityLogTab = 'all';
        }

        $this->resetPage();
    }

    public function updatedCustomDateRange(): void
    {
        if (! $this->hasCompleteCustomDateRange()) {
            $this->timeFrom = null;
            $this->timeTo = null;
        }

        $this->resetPage();
    }

    public function updatedTimeFrom(): void
    {
        $timeFrom = $this->normalizedTimeFilter($this->timeFrom);

        $this->timeFrom = $timeFrom;

        if ($timeFrom === null) {
            $this->timeTo = null;
            $this->resetPage();

            return;
        }

        $timeTo = $this->normalizedTimeFilter($this->timeTo);

        if ($timeTo !== null && $timeTo < $this->timeToMinTime()) {
            $this->timeTo = null;
        }

        $this->resetPage();
    }

    public function updatedTimeTo(): void
    {
        $timeFrom = $this->normalizedTimeFilter($this->timeFrom);
        $timeTo = $this->normalizedTimeFilter($this->timeTo);

        $this->timeTo = $timeTo;

        if ($timeFrom === null || ($timeTo !== null && $timeTo < $this->timeToMinTime())) {
            $this->timeTo = null;
        }

        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $allowedPerPageValues = [10, 25, 50, 100];

        if (! in_array((int) $this->perPage, $allowedPerPageValues, true)) {
            $this->perPage = 25;
        }

        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->allowedSortFields(), true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = $field === 'id' ? 'desc' : 'asc';
        }

        $this->resetPage();
    }

    public function setDateRangeFilter(string $dateRangeFilter): void
    {
        if (! in_array($dateRangeFilter, $this->allowedDateRangeFilters(), true)) {
            return;
        }

        if ($this->dateRangeFilter === $dateRangeFilter) {
            return;
        }

        $this->dateRangeFilter = $dateRangeFilter;
        $this->resetPage();
    }

    public function setLogNameFilter(string $logNameFilter): void
    {
        $logNameFilter = trim($logNameFilter);

        if ($logNameFilter === '') {
            $logNameFilter = 'all';
        }

        if ($this->logNameFilter === $logNameFilter) {
            return;
        }

        $this->logNameFilter = $logNameFilter;
        $this->resetPage();
    }

    public function setActivityLogTab(string $activityLogTab): void
    {
        if (! in_array($activityLogTab, $this->allowedActivityLogTabs(), true)) {
            return;
        }

        if ($this->activityLogTab === $activityLogTab) {
            return;
        }

        $this->activityLogTab = $activityLogTab;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->logNameFilter = 'all';
        $this->eventFilter = 'all';
        $this->subjectTypeFilter = 'all';
        $this->causerTypeFilter = 'all';
        $this->dateRangeFilter = 'all';
        $this->customDateRange = [
            'start' => null,
            'end' => null,
            'preset' => null,
        ];
        $this->timeFrom = null;
        $this->timeTo = null;
        $this->perPage = 25;
        $this->sortField = 'id';
        $this->sortDirection = 'desc';

        $this->resetPage();
    }

    public function openActivityLogModal(int $activityLogId): void
    {
        $activityLog = DB::table('activity_log')
            ->where('id', $activityLogId)
            ->first();

        if (! $activityLog) {
            $this->closeActivityLogModal();

            return;
        }

        $this->selectedActivityLogId = $activityLogId;
        $this->nextActivityLogId = $this->nextActivityLogIdFromCurrentTablePage($activityLogId);
        $this->selectedActivityLog = $this->normalizeActivityLogDetail($activityLog);
        $this->activityLogModalOpen = true;
    }

    public function closeActivityLogModal(): void
    {
        $this->activityLogModalOpen = false;
        $this->selectedActivityLogId = null;
        $this->nextActivityLogId = null;
        $this->selectedActivityLog = null;
    }

    public function openNextActivityLogFromList(): void
    {
        if ($this->nextActivityLogId === null) {
            return;
        }

        $this->openActivityLogModal($this->nextActivityLogId);
    }

    private function activityLogQuery(): Builder
    {
        $query = $this->filteredBaseQuery()
            ->select([
                'id',
                'log_name',
                'description',
                'subject_type',
                'subject_id',
                'event',
                'causer_type',
                'causer_id',
                'attribute_changes',
                'properties',
                'created_at',
                'updated_at',
            ]);

        $sortField = in_array($this->sortField, $this->allowedSortFields(), true)
            ? $this->sortField
            : 'id';

        return $query->orderBy($sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');
    }

    private function nextActivityLogIdFromCurrentTablePage(int $activityLogId): ?int
    {
        $currentPageActivityLogIds = $this->activityLogQuery()
            ->forPage(max(1, (int) $this->getPage()), $this->perPage)
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->values();

        $currentIndex = $currentPageActivityLogIds->search($activityLogId, true);

        if ($currentIndex === false) {
            return null;
        }

        $nextActivityLogId = $currentPageActivityLogIds->get($currentIndex + 1);

        return is_int($nextActivityLogId) ? $nextActivityLogId : null;
    }

    private function filteredBaseQuery(
        bool $applyLogNameFilter = true,
        bool $applyDateRangeFilter = true,
        bool $applyEventFilter = true,
        bool $applySubjectTypeFilter = true,
        bool $applyCauserTypeFilter = true,
        bool $applyCustomDateTimeFilter = true,
        bool $applyActivityLogTabFilter = true,
    ): Builder {
        $query = DB::table('activity_log');

        if ($applyLogNameFilter && $this->logNameFilter !== 'all') {
            $query->where('log_name', $this->logNameFilter);
        }

        if ($applyEventFilter && $this->eventFilter !== 'all') {
            $query->where('event', $this->eventFilter);
        }

        if ($applySubjectTypeFilter && $this->subjectTypeFilter !== 'all') {
            $query->where('subject_type', $this->subjectTypeFilter);
        }

        if ($applyCauserTypeFilter && $this->causerTypeFilter !== 'all') {
            $query->where('causer_type', $this->causerTypeFilter);
        }

        if ($applyDateRangeFilter) {
            $this->applyDateRangeFilter($query, $this->dateRangeFilter);
        }

        if ($applyCustomDateTimeFilter) {
            $this->applyDateTimeFilter($query);
        }

        if ($applyActivityLogTabFilter) {
            $this->applyActivityLogTabFilter($query);
        }

        $search = mb_strtolower(trim($this->search));

        if ($search !== '') {
            $query->where(function (Builder $searchQuery) use ($search): void {
                if (ctype_digit($search)) {
                    $searchQuery
                        ->orWhere('id', (int) $search)
                        ->orWhere('subject_id', (int) $search)
                        ->orWhere('causer_id', (int) $search);
                }

                foreach (['log_name', 'description', 'event', 'subject_type', 'causer_type'] as $column) {
                    $searchQuery->orWhereRaw('LOWER('.$column.') LIKE ?', ['%'.$search.'%']);
                }
            });
        }

        return $query;
    }

    /**
     * @return array<string, int>
     */
    private function summary(): array
    {
        $filteredQuery = $this->filteredBaseQuery();

        return [
            'total_entries' => DB::table('activity_log')->count(),
            'filtered_entries' => (clone $filteredQuery)->count(),
            'latest_id' => (int) DB::table('activity_log')->max('id'),
            'log_names' => DB::table('activity_log')
                ->whereNotNull('log_name')
                ->where('log_name', '!=', '')
                ->distinct()
                ->count('log_name'),
            'events' => DB::table('activity_log')
                ->whereNotNull('event')
                ->where('event', '!=', '')
                ->distinct()
                ->count('event'),
            'with_subject' => (clone $filteredQuery)
                ->whereNotNull('subject_type')
                ->whereNotNull('subject_id')
                ->count(),
            'with_causer' => (clone $filteredQuery)
                ->whereNotNull('causer_type')
                ->whereNotNull('causer_id')
                ->count(),
            'with_properties' => (clone $filteredQuery)
                ->whereRaw("COALESCE(properties::text, '') NOT IN ('', '[]', '{}')")
                ->count(),
            'with_changes' => (clone $filteredQuery)
                ->whereRaw("COALESCE(attribute_changes::text, '') NOT IN ('', '[]', '{}') OR (properties::jsonb ? 'before' AND properties::jsonb ? 'after')")
                ->count(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function distinctOptions(string $column): array
    {
        if (! in_array($column, ['log_name', 'event', 'subject_type', 'causer_type'], true)) {
            return [];
        }

        $query = match ($column) {
            'log_name' => $this->filteredBaseQuery(applyLogNameFilter: false),
            'event' => $this->filteredBaseQuery(applyEventFilter: false),
            'subject_type' => $this->filteredBaseQuery(applySubjectTypeFilter: false),
            'causer_type' => $this->filteredBaseQuery(applyCauserTypeFilter: false),
        };

        return $query
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(static fn (mixed $value): string => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{key: string, label: string, count: int, color: string}>
     */
    private function dateRangeCards(): array
    {
        $baseQuery = $this->filteredBaseQuery(applyDateRangeFilter: false);

        return collect([
            [
                'key' => 'all',
                'label' => __('ui.states.all'),
                'color' => 'sky',
            ],
            [
                'key' => 'today',
                'label' => __('Today'),
                'color' => 'emerald',
            ],
            [
                'key' => 'last_24h',
                'label' => __('Last 24h'),
                'color' => 'green',
            ],
            [
                'key' => 'last_7d',
                'label' => __('Last 7d'),
                'color' => 'amber',
            ],
            [
                'key' => 'last_30d',
                'label' => __('Last 30d'),
                'color' => 'purple',
            ],
        ])
            ->map(function (array $card) use ($baseQuery): array {
                $query = clone $baseQuery;

                $this->applyDateRangeFilter($query, $card['key']);

                return [
                    ...$card,
                    'count' => $query->count(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{key: string, label: string, count: int, color: string}>
     */
    private function logNameCards(): array
    {
        $baseQuery = $this->filteredBaseQuery(applyLogNameFilter: false);

        $cards = [
            [
                'key' => 'all',
                'label' => __('ui.states.all'),
                'count' => (clone $baseQuery)->count(),
                'color' => 'sky',
            ],
        ];

        $logNameRows = (clone $baseQuery)
            ->select('log_name', DB::raw('COUNT(*) as activity_log_count'))
            ->whereNotNull('log_name')
            ->where('log_name', '!=', '')
            ->groupBy('log_name')
            ->orderByDesc('activity_log_count')
            ->orderBy('log_name')
            ->limit(8)
            ->get();

        foreach ($logNameRows as $logNameRow) {
            $logName = trim((string) ($logNameRow->log_name ?? ''));

            if ($logName === '') {
                continue;
            }

            $cards[] = [
                'key' => $logName,
                'label' => $logName,
                'count' => (int) ($logNameRow->activity_log_count ?? 0),
                'color' => 'blue',
            ];
        }

        return $cards;
    }

    /**
     * @return array<int, array{key: string, label: string, count: int, color: string, icon: string}>
     */
    private function activityLogTabCards(): array
    {
        $baseQuery = $this->filteredBaseQuery(applyActivityLogTabFilter: false);

        return collect([
            [
                'key' => 'app',
                'label' => __('App Logs'),
                'color' => 'blue',
                'icon' => 'cube',
            ],
            [
                'key' => 'artisan',
                'label' => __('Artisan Logs'),
                'color' => 'amber',
                'icon' => 'command-line',
            ],
            [
                'key' => 'system',
                'label' => __('System Logs'),
                'color' => 'purple',
                'icon' => 'server',
            ],
            [
                'key' => 'all',
                'label' => __('All Logs'),
                'color' => 'sky',
                'icon' => 'database',
            ],
        ])
            ->map(function (array $card) use ($baseQuery): array {
                $query = clone $baseQuery;

                $this->applyActivityLogTabFilter($query, $card['key']);

                return [
                    ...$card,
                    'count' => $query->count(),
                ];
            })
            ->values()
            ->all();
    }

    private function applyActivityLogTabFilter(Builder $query, ?string $activityLogTab = null): void
    {
        $activityLogTab ??= $this->activityLogTab;

        match ($activityLogTab) {
            'app' => $query->where(function (Builder $appQuery): void {
                $appQuery
                    ->whereNotNull('subject_type')
                    ->orWhereNotNull('causer_type');
            }),

            'artisan' => $query->whereRaw(
                "COALESCE(properties::jsonb #>> '{actor,php_sapi}', '') = ?",
                ['cli'],
            ),

            'system' => $query
                ->whereNull('subject_type')
                ->whereNull('causer_type')
                ->where(function (Builder $systemQuery): void {
                    $systemQuery
                        ->whereNull('properties')
                        ->orWhereRaw("COALESCE(properties::text, '') IN ('', '[]', '{}')")
                        ->orWhereRaw(
                            "COALESCE(properties::jsonb #>> '{actor,php_sapi}', '') <> ?",
                            ['cli'],
                        );
                }),

            default => null,
        };
    }

    private function datePickerMinDate(): ?string
    {
        return $this->datePickerBoundaryDate('min');
    }

    private function datePickerMaxDate(): ?string
    {
        return $this->datePickerBoundaryDate('max');
    }

    private function datePickerBoundaryDate(string $aggregate): ?string
    {
        $query = $this->filteredBaseQuery(applyCustomDateTimeFilter: false);

        $value = $aggregate === 'min'
            ? $query->min('created_at')
            : $query->max('created_at');

        if ($value === null) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function applyDateRangeFilter(Builder $query, string $dateRangeFilter): void
    {
        match ($dateRangeFilter) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'last_24h' => $query->where('created_at', '>=', now()->subDay()),
            'last_7d' => $query->where('created_at', '>=', now()->subDays(7)),
            'last_30d' => $query->where('created_at', '>=', now()->subDays(30)),
            default => null,
        };
    }

    private function applyDateTimeFilter(Builder $query): void
    {
        if (! $this->hasCompleteCustomDateRange()) {
            return;
        }

        $dateFrom = $this->dateFilterBoundary('from');
        $dateTo = $this->dateFilterBoundary('to');

        if ($dateFrom !== null) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== null) {
            $query->where('created_at', '<=', $dateTo);
        }

        $timeFrom = $this->normalizedTimeFilter($this->timeFrom);
        $timeTo = $timeFrom !== null
            ? $this->normalizedTimeFilter($this->timeTo)
            : null;

        if ($timeTo !== null && $timeTo < $this->timeToMinTime()) {
            $timeTo = null;
        }

        if ($timeFrom !== null) {
            $query->whereTime('created_at', '>=', $timeFrom);
        }

        if ($timeTo !== null) {
            $query->whereTime('created_at', '<=', $timeTo);
        }
    }

    private function hasCompleteCustomDateRange(): bool
    {
        return trim((string) ($this->customDateRange['start'] ?? '')) !== ''
            && trim((string) ($this->customDateRange['end'] ?? '')) !== '';
    }

    private function dateFilterBoundary(string $boundary): ?Carbon
    {
        if (! $this->hasCompleteCustomDateRange()) {
            return null;
        }

        $dateKey = $boundary === 'from' ? 'start' : 'end';
        $dateValue = trim((string) ($this->customDateRange[$dateKey] ?? ''));

        if ($dateValue === '') {
            return null;
        }

        try {
            $date = Carbon::parse($dateValue);
        } catch (\Throwable) {
            return null;
        }

        return $boundary === 'from'
            ? $date->startOfDay()
            : $date->endOfDay();
    }

    private function dateTimeFilterBoundary(string $boundary): ?Carbon
    {
        if (! $this->hasCompleteCustomDateRange()) {
            return null;
        }

        $dateKey = $boundary === 'from' ? 'start' : 'end';
        $dateValue = trim((string) ($this->customDateRange[$dateKey] ?? ''));

        if ($dateValue === '') {
            return null;
        }

        try {
            $date = Carbon::parse($dateValue);
        } catch (\Throwable) {
            return null;
        }

        $timeFrom = $this->normalizedTimeFilter($this->timeFrom);

        $timeValue = $boundary === 'from'
            ? $timeFrom
            : ($timeFrom !== null ? $this->normalizedTimeFilter($this->timeTo) : null);

        if ($timeValue !== null) {
            [$hour, $minute] = explode(':', $timeValue);

            return $date->setTime((int) $hour, (int) $minute);
        }

        return $boundary === 'from'
            ? $date->startOfDay()
            : $date->endOfDay();
    }

    private function timeFromIntervalMinutes(): int
    {
        return max(1, min(1440, $this->timeFromInterval));
    }

    private function timeToIntervalMinutes(): int
    {
        return max(1, min(1440, $this->timeToInterval));
    }

    private function timeToMinTime(): string
    {
        $timeFrom = $this->normalizedTimeFilter($this->timeFrom);

        if ($timeFrom === null) {
            return '00:00';
        }

        [$hour, $minute] = array_map('intval', explode(':', $timeFrom));

        $timeFromMinutes = ($hour * 60) + $minute;
        $interval = $this->timeToIntervalMinutes();

        $nextSlotMinutes = (int) (floor($timeFromMinutes / $interval) * $interval);

        if ($nextSlotMinutes <= $timeFromMinutes) {
            $nextSlotMinutes += $interval;
        }

        if ($nextSlotMinutes >= 1440) {
            return '23:59';
        }

        return sprintf(
            '%02d:%02d',
            intdiv($nextSlotMinutes, 60),
            $nextSlotMinutes % 60,
        );
    }

    private function normalizedTimeFilter(?string $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return null;
        }

        if (! preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            return null;
        }

        [$hour, $minute] = array_map('intval', explode(':', $value));

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeActivityLogDetail(object $activityLog): array
    {
        $propertiesPayload = $this->decodeJsonValue($activityLog->properties ?? null);
        $attributeChangesPayload = $this->decodeJsonValue($activityLog->attribute_changes ?? null);

        if (
            $attributeChangesPayload === null
            && is_array($propertiesPayload)
            && is_array($propertiesPayload['before'] ?? null)
            && is_array($propertiesPayload['after'] ?? null)
        ) {
            $attributeChangesPayload = [
                'old' => $propertiesPayload['before'],
                'new' => $propertiesPayload['after'],
            ];
        }

        $attributeChangeRows = $this->attributeChangeRows($attributeChangesPayload);
        $actor = is_array($propertiesPayload) && is_array($propertiesPayload['actor'] ?? null)
            ? $this->normalizeActivityActor($propertiesPayload['actor'])
            : null;

        return [
            'id' => (int) ($activityLog->id ?? 0),
            'log_name' => trim((string) ($activityLog->log_name ?? '')),
            'description' => trim((string) ($activityLog->description ?? '')),
            'subject_type' => trim((string) ($activityLog->subject_type ?? '')),
            'subject_id' => $activityLog->subject_id ?? null,
            'event' => trim((string) ($activityLog->event ?? '')),
            'causer_type' => trim((string) ($activityLog->causer_type ?? '')),
            'causer_id' => $activityLog->causer_id ?? null,
            'actor' => $actor,
            'created_at' => $activityLog->created_at ?? null,
            'updated_at' => $activityLog->updated_at ?? null,
            'properties_json' => $this->formatJsonValue($activityLog->properties ?? null),
            'attribute_changes_json' => $attributeChangesPayload !== null
                ? json_encode($attributeChangesPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null,
            'properties_rows' => $this->payloadRows($propertiesPayload),
            'attribute_changes_rows' => $attributeChangeRows !== []
                ? $attributeChangeRows
                : $this->payloadRows($attributeChangesPayload),
            'attribute_changes_is_diff' => $attributeChangeRows !== [],
        ];
    }

    /**
     * @param  array<string, mixed>  $actor
     * @return array<string, string|null>
     */
    private function normalizeActivityActor(array $actor): array
    {
        return [
            'type' => trim((string) ($actor['type'] ?? '')),
            'terminal_user' => trim((string) ($actor['terminal_user'] ?? '')) ?: null,
            'hostname' => trim((string) ($actor['hostname'] ?? '')) ?: null,
            'php_sapi' => trim((string) ($actor['php_sapi'] ?? '')) ?: null,
            'cwd' => trim((string) ($actor['cwd'] ?? '')) ?: null,
        ];
    }

    private function decodeJsonValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $stringValue = trim((string) $value);

        if ($stringValue === '' || $stringValue === '[]' || $stringValue === '{}' || $stringValue === 'null') {
            return null;
        }

        $decoded = json_decode($stringValue, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $stringValue;
    }

    /**
     * @return array<int, array{key: string, value: string}>
     */
    private function payloadRows(mixed $payload, string $prefix = ''): array
    {
        if ($payload === null) {
            return [];
        }

        if (! is_array($payload)) {
            return [
                [
                    'key' => $prefix !== '' ? $prefix : 'value',
                    'value' => $this->payloadValue($payload),
                ],
            ];
        }

        if ($payload === []) {
            return [
                [
                    'key' => $prefix !== '' ? $prefix : 'value',
                    'value' => '[]',
                ],
            ];
        }

        if ($this->isScalarList($payload)) {
            return [
                [
                    'key' => $prefix !== '' ? $prefix : 'value',
                    'value' => $this->payloadValue($payload),
                ],
            ];
        }

        $rows = [];

        foreach ($payload as $key => $value) {
            $keyPath = $prefix !== '' ? $prefix.'.'.$key : (string) $key;

            if (is_array($value) && $value !== []) {
                array_push($rows, ...$this->payloadRows($value, $keyPath));

                continue;
            }

            $rows[] = [
                'key' => $keyPath,
                'value' => $this->payloadValue($value),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{field: string, old: string, new: string, changed: bool}>
     */
    private function attributeChangeRows(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        $oldValues = $payload['old'] ?? null;
        $newValues = $payload['attributes'] ?? $payload['new'] ?? null;

        if (! is_array($oldValues) || ! is_array($newValues)) {
            return [];
        }

        $fields = array_values(array_unique([
            ...array_keys($oldValues),
            ...array_keys($newValues),
        ]));

        sort($fields, SORT_NATURAL | SORT_FLAG_CASE);

        return collect($fields)
            ->map(fn (string $field): array => [
                'field' => $field,
                'old' => $this->payloadValue($oldValues[$field] ?? null),
                'new' => $this->payloadValue($newValues[$field] ?? null),
                'changed' => $this->payloadValue($oldValues[$field] ?? null) !== $this->payloadValue($newValues[$field] ?? null),
            ])
            ->values()
            ->all();
    }

    private function isScalarList(array $value): bool
    {
        if ($value === [] || ! array_is_list($value)) {
            return false;
        }

        return collect($value)
            ->every(static fn (mixed $item): bool => $item === null || is_bool($item) || is_scalar($item));
    }

    private function payloadValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return trim((string) $value) !== '' ? (string) $value : '""';
        }

        if (is_array($value) && $this->isScalarList($value)) {
            return collect($value)
                ->map(fn (mixed $item): string => $this->payloadValue($item))
                ->implode(', ');
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return is_string($encoded) && $encoded !== '' ? $encoded : '—';
    }

    private function formatJsonValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);

        if ($stringValue === '' || $stringValue === '[]' || $stringValue === '{}' || $stringValue === 'null') {
            return null;
        }

        $decoded = json_decode($stringValue, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $stringValue;
        }

        $encoded = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return is_string($encoded) && trim($encoded) !== '' ? $encoded : null;
    }

    /**
     * @return array<int, string>
     */
    private function allowedActivityLogTabs(): array
    {
        return [
            'all',
            'app',
            'artisan',
            'system',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function allowedDateRangeFilters(): array
    {
        return [
            'all',
            'today',
            'last_24h',
            'last_7d',
            'last_30d',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function allowedSortFields(): array
    {
        return [
            'id',
            'created_at',
            'log_name',
            'event',
            'description',
            'subject_type',
            'causer_type',
        ];
    }
}
