{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/01-strang-trunk/02-trunk-start.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-03-trunk-start';
    $renderMode = $renderMode ?? 'documentation';
    $trunkStartProps = [
        [
            'name' => 'start-length',
            'default' => 'stem-length',
            'effect' =>
                'Controls the first visible trunk segment from the canvas origin to the trunk-start nodeEnd anchor.',
        ],
        [
            'name' => ':start-label',
            'default' => 'null',
            'effect' =>
                'Centered label for the start of the trunk. Use it for the general meaning of the chain, not for record-specific node facts.',
        ],
        [
            'name' => ':start-node-labels',
            'default' => '[]',
            'effect' =>
                'Left/right labels attached to the first trunk anchor. This is where the first concrete facts of the chain become visible.',
        ],
        [
            'name' => 'start-label-space',
            'default' => '3rem',
            'effect' =>
                'Additional measured space for the centered start label so the canvas bounds can include the label area.',
        ],
        [
            'name' => ':start-shift-enabled',
            'default' => 'config trunk_start_shift_enabled',
            'effect' =>
                'Allows the start area to be compensated if the real start-label bounds collide with side content later.',
        ],
        [
            'name' => 'start-shift-length',
            'default' => 'config trunk_start_shift_length',
            'effect' => 'Minimum compensation length used only when such a real start collision is detected.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ trunkStartVariant: 'compare' }"
    >
        <flux:callout
            color="indigo"
            icon="square-pen"
        >
            <flux:callout.heading>
                {{ __('3. Trunk start') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('The trunk start is the first meaningful boundary of the graph. The centered start label names the chain; left/right node labels describe the first concrete state.') }}
            </flux:callout.text>

            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="trunk-start-compare"
                        x-on:click="trunkStartVariant = 'compare'"
                    >
                        {{ __('Default vs props') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start-default"
                        x-on:click="trunkStartVariant = 'default'"
                    >
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start-long-start"
                        x-on:click="trunkStartVariant = 'longStart'"
                    >
                        {{ __('Long start') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start-wide-labels"
                        x-on:click="trunkStartVariant = 'wideLabels'"
                    >
                        {{ __('Wide labels') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start-spacing"
                        x-on:click="trunkStartVariant = 'spacing'"
                    >
                        {{ __('Start spacing') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-start-colors"
                        x-on:click="trunkStartVariant = 'colors'"
                    >
                        {{ __('Colors') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="trunk-start-compare">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('The left example keeps trunk-start on its defaults. The right example changes only the trunk-start props: start length, centered start label, and left/right labels at the first anchor node.') }}
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

                <flux:tab.panel name="trunk-start-default">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    start-length="4rem"
    start-label-space="3rem"
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
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-start-long-start">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    start-length="8rem"
    :start-label="[
        'text' => ['Idea development', 'longer start segment'],
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
            'text' => ['The first anchor is pushed further away from the canvas origin.'],
            'width' => 'long',
            'align' => 'left',
            'justify' => true,
        ],
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-start-wide-labels">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    start-length="4rem"
    :start-label="[
        'text' => ['Idea development', 'wider chain description'],
        'width' => 'long',
        'align' => 'center',
    ]"
    :start-node-labels="[
        'left' => [
            'text' => ['Notebook fragment', 'first recorded source'],
            'width' => 'halfLong',
            'align' => 'right',
        ],
        'right' => [
            'text' => ['This longer note shows how label width changes the required horizontal canvas space.'],
            'width' => 'long',
            'align' => 'left',
            'justify' => true,
        ],
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-start-spacing">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    start-length="4rem"
    start-label-space="6rem"
    :start-shift-enabled="true"
    start-shift-length="8rem"
    :start-label="[
        'text' => ['Idea development', 'extra start bounds'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :start-node-labels="[
        'left' => [
            'text' => ['Start bounds', 'more reserved room'],
            'width' => 'default',
            'align' => 'right',
        ],
        'right' => [
            'text' => ['Useful when later side content gets close to the trunk-start label.'],
            'width' => 'long',
            'align' => 'left',
            'justify' => true,
        ],
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-start-colors">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph
    color="indigo"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        color="sky"
        :stem-count="4"
        start-length="4rem"
        :start-label="[
            'text' => ['Idea development', 'trunk overrides graph color'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['Inherited from trunk', 'sky'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['Explicit label color', 'amber'],
                'width' => 'default',
                'align' => 'left',
                'color' => 'amber',
            ],
        ]"
    /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
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
                        @foreach ($trunkStartProps as $prop)
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
                    <span>{{ __('Step 3 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="trunkStartVariant === 'compare'"
                    >
                        {{ __('default vs props') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="trunkStartVariant === 'default'"
                    >
                        {{ __('default') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="indigo"
                        x-show="trunkStartVariant === 'longStart'"
                    >
                        {{ __('long start') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="sky"
                        x-show="trunkStartVariant === 'wideLabels'"
                    >
                        {{ __('wide labels') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="trunkStartVariant === 'spacing'"
                    >
                        {{ __('spacing') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="sky"
                        x-show="trunkStartVariant === 'colors'"
                    >
                        {{ __('colors') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            <div
                class="mt-4 grid gap-4 xl:grid-cols-2"
                x-show="trunkStartVariant === 'compare'"
            >
                <div
                    class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                >
                    <x-translation-workbench::ui.tw-graph
                        class="px-20 py-12"
                        graph-id="idea-to-paper-step-03-trunk-start-compare-default"
                        :dev="$dev"
                        :coordinates="$coordinates"
                        slot-min-height="42rem"
                        horizontal-padding="24rem"
                        min-width="40rem"
                        min-height="42rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                    </x-translation-workbench::ui.tw-graph>
                </div>

                <div
                    class="overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                >
                    <x-translation-workbench::ui.tw-graph
                        class="px-20 py-12"
                        graph-id="idea-to-paper-step-03-trunk-start-compare-props"
                        :dev="$dev"
                        :coordinates="$coordinates"
                        slot-min-height="48rem"
                        horizontal-padding="30rem"
                        min-width="52rem"
                        min-height="48rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.paper"
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
                </div>
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkStartVariant === 'default'"
            >
@endif

<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    color="indigo"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-size="2.75rem"
    cap-length="1.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
    slot-min-height="42rem"
    horizontal-padding="28rem"
    min-width="48rem"
    min-height="42rem"
>
    <x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        :stem-count="4"
        start-length="4rem"
        start-label-space="3rem"
        :stem-lengths="[
            1 => '5rem',
            2 => '5rem',
            3 => '5rem',
            4 => '5rem',
        ]"
        end-length="3rem"
        :start-label="[
            'text' => ['Idea development', 'notes to paper'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :end-label="[
            'text' => ['next', 'structure the thought'],
            'width' => 'default',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['1879 notebook', 'raw observation'],
                'width' => 'default',
                'align' => 'right',
                'color' => 'indigo',
            ],
            'right' => [
                'text' => ['A loose idea is captured as a short note.'],
                'width' => 'long',
                'align' => 'left',
                'justify' => true,
                'color' => 'zinc',
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkStartVariant === 'longStart'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-03-trunk-start-long"
            :dev="$dev"
            :coordinates="$coordinates"
            color="indigo"
            line-length="4rem"
            stem-length="5rem"
            slot-min-height="48rem"
            horizontal-padding="28rem"
            min-width="48rem"
            min-height="48rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                :stem-count="4"
                start-length="8rem"
                :stem-lengths="[
                    1 => '5rem',
                    2 => '5rem',
                    3 => '5rem',
                    4 => '5rem',
                ]"
                end-length="3rem"
                :start-label="[
                    'text' => ['Idea development', 'longer start segment'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
                :end-label="[
                    'text' => ['next', 'structure the thought'],
                    'width' => 'default',
                    'align' => 'center',
                ]"
                :start-node-labels="[
                    'left' => [
                        'text' => ['1879 notebook', 'raw observation'],
                        'width' => 'default',
                        'align' => 'right',
                        'color' => 'indigo',
                    ],
                    'right' => [
                        'text' => ['The first anchor is pushed further away from the canvas origin.'],
                        'width' => 'long',
                        'align' => 'left',
                        'justify' => true,
                        'color' => 'zinc',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkStartVariant === 'wideLabels'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-24 py-12"
            graph-id="idea-to-paper-step-03-trunk-start-wide"
            :dev="$dev"
            :coordinates="$coordinates"
            color="indigo"
            line-length="4rem"
            stem-length="5rem"
            slot-min-height="42rem"
            horizontal-padding="34rem"
            min-width="56rem"
            min-height="42rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                :stem-count="4"
                start-length="4rem"
                :stem-lengths="[
                    1 => '5rem',
                    2 => '5rem',
                    3 => '5rem',
                    4 => '5rem',
                ]"
                end-length="3rem"
                :start-label="[
                    'text' => ['Idea development', 'wider chain description'],
                    'width' => 'long',
                    'align' => 'center',
                ]"
                :end-label="[
                    'text' => ['next', 'structure the thought'],
                    'width' => 'default',
                    'align' => 'center',
                ]"
                :start-node-labels="[
                    'left' => [
                        'text' => ['Notebook fragment', 'first recorded source'],
                        'width' => 'halfLong',
                        'align' => 'right',
                        'color' => 'indigo',
                    ],
                    'right' => [
                        'text' => [
                            'This longer note shows how label width changes the required horizontal canvas space.',
                        ],
                        'width' => 'long',
                        'align' => 'left',
                        'justify' => true,
                        'color' => 'zinc',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkStartVariant === 'spacing'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-03-trunk-start-spacing"
            :dev="$dev"
            :coordinates="$coordinates"
            color="indigo"
            line-length="4rem"
            stem-length="5rem"
            slot-min-height="44rem"
            horizontal-padding="28rem"
            min-width="48rem"
            min-height="44rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                :stem-count="4"
                start-length="4rem"
                start-label-space="6rem"
                :start-shift-enabled="true"
                start-shift-length="8rem"
                :stem-lengths="[
                    1 => '5rem',
                    2 => '5rem',
                    3 => '5rem',
                    4 => '5rem',
                ]"
                end-length="3rem"
                :start-label="[
                    'text' => ['Idea development', 'extra start bounds'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
                :end-label="[
                    'text' => ['next', 'structure the thought'],
                    'width' => 'default',
                    'align' => 'center',
                ]"
                :start-node-labels="[
                    'left' => [
                        'text' => ['Start bounds', 'more reserved room'],
                        'width' => 'default',
                        'align' => 'right',
                        'color' => 'indigo',
                    ],
                    'right' => [
                        'text' => ['Useful when later side content gets close to the trunk-start label.'],
                        'width' => 'long',
                        'align' => 'left',
                        'justify' => true,
                        'color' => 'zinc',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="trunkStartVariant === 'colors'"
    >
        <x-translation-workbench::ui.tw-graph
            class="px-20 py-12"
            graph-id="idea-to-paper-step-03-trunk-start-colors"
            :dev="$dev"
            :coordinates="$coordinates"
            color="indigo"
            line-length="4rem"
            stem-length="5rem"
            slot-min-height="42rem"
            horizontal-padding="28rem"
            min-width="48rem"
            min-height="42rem"
        >
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.paper"
                color="sky"
                :stem-count="4"
                start-length="4rem"
                :stem-lengths="[
                    1 => '5rem',
                    2 => '5rem',
                    3 => '5rem',
                    4 => '5rem',
                ]"
                end-length="3rem"
                :start-label="[
                    'text' => ['Idea development', 'trunk overrides graph color'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ]"
                :end-label="[
                    'text' => ['next', 'structure the thought'],
                    'width' => 'default',
                    'align' => 'center',
                ]"
                :start-node-labels="[
                    'left' => [
                        'text' => ['Inherited from trunk', 'sky'],
                        'width' => 'default',
                        'align' => 'right',
                    ],
                    'right' => [
                        'text' => ['Explicit label color', 'amber'],
                        'width' => 'default',
                        'align' => 'left',
                        'color' => 'amber',
                    ],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    </div>
    </flux:callout>
    </section>
@endif
