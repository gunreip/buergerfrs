{{-- resources/views/components/admin/partials/translation-lang-ballast/⚡table.blade.php --}}

@php
    $activeActionFilter = $activeActionFilter ?? 'action_files';

    $isActionFilesTab = $activeActionFilter === 'action_files';

    $tableRows = $isActionFilesTab ? $actionFileRows : $actionRows;

    $tableTitle = match ($activeActionFilter) {
        'remove' => __('Lang cleanup candidates'),
        'add' => __('Missing in lang'),
        'base_duplicates' => __('Sub-language duplicates'),
        'review' => __('Needs review'),
        default => __('Affected lang files'),
    };

    $tableDescription = match ($activeActionFilter) {
        'remove' => __(
            'Entries that exist in lang/* but are no longer exportable from the current database state. These are lang file cleanup candidates, not database delete candidates.',
        ),
        'add' => __('Exportable database entries that are currently missing from lang/* files.'),
        'base_duplicates' => __(
            'Sub-language values that are identical to their main language value. They are intentionally not exported, but remain visible for later database cleanup.',
        ),
        'review' => __(
            'Entries where the audit cannot safely derive an automatic lang file cleanup or add recommendation.',
        ),
        default => __('Lang files grouped by generated audit candidates from the latest lang ballast audit.'),
    };

    $decisionStatusMeta = [
        'open' => [
            'label' => __('admin.translation_list.filter.open'),
            'color' => 'amber',
        ],
        'reviewed' => [
            'label' => __('admin.translation_list.modal_edit.reviewed'),
            'color' => 'sky',
        ],
        'approved' => [
            'label' => __('Approved'),
            'color' => 'emerald',
        ],
        'ignored' => [
            'label' => __('Ignored'),
            'color' => 'zinc',
        ],
    ];
@endphp

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="$tableTitle"
        :description="$tableDescription"
    />

    @if ($tableRows->hasPages())
        {{-- Pagination --}}
        <div
            class="mb-2 mt-2"
            id="translation-lang-ballast-pagination-top"
        >
            <x-ui.table.pagination
                class="m-0! p-0!"
                id="translation-lang-ballast-pagination-top"
                :paginator="$tableRows"
                scroll-to="#translation-lang-ballast-pagination-top"
            />
        </div>
    @endif

    <div
        class="mx-auto max-w-full scroll-mt-6"
        id="translation-lang-ballast-table"
    >
        <div class="overflow-hidden rounded-t-lg">
            @if ($isActionFilesTab)
                <flux:table
                    container:class="max-h-280 app-table scrollbar-gutter-auto border-b-1 border-zinc-200 dark:border-zinc-700 mb-3 pb-2"
                >
                    <flux:table.columns
                        class="bg-zinc-800 text-zinc-400"
                        sticky
                    >
                        <flux:table.column
                            class="w-14 tabular-nums"
                            align="center"
                        >
                            <flux:icon.tally-5
                                class="ml-3"
                                stroke-width="1"
                            />
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'file'"
                            :direction="$sortDirection"
                            wire:click="sortBy('file')"
                        >
                            {{ __('File') }}
                        </flux:table.column>

                        <flux:table.column
                            class="w-px whitespace-nowrap"
                            align="center"
                            sortable
                            :sorted="$sortField === 'entries'"
                            :direction="$sortDirection"
                            wire:click="sortBy('entries')"
                        >
                            {{ __('Entries') }}
                        </flux:table.column>

                        <flux:table.column>
                            {{ __('IDs') }}
                        </flux:table.column>

                        <flux:table.column
                            class="w-px whitespace-nowrap"
                            align="center"
                            sortable
                            :sorted="$sortField === 'decisions'"
                            :direction="$sortDirection"
                            wire:click="sortBy('decisions')"
                        >
                            {{ __('Decision') }}
                        </flux:table.column>

                        <flux:table.column
                            class="w-px whitespace-nowrap"
                            align="center"
                            sortable
                            :sorted="$sortField === 'locales'"
                            :direction="$sortDirection"
                            wire:click="sortBy('locales')"
                        >
                            {{ __('Locales') }}
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'namespaces'"
                            :direction="$sortDirection"
                            wire:click="sortBy('namespaces')"
                        >
                            {{ __('Namespaces') }}
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'groups'"
                            :direction="$sortDirection"
                            wire:click="sortBy('groups')"
                        >
                            {{ __('Groups') }}
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'reasons'"
                            :direction="$sortDirection"
                            wire:click="sortBy('reasons')"
                        >
                            {{ __('Reasons') }}
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($actionFileRows as $actionFileRow)
                            @php
                                $translationKeyIds = collect($actionFileRow['translation_key_ids'] ?? [])
                                    ->filter()
                                    ->values();

                                $decisionStatuses = (array) ($actionFileRow['decision_statuses'] ?? []);
                            @endphp

                            <flux:table.row
                                wire:key="translation-lang-ballast-file-{{ md5((string) ($actionFileRow['file'] ?? $loop->index)) }}"
                            >
                                <flux:table.cell
                                    class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                    align="end"
                                >
                                    {{ $actionFileRows->firstItem() + $loop->index }}
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <code class="wrap-anywhere block text-xs">
                                        {{ $actionFileRow['file'] ?? '—' }}
                                    </code>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="center"
                                >
                                    <flux:badge
                                        class="tabular-nums"
                                        size="sm"
                                        variant="subtle"
                                        color="sky"
                                    >
                                        {{ number_format((int) ($actionFileRow['entries'] ?? 0)) }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($translationKeyIds->take(8) as $translationKeyId)
                                            <flux:badge
                                                class="tabular-nums"
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                #{{ $translationKeyId }}
                                            </flux:badge>
                                        @empty
                                            —
                                        @endforelse

                                        @if ($translationKeyIds->count() > 8)
                                            <flux:badge
                                                class="tabular-nums"
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                &hellip; +{{ $translationKeyIds->count() - 8 }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="center"
                                >
                                    <div class="flex flex-col items-center gap-1">
                                        @foreach ($decisionStatusMeta as $decisionStatusKey => $decisionStatus)
                                            @php
                                                $decisionStatusCount =
                                                    (int) ($decisionStatuses[$decisionStatusKey] ?? 0);
                                            @endphp

                                            @if ($decisionStatusCount > 0)
                                                <flux:badge
                                                    class="tabular-nums"
                                                    size="sm"
                                                    variant="subtle"
                                                    color="{{ $decisionStatus['color'] }}"
                                                >
                                                    {{ $decisionStatus['label'] }}
                                                    {{ number_format($decisionStatusCount) }}
                                                </flux:badge>
                                            @endif
                                        @endforeach
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="center"
                                >
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @forelse (($actionFileRow['locales'] ?? []) as $locale)
                                            @php
                                                $displayLocale = trim((string) $locale);
                                            @endphp

                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                <span class="inline-flex items-center gap-1">
                                                    <x-ui.locale.flag
                                                        :locale="$displayLocale"
                                                        size="xs"
                                                    />

                                                    <span class="font-mono font-semibold uppercase">
                                                        {{ $displayLocale }}
                                                    </span>
                                                </span>
                                            </flux:badge>
                                        @empty
                                            —
                                        @endforelse
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse (($actionFileRow['namespaces'] ?? []) as $namespace)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="emerald"
                                            >
                                                {{ $namespace }}
                                            </flux:badge>
                                        @empty
                                            —
                                        @endforelse
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse (($actionFileRow['groups'] ?? []) as $group)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="violet"
                                            >
                                                {{ $group }}
                                            </flux:badge>
                                        @empty
                                            —
                                        @endforelse
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse (($actionFileRow['reason_details'] ?? []) as $reasonDetail => $count)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ $reasonDetail }}: {{ number_format((int) $count) }}
                                            </flux:badge>
                                        @empty
                                            —
                                        @endforelse
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="8">
                                    <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ __('No lang ballast files found for the selected filters. Run php artisan project:translations first if the audit files are missing.') }}
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:table
                    container:class="max-h-280 app-table scrollbar-gutter-auto border-b-1 border-zinc-200 dark:border-zinc-700 mb-3 pb-2 mr-4 -pr-4"
                >
                    <flux:table.columns
                        class="bg-zinc-800 text-zinc-400"
                        sticky
                    >
                        <flux:table.column
                            class="w-14 tabular-nums"
                            align="center"
                        >
                            <flux:icon.tally-5
                                class="ml-3"
                                stroke-width="1"
                            />
                        </flux:table.column>

                        <flux:table.column
                            class="w-px whitespace-nowrap"
                            align="center"
                        >
                            {{ __('admin.user_list.table.id') }}
                        </flux:table.column>

                        <flux:table.column
                            class="w-px whitespace-nowrap"
                            align="center"
                        >
                            {{ __('Locale') }}
                        </flux:table.column>

                        <flux:table.column
                            class="w-px whitespace-nowrap"
                            align="center"
                        >
                            {{ __('Decision') }}
                        </flux:table.column>

                        <flux:table.column>
                            {{ __('admin.translation_list.modal.namespace') }}
                        </flux:table.column>

                        <flux:table.column>
                            {{ __('admin.translation_list.modal.group') }}
                        </flux:table.column>

                        <flux:table.column>
                            {{ __('admin.translation_list.modal.source') }}
                        </flux:table.column>

                        <flux:table.column>
                            {{ __('admin.translation_list.modal_history.reason') }}
                        </flux:table.column>

                        <flux:table.column
                            class="w-24 whitespace-nowrap"
                            align="center"
                        >
                            {{ __('ui.labels.actions') }}
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($actionRows as $actionRow)
                            @php
                                $translationKeyId = (int) ($actionRow['translation_key_id'] ?? 0);
                                $translationValueId = (int) ($actionRow['translation_value_id'] ?? 0);
                                $locale = trim((string) ($actionRow['locale'] ?? ''));
                                $namespace = trim((string) ($actionRow['namespace'] ?? ''));
                                $group = trim((string) ($actionRow['group'] ?? ''));
                                $translationKey = trim((string) ($actionRow['key'] ?? ''));
                                $suggestedKey = trim((string) ($actionRow['suggested_key'] ?? ''));
                                $file = trim((string) ($actionRow['file'] ?? ''));
                                $fileKey = trim((string) ($actionRow['file_key'] ?? ''));
                                $value = trim((string) ($actionRow['value'] ?? ''));
                                $reasonDetail = trim((string) ($actionRow['reason_detail'] ?? ''));
                                $langFileActionReason = trim((string) ($actionRow['lang_file_action_reason'] ?? ''));
                                $keyStatus = trim((string) ($actionRow['key_status'] ?? ''));
                                $workflowStatus = trim((string) ($actionRow['workflow_status'] ?? ''));
                                $valueStatus = trim((string) ($actionRow['value_status'] ?? ''));
                                $isBaseDuplicate = (bool) ($actionRow['is_base_duplicate'] ?? false);
                                $candidateHash = trim((string) ($actionRow['candidate_hash'] ?? ''));
                                $decisionStatus = trim((string) ($actionRow['decision_status'] ?? 'open')) ?: 'open';
                                $decisionColor = match ($decisionStatus) {
                                    'approved' => 'emerald',
                                    'reviewed' => 'sky',
                                    'ignored' => 'zinc',
                                    default => 'amber',
                                };
                                $decisionLabel = match ($decisionStatus) {
                                    'approved' => __('Approved'),
                                    'reviewed' => __('admin.translation_list.modal_edit.reviewed'),
                                    'ignored' => __('Ignored'),
                                    default => __('admin.translation_list.filter.open'),
                                };
                            @endphp

                            <flux:table.row
                                wire:key="translation-lang-ballast-action-{{ $activeActionFilter }}-{{ md5($locale . '|' . $translationKey . '|' . $file . '|' . $fileKey) }}"
                            >
                                <flux:table.cell
                                    class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                    align="end"
                                >
                                    {{ $actionRows->firstItem() + $loop->index }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="center"
                                >
                                    @if ($translationKeyId > 0)
                                        <div class="flex flex-col items-center gap-1">
                                            <flux:badge
                                                class="tabular-nums"
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                #{{ $translationKeyId }}
                                            </flux:badge>

                                            @if ($translationValueId > 0)
                                                <flux:badge
                                                    class="tabular-nums"
                                                    size="sm"
                                                    variant="subtle"
                                                    color="zinc"
                                                >
                                                    V#{{ $translationValueId }}
                                                </flux:badge>
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="center"
                                >
                                    @if ($locale !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            <span class="inline-flex items-center gap-1">
                                                <x-ui.locale.flag
                                                    :locale="$locale"
                                                    size="xs"
                                                />

                                                <span class="font-mono font-semibold uppercase">
                                                    {{ $locale }}
                                                </span>
                                            </span>
                                        </flux:badge>
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="center"
                                >
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="{{ $decisionColor }}"
                                    >
                                        {{ $decisionLabel }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    @if ($namespace !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="emerald"
                                        >
                                            {{ $namespace }}
                                        </flux:badge>
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    @if ($group !== '')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="violet"
                                        >
                                            {{ $group }}
                                        </flux:badge>
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="max-w-2xl space-y-2">
                                        @if ($translationKey !== '')
                                            <div>
                                                <div
                                                    class="text-[0.7rem] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                                    {{ __('TranslationKey') }}
                                                </div>

                                                <code class="wrap-anywhere block text-xs">
                                                    {{ $translationKey }}
                                                </code>
                                            </div>
                                        @endif

                                        @if ($suggestedKey !== '' && $suggestedKey !== $translationKey)
                                            <div>
                                                <div
                                                    class="text-[0.7rem] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                                    {{ __('admin.translation_list.table.suggested_key') }}
                                                </div>

                                                <code class="wrap-anywhere block text-xs">
                                                    {{ $suggestedKey }}
                                                </code>
                                            </div>
                                        @endif

                                        <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-2">
                                            @if ($file !== '')
                                                <span
                                                    class="shrink-0 text-xs font-semibold text-zinc-500 dark:text-zinc-400"
                                                >
                                                    {{ __('admin.translation_list.modal.path') }}:
                                                </span>

                                                <code
                                                    class="wrap-anywhere min-w-0 max-w-full whitespace-normal text-xs"
                                                >
                                                    {{ $file }}
                                                </code>
                                            @endif

                                            @if ($fileKey !== '')
                                                <flux:badge
                                                    class="font-mono"
                                                    size="sm"
                                                    variant="subtle"
                                                    color="sky"
                                                >
                                                    {{ $fileKey }}
                                                </flux:badge>
                                            @endif
                                        </div>

                                        @if ($value !== '')
                                            <div class="wrap-anywhere text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $value }}
                                            </div>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex flex-col items-start gap-1">
                                        @if ($reasonDetail !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ $reasonDetail }}
                                            </flux:badge>
                                        @endif

                                        @if ($langFileActionReason !== '' && $langFileActionReason !== $reasonDetail)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ $langFileActionReason }}
                                            </flux:badge>
                                        @endif

                                        @if ($keyStatus !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ $keyStatus === 'obsolete' ? 'red' : 'zinc' }}"
                                            >
                                                {{ __('admin.translation_list.table.key') }}: {{ $keyStatus }}
                                            </flux:badge>
                                        @endif

                                        @if ($workflowStatus !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ $workflowStatus === 'reviewed' ? 'emerald' : 'zinc' }}"
                                            >
                                                {{ __('admin.translation_list.meta.workflow') }}:
                                                {{ $workflowStatus }}
                                            </flux:badge>
                                        @endif

                                        @if ($valueStatus !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ $valueStatus === 'ok' ? 'emerald' : 'amber' }}"
                                            >
                                                {{ __('Value') }}: {{ $valueStatus }}
                                            </flux:badge>
                                        @endif

                                        @if ($isBaseDuplicate)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="blue"
                                            >
                                                {{ __('Sub-language duplicate') }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="w-px whitespace-nowrap align-top"
                                    align="end"
                                >
                                    @if ($activeActionFilter === 'base_duplicates')
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="blue"
                                        >
                                            {{ __('No lang action') }}
                                        </flux:badge>
                                    @elseif ($candidateHash !== '')
                                        <div class="flex flex-col items-center gap-1">
                                            <flux:button
                                                type="button"
                                                {{-- size="xs" --}}
                                                variant="{{ $decisionStatus === 'reviewed' ? 'primary' : 'ghost' }}"
                                                color="sky"
                                                wire:click="setDecisionStatus('{{ $candidateHash }}', 'reviewed')"
                                            >
                                                {{ __('ui.button.review.review') }}
                                            </flux:button>

                                            <flux:button
                                                type="button"
                                                {{-- size="xs" --}}
                                                variant="{{ $decisionStatus === 'approved' ? 'primary' : 'ghost' }}"
                                                color="emerald"
                                                wire:click="setDecisionStatus('{{ $candidateHash }}', 'approved')"
                                            >
                                                {{ __('Approve') }}
                                            </flux:button>

                                            <flux:button
                                                type="button"
                                                {{-- size="xs" --}}
                                                variant="{{ $decisionStatus === 'ignored' ? 'primary' : 'ghost' }}"
                                                color="zinc"
                                                wire:click="setDecisionStatus('{{ $candidateHash }}', 'ignored')"
                                            >
                                                {{ __('Ignore') }}
                                            </flux:button>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="9">
                                    <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ __('No language-ballast entries found for the selected filters.') }}
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>

        @if ($tableRows->hasPages())
            <flux:separator
                class=""
                text="{{ __('admin.client_list.table.pagination') }}"
            />

            {{-- Pagination --}}
            <div class="mt-4">
                <x-ui.table.pagination
                    :paginator="$tableRows"
                    scroll-to="#translation-lang-ballast-pagination-top"
                />
            </div>
        @endif

    </div>
</flux:card>
