{{-- resources/views/components/admin/partials/translation-list/⚡filter.blade.php --}}

{{-- Filter Part for Translation List --}}
<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__('Refine the translation list by key, value, language, and translation file.')"
    >

        @php
            $selectedTargetLanguage =
                $languageFilter !== '' ? collect($targetLanguages ?? [])->firstWhere('locale', $languageFilter) : null;
            $workflowTooltips = [
                'all' => [
                    'title' => __('Workflow: All'),
                    'text' => __(
                        'All currently code-relevant translation entries. This number changes when the current code introduces or removes translation spots.',
                    ),
                ],
                'open' => [
                    'title' => __('Workflow: Open'),
                    'text' => __(
                        'Code-relevant translation entries that are still open in the workflow and should be reviewed or edited.',
                    ),
                ],
                'reviewed' => [
                    'title' => __('Workflow: Reviewed'),
                    'text' => __(
                        'Code-relevant translation entries that are still current, but have already been marked as reviewed.',
                    ),
                ],
                'history' => [
                    'title' => __('Workflow: History'),
                    'text' => __(
                        'Reviewed history entries, including older or no longer current items that remain documented for traceability.',
                    ),
                ],
                'completed' => [
                    'title' => __('Workflow: Completed'),
                    'text' => __(
                        'Code-relevant translation entries with status OK that are already consistent and can be actively used.',
                    ),
                ],
            ];
            $statusTooltips = [
                'all' => [
                    'title' => __('Status: All'),
                    'text' => __('All statuses within the currently selected relevance scope.'),
                ],
                'ok' => [
                    'title' => __('Status: OK'),
                    'text' => __('Translations that are already coherent and can be actively used in the lang files.'),
                ],
                'missing' => [
                    'title' => __('Status: Missing'),
                    'text' => __(
                        'Concrete translation keys that exist in code, but still miss the required translation value.',
                    ),
                ],
                'native' => [
                    'title' => __('Status: Native'),
                    'text' => __(
                        'Literal texts found in code that still need review and usually need to be replaced by real translation keys.',
                    ),
                ],
                'dynamic' => [
                    'title' => __('Status: Dynamic'),
                    'text' => __(
                        'Runtime-generated translation values. These are special cases and often remain dynamic by design.',
                    ),
                ],
                'obsolete' => [
                    'title' => __('Status: Obsolete'),
                    'text' => __(
                        'Entries that are no longer actively used by the current code and usually belong in history or archive.',
                    ),
                ],
                'invalid' => [
                    'title' => __('Status: Invalid'),
                    'text' => __('Invalid translation calls that definitely need correction and review.'),
                ],
            ];
            $typeTooltips = [
                'all' => [
                    'title' => __('Type: All'),
                    'text' => __(
                        'All currently code-relevant translation entry types that belong to the active work set.',
                    ),
                ],
                'key' => [
                    'title' => __('Type: Key'),
                    'text' => __(
                        'Concrete translation keys that already exist or become real translations once they have been reviewed and maintained.',
                    ),
                ],
                'vendor' => [
                    'title' => __('Type: Vendor'),
                    'text' => __('Vendor translation entries imported from package language files.'),
                ],
                'backfill_by_translation' => [
                    'title' => __('Type: Backfill'),
                    'text' => __(
                        'Entries that were derived or repaired from existing translation values during backfill workflows.',
                    ),
                ],
                'native' => [
                    'title' => __('Type: Native'),
                    'text' => __(
                        'Literal texts from code that still need to be reviewed and typically converted into translation keys.',
                    ),
                ],
                'dynamic' => [
                    'title' => __('Type: Dynamic'),
                    'text' => __('Dynamic translation expressions built at runtime and usually left as special cases.'),
                ],
                'archive' => [
                    'title' => __('Type: Archive'),
                    'text' => __(
                        'Entries that are no longer currently code-relevant and therefore live outside the active translation work set.',
                    ),
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
                        class="mr-2 w-24 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Workflow') }}
                    </span>

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['all']['title']"
                        :text="$workflowTooltips['all']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            variant="{{ !($showArchived ?? false) && $workflowStatus === 'all' && $status === 'all' ? 'primary' : 'ghost' }}"
                            wire:click="showAllRelevantTranslations"
                        >
                            {{ __('All') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowRelevantTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['open']['title']"
                        :text="$workflowTooltips['open']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            variant="{{ !($showArchived ?? false) && $workflowStatus === 'open' ? 'primary' : 'ghost' }}"
                            wire:click="setWorkflowStatus('open')"
                        >
                            {{ __('Open') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowOpenTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['reviewed']['title']"
                        :text="$workflowTooltips['reviewed']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            variant="{{ !($showArchived ?? false) && $workflowStatus === 'reviewed' ? 'primary' : 'ghost' }}"
                            wire:click="setWorkflowStatus('reviewed')"
                        >
                            {{ __('Reviewed') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowReviewedTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['history']['title']"
                        :text="$workflowTooltips['history']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            variant="{{ ($showArchived ?? false) && $workflowStatus === 'reviewed' ? 'primary' : 'ghost' }}"
                            wire:click="showHistoryTranslations"
                        >
                            {{ __('History') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowHistoryTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>

                    <x-ui.tooltip.trigger
                        :title="$workflowTooltips['completed']['title']"
                        :text="$workflowTooltips['completed']['text']"
                    >
                        <flux:button
                            type="button"
                            size="sm"
                            variant="{{ !($showArchived ?? false) && $workflowStatus === 'all' && $status === 'ok' ? 'primary' : 'ghost' }}"
                            wire:click="showCompletedTranslations"
                        >
                            {{ __('Completed') }}
                            <span class="ml-1 opacity-70">
                                {{ $workflowCompletedTotal ?? 0 }}
                            </span>
                        </flux:button>
                    </x-ui.tooltip.trigger>
                </div>

                {{-- Status filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-24 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Status') }}
                    </span>

                    @foreach ($statusOptions as $option)
                        @php
                            $count = $option === 'all' ? $total : $statusCounts[$option] ?? 0;
                            $statusTooltip = $statusTooltips[$option] ?? $statusTooltips['all'];
                        @endphp

                        <x-ui.tooltip.trigger
                            :title="$statusTooltip['title']"
                            :text="$statusTooltip['text']"
                        >
                            <flux:button
                                type="button"
                                size="sm"
                                variant="{{ $status === $option ? 'primary' : 'ghost' }}"
                                wire:click="setStatus('{{ $option }}')"
                            >
                                {{ str($option)->headline() }}
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
                        class="mr-2 w-24 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Type') }}
                    </span>

                    @foreach ($classificationOptions as $option)
                        @php
                            $count =
                                $option === 'all' ? $activeTypeTotal ?? 0 : $activeClassificationCounts[$option] ?? 0;
                            $label = match ($option) {
                                'all' => __('All types'),
                                'backfill_by_translation' => __('Backfill'),
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
                                variant="{{ $isActiveType ? 'primary' : 'ghost' }}"
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
                            variant="{{ $showArchived ?? false ? 'primary' : 'ghost' }}"
                            wire:click="showArchivedTranslations"
                        >
                            {{ __('Archive') }}
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
                    class="min-w-58 flex items-center justify-center rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
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
                            {{ __('Only problems') }}

                            <span class="ml-1 opacity-70">
                                {{ $problemCount }}
                            </span>
                        </flux:label>
                    </flux:field>
                </div>

                <div
                    class="min-w-58 flex items-center justify-center rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
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
                            Only duplicates

                            <span class="ml-1 opacity-70">
                                {{ $duplicateCount }}
                            </span>
                        </flux:label>
                    </flux:field>
                </div>

                {{-- <div class="flex items-center justify-center">
                    <flux:link
                        class="text-xs font-medium text-sky-700 hover:text-sky-600 dark:text-sky-300 dark:hover:text-sky-200"
                        :href="route('admin.translation-sub-languages')"
                        wire:navigate
                    >
                        Open Sub-Language Review
                    </flux:link>
                </div> --}}
            </div>
        </div>

    </x-ui.headers.card>

    <div class="flex w-full flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1 basis-72">
            <flux:label for="translation-list-search">
                <x-ui.tooltip.trigger
                    :title="__('Filter by search')"
                    :text="__(
                        'Enter a search term to filter the list of translations by key or value. The search is case-sensitive.',
                    )"
                >
                    {{ __('Search') }}
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
                    placeholder="{{ __('Search by key or value') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:label for="translation-list-language-filter">
                <x-ui.tooltip.trigger
                    :title="__('Select a target language')"
                    :text="__('Select a target language to filter the list of translations.')"
                >
                    {{ __('Target Language') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.language stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-list-language-filter"
                    name="translation-list-language-filter"
                    wire:model.live="languageFilter"
                >
                    @if (!$languageFilter)
                        <flux:select.option
                            value=""
                            disabled
                            selected
                        >
                            {{ __('— Select target language —') }}
                        </flux:select.option>
                    @endif

                    @foreach ($targetLanguages as $translationLanguage)
                        <flux:select.option value="{{ $translationLanguage->locale }}">
                            {{ $translationLanguage->locale }}
                            ·
                            {{ $translationLanguage->native_name }}
                        </flux:select.option>
                    @endforeach

                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:label for="translation-list-file-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by translation file')"
                    :text="__('Select a translation file to filter the list of translations.')"
                >
                    {{ __('Translation file') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.document-text stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-list-file-filter"
                    name="translation-list-file-filter"
                    wire:model.live="fileFilter"
                >
                    <flux:select.option value="">
                        {{ __('All files') }}
                    </flux:select.option>

                    @foreach ($translationFiles as $translationFile)
                        <flux:select.option value="{{ $translationFile }}">
                            {{ $translationFile }}.php
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto min-w-0 flex-none basis-56">
            <x-ui.table.per-page-selector
                id="translation-list-per-page"
                name="translation-list-per-page"
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
            {{ __('Showing') }} {{ number_format($filteredTotal) }} {{ __('of') }} {{ number_format($total) }}
        </flux:badge>

        @if ($hasActiveFilters)
            <flux:badge
                color="amber"
                variant="subtle"
            >
                {{ __('Filters active') }}
            </flux:badge>
        @endif
    </div>
</flux:card>
