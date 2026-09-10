{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/05-strang-flow/01-flow.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-08-flow';
    $renderMode = $renderMode ?? 'documentation';
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/05-strang-flow/01-flow.blade.php';
    $flowProps = [
        [
            'name' => 'id',
            'default' => 'auto id',
            'effect' =>
                'Stable element prefix for a flow strand, including labels, DEV identifiers, bounds, and attach targets.',
        ],
        [
            'name' => 'direction',
            'default' => 'bottom-top',
            'effect' =>
                'Main flow direction. The first flow examples stay vertical; later decision branches can turn left or right.',
        ],
        [
            'name' => ':start-label',
            'default' => 'null',
            'effect' =>
                'Centered label that names the flow entry point, for example a process start, first milestone, or initial state.',
        ],
        [
            'name' => ':start-node-labels',
            'default' => '[]',
            'effect' =>
                'Optional left/right facts at the first flow anchor, using the same text-label structure as trunk and branch labels.',
        ],
        [
            'name' => 'start-length',
            'default' => 'stem-length',
            'effect' => 'Length of the first visible flow stem before the first anchor node.',
        ],
        [
            'name' => 'before-length / after-length',
            'default' => '2rem / 2rem',
            'effect' =>
                'Lengths around a flow-step label. The label gap is calculated from the step label height unless label-gap is set explicitly.',
        ],
        [
            'name' => ':step-label',
            'default' => 'null',
            'effect' => 'Centered label that names the process step, status, or decision reason.',
        ],
        [
            'name' => ':decision-label',
            'default' => 'null',
            'effect' =>
                'Centered label at the decision anchor. The flow continues only through the left/right sideways branches.',
        ],
        [
            'name' => ':node-labels',
            'default' => '[]',
            'effect' =>
                'Optional left/right facts at the flow-step end anchor. The first step keeps only the end anchor public.',
        ],
        [
            'name' => 'color',
            'default' => 'inherited graph color / zinc',
            'effect' => 'Flow color inherited from tw-graph unless the flow component overrides it.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ flowVariant: 'start' }"
    >
        <flux:callout
            color="cyan"
            icon="workflow"
        >
            <flux:callout.heading>
                {{ __('8. Flow') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('A flow strand is intended for process-like graphs: steps, decisions, side paths, joins, and returns. It stays close to the existing strang layer, but the language is neutral enough for business processes, documentation flows, and programming-like control structures.') }}
            </flux:callout.text>

            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="flow-start"
                        x-on:click="flowVariant = 'start'"
                    >
                        {{ __('Flow start') }}
                    </flux:tab>
                    <flux:tab
                        name="flow-step"
                        x-on:click="flowVariant = 'step'"
                    >
                        {{ __('Flow step') }}
                    </flux:tab>
                    <flux:tab
                        name="flow-decision"
                        x-on:click="flowVariant = 'decision'"
                    >
                        {{ __('Flow decision') }}
                    </flux:tab>
                    <flux:tab
                        name="flow-branch-steps"
                        x-on:click="flowVariant = 'branchSteps'"
                    >
                        {{ __('Flow branch steps') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="flow-start">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Flow start defines only the entry into an Ablaufdiagramm. The public API mirrors trunk-start: one start label, one first anchor node, and optional left/right labels. Internally it still delegates to parts.start, so the existing part and segment chain remains the single geometry source.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.flow-start
    id="literature.flow.1.paper-process"
    <span class="text-lime-300">start-length="7rem"
    :start-label="[
        'text' => ['Paper process', 'start'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :start-node-labels="[
        'left' => [
            'text' => ['Input', 'raw thought'],
            'width' => 'default',
            'align' => 'right',
        ],
        'right' => [
            'text' => ['Next', 'draft decision'],
            'width' => 'default',
            'align' => 'left',
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="flow-step">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Flow step adds a labeled process step after an existing flow anchor. The centered step label describes the shared process state; optional node labels at the end anchor carry concrete facts about the next hand-authored decision point.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.flow-step
    id="literature.flow.1.paper-process.step-1"
    :anchor-start="['x' => '0rem', 'y' => '7rem']"
    <span class="text-lime-300">before-length="2rem"
    after-length="3rem"
    :step-label="[
        'text' => ['Draft prepared', 'structure exists'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :node-labels="[
        'end' => [
            'right' => [
                'text' => ['Next', 'review choice'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="flow-decision">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Flow decision marks the first explicit branch point in a handmade process graph. It does not continue the center line; it renders one decision anchor and splits the flow into left and right sideways parts.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>...

&lt;x-translation-workbench::ui.tw-graph.strang.flow-decision
    id="literature.flow.1.paper-process.decision-1"
    :anchor-start="['x' => '0rem', 'y' => '16.25rem']"
    <span class="text-lime-300">bridge-length="9rem"
    :decision-label="[
        'text' => ['Peer review', 'decision'],
        'width' => 'halfLong',
        'align' => 'center',
        'connectorLength' => '4rem',
    ]"
    :node-labels="[
        'end' => [
            'left' => [
                'text' => ['Revise', 'major comments'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['Accept', 'minor edits'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="flow-branch-steps">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('After the decision, the same flow-step component can continue on each decision output. The branch steps are anchored to the left and right ends of flow-decision, so the center line stays stopped at the decision point.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>...

&lt;x-translation-workbench::ui.tw-graph.strang.flow-step
    id="literature.flow.1.paper-process.revision-step"
    <span class="text-lime-300">:anchor-start="['x' => '-14.5rem', 'y' => '21.75rem']"</span>
    <span class="text-amber-300">before-length="1.5rem"
    after-length="2.5rem"</span>
    <span class="text-lime-300">:step-label="[
        'text' => ['Revision loop', 'comments resolved'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"</span>
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.flow-step
    id="literature.flow.1.paper-process.accepted-step"
    <span class="text-lime-300">:anchor-start="['x' => '14.5rem', 'y' => '21.75rem']"</span>
    <span class="text-amber-300">before-length="1.5rem"
    after-length="2.5rem"</span>
    <span class="text-lime-300">:step-label="[
        'text' => ['Accepted path', 'publication prep'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>
            </flux:tab.group>

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column class="w-40">{{ __('Prop') }}</flux:table.column>
                        <flux:table.column class="w-40">{{ __('Default') }}</flux:table.column>
                        <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($flowProps as $prop)
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
                    <span>{{ __('Step 8 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="cyan"
                        x-show="flowVariant === 'start'"
                    >
                        {{ __('flow start') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="sky"
                        x-show="flowVariant === 'step'"
                    >
                        {{ __('flow step') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="emerald"
                        x-show="flowVariant === 'decision'"
                    >
                        {{ __('flow decision') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="flowVariant === 'branchSteps'"
                    >
                        {{ __('flow branch steps') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="flowVariant === 'start'"
            >
@endif
{{-- Flow Start --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    color="cyan"
    slot-min-height="30rem"
    horizontal-padding="28rem"
    min-width="52rem"
    min-height="30rem"
>
    <x-translation-workbench::ui.tw-graph.strang.flow-start
        id="literature.flow.1.paper-process"
        start-length="7rem"
        :start-label="[
            'text' => ['Paper process', 'start'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['Input', 'raw thought'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['Next', 'draft decision'],
                'width' => 'default',
                'align' => 'left',
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'step'"
    >
        {{-- Flow Step --}}
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-08-flow-step"
            :dev="$dev"
            :coordinates="$coordinates"
            color="zinc"
            slot-min-height="44rem"
            horizontal-padding="32rem"
            min-width="58rem"
            min-height="44rem"
        >
            <div class="pointer-events-none opacity-25">
                <x-translation-workbench::ui.tw-graph.strang.flow-start
                    id="literature.flow.1.paper-process"
                    start-length="7rem"
                    :node-end-dot="false"
                    :start-label="[
                        'text' => ['Paper process', 'start'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                    :start-node-labels="[
                        'left' => [
                            'text' => ['Input', 'raw thought'],
                            'width' => 'default',
                            'align' => 'right',
                        ],
                    ]"
                />
            </div>

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="literature.flow.1.paper-process.step-1"
                color="cyan"
                :anchor-start="['x' => '0rem', 'y' => '7rem']"
                before-length="2rem"
                after-length="3rem"
                :step-label="[
                    'text' => ['Draft prepared', 'structure exists'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
                :node-labels="[
                    'end' => [
                        'right' => [
                            'text' => ['Next', 'review choice'],
                            'width' => 'default',
                            'align' => 'left',
                        ],
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'decision'"
    >
        {{-- Flow Decision --}}
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-08-flow-decision"
            :dev="$dev"
            :coordinates="$coordinates"
            color="zinc"
            slot-min-height="58rem"
            horizontal-padding="34rem"
            min-width="64rem"
            min-height="58rem"
        >
            <div class="pointer-events-none opacity-25">
                <x-translation-workbench::ui.tw-graph.strang.flow-start
                    id="literature.flow.1.paper-process"
                    start-length="7rem"
                    :node-end-dot="false"
                    :start-label="[
                        'text' => ['Paper process', 'start'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                    :start-node-labels="[
                        'left' => [
                            'text' => ['Input', 'raw thought'],
                            'width' => 'default',
                            'align' => 'right',
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="literature.flow.1.paper-process.step-1"
                    :anchor-start="['x' => '0rem', 'y' => '7rem']"
                    before-length="2rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => ['Draft prepared', 'structure exists'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'end' => [
                            'right' => [
                                'text' => ['Next', 'review choice'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                    ]"
                />
            </div>

            <x-translation-workbench::ui.tw-graph.strang.flow-decision
                id="literature.flow.1.paper-process.decision-1"
                color="cyan"
                :anchor-start="['x' => '0rem', 'y' => '16.25rem']"
                bridge-length="9rem"
                :decision-label="[
                    'text' => ['Peer review', 'decision'],
                    'width' => 'halfLong',
                    'align' => 'center',
                    'connectorLength' => '4rem',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'branchSteps'"
    >
        {{-- Flow Branch Step --}}
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-08-flow-branch-steps"
            :dev="$dev"
            :coordinates="$coordinates"
            color="zinc"
            slot-min-height="74rem"
            horizontal-padding="36rem"
            min-width="70rem"
            min-height="74rem"
        >
            <div class="pointer-events-none opacity-25">
                <x-translation-workbench::ui.tw-graph.strang.flow-start
                    id="literature.flow.1.paper-process"
                    start-length="7rem"
                    :node-end-dot="false"
                    :start-label="[
                        'text' => ['Paper process', 'start'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                    :start-node-labels="[
                        'left' => [
                            'text' => ['Input', 'raw thought'],
                            'width' => 'default',
                            'align' => 'right',
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.flow-step
                    id="literature.flow.1.paper-process.step-1"
                    :anchor-start="['x' => '0rem', 'y' => '7rem']"
                    before-length="2rem"
                    after-length="3rem"
                    :step-label="[
                        'text' => ['Draft prepared', 'structure exists'],
                        'width' => 'halfLong',
                        'align' => 'center',
                    ]"
                    :node-labels="[
                        'end' => [
                            'right' => [
                                'text' => ['Next', 'review choice'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.flow-decision
                    id="literature.flow.1.paper-process.decision-1"
                    :anchor-start="['x' => '0rem', 'y' => '16.25rem']"
                    bridge-length="9rem"
                    :decision-label="[
                        'text' => ['Peer review', 'decision'],
                        'width' => 'halfLong',
                        'align' => 'center',
                        'connectorLength' => '3.5rem',
                    ]"
                />
            </div>

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="literature.flow.1.paper-process.revision-step"
                color="cyan"
                :anchor-start="['x' => '-14.5rem', 'y' => '21.75rem']"
                before-length="1.5rem"
                after-length="2.5rem"
                :step-label="[
                    'text' => ['Revision loop', 'comments resolved'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
            />

            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="literature.flow.1.paper-process.accepted-step"
                color="cyan"
                :anchor-start="['x' => '14.5rem', 'y' => '21.75rem']"
                before-length="1.5rem"
                after-length="2.5rem"
                :step-label="[
                    'text' => ['Accepted path', 'publication prep'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
    </flux:callout>
    </section>
@endif
