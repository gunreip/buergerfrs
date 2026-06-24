{{-- resources/views/components/admin/partials/activity-log/⚡modal.blade.php --}}

@php
    $activityLog = $selectedActivityLog ?? null;
    $nextActivityLogId = $nextActivityLogId ?? null;

    $propertiesJson = is_array($activityLog) ? trim((string) ($activityLog['properties_json'] ?? '')) : '';
    $attributeChangesJson = is_array($activityLog) ? trim((string) ($activityLog['attribute_changes_json'] ?? '')) : '';
    $propertiesRows = is_array($activityLog) ? (array) ($activityLog['properties_rows'] ?? []) : [];
    $attributeChangesRows = is_array($activityLog) ? (array) ($activityLog['attribute_changes_rows'] ?? []) : [];
    $attributeChangesIsDiff = is_array($activityLog)
        ? (bool) ($activityLog['attribute_changes_is_diff'] ?? false)
        : false;

    $hasPropertiesPayload = $propertiesRows !== [] || $propertiesJson !== '';
    $hasAttributeChangesPayload = $attributeChangesRows !== [] || $attributeChangesJson !== '';
    $payloadGridUsesTwoColumns = $hasPropertiesPayload && $hasAttributeChangesPayload;

    $subjectType = is_array($activityLog) ? trim((string) ($activityLog['subject_type'] ?? '')) : '';
    $subjectId = is_array($activityLog) ? $activityLog['subject_id'] ?? null : null;
    $causerType = is_array($activityLog) ? trim((string) ($activityLog['causer_type'] ?? '')) : '';
    $causerId = is_array($activityLog) ? $activityLog['causer_id'] ?? null : null;
    $actor = is_array($activityLog) && is_array($activityLog['actor'] ?? null) ? $activityLog['actor'] : [];
    $actorType = trim((string) ($actor['type'] ?? ''));
    $terminalUser = trim((string) ($actor['terminal_user'] ?? ''));
    $terminalHostname = trim((string) ($actor['hostname'] ?? ''));
    $phpSapi = trim((string) ($actor['php_sapi'] ?? ''));
@endphp

<flux:modal
    class="w-full max-w-7xl"
    name="activity-log-details"
    wire:model.self="activityLogModalOpen"
>
    @if (is_array($activityLog))
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink items-start justify-between gap-4">
                {{-- Card Header with ID badge and next button --}}
                <x-ui.headers.card
                    :title="__('Activity Log Details')"
                    :description="__(
                        'Detailed activity_log entry information, including properties and attribute changes.',
                    )"
                />

                <div class="mr-8 mt-2 flex flex-col items-end gap-2">
                    {{-- Badge with activity log ID --}}
                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                        color="zinc"
                    >
                        #{{ (int) ($activityLog['id'] ?? 0) }}
                    </flux:badge>

                    @if ($nextActivityLogId !== null)
                        {{-- Button Open Next Activity Log Entry --}}
                        <x-ui.button.next-edit
                            :loading="true"
                            wire:click="openNextActivityLogFromList"
                            :aria-label="__('Open next activity log entry')"
                        />
                    @endif
                </div>
            </div>

            <div class="min-h-0 overflow-y-auto py-6">
                <div class="space-y-6">
                    <div class="grid gap-3 md:grid-cols-4">
                        <flux:callout
                            color="blue"
                            icon="folder-search"
                            stroke-width="1"
                        >
                            <flux:callout.heading>
                                {{ __('Log') }}
                            </flux:callout.heading>

                            <flux:callout.text class="font-mono text-sm">
                                {{ trim((string) ($activityLog['log_name'] ?? '')) !== '' ? $activityLog['log_name'] : '—' }}
                            </flux:callout.text>
                        </flux:callout>

                        <flux:callout
                            color="purple"
                            icon="waypoints"
                            stroke-width="1"
                        >
                            <flux:callout.heading>
                                {{ __('admin.translation_list.modal_history.event') }}
                            </flux:callout.heading>

                            <flux:callout.text class="wrap-anywhere font-mono text-sm">
                                {{ trim((string) ($activityLog['event'] ?? '')) !== '' ? $activityLog['event'] : '—' }}
                            </flux:callout.text>
                        </flux:callout>

                        <flux:callout
                            color="amber"
                            icon="component"
                            stroke-width="1"
                        >
                            <flux:callout.heading>
                                {{ __('Subject') }}
                            </flux:callout.heading>

                            <flux:callout.text>
                                @if ($subjectType !== '' || $subjectId !== null)
                                    <div class="flex flex-wrap items-center gap-1">
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
                            </flux:callout.text>
                        </flux:callout>

                        <flux:callout
                            color="green"
                            icon="user"
                            stroke-width="1"
                            heading="Causerheader"
                            text="Causertext"
                        >
                            <flux:callout.heading>
                                {{ __('Causer') }}
                            </flux:callout.heading>

                            <flux:callout.text>
                                @if ($causerType !== '' || $causerId !== null)
                                    <div class="flex flex-wrap items-center gap-1">
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
                                @elseif ($actorType === 'terminal' && $terminalUser !== '')
                                    <div class="flex flex-wrap items-center gap-1">
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="green"
                                        >
                                            {{ __('Terminal') }}
                                        </flux:badge>

                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ $terminalUser }}
                                        </flux:badge>

                                        @if ($terminalHostname !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                {{ $terminalHostname }}
                                            </flux:badge>
                                        @endif

                                        @if ($phpSapi !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                {{ $phpSapi }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                @else
                                    <x-ui.badge.no-value />
                                @endif
                            </flux:callout.text>
                        </flux:callout>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        <flux:card>
                            <x-ui.headers.card
                                :title="__('ui.labels.description')"
                                :description="__('Human-readable activity description.')"
                            />

                            <flux:text class="wrap-anywhere mt-1 hyphens-auto text-sm text-zinc-700 dark:text-zinc-300">
                                {{ trim((string) ($activityLog['description'] ?? '')) !== '' ? $activityLog['description'] : '—' }}
                            </flux:text>
                        </flux:card>

                        <flux:card>
                            <x-ui.headers.card
                                :title="__('admin.client_list.table.created')"
                                :description="__('Creation timestamp of this activity_log entry.')"
                            />

                            <flux:text class="mt-1 text-sm tabular-nums text-zinc-700 dark:text-zinc-300">
                                @if ($activityLog['created_at'] ?? null)
                                    <span class="mr-2">
                                        <x-ui.date-time.date :value="$activityLog['created_at']" />
                                    </span>
                                    <span>
                                        <x-ui.date-time.time :value="$activityLog['created_at']" />
                                    </span>
                                @else
                                    <x-ui.badge.no-date class="mr-2" />
                                    <x-ui.badge.no-time />
                                @endif
                            </flux:text>
                        </flux:card>

                        <flux:card>
                            <x-ui.headers.card
                                :title="__('Updated')"
                                :description="__('Last update timestamp of this activity_log entry.')"
                            />

                            <flux:text class="mt-1 text-sm tabular-nums text-zinc-700 dark:text-zinc-300">
                                @if ($activityLog['updated_at'] ?? null)
                                    <span class="mr-2">
                                        <x-ui.date-time.date :value="$activityLog['updated_at']" />
                                    </span>
                                    <span>
                                        <x-ui.date-time.time :value="$activityLog['updated_at']" />
                                    </span>
                                @else
                                    <x-ui.badge.no-date class="mr-2" />
                                    <x-ui.badge.no-time />
                                @endif
                            </flux:text>
                        </flux:card>
                    </div>

                    {{-- Properties and Attribute Changes Payloads (if available). If both are present, they will be displayed in a 2-column grid layout. If only one is present, it will take the full width. --}}
                    <div @class(['grid gap-3', 'xl:grid-cols-2' => $payloadGridUsesTwoColumns])>
                        @if ($hasPropertiesPayload || !$hasAttributeChangesPayload)
                            <flux:card>
                                {{-- Properties payload is commonly used to store the state of the model's attributes at the time of the event. It can include both old and new values, but often it's just a snapshot of the current state. Attribute changes payload, on the other hand, is specifically designed to capture the differences between the old and new states of the model's attributes, making it easier to see what exactly changed during an update event. --}}
                                <x-ui.headers.card
                                    :title="__('Properties')"
                                    :description="__('Structured payload view. Raw JSON is available below as fallback.')"
                                />

                                @if ($propertiesRows !== [])
                                    <div class="mt-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        {{-- Table Properties no explicit Header Row --}}
                                        <flux:table class="w-full table-fixed">
                                            {{-- Table Rows --}}
                                            <flux:table.rows class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                                @foreach ($propertiesRows as $propertiesRow)
                                                    {{-- Table Row --}}
                                                    <flux:table.row>
                                                        {{-- Table Cell with Property Key --}}
                                                        <flux:table.cell
                                                            class="wrap-anywhere w-56 max-w-56 whitespace-normal bg-cyan-300/10 px-3 py-2 align-top font-mono font-semibold text-cyan-800 dark:bg-cyan-700/10 dark:text-cyan-400"
                                                        >
                                                            {{ $propertiesRow['key'] ?? '—' }}
                                                        </flux:table.cell>
                                                        {{-- Table Cell with Property Value --}}
                                                        <flux:table.cell
                                                            class="wrap-anywhere min-w-0 whitespace-normal px-3 py-2 font-mono"
                                                        >
                                                            {{ $propertiesRow['value'] ?? '—' }}
                                                        </flux:table.cell>
                                                    </flux:table.row>
                                                @endforeach
                                            </flux:table.rows>
                                        </flux:table>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        {{-- No Property Values --}}
                                        <x-ui.badge.no-value />
                                    </div>
                                @endif

                                @if ($propertiesJson !== '')
                                    <details class="mt-3">
                                        <summary
                                            class="cursor-pointer text-xs font-semibold text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                        >
                                            {{ __('Raw JSON') }}
                                        </summary>

                                        <pre class="mt-2 h-full rounded-lg bg-zinc-950 p-4 text-xs text-zinc-100"><code>{{ $propertiesJson }}</code></pre>
                                    </details>
                                @endif
                            </flux:card>
                        @endif

                        @if ($hasAttributeChangesPayload || !$hasPropertiesPayload)
                            <flux:card>
                                <x-ui.headers.card
                                    :title="__('Attribute changes')"
                                    :description="$attributeChangesIsDiff
                                        ? __('Detected old/new structure. Showing field diff.')
                                        : __('Structured payload view. Raw JSON is available below as fallback.')"
                                />

                                @if ($attributeChangesRows !== [])
                                    <div class="mt-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        @if ($attributeChangesIsDiff)
                                            <table class="w-full text-left text-xs">
                                                <thead
                                                    class="sticky top-0 bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400"
                                                >
                                                    <tr>
                                                        <th class="w-44 px-3 py-2 font-semibold">
                                                            {{ __('Field') }}
                                                        </th>
                                                        <th class="px-3 py-2 font-semibold">
                                                            {{ __('Old') }}
                                                        </th>
                                                        <th class="px-3 py-2 font-semibold">
                                                            {{ __('New') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                                    @foreach ($attributeChangesRows as $attributeChangesRow)
                                                        <tr>
                                                            <th
                                                                class="bg-zinc-50 px-3 py-2 align-top font-mono font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                                                                {{ $attributeChangesRow['field'] ?? '—' }}
                                                            </th>
                                                            <td
                                                                class="wrap-anywhere px-3 py-2 font-mono text-zinc-600 dark:text-zinc-400">
                                                                {{ $attributeChangesRow['old'] ?? '—' }}
                                                            </td>
                                                            <td
                                                                class="wrap-anywhere px-3 py-2 font-mono text-zinc-800 dark:text-zinc-200">
                                                                {{ $attributeChangesRow['new'] ?? '—' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="w-full text-left text-xs">
                                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                                    @foreach ($attributeChangesRows as $attributeChangesRow)
                                                        <tr>
                                                            <th
                                                                class="w-56 bg-zinc-50 px-3 py-2 align-top font-mono font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                                                                {{ $attributeChangesRow['key'] ?? '—' }}
                                                            </th>
                                                            <td
                                                                class="wrap-anywhere px-3 py-2 font-mono text-zinc-700 dark:text-zinc-300">
                                                                {{ $attributeChangesRow['value'] ?? '—' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <x-ui.badge.no-value />
                                    </div>
                                @endif

                                @if ($attributeChangesJson !== '')
                                    <details class="mt-3">
                                        <summary
                                            class="cursor-pointer text-xs font-semibold text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                        >
                                            {{ __('Raw JSON') }}
                                        </summary>

                                        <pre class="mt-2 max-h-80 overflow-auto rounded-lg bg-zinc-950 p-4 text-xs text-zinc-100"><code>{{ $attributeChangesJson }}</code></pre>
                                    </details>
                                @endif
                            </flux:card>
                        @endif
                    </div>

                </div>
            </div>

            <div class="flex shrink-0 justify-end gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                {{-- Button Close Modal --}}
                <x-ui.button.cancel
                    label="{{ __('ui.actions.close') }}"
                    icon="circle-x"
                    :loading="true"
                    wire:click="closeActivityLogModal"
                />

                @if ($nextActivityLogId !== null)
                    {{-- Button Open Next Activity Log Entry --}}
                    <x-ui.button.next-edit
                        class="h-10 w-10"
                        :loading="true"
                        wire:click="openNextActivityLogFromList"
                        :aria-label="__('Open next activity log entry')"
                    />
                @endif
            </div>
        </div>
    @endif
</flux:modal>
