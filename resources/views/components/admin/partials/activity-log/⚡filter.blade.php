{{-- resources/views/components/admin/partials/activity-log/⚡filter.blade.php --}}

@php
    $dateRangeFilter = $dateRangeFilter ?? 'all';
    $dateTimeFrom = trim((string) ($dateTimeFrom ?? ''));
    $dateTimeTo = trim((string) ($dateTimeTo ?? ''));
    $logNameFilter = $logNameFilter ?? 'all';
    $eventFilter = $eventFilter ?? 'all';
    $subjectTypeFilter = $subjectTypeFilter ?? 'all';
    $causerTypeFilter = $causerTypeFilter ?? 'all';
    $dateRangeCards = $dateRangeCards ?? [];
    $logNameCards = $logNameCards ?? [];
    $logNameOptions = $logNameOptions ?? [];
    $eventOptions = $eventOptions ?? [];
    $subjectTypeOptions = $subjectTypeOptions ?? [];
    $causerTypeOptions = $causerTypeOptions ?? [];

@endphp

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filters')"
        :description="__(
            'Refine activity_log entries by timespan, date/time, log name, text search, event, subject type, and causer type.',
        )"
    >
        <div class="ml-auto grid w-full max-w-full gap-2 xl:w-[62rem] 2xl:w-[70rem]">
            <div
                class="flex w-full flex-wrap items-center justify-start gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">

                {{-- Buttons Timespan --}}
                <x-ui.tooltip.trigger
                    :title="__('Timespan')"
                    :text="__('Filter activity log entries by predefined timespans.')"
                >
                    <span
                        class="mr-2 w-28 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Timespan') }}
                    </span>
                </x-ui.tooltip.trigger>

                @foreach ($dateRangeCards as $dateRangeCard)
                    <flux:button
                        type="button"
                        size="sm"
                        :variant="$dateRangeFilter === $dateRangeCard['key'] ? 'primary' : 'filled'"
                        :color="$dateRangeFilter === $dateRangeCard['key'] ? $dateRangeCard['color'] : null"
                        wire:click="setDateRangeFilter('{{ $dateRangeCard['key'] }}')"
                    >
                        {{ $dateRangeCard['label'] }}
                        <span class="ml-2 opacity-70">
                            {{ number_format((int) $dateRangeCard['count']) }}
                        </span>
                    </flux:button>
                @endforeach
            </div>

            <div
                class="flex w-full flex-wrap items-center justify-start gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">

                {{-- Buttons Log --}}
                <x-ui.tooltip.trigger
                    :title="__('Log')"
                    :text="__(
                        'Filter activity log entries by log name. This is the most common filter to find relevant entries quickly.',
                    )"
                >
                    <span
                        class="mr-2 w-28 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Log') }}
                    </span>
                </x-ui.tooltip.trigger>

                @foreach ($logNameCards as $logNameCard)
                    <flux:button
                        type="button"
                        size="sm"
                        :variant="$logNameFilter === $logNameCard['key'] ? 'primary' : 'filled'"
                        :color="$logNameFilter === $logNameCard['key'] ? $logNameCard['color'] : null"
                        wire:click="setLogNameFilter('{{ $logNameCard['key'] }}')"
                    >
                        {{ $logNameCard['label'] }}
                        <span class="ml-2 opacity-70">
                            {{ number_format((int) $logNameCard['count']) }}
                        </span>
                    </flux:button>
                @endforeach
            </div>
        </div>
    </x-ui.headers.card>

    <div class="mt-4 grid w-full gap-3 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-end">
        {{-- Left: input/select filters --}}
        <div class="grid w-full gap-3 xl:grid-cols-[repeat(15,minmax(0,1fr))]">
            {{-- Input Search --}}
            <div class="min-w-0 xl:col-span-6">
                <flux:field>
                    <flux:label for="activity-log-search">
                        <x-ui.tooltip.trigger
                            :title="__('ui.actions.search')"
                            :text="__('Search by ID, log name, description, event, subject, or causer.')"
                        >
                            {{ __('ui.actions.search') }}
                        </x-ui.tooltip.trigger>
                    </flux:label>

                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.magnifying-glass stroke-width="1" />
                        </flux:input.group.prefix>

                        <flux:input
                            id="activity-log-search"
                            name="activity-log-search"
                            clearable
                            copyable
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Search activity log entries') }}"
                        />
                    </flux:input.group>
                </flux:field>
            </div>

            {{-- Select Log --}}
            <div class="min-w-0 xl:col-span-3">
                <flux:field>
                    <flux:label>
                        <x-ui.tooltip.trigger
                            :title="__('Log')"
                            :text="__(
                                'Filter activity log entries by log name. This is the most common filter to find relevant entries quickly.',
                            )"
                        >
                            {{ __('Log') }}
                        </x-ui.tooltip.trigger>
                    </flux:label>

                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.folder-search stroke-width="1" />
                        </flux:input.group.prefix>

                        <flux:select
                            id="activity-log-log"
                            name="activity-log-log"
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="logNameFilter"
                        >
                            <flux:select.option value="all">
                                {{ __('ui.states.all') }}
                            </flux:select.option>

                            @foreach ($logNameOptions as $logNameOption)
                                <flux:select.option value="{{ $logNameOption }}">
                                    {{ $logNameOption }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>
                </flux:field>
            </div>

            {{-- Select Event --}}
            <div class="min-w-0 xl:col-span-6">
                <flux:field>
                    <flux:label>
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.modal_history.event')"
                            :text="__(
                                'Filter activity log entries by event type, such as created, updated, deleted, etc.',
                            )"
                        >
                            {{ __('admin.translation_list.modal_history.event') }}
                        </x-ui.tooltip.trigger>
                    </flux:label>

                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.waypoints stroke-width="1" />
                        </flux:input.group.prefix>

                        <flux:select
                            id="activity-log-namespace"
                            name="activity-log-namespace"
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="eventFilter"
                        >
                            <flux:select.option value="all">
                                {{ __('ui.states.all') }}
                            </flux:select.option>

                            @foreach ($eventOptions as $eventOption)
                                <flux:select.option value="{{ $eventOption }}">
                                    {{ $eventOption }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>
                </flux:field>
            </div>

            {{-- Select Subject --}}
            <div class="min-w-0 xl:col-span-6">
                <flux:field>
                    <flux:label>
                        <x-ui.tooltip.trigger
                            :title="__('Subject')"
                            :text="__(
                                'Filter activity log entries by subject type, which is the model that the activity log entry is about. For example, if the activity log entry is about a User model, the subject type would be App\Models\User.',
                            )"
                        >
                            {{ __('Subject') }}
                        </x-ui.tooltip.trigger>
                    </flux:label>

                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.component stroke-width="1" />
                        </flux:input.group.prefix>

                        <flux:select
                            id="activity-log-subject"
                            name="activity-log-subject"
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="subjectTypeFilter"
                        >
                            <flux:select.option value="all">
                                {{ __('ui.states.all') }}
                            </flux:select.option>

                            @foreach ($subjectTypeOptions as $subjectTypeOption)
                                <flux:select.option value="{{ $subjectTypeOption }}">
                                    {{ class_basename($subjectTypeOption) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>
                </flux:field>
            </div>

            {{-- Select Causer --}}
            <div class="min-w-0 xl:col-span-9">
                <flux:field>
                    <flux:label>
                        <x-ui.tooltip.trigger
                            :title="__('Causer')"
                            :text="__(
                                'Filter activity log entries by causer type, which is the model that performed the action. For example, if the action was performed by a User model, the causer type would be App\Models\User.',
                            )"
                        >
                            {{ __('Causer') }}
                        </x-ui.tooltip.trigger>
                    </flux:label>

                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user stroke-width="1" />
                        </flux:input.group.prefix>

                        <flux:select
                            id="activity-log-causer"
                            name="activity-log-causer"
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="causerTypeFilter"
                        >
                            <flux:select.option value="all">
                                {{ __('ui.states.all') }}
                            </flux:select.option>

                            @foreach ($causerTypeOptions as $causerTypeOption)
                                <flux:select.option value="{{ $causerTypeOption }}">
                                    {{ class_basename($causerTypeOption) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:input.group>
                </flux:field>
            </div>
        </div>

        {{-- Right: table controls --}}
        <div class="flex shrink-0 items-end justify-end gap-3">
            {{-- Per Page --}}
            <div class="w-56">
                <x-ui.table.per-page-selector
                    id="activity-log-per-page"
                    name="activity-log-per-page"
                    model="perPage"
                    :options="[10, 25, 50, 100]"
                />
            </div>

            {{-- Reset --}}
            <div class="flex items-end">
                <x-ui.tooltip.trigger
                    :title="__('Reset Filters')"
                    :text="__('Clear all filters and show the complete activity log.')"
                >
                    <x-ui.button.reset wire:click="clearFilters" />
                </x-ui.tooltip.trigger>
            </div>
        </div>
    </div>

</flux:card>
