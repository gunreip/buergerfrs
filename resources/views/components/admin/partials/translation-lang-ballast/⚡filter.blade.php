{{-- resources/views/components/admin/partials/translation-lang-ballast/⚡filter.blade.php --}}

@php
    $summary = $summary ?? [];
    $activeActionFilter = $activeActionFilter ?? 'action_files';
    $namespaceOptions = $namespaceOptions ?? [];
    $groupOptions = $groupOptions ?? [];
    $localeOptions = $localeOptions ?? [];
    $decisionFilter = $decisionFilter ?? 'all';

    $decisionCards = [
        [
            'key' => 'all',
            'label' => __('ui.states.all'),
            'count' => (int) data_get($summary, 'decisions.total_entries', 0),
            'color' => 'zinc',
        ],
        [
            'key' => 'open',
            'label' => __('ui.open'),
            'count' => (int) data_get($summary, 'decision_open_entries', 0),
            'color' => 'amber',
        ],
        [
            'key' => 'reviewed',
            'label' => __('admin.translation_list.modal_edit.reviewed'),
            'count' => (int) data_get($summary, 'decision_reviewed_entries', 0),
            'color' => 'sky',
        ],
        [
            'key' => 'approved',
            'label' => __('ui.labels.approved'),
            'count' => (int) data_get($summary, 'decision_approved_entries', 0),
            'color' => 'emerald',
        ],
        [
            'key' => 'ignored',
            'label' => __('Ignored'),
            'count' => (int) data_get($summary, 'decision_ignored_entries', 0),
            'color' => 'zinc',
        ],
    ];

    $actionTypeCards = [
        [
            'key' => 'action_files',
            'label' => __('Action files'),
            'count' =>
                (int) data_get($summary, 'action_file_remove_candidate_files', 0) +
                (int) data_get($summary, 'action_file_add_candidate_files', 0) +
                (int) data_get($summary, 'action_file_review_candidate_files', 0),
            'color' => 'sky',
        ],
        [
            'key' => 'remove',
            'label' => __('Lang cleanup'),
            'count' => (int) data_get(
                $summary,
                'lang_file_cleanup_candidate_entries',
                data_get($summary, 'lang_file_remove_candidate_entries', 0),
            ),
            'color' => 'red',
        ],
        [
            'key' => 'add',
            'label' => __('Missing in lang'),
            'count' => (int) data_get($summary, 'lang_file_add_candidate_entries', 0),
            'color' => 'amber',
        ],
        [
            'key' => 'base_duplicates',
            'label' => __('Sub-language duplicates'),
            'count' => (int) data_get($summary, 'sub_language_base_duplicate_entries', 0),
            'color' => 'blue',
        ],
        [
            'key' => 'review',
            'label' => __('Needs review'),
            'count' => (int) data_get($summary, 'lang_file_review_candidate_entries', 0),
            'color' => 'violet',
        ],
    ];
@endphp

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filters')"
        :description="__(
            'Refine lang file cleanup candidates, missing lang entries, and review cases by namespace, group, locale, and text search.',
        )"
    >
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                <span
                    class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                >
                    <x-ui.tooltip.trigger
                        :title="__('ui.nouns.view')"
                        :text="__(
                            'Switch between affected lang files, lang cleanup candidates, missing lang entries, redundant sub-language values, and entries that need review.',
                        )"
                    >
                        {{ __('ui.nouns.view') }}
                    </x-ui.tooltip.trigger>
                </span>

                @foreach ($actionTypeCards as $actionTypeCard)
                    <flux:button
                        type="button"
                        size="sm"
                        :variant="$activeActionFilter === $actionTypeCard['key'] ? 'primary' : 'ghost'"
                        :color="$activeActionFilter === $actionTypeCard['key'] ? $actionTypeCard['color'] : null"
                        wire:click="setActionFilter('{{ $actionTypeCard['key'] }}')"
                    >
                        {{ $actionTypeCard['label'] }}
                        <span class="ml-1 opacity-70">
                            {{ number_format((int) $actionTypeCard['count']) }}
                        </span>
                    </flux:button>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                <span
                    class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                >
                    <x-ui.tooltip.trigger
                        :title="__('Decision')"
                        :text="__('Filter candidates by current lang ballast review decision status.')"
                    >
                        {{ __('Decision') }}
                    </x-ui.tooltip.trigger>
                </span>

                @foreach ($decisionCards as $decisionCard)
                    <flux:button
                        type="button"
                        size="sm"
                        :variant="$decisionFilter === $decisionCard['key'] ? 'primary' : 'ghost'"
                        :color="$decisionFilter === $decisionCard['key'] ? $decisionCard['color'] : null"
                        wire:click="setDecisionFilter('{{ $decisionCard['key'] }}')"
                    >
                        {{ $decisionCard['label'] }}
                        <span class="ml-1 opacity-70">
                            {{ number_format((int) $decisionCard['count']) }}
                        </span>
                    </flux:button>
                @endforeach
            </div>
        </div>
    </x-ui.headers.card>

    <div class="mt-4 grid w-full gap-3 xl:grid-cols-[minmax(0,1fr)_18rem_18rem_16rem_auto_auto]">
        <div class="min-w-0">
            <flux:label
                class="pb-1"
                for="translation-lang-ballast-search"
            >
                <x-ui.tooltip.trigger
                    :title="__('ui.actions.search')"
                    :text="__('Search by file, key, suggested key, namespace, group, locale, or value.')"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="translation-lang-ballast-search"
                    type="text"
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search file, key, suggested key, namespace, group, locale, or value') }}"
                />
            </flux:input.group>
        </div>

        <flux:field>
            <flux:label>
                {{ __('admin.translation_list.modal.namespace') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.folder-pen stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-lang-ballast-namespace"
                    name="translation-lang-ballast-namespace"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="namespaceFilter"
                >
                    <flux:select.option value="all">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($namespaceOptions as $namespaceOption)
                        <flux:select.option value="{{ $namespaceOption }}">
                            {{ $namespaceOption }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field>
            <flux:label>{{ __('admin.translation_list.modal.group') }}</flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.save-pen stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-lang-ballast-group"
                    name="translation-lang-ballast-group"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="groupFilter"
                >
                    <flux:select.option value="all">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($groupOptions as $groupOption)
                        <flux:select.option value="{{ $groupOption }}">
                            {{ $groupOption }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Locale') }}</flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.language stroke-width="1" />
                </flux:input.group.prefix>
                <flux:select
                    id="translation-lang-ballast-locale"
                    name="translation-lang-ballast-locale"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="localeFilter"
                >
                    <flux:select.option value="all">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($localeOptions as $localeOption)
                        <flux:select.option value="{{ $localeOption }}">
                            <div class="flex items-center gap-2">
                                <x-ui.locale.flag :locale="$localeOption" />
                                <span class="ml-2">{{ $localeOption }}</span>
                            </div>
                            {{-- {{ $localeOption }} --}}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        <div>
            <x-ui.table.per-page-selector
                id="translation-lang-ballast-per-page"
                name="translation-lang-ballast-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex items-end">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <flux:badge
            color="sky"
            variant="subtle"
        >
            {{ __('Preview mode') }}
        </flux:badge>

        <flux:badge
            color="zinc"
            variant="subtle"
        >
            {{ __('No write actions') }}
        </flux:badge>

        @if (
            $activeActionFilter !== 'action_files' ||
                trim((string) ($search ?? '')) !== '' ||
                ($namespaceFilter ?? 'all') !== 'all' ||
                ($groupFilter ?? 'all') !== 'all' ||
                ($localeFilter ?? 'all') !== 'all' ||
                ($decisionFilter ?? 'all') !== 'all')
            <flux:badge
                color="amber"
                variant="subtle"
            >
                {{ __('admin.translation_list.filter.filters_active') }}
            </flux:badge>
        @endif
    </div>
</flux:card>
