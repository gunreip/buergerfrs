{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/tw-graph-data-driven.blade.php --}}

@php
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
        $twGraphDataDriven,
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

    <div class="mt-3 grid gap-3 xl:grid-cols-3">
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
        <flux:callout
            class="mt-3"
            color="green"
            icon="git-branch"
            x-data="{ twGraphDataDrivenDev: true, twGraphDataDrivenCoordinates: true }"
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
                            {{ number_format((int) $twGraphDataDrivenPreviewLimits->get('available_events', 0)) }}
                            {{ __('events') }}
                        </flux:badge>
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
                    </span>

                    <span class="flex flex-wrap items-center justify-end gap-3">
                        <flux:field
                            class="items-center gap-2"
                            variant="inline"
                            x-on:click.stop
                        >
                            <flux:switch
                                class="switch-colored hover:cursor-pointer"
                                x-model="twGraphDataDrivenDev"
                            />
                            <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                                {{ __('DEV') }}
                            </flux:label>
                        </flux:field>

                        <flux:field
                            class="items-center gap-2"
                            variant="inline"
                            x-on:click.stop
                        >
                            <flux:switch
                                class="switch-colored hover:cursor-pointer"
                                x-model="twGraphDataDrivenCoordinates"
                            />
                            <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                                {{ __('X/Y') }}
                            </flux:label>
                        </flux:field>
                    </span>
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

                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-12"
                    :graph-id="$twGraphDataDrivenPreviewGraph->get('graph_id')"
                    :dev="true"
                    :coordinates="false"
                    :color="$twGraphDataDrivenPreviewGraph->get('color', 'cyan')"
                    :line-length="$twGraphDataDrivenPreviewGraph->get('line_length', '3.5rem')"
                    :slot-min-height="$twGraphDataDrivenPreviewGraph->get('slot_min_height', '34rem')"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        :color="$twGraphDataDrivenPreviewTrunk->get('color', 'green')"
                        :path-count="$twGraphDataDrivenPreviewTrunk->get('path_count', 4)"
                        :path-lengths="$twGraphDataDrivenPreviewTrunk->get('path_lengths', [])"
                        :end-length="$twGraphDataDrivenPreviewTrunk->get('end_length')"
                        :start-label="$twGraphDataDrivenPreviewTrunk->get('start_label')"
                        :end-label="$twGraphDataDrivenPreviewTrunk->get('end_label')"
                        :start-node-labels="$twGraphDataDrivenPreviewTrunk->get('start_node_labels', [])"
                        :node-labels="$twGraphDataDrivenPreviewTrunk->get('node_labels', [])"
                    />

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
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length', '12rem')"
                                :stem-length="$rekeyPreview->get('stem_length', '4rem')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :start-label="$rekeyPreview->get('start_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="8"
                            />
                        @elseif ($rekeyKind === 'source')
                            <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                                :component-counter="$rekeyPreview->get('component_counter', $rekeyIndex + 1)"
                                :color="$rekeyPreview->get('color', 'sky')"
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length', '12rem')"
                                :stem-length="$rekeyPreview->get('stem_length', '4rem')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :start-label="$rekeyPreview->get('start_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="8"
                            />
                        @elseif ($rekeyKind === 'target' && $rekeySide === 'left')
                            <x-translation-workbench::ui.tw-graph.strang.rekey-target-left
                                :component-counter="$rekeyPreview->get('component_counter', $rekeyIndex + 1)"
                                :color="$rekeyPreview->get('color', 'sky')"
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.3.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length', '12rem')"
                                :stem-length="$rekeyPreview->get('stem_length', '5rem')"
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
                                :attach-to="$rekeyPreview->get('attach_to', 'strang.trunk.path.3.end')"
                                :bridge-length="$rekeyPreview->get('bridge_length', '12rem')"
                                :stem-length="$rekeyPreview->get('stem_length', '5rem')"
                                :stem-continuation="$rekeyPreview->get('stem_continuation', [])"
                                :end-length="$rekeyPreview->get('end_length')"
                                :cap-length="$rekeyPreview->get('cap_length')"
                                :end-label="$rekeyPreview->get('end_label')"
                                :node-labels="$rekeyPreview->get('node_labels', [])"
                                :z-index="7"
                            />
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
                                    :attach-to="$mergePreview->get('attach_to', 'strang.trunk.path.1.end')"
                                    :bridge-length="$mergePreview->get('bridge_length', '6rem')"
                                    :stem-length="$mergePreview->get('stem_length', '5rem')"
                                    :stem-continuation="$mergePreview->get('stem_continuation', [])"
                                    :arc-sizes="$mergePreview->get('arc_sizes', [])"
                                    :start-label="$mergePreview->get('start_label')"
                                    :node-labels="$mergePreview->get('node_labels', [])"
                                    :extension-count="$mergePreview->get('extension_count', 0)"
                                    extension-bridge-length="30rem"
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
                                    :attach-to="$mergePreview->get('attach_to', 'strang.trunk.path.1.end')"
                                    :bridge-length="$mergePreview->get('bridge_length', '6rem')"
                                    :stem-length="$mergePreview->get('stem_length', '5rem')"
                                    :stem-continuation="$mergePreview->get('stem_continuation', [])"
                                    :arc-sizes="$mergePreview->get('arc_sizes', [])"
                                    :start-label="$mergePreview->get('start_label')"
                                    :node-labels="$mergePreview->get('node_labels', [])"
                                    :extension-count="$mergePreview->get('extension_count', 0)"
                                    extension-bridge-length="30rem"
                                    :extension-stem-lengths="$mergePreview->get('extension_stem_lengths', [])"
                                    :extension-stem-continuations="$mergePreview->get('extension_stem_continuations', [])"
                                    :extension-bridge-continuations="$mergePreview->get('extension_bridge_continuations', [])"
                                    :extension-arc-sizes="$mergePreview->get('extension_arc_sizes', [])"
                                    :extension-node-labels="$mergePreview->get('extension_node_labels', [])"
                                />
                            @endif
                        @endforeach
                    @endif

                    @foreach ($twGraphDataDrivenPreviewBranches as $branchIndex => $branchPreview)
                        @php
                            $branchPreview = collect($branchPreview);
                        @endphp

                        @if ($branchPreview->get('side') === 'right')
                            <x-translation-workbench::ui.tw-graph.strang.branch-right
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                :attach-to="$branchPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :bridge-length="$branchPreview->get('bridge_length', '12rem')"
                                :step="$branchPreview->get('step')"
                                :stem-length="$branchPreview->get('stem_length', '4rem')"
                                :stem-continuation="$branchPreview->get('stem_continuation', [])"
                                :branch-extension="$branchPreview->get('branch_extension', [])"
                                :node-labels="$branchPreview->get('node_labels', [])"
                                :z-index="6 - ($branchIndex % 4)"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.branch-end
                                side="right"
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                attach-to="strang.branch-right.end"
                                :length="$branchPreview->get('end_length', '2rem')"
                                :cap-length="$branchPreview->get('end_cap_length', '2rem')"
                                :end-label="$branchPreview->get('end_label')"
                                :counter-start="$branchPreview->get('end_counter_start')"
                                :z-index="5 - ($branchIndex % 4)"
                            />
                        @else
                            <x-translation-workbench::ui.tw-graph.strang.branch-left
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                :attach-to="$branchPreview->get('attach_to', 'strang.trunk.path.1.end')"
                                :bridge-length="$branchPreview->get('bridge_length', '12rem')"
                                :step="$branchPreview->get('step')"
                                :stem-length="$branchPreview->get('stem_length', '4rem')"
                                :stem-continuation="$branchPreview->get('stem_continuation', [])"
                                :branch-extension="$branchPreview->get('branch_extension', [])"
                                :node-labels="$branchPreview->get('node_labels', [])"
                                :z-index="6 - ($branchIndex % 4)"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.branch-end
                                side="left"
                                :component-counter="$branchPreview->get('component_counter', $branchIndex + 1)"
                                :color="$branchPreview->get('color', 'red')"
                                attach-to="strang.branch-left.end"
                                :length="$branchPreview->get('end_length', '2rem')"
                                :cap-length="$branchPreview->get('end_cap_length', '2rem')"
                                :end-label="$branchPreview->get('end_label')"
                                :counter-start="$branchPreview->get('end_counter_start')"
                                :z-index="5 - ($branchIndex % 4)"
                            />
                        @endif
                    @endforeach
                </x-translation-workbench::ui.tw-graph>

                @if (filled(data_get($twGraphDataDrivenPreviewGraph->get('header'), 'text')))
                    <flux:callout.heading class="mt-2 flex items-center justify-center gap-2">
                        @foreach ((array) data_get($twGraphDataDrivenPreviewGraph->get('header'), 'text', []) as $graphHeaderLine)
                            <span>{{ $graphHeaderLine }}</span>
                        @endforeach
                    </flux:callout.heading>
                @endif

            </div>
        </flux:callout>
    @endif

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
                                    <span>{{ __('last') }}: {{ $outcome->get('last_seen_at') ?: __('n/a') }}</span>
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
