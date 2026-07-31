{{-- resources/views/components/admin/partials/translation-list/⚡filter.blade.php --}}

{{--
TODO: weitere Usage-Audit-Follow-up-States wie skipped sichtbar machen.
--}}

{{-- Filter Part for Translation List --}}
<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('admin.permissions.filters.title')"
        :description="__(
            'admin.translation_list.filter.refine_the_translation_list_by_key_value_language_and_translation_file',
        )"
    >

        @php
            $selectedTargetLanguage =
                $languageFilter !== '' ? collect($targetLanguages ?? [])->firstWhere('locale', $languageFilter) : null;
            $mainLanguageFileStats = $selectedMainLanguageFileStats ?? null;
            $fileObsoleteEntryCount = (int) ($fileObsoleteEntryCount ?? 0);
            $workflowTooltips = [
                'all' => [
                    'title' => __('admin.translation_list.filter.workflow_all'),
                    'text' => __(
                        'admin.translation_list.filter.all_currently_code_relevant_translation_entries_this_number_changes_when_the_cur',
                    ),
                    'color' => 'sky',
                ],
                'open' => [
                    'title' => __('admin.translation_list.filter.workflow_open'),
                    'text' => __(
                        'admin.translation_list.filter.code_relevant_translation_entries_that_are_still_open_in_the_workflow_and_should',
                    ),
                ],
                'reviewed' => [
                    'title' => __('admin.translation_list.filter.workflow_reviewed'),
                    'text' => __(
                        'admin.translation_list.filter.code_relevant_translation_entries_that_are_still_current_but_have_already_been_m',
                    ),
                ],
                'history' => [
                    'title' => __('admin.translation_list.filter.workflow_history'),
                    'text' => __(
                        'admin.translation_list.filter.reviewed_history_entries_including_older_or_no_longer_current_items_that_remain_',
                    ),
                ],
                'completed' => [
                    'title' => __('admin.translation_list.filter.workflow_completed'),
                    'text' => __(
                        'admin.translation_list.filter.code_relevant_translation_entries_with_status_ok_that_are_already_consistent_and',
                    ),
                ],
            ];
            $statusTooltips = [
                'all' => [
                    'title' => __('admin.translation_list.filter.status_all'),
                    'text' => __(
                        'admin.translation_list.filter.all_statuses_within_the_currently_selected_relevance_scope',
                    ),
                    'color' => 'sky',
                ],
                'ok' => [
                    'title' => __('admin.translation_list.filter.status_ok'),
                    'text' => __(
                        'admin.translation_list.filter.translations_that_are_already_coherent_and_can_be_actively_used_in_the_lang_file',
                    ),
                    'color' => 'emerald',
                ],
                'missing' => [
                    'title' => __('admin.translation_list.filter.status_missing'),
                    'text' => __(
                        'admin.translation_list.filter.concrete_translation_keys_that_exist_in_code_but_still_miss_the_required_transla',
                    ),
                    'color' => 'amber',
                ],
                'native' => [
                    'title' => __('admin.translation_list.filter.status_native'),
                    'text' => __(
                        'admin.translation_list.filter.literal_texts_found_in_code_that_still_need_review_and_usually_need_to_be_replac',
                    ),
                    'color' => 'indigo',
                ],
                'dynamic' => [
                    'title' => __('admin.translation_list.filter.status_dynamic'),
                    'text' => __(
                        'admin.translation_list.filter.runtime_generated_translation_values_these_are_special_cases_and_often_remain_dy',
                    ),
                    'color' => 'orange',
                ],
                'obsolete' => [
                    'title' => __('admin.translation_list.filter.status_obsolete'),
                    'text' => __(
                        'admin.translation_list.filter.obsolete_shows_two_counts_first_the_db_obsolete_work_items_then_the_file_obsolet',
                    ),
                    'color' => 'fuchsia',
                ],
                'invalid' => [
                    'title' => __('admin.translation_list.filter.status_invalid'),
                    'text' => __(
                        'admin.translation_list.filter.invalid_translation_calls_that_definitely_need_correction_and_review',
                    ),
                    'color' => 'pink',
                ],
            ];
            $typeTooltips = [
                'all' => [
                    'title' => __('admin.translation_list.filter.type_all'),
                    'text' => __(
                        'admin.translation_list.filter.all_currently_code_relevant_translation_entry_types_that_belong_to_the_active_wo',
                    ),
                    'color' => 'sky',
                ],
                'key' => [
                    'title' => __('admin.translation_list.filter.type_key'),
                    'text' => __(
                        'admin.translation_list.filter.concrete_translation_keys_that_already_exist_or_become_real_translations_once_th',
                    ),
                    'color' => 'emerald',
                ],
                'vendor' => [
                    'title' => __('admin.translation_list.filter.type_vendor'),
                    'text' => __(
                        'admin.translation_list.filter.vendor_translation_entries_imported_from_package_language_files',
                    ),
                    'color' => 'indigo',
                ],
                'backfill_by_translation' => [
                    'title' => __('admin.translation_list.filter.type_backfill'),
                    'text' => __(
                        'admin.translation_list.filter.entries_that_were_derived_or_repaired_from_existing_translation_values_during_ba',
                    ),
                    'color' => 'cyan',
                ],
                'native' => [
                    'title' => __('admin.translation_list.filter.type_native'),
                    'text' => __(
                        'admin.translation_list.filter.literal_texts_from_code_that_still_need_to_be_reviewed_and_typically_converted_i',
                    ),
                    'color' => 'amber',
                ],
                'dynamic' => [
                    'title' => __('admin.translation_list.filter.type_dynamic'),
                    'text' => __(
                        'admin.translation_list.filter.dynamic_translation_expressions_built_at_runtime_and_usually_left_as_special_cas',
                    ),
                    'color' => 'orange',
                ],
                'archive' => [
                    'title' => __('admin.translation_list.filter.type_archive'),
                    'text' => __(
                        'admin.translation_list.filter.entries_that_are_no_longer_currently_code_relevant_and_therefore_live_outside_th',
                    ),
                    'color' => 'pink',
                ],
            ];
            $dynamicTooltips = [
                'none' => [
                    'title' => __('ui.all-entries'),
                    'text' => __(
                        'Show all current translation entries without a dynamic focus filter.',
                    ),
                    'color' => 'sky',
                ],
                'all' => [
                    'title' => __('ui.all-dynamic'),
                    'text' => __(
                        'Show all dynamic translation entries including dynamic multi entries.',
                    ),
                    'color' => 'orange',
                ],
                'candidate' => [
                    'title' => __('Candidates'),
                    'text' => __(
                        'Show dynamic translation candidates that are not marked as dynamic multi.',
                    ),
                    'color' => 'amber',
                ],
                'multi' => [
                    'title' => __('Dynamic multi'),
                    'text' => __(
                        'Show translation keys marked as dynamic multi.',
                    ),
                    'color' => 'emerald',
                ],
                'without_suggested_key' => [
                    'title' => __('Without suggested key'),
                    'text' => __(
                        'Show dynamic translation candidates that do not have a suggested key yet.',
                    ),
                    'color' => 'rose',
                ],
                'reactivated_stale' => [
                    'title' => __('Reactivated stale'),
                    'text' => __(
                        'Show legacy dynamic entries that were reactivated from stale audit state.',
                    ),
                    'color' => 'violet',
                ],
            ];
        @endphp

        <div class="mb-6 grid gap-2 xl:grid-cols-[5rem_1fr_auto]">
            <div
                class="flex min-h-12 items-center justify-center rounded-md border border-zinc-200/70 bg-zinc-50/70 px-2 py-2 text-sm font-semibold uppercase tracking-wide text-zinc-600 xl:min-h-full dark:border-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300">
                @if ($selectedTargetLanguage)
                    <div class="flex flex-col items-center gap-2">
                        <x-ui.locale.flag
                            :locale="$selectedTargetLanguage->locale"
                            size="lg"
                        />

                        <span class="font-mono text-xs tracking-[0.2em]">
                            {{ $selectedTargetLanguage->locale }}
                        </span>
                    </div>
                @else
                    <span>ALL</span>
                @endif
            </div>

            <div class="space-y-2">

                {{-- Workflow filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('ui.workflow') }}
                    </span>

                    @php
                        $isWorkflowAllActive =
                            !($showArchived ?? false) && $workflowStatus === 'all' && $status === 'all';
                    @endphp

                    <x-ui.tooltip.trigger
                        class="hover:cursor-help"
                        :title="$workflowTooltips['all']['title']"
                        :text="$workflowTooltips['all']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$isWorkflowAllActive ? 'primary' : 'ghost'"
                            :color="$isWorkflowAllActive ? ($workflowTooltips['all']['color'] ?? null) : null"
                            wire:click="showAllRelevantTranslations"
                        >
                            {{ __('ui.states.all') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowRelevantTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    @php
                        $isWorkflowOpenActive = !($showArchived ?? false) && $workflowStatus === 'open';
                    @endphp

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['open']['title']"
                        :text="$workflowTooltips['open']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$isWorkflowOpenActive ? 'primary' : 'ghost'"
                            :color="$isWorkflowOpenActive ? ($workflowTooltips['open']['color'] ?? null) : null"
                            wire:click="setWorkflowStatus('open')"
                        >
                            {{ __('ui.open') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowOpenTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    @php
                        $isWorkflowReviewedActive = !($showArchived ?? false) && $workflowStatus === 'reviewed';
                    @endphp

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['reviewed']['title']"
                        :text="$workflowTooltips['reviewed']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$isWorkflowReviewedActive ? 'primary' : 'ghost'"
                            :color="$isWorkflowReviewedActive ? ($workflowTooltips['reviewed']['color'] ?? null) : null"
                            wire:click="setWorkflowStatus('reviewed')"
                        >
                            {{ __('admin.translation_list.modal_edit.reviewed') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowReviewedTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    @php
                        $isWorkflowHistoryActive = ($showArchived ?? false) && $workflowStatus === 'reviewed';
                    @endphp

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['history']['title']"
                        :text="$workflowTooltips['history']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$isWorkflowHistoryActive ? 'primary' : 'ghost'"
                            :color="$isWorkflowHistoryActive ? ($workflowTooltips['history']['color'] ?? null) : null"
                            wire:click="showHistoryTranslations"
                        >
                            {{ __('admin.translation_list.table.history') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowHistoryTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    @php
                        $isWorkflowCompletedActive =
                            !($showArchived ?? false) && $workflowStatus === 'all' && $status === 'ok';
                    @endphp

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['completed']['title']"
                        :text="$workflowTooltips['completed']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="$isWorkflowCompletedActive ? 'primary' : 'ghost'"
                            :color="$isWorkflowCompletedActive ? ($workflowTooltips['completed']['color'] ?? null) : null"
                            wire:click="showCompletedTranslations"
                        >
                            {{ __('admin.translation_list.filter.completed') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowCompletedTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    @if ($mainLanguageFileStats)
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.filter.main_language_files_locale', [
                                'locale' => strtoupper($mainLanguageFileStats->locale),
                            ])"
                            :text="__(
                                'admin.translation_list.filter.files_lang_files_currently_contain_entries_translation_entries_db_currently_has_',
                                [
                                    'files' => $mainLanguageFileStats->file_count,
                                    'entries' => $mainLanguageFileStats->entry_count,
                                    'db' => $mainLanguageFileStats->db_entry_count,
                                    'state' => $mainLanguageFileStats->in_sync
                                        ? __('admin.translation_list.filter.db_and_files_are_currently_in_sync')
                                        : __('admin.translation_list.filter.db_and_files_are_currently_not_in_sync'),
                                ],
                            )"
                        >
                            <flux:button
                                type="button"
                                size="sm"
                                :variant="$mainLanguageFileStats->in_sync ? 'primary' : 'ghost'"
                                :color="$mainLanguageFileStats->in_sync ? 'emerald' : null"
                            >
                                {{ strtoupper($mainLanguageFileStats->locale) }}-Files
                                <span class="ml-1 opacity-70">
                                    {{ $mainLanguageFileStats->file_count }} /
                                    {{ $mainLanguageFileStats->entry_count }}
                                </span>
                            </flux:button>
                        </x-ui.tooltip.trigger>
                    @endif
                </div>

                {{-- Status filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('ui.status') }}
                    </span>

                    @foreach ($statusOptions as $option)
                        @php
                            $count = $option === 'all' ? $total : $statusCounts[$option] ?? 0;
                            $displayCount = $option === 'obsolete' ? $count . '/' . $fileObsoleteEntryCount : $count;
                            $statusTooltip = $statusTooltips[$option] ?? $statusTooltips['all'];
                        @endphp

                        <x-ui.tooltip.trigger
                            :title="$statusTooltip['title']"
                            :text="$statusTooltip['text']"
                        >
                            <flux:button
                                type="button"
                                size="sm"
                                :variant="$status === $option ? 'primary' : 'ghost'"
                                :color="$status === $option ? ($statusTooltip['color'] ?? null) : null"
                                wire:click="setStatus('{{ $option }}')"
                            >
                                {{ str($option)->headline() }}
                                <span class="ml-1 opacity-70">
                                    {{ $displayCount }}
                                </span>
                            </flux:button>
                        </x-ui.tooltip.trigger>
                    @endforeach
                </div>

                {{-- Dynamic filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dynamic') }}
                    </span>

                    @foreach ($dynamicFilterOptions as $option)
                        @php
                            $count = $dynamicFilterCounts[$option] ?? 0;
                            $label = match ($option) {
                                'none' => __('ui.all-entries'),
                                'all' => __('ui.all-dynamic'),
                                'candidate' => __('Candidates'),
                                'multi' => __('Dynamic multi'),
                                'without_suggested_key' => __('Without suggested key'),
                                'reactivated_stale' => __('Reactivated stale'),
                                default => str($option)->headline(),
                            };
                            $dynamicTooltip = $dynamicTooltips[$option] ?? $dynamicTooltips['none'];
                            $isActiveDynamicFilter = $dynamicFilter === $option;
                        @endphp

                        <x-ui.tooltip.trigger
                            :title="$dynamicTooltip['title']"
                            :text="$dynamicTooltip['text']"
                        >
                            <flux:button
                                type="button"
                                size="sm"
                                :variant="$isActiveDynamicFilter ? 'primary' : 'ghost'"
                                :color="$isActiveDynamicFilter ? ($dynamicTooltip['color'] ?? null) : null"
                                wire:click="setDynamicFilter('{{ $option }}')"
                            >
                                {{ $label }}
                                <span class="ml-1 opacity-70">
                                    {{ $count }}
                                </span>
                            </flux:button>
                        </x-ui.tooltip.trigger>
                    @endforeach
                </div>

                {{-- Classification filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-36 shrink-0 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('ui.type') }}
                    </span>

                    @foreach ($classificationOptions as $option)
                        @php
                            $count =
                                $option === 'all' ? $activeTypeTotal ?? 0 : $activeClassificationCounts[$option] ?? 0;
                            $label = match ($option) {
                                'all' => __('ui.all-types'),
                                'backfill_by_translation' => __('admin.translation_list.meta.backfill'),
                                default => str($option)->headline(),
                            };
                            $isActiveType = !($showArchived ?? false) && $classification === $option;
                            $typeTooltip = $typeTooltips[$option] ?? $typeTooltips['all'];
                        @endphp

                        <x-ui.tooltip.trigger
                            :title="$typeTooltip['title']"
                            :text="$typeTooltip['text']"
                        >
                            <flux:button
                                type="button"
                                size="sm"
                                :variant="$isActiveType ? 'primary' : 'ghost'"
                                :color="$isActiveType ? ($typeTooltip['color'] ?? null) : null"
                                wire:click="setActiveClassification('{{ $option }}')"
                            >
                                {{ $label }}
                                <span class="ml-1 opacity-70">
                                    {{ $count }}
                                </span>
                            </flux:button>
                        </x-ui.tooltip.trigger>
                    @endforeach

                    <x-ui.tooltip.trigger
                        :title="$typeTooltips['archive']['title']"
                        :text="$typeTooltips['archive']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            :variant="($showArchived ?? false) ? 'primary' : 'ghost'"
                            :color="($showArchived ?? false) ? ($typeTooltips['archive']['color'] ?? null) : null"
                            wire:click="showArchivedTranslations"
                        >
                            {{ __('admin.translation_list.filter.archive') }}
                            <span class="ml-1 opacity-70">
                                {{ $archiveCount ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>
                </div>
            </div>

            {{-- Quick focus filters --}}
            <div class="grid gap-2">
                <div
                    class="min-w-58 flex items-center justify-start rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
                    <flux:field variant="inline">
                        <flux:switch
                            class="switch-colored mr-3 hover:cursor-pointer"
                            wire:click="toggleOnlyProblems"
                            :checked="$onlyProblems"
                        />

                        <flux:label
                            class="text-sm opacity-70 hover:cursor-pointer"
                            wire:click="toggleOnlyProblems"
                        >
                            {{ __('admin.translation_list.meta.only_problems') }}

                            <span class="ml-1 opacity-70">
                                {{ $problemCount }}
                            </span>
                        </flux:label>
                    </flux:field>
                </div>

                <div
                    class="min-w-58 flex items-center justify-start rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
                    <flux:field variant="inline">
                        <flux:switch
                            class="switch-colored mr-3 hover:cursor-pointer"
                            wire:click="toggleOnlyBaseDuplicates"
                            :checked="$onlyBaseDuplicates"
                        />

                        <flux:label
                            class="text-sm opacity-70 hover:cursor-pointer"
                            wire:click="toggleOnlyBaseDuplicates"
                        >
                            {{ __('Only duplicates') }}

                            <span class="ml-1 opacity-70">
                                {{ $duplicateCount }}
                            </span>
                        </flux:label>
                    </flux:field>
                </div>

                <div
                    class="min-w-58 flex items-center justify-start rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
                    <flux:field variant="inline">
                        <flux:switch
                            class="switch-colored mr-3 hover:cursor-pointer"
                            wire:click="toggleOnlyNeedsKey"
                            :checked="$onlyNeedsKey"
                        />

                        <flux:label
                            class="text-sm opacity-70 hover:cursor-pointer"
                            wire:click="toggleOnlyNeedsKey"
                        >
                            {{ __('Only Needs key') }}

                            <span class="ml-1 opacity-70">
                                {{ $needsKeyCount }}
                            </span>
                        </flux:label>
                    </flux:field>
                </div>
            </div>
        </div>
    </x-ui.headers.card>

    <div class="flex w-full flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1 basis-72">
            <flux:field>
                <flux:label for="translation-list-search">
                    <x-ui.tooltip.trigger
                        :title="__('admin.translation_list.filter.filter_by_search')"
                        :text="__(
                            'admin.translation_list.filter.enter_a_search_term_to_filter_the_list_of_translations_by_key_or_value_the_searc',
                        )"
                    >
                        {{ __('ui.actions.search') }}
                    </x-ui.tooltip.trigger>
                </flux:label>

                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.magnifying-glass stroke-width="1" />
                    </flux:input.group.prefix>

                    <flux:input
                        class="w-full min-w-0"
                        id="translation-list-search"
                        name="translation-list-search"
                        type="text"
                        copyable
                        clearable
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('admin.translation_list.filter.search_by_key_or_value') }}"
                    />
                </flux:input.group>
            </flux:field>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:field>
                <flux:label for="translation-list-language-filter">
                    <x-ui.tooltip.trigger
                        :title="__('admin.translation_list.filter.select_a_target_language')"
                        :text="__(
                            'admin.translation_list.filter.select_a_target_language_to_filter_the_list_of_translations',
                        )"
                    >
                        {{ __('admin.translation_list.filter.target_language') }}
                    </x-ui.tooltip.trigger>
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.language stroke-width="1" />
                    </flux:input.group.prefix>

                    <flux:select
                        id="translation-list-language-filter"
                        name="translation-list-language-filter"
                        variant="listbox"
                        searchable
                        wire:model.live="languageFilter"
                    >
                        @if (!$languageFilter)
                            <flux:select.option
                                value=""
                                disabled
                                selected
                            >
                                <div class="flex items-center gap-2">
                                    <flux:icon.language
                                        class="text-zinc-400"
                                        variant="mini"
                                    />
                                    {{ __('admin.translation_list.filter.select_target_language') }}
                                </div>
                            </flux:select.option>
                        @endif

                        @foreach ($targetLanguages as $translationLanguage)
                            <flux:select.option value="{{ $translationLanguage->locale }}">
                                <div class="flex items-center gap-2">
                                    <x-ui.locale.flag :locale="$translationLanguage->locale" />
                                    <div>
                                        <span
                                            class="mr-2 text-center font-mono uppercase">{{ $translationLanguage->locale }}</span>
                                        ·
                                        <span class="ml-2">{{ $translationLanguage->native_name }}</span>
                                    </div>
                                </div>
                            </flux:select.option>
                        @endforeach

                    </flux:select>
                </flux:input.group>
            </flux:field>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:field>
                <flux:label for="translation-list-namespace-filter">
                    <x-ui.tooltip.trigger
                        :title="__('admin.translation_list.filter.filter_by_namespace')"
                        :text="__(
                            'admin.translation_list.filter.select_a_translation_namespace_to_narrow_the_list_to_the_corresponding_translati',
                        )"
                    >
                        {{ __('admin.translation_list.modal.namespace') }}
                    </x-ui.tooltip.trigger>
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.squares-2x2 stroke-width="1" />
                    </flux:input.group.prefix>

                    <flux:select
                        id="translation-list-namespace-filter"
                        name="translation-list-namespace-filter"
                        variant="listbox"
                        searchable
                        wire:model.live="namespaceFilter"
                    >
                        <flux:select.option value="">
                            {{ __('admin.translation_list.filter.all_namespaces') }}
                        </flux:select.option>

                        @foreach ($translationNamespaces as $translationNamespace)
                            <flux:select.option value="{{ $translationNamespace }}">
                                {{ $translationNamespace }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>
            </flux:field>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:field>
                <flux:label for="translation-list-group-filter">
                    <x-ui.tooltip.trigger
                        :title="__('admin.translation_list.filter.filter_by_group')"
                        :text="__(
                            'admin.translation_list.filter.select_a_translation_group_within_the_current_namespace_and_filter_context',
                        )"
                    >
                        {{ __('admin.translation_list.modal.group') }}
                    </x-ui.tooltip.trigger>
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.document-text stroke-width="1" />
                    </flux:input.group.prefix>

                    <flux:select
                        id="translation-list-group-filter"
                        name="translation-list-group-filter"
                        variant="listbox"
                        searchable
                        wire:model.live="groupFilter"
                    >
                        <flux:select.option value="">
                            {{ __('admin.translation_list.filter.all_groups') }}
                        </flux:select.option>

                        @foreach ($translationGroups as $translationGroup)
                            <flux:select.option value="{{ $translationGroup }}">
                                {{ $translationGroup }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>
            </flux:field>
        </div>

        <div class="ml-auto min-w-0 flex-none basis-56">
            <x-ui.table.per-page-selector
                id="translation-list-per-page"
                name="translation-list-per-page"
                action="setPerPage"
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
            {{ __('admin.translation_list.filter.showing') }} {{ number_format($filteredTotal) }}
            {{ __('admin.translation_list.filter.of') }} {{ number_format($total) }}
        </flux:badge>

        @if ($hasActiveFilters)
            <flux:badge
                color="amber"
                variant="subtle"
            >
                {{ __('admin.translation_list.filter.filters_active') }}
            </flux:badge>
        @endif
    </div>

</flux:card>
