{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/01-strang-trunk/01-trunk.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-02-trunk';
    $renderMode = $renderMode ?? 'documentation';
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/01-strang-trunk/01-trunk.blade.php';
    $trunkProps = [
        [
            'name' => 'id',
            'default' => 'auto id',
            'effect' => 'Stable element prefix for labels, DEV identifiers, anchors, and later attach-to targets.',
        ],
        [
            'name' => 'component-counter',
            'default' => '1',
            'effect' =>
                'Fallback counter when no explicit id is supplied. Handmade graphs should usually set id explicitly.',
        ],
        [
            'name' => 'direction',
            'default' => 'bottom-top',
            'effect' => 'Direction of the trunk timeline. The usual vertical authoring flow grows from bottom to top.',
        ],
        [
            'name' => ':anchor-start',
            'default' => "['x' => '0rem', 'y' => '0rem']",
            'effect' =>
                'Manual start coordinate. Most handmade graphs can keep the default and let tw-graph provide the canvas origin.',
        ],
        [
            'name' => 'color',
            'default' => 'inherited graph color / zinc',
            'effect' => 'Trunk color inherited from tw-graph unless the trunk sets its own color.',
        ],
        [
            'name' => 'start-length',
            'default' => 'stem-length',
            'effect' => 'Length of the trunk-start segment before the first nodeEnd anchor.',
        ],
        [
            'name' => 'stem-length',
            'default' => 'tw-graph stem-length',
            'effect' => 'Default length for every trunk stem unless stem-lengths overrides a specific one.',
        ],
        [
            'name' => ':stem-count',
            'default' => 'default-path-segments',
            'effect' => 'How many trunk stems are rendered after the start segment.',
        ],
        [
            'name' => ':stem-lengths',
            'default' => '[]',
            'effect' => 'Per-stem length overrides. Missing or null entries keep the default stem length.',
        ],
        [
            'name' => ':node-labels',
            'default' => '[]',
            'effect' => 'Text labels attached to numbered trunk stem anchor nodes.',
        ],
        [
            'name' => 'default-path-segments',
            'default' => '10',
            'effect' => 'Fallback stem count when stem-count is not set.',
        ],
        ['name' => 'end-length', 'default' => 'stem-length', 'effect' => 'Length of the final trunk-end segment.'],
        ['name' => 'end-cap-length', 'default' => 'cap-length', 'effect' => 'Cap size used by the trunk end segment.'],
        [
            'name' => ':start-label',
            'default' => 'null',
            'effect' =>
                'Centered label at the trunk start. Accepts text, width, align, color, and related text-label options.',
        ],
        ['name' => ':end-label', 'default' => 'null', 'effect' => 'Centered label at the trunk end.'],
        [
            'name' => ':start-node-labels',
            'default' => '[]',
            'effect' => 'Left/right labels attached to the trunk-start nodeEnd anchor.',
        ],
        [
            'name' => 'start-label-space',
            'default' => '3rem',
            'effect' => 'Extra space considered for start-label placement and bounds.',
        ],
        [
            'name' => ':start-shift-enabled',
            'default' => 'config trunk_start_shift_enabled',
            'effect' => 'Allows collision compensation to extend the start area when needed.',
        ],
        [
            'name' => 'start-shift-length',
            'default' => 'config trunk_start_shift_length',
            'effect' => 'Minimum visible delta used when trunk-start collision compensation is applied.',
        ],
        [
            'name' => 'z-index',
            'default' => '20',
            'effect' => 'Layer position of the trunk relative to side strangs and DEV overlays.',
        ],
        ['name' => 'counter-start', 'default' => '1', 'effect' => 'Initial DEV node counter value for this trunk.'],
        [
            'name' => ':dev-mode',
            'default' => 'inherited dev',
            'effect' => 'Optional local DEV override. Usually inherited from tw-graph.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ trunkVariant: 'default' }"
    >
        <flux:callout
            color="indigo"
            icon="git-commit-horizontal"
        >
            <flux:callout.heading>
                {{ __('2. Add the trunk') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('The trunk is the first real graph structure inside the canvas. Here it is still treated as one authoring component; trunk-start, trunk stems, and trunk-end are documented separately in the next steps.') }}
            </flux:callout.text>

            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="trunk-default"
                        x-on:click="trunkVariant = 'default'"
                    >
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-stem-count"
                        x-on:click="trunkVariant = 'stemCount'"
                    >
                        {{ __('Stem count') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-stem-lengths"
                        x-on:click="trunkVariant = 'stemLengths'"
                    >
                        {{ __('Stem lengths') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-direction"
                        x-on:click="trunkVariant = 'direction'"
                    >
                        {{ __('Direction') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start"
                        x-on:click="trunkVariant = 'trunkStart'"
                    >
                        {{ __('Trunk start') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start-shift"
                        x-on:click="trunkVariant = 'startShift'"
                    >
                        {{ __('Start shift') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-end"
                        x-on:click="trunkVariant = 'trunkEnd'"
                    >
                        {{ __('Trunk end') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="trunk-default">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Default renders the trunk exactly as configured by the graph defaults. No trunk prop changes the visual output here.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    :dev="true"
    slot-min-height="42rem"
    horizontal-padding="24rem"
    min-width="40rem"
    min-height="42rem"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-stem-count">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Only :stem-count is changed here. The default stem count is replaced by 4 rendered trunk stems.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        <span class="text-lime-300">:stem-count="4"</span>
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-stem-lengths">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('This builds on the previous stem-count example. Only selected stem lengths are overridden; missing or null entries continue to use the default stem length. Stem indexes name the rendered stem sections; they are not the same as DEV node counters, which usually appear one step higher at the stem end anchor.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        :stem-count="4"
        <span class="text-lime-300">:stem-lengths="[
            1 => '3rem',
            2 => null,
            3 => '8rem',
            4 => '4rem',
        ]"</span>
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-direction">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Only direction is changed here. The default bottom-to-top flow is replaced by top-to-bottom rendering.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        <span class="text-lime-300">direction="top-bottom"</span>
        :stem-count="4"
        start-length="4rem"
        end-length="3rem"
        :start-label="[
            'text' => ['Idea development', 'top to bottom'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        <span class="text-amber-300">:end-label="[
            'text' => ['next', 'inverted direction'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"</span>
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-start">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Trunk start compares the untouched start segment with explicit start props. The highlighted props control the first stem, the centered start label, and the left/right labels at the first anchor node.') }}
                    </p>
                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        <span class="text-amber-300">color="sky"</span>
        <span class="text-lime-300">start-length="8rem"
        :start-label="[
            'text' => ['Idea development', 'notes to paper'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['1879 notebook', 'raw observation'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['A loose idea is captured as a short note.'],
                'width' => 'long',
                'align' => 'left',
                'justify' => true,
            ],
        ]"</span>
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-start-shift">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Start shift is off by default. When enabled explicitly, it moves only the trunk-start origin away from the first regular trunk stem; downstream attach-to points stay stable. The merge is shown only as a reference attached to stem-1.') }}
                    </p>
                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        <span class="text-amber-300">color="zinc"</span>
        :stem-count="3"
        <span class="text-amber-300">:node-labels="[
            1 => [
                'right' => [
                    'text' => ['Draft state', 'before merge'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"</span>
    /&gt;

    <span class="text-amber-300">&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
        attach-to="strang.trunk.center.1.stem-1"
        ...
    /&gt;</span>
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        <span class="text-amber-300">color="violet"</span>
        :stem-count="3"
        <span class="text-lime-300">:start-shift-enabled="true"
        start-shift-length="14rem"</span>
        <span class="text-amber-300">:node-labels="[
            1 => [
                'right' => [
                    'text' => ['Draft state', 'before merge'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"</span>
    /&gt;

    <span class="text-amber-300">&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
        attach-to="strang.trunk.center.1.stem-1"
        ...
    /&gt;</span>
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-end">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Trunk end compares the default closing segment with explicit end props. The highlighted props control the final stem, cap width, and centered end label.') }}
                    </p>
                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph ...&gt;
    ...
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        <span class="text-amber-300">color="emerald"</span>
        <span class="text-lime-300">end-length="7rem"
        end-cap-length="3rem"
        :end-label="[
            'text' => ['published paper', 'stable reference'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"</span>
    /&gt;
    ...
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                    </div>
                </flux:tab.panel>
            </flux:tab.group>

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column class="w-36">{{ __('Prop') }}</flux:table.column>
                        <flux:table.column class="w-36">{{ __('Default') }}</flux:table.column>
                        <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($trunkProps as $prop)
                            <flux:table.row>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['name'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['default'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell
                                    class="min-w-0 whitespace-normal break-words text-xs leading-5 text-zinc-600 dark:text-zinc-300"
                                >
                                    {{ $prop['effect'] }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>

        <flux:callout
            color="zinc"
            icon="square-dashed-text"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span>{{ __('Step 2 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="trunkVariant === 'default'"
                    >
                        {{ __('default') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="indigo"
                        x-show="trunkVariant === 'stemCount'"
                    >
                        {{ __('stem count') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="trunkVariant === 'stemLengths'"
                    >
                        {{ __('stem lengths') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="sky"
                        x-show="trunkVariant === 'direction'"
                    >
                        {{ __('direction') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="indigo"
                        x-show="trunkVariant === 'trunkStart'"
                    >
                        {{ __('trunk start') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="violet"
                        x-show="trunkVariant === 'startShift'"
                    >
                        {{ __('start shift') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="emerald"
                        x-show="trunkVariant === 'trunkEnd'"
                    >
                        {{ __('trunk end') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkVariant === 'default'"
            >
@endif

<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    slot-min-height="42rem"
    horizontal-padding="24rem"
    min-width="40rem"
    min-height="42rem"
>
    <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkVariant === 'stemCount'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-02-trunk-stem-count"
            :dev="$dev"
            :coordinates="$coordinates"
            slot-min-height="54rem"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="54rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                :stem-count="4"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkVariant === 'stemLengths'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-02-trunk-stem-lengths"
            :dev="$dev"
            :coordinates="$coordinates"
            slot-min-height="48rem"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="48rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                :stem-count="4"
                :stem-lengths="[
                    1 => '3rem',
                    2 => null,
                    3 => '8rem',
                    4 => '4rem',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkVariant === 'direction'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-02-trunk-direction"
            :dev="$dev"
            :coordinates="$coordinates"
            slot-min-height="42rem"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="42rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                direction="top-bottom"
                :stem-count="4"
                start-length="4rem"
                end-length="3rem"
                :start-label="[
                    'text' => ['Idea development', 'top to bottom'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
                :end-label="[
                    'text' => ['next', 'inverted direction'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div
        class="mt-4 grid gap-4 xl:grid-cols-2"
        x-show="trunkVariant === 'trunkStart'"
    >
        <div
            class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-20 py-12"
                graph-id="idea-to-paper-step-02-trunk-start-default"
                :dev="$dev"
                :coordinates="$coordinates"
                slot-min-height="42rem"
                horizontal-padding="24rem"
                min-width="40rem"
                min-height="42rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
            </x-translation-workbench::ui.tw-graph>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>

        <div
            class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-20 py-12"
                graph-id="idea-to-paper-step-02-trunk-start-props"
                :dev="$dev"
                :coordinates="$coordinates"
                slot-min-height="48rem"
                horizontal-padding="30rem"
                min-width="52rem"
                min-height="48rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    id="literature.center.1.paper"
                    color="sky"
                    start-length="8rem"
                    :start-label="[
                        'text' => ['Idea development', 'notes to paper'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                    :start-node-labels="[
                        'left' => [
                            'text' => ['1879 notebook', 'raw observation'],
                            'width' => 'default',
                            'align' => 'right',
                        ],
                        'right' => [
                            'text' => ['A loose idea is captured as a short note.'],
                            'width' => 'long',
                            'align' => 'left',
                            'justify' => true,
                        ],
                    ]"
                />
            </x-translation-workbench::ui.tw-graph>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>
    </div>

    <div
        class="mt-4 grid gap-4 xl:grid-cols-2"
        x-show="trunkVariant === 'startShift'"
    >
        <div
            class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <p class="px-4 pt-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Default: no start shift') }}
            </p>
            <x-translation-workbench::ui.tw-graph
                class="px-20 py-12"
                graph-id="idea-to-paper-step-02-trunk-start-shift-default"
                :dev="$dev"
                :coordinates="$coordinates"
                slot-min-height="54rem"
                horizontal-padding="30rem"
                min-width="52rem"
                min-height="54rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    id="literature.center.1.paper"
                    color="zinc"
                    :stem-count="3"
                    :node-labels="[
                        1 => [
                            'right' => [
                                'text' => ['Draft state', 'before merge'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.merge-left
                    id="literature.left.1.review-note"
                    attach-to="strang.trunk.center.1.stem-1"
                />
            </x-translation-workbench::ui.tw-graph>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>

        <div
            class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <p class="px-4 pt-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('Explicit: start origin shifted') }}
            </p>
            <x-translation-workbench::ui.tw-graph
                class="px-20 py-12"
                graph-id="idea-to-paper-step-02-trunk-start-shift-props"
                :dev="$dev"
                :coordinates="$coordinates"
                slot-min-height="70rem"
                horizontal-padding="30rem"
                min-width="52rem"
                min-height="70rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    id="literature.center.1.paper"
                    color="violet"
                    :stem-count="3"
                    :start-shift-enabled="true"
                    start-shift-length="14rem"
                    :node-labels="[
                        1 => [
                            'right' => [
                                'text' => ['Draft state', 'before merge'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.merge-left
                    id="literature.left.1.review-note"
                    attach-to="strang.trunk.center.1.stem-1"
                />
            </x-translation-workbench::ui.tw-graph>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>
    </div>

    <div
        class="mt-4 grid gap-4 xl:grid-cols-2"
        x-show="trunkVariant === 'trunkEnd'"
    >
        <div
            class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-20 py-12"
                graph-id="idea-to-paper-step-02-trunk-end-default"
                :dev="$dev"
                :coordinates="$coordinates"
                slot-min-height="42rem"
                horizontal-padding="24rem"
                min-width="40rem"
                min-height="42rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
            </x-translation-workbench::ui.tw-graph>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>

        <div
            class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-20 py-12"
                graph-id="idea-to-paper-step-02-trunk-end-props"
                :dev="$dev"
                :coordinates="$coordinates"
                slot-min-height="50rem"
                horizontal-padding="28rem"
                min-width="48rem"
                min-height="50rem"
            >
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    id="literature.center.1.paper"
                    color="emerald"
                    end-length="7rem"
                    end-cap-length="3rem"
                    :end-label="[
                        'text' => ['published paper', 'stable reference'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                />
            </x-translation-workbench::ui.tw-graph>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>
    </div>
    </flux:callout>
    </section>
@endif
