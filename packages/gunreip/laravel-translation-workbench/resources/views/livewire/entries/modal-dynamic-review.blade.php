{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-dynamic-review.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-finding-dynamic-review"
    wire:model="dynamicReviewModalOpen"
>
    <div class="space-y-4">
        <div class="flex items-start gap-3">
            <div class="min-w-0 space-y-1">
                <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <flux:heading
                        size="xl"
                        level="3"
                    >
                        {{ __('Review dynamic data') }}
                    </flux:heading>

                    @if ($dynamicReviewFinding)
                        <flux:badge
                            color="teal"
                            size="sm"
                        >
                            {{ __('Dynamic') }}
                        </flux:badge>

                        @if (
                            (bool) ($dynamicReviewFinding->is_dynamic_multi ?? false) ||
                                (bool) ($dynamicReviewFinding->reviewed_is_dynamic_multi ?? false))
                            <flux:badge
                                color="cyan"
                                size="sm"
                            >
                                {{ __('Dynamic option list') }}
                            </flux:badge>
                        @endif
                    @endif
                </div>

                <flux:text class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Clarify whether this dynamic finding is a single runtime value or an option list before editing translations.') }}
                </flux:text>
            </div>

            <div class="mr-8 ms-auto flex shrink-0 items-center gap-2">
                @if ($dynamicReviewFinding)
                    <flux:badge
                        class="h-6 tabular-nums"
                        variant="subtle"
                    >
                        #{{ $dynamicReviewFinding->id }}
                    </flux:badge>

                    @php
                        $dynamicReviewHasTranslationKey = filled($dynamicReviewFinding->translation_key);
                        $dynamicReviewIsReviewed = ($dynamicReviewFinding->review_status ?? null) === 'reviewed';
                        $dynamicReviewSourceCount = (int) ($dynamicReviewFinding->dynamic_source_count ?? 0);
                        $dynamicReviewUnresolvedCount =
                            (int) ($dynamicReviewFinding->dynamic_unresolved_source_count ?? 0);
                        $dynamicReviewReady = (bool) ($dynamicReviewReady ?? false);
                        $dynamicReviewCanContinue =
                            $dynamicReviewHasTranslationKey && $dynamicReviewIsReviewed && $dynamicReviewReady;
                    @endphp

                @endif
            </div>
        </div>

        @if ($dynamicReviewFinding)
            @php
                $dynamicReviewSources = collect($dynamicReviewSources ?? []);
                $dynamicReviewRuntimeSources = $dynamicReviewSources
                    ->filter(static fn(array $source): bool => (bool) ($source['is_runtime_options'] ?? false))
                    ->values();
                $dynamicReviewLinkedSources = $dynamicReviewSources
                    ->reject(static fn(array $source): bool => (bool) ($source['is_runtime_options'] ?? false))
                    ->values();
                $dynamicReviewConfirmedLinkCount = $dynamicReviewLinkedSources
                    ->filter(static fn(array $source): bool => ($source['link_review_status'] ?? null) === 'confirmed')
                    ->count();
                $dynamicReviewPendingLinkCount = $dynamicReviewLinkedSources
                    ->filter(static fn(array $source): bool => ($source['link_review_status'] ?? null) !== 'confirmed')
                    ->count();
                $dynamicReviewRuntimeValueCount = $dynamicReviewRuntimeSources->sum('values_count');
                $dynamicReviewHasConsumerSources = $dynamicReviewLinkedSources->isNotEmpty();
                $dynamicReviewHasRuntimeSources = $dynamicReviewRuntimeSources->isNotEmpty();
                $dynamicReviewHasRuntimeValues = $dynamicReviewRuntimeValueCount > 0;
                $dynamicReviewRuntimeMissing =
                    $dynamicReviewHasConsumerSources && !$dynamicReviewHasRuntimeSources;
                $dynamicReviewLinkMissing =
                    $dynamicReviewHasConsumerSources &&
                    $dynamicReviewHasRuntimeSources &&
                    $dynamicReviewConfirmedLinkCount === 0;
                $dynamicReviewTranslationKey = trim((string) ($dynamicReviewFinding->translation_key ?? ''));
                $dynamicReviewKeyAnchor = trim(
                    (string) ($dynamicReviewFinding->translation_key ?:
                    $dynamicReviewFinding->key_suggested_key ?:
                    $dynamicReviewFinding->suggested_key ?? ''),
                );
                $dynamicReviewRuntimeSuggestedKeys = $dynamicReviewRuntimeSources
                    ->pluck('suggested_key')
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->filter(static fn(string $key): bool => $key !== '')
                    ->unique()
                    ->values();
                $dynamicReviewDataState = trim(
                    (string) ($dynamicReviewFinding->key_dynamic_data_state ?? '' ?:
                    $dynamicReviewFinding->dynamic_data_state ?? ''),
                );
                $dynamicReviewSourceTypes = collect(
                    explode(',', (string) ($dynamicReviewFinding->dynamic_source_types ?? '')),
                )
                    ->map(static fn(string $sourceType): string => trim($sourceType))
                    ->filter(static fn(string $sourceType): bool => $sourceType !== '')
                    ->values();
                $dynamicReviewIsMulti =
                    (bool) ($dynamicReviewFinding->is_dynamic_multi ?? false) ||
                    (bool) ($dynamicReviewFinding->reviewed_is_dynamic_multi ?? false) ||
                    (int) ($dynamicReviewFinding->dynamic_multi_source_count ?? 0) > 0;
            @endphp

            <div class="grid gap-3 xl:grid-cols-4">
                {{-- Callout Translation key --}}
                <flux:callout
                    color="{{ $dynamicReviewHasTranslationKey ? 'green' : 'red' }}"
                    icon="key-round"
                >
                    <flux:callout.heading>
                        <span>{{ __('Translation key') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Translation key')"
                            :text="__(
                                'The translation key is the anchor for this dynamic finding. It may be missing or unresolved if the finding has not been reviewed yet.',
                            )"
                        />
                    </flux:callout.heading>
                    <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                        {{ $dynamicReviewTranslationKey !== '' ? $dynamicReviewTranslationKey : __('Missing translation key') }}
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="{{ $dynamicReviewReady ? 'green' : 'orange' }}"
                    icon="database-zap"
                >
                    <flux:callout.heading>{{ __('Data state') }}</flux:callout.heading>
                    <flux:callout.text class="text-xs">
                        @if ($dynamicReviewReady)
                            {{ __('Dynamic data is resolved enough for editing.') }}
                        @elseif ($dynamicReviewRuntimeMissing)
                            {{ __('Runtime option values have not been observed for this dynamic consumer yet.') }}
                        @elseif ($dynamicReviewLinkMissing)
                            {{ __('Confirm the runtime option link before editing dynamic translations.') }}
                        @else
                            {{ __('Dynamic data still needs review before editing.') }}
                        @endif
                    </flux:callout.text>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @if ($dynamicReviewReady && $dynamicReviewDataState !== 'structured')
                            <span
                                class="relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-['']">
                                <flux:badge
                                    size="sm"
                                    color="orange"
                                >
                                    {{ $dynamicReviewDataState !== '' ? str($dynamicReviewDataState)->headline() : __('No state') }}
                                </flux:badge>
                            </span>
                            <flux:badge
                                size="sm"
                                color="green"
                            >
                                {{ __('Review resolved') }}
                            </flux:badge>
                        @else
                            <flux:badge
                                size="sm"
                                color="{{ $dynamicReviewDataState === 'structured' ? 'green' : 'orange' }}"
                            >
                                {{ $dynamicReviewDataState !== '' ? str($dynamicReviewDataState)->headline() : __('No state') }}
                            </flux:badge>
                        @endif

                        @if ($dynamicReviewReady && $dynamicReviewUnresolvedCount > 0)
                            <span
                                class="relative inline-flex opacity-60 after:absolute after:left-1 after:right-1 after:top-1/2 after:h-px after:-rotate-12 after:bg-current after:content-['']">
                                <flux:badge
                                    size="sm"
                                    color="red"
                                >
                                    {{ __('Unresolved') }}: {{ $dynamicReviewUnresolvedCount }}
                                </flux:badge>
                            </span>
                            <flux:badge
                                size="sm"
                                color="green"
                            >
                                {{ __('Resolved by review') }}
                            </flux:badge>
                        @else
                            <flux:badge
                                size="sm"
                                color="{{ $dynamicReviewUnresolvedCount > 0 ? 'red' : 'green' }}"
                            >
                                {{ __('Unresolved') }}: {{ $dynamicReviewUnresolvedCount }}
                            </flux:badge>
                        @endif

                        @if (!$dynamicReviewReady && $dynamicReviewRuntimeMissing)
                            <flux:badge
                                size="sm"
                                color="red"
                            >
                                {{ __('Runtime values missing') }}
                            </flux:badge>
                        @elseif (!$dynamicReviewReady && $dynamicReviewLinkMissing)
                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ __('Runtime link missing') }}
                            </flux:badge>
                        @elseif ($dynamicReviewHasRuntimeValues)
                            <flux:badge
                                size="sm"
                                color="green"
                            >
                                {{ __('Runtime values') }}: {{ $dynamicReviewRuntimeValueCount }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:callout>

                {{-- Callout Dynamic Type --}}
                <flux:callout
                    color="{{ $dynamicReviewIsMulti ? 'cyan' : 'teal' }}"
                    icon="list-tree"
                >
                    <flux:callout.heading>{{ __('Dynamic type') }}</flux:callout.heading>
                    <flux:callout.text class="text-xs">
                        {{ $dynamicReviewIsMulti ? __('This finding is currently classified as DynamicMulti.') : __('This finding is currently classified as Dynamic.') }}
                    </flux:callout.text>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <flux:badge
                            size="sm"
                            color="{{ $dynamicReviewIsMulti ? 'cyan' : 'teal' }}"
                        >
                            {{ $dynamicReviewIsMulti ? __('Current: DynamicMulti') : __('Current: Dynamic') }}
                        </flux:badge>
                    </div>
                </flux:callout>

                {{-- Callout Discoveries --}}
                <flux:callout
                    color="sky"
                    icon="scan-search"
                >
                    <flux:callout.heading>{{ __('Discoveries') }}</flux:callout.heading>
                    <flux:callout.text class="text-xs">
                        {{ __('Scanner context that may describe runtime option values.') }}
                    </flux:callout.text>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <flux:badge size="sm">{{ __('Sources') }}: {{ $dynamicReviewSourceCount }}</flux:badge>
                        <flux:badge size="sm">{{ __('Values') }}:
                            {{ (int) ($dynamicReviewFinding->dynamic_source_value_count ?? 0) }}</flux:badge>
                        @foreach ($dynamicReviewSourceTypes as $sourceType)
                            <flux:badge
                                size="sm"
                                color="sky"
                            >
                                {{ $sourceType }}
                            </flux:badge>
                        @endforeach
                    </div>
                </flux:callout>
            </div>

            {{-- Callout Dynamic Relationship --}}
            <flux:callout
                color="{{ $dynamicReviewConfirmedLinkCount > 0 || $dynamicReviewLinkedSources->isEmpty() ? 'green' : 'amber' }}"
                icon="git-branch"
            >
                <div class="grid gap-3 xl:grid-cols-3">
                    <div class="min-w-0 space-y-1">
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Dynamic consumer') }}</span>
                                <x-ui.tooltip.simple
                                    :header="__('Dynamic consumer')"
                                    :text="__(
                                        'The selected finding renders one dynamic value at runtime. It may need to be linked to a runtime option source before translations can be edited reliably.',
                                    )"
                                    max-width="max-w-[20rem]"
                                />
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                            {{ $dynamicReviewKeyAnchor !== '' ? $dynamicReviewKeyAnchor : __('No key anchor') }}
                        </flux:callout.text>
                    </div>

                    <div class="min-w-0 space-y-1">
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Runtime option source') }}</span>
                                <x-ui.tooltip.simple
                                    :header="__('Runtime option source')"
                                    :text="__(
                                        'Runtime sources are value collections observed while the application rendered option lists or database-backed values.',
                                    )"
                                />
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="space-y-1 text-xs">
                            @forelse ($dynamicReviewRuntimeSuggestedKeys as $runtimeSuggestedKey)
                                <div class="wrap-anywhere text-wrap font-mono">{{ $runtimeSuggestedKey }}</div>
                            @empty
                                <span class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('No runtime source observed yet') }}
                                </span>
                            @endforelse
                        </flux:callout.text>
                    </div>

                    <div class="min-w-0 space-y-1">
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Review link') }}</span>
                                <x-ui.tooltip.simple
                                    :header="__('Review link')"
                                    :text="__(
                                        'Confirmed links say that this dynamic consumer should use the listed runtime option source for its translatable values.',
                                    )"
                                    max-width="max-w-[20rem]"
                                />
                            </span>
                        </flux:callout.heading>
                        <div class="flex flex-wrap gap-1.5">
                            <flux:badge
                                size="sm"
                                color="{{ $dynamicReviewRuntimeSources->isNotEmpty() ? 'green' : 'orange' }}"
                            >
                                {{ __('Runtime sources') }}: {{ $dynamicReviewRuntimeSources->count() }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="{{ $dynamicReviewLinkedSources->isNotEmpty() ? 'sky' : 'zinc' }}"
                            >
                                {{ __('Consumers') }}: {{ $dynamicReviewLinkedSources->count() }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="{{ $dynamicReviewConfirmedLinkCount > 0 ? 'green' : ($dynamicReviewLinkedSources->isNotEmpty() ? 'red' : 'zinc') }}"
                            >
                                {{ __('Confirmed links') }}: {{ $dynamicReviewConfirmedLinkCount }}
                            </flux:badge>
                            @if ($dynamicReviewPendingLinkCount > 0)
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('Pending links') }}: {{ $dynamicReviewPendingLinkCount }}
                                </flux:badge>
                            @endif
                        </div>
                    </div>
                </div>
            </flux:callout>

            {{-- Card Dynamic Sources --}}
            <flux:card>
                <x-ui.headers.card
                    :title="__('Dynamic sources')"
                    :description="__('Observed runtime options and related dynamic findings for the selected entry.')"
                >
                    <flux:button
                        type="button"
                        size="xs"
                        variant="primary"
                        icon="languages"
                        :disabled="!$dynamicReviewCanContinue"
                        wire:click="continueDynamicEdit"
                    >
                        {{ __('Translate dynamic values') }}
                    </flux:button>
                </x-ui.headers.card>

                @if ($dynamicReviewSources->isEmpty())
                    <flux:callout
                        class="mt-4"
                        color="amber"
                        icon="info"
                    >
                        <flux:callout.heading>{{ __('No dynamic sources found') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('The finding is marked as dynamic, but no structured dynamic source rows have been linked yet.') }}
                        </flux:callout.text>
                    </flux:callout>
                @else
                    @foreach ([
        [
            'title' => __('Runtime options'),
            'description' => __('Values observed while the application rendered this option source. These rows are keyed by translation_workbench_keys.suggested_key.'),
            'rows' => $dynamicReviewRuntimeSources,
            'empty' => __('No runtime options have been observed for this finding yet.'),
            'color' => 'cyan',
            'icon' => 'database-zap',
            'match_target' => 'dynamic_key',
        ],
        [
            'title' => __('Related dynamic findings'),
            'description' => __('Scanner or discovery rows that may describe the same dynamic translation context.'),
            'rows' => $dynamicReviewLinkedSources,
            'empty' => __('No additional dynamic findings are linked to this entry.'),
            'color' => 'sky',
            'icon' => 'scan-search',
            'match_target' => 'runtime_options',
        ],
    ] as $dynamicSourceSection)
                        @php
                            $dynamicSourceSectionSuggestedKeys = $dynamicSourceSection['rows']
                                ->pluck('suggested_key')
                                ->map(static fn(mixed $key): string => trim((string) $key))
                                ->filter(static fn(string $key): bool => $key !== '')
                                ->unique()
                                ->values();

                            if ($dynamicSourceSection['match_target'] === 'dynamic_key') {
                                $dynamicSourceSectionKeyMatch =
                                    $dynamicReviewKeyAnchor !== '' &&
                                    $dynamicSourceSectionSuggestedKeys->count() === 1 &&
                                    $dynamicSourceSectionSuggestedKeys->first() === $dynamicReviewKeyAnchor
                                        ? 'equal'
                                        : ($dynamicReviewKeyAnchor !== '' &&
                                        $dynamicSourceSectionSuggestedKeys->isNotEmpty()
                                            ? 'differs'
                                            : 'unknown');
                                $dynamicSourceSectionKeyMatchLabel = match ($dynamicSourceSectionKeyMatch) {
                                    'equal' => __('Equal to dynamic key'),
                                    'differs' => __('Differs to dynamic key'),
                                    default => __('No dynamic key comparison'),
                                };
                            } else {
                                $dynamicSourceSectionKeyMatch =
                                    $dynamicReviewRuntimeSuggestedKeys->isNotEmpty() &&
                                    $dynamicSourceSectionSuggestedKeys->isNotEmpty() &&
                                    $dynamicSourceSectionSuggestedKeys
                                        ->diff($dynamicReviewRuntimeSuggestedKeys)
                                        ->isEmpty()
                                        ? 'equal'
                                        : ($dynamicReviewRuntimeSuggestedKeys->isNotEmpty() &&
                                        $dynamicSourceSectionSuggestedKeys->isNotEmpty()
                                            ? 'differs'
                                            : 'unknown');
                                $dynamicSourceSectionKeyMatchLabel = match ($dynamicSourceSectionKeyMatch) {
                                    'equal' => __('Equal to runtime option'),
                                    'differs' => __('Differs to runtime option'),
                                    default => __('No runtime option comparison'),
                                };
                            }

                            $dynamicSourceSectionKeyMatchColor = match ($dynamicSourceSectionKeyMatch) {
                                'equal' => 'green',
                                'differs' => 'orange',
                                default => 'zinc',
                            };
                        @endphp

                        <flux:callout
                            class="mt-4"
                            color="{{ $dynamicSourceSection['color'] }}"
                            icon="{{ $dynamicSourceSection['icon'] }}"
                        >
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:callout.heading>{{ $dynamicSourceSection['title'] }}
                                        </flux:callout.heading>
                                        <flux:badge
                                            class="shrink-0"
                                            size="sm"
                                            color="{{ $dynamicSourceSectionKeyMatchColor }}"
                                        >
                                            {{ $dynamicSourceSectionKeyMatchLabel }}
                                        </flux:badge>
                                    </div>
                                    <flux:callout.text>{{ $dynamicSourceSection['description'] }}</flux:callout.text>
                                </div>

                                <flux:badge
                                    class="shrink-0"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Rows') }}: {{ $dynamicSourceSection['rows']->count() }}
                                </flux:badge>
                            </div>

                            @if ($dynamicSourceSection['rows']->isEmpty())
                                <flux:callout
                                    class="mt-3"
                                    color="zinc"
                                    icon="info"
                                >
                                    <flux:callout.text>{{ $dynamicSourceSection['empty'] }}</flux:callout.text>
                                </flux:callout>
                            @else
                                {{-- Table Runtime Options --}}
                                <flux:table class="">
                                    {{-- Table Header Row Runtime Options --}}
                                    <flux:table.columns>
                                        {{-- Table Header Column Source --}}
                                        <flux:table.column>
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ __('Source') }}</span>
                                                <flux:badge
                                                    size="sm"
                                                    variant="subtle"
                                                >
                                                    {{ $dynamicSourceSection['rows']->count() }}
                                                </flux:badge>
                                            </div>
                                        </flux:table.column>
                                        {{-- Table Header Column Classification --}}
                                        <flux:table.column class="w-40">
                                            {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.classification') }}
                                        </flux:table.column>
                                        {{-- Table Header Column Values --}}
                                        <flux:table.column>
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ __('Values') }}</span>
                                                <flux:badge
                                                    size="sm"
                                                    variant="subtle"
                                                >
                                                    {{ $dynamicSourceSection['rows']->sum('values_count') }}
                                                </flux:badge>
                                            </div>
                                        </flux:table.column>
                                        {{-- Table Header Column State --}}
                                        <flux:table.column class="w-32">{{ __('State') }}</flux:table.column>
                                    </flux:table.columns>

                                    {{-- Table Body Rows Runtime Options --}}
                                    <flux:table.rows>
                                        @foreach ($dynamicSourceSection['rows'] as $source)
                                            @php
                                                $dynamicSourceEditorUrl = null;

                                                if (filled($source['source_path'] ?? null)) {
                                                    $dynamicSourceAbsolutePath = str_replace(
                                                        '\\',
                                                        '/',
                                                        base_path((string) $source['source_path']),
                                                    );
                                                    $dynamicSourceEditorPath = str_replace(
                                                        '%2F',
                                                        '/',
                                                        rawurlencode($dynamicSourceAbsolutePath),
                                                    );
                                                    $dynamicSourceEditorLine =
                                                        $source['source_line'] ?? null
                                                            ? ':' . $source['source_line']
                                                            : '';
                                                    $dynamicSourceEditorUrl =
                                                        'vscode://vscode-remote/wsl+' .
                                                        rawurlencode(
                                                            (string) config(
                                                                'translation-workbench.editor.vscode_wsl_distro',
                                                            ),
                                                        ) .
                                                        $dynamicSourceEditorPath .
                                                        $dynamicSourceEditorLine;
                                                }
                                            @endphp

                                            {{-- Table Row Runtime Options --}}
                                            <flux:table.row>
                                                {{-- Table Row Cell Source --}}
                                                <flux:table.cell>
                                                    <div class="flex max-w-2xl items-start gap-2">
                                                        @if ($dynamicSourceEditorUrl)
                                                            <flux:tooltip
                                                                class="max-w-[20rem] space-y-2"
                                                                content="{{ __('Open in VSC') }}"
                                                            >
                                                                <flux:button
                                                                    class="mt-0.5 h-5 w-5 shrink-0"
                                                                    type="button"
                                                                    size="xs"
                                                                    variant="ghost"
                                                                    icon="external-link"
                                                                    icon:class="text-sky-500 dark:text-sky-400"
                                                                    :href="$dynamicSourceEditorUrl"
                                                                    :aria-label="__('Open source in VS Code')"
                                                                />
                                                            </flux:tooltip>
                                                        @endif

                                                        <div class="min-w-0 space-y-1.5">
                                                            <div class="flex flex-wrap items-center gap-1">
                                                                <x-ui.tooltip.simple
                                                                    :title="__('Dynamic source ID')"
                                                                    :text="__(
                                                                        'Internal row ID from translation_workbench_dynamic_sources. This is not a source-code line number.',
                                                                    )"
                                                                >
                                                                    <flux:badge
                                                                        size="sm"
                                                                        color="sky"
                                                                    >
                                                                        #{{ $source['id'] }}
                                                                    </flux:badge>
                                                                </x-ui.tooltip.simple>

                                                                @if ($source['key_id'])
                                                                    <x-ui.tooltip.simple
                                                                        :title="__('Linked translation key')"
                                                                        :text="__(
                                                                            'Foreign key to translation_workbench_keys. The suggested key below is the review anchor for this runtime option source.',
                                                                        )"
                                                                    >
                                                                        <flux:badge
                                                                            size="sm"
                                                                            color="cyan"
                                                                        >
                                                                            {{ __('Key') }}
                                                                            #{{ $source['key_id'] }}
                                                                        </flux:badge>
                                                                    </x-ui.tooltip.simple>
                                                                @endif

                                                                <flux:badge
                                                                    size="sm"
                                                                    color="{{ $source['is_runtime_options'] ?? false ? 'cyan' : 'violet' }}"
                                                                >
                                                                    {{ $source['is_runtime_options'] ?? false ? __('Runtime source') : __('Dynamic consumer') }}
                                                                </flux:badge>

                                                                @if ($source['source_type'])
                                                                    <flux:badge size="sm">
                                                                        {{ $source['source_type'] }}
                                                                    </flux:badge>
                                                                @endif
                                                                @if ($source['source_reference'])
                                                                    <x-ui.tooltip.simple
                                                                        :title="__('Discovery reference')"
                                                                        :text="__(
                                                                            'Reference reported by the runtime collector or dynamic option discovery scanner. This may point to the option definition and can differ from the finding position.',
                                                                        )"
                                                                    >
                                                                        <flux:badge
                                                                            size="sm"
                                                                            color="amber"
                                                                            variant="subtle"
                                                                        >
                                                                            {{ __('Ref') }}
                                                                        </flux:badge>
                                                                    </x-ui.tooltip.simple>
                                                                @endif
                                                                @if ($source['source_path'])
                                                                    <x-ui.tooltip.simple
                                                                        :title="__('Finding position')"
                                                                        :text="__(
                                                                            'Source-code position of the translation finding. The VSC link opens this position.',
                                                                        )"
                                                                    >
                                                                        <flux:badge
                                                                            size="sm"
                                                                            color="zinc"
                                                                        >
                                                                            {{ __('Line') }}
                                                                            {{ $source['source_line'] ?: '—' }}
                                                                        </flux:badge>
                                                                    </x-ui.tooltip.simple>
                                                                @endif
                                                            </div>

                                                            @if ($source['suggested_key'])
                                                                <div
                                                                    class="wrap-anywhere text-wrap font-mono text-xs text-cyan-700 dark:text-cyan-300">
                                                                    {{ __('Suggested key') }}:
                                                                    {{ $source['suggested_key'] }}
                                                                </div>
                                                            @endif

                                                            @if ($source['source_path'])
                                                                <div
                                                                    class="wrap-anywhere font-mono text-xs text-zinc-500 dark:text-zinc-400"
                                                                    title="{{ $source['source_path'] }}"
                                                                >
                                                                    {{ $source['source_path'] }}@if ($source['source_line'])
                                                                        :{{ $source['source_line'] }}
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            @if ($source['source_expression'])
                                                                <div
                                                                    class="wrap-anywhere font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                                                    {{ $source['source_expression'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </flux:table.cell>

                                                {{-- Table Row Cell Classification --}}
                                                <flux:table.cell>
                                                    <div class="flex flex-col items-start gap-1">
                                                        <x-ui.tooltip.simple
                                                            :title="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.classification')"
                                                            :text="__(
                                                                'Combined scanner result from cardinality and origin, for example single_hardcoded or multi_db. Unknown means the scanner could not derive a reliable combined classification yet.',
                                                            )"
                                                        >
                                                            <flux:badge
                                                                size="sm"
                                                                color="{{ $source['classification'] === 'unknown' ? 'orange' : 'green' }}"
                                                            >
                                                                {{ $source['classification'] }}
                                                            </flux:badge>
                                                        </x-ui.tooltip.simple>

                                                        <x-ui.tooltip.simple
                                                            :title="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.cardinality')"
                                                            :text="__(
                                                                'Whether the scanner found one dynamic value or multiple option values. Unknown means this still needs review or better scanner data.',
                                                            )"
                                                        >
                                                            <flux:badge
                                                                size="sm"
                                                                color="{{ $source['cardinality'] === 'multi' ? 'cyan' : ($source['cardinality'] === 'single' ? 'teal' : 'orange') }}"
                                                            >
                                                                {{ $source['cardinality'] }}
                                                            </flux:badge>
                                                        </x-ui.tooltip.simple>

                                                        <x-ui.tooltip.simple
                                                            :title="__('Origin')"
                                                            :text="__(
                                                                'Where the possible values appear to come from, for example hardcoded code options or database-backed data. Unknown means the origin was not detected reliably.',
                                                            )"
                                                        >
                                                            <flux:badge
                                                                size="sm"
                                                                color="{{ $source['origin'] === 'unknown' ? 'orange' : 'sky' }}"
                                                                variant="subtle"
                                                            >
                                                                {{ $source['origin'] }}
                                                            </flux:badge>
                                                        </x-ui.tooltip.simple>
                                                    </div>
                                                </flux:table.cell>

                                                {{-- Table Row Cell Values --}}
                                                <flux:table.cell>
                                                    <div class="max-h-24 min-w-52 space-y-1 overflow-y-auto pr-2">
                                                        @foreach ($source['values'] as $value)
                                                            <div class="font-mono text-xs">
                                                                {{ $value['value_key'] }}
                                                                @if ($value['native_label'])
                                                                    <span class="text-zinc-500 dark:text-zinc-400">
                                                                        {{ $value['native_label'] }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </flux:table.cell>

                                                {{-- Table Row Cell State --}}
                                                <flux:table.cell>
                                                    <div class="flex flex-col items-start gap-1">
                                                        @if (!($source['is_runtime_options'] ?? false) && $dynamicReviewRuntimeSources->isNotEmpty())
                                                            @if (($source['link_review_status'] ?? null) === 'confirmed')
                                                                <flux:badge
                                                                    size="sm"
                                                                    color="green"
                                                                >
                                                                    {{ __('Link confirmed') }}
                                                                </flux:badge>
                                                            @endif

                                                            <flux:button
                                                                type="button"
                                                                size="xs"
                                                                variant="primary"
                                                                color="{{ ($source['link_review_status'] ?? null) === 'confirmed' ? 'zinc' : 'cyan' }}"
                                                                icon="link"
                                                                wire:click="openDynamicSourceLinkConfirm({{ $source['id'] }})"
                                                            >
                                                                {{ ($source['link_review_status'] ?? null) === 'confirmed' ? __('Review link') : __('Confirm link') }}
                                                            </flux:button>
                                                        @endif

                                                        <flux:badge
                                                            size="sm"
                                                            color="{{ in_array($source['status'], ['needs_review', 'unresolved'], true) ? 'red' : 'green' }}"
                                                        >
                                                            {{ $source['status'] }}
                                                        </flux:badge>
                                                        <flux:badge
                                                            size="sm"
                                                            variant="subtle"
                                                        >
                                                            {{ $source['confidence'] }}
                                                        </flux:badge>
                                                    </div>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @endif
                        </flux:callout>
                    @endforeach
                @endif
            </flux:card>
        @endif
    </div>
</flux:modal>
