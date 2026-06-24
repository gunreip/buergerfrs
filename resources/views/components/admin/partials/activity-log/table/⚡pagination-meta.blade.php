{{-- resources/views/components/admin/partials/activity-log/table/⚡pagination-meta.blade.php --}}

@if ($tableEntryFirstCreatedAt || $tableEntryLastCreatedAt || $activityLogs?->hasPages())
    {{-- Current table entry Date/Time range and pagination --}}
    <div
        class="my-2 border-b border-zinc-800/10 pb-2 dark:border-white/20"
        id="activity-log-pagination-top"
    >
        <div class="mb-1 flex min-h-7 w-full flex-wrap items-center justify-between gap-2">
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
                @include('components.admin.partials.activity-log.table.⚡active-filter-badges')
            </div>

            <div class="shrink-0 text-right text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Displayed date/time') }}:

                @if ($tableEntryFirstCreatedAt && $tableEntryLastCreatedAt)
                    <span class="ml-1 tabular-nums">
                        <x-ui.date-time.date
                            class="text-xs text-zinc-500 dark:text-zinc-400"
                            :value="$tableEntryFirstCreatedAt"
                        />
                        <x-ui.date-time.time
                            class="text-xs text-zinc-500 dark:text-zinc-400"
                            :value="$tableEntryFirstCreatedAt"
                        />
                    </span>

                    <span class="mx-1">–</span>

                    <span class="tabular-nums">
                        <x-ui.date-time.date
                            class="text-xs text-zinc-500 dark:text-zinc-400"
                            :value="$tableEntryLastCreatedAt"
                        />
                        <x-ui.date-time.time
                            class="text-xs text-zinc-500 dark:text-zinc-400"
                            :value="$tableEntryLastCreatedAt"
                        />
                    </span>
                @else
                    <span class="ml-1">—</span>
                @endif
            </div>
        </div>

        @if ($activityLogs?->hasPages())
            <x-ui.table.pagination
                class="m-0! p-0!"
                id="activity-log-pagination-top"
                :paginator="$activityLogs"
                scroll-to="#activity-log-pagination-top"
            />
        @endif
    </div>
@endif
