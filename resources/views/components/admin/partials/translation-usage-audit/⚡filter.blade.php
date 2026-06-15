{{-- resources/views/components/admin/partials/translation-usage-audit/⚡filter.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filters')"
        :description="__('Refine the usage audit list by audit type, text, UI candidate state, and stale usage state.')"
    >
        @php
            $manualNeedsKeyItems = $manualNeedsKeyItems ?? collect();

            $auditTypeTotal = $duplicateItems->count() + $frequentItems->count() + $manualNeedsKeyItems->count();

            $activeAuditItems = match ($activeTab) {
                'manual_needs_key' => $manualNeedsKeyItems,
                'frequent' => $frequentItems,
                default => $duplicateItems,
            };

            $withoutUiCandidateCount = $activeAuditItems
                ->filter(static fn(array $item): bool => !(bool) ($item['already_has_ui_candidate'] ?? false))
                ->count();

            $withStaleUsagesCount = $activeAuditItems
                ->filter(static fn(array $item): bool => (bool) ($item['has_stale_usages'] ?? false))
                ->count();

            $allUsageAuditDecisionIndex = $allUsageAuditDecisionIndex ?? collect();

            $savedDecisionCount = $activeUsageAuditDecisionIndex->count();
            $newDecisionCount = max(0, $activeAuditItems->count() - $savedDecisionCount);
            $draftDecisionCount = $activeUsageAuditDecisionIndex
                ->filter(static fn($decision): bool => $decision->decision_status === 'draft')
                ->count();
            $readyDecisionCount = $activeUsageAuditDecisionIndex
                ->filter(static fn($decision): bool => $decision->decision_status === 'ready')
                ->count();
            $appliedDecisionCount = $activeUsageAuditDecisionIndex
                ->filter(static fn($decision): bool => $decision->decision_status === 'applied')
                ->count();

            $manualNeedsKeyDecisionCount = $manualNeedsKeyItems->count();

            $auditNeedsKeyDecisionCount = $allUsageAuditDecisionIndex
                ->filter(
                    static fn($decision): bool => $decision->audit_type !== 'manual_needs_key' &&
                        ($decision->decision_status === 'needs_key' || $decision->decision_action === 'create_new_key'),
                )
                ->count();

            $needsKeyDecisionCount = $auditNeedsKeyDecisionCount + $manualNeedsKeyDecisionCount;
            $skippedDecisionCount = $activeUsageAuditDecisionIndex
                ->filter(static fn($decision): bool => $decision->decision_action === 'skip')
                ->count();

            $auditTooltips = [
                'duplicate' => [
                    'title' => __('Duplicate usage literals'),
                    'text' => __(
                        'Source-language literals that are assigned to multiple translation keys and are strong centralization candidates.',
                    ),
                    'color' => 'amber',
                ],
                'frequent' => [
                    'title' => __('Frequent usage literals'),
                    'text' => __(
                        'Source-language literals that are used frequently and may be candidates for common UI translations.',
                    ),
                    'color' => 'sky',
                ],
                'manual_needs_key' => [
                    'title' => __('Manual needs key'),
                    'text' => __(
                        'Translation keys that were manually marked as needing a new key from the translation list.',
                    ),
                    'color' => 'amber',
                ],
            ];
        @endphp

        <div class="mb-6 grid gap-2 xl:grid-cols-[1fr_auto]">
            <div class="space-y-2">
                {{-- Audit type filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('All audit types')"
                            :text="__('Show all items regardless of their audit type.')"
                        >
                            {{ __('Audit') }}
                        </x-ui.tooltip.trigger>
                    </span>

                    <x-ui.tooltip.trigger
                        :title="$auditTooltips['duplicate']['title']"
                        :text="$auditTooltips['duplicate']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$activeTab === 'duplicate' ? 'primary' : 'ghost'"
                            :color="$activeTab === 'duplicate' ? ($auditTooltips['duplicate']['color'] ?? null) : null"
                            wire:click="setActiveTab('duplicate')"
                        >
                            {{ __('Duplicate') }}
                            <span class="ml-1 opacity-70">
                                {{ $duplicateItems->count() }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="$auditTooltips['frequent']['title']"
                        :text="$auditTooltips['frequent']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$activeTab === 'frequent' ? 'primary' : 'ghost'"
                            :color="$activeTab === 'frequent' ? ($auditTooltips['frequent']['color'] ?? null) : null"
                            wire:click="setActiveTab('frequent')"
                        >
                            {{ __('Frequent') }}
                            <span class="ml-1 opacity-70">
                                {{ $frequentItems->count() }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="$auditTooltips['manual_needs_key']['title']"
                        :text="$auditTooltips['manual_needs_key']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$activeTab === 'manual_needs_key' ? 'primary' : 'ghost'"
                            :color="$activeTab === 'manual_needs_key' ? ($auditTooltips['manual_needs_key']['color'] ?? null) :
                                null"
                            wire:click="setActiveTab('manual_needs_key')"
                        >
                            {{ __('Manual needs key') }}
                            <span class="ml-1 opacity-70">
                                {{ $manualNeedsKeyItems->count() }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <flux:badge
                        size="sm"
                        variant="subtle"
                        color="zinc"
                    >
                        {{ __('Total') }} {{ $auditTypeTotal }}
                    </flux:badge>
                </div>

                {{-- Decision state filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <x-ui.tooltip.trigger
                        :title="__('All decisions')"
                        :text="__('Show all items regardless of their usage audit decision state.')"
                    >
                        <span
                            class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                        >
                            {{ __('Decision') }}
                        </span>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('New items')"
                        :text="__('Items that do not have a saved usage audit decision yet.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'all' ? 'primary' : 'ghost'"
                            :color="$decisionFilter === 'all' ? 'sky' : null"
                            wire:click="$set('decisionFilter', 'all')"
                        >
                            {{ __('ui.states.all') }}
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('New items')"
                        :text="__('Items that do not have a saved usage audit decision yet.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'new' ? 'primary' : 'ghost'"
                            :color="$decisionFilter === 'new' ? 'lime' : null"
                            wire:click="$set('decisionFilter', 'new')"
                        >
                            {{ __('New') }}
                            <span class="ml-1 opacity-70">{{ $newDecisionCount }}</span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('Saved decisions')"
                        :text="__('Items that have a saved usage audit decision in any state.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'saved' ? 'primary' : 'ghost'"
                            :color="$decisionFilter === 'saved' ? 'red' : null"
                            wire:click="$set('decisionFilter', 'saved')"
                        >
                            {{ __('Saved') }}
                            <span class="ml-1 opacity-70">{{ $savedDecisionCount }}</span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('Draft decisions')"
                        :text="__('Items that have a saved usage audit decision in the draft state.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'draft' ? 'primary' : 'ghost'"
                            :color="$decisionFilter === 'draft' ? 'yellow' : null"
                            wire:click="$set('decisionFilter', 'draft')"
                        >
                            {{ __('Draft') }}
                            <span class="ml-1 opacity-70">{{ $draftDecisionCount }}</span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('Ready decisions')"
                        :text="__('Items that have a saved usage audit decision in the ready state.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'ready' ? 'primary' : 'ghost'"
                            color="{{ $decisionFilter === 'ready' ? 'emerald' : null }}"
                            wire:click="$set('decisionFilter', 'ready')"
                        >
                            {{ __('Ready') }}
                            <span class="ml-1 opacity-70">{{ $readyDecisionCount }}</span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('Applied decisions')"
                        :text="__('Items that have a saved usage audit decision in the applied state.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'applied' ? 'primary' : 'ghost'"
                            :color="$decisionFilter === 'applied' ? 'sky' : null"
                            wire:click="$set('decisionFilter', 'applied')"
                        >
                            {{ __('Applied') }}
                            <span class="ml-1 opacity-70">{{ $appliedDecisionCount }}</span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('Needs key decisions')"
                        :text="__(
                            'Items that have a saved usage audit decision in the needs_key state, or items that are in the manual needs key audit and do not have a saved decision yet.',
                        )"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'needs_key' ? 'primary' : 'ghost'"
                            color="{{ $decisionFilter === 'needs_key' ? 'amber' : null }}"
                            wire:click="$set('decisionFilter', 'needs_key')"
                        >
                            {{ __('Needs key') }}
                            <span class="ml-1 tabular-nums opacity-70">
                                {{ $auditNeedsKeyDecisionCount }}/{{ $manualNeedsKeyDecisionCount }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="__('Skipped decisions')"
                        :text="__('Items that have a saved usage audit decision with the skip action.')"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$decisionFilter === 'skipped' ? 'primary' : 'ghost'"
                            :color="$decisionFilter === 'skipped' ? 'violet' : null"
                            wire:click="$set('decisionFilter', 'skipped')"
                        >
                            {{ __('Skipped') }}
                            <span class="ml-1 opacity-70">{{ $skippedDecisionCount }}</span>
                        </flux:button>
                    </x-ui.tooltip.trigger>
                </div>
            </div>

            {{-- Quick focus filters --}}
            <div class="grid gap-2">
                <div
                    class="min-w-58 flex items-center justify-start rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
                    <flux:field variant="inline">
                        <x-ui.tooltip.trigger
                            :title="__('Without UI candidate')"
                            :text="__('Show only items that do not have a UI candidate yet.')"
                        >
                            <flux:switch
                                class="switch-colored mr-3 hover:cursor-pointer"
                                wire:click="toggleOnlyWithoutUiCandidate"
                                :checked="$onlyWithoutUiCandidate"
                            />

                            <flux:label
                                class="text-sm opacity-70 hover:cursor-pointer"
                                wire:click="toggleOnlyWithoutUiCandidate"
                            >
                                {{ __('Without UI candidate') }}

                                <span class="ml-1 opacity-70">
                                    {{ $withoutUiCandidateCount }}
                                </span>
                            </flux:label>
                        </x-ui.tooltip.trigger>
                    </flux:field>
                </div>

                <div
                    class="min-w-58 flex items-center justify-start rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
                    <flux:field variant="inline">
                        <x-ui.tooltip.trigger
                            :title="__('With stale usages')"
                            :text="__('Show only items that have usages that might be stale and need review.')"
                        >
                            <flux:switch
                                class="switch-colored mr-3 hover:cursor-pointer"
                                wire:click="toggleOnlyWithStaleUsages"
                                :checked="$onlyWithStaleUsages"
                            />

                            <flux:label
                                class="text-sm opacity-70 hover:cursor-pointer"
                                wire:click="toggleOnlyWithStaleUsages"
                            >
                                {{ __('With stale usages') }}

                                <span class="ml-1 opacity-70">
                                    {{ $withStaleUsagesCount }}
                                </span>
                            </flux:label>
                        </x-ui.tooltip.trigger>
                    </flux:field>
                </div>
            </div>
        </div>
    </x-ui.headers.card>

    <div class="flex w-full flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1 basis-80">
            <flux:label
                class="pb-1"
                for="translation-usage-audit-search"
            >
                <x-ui.tooltip.trigger
                    :title="__('ui.actions.search')"
                    :text="__(
                        'Search by source value, normalized value, UI key, or suggested key. Text search is case-insensitive and matches substrings.',
                    )"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="translation-usage-audit-search"
                    type="text"
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search source value, normalized value, UI key, or suggested key') }}"
                />
            </flux:input.group>
        </div>

        <div class="flex-none">
            <flux:label
                class="pb-1"
                for="translation-usage-audit-min-current-usages"
            >
                <x-ui.tooltip.trigger
                    :title="__('Min current usages')"
                    :text="__(
                        'Show only items that have at least this many current usages in the system. This can help focus on items with more impact.',
                    )"
                >
                    {{ __('Min current usages') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.arrow-down-0-1 stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="translation-usage-audit-min-current-usages"
                    type="number"
                    min="0"
                    wire:model.live.debounce.300ms="minCurrentUsages"
                />
            </flux:input.group>

        </div>

        <div class="ml-auto flex-none basis-56">
            <x-ui.table.per-page-selector
                id="translation-usage-audit-per-page"
                name="translation-usage-audit-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <flux:badge
            color="sky"
            variant="subtle"
        >
            {{ __('admin.translation_list.filter.showing') }} {{ number_format($translationUsageItems->count()) }}
            {{ __('admin.translation_list.filter.of') }} {{ number_format($activeItems->count()) }}
        </flux:badge>

        @if (
            $search !== '' ||
                $onlyWithoutUiCandidate ||
                $onlyWithStaleUsages ||
                $decisionFilter !== 'all' ||
                (int) $minCurrentUsages > 0)
            <flux:badge
                color="amber"
                variant="subtle"
            >
                {{ __('admin.translation_list.filter.filters_active') }}
            </flux:badge>
        @endif
    </div>
</flux:card>
