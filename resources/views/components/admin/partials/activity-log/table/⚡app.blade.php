{{-- resources/views/components/admin/partials/activity-log/table/⚡app.blade.php --}}

{{-- App Logs: app/model focused activity_log table --}}
<div
    class="mx-auto max-w-full scroll-mt-6"
    id="activity-log-table"
>
    <div class="overflow-hidden rounded-t-lg">
        {{-- Activity-Log Table --}}
        <flux:table container:class="app-table">

            {{-- Activity-Log-Header Row --}}
            <flux:table.columns
                class="bg-zinc-800 text-zinc-400"
                sticky
            >
                {{-- Table-Header Sequence-Number --}}
                <flux:table.column
                    class="w-14 tabular-nums"
                    align="center"
                >
                    <flux:icon.tally-5
                        class="ml-3"
                        stroke-width="1"
                    />
                </flux:table.column>

                {{-- Table-Header ID --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    align="center"
                    sortable
                    :sorted="$sortField === 'id'"
                    :direction="$sortDirection"
                    wire:click="sortBy('id')"
                >
                    {{ __('admin.user_list.table.id') }}
                </flux:table.column>

                {{-- Table-Header Created --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    align="center"
                    sortable
                    :sorted="$sortField === 'created_at'"
                    :direction="$sortDirection"
                    wire:click="sortBy('created_at')"
                >
                    {{ __('admin.client_list.table.created') }}
                </flux:table.column>

                {{-- Table-Header Log --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    sortable
                    :sorted="$sortField === 'log_name'"
                    :direction="$sortDirection"
                    wire:click="sortBy('log_name')"
                >
                    {{ __('Log') }}
                </flux:table.column>

                {{-- Table-Header Event --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'event'"
                    :direction="$sortDirection"
                    wire:click="sortBy('event')"
                >
                    {{ __('admin.translation_list.modal_history.event') }}
                </flux:table.column>

                {{-- Table-Header Description --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'description'"
                    :direction="$sortDirection"
                    wire:click="sortBy('description')"
                >
                    {{ __('ui.labels.description') }}
                </flux:table.column>

                {{-- Table-Header Subject --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    align="center"
                    sortable
                    :sorted="$sortField === 'subject_type'"
                    :direction="$sortDirection"
                    wire:click="sortBy('subject_type')"
                >
                    {{ __('Subject') }}
                </flux:table.column>

                {{-- Table-Header Causer --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    align="center"
                    sortable
                    :sorted="$sortField === 'causer_type'"
                    :direction="$sortDirection"
                    wire:click="sortBy('causer_type')"
                >
                    {{ __('Causer') }}
                </flux:table.column>

                {{-- Table-Header Data --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    align="center"
                >
                    {{ __('Data') }}
                </flux:table.column>

                {{-- Table-Header Actions --}}
                <flux:table.column
                    class="w-px whitespace-nowrap"
                    align="center"
                >
                    <span class="mr-4">{{ __('ui.table.headers.actions') }}</span>
                </flux:table.column>
            </flux:table.columns>

            {{-- Table-Rows --}}
            <flux:table.rows>
                @forelse ($activityLogs as $activityLog)
                    @php
                        $id = (int) ($activityLog->id ?? 0);
                        $logName = trim((string) ($activityLog->log_name ?? ''));
                        $event = trim((string) ($activityLog->event ?? ''));
                        $description = trim((string) ($activityLog->description ?? ''));
                        $subjectType = trim((string) ($activityLog->subject_type ?? ''));
                        $subjectId = $activityLog->subject_id ?? null;
                        $causerType = trim((string) ($activityLog->causer_type ?? ''));
                        $causerId = $activityLog->causer_id ?? null;
                        $createdAt = $activityLog->created_at ?? null;
                        $hasProperties = $jsonHasData($activityLog->properties ?? null);
                        $hasChanges = $jsonHasData($activityLog->attribute_changes ?? null);
                    @endphp

                    {{-- Table-Row --}}
                    <flux:table.row wire:key="activity-log-app-{{ $id }}">
                        {{-- Table-Cell Sequence-Number --}}
                        <flux:table.cell
                            class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                            align="end"
                        >
                            {{ $activityLogs->firstItem() + $loop->index }}
                        </flux:table.cell>

                        {{-- Table-Cell ID --}}
                        <flux:table.cell
                            class="w-px whitespace-nowrap align-top"
                            align="center"
                        >
                            <flux:badge
                                class="tabular-nums"
                                size="sm"
                                variant="subtle"
                                color="zinc"
                            >
                                #{{ $id }}
                            </flux:badge>
                        </flux:table.cell>

                        {{-- Table-Cell Created --}}
                        <flux:table.cell
                            class="w-px whitespace-nowrap align-top"
                            align="center"
                        >
                            @if ($createdAt)
                                <div class="flex flex-col items-center gap-1 tabular-nums">
                                    <x-ui.date-time.date
                                        class="text-xs"
                                        :value="$createdAt"
                                    />

                                    <x-ui.date-time.time
                                        class="text-xs text-zinc-500 dark:text-zinc-400"
                                        :value="$createdAt"
                                    />
                                </div>
                            @else
                                <x-ui.badge.no-value />
                            @endif
                        </flux:table.cell>

                        {{-- Table-Cell Log --}}
                        <flux:table.cell class="w-px whitespace-nowrap align-top">
                            @if ($logName !== '')
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                    color="blue"
                                >
                                    {{ $logName }}
                                </flux:badge>
                            @else
                                —
                            @endif
                        </flux:table.cell>

                        {{-- Table-Cell Event --}}
                        <flux:table.cell class="align-top">
                            @if ($event !== '')
                                <code class="wrap-anywhere block text-xs">
                                    {{ $event }}
                                </code>
                            @else
                                <x-ui.badge.no-value />
                            @endif
                        </flux:table.cell>

                        {{-- Table-Cell Description --}}
                        <flux:table.cell class="align-top">
                            @if ($description !== '')
                                <div class="wrap-anywhere max-w-2xl text-sm">
                                    {{ $description }}
                                </div>
                            @else
                                <x-ui.badge.no-value />
                            @endif
                        </flux:table.cell>

                        {{-- Table-Cell Subject --}}
                        <flux:table.cell
                            class="w-px whitespace-nowrap align-top"
                            align="center"
                        >
                            @if ($subjectType !== '' || $subjectId !== null)
                                <div class="flex flex-col items-center gap-1">
                                    @if ($subjectType !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="amber"
                                        >
                                            {{ class_basename($subjectType) }}
                                        </flux:badge>
                                    @endif

                                    @if ($subjectId !== null)
                                        <flux:badge
                                            class="tabular-nums"
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            #{{ $subjectId }}
                                        </flux:badge>
                                    @endif
                                </div>
                            @else
                                <x-ui.badge.no-value />
                            @endif
                        </flux:table.cell>

                        {{-- Table-Cell Causer --}}
                        <flux:table.cell
                            class="w-px whitespace-nowrap align-top"
                            align="center"
                        >
                            @if ($causerType !== '' || $causerId !== null)
                                <div class="flex flex-col items-center gap-1">
                                    @if ($causerType !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="green"
                                        >
                                            {{ class_basename($causerType) }}
                                        </flux:badge>
                                    @endif

                                    @if ($causerId !== null)
                                        <flux:badge
                                            class="tabular-nums"
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            #{{ $causerId }}
                                        </flux:badge>
                                    @endif
                                </div>
                            @else
                                <x-ui.badge.no-value />
                            @endif
                        </flux:table.cell>

                        {{-- Table-Cell Data --}}
                        <flux:table.cell
                            class="w-px whitespace-nowrap align-top"
                            align="center"
                        >
                            <div class="flex flex-col items-center gap-1 pr-4">
                                @if ($hasProperties)
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="violet"
                                    >
                                        {{ __('Properties') }}
                                    </flux:badge>
                                @endif

                                @if ($hasChanges)
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="orange"
                                    >
                                        {{ __('admin.permissions.modals.roles.changes') }}
                                    </flux:badge>
                                @endif

                                @unless ($hasProperties || $hasChanges)
                                    <x-ui.badge.no-value />
                                @endunless
                            </div>
                        </flux:table.cell>

                        {{-- Table-Cell Actions --}}
                        <flux:table.cell
                            class="w-px whitespace-nowrap align-top"
                            align="end"
                        >
                            <flux:button
                                class="mr-4"
                                type="button"
                                size="sm"
                                variant="primary"
                                color="lime"
                                icon="scan-search"
                                wire:click="openActivityLogModal({{ $id }})"
                            >
                                {{ __('Details') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    {{-- Table Row No Data --}}
                    <flux:table.row>
                        {{-- Table-Cell No Data --}}
                        <flux:table.cell colspan="10">
                            <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No app activity log entries found for the selected filters.') }}
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
