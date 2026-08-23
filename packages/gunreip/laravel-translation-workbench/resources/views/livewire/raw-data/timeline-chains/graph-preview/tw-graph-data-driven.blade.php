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
    $twGraphDataDrivenPreview = collect(data_get($twGraphDataDriven, 'render_preview', []));
    $twGraphDataDrivenPreviewGraph = collect($twGraphDataDrivenPreview->get('graph', []));
    $twGraphDataDrivenPreviewTrunk = collect($twGraphDataDrivenPreview->get('trunk', []));
    $twGraphDataDrivenPreviewMerge = collect($twGraphDataDrivenPreview->get('merge', []));
    $twGraphDataDrivenPreviewMerges = collect($twGraphDataDrivenPreview->get('merges', []));
    $twGraphDataDrivenPreviewLimits = collect($twGraphDataDrivenPreview->get('limits', []));
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
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-12"
                    :graph-id="$twGraphDataDrivenPreviewGraph->get('graph_id')"
                    :dev="true"
                    :coordinates="true"
                    :color="$twGraphDataDrivenPreviewGraph->get('color', 'cyan')"
                    :line-length="$twGraphDataDrivenPreviewGraph->get('line_length', '3.5rem')"
                    :slot-min-height="$twGraphDataDrivenPreviewGraph->get('slot_min_height', '34rem')"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        :color="$twGraphDataDrivenPreviewTrunk->get('color', 'green')"
                        :path-count="$twGraphDataDrivenPreviewTrunk->get('path_count', 4)"
                        :path-lengths="$twGraphDataDrivenPreviewTrunk->get('path_lengths', [])"
                        :start-label="$twGraphDataDrivenPreviewTrunk->get('start_label')"
                        :end-label="$twGraphDataDrivenPreviewTrunk->get('end_label')"
                        :node-labels="$twGraphDataDrivenPreviewTrunk->get('node_labels', [])"
                    />

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
                                    :start-label="$mergePreview->get('start_label')"
                                    :node-labels="$mergePreview->get('node_labels', [])"
                                    :extension-count="$mergePreview->get('extension_count', 0)"
                                    extension-bridge-length="30rem"
                                    :extension-node-labels="$mergePreview->get('extension_node_labels', [])"
                                />
                            @else
                                <x-translation-workbench::ui.tw-graph.strang.merge-left
                                    :component-counter="$mergeIndex + 1"
                                    :color="$mergePreview->get('color', 'amber')"
                                    :attach-to="$mergePreview->get('attach_to', 'strang.trunk.path.1.end')"
                                    :bridge-length="$mergePreview->get('bridge_length', '6rem')"
                                    :stem-length="$mergePreview->get('stem_length', '5rem')"
                                    :start-label="$mergePreview->get('start_label')"
                                    :node-labels="$mergePreview->get('node_labels', [])"
                                    :extension-count="$mergePreview->get('extension_count', 0)"
                                    extension-bridge-length="30rem"
                                    :extension-node-labels="$mergePreview->get('extension_node_labels', [])"
                                />
                            @endif
                        @endforeach
                    @endif
                </x-translation-workbench::ui.tw-graph>
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
