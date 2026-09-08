{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/01-strang-trunk/03-trunk-end.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-04-trunk-end';
    $renderMode = $renderMode ?? 'documentation';
    $trunkEndProps = [
        [
            'name' => 'end-length',
            'default' => 'stem-length',
            'effect' => 'Controls the final trunk stem before the cap closes the chain.',
        ],
        [
            'name' => 'end-cap-length',
            'default' => 'cap-length',
            'effect' => 'Controls the horizontal cap width of the trunk end.',
        ],
        [
            'name' => ':end-label',
            'default' => 'null',
            'effect' =>
                'Centered label for the end of the trunk. Use it for the closing state or outcome of the visible chain.',
        ],
        [
            'name' => 'color',
            'default' => 'inherited graph color',
            'effect' => 'Sets the trunk line, cap, nodes, and labels unless a nested label defines its own color.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ trunkEndVariant: 'default' }"
    >
        <flux:callout
            color="indigo"
            icon="flag"
        >
            <flux:callout.heading>
                {{ __('4. Trunk end') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('The trunk end closes the central chain. Its label is centered over the end cap and should describe the outcome, not another side-node fact.') }}
            </flux:callout.text>

            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="trunk-end-default"
                        x-on:click="trunkEndVariant = 'default'"
                    >
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-end-long-end"
                        x-on:click="trunkEndVariant = 'longEnd'"
                    >
                        {{ __('Long end') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-end-wide-label"
                        x-on:click="trunkEndVariant = 'wideLabel'"
                    >
                        {{ __('Wide label') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-end-cap"
                        x-on:click="trunkEndVariant = 'cap'"
                    >
                        {{ __('Cap length') }}
                    </flux:tab>
                    <flux:tab
                        name="trunk-end-color"
                        x-on:click="trunkEndVariant = 'color'"
                    >
                        {{ __('Color') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="trunk-end-default">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    end-length="3rem"
    end-cap-length="1.75rem"
    :end-label="[
        'text' => ['draft complete', 'ready for review'],
        'width' => 'default',
        'align' => 'center',
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-end-long-end">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    end-length="7rem"
    :end-label="[
        'text' => ['draft complete', 'longer closing stem'],
        'width' => 'default',
        'align' => 'center',
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-end-wide-label">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    end-length="3rem"
    :end-label="[
        'text' => ['published paper', 'the idea becomes a stable reference'],
        'width' => 'long',
        'align' => 'center',
        'justify' => true,
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-end-cap">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id="literature.center.1.paper"
    :stem-count="4"
    end-length="3rem"
    end-cap-length="3rem"
    :end-label="[
        'text' => ['draft complete', 'wider end cap'],
        'width' => 'default',
        'align' => 'center',
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="trunk-end-color">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph
    color="indigo"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        color="emerald"
        :stem-count="4"
        :end-label="[
            'text' => ['explicit end label color', 'amber'],
            'width' => 'default',
            'align' => 'center',
            'color' => 'amber',
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
                        @foreach ($trunkEndProps as $prop)
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
                    <span>{{ __('Step 4 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="trunkEndVariant === 'default'"
                    >
                        {{ __('default') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="indigo"
                        x-show="trunkEndVariant === 'longEnd'"
                    >
                        {{ __('long end') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="sky"
                        x-show="trunkEndVariant === 'wideLabel'"
                    >
                        {{ __('wide label') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="trunkEndVariant === 'cap'"
                    >
                        {{ __('cap') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="emerald"
                        x-show="trunkEndVariant === 'color'"
                    >
                        {{ __('color') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkEndVariant === 'default'"
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
        :stem-lengths="[
            1 => '5rem',
            2 => '5rem',
            3 => '5rem',
            4 => '5rem',
        ]"
        end-length="3rem"
        end-cap-length="1.75rem"
        :start-label="[
            'text' => ['Idea development', 'notes to paper'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :end-label="[
            'text' => ['draft complete', 'ready for review'],
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
        :node-labels="[
            2 => [
                'left' => [
                    'text' => ['1888 outline', 'first structure'],
                    'width' => 'default',
                    'align' => 'right',
                ],
                'right' => [
                    'text' => ['The note receives a rough order and becomes a working outline.'],
                    'width' => 'long',
                    'align' => 'left',
                    'justify' => true,
                ],
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>

@if ($renderMode === 'documentation')
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkEndVariant === 'longEnd'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-04-trunk-end-long"
                    :dev="$dev"
                    :coordinates="$coordinates"
                    color="indigo"
                    line-length="4rem"
                    stem-length="5rem"
                    slot-min-height="46rem"
                    horizontal-padding="28rem"
                    min-width="48rem"
                    min-height="46rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        id="literature.center.1.paper"
                        :stem-count="4"
                        start-length="4rem"
                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                        end-length="7rem"
                        :start-label="[
                            'text' => ['Idea development', 'notes to paper'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :end-label="[
                            'text' => ['draft complete', 'longer closing stem'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :start-node-labels="[
                            'left' => [
                                'text' => ['1879 notebook', 'raw observation'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkEndVariant === 'wideLabel'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-12"
                    graph-id="idea-to-paper-step-04-trunk-end-wide"
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
                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                        end-length="3rem"
                        :start-label="[
                            'text' => ['Idea development', 'notes to paper'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :end-label="[
                            'text' => ['published paper', 'the idea becomes a stable reference'],
                            'width' => 'long',
                            'align' => 'center',
                            'justify' => true,
                        ]"
                        :start-node-labels="[
                            'left' => [
                                'text' => ['1879 notebook', 'raw observation'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkEndVariant === 'cap'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-04-trunk-end-cap"
                    :dev="$dev"
                    :coordinates="$coordinates"
                    color="indigo"
                    line-length="4rem"
                    stem-length="5rem"
                    cap-length="1.75rem"
                    slot-min-height="42rem"
                    horizontal-padding="28rem"
                    min-width="48rem"
                    min-height="42rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        id="literature.center.1.paper"
                        :stem-count="4"
                        start-length="4rem"
                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                        end-length="3rem"
                        end-cap-length="3rem"
                        :start-label="[
                            'text' => ['Idea development', 'notes to paper'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :end-label="[
                            'text' => ['draft complete', 'wider end cap'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :start-node-labels="[
                            'left' => [
                                'text' => ['1879 notebook', 'raw observation'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="trunkEndVariant === 'color'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-04-trunk-end-color"
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
                        color="emerald"
                        :stem-count="4"
                        start-length="4rem"
                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                        end-length="3rem"
                        :start-label="[
                            'text' => ['Idea development', 'notes to paper'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :end-label="[
                            'text' => ['explicit end label color', 'amber'],
                            'width' => 'default',
                            'align' => 'center',
                            'color' => 'amber',
                        ]"
                        :start-node-labels="[
                            'left' => [
                                'text' => ['Inherited from trunk', 'emerald'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>
        </flux:callout>
    </section>
@endif
