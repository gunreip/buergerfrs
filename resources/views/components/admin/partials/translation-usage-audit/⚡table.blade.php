{{-- resources/views/components/admin/partials/translation-usage-audit/⚡table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Usage literals')"
        :description="__('Source-language literals from the selected audit report.')"
    >

    </x-ui.headers.card>

    @if ($translationUsageItems->hasPages())
        {{-- Pagination --}}
        <div
            class="mt-4"
            id="translation-usage-audit-pagination-top"
        >
            <x-ui.table.pagination
                :paginator="$translationUsageItems"
                scroll-to="#translation-usage-audit-pagination-top"
            />
        </div>
    @endif

    <div
        class="mx-auto max-w-full scroll-mt-6"
        id="translation-usage-audit-table"
    >

        <div class="overflow-hidden rounded-t-lg">

            {{-- Table --}}
            {{-- Source value, keys, usages, current, stale, UI candidate, UI keys, decision, actions --}}
            <flux:table
                container:class="max-h-280 app-table scrollbar-gutter-auto border-b-1 border-zinc-200 dark:border-zinc-700 mb-3 pb-2"
            >

                {{-- Table Headers  --}}
                <flux:table.columns
                    class="bg-zinc-800 text-zinc-400"
                    sticky
                >

                    {{-- Sequence Number (not sortable, reflects current order in the list, useful for reference and identification) --}}
                    <flux:table.column
                        class="w-14 tabular-nums"
                        align="center"
                    >
                        <flux:icon.tally-5
                            class="ml-3"
                            stroke-width="1"
                        />

                    </flux:table.column>

                    {{-- Column ID --}}
                    <flux:table.column
                        class="w-32 tabular-nums"
                        sortable
                        :sorted="$sortField === 'id'"
                        :direction="$sortDirection"
                        align="center"
                        wire:click="sortBy('id')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('admin.user_list.table.id')"
                            :text="__('Identifier of the first associated translation key, if available')"
                        >
                            {{ __('admin.user_list.table.id') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Source value --}}
                    <flux:table.column
                        class="w-(--translation-balanced-column-width)"
                        sortable
                        :sorted="$sortField === 'value'"
                        :direction="$sortDirection"
                        wire:click="sortBy('value')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('Source value')"
                            :text="__('The original text of the source value')"
                        >
                            {{ __('Source value') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Keys --}}
                    <flux:table.column
                        class="w-px whitespace-nowrap"
                        align="center"
                        sortable
                        :sorted="$sortField === 'translation_key_count'"
                        :direction="$sortDirection"
                        wire:click="sortBy('translation_key_count')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('Keys')"
                            :text="__('Number of translation keys associated with the source value')"
                        >
                            {{ __('Keys') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Usages --}}
                    <flux:table.column
                        class="w-px whitespace-nowrap"
                        align="center"
                        sortable
                        :sorted="$sortField === 'usage_count_total'"
                        :direction="$sortDirection"
                        wire:click="sortBy('usage_count_total')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('admin.translation_list.modal.usages')"
                            :text="__('Number of usages of the translation key')"
                        >
                            {{ __('admin.translation_list.modal.usages') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Current --}}
                    <flux:table.column
                        class="w-px whitespace-nowrap"
                        align="center"
                        sortable
                        :sorted="$sortField === 'usage_count_current'"
                        :direction="$sortDirection"
                        wire:click="sortBy('usage_count_current')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('ui.current')"
                            :text="__('Current usages of the translation key')"
                        >
                            {{ __('ui.current') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Stale --}}
                    <flux:table.column
                        class="w-px whitespace-nowrap"
                        align="center"
                        sortable
                        :sorted="$sortField === 'usage_count_stale'"
                        :direction="$sortDirection"
                        wire:click="sortBy('usage_count_stale')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('Stale')"
                            :text="__('Stale usages of the translation key')"
                        >
                            {{ __('Stale') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column UI candidate --}}
                    <flux:table.column
                        align="center"
                        sortable
                        :sorted="$sortField === 'already_has_ui_candidate'"
                        :direction="$sortDirection"
                        wire:click="sortBy('already_has_ui_candidate')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('UI candidate')"
                            :text="__(
                                'Indicates whether there is an existing UI translation key for the source value',
                            )"
                        >
                            {{ __('UI candidate') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column UI keys --}}
                    <flux:table.column sortable>
                        <x-ui.tooltip.trigger
                            class="w-(--translation-balanced-column-width)"
                            :title="__('UI keys')"
                            :text="__(
                                'Suggested UI key, existing UI keys, and saved decision target key for this source-language literal',
                            )"
                        >
                            {{ __('UI keys') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Decision --}}
                    <flux:table.column
                        class="w-px whitespace-nowrap"
                        align="center"
                        sortable
                        :sorted="$sortField === 'decision'"
                        :direction="$sortDirection"
                        wire:click="sortBy('decision')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('Decision')"
                            :text="__('Saved usage-audit decision state for this source-language literal')"
                        >
                            {{ __('Decision') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actions --}}
                    <flux:table.column
                        class="w-px whitespace-nowrap"
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('ui.table.headers.actions')"
                            :text="__('Available actions for the translation key')"
                        >
                            {{ __('ui.table.headers.actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($translationUsageItems as $item)
                        @php
                            $itemAuditType = (string) ($item['audit_type'] ?? $activeTab);
                            $normalizedValue = (string) ($item['normalized_value'] ?? '');
                            $normalizedValueHash = md5($normalizedValue);
                            $isManualNeedsKeyItem = $itemAuditType === 'manual_needs_key';
                            $hasUiCandidate = (bool) ($item['already_has_ui_candidate'] ?? false);
                            $hasStaleUsages = (bool) ($item['has_stale_usages'] ?? false);
                            $uiKeys = collect($item['ui_keys'] ?? [])
                                ->filter()
                                ->values();
                            $suggestedUiKey = (string) ($item['suggested_ui_key'] ?? '');
                            $firstTranslationKeyId = (int) data_get($item, 'keys.0.translation_key_id', 0);
                            $usageAuditDecision = $usageAuditDecisionIndex->get(
                                $itemAuditType . '|' . $normalizedValueHash,
                            );
                            $hasMissingUiTranslationKey =
                                !$hasUiCandidate &&
                                $suggestedUiKey !== '' &&
                                $uiKeys->isEmpty() &&
                                trim((string) ($usageAuditDecision?->target_translation_key ?? '')) === '';
                        @endphp

                        {{-- Table row --}}
                        <flux:table.row
                            wire:key="translation-usage-audit-{{ $activeTab }}-{{ $normalizedValueHash }}"
                        >
                            {{-- Sequence Number --}}
                            <flux:table.cell
                                class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                align="end"
                            >
                                {{ $translationUsageItems->firstItem() + $loop->index }}
                            </flux:table.cell>

                            {{-- Cell ID --}}
                            <flux:table.cell
                                class="w-32 align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                align="end"
                            >
                                @if ($firstTranslationKeyId > 0)
                                    #{{ $firstTranslationKeyId }}
                                @else
                                    —
                                @endif
                            </flux:table.cell>

                            {{-- Source value --}}
                            <flux:table.cell class="align-top">
                                <div class="max-w-xl">
                                    <div class="hyphens-auto text-wrap font-medium">
                                        {{ $item['value'] ?? '—' }}
                                    </div>

                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ strtoupper((string) ($item['locale'] ?? '—')) }}
                                        </flux:badge>

                                        @if ($isManualNeedsKeyItem)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ __('Manual needs key') }}
                                            </flux:badge>
                                        @endif

                                        @if ($hasStaleUsages)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ __('stale') }}
                                            </flux:badge>
                                        @endif

                                        @if ((int) ($item['non_ui_translation_key_count'] ?? 0) > 0)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="sky"
                                            >
                                                {{ __('non-ui') }}
                                                {{ (int) ($item['non_ui_translation_key_count'] ?? 0) }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- Keys --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                <span class="tabular-nums">
                                    {{ (int) ($item['translation_key_count'] ?? 0) }}
                                </span>
                            </flux:table.cell>

                            {{-- Usages --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                <span class="font-semibold tabular-nums">
                                    {{ (int) ($item['usage_count_total'] ?? ($item['usage_count'] ?? 0)) }}
                                </span>
                            </flux:table.cell>

                            {{-- Current --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                <span class="tabular-nums text-emerald-700 dark:text-emerald-300">
                                    {{ (int) ($item['usage_count_current'] ?? 0) }}
                                </span>
                            </flux:table.cell>

                            {{-- Stale --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                <span class="tabular-nums text-amber-700 dark:text-amber-300">
                                    {{ (int) ($item['usage_count_stale'] ?? 0) }}
                                </span>
                            </flux:table.cell>

                            {{-- UI candidates --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                @if ($hasUiCandidate)
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="emerald"
                                    >
                                        {{ __('ui.filters.yes') }}
                                    </flux:badge>
                                @else
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="{{ $hasMissingUiTranslationKey ? 'amber' : 'zinc' }}"
                                    >
                                        {{ __('no') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            {{-- UI keys --}}
                            <flux:table.cell class="align-top">
                                @php
                                    $decisionTargetTranslationKey = trim(
                                        (string) ($usageAuditDecision?->target_translation_key ?? ''),
                                    );
                                @endphp

                                @if (
                                    $suggestedUiKey !== '' ||
                                        $decisionTargetTranslationKey !== '' ||
                                        $uiKeys->isNotEmpty() ||
                                        $hasMissingUiTranslationKey)
                                    <div class="max-w-md space-y-2">
                                        @if ($suggestedUiKey !== '')
                                            <div class="min-w-0">
                                                <div
                                                    class="text-[0.7rem] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                                    {{ __('admin.translation_list.table.suggested_key') }}
                                                </div>

                                                <code class="wrap-anywhere block text-xs">
                                                    {{ $suggestedUiKey }}
                                                </code>
                                            </div>
                                        @endif

                                        @if ($hasMissingUiTranslationKey)
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <flux:badge
                                                    size="sm"
                                                    variant="subtle"
                                                    color="amber"
                                                >
                                                    {{ __('Missing translation key') }}
                                                </flux:badge>

                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ __('Suggested key is not assigned to an existing UI translation key yet.') }}
                                                </span>
                                            </div>
                                        @endif

                                        @if ($decisionTargetTranslationKey !== '')
                                            <div class="min-w-0">
                                                <div
                                                    class="text-[0.7rem] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                                    {{ __('Decision UI key') }}
                                                </div>

                                                {{-- <code class="wrap-anywhere block text-xs">
                                                    {{ $decisionTargetTranslationKey }}
                                                </code> --}}
                                            </div>
                                        @endif

                                        @if ($uiKeys->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($uiKeys as $uiKey)
                                                    <flux:badge
                                                        class="font-mono"
                                                        size="sm"
                                                        variant="subtle"
                                                        color="emerald"
                                                    >
                                                        {{ $uiKey }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </flux:table.cell>

                            {{-- Decision --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                @if ($usageAuditDecision)
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="flex flex-wrap justify-center gap-1">
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ $this->usageAuditDecisionActionColor($usageAuditDecision->decision_action) }}"
                                            >
                                                {{ $this->usageAuditDecisionActionLabel($usageAuditDecision->decision_action) }}
                                            </flux:badge>

                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ $this->usageAuditDecisionStatusColor($usageAuditDecision->decision_status) }}"
                                            >
                                                {{ $this->usageAuditDecisionStatusLabel($usageAuditDecision->decision_status) }}
                                            </flux:badge>
                                        </div>

                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            #{{ $usageAuditDecision->id }}
                                        </flux:badge>
                                    </div>
                                @else
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="zinc"
                                    >
                                        {{ __('New') }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            {{-- Actions --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="end"
                            >
                                <div class="mr-4 inline-flex flex-col items-center gap-2">
                                    {{-- Review button --}}
                                    <x-ui.button.review
                                        size="sm"
                                        wire:click="openUsageAuditModal('{{ $itemAuditType }}', '{{ $normalizedValueHash }}')"
                                    >
                                        {{ __('ui.button.review.review') }}
                                    </x-ui.button.review>

                                    <x-ui.button.edit
                                        size="sm"
                                        icon="pencil"
                                        wire:click="openUsageAuditEditModal('{{ $itemAuditType }}', '{{ $normalizedValueHash }}')"
                                    >
                                        {{ __('admin.translation_list.modal.edit') }}
                                    </x-ui.button.edit>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="11">
                                <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No usage audit entries found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($translationUsageItems->hasPages())
            <flux:separator
                class=""
                text="{{ __('ui.pagination') }}"
            />

            {{-- Pagination --}}
            <div class="mt-4">
                <x-ui.table.pagination
                    :paginator="$translationUsageItems"
                    scroll-to="#translation-usage-audit-pagination-top"
                />
            </div>
        @endif

    </div>
</flux:card>
