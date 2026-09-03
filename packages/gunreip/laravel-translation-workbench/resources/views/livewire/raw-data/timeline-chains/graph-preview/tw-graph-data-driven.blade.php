{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/tw-graph-data-driven.blade.php --}}

@php
    $twGraphDataDrivenDevEnabled = filter_var($dev ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) ($dev ?? true);
    $twGraphDataDrivenCoordinatesEnabled = filter_var($coordinates ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) ($coordinates ?? true);
    $twGraphDataDriven = \Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData::fromTimelineChain(
        $mainRow ?? null,
        $rootRows ?? collect(),
        $originRows ?? collect(),
    );
    $twGraphDataDrivenMeta = collect($twGraphDataDriven['meta'] ?? []);
    $twGraphDataDrivenTrunk = collect(data_get($twGraphDataDriven, 'strangs.trunk', []));
    $twGraphDataDrivenMerge = collect(data_get($twGraphDataDriven, 'strangs.merge.strangs', []));
    $twGraphDataDrivenBranch = collect(data_get($twGraphDataDriven, 'strangs.branch.strangs', []));
    $twGraphDataDrivenMergeOutcomes = collect(data_get($twGraphDataDriven, 'merge_outcomes.rows', []));
    $twGraphDataDrivenMergeOutcomeSummary = collect(data_get($twGraphDataDriven, 'merge_outcomes.summary', []));
    $twGraphDataDrivenPreview = collect(data_get($twGraphDataDriven, 'render_preview', []));
    $twGraphDataDrivenPreviewGraph = collect($twGraphDataDrivenPreview->get('graph', []));
    $twGraphDataDrivenPreviewTrunk = collect($twGraphDataDrivenPreview->get('trunk', []));
    $twGraphDataDrivenPreviewMerge = collect($twGraphDataDrivenPreview->get('merge', []));
    $twGraphDataDrivenPreviewMerges = collect($twGraphDataDrivenPreview->get('merges', []));
    $twGraphDataDrivenPreviewRekeys = collect($twGraphDataDrivenPreview->get('rekeys', []));
    $twGraphDataDrivenPreviewBranches = collect($twGraphDataDrivenPreview->get('branches', []));
    $twGraphDataDrivenPreviewLimits = collect($twGraphDataDrivenPreview->get('limits', []));
    $twGraphDataDrivenClassificationNoiseReport = collect(
        $twGraphDataDrivenPreviewLimits->get('classification_noise_report', []),
    );
    $twGraphDataDrivenFindingInspect = \Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData::inspectFinding(
        5486,
        $mainRow ?? null,
        $originRows ?? collect(),
    );
    $twGraphDataDrivenInspectFinding = collect($twGraphDataDrivenFindingInspect['finding'] ?? []);
    $twGraphDataDrivenInspectOrigin = collect($twGraphDataDrivenFindingInspect['origin_row'] ?? []);
    $twGraphDataDrivenInspectRendered = collect($twGraphDataDrivenFindingInspect['rendered_as'] ?? []);
    $twGraphDataDrivenInspectShared = collect($twGraphDataDrivenFindingInspect['shared_candidate'] ?? []);
    $twGraphDataDrivenInspectReviews = collect($twGraphDataDrivenFindingInspect['reviews'] ?? []);
    $twGraphDataDrivenInspectTimeline = collect($twGraphDataDrivenFindingInspect['timeline_events'] ?? []);
    $twGraphDataDrivenInspectLangValues = collect($twGraphDataDrivenFindingInspect['lang_values'] ?? []);
    $twGraphDataDrivenInspectKeys = collect($twGraphDataDrivenFindingInspect['related_translation_keys'] ?? []);
    $twGraphDataDrivenJson = json_encode(
        [
            'meta' => $twGraphDataDriven['meta'] ?? [],
            'facts' => [
                'key_ids' => data_get($twGraphDataDriven, 'facts.key_ids', []),
                'finding_ids' => data_get($twGraphDataDriven, 'facts.finding_ids', []),
                'review_ids' => data_get($twGraphDataDriven, 'facts.review_ids', []),
                'lang_value_ids' => data_get($twGraphDataDriven, 'facts.lang_value_ids', []),
                'timeline_event_count' => count((array) data_get($twGraphDataDriven, 'facts.timeline_event_ids', [])),
                'related_translation_keys' => data_get($twGraphDataDriven, 'facts.related_translation_keys', []),
            ],
            'render_preview' => $twGraphDataDriven['render_preview'] ?? [],
            'merge_outcomes' => [
                'summary' => data_get($twGraphDataDriven, 'merge_outcomes.summary', []),
                'row_count' => count((array) data_get($twGraphDataDriven, 'merge_outcomes.rows', [])),
            ],
        ],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    );
@endphp

<flux:callout
    class="mt-4"
    color="cyan"
    icon="database-zap"
>
    <flux:callout.heading>
        <span class="inline-flex flex-wrap items-center gap-2">
            <span>{{ __('TW Graph data-driven foundation') }}</span>
            <flux:badge
                size="sm"
                color="cyan"
            >
                {{ $twGraphDataDrivenMeta->get('graph_id') }}
            </flux:badge>
            <flux:badge
                size="sm"
                color="zinc"
            >
                {{ __('Intent only') }}
            </flux:badge>
        </span>
    </flux:callout.heading>

    <flux:callout.text>
        {{ __('First neutral graph data model derived from the selected timeline-chain row. This does not render geometry yet; it only describes the trunk, merge and branch intent that the authoring components can consume later.') }}
    </flux:callout.text>

    <div class="mt-3 grid gap-3 xl:grid-cols-4">
        <flux:callout
            color="green"
            icon="git-branch"
        >
            <flux:callout.heading>{{ __('Trunk') }}</flux:callout.heading>
            <flux:callout.text>
                <div class="space-y-2 text-xs">
                    <div class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                        {{ $twGraphDataDrivenTrunk->get('key') }}
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <flux:badge
                            size="sm"
                            color="green"
                        >
                            {{ __('Root key') }}:
                            #{{ $twGraphDataDrivenTrunk->get('root_key_id') ?: '?' }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="sky"
                        >
                            {{ __('Events') }}:
                            {{ number_format((int) $twGraphDataDrivenTrunk->get('event_count', 0)) }}
                        </flux:badge>
                    </div>
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ (int) $twGraphDataDrivenPreviewLimits->get('potential_dead_dev_events', 0) > 0 ? 'amber' : 'zinc' }}"
            icon="list-tree"
        >
            <flux:callout.heading>{{ __('Event compaction') }}</flux:callout.heading>
            <flux:callout.text>
                <div class="space-y-2 text-xs">
                    <div class="flex flex-wrap gap-1">
                        <flux:badge
                            size="sm"
                            color="zinc"
                        >
                            {{ __('Events') }}:
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('available_events', 0)) }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="green"
                        >
                            {{ __('Normal') }}:
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('normal_events', 0)) }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="cyan"
                        >
                            {{ __('Types') }}:
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('available_event_types', 0)) }}
                        </flux:badge>
                        @if ((int) $twGraphDataDrivenPreviewLimits->get('potential_dead_dev_events', 0) > 0)
                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ __('dead DEV?') }}:
                                {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('potential_dead_dev_events', 0)) }}
                            </flux:badge>
                        @endif
                    </div>

                    @if ((int) $twGraphDataDrivenPreviewLimits->get('potential_dead_dev_events', 0) > 0)
                        <flux:text class="text-xs text-zinc-600 dark:text-zinc-300">
                            {{ __('Potential historical classification noise. Suggested retained classification events: :count.', ['count' => number_format((int) $twGraphDataDrivenPreviewLimits->get('retained_classification_events', 0))]) }}
                        </flux:text>

                        @if ($twGraphDataDrivenClassificationNoiseReport->isNotEmpty())
                            <div class="space-y-1">
                                <div class="flex flex-wrap gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ $twGraphDataDrivenClassificationNoiseReport->get('first_seen') }}
                                        ->
                                        {{ $twGraphDataDrivenClassificationNoiseReport->get('last_seen') }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >
                                        {{ __('variants') }}:
                                        {{ number_format((int) $twGraphDataDrivenClassificationNoiseReport->get('state_variant_count', 0)) }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >
                                        {{ __('keys/findings') }}:
                                        {{ number_format((int) $twGraphDataDrivenClassificationNoiseReport->get('key_count', 0)) }}
                                        /
                                        {{ number_format((int) $twGraphDataDrivenClassificationNoiseReport->get('finding_count', 0)) }}
                                    </flux:badge>
                                </div>

                                @foreach (collect($twGraphDataDrivenClassificationNoiseReport->get('top_sources', []))->take(2) as $noiseSource)
                                    <div
                                        class="wrap-anywhere font-mono text-[0.68rem] leading-tight text-zinc-600 dark:text-zinc-300">
                                        {{ number_format((int) data_get($noiseSource, 'total', 0)) }}
                                        x
                                        {{ data_get($noiseSource, 'source_path') }}:{{ data_get($noiseSource, 'source_line') }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <flux:text class="text-xs text-zinc-500">
                            {{ __('No high-volume DEV classification noise detected.') }}
                        </flux:text>
                    @endif
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ $twGraphDataDrivenMerge->isNotEmpty() ? 'amber' : 'zinc' }}"
            icon="git-merge"
        >
            <flux:callout.heading>{{ __('Merge candidates') }}</flux:callout.heading>
            <flux:callout.text>
                <div class="space-y-1">
                    @forelse ($twGraphDataDrivenMerge as $mergeStrang)
                        <div class="flex flex-wrap items-center gap-1 text-xs">
                            <flux:badge
                                size="sm"
                                color="{{ $mergeStrang['side'] === 'left' ? 'amber' : 'green' }}"
                            >
                                {{ $mergeStrang['side'] }}
                            </flux:badge>
                            <span class="wrap-anywhere font-mono text-zinc-700 dark:text-zinc-200">
                                {{ $mergeStrang['origin_key'] ?: $mergeStrang['source_root'] }}
                            </span>
                        </div>
                    @empty
                        <flux:text class="text-xs text-zinc-500">
                            {{ __('No origin merge candidates in the current sample.') }}
                        </flux:text>
                    @endforelse
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ $twGraphDataDrivenBranch->isNotEmpty() ? 'fuchsia' : 'zinc' }}"
            icon="git-branch"
        >
            <flux:callout.heading>{{ __('Branch candidates') }}</flux:callout.heading>
            <flux:callout.text>
                <div class="space-y-1">
                    @forelse ($twGraphDataDrivenBranch->take(5) as $branchStrang)
                        <div class="flex flex-wrap items-center gap-1 text-xs">
                            <flux:badge
                                size="sm"
                                color="{{ $branchStrang['side'] === 'left' ? 'fuchsia' : 'violet' }}"
                            >
                                {{ $branchStrang['side'] }}
                            </flux:badge>
                            <span class="wrap-anywhere text-zinc-700 dark:text-zinc-200">
                                {{ $branchStrang['branch'] }} · {{ $branchStrang['event'] }}
                            </span>
                        </div>
                    @empty
                        <flux:text class="text-xs text-zinc-500">
                            {{ __('No branch candidates in the current sample.') }}
                        </flux:text>
                    @endforelse

                    @if ($twGraphDataDrivenBranch->count() > 5)
                        <flux:badge
                            size="sm"
                            color="fuchsia"
                        >
                            {{ __('+:count more', ['count' => number_format($twGraphDataDrivenBranch->count() - 5)]) }}
                        </flux:badge>
                    @endif
                </div>
            </flux:callout.text>
        </flux:callout>
    </div>

    @if (in_array(
            $twGraphDataDrivenPreview->get('mode'),
            ['trunk_only', 'trunk_with_single_merge', 'trunk_with_limited_merge'],
            true))
        <div
            x-data="{
                twGraphDataDrivenDev: @js($twGraphDataDrivenDevEnabled),
                twGraphDataDrivenCoordinates: @js($twGraphDataDrivenCoordinatesEnabled),
                twGraphDebugBoundsCollisionOnly: false,
            }"
        >
        <flux:callout
            class="mt-3"
            color="green"
            icon="git-branch"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span class="inline-flex flex-wrap items-center gap-2">
                        <span>{{ __('Data-driven graph preview') }}</span>
                        <flux:badge
                            size="sm"
                            color="green"
                        >
                            {{ $twGraphDataDrivenPreviewMerges->isNotEmpty() ? __('Trunk + merge') : __('Trunk only') }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="zinc"
                        >
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('rendered_event_labels', 0)) }}
                            /
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('normal_events', $twGraphDataDrivenPreviewLimits->get('available_events', 0))) }}
                            {{ __('normal events') }}
                        </flux:badge>
                        @if ((int) $twGraphDataDrivenPreviewLimits->get('compacted_events', 0) > 0)
                            <flux:badge
                                size="sm"
                                color="cyan"
                            >
                                +{{ number_format((int) $twGraphDataDrivenPreviewLimits->get('compacted_events', 0)) }}
                                {{ __('compacted') }}
                            </flux:badge>
                        @endif
                        @if ((int) $twGraphDataDrivenPreviewLimits->get('potential_dead_dev_events', 0) > 0)
                            <flux:badge
                                title="{{ __('Potential historical DEV classification noise. These events are not deleted here; this badge only marks candidates that could later be classified as dead_dev_event and excluded from normal timeline views.') }}"
                                size="sm"
                                color="amber"
                            >
                                {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('potential_dead_dev_events', 0)) }}
                                {{ __('dead DEV?') }}
                            </flux:badge>
                        @endif
                        <flux:badge
                            size="sm"
                            color="{{ (int) $twGraphDataDrivenPreviewLimits->get('rendered_merge_candidates', 0) > 0 ? 'amber' : 'zinc' }}"
                        >
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('rendered_merge_candidates', 0)) }}
                            /
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('available_merge_strangs', 0)) }}
                            {{ __('merge') }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="{{ (int) $twGraphDataDrivenPreviewLimits->get('rendered_branch_candidates', 0) > 0 ? 'red' : 'zinc' }}"
                        >
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('rendered_branch_candidates', 0)) }}
                            /
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('available_branch_candidates', 0)) }}
                            {{ __('branch findings') }}
                        </flux:badge>
                        <flux:badge
                            size="sm"
                            color="{{ (int) $twGraphDataDrivenPreviewLimits->get('rendered_rekey_strangs', 0) > 0 ? 'sky' : 'zinc' }}"
                        >
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('rendered_rekey_strangs', 0)) }}
                            /
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('available_rekey_relations', 0)) }}
                            {{ __('rekey') }}
                        </flux:badge>
                        <flux:badge
                            title="trunk label padding {{ data_get($twGraphDataDrivenPreviewGraph->get('horizontal_padding_debug', []), 'trunk_label_level', 'default') }} | left strangs: {{ data_get($twGraphDataDrivenPreviewGraph->get('horizontal_padding_debug', []), 'has_left_strangs') ? 'yes' : 'no' }} | padding: {{ data_get($twGraphDataDrivenPreviewGraph->get('horizontal_padding_debug', []), 'horizontal_padding', '12rem') }}"
                            size="sm"
                            color="{{ in_array(data_get($twGraphDataDrivenPreviewGraph->get('horizontal_padding_debug', []), 'trunk_label_level'), ['halfLong', 'long'], true) ? 'lime' : 'zinc' }}"
                        >
                            {{ __('trunk label padding') }}:
                            {{ data_get($twGraphDataDrivenPreviewGraph->get('horizontal_padding_debug', []), 'trunk_label_level', 'default') }}
                        </flux:badge>
                    </span>

                    @if ($twGraphDataDrivenDevEnabled)
                    <span class="flex flex-wrap items-center justify-end gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 text-xs opacity-70 hover:cursor-pointer"
                            x-on:click.stop="twGraphDataDrivenDev = !twGraphDataDrivenDev"
                        >
                            <span
                                class="relative inline-flex h-5 w-9 items-center rounded-full border transition"
                                x-bind:class="twGraphDataDrivenDev ? 'border-green-500 bg-green-500' : 'border-zinc-300 bg-zinc-200 dark:border-zinc-700 dark:bg-zinc-800'"
                            >
                                <span
                                    class="inline-block h-4 w-4 rounded-full bg-white shadow transition"
                                    x-bind:class="twGraphDataDrivenDev ? 'translate-x-4' : 'translate-x-0.5'"
                                ></span>
                            </span>
                            <span>{{ __('DEV') }}</span>
                        </button>

                        @if ($twGraphDataDrivenCoordinatesEnabled)
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 text-xs opacity-70 hover:cursor-pointer"
                                x-on:click.stop="twGraphDataDrivenCoordinates = !twGraphDataDrivenCoordinates"
                            >
                                <span
                                    class="relative inline-flex h-5 w-9 items-center rounded-full border transition"
                                    x-bind:class="twGraphDataDrivenCoordinates ? 'border-sky-500 bg-sky-500' : 'border-zinc-300 bg-zinc-200 dark:border-zinc-700 dark:bg-zinc-800'"
                                >
                                    <span
                                        class="inline-block h-4 w-4 rounded-full bg-white shadow transition"
                                        x-bind:class="twGraphDataDrivenCoordinates ? 'translate-x-4' : 'translate-x-0.5'"
                                    ></span>
                                </span>
                                <span>{{ __('X/Y') }}</span>
                            </button>
                        @endif
                    </span>
                    @endif
                </span>
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('Reduced visual pass: the canonical trunk is rendered with up to five origin merge candidates. Additional merge and branch candidates stay in the data model until each mapping is easy to verify.') }}
            </flux:callout.text>

            <div
                class="mt-3"
                x-bind:class="{
                    'tw-graph-protocol-dev-disabled': !twGraphDataDrivenDev,
                    'tw-graph-protocol-coordinates-disabled': !twGraphDataDrivenCoordinates,
                }"
            >
                @if (filled(data_get($twGraphDataDrivenPreviewGraph->get('header'), 'text')))
                    <flux:callout.heading class="mb-2">
                        @foreach ((array) data_get($twGraphDataDrivenPreviewGraph->get('header'), 'text', []) as $graphHeaderLine)
                            <span>{{ $graphHeaderLine }}</span>
                        @endforeach
                    </flux:callout.heading>
                @endif

                @php
                    $twGraphDebugOverlayElementId = static fn(
                        mixed $id,
                    ): string => \Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier::normalize($id);
                    $twGraphDebugOverlayRows = collect(
                        data_get($twGraphDataDrivenPreviewTrunk->get('layout', []), 'trunkBoundsDebug', []),
                    )
                        ->merge(
                            $twGraphDataDrivenPreviewMerges->flatMap(
                                fn($merge) => (array) data_get($merge, 'layout.mergeBoundsDebug', []),
                            ),
                        )
                        ->merge(
                            $twGraphDataDrivenPreviewRekeys->flatMap(
                                fn($rekey) => (array) data_get($rekey, 'layout.rekeyBoundsDebug', []),
                            ),
                        )
                        ->merge(
                            $twGraphDataDrivenPreviewBranches->flatMap(
                                fn($branch) => (array) data_get($branch, 'layout.branchBoundsDebug', []),
                            ),
                        )
                        ->values();
                    $twGraphDebugBoundNumberById = $twGraphDebugOverlayRows
                        ->mapWithKeys(
                            fn($debugBox, int $index) => [
                                $twGraphDebugOverlayElementId(
                                    data_get($debugBox, 'id', data_get($debugBox, 'renderId', '')),
                                ) => $index + 1,
                            ],
                        )
                        ->all();
                    $twGraphDebugBoundNumber = static fn(mixed $id): int|string => $twGraphDebugBoundNumberById[
                        $twGraphDebugOverlayElementId($id)
                    ] ?? '';
                @endphp

                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-12"
                    :graph-id="$twGraphDataDrivenPreviewGraph->get('graph_id')"
                    :dev="$twGraphDataDrivenDevEnabled"
                    :coordinates="$twGraphDataDrivenCoordinatesEnabled"
                    :color="$twGraphDataDrivenPreviewGraph->get('color', 'cyan')"
                    :line-length="$twGraphDataDrivenPreviewGraph->get('line_length', '4rem')"
                    :line-width="$twGraphDataDrivenPreviewGraph->get('line_width', '0.25rem')"
                    :node-size="$twGraphDataDrivenPreviewGraph->get('node_size', '0.95rem')"
                    :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size', '2.75rem')"
                    :cap-length="$twGraphDataDrivenPreviewGraph->get('cap_length', '1.75rem')"
                    :bridge-length="$twGraphDataDrivenPreviewGraph->get('bridge_length', '4rem')"
                    :stem-length="$twGraphDataDrivenPreviewGraph->get('stem_length', '4rem')"
                    :connector-length="$twGraphDataDrivenPreviewGraph->get('connector_length', '2rem')"
                    :connector-gap="$twGraphDataDrivenPreviewGraph->get('connector_gap', '0.25rem')"
                    :slot-min-height="$twGraphDataDrivenPreviewGraph->get('slot_min_height', '34rem')"
                    :horizontal-padding="$twGraphDataDrivenPreviewGraph->get('horizontal_padding', '12rem')"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        :color="$twGraphDataDrivenPreviewTrunk->get('color', 'green')"
                        :dev-mode="$twGraphDataDrivenDevEnabled"
                        :stem-length="$twGraphDataDrivenPreviewGraph->get('stem_length')"
                        :start-length="$twGraphDataDrivenPreviewTrunk->get('start_length')"
                        :start-shift-enabled="false"
                        :start-shift-length="$twGraphDataDrivenPreviewTrunk->get('start_shift_length')"
                        :path-count="$twGraphDataDrivenPreviewTrunk->get('path_count', 4)"
                        :path-lengths="$twGraphDataDrivenPreviewTrunk->get('path_lengths', [])"
                        :end-length="$twGraphDataDrivenPreviewTrunk->get('end_length')"
                        :start-label="$twGraphDataDrivenPreviewTrunk->get('start_label')"
                        :end-label="$twGraphDataDrivenPreviewTrunk->get('end_label')"
                        :start-node-labels="$twGraphDataDrivenPreviewTrunk->get('start_node_labels', [])"
                        :node-labels="$twGraphDataDrivenPreviewTrunk->get('node_labels', [])"
                    />

                    @if ($twGraphDataDrivenDevEnabled)
                        @foreach (collect(data_get($twGraphDataDrivenPreviewTrunk->get('layout', []), 'trunkBoundsDebug', [])) as $trunkBoundsDebug)
                            @php
                                $trunkBoundsDebugNumber = $twGraphDebugBoundNumber(data_get($trunkBoundsDebug, 'id'));
                                $trunkBoundsDebugTooltip =
                                    'Trunk bounds' .
                                    ' | ' .
                                    data_get($trunkBoundsDebug, 'type') .
                                    ': ' .
                                    \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label(
                                        data_get($trunkBoundsDebug, 'id'),
                                    ) .
                                    ' | x=' .
                                    data_get($trunkBoundsDebug, 'x', '0rem') .
                                    ' y=' .
                                    data_get($trunkBoundsDebug, 'y', '0rem') .
                                    ' width=' .
                                    data_get($trunkBoundsDebug, 'width', '0rem') .
                                    ' height=' .
                                    data_get($trunkBoundsDebug, 'height', '0rem');
                            @endphp

                            <span
                                class="tw-graph-protocol-dev-only absolute z-[1] cursor-copy rounded-sm border border-dashed border-green-400/80 bg-green-300/5"
                                data-tw-graph-path="{{ $trunkBoundsDebugTooltip }}"
                                title="{{ $trunkBoundsDebugTooltip }}"
                                style="
                                    left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($trunkBoundsDebug, 'x', '0rem') }});
                                    bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($trunkBoundsDebug, 'y', '0rem') }});
                                    width: {{ data_get($trunkBoundsDebug, 'width', '0rem') }};
                                    height: {{ data_get($trunkBoundsDebug, 'height', '0rem') }};
                                "
                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                            >
                                <span
                                    class="absolute left-0 top-0 -translate-x-1/2 -translate-y-1/2 rounded-sm border border-green-500/70 bg-white px-1 font-mono text-[0.6rem] leading-4 text-green-700 shadow-sm dark:bg-zinc-950 dark:text-green-300"
                                >
                                    {{ $trunkBoundsDebugNumber }}
                                </span>
                            </span>
                        @endforeach
                    @endif

                    @foreach ($twGraphDataDrivenPreviewRekeys as $rekeyIndex => $rekeyPreview)
                        @php
                            $rekeyPreview = collect($rekeyPreview);
                            $rekeyKind = $rekeyPreview->get('kind');
                            $rekeySide = $rekeyPreview->get('side');
                        @endphp

                        @if ($rekeyKind === 'source' && $rekeySide === 'right')
                            <x-translation-workbench::ui.tw-graph.strang.rekey-source-right
                                :component-counter="$rekeyPreview->get('component_counter', $rekeyIndex + 1)"
                                :color="$rekeyPreview->get('color', 'sky')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                :stem-length="$rekeyPreview->get('stem_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :start-label="$rekeyPreview->get('start_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="8"
                            />
                        @elseif ($rekeyKind === 'source')
                            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                                :component-counter="$rekeyPreview->get('component_counter', $rekeyIndex + 1)"
                                :color="$rekeyPreview->get('color', 'sky')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                :stem-length="$rekeyPreview->get('stem_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :start-label="$rekeyPreview->get('start_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="8"
                            />
                        @elseif ($rekeyKind === 'target' && $rekeySide === 'left')
                            <x-translation-workbench::ui.tw-graph.strang.rekey-target-left
                                :component-counter="$rekeyPreview->get('component_counter', $rekeyIndex + 1)"
                                :color="$rekeyPreview->get('color', 'sky')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.3.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                :stem-length="$rekeyPreview->get('stem_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :end-length="$rekeyPreview->get('end_length')"
                                :cap-length="$rekeyPreview->get('cap_length')"
                                :end-label="$rekeyPreview->get('end_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="7"
                            />
                        @else
                            <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                                :component-counter="$rekeyPreview->get('component_counter', $rekeyIndex + 1)"
                                :color="$rekeyPreview->get('color', 'sky')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.3.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                :stem-length="$rekeyPreview->get('stem_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :end-length="$rekeyPreview->get('end_length')"
                                :cap-length="$rekeyPreview->get('cap_length')"
                                :end-label="$rekeyPreview->get('end_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="7"
                            />
                        @endif

                        @if ($twGraphDataDrivenDevEnabled)
                            @foreach (collect(data_get($rekeyPreview, 'layout.rekeyBoundsDebug', [])) as $rekeyBoundsDebug)
                            @php
                                $rekeyBoundsDebugNumber = $twGraphDebugBoundNumber(data_get($rekeyBoundsDebug, 'id'));
                                $rekeyBoundsDebugType = (string) data_get($rekeyBoundsDebug, 'type', '');
                                $rekeyBoundsDebugIsSubBox =
                                    str_ends_with($rekeyBoundsDebugType, '-start') ||
                                    str_ends_with($rekeyBoundsDebugType, '-labels') ||
                                    str_ends_with($rekeyBoundsDebugType, '-tail') ||
                                    $rekeyBoundsDebugType === 'rekey-target-body' ||
                                    $rekeyBoundsDebugType === 'rekey-target-end';
                                $rekeyBoundsDebugTooltip =
                                    'Rekey bounds' .
                                    ' | ' .
                                    data_get($rekeyBoundsDebug, 'side', 'n/a') .
                                    ' | ' .
                                    $rekeyBoundsDebugType .
                                    ': ' .
                                    \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label(
                                        data_get($rekeyBoundsDebug, 'id'),
                                    ) .
                                    ' | x=' .
                                    data_get($rekeyBoundsDebug, 'x', '0rem') .
                                    ' y=' .
                                    data_get($rekeyBoundsDebug, 'y', '0rem') .
                                    ' width=' .
                                    data_get($rekeyBoundsDebug, 'width', '0rem') .
                                    ' height=' .
                                    data_get($rekeyBoundsDebug, 'height', '0rem');
                            @endphp

                            <span
                                data-tw-graph-path="{{ $rekeyBoundsDebugTooltip }}"
                                title="{{ $rekeyBoundsDebugTooltip }}"
                                style="
                                    left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($rekeyBoundsDebug, 'x', '0rem') }});
                                    bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($rekeyBoundsDebug, 'y', '0rem') }});
                                    width: {{ data_get($rekeyBoundsDebug, 'width', '0rem') }};
                                    height: {{ data_get($rekeyBoundsDebug, 'height', '0rem') }};
                                "
                                @class([
                                    'tw-graph-protocol-dev-only absolute cursor-copy rounded-sm border border-dashed',
                                    'z-[2] border-lime-400/80 bg-lime-300/5' => $rekeyBoundsDebugIsSubBox,
                                    'z-[1] border-sky-400/75 bg-sky-300/5' => !$rekeyBoundsDebugIsSubBox,
                                ])
                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                            >
                                <span @class([
                                    'absolute left-0 top-0 -translate-x-1/2 -translate-y-1/2 rounded-sm border bg-white px-1 font-mono text-[0.6rem] leading-4 shadow-sm dark:bg-zinc-950',
                                    'border-lime-500/70 text-lime-700 dark:text-lime-300' => $rekeyBoundsDebugIsSubBox,
                                    'border-sky-500/70 text-sky-700 dark:text-sky-300' => !$rekeyBoundsDebugIsSubBox,
                                ])>
                                    {{ $rekeyBoundsDebugNumber }}
                                </span>
                            </span>
                            @endforeach
                        @endif
                    @endforeach

                    @if ($twGraphDataDrivenPreviewMerge->isNotEmpty())
                        @foreach ($twGraphDataDrivenPreviewMerges as $mergeIndex => $mergePreview)
                            @php
                                $mergePreview = collect($mergePreview);
                            @endphp

                            @if ($mergePreview->get('side') === 'right')
                                <x-translation-workbench::ui.tw-graph.strang.merge-right
                                    :component-counter="$mergeIndex + 1"
                                    :color="$mergePreview->get('color', 'amber')"
                                    :dev-mode="$twGraphDataDrivenDevEnabled"
                                    :attach-to="$mergePreview->get('attach_to', 'strang.trunk.path.1.end')"
                                    :bridge-length="$mergePreview->get('bridge_length') ??
                                        $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                    :stem-length="$mergePreview->get('stem_length') ??
                                        $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                    :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                    :stem-continuation="$mergePreview->get('stem_continuation', [])"
                                    :arc-sizes="$mergePreview->get('arc_sizes', [])"
                                    :start-label="$mergePreview->get('start_label')"
                                    :node-labels="$mergePreview->get('node_labels', [])"
                                    :extension-count="$mergePreview->get('extension_count', 0)"
                                    :extension-stem-lengths="$mergePreview->get('extension_stem_lengths', [])"
                                    :extension-stem-continuations="$mergePreview->get('extension_stem_continuations', [])"
                                    :extension-bridge-continuations="$mergePreview->get('extension_bridge_continuations', [])"
                                    :extension-arc-sizes="$mergePreview->get('extension_arc_sizes', [])"
                                    :extension-node-labels="$mergePreview->get('extension_node_labels', [])"
                                />
                            @else
                                <x-translation-workbench::ui.tw-graph.strang.merge-left
                                    :component-counter="$mergeIndex + 1"
                                    :color="$mergePreview->get('color', 'amber')"
                                    :dev-mode="$twGraphDataDrivenDevEnabled"
                                    :attach-to="$mergePreview->get('attach_to', 'strang.trunk.path.1.end')"
                                    :bridge-length="$mergePreview->get('bridge_length') ??
                                        $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                    :stem-length="$mergePreview->get('stem_length') ??
                                        $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                    :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                    :stem-continuation="$mergePreview->get('stem_continuation', [])"
                                    :arc-sizes="$mergePreview->get('arc_sizes', [])"
                                    :start-label="$mergePreview->get('start_label')"
                                    :node-labels="$mergePreview->get('node_labels', [])"
                                    :extension-count="$mergePreview->get('extension_count', 0)"
                                    :extension-stem-lengths="$mergePreview->get('extension_stem_lengths', [])"
                                    :extension-stem-continuations="$mergePreview->get('extension_stem_continuations', [])"
                                    :extension-bridge-continuations="$mergePreview->get('extension_bridge_continuations', [])"
                                    :extension-arc-sizes="$mergePreview->get('extension_arc_sizes', [])"
                                    :extension-node-labels="$mergePreview->get('extension_node_labels', [])"
                                />
                            @endif

                            @if ($twGraphDataDrivenDevEnabled)
                                @foreach (collect(data_get($mergePreview, 'layout.mergeBoundsDebug', [])) as $mergeBoundsDebug)
                                @php
                                    $mergeBoundsDebugNumber = $twGraphDebugBoundNumber(
                                        data_get($mergeBoundsDebug, 'id'),
                                    );
                                    $mergeBoundsDebugType = (string) data_get($mergeBoundsDebug, 'type', '');
                                    $mergeBoundsDebugIsSubBox =
                                        str_ends_with($mergeBoundsDebugType, '-start') ||
                                        str_ends_with($mergeBoundsDebugType, '-labels') ||
                                        str_ends_with($mergeBoundsDebugType, '-tail');
                                    $mergeBoundsDebugTooltip =
                                        'Merge bounds' .
                                        ' | ' .
                                        data_get($mergeBoundsDebug, 'side', 'n/a') .
                                        ' | ' .
                                        $mergeBoundsDebugType .
                                        ': ' .
                                        \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label(
                                            data_get($mergeBoundsDebug, 'id'),
                                        ) .
                                        ' | x=' .
                                        data_get($mergeBoundsDebug, 'x', '0rem') .
                                        ' y=' .
                                        data_get($mergeBoundsDebug, 'y', '0rem') .
                                        ' width=' .
                                        data_get($mergeBoundsDebug, 'width', '0rem') .
                                        ' height=' .
                                        data_get($mergeBoundsDebug, 'height', '0rem');
                                @endphp

                                <span
                                    data-tw-graph-path="{{ $mergeBoundsDebugTooltip }}"
                                    title="{{ $mergeBoundsDebugTooltip }}"
                                    style="
                                        left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($mergeBoundsDebug, 'x', '0rem') }});
                                        bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($mergeBoundsDebug, 'y', '0rem') }});
                                        width: {{ data_get($mergeBoundsDebug, 'width', '0rem') }};
                                        height: {{ data_get($mergeBoundsDebug, 'height', '0rem') }};
                                    "
                                    @class([
                                        'tw-graph-protocol-dev-only absolute cursor-copy rounded-sm border border-dashed',
                                        'z-[2] border-lime-400/80 bg-lime-300/5' => $mergeBoundsDebugIsSubBox,
                                        'z-[1] border-amber-400/75 bg-amber-300/5' => !$mergeBoundsDebugIsSubBox,
                                    ])
                                    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                                >
                                    <span @class([
                                        'absolute left-0 top-0 -translate-x-1/2 -translate-y-1/2 rounded-sm border bg-white px-1 font-mono text-[0.6rem] leading-4 shadow-sm dark:bg-zinc-950',
                                        'border-lime-500/70 text-lime-700 dark:text-lime-300' => $mergeBoundsDebugIsSubBox,
                                        'border-amber-500/70 text-amber-700 dark:text-amber-300' => !$mergeBoundsDebugIsSubBox,
                                    ])>
                                        {{ $mergeBoundsDebugNumber }}
                                    </span>
                                </span>
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @foreach ($twGraphDataDrivenPreviewBranches as $branchIndex => $branchPreview)
                        @php
                            $branchPreview = collect($branchPreview);
                        @endphp

                        @if ($branchPreview->get('side') === 'right')
                            <x-translation-workbench::ui.tw-graph.strang.branch-right
                                :id="$branchPreview->get('id')"
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                :attach-to="$branchPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :entry-stem-length="$branchPreview->get('entry_stem_length')"
                                :bridge-length="$branchPreview->get('bridge_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                :step="$branchPreview->get('step')"
                                :stem-length="$branchPreview->get('stem_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                :stem-continuation="$branchPreview->get('stem_continuation', [])"
                                :branch-extension="$branchPreview->get('branch_extension', [])"
                                :node-labels="$branchPreview->get('node_labels', [])"
                                :z-index="6 - ($branchIndex % 4)"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.branch-end
                                side="right"
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                attach-to="strang.branch-right.end"
                                :length="$branchPreview->get('end_length')"
                                :cap-length="$branchPreview->get('end_cap_length')"
                                :end-label="$branchPreview->get('end_label')"
                                :counter-start="$branchPreview->get('end_counter_start')"
                                :z-index="5 - ($branchIndex % 4)"
                            />
                        @else
                            <x-translation-workbench::ui.tw-graph.strang.branch-left
                                :id="$branchPreview->get('id')"
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                :attach-to="$branchPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :entry-stem-length="$branchPreview->get('entry_stem_length')"
                                :bridge-length="$branchPreview->get('bridge_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('bridge_length')"
                                :step="$branchPreview->get('step')"
                                :stem-length="$branchPreview->get('stem_length') ??
                                    $twGraphDataDrivenPreviewGraph->get('stem_length')"
                                :arc-size="$twGraphDataDrivenPreviewGraph->get('arc_size')"
                                :stem-continuation="$branchPreview->get('stem_continuation', [])"
                                :branch-extension="$branchPreview->get('branch_extension', [])"
                                :node-labels="$branchPreview->get('node_labels', [])"
                                :z-index="6 - ($branchIndex % 4)"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.branch-end
                                side="left"
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                :dev-mode="$twGraphDataDrivenDevEnabled"
                                attach-to="strang.branch-left.end"
                                :length="$branchPreview->get('end_length')"
                                :cap-length="$branchPreview->get('end_cap_length')"
                                :end-label="$branchPreview->get('end_label')"
                                :counter-start="$branchPreview->get('end_counter_start')"
                                :z-index="5 - ($branchIndex % 4)"
                            />
                        @endif

                        @if ($twGraphDataDrivenDevEnabled)
                            @foreach (collect(data_get($branchPreview, 'layout.branchBoundsDebug', [])) as $branchBoundsDebug)
                            @php
                                $branchBoundsDebugNumber = $twGraphDebugBoundNumber(data_get($branchBoundsDebug, 'id'));
                                $branchBoundsDebugTooltip =
                                    'Branch bounds' .
                                    ' | ' .
                                    data_get($branchBoundsDebug, 'type') .
                                    ': ' .
                                    \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label(
                                        data_get($branchBoundsDebug, 'id'),
                                    ) .
                                    ' | x=' .
                                    data_get($branchBoundsDebug, 'x', '0rem') .
                                    ' y=' .
                                    data_get($branchBoundsDebug, 'y', '0rem') .
                                    ' width=' .
                                    data_get($branchBoundsDebug, 'width', '0rem') .
                                    ' height=' .
                                    data_get($branchBoundsDebug, 'height', '0rem');
                            @endphp

                            <span
                                class="tw-graph-protocol-dev-only absolute z-[1] cursor-copy rounded-sm border border-dashed border-sky-400/70 bg-sky-300/5"
                                data-tw-graph-path="{{ $branchBoundsDebugTooltip }}"
                                title="{{ $branchBoundsDebugTooltip }}"
                                style="
                                    left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($branchBoundsDebug, 'x', '0rem') }});
                                    bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($branchBoundsDebug, 'y', '0rem') }});
                                    width: {{ data_get($branchBoundsDebug, 'width', '0rem') }};
                                    height: {{ data_get($branchBoundsDebug, 'height', '0rem') }};
                                "
                                x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                            >
                                <span
                                    class="absolute left-0 top-0 -translate-x-1/2 -translate-y-1/2 rounded-sm border border-sky-500/70 bg-white px-1 font-mono text-[0.6rem] leading-4 text-sky-700 shadow-sm dark:bg-zinc-950 dark:text-sky-300"
                                >
                                    {{ $branchBoundsDebugNumber }}
                                </span>
                            </span>
                            @endforeach
                        @endif
                    @endforeach
                </x-translation-workbench::ui.tw-graph>

                @php
                    $twGraphDebugBoundLevel = static function (string $type): string {
                        return str_ends_with($type, '-start') ||
                            str_ends_with($type, '-labels') ||
                            str_ends_with($type, '-tail') ||
                            in_array(
                                $type,
                                [
                                    'start-label-inclusive',
                                    'middle-label-inclusive',
                                    'end-label-inclusive',
                                    'branch-start',
                                    'branch-body',
                                    'branch-end',
                                    'rekey-target-body',
                                    'rekey-target-end',
                                ],
                                true,
                            )
                            ? 'sub'
                            : 'main';
                    };
                    $twGraphDebugCollisionType = static function (string $firstLevel, string $secondLevel): string {
                        return $firstLevel === 'sub' && $secondLevel === 'sub'
                            ? 'sub'
                            : ($firstLevel === 'main' && $secondLevel === 'main'
                                ? 'main'
                                : 'main-sub');
                    };
                    $twGraphElementId = static fn(
                        mixed $id,
                    ): string => \Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier::normalize($id);
                    $twGraphDebugBoundRow = static function (
                        array $debugBox,
                        string $scope,
                        string $sideDefault = '',
                    ) use ($twGraphDebugBoundLevel, $twGraphElementId): array {
                        $renderId = (string) data_get($debugBox, 'renderId', data_get($debugBox, 'id', 'n/a'));

                        return [
                            'scope' => $scope,
                            'side' => data_get($debugBox, 'side', $sideDefault),
                            'type' => data_get($debugBox, 'type', 'n/a'),
                            'level' => $twGraphDebugBoundLevel((string) data_get($debugBox, 'type', 'n/a')),
                            'id' => $twGraphElementId(data_get($debugBox, 'id', $renderId)),
                            'render_id' => $renderId,
                            'x' => data_get($debugBox, 'x', '0rem'),
                            'y' => data_get($debugBox, 'y', '0rem'),
                            'width' => data_get($debugBox, 'width', '0rem'),
                            'height' => data_get($debugBox, 'height', '0rem'),
                        ];
                    };
                    $twGraphDebugBaseBoundRows = collect(
                        data_get($twGraphDataDrivenPreviewTrunk->get('layout', []), 'trunkBoundsDebug', []),
                    )
                        ->map(fn($debugBox) => $twGraphDebugBoundRow((array) $debugBox, 'trunk', 'center'))
                        ->merge(
                            $twGraphDataDrivenPreviewMerges
                                ->flatMap(fn($merge) => (array) data_get($merge, 'layout.mergeBoundsDebug', []))
                                ->map(fn($debugBox) => $twGraphDebugBoundRow((array) $debugBox, 'merge')),
                        )
                        ->merge(
                            $twGraphDataDrivenPreviewRekeys
                                ->flatMap(fn($rekey) => (array) data_get($rekey, 'layout.rekeyBoundsDebug', []))
                                ->map(fn($debugBox) => $twGraphDebugBoundRow((array) $debugBox, 'rekey')),
                        )
                        ->merge(
                            $twGraphDataDrivenPreviewBranches
                                ->flatMap(fn($branch) => (array) data_get($branch, 'layout.branchBoundsDebug', []))
                                ->map(fn($debugBox) => $twGraphDebugBoundRow((array) $debugBox, 'branch')),
                        )
                        ->values();
                    $twGraphDebugLevelById = $twGraphDebugBaseBoundRows->mapWithKeys(
                        fn($debugBoundRow) => [
                            (string) data_get($debugBoundRow, 'id') => (string) data_get(
                                $debugBoundRow,
                                'level',
                                'main',
                            ),
                        ],
                    );
                    $twGraphDebugCollisionTypeById = collect(
                        data_get($twGraphDataDrivenPreviewTrunk->get('layout', []), 'trunkCollisionDebug', []),
                    )->reduce(function (array $collisionTypes, array $collision) use (
                        $twGraphDebugLevelById,
                        $twGraphDebugCollisionType,
                        $twGraphElementId,
                    ): array {
                        $trunkId = $twGraphElementId(data_get($collision, 'trunk', ''));
                        $againstId = $twGraphElementId(data_get($collision, 'against', ''));

                        if ($trunkId === '' || $againstId === '') {
                            return $collisionTypes;
                        }

                        $collisionType = $twGraphDebugCollisionType(
                            (string) $twGraphDebugLevelById->get($trunkId, 'main'),
                            (string) $twGraphDebugLevelById->get($againstId, 'main'),
                        );

                        $collisionTypes[$trunkId] = $collisionType;
                        $collisionTypes[$againstId] = $collisionType;

                        return $collisionTypes;
                    }, []);

                    foreach ($twGraphDataDrivenPreviewMerges as $mergePreview) {
                        foreach ((array) data_get($mergePreview, 'layout.mergeCollisionDebug', []) as $collision) {
                            $firstId = $twGraphElementId(data_get($collision, 'first', ''));
                            $secondId = $twGraphElementId(data_get($collision, 'second', ''));

                            if ($firstId === '' || $secondId === '') {
                                continue;
                            }

                            $collisionType = $twGraphDebugCollisionType(
                                (string) $twGraphDebugLevelById->get($firstId, 'main'),
                                (string) $twGraphDebugLevelById->get($secondId, 'main'),
                            );

                            $twGraphDebugCollisionTypeById[$firstId] = $collisionType;
                            $twGraphDebugCollisionTypeById[$secondId] = $collisionType;
                        }
                    }

                    $twGraphBranchCollisionBoundId = static function (string $branchId, string $type) use (
                        $twGraphElementId,
                    ): string {
                        if ($branchId === '') {
                            return '';
                        }

                        $boundId =
                            $type === 'bridge' ? $branchId . '.main.path.branch.bridge1' : $branchId . '.label-bounds';

                        return $twGraphElementId($boundId);
                    };
                    $twGraphBranchEndBoundId = static function (string $branchEndSegmentId) use (
                        $twGraphElementId,
                    ): string {
                        if ($branchEndSegmentId === '') {
                            return '';
                        }

                        $branchEndSegmentId = $twGraphElementId($branchEndSegmentId);
                        $branchEndBoundId = str_ends_with($branchEndSegmentId, '.branch.end.segment')
                            ? substr($branchEndSegmentId, 0, -strlen('.segment')) . '.bounds'
                            : $branchEndSegmentId;
                        $branchEndBoundId = str_ends_with($branchEndBoundId, '.end.path.branch-end.segment')
                            ? substr($branchEndSegmentId, 0, -strlen('.segment')) . '.bounds'
                            : $branchEndBoundId;

                        return $twGraphElementId($branchEndBoundId);
                    };

                    foreach ($twGraphDataDrivenPreviewBranches as $branchPreview) {
                        foreach ((array) data_get($branchPreview, 'layout.branchCollisionDebug', []) as $collision) {
                            $type = (string) data_get($collision, 'type', 'label');
                            $branchId = $twGraphBranchCollisionBoundId(
                                (string) data_get($collision, 'branch', ''),
                                $type,
                            );
                            $againstId = $twGraphBranchCollisionBoundId(
                                (string) data_get($collision, 'against', ''),
                                $type,
                            );

                            if ($branchId === '' || $againstId === '') {
                                continue;
                            }

                            $collisionType = $twGraphDebugCollisionType(
                                (string) $twGraphDebugLevelById->get($branchId, 'main'),
                                (string) $twGraphDebugLevelById->get($againstId, 'main'),
                            );

                            $twGraphDebugCollisionTypeById[$branchId] = $collisionType;
                            $twGraphDebugCollisionTypeById[$againstId] = $collisionType;
                        }

                        foreach ((array) data_get($branchPreview, 'layout.warnings', []) as $warning) {
                            if ((string) data_get($warning, 'type') !== 'branch-end-over-bridge') {
                                continue;
                            }

                            $branchEndId = $twGraphBranchEndBoundId((string) data_get($warning, 'label', ''));
                            $bridgeId = $twGraphElementId(data_get($warning, 'bridge', ''));

                            if ($branchEndId === '' || $bridgeId === '') {
                                continue;
                            }

                            $collisionType = $twGraphDebugCollisionType(
                                (string) $twGraphDebugLevelById->get($branchEndId, 'sub'),
                                (string) $twGraphDebugLevelById->get($bridgeId, 'main'),
                            );

                            $twGraphDebugCollisionTypeById[$branchEndId] = $collisionType;
                            $twGraphDebugCollisionTypeById[$bridgeId] = $collisionType;
                        }
                    }

                    $twGraphDebugCollisionDeltaById = collect(
                        data_get($twGraphDataDrivenPreviewTrunk->get('layout', []), 'trunkCollisionDebug', []),
                    )->reduce(function (array $collisionDeltas, array $collision) use ($twGraphElementId): array {
                        $trunkId = $twGraphElementId(data_get($collision, 'trunk', ''));
                        $againstId = $twGraphElementId(data_get($collision, 'against', ''));
                        $value = trim(
                            'y +' .
                                (string) data_get($collision, 'overlapHeight', '0rem') .
                                ' / x ' .
                                (string) data_get($collision, 'overlapWidth', '0rem'),
                        );

                        foreach ([$trunkId, $againstId] as $id) {
                            if ($id === '') {
                                continue;
                            }

                            $collisionDeltas[$id] = collect([...((array) ($collisionDeltas[$id] ?? [])), $value])
                                ->unique()
                                ->values()
                                ->all();
                        }

                        return $collisionDeltas;
                    }, []);

                    foreach ($twGraphDataDrivenPreviewMerges as $mergePreview) {
                        foreach ((array) data_get($mergePreview, 'layout.mergeCollisionDebug', []) as $collision) {
                            $firstId = $twGraphElementId(data_get($collision, 'first', ''));
                            $secondId = $twGraphElementId(data_get($collision, 'second', ''));
                            $value = trim(
                                (string) data_get($collision, 'type', 'merge overlap') .
                                    ' | preferred ' .
                                    (string) data_get($collision, 'preferredCompensationDirection', 'vertical') .
                                    ' +' .
                                    (string) data_get($collision, 'preferredIncrement', '0rem') .
                                    ' | bridge +' .
                                    (string) data_get($collision, 'requiredBridgeIncrement', '0rem') .
                                    ' | stem +' .
                                    (string) data_get($collision, 'requiredStemIncrement', '0rem') .
                                    ' | overlap x=' .
                                    (string) data_get($collision, 'overlapWidth', '0rem') .
                                    ' y=' .
                                    (string) data_get($collision, 'overlapHeight', '0rem') .
                                    ' | gap=' .
                                    (string) data_get($collision, 'gap', '0rem'),
                            );

                            foreach ([$firstId, $secondId] as $id) {
                                if ($id === '') {
                                    continue;
                                }

                                $twGraphDebugCollisionDeltaById[$id] = collect([
                                    ...((array) ($twGraphDebugCollisionDeltaById[$id] ?? [])),
                                    $value,
                                ])
                                    ->unique()
                                    ->values()
                                    ->all();
                            }
                        }
                    }

                    foreach ($twGraphDataDrivenPreviewBranches as $branchPreview) {
                        foreach ((array) data_get($branchPreview, 'layout.branchCollisionDebug', []) as $collision) {
                            $type = (string) data_get($collision, 'type', 'label');
                            $branchId = $twGraphBranchCollisionBoundId(
                                (string) data_get($collision, 'branch', ''),
                                $type,
                            );
                            $againstId = $twGraphBranchCollisionBoundId(
                                (string) data_get($collision, 'against', ''),
                                $type,
                            );
                            $side = (string) data_get($collision, 'side', '');
                            $direction = $side === 'left' ? 'left' : ($side === 'right' ? 'right' : 'x');
                            $value = $direction . ' +' . (string) data_get($collision, 'requiredIncrement', '0rem');

                            foreach ([$branchId, $againstId] as $id) {
                                if ($id === '') {
                                    continue;
                                }

                                $twGraphDebugCollisionDeltaById[$id] = collect([
                                    ...((array) ($twGraphDebugCollisionDeltaById[$id] ?? [])),
                                    $value,
                                ])
                                    ->unique()
                                    ->values()
                                    ->all();
                            }
                        }

                        foreach ((array) data_get($branchPreview, 'layout.warnings', []) as $warning) {
                            if ((string) data_get($warning, 'type') !== 'branch-end-over-bridge') {
                                continue;
                            }

                            $branchEndId = $twGraphBranchEndBoundId((string) data_get($warning, 'label', ''));
                            $bridgeId = $twGraphElementId(data_get($warning, 'bridge', ''));
                            $value = trim(
                                (string) data_get($warning, 'message', 'branch-end/bridge overlap') .
                                    ' | bridge=' .
                                    (string) data_get($warning, 'bridge', ''),
                            );

                            foreach ([$branchEndId, $bridgeId] as $id) {
                                if ($id === '') {
                                    continue;
                                }

                                $twGraphDebugCollisionDeltaById[$id] = collect([
                                    ...((array) ($twGraphDebugCollisionDeltaById[$id] ?? [])),
                                    $value,
                                ])
                                    ->unique()
                                    ->values()
                                    ->all();
                            }
                        }
                    }

                    $twGraphDebugAppliedCorrectionById = collect(
                        data_get($twGraphDataDrivenPreviewGraph, 'layout_corrections.applied', []),
                    )->reduce(function (array $appliedCorrections, array $correction) use ($twGraphElementId): array {
                        $target = $twGraphElementId(data_get($correction, 'target', ''));
                        $prop = (string) data_get($correction, 'prop', '');
                        $value =
                            (string) (data_get($correction, 'effectiveValue') ?? data_get($correction, 'value', ''));

                        if ($target === '' || $prop === '' || $value === '') {
                            return $appliedCorrections;
                        }

                        $label = $prop . '=' . $value;

                        foreach ([$target, $target . '.bounds'] as $id) {
                            $appliedCorrections[$id] = collect([...((array) ($appliedCorrections[$id] ?? [])), $label])
                                ->unique()
                                ->values()
                                ->all();
                        }

                        return $appliedCorrections;
                    }, []);

                    $twGraphDebugAppliedCompensationById = collect([
                        $twGraphDataDrivenPreviewTrunk->all(),
                        ...$twGraphDataDrivenPreviewBranches->all(),
                        ...$twGraphDataDrivenPreviewMerges->all(),
                        ...$twGraphDataDrivenPreviewRekeys->all(),
                    ])
                        ->flatMap(
                            static fn(array $preview): array => (array) data_get(
                                $preview,
                                'layout.appliedCompensations',
                                [],
                            ),
                        )
                        ->reduce(function (array $appliedCompensations, array $compensation) use (
                            $twGraphElementId,
                        ): array {
                            $target = $twGraphElementId(data_get($compensation, 'target', ''));
                            $prop = (string) data_get($compensation, 'prop', '');
                            $value = (string) data_get($compensation, 'effectiveValue', '');

                            if ($target === '' || $prop === '' || $value === '') {
                                return $appliedCompensations;
                            }

                            $parts = [$prop . '=' . $value];

                            if (filled(data_get($compensation, 'delta'))) {
                                $parts[] = 'delta=' . data_get($compensation, 'delta');
                            }

                            if (filled(data_get($compensation, 'overlapWidth'))) {
                                $parts[] = 'overlap=' . data_get($compensation, 'overlapWidth');
                            }

                            if (filled(data_get($compensation, 'gap'))) {
                                $parts[] = 'gap=' . data_get($compensation, 'gap');
                            }

                            foreach ((array) data_get($compensation, 'sources', []) as $source) {
                                if (filled(data_get($source, 'requiredIncrement'))) {
                                    $parts[] =
                                        data_get($source, 'source') .
                                        ': required=' .
                                        data_get($source, 'requiredIncrement');
                                }

                                if (filled(data_get($source, 'gap'))) {
                                    $parts[] = 'gap=' . data_get($source, 'gap');
                                }
                            }

                            $label = implode(' | ', array_filter($parts));

                            $targetIds = [$target, $target . '.bounds'];
                            if (preg_match('/^strang\.trunk\.1\.stem(\d+)$/', $target, $matches) === 1) {
                                $stemNumber = (int) $matches[1];
                                $targetIds[] = 'strang.trunk.1.bounds';

                                if ($stemNumber === 1) {
                                    $targetIds[] = 'strang.trunk.1.start.bounds.label';
                                    $targetIds[] = 'strang.trunk.1.start.left.bounds.label';
                                    $targetIds[] = 'strang.trunk.1.start.right.bounds.label';
                                    $targetIds[] = 'strang.trunk.1.start.nodeEndLabel1.bounds';
                                    $targetIds[] = 'strang.trunk.1.start.nodeEndLabel2.bounds';
                                }
                            }
                            if (
                                preg_match('/^strang\.(left|right)\.(\d+)\.merge\.stem(\d+)$/', $target, $matches) === 1
                            ) {
                                $targetIds[] = 'strang.' . $matches[1] . '.' . $matches[2] . '.merge.start.stem.bounds';
                                $targetIds[] =
                                    'strang.' . $matches[1] . '.' . $matches[2] . '.merge.start.stem.labels.bounds';
                                $targetIds[] =
                                    'strang.' . $matches[1] . '.' . $matches[2] . '.merge.start.stem.tail.bounds';
                            }
                            if (
                                preg_match(
                                    '/^strang\.(left|right)\.(\d+)\.merge\.extension\.(\d+)\.stem(\d+)$/',
                                    $target,
                                    $matches,
                                ) === 1
                            ) {
                                $targetIds[] =
                                    'strang.' .
                                    $matches[1] .
                                    '.' .
                                    $matches[2] .
                                    '.merge.extension.' .
                                    $matches[3] .
                                    '.start.stem.bounds';
                                $targetIds[] =
                                    'strang.' .
                                    $matches[1] .
                                    '.' .
                                    $matches[2] .
                                    '.merge.extension.' .
                                    $matches[3] .
                                    '.start.stem.labels.bounds';
                                $targetIds[] =
                                    'strang.' .
                                    $matches[1] .
                                    '.' .
                                    $matches[2] .
                                    '.merge.extension.' .
                                    $matches[3] .
                                    '.start.stem.tail.bounds';
                            }

                            foreach (array_unique($targetIds) as $id) {
                                $appliedCompensations[$id] = collect([
                                    ...((array) ($appliedCompensations[$id] ?? [])),
                                    $label,
                                ])
                                    ->unique()
                                    ->values()
                                    ->all();
                            }

                            return $appliedCompensations;
                        }, []);

                    $twGraphDebugBoundRows = $twGraphDebugBaseBoundRows
                        ->map(function ($debugBoundRow) use (
                            $twGraphDebugAppliedCompensationById,
                            $twGraphDebugAppliedCorrectionById,
                            $twGraphDebugBoundNumberById,
                            $twGraphDebugCollisionTypeById,
                            $twGraphDebugCollisionDeltaById,
                        ) {
                            $debugBoundId = (string) data_get($debugBoundRow, 'id');

                            return [
                                ...$debugBoundRow,
                                'debug_no' => $twGraphDebugBoundNumberById[$debugBoundId] ?? '',
                                'display_id' => \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label(
                                    $debugBoundId,
                                ),
                                'collision' => array_key_exists($debugBoundId, $twGraphDebugCollisionTypeById),
                                'collision_type' => $twGraphDebugCollisionTypeById[$debugBoundId] ?? 'none',
                                'collision_delta' => collect(
                                    (array) ($twGraphDebugCollisionDeltaById[$debugBoundId] ?? []),
                                )->join(' | '),
                                'applied_compensation' => collect(
                                    (array) ($twGraphDebugAppliedCompensationById[$debugBoundId] ?? []),
                                )->join(' | '),
                                'applied_correction' => collect(
                                    (array) ($twGraphDebugAppliedCorrectionById[$debugBoundId] ?? []),
                                )->join(' | '),
                            ];
                        })
                        ->values();
                    $twGraphDebugCollisionBoundRows = $twGraphDebugBoundRows->where('collision', true)->values();
                @endphp

                @if ($twGraphDataDrivenDevEnabled)
                <div
                    class="mt-4 overflow-hidden rounded-lg border border-zinc-200 bg-white/75 dark:border-zinc-700 dark:bg-zinc-900/45"
                    x-show="twGraphDataDrivenDev"
                >
                    <div
                        class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 px-3 py-2 dark:border-zinc-700">
                        <flux:heading size="sm">{{ __('Debug bounds') }}</flux:heading>
                        <div class="flex flex-wrap items-center justify-end gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 text-xs opacity-70 hover:cursor-pointer"
                                x-on:click.stop="twGraphDebugBoundsCollisionOnly = !twGraphDebugBoundsCollisionOnly"
                            >
                                <span
                                    class="relative inline-flex h-5 w-9 items-center rounded-full border transition"
                                    x-bind:class="twGraphDebugBoundsCollisionOnly ? 'border-red-500 bg-red-500' : 'border-zinc-300 bg-zinc-200 dark:border-zinc-700 dark:bg-zinc-800'"
                                >
                                    <span
                                        class="inline-block h-4 w-4 rounded-full bg-white shadow transition"
                                        x-bind:class="twGraphDebugBoundsCollisionOnly ? 'translate-x-4' : 'translate-x-0.5'"
                                    ></span>
                                </span>
                                <span>{{ __('Collisions only') }}</span>
                            </button>

                            <flux:badge
                                size="sm"
                                color="{{ $twGraphDebugCollisionBoundRows->isNotEmpty() ? 'red' : 'zinc' }}"
                            >
                                {{ __('collisions') }}:
                                {{ $twGraphDebugCollisionBoundRows->count() }}
                            </flux:badge>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-xs">
                            <thead class="bg-zinc-50 text-zinc-600 dark:bg-zinc-950/60 dark:text-zinc-300">
                                <tr>
                                    <th class="px-3 py-2 font-medium">{{ __('#') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Scope') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Side') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Type') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Element ID') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Coordinates') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Dimension') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Collision Type') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Collision') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Collision Delta') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Applied Compensation') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Applied Correction') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-700/70">
                                @forelse ($twGraphDebugBoundRows as $debugBoundRow)
                                    <tr x-show="!twGraphDebugBoundsCollisionOnly || @js((bool) data_get($debugBoundRow, 'collision'))">
                                        <td class="px-3 py-2 align-top font-mono text-zinc-700 dark:text-zinc-200">
                                            {{ data_get($debugBoundRow, 'debug_no') }}
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <flux:badge
                                                size="sm"
                                                color="{{ data_get($debugBoundRow, 'scope') === 'trunk' ? 'green' : (data_get($debugBoundRow, 'scope') === 'merge' ? 'amber' : 'sky') }}"
                                            >
                                                {{ data_get($debugBoundRow, 'scope') }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-3 py-2 align-top text-zinc-700 dark:text-zinc-200">
                                            {{ filled(data_get($debugBoundRow, 'side')) ? data_get($debugBoundRow, 'side') : 'center' }}
                                        </td>
                                        <td class="px-3 py-2 align-top text-zinc-700 dark:text-zinc-200">
                                            {{ data_get($debugBoundRow, 'type') }}
                                        </td>
                                        <td
                                            class="wrap-anywhere cursor-copy px-3 py-2 align-top font-mono text-zinc-900 dark:text-zinc-100"
                                            title="{{ data_get($debugBoundRow, 'id') }}"
                                            x-on:click.stop="navigator.clipboard?.writeText(@js(data_get($debugBoundRow, 'id')))"
                                        >
                                            {{ data_get($debugBoundRow, 'display_id') }}
                                        </td>
                                        <td class="px-3 py-2 align-top font-mono text-zinc-700 dark:text-zinc-200">
                                            x={{ data_get($debugBoundRow, 'x') }}
                                            <span class="text-zinc-400">/</span>
                                            y={{ data_get($debugBoundRow, 'y') }}
                                        </td>
                                        <td class="px-3 py-2 align-top font-mono text-zinc-700 dark:text-zinc-200">
                                            w={{ data_get($debugBoundRow, 'width') }}
                                            <span class="text-zinc-400">/</span>
                                            h={{ data_get($debugBoundRow, 'height') }}
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <flux:badge
                                                size="sm"
                                                color="{{ data_get($debugBoundRow, 'collision_type') === 'sub' ? 'red' : (data_get($debugBoundRow, 'collision_type') === 'main-sub' ? 'amber' : (data_get($debugBoundRow, 'collision_type') === 'main' ? 'zinc' : 'zinc')) }}"
                                            >
                                                {{ data_get($debugBoundRow, 'collision_type') }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <flux:badge
                                                size="sm"
                                                color="{{ data_get($debugBoundRow, 'collision') ? 'red' : 'zinc' }}"
                                            >
                                                {{ data_get($debugBoundRow, 'collision') ? __('yes') : __('no') }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-3 py-2 align-top font-mono text-zinc-700 dark:text-zinc-200">
                                            {{ data_get($debugBoundRow, 'collision_delta') }}
                                        </td>
                                        <td class="px-3 py-2 align-top font-mono text-zinc-700 dark:text-zinc-200">
                                            {{ data_get($debugBoundRow, 'applied_compensation') }}
                                        </td>
                                        <td class="px-3 py-2 align-top font-mono text-zinc-700 dark:text-zinc-200">
                                            {{ data_get($debugBoundRow, 'applied_correction') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            class="px-3 py-4 text-center text-zinc-500"
                                            colspan="12"
                                        >
                                            {{ __('No debug bound boxes available.') }}
                                        </td>
                                    </tr>
                                @endforelse
                                @if ($twGraphDebugBoundRows->isNotEmpty() && $twGraphDebugCollisionBoundRows->isEmpty())
                                    <tr x-show="twGraphDebugBoundsCollisionOnly">
                                        <td
                                            class="px-3 py-4 text-center text-zinc-500"
                                            colspan="12"
                                        >
                                            {{ __('No collision debug bound boxes available.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if (filled(data_get($twGraphDataDrivenPreviewGraph->get('header'), 'text')))
                    <flux:callout.heading class="mt-2 flex items-center justify-center gap-2">
                        @foreach ((array) data_get($twGraphDataDrivenPreviewGraph->get('header'), 'text', []) as $graphHeaderLine)
                            <span>{{ $graphHeaderLine }}</span>
                        @endforeach
                    </flux:callout.heading>
                @endif

            </div>
        </flux:callout>
        </div>
    @endif

    @if ($twGraphDataDrivenDevEnabled)
        <flux:callout
            class="mt-3"
            color="amber"
            icon="git-branch-plus"
        >
        <flux:callout.heading>
            <span class="flex w-full flex-wrap items-center justify-between gap-3">
                <span>{{ __('Merge origin outcomes') }}</span>
                <span class="inline-flex flex-wrap items-center gap-1">
                    <flux:badge
                        size="sm"
                        color="zinc"
                    >{{ __('Total') }}:
                        {{ number_format((int) $twGraphDataDrivenMergeOutcomeSummary->get('total', 0)) }}</flux:badge>
                    <flux:badge
                        size="sm"
                        color="green"
                    >{{ __('Source active') }}:
                        {{ number_format((int) $twGraphDataDrivenMergeOutcomeSummary->get('source_active', 0)) }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="red"
                    >{{ __('Branch candidates') }}:
                        {{ number_format((int) $twGraphDataDrivenMergeOutcomeSummary->get('branch_candidates', 0)) }}
                    </flux:badge>
                    @foreach (collect($twGraphDataDrivenMergeOutcomeSummary->get('groups', [])) as $group => $count)
                        <flux:badge
                            size="sm"
                            color="{{ str_contains((string) $group, 'ended') ? 'red' : (str_contains((string) $group, 'pending') || str_contains((string) $group, 'review') ? 'yellow' : 'green') }}"
                        >
                            {{ $group }}: {{ number_format((int) $count) }}
                        </flux:badge>
                    @endforeach
                </span>
            </span>
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('Diagnostic data only: current origin state before deciding which origins should become visual branch strangs.') }}
        </flux:callout.text>

        <div class="mt-3 max-h-80 overflow-auto rounded-lg bg-white/70 dark:bg-zinc-900/40">
            <table class="min-w-full text-left text-xs">
                <thead class="sticky top-0 z-10 bg-white text-zinc-600 dark:bg-zinc-900 dark:text-zinc-300">
                    <tr>
                        <th class="px-3 py-2 font-medium">{{ __('Finding') }}</th>
                        <th class="px-3 py-2 font-medium">{{ __('Origin key') }}</th>
                        <th class="px-3 py-2 font-medium">{{ __('Outcome') }}</th>
                        <th class="px-3 py-2 font-medium">{{ __('Shared') }}</th>
                        <th class="px-3 py-2 font-medium">{{ __('Seen') }}</th>
                        <th class="px-3 py-2 font-medium">{{ __('Source') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-700/70">
                    @forelse ($twGraphDataDrivenMergeOutcomes as $outcome)
                        @php
                            $outcome = collect($outcome);
                            $isBranchCandidate = (bool) $outcome->get('branch_hint');
                        @endphp
                        <tr>
                            <td class="px-3 py-2 align-top">
                                <div class="flex flex-col gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="{{ $isBranchCandidate ? 'red' : 'green' }}"
                                    >
                                        #{{ $outcome->get('finding_id') ?: '?' }}
                                    </flux:badge>
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ $outcome->get('side') }}</span>
                                </div>
                            </td>
                            <td class="wrap-anywhere px-3 py-2 align-top font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $outcome->get('origin_key') ?: __('n/a') }}
                            </td>
                            <td class="px-3 py-2 align-top">
                                <div class="flex flex-col gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="{{ $isBranchCandidate ? 'red' : 'green' }}"
                                    >
                                        {{ $outcome->get('outcome') }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="{{ $isBranchCandidate ? 'red' : 'green' }}"
                                    >
                                        {{ $outcome->get('outcome_group') }}
                                    </flux:badge>
                                    <span class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('finding') }}: {{ $outcome->get('finding_status') }}
                                        ·
                                        {{ __('key') }}: {{ $outcome->get('origin_key_status') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <div class="flex flex-col gap-1">
                                    <span class="text-zinc-900 dark:text-zinc-100">
                                        {{ $outcome->get('shared_candidate_status') }}
                                    </span>
                                    @if ($outcome->get('shared_candidate_id'))
                                        <span class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('candidate') }} #{{ $outcome->get('shared_candidate_id') }}
                                            @if ($outcome->get('matched_key_id'))
                                                · {{ __('matched key') }} #{{ $outcome->get('matched_key_id') }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <div class="flex flex-col gap-1 text-zinc-700 dark:text-zinc-200">
                                    <span>{{ __('first') }}:
                                        {{ $outcome->get('first_seen_at') ?: __('n/a') }}</span>
                                    <span>{{ __('last') }}:
                                        {{ $outcome->get('last_seen_at') ?: __('n/a') }}</span>
                                </div>
                            </td>
                            <td class="wrap-anywhere px-3 py-2 align-top font-mono text-zinc-600 dark:text-zinc-300">
                                {{ $outcome->get('source_path') ?: __('n/a') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                class="px-3 py-4 text-center text-zinc-500"
                                colspan="6"
                            >
                                {{ __('No merge origin outcomes collected for this chain.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </flux:callout>

        <flux:callout
            class="mt-3"
            color="zinc"
            icon="scan-search"
        >
        <flux:callout.heading>
            <span class="inline-flex flex-wrap items-center gap-2">
                <span>{{ __('Finding information sample') }}</span>
                <flux:badge
                    size="sm"
                    color="zinc"
                >
                    #{{ $twGraphDataDrivenFindingInspect['finding_id'] }}
                </flux:badge>
                @if ($twGraphDataDrivenInspectRendered->isNotEmpty())
                    <flux:badge
                        size="sm"
                        color="amber"
                    >
                        {{ $twGraphDataDrivenInspectRendered->get('side') }}
                        ·
                        {{ __('rendered') }}
                    </flux:badge>
                @else
                    <flux:badge
                        size="sm"
                        color="zinc"
                    >
                        {{ __('not rendered in current preview limit') }}
                    </flux:badge>
                @endif
            </span>
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('Temporary inspection block for deciding which values should become direct graph labels and which values should move into tooltips.') }}
        </flux:callout.text>

        <div class="mt-3 grid gap-3 xl:grid-cols-3">
            <flux:callout color="sky">
                <flux:callout.heading>{{ __('Finding') }}</flux:callout.heading>
                <flux:callout.text>
                    <dl class="space-y-1 text-xs">
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Suggested key') }}</dt>
                            <dd class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectFinding->get('suggested_key') ?: __('n/a') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Found / existing key') }}
                            </dt>
                            <dd class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectFinding->get('found_translation_key') ?: __('n/a') }}
                                @if ($twGraphDataDrivenInspectFinding->get('existing_key'))
                                    -> {{ $twGraphDataDrivenInspectFinding->get('existing_key') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Literal') }}</dt>
                            <dd class="wrap-anywhere text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectFinding->get('literal_text') ?: $twGraphDataDrivenInspectFinding->get('literal_text_suggested') ?: __('n/a') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Expression') }}</dt>
                            <dd class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectFinding->get('raw_expression') ?: __('n/a') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Source') }}</dt>
                            <dd class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectFinding->get('source_path') ?: __('n/a') }}
                                @if ($twGraphDataDrivenInspectFinding->get('source_line'))
                                    :{{ $twGraphDataDrivenInspectFinding->get('source_line') }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="sky"
                            >{{ __('Status') }}: {{ $twGraphDataDrivenInspectFinding->get('status') ?: __('n/a') }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >{{ __('Kind') }}: {{ $twGraphDataDrivenInspectFinding->get('kind') ?: __('n/a') }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >{{ __('Seen') }}: {{ $twGraphDataDrivenInspectFinding->get('scan_count') ?: 0 }}
                            </flux:badge>
                        </div>
                    </dl>
                </flux:callout.text>
            </flux:callout>

            <flux:callout color="amber">
                <flux:callout.heading>{{ __('Merge / shared context') }}</flux:callout.heading>
                <flux:callout.text>
                    <dl class="space-y-1 text-xs">
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Origin row') }}</dt>
                            <dd class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectOrigin->get('first_origin_key') ?: __('n/a') }}
                                @if ($twGraphDataDrivenInspectOrigin->get('translation_key'))
                                    -> {{ $twGraphDataDrivenInspectOrigin->get('translation_key') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Shared candidate') }}
                            </dt>
                            <dd class="wrap-anywhere font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectShared->get('current_translation_key') ?: __('n/a') }}
                                @if ($twGraphDataDrivenInspectShared->get('suggested_shared_translation_key'))
                                    -> {{ $twGraphDataDrivenInspectShared->get('suggested_shared_translation_key') }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="amber"
                            >{{ __('Candidate') }}: #{{ $twGraphDataDrivenInspectShared->get('id') ?: '?' }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >{{ __('Confidence') }}:
                                {{ $twGraphDataDrivenInspectShared->get('confidence') ?: __('n/a') }}</flux:badge>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >{{ __('Status') }}: {{ $twGraphDataDrivenInspectShared->get('status') ?: __('n/a') }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >{{ __('Matched findings') }}:
                                {{ $twGraphDataDrivenInspectShared->get('matched_finding_count') ?: 0 }}</flux:badge>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Rendered as') }}</dt>
                            <dd class="wrap-anywhere text-zinc-900 dark:text-zinc-100">
                                {{ $twGraphDataDrivenInspectRendered->get('strang') ?: __('Not currently rendered') }}
                                @if ($twGraphDataDrivenInspectRendered->get('component_counter'))
                                    #{{ $twGraphDataDrivenInspectRendered->get('component_counter') }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </flux:callout.text>
            </flux:callout>

            <flux:callout color="fuchsia">
                <flux:callout.heading>{{ __('Related records') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="space-y-2 text-xs">
                        <div>
                            <div class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Keys') }}</div>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($twGraphDataDrivenInspectKeys as $key)
                                    <flux:badge
                                        size="sm"
                                        color="fuchsia"
                                    >{{ $key }}</flux:badge>
                                @empty
                                    <flux:text class="text-xs text-zinc-500">{{ __('n/a') }}</flux:text>
                                @endforelse
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="blue"
                            >{{ __('Reviews') }}: {{ $twGraphDataDrivenInspectReviews->count() }}</flux:badge>
                            <flux:badge
                                size="sm"
                                color="green"
                            >{{ __('Timeline') }}: {{ $twGraphDataDrivenInspectTimeline->count() }}</flux:badge>
                            <flux:badge
                                size="sm"
                                color="purple"
                            >{{ __('Lang values') }}: {{ $twGraphDataDrivenInspectLangValues->count() }}
                            </flux:badge>
                        </div>
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>

        <div class="mt-3 grid gap-3 xl:grid-cols-3">
            <flux:callout color="green">
                <flux:callout.heading>{{ __('Timeline rows') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="max-h-48 space-y-2 overflow-auto text-xs">
                        @forelse ($twGraphDataDrivenInspectTimeline as $timelineRow)
                            <div class="rounded-md bg-white/70 p-2 dark:bg-zinc-900/40">
                                <div class="flex flex-wrap gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="green"
                                    >#{{ $timelineRow['id'] ?? '?' }}</flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >{{ $timelineRow['event_type'] ?? __('n/a') }}</flux:badge>
                                </div>
                                <div class="wrap-anywhere mt-1 font-mono text-zinc-700 dark:text-zinc-200">
                                    key #{{ $timelineRow['key_id'] ?? '?' }}
                                    ·
                                    review #{{ $timelineRow['review_id'] ?? '?' }}
                                    ·
                                    {{ $timelineRow['created_at'] ?? __('n/a') }}
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-xs text-zinc-500">
                                {{ __('No timeline rows found for this finding.') }}</flux:text>
                        @endforelse
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout color="purple">
                <flux:callout.heading>{{ __('Lang value rows') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="max-h-48 space-y-2 overflow-auto text-xs">
                        @forelse ($twGraphDataDrivenInspectLangValues as $langValueRow)
                            <div class="rounded-md bg-white/70 p-2 dark:bg-zinc-900/40">
                                <div class="flex flex-wrap gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="purple"
                                    >#{{ $langValueRow['id'] ?? '?' }}</flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >{{ $langValueRow['locale'] ?? __('n/a') }}</flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >{{ $langValueRow['status'] ?? __('n/a') }}</flux:badge>
                                </div>
                                <div class="wrap-anywhere mt-1 font-mono text-zinc-700 dark:text-zinc-200">
                                    {{ $langValueRow['translation_key'] ?? __('n/a') }}
                                </div>
                                <div class="wrap-anywhere mt-1 text-zinc-900 dark:text-zinc-100">
                                    {{ $langValueRow['value'] ?? __('n/a') }}
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-xs text-zinc-500">
                                {{ __('No lang value rows found for the related keys.') }}</flux:text>
                        @endforelse
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout color="blue">
                <flux:callout.heading>{{ __('Review rows') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="max-h-48 space-y-2 overflow-auto text-xs">
                        @forelse ($twGraphDataDrivenInspectReviews as $reviewRow)
                            <div class="rounded-md bg-white/70 p-2 dark:bg-zinc-900/40">
                                <div class="flex flex-wrap gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="blue"
                                    >#{{ $reviewRow['id'] ?? '?' }}</flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >{{ $reviewRow['review_type'] ?? __('n/a') }}</flux:badge>
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >{{ $reviewRow['decision'] ?? __('n/a') }}</flux:badge>
                                </div>
                                <div class="wrap-anywhere mt-1 font-mono text-zinc-700 dark:text-zinc-200">
                                    key #{{ $reviewRow['key_id'] ?? '?' }}
                                    ·
                                    finding #{{ $reviewRow['finding_id'] ?? '?' }}
                                    ·
                                    {{ $reviewRow['reviewed_at'] ?? ($reviewRow['created_at'] ?? __('n/a')) }}
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-xs text-zinc-500">
                                {{ __('No direct review rows found for this finding.') }}</flux:text>
                        @endforelse
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>
        </flux:callout>
    @endif

    <flux:accordion class="mt-3">
        <flux:accordion.item>
            <flux:accordion.heading>
                {{ __('Data-driven graph model JSON') }}
            </flux:accordion.heading>
            <flux:accordion.content>
                <pre class="max-h-96 overflow-auto rounded-md bg-zinc-950 p-3 text-xs leading-relaxed text-zinc-100"><code>{{ $twGraphDataDrivenJson }}</code></pre>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:callout>
