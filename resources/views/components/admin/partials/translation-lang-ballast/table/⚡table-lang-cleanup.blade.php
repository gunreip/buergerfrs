{{-- resources/views/components/admin/partials/translation-lang-ballast/table/⚡table-lang-cleanup.blade.php --}}

{{-- Table Lang Cleanup --}}
<flux:table container:class="app-table">
    {{-- Table Header Row --}}
    <flux:table.columns
        class="bg-zinc-800 text-zinc-400"
        sticky
    >
        {{-- Table Header  --}}
        <flux:table.column
            class="w-14 tabular-nums"
            align="center"
        >
            <flux:icon.tally-5
                class="ml-3"
                stroke-width="1"
            />
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column
            class="w-px whitespace-nowrap"
            align="center"
        >
            {{ __('admin.user_list.table.id') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column
            class="w-px whitespace-nowrap"
            align="center"
        >
            {{ __('Locale') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column
            class="w-px whitespace-nowrap"
            align="center"
        >
            {{ __('Decision') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column>
            {{ __('admin.translation_list.modal.namespace') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column>
            {{ __('admin.translation_list.modal.group') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column>
            {{ __('admin.translation_list.modal.source') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column>
            {{ __('admin.translation_list.modal_history.reason') }}
        </flux:table.column>

        {{-- Table Header  --}}
        <flux:table.column
            class="w-24 whitespace-nowrap"
            align="center"
        >
            {{ __('ui.table.headers.actions') }}
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
                    'approved' => __('ui.approved'),
                    'reviewed' => __('admin.translation_list.modal_edit.reviewed'),
                    'ignored' => __('Ignored'),
                    default => __('ui.open'),
                };
            @endphp

            {{-- Table Row Data --}}
            <flux:table.row
                wire:key="translation-lang-ballast-action-{{ $activeActionFilter }}-{{ md5($locale . '|' . $translationKey . '|' . $file . '|' . $fileKey) }}"
            >
                {{-- Table Cell  --}}
                <flux:table.cell
                    class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                    align="end"
                >
                    {{ $actionRows->firstItem() + $loop->index }}
                </flux:table.cell>

                {{-- Table Cell  --}}
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
                        <x-ui.badge.no-value />
                    @endif
                </flux:table.cell>

                {{-- Table Cell  --}}
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

                {{-- Table Cell  --}}
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

                {{-- Table Cell  --}}
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
                        <x-ui.badge.no-value />
                    @endif
                </flux:table.cell>

                {{-- Table Cell  --}}
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
                        <x-ui.badge.no-value />
                    @endif
                </flux:table.cell>

                {{-- Table Cell  --}}
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
                                <span class="shrink-0 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                                    {{ __('admin.translation_list.modal.path') }}:
                                </span>

                                <code class="wrap-anywhere min-w-0 max-w-full whitespace-normal text-xs">
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

                {{-- Table Cell  --}}
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

                {{-- Table Cell  --}}
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
                                variant="{{ $decisionStatus === 'reviewed' ? 'primary' : 'ghost' }}"
                                color="sky"
                                wire:click="setDecisionStatus('{{ $candidateHash }}', 'reviewed')"
                            >
                                {{ __('ui.button.review.review') }}
                            </flux:button>

                            <flux:button
                                type="button"
                                variant="{{ $decisionStatus === 'approved' ? 'primary' : 'ghost' }}"
                                color="emerald"
                                wire:click="setDecisionStatus('{{ $candidateHash }}', 'approved')"
                            >
                                {{ __('Approve') }}
                            </flux:button>

                            <flux:button
                                type="button"
                                variant="{{ $decisionStatus === 'ignored' ? 'primary' : 'ghost' }}"
                                color="zinc"
                                wire:click="setDecisionStatus('{{ $candidateHash }}', 'ignored')"
                            >
                                {{ __('Ignore') }}
                            </flux:button>
                        </div>
                    @else
                        <x-ui.badge.no-value />
                    @endif
                </flux:table.cell>
            </flux:table.row>
        @empty
            {{-- Table Row No Data --}}
            <flux:table.row>
                {{-- Table Cell No Data --}}
                <flux:table.cell colspan="9">
                    {{-- Table Cell Content No Data --}}
                    <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No language-ballast entries found for the selected filters.') }}
                    </div>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>

</flux:table>
