{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/primitives-catalog.blade.php --}}

@php
    $tab = '__tw_indent__';
    $ellipsis = '...';
    $primitiveRows = [
        [
            'name' => 'line',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.primitives.line',
            'purpose' =>
                'Neutral line primitive. Segments decide whether it becomes path.top-bottom, path-start, path-end, merge-path, etc.',
            'props' => [
                $tab . 'id="line"',
                $tab . 'direction="bottom-top"',
                $tab . 'length="4rem"',
                $tab . 'startX="0rem"',
                $tab . 'startY="0rem"',
                $tab . 'endX="0rem"',
                $tab . 'endY="0rem"',
                $tab . 'nodeStart=false',
                $tab . 'nodeEnd=false',
                $tab . 'gradient=false',
                $tab . 'cap=false',
                $tab . 'capLength="1.25rem"',
                $tab . 'color="cyan"',
            ],
            'view' => 'line',
        ],
        [
            'name' => 'arc',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.primitives.arc',
            'purpose' => 'Neutral quarter-circle primitive. Segments decide the start/end anchor pair.',
            'props' => [
                $tab . 'id="arc"',
                $tab . 'startAnchor="e"',
                $tab . 'endAnchor="s"',
                $tab . 'startX="0rem"',
                $tab . 'startY="0rem"',
                $tab . 'endX="0rem"',
                $tab . 'endY="0rem"',
                $tab . 'color="cyan"',
            ],
            'view' => 'arc',
        ],
        [
            'name' => 'connector',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.primitives.connector',
            'purpose' => 'Neutral helper connector. Segments decide which text/node it belongs to.',
            'props' => [
                $tab . 'id="connector"',
                $tab . 'placement="right"',
                $tab . 'anchorX="0rem"',
                $tab . 'anchorY="0rem"',
                $tab . 'length="2rem"',
                $tab . 'gap=null',
                $tab . 'color="cyan"',
            ],
            'view' => 'connector',
        ],
        [
            'name' => 'text',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.primitives.text',
            'purpose' =>
                'Neutral text primitive. Segments decide whether it is a label, start/end text, name, or DEV marker.',
            'props' => [
                $tab . 'id="text"',
                $tab . 'text=null',
                $tab . 'anchorX="0rem"',
                $tab . 'anchorY="0rem"',
                $tab . 'side="right"',
                $tab . 'offset="0rem"',
                $tab . 'badge=true',
                $tab . 'badgeColor="cyan"',
            ],
            'view' => 'text',
        ],
        [
            'name' => 'node',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.primitives.node',
            'purpose' => 'Neutral point marker. Segments decide whether a node is rendered at start/end/dev anchors.',
            'props' => [
                $tab . 'id="node"',
                $tab . 'anchorX="0rem"',
                $tab . 'anchorY="0rem"',
                $tab . 'size=null',
                $tab . 'color="cyan"',
            ],
            'view' => 'node',
        ],
        [
            'name' => 'dev-node-counter',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.primitives.dev-node-counter',
            'purpose' =>
                'DEV-only start-anchor marker for a segment. Horizontal segments render above, vertical segments beside.',
            'props' => [
                $tab . ':dev="true"',
                $tab . ':segment="$segment"',
                $tab . 'counter="1"',
                $tab . 'side="right|left"',
                $tab . 'color="zinc"',
            ],
            'view' => 'dev-node-counter',
        ],
    ];

    $devCounterHorizontalSegment = [
        'id' => 'catalog.primitive.dev-counter.horizontal',
        'direction' => 'left-right',
        'anchorStart' => ['x' => '-2rem', 'y' => '3rem'],
    ];
    $devCounterVerticalSegment = [
        'id' => 'catalog.primitive.dev-counter.vertical',
        'direction' => 'bottom-top',
        'anchorStart' => ['x' => '1.5rem', 'y' => '1.5rem'],
    ];
@endphp

<div
    class="w-full"
    x-data="{ twGraphDev: true }"
>
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading class="rounded rounded-b-md bg-sky-800 p-2">
                <span class="inline-flex items-center gap-2">
                    <span class="w-32">{{ __('Primitive catalog') }}</span>
                    <flux:field
                        class="items-center gap-2"
                        variant="inline"
                        x-on:click.stop
                    >
                        <flux:switch
                            class="switch-colored hover:cursor-pointer"
                            x-model="twGraphDev"
                        />
                        <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                            {{ __('DEV') }}
                        </flux:label>
                    </flux:field>
                    <flux:badge
                        class="w-xs inline-block"
                        color="sky"
                    >
                        <flux:breadcrumbs>
                            <flux:breadcrumbs.item href="#">{{ __('primitives') }}</flux:breadcrumbs.item>
                        </flux:breadcrumbs>
                    </flux:badge>
                    <flux:badge
                        class="w-48"
                        color="amber"
                    >
                        {{ __('primitive building blocks') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('line / arc / connector / text / node / dev-node-counter') }}
                    </flux:badge>
                </span>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div
                    class="mt-3 w-full"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !twGraphDev }"
                >
                    <flux:tab.group>
                        <flux:tabs>
                            @foreach ($primitiveRows as $row)
                                <flux:tab name="{{ $row['view'] }}">
                                    {{ $row['name'] }}
                                </flux:tab>
                            @endforeach
                        </flux:tabs>

                        @foreach ($primitiveRows as $row)
                            <flux:tab.panel name="{{ $row['view'] }}">
                                <flux:card class="mt-3 dark:bg-zinc-800">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                            {{ $row['name'] }}
                                        </span>
                                        <flux:badge
                                            size="sm"
                                            color="red"
                                        >
                                            {{ __('primitive') }}
                                        </flux:badge>
                                    </div>

                                    {{-- Component Canvas Rendering --}}
                                    <div class="my-24 flex justify-center">
                                        <div
                                            class="tw-graph-protocol tw-graph-protocol-catalog-preview"
                                            style="
                                                --tw-graph-protocol-color-rgb: 6 182 212;
                                                --tw-graph-protocol-color-alpha: 0.5;
                                                --tw-graph-protocol-path-width: 0.25rem;
                                                --tw-graph-protocol-path-half: calc(var(--tw-graph-protocol-path-width) / 2);
                                                --tw-graph-protocol-text-connector-width: calc(var(--tw-graph-protocol-path-width) / 2);
                                                --tw-graph-protocol-text-connector-anchor-gap: 0.25rem;
                                                --tw-graph-protocol-node-size: 1rem;
                                                --tw-graph-protocol-node-half: calc(var(--tw-graph-protocol-node-size) / 2);
                                                --tw-graph-protocol-arc-size: 2.75rem;
                                                --tw-graph-protocol-arc-radius: var(--tw-graph-protocol-arc-size);
                                                --tw-graph-protocol-trunk-x: 50%;
                                            "
                                        >
                                            {{-- Component Calls / Props --}}
                                            @switch($row['view'])
                                                @case('line')
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.line
                                                        id="catalog.primitive.line"
                                                        direction="bottom-top"
                                                        length="4rem"
                                                        start-x="0rem"
                                                        start-y="0.75rem"
                                                        end-x="0rem"
                                                        end-y="4.75rem"
                                                        :node-start="false"
                                                        :node-end="true"
                                                        color="cyan"
                                                    />
                                                @break

                                                @case('arc')
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.arc
                                                        id="catalog.primitive.arc"
                                                        start-anchor="e"
                                                        end-anchor="s"
                                                        start-x="0rem"
                                                        start-y="4.5rem"
                                                        end-x="-2.5rem"
                                                        end-y="2rem"
                                                        color="amber"
                                                    />
                                                @break

                                                @case('connector')
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.connector
                                                        id="catalog.primitive.connector"
                                                        placement="right"
                                                        anchor-x="-1.5rem"
                                                        anchor-y="3rem"
                                                        length="3rem"
                                                        gap="0.25rem"
                                                        color="sky"
                                                    />
                                                @break

                                                @case('text')
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.text
                                                        id="catalog.primitive.text"
                                                        text="Text"
                                                        side="right"
                                                        anchor-x="-1.5rem"
                                                        anchor-y="3rem"
                                                        offset="3.25rem"
                                                        badge-color="sky"
                                                    />
                                                @break

                                                @case('node')
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.node
                                                        id="catalog.primitive.node"
                                                        anchor-x="0rem"
                                                        anchor-y="3rem"
                                                        color="lime"
                                                    />
                                                @break

                                                @case('dev-node-counter')
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.line
                                                        id="catalog.primitive.dev-counter.horizontal.line"
                                                        direction="left-right"
                                                        length="4rem"
                                                        start-x="-2rem"
                                                        start-y="3rem"
                                                        end-x="2rem"
                                                        end-y="3rem"
                                                        :node-start="true"
                                                        :node-end="false"
                                                        color="zinc"
                                                    />
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.dev-node-counter
                                                        id="catalog.primitive.dev-counter.horizontal"
                                                        :dev="true"
                                                        :segment="$devCounterHorizontalSegment"
                                                        counter="H"
                                                        color="amber"
                                                    />

                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.line
                                                        id="catalog.primitive.dev-counter.vertical.line"
                                                        direction="bottom-top"
                                                        length="3rem"
                                                        start-x="1.5rem"
                                                        start-y="1.5rem"
                                                        end-x="1.5rem"
                                                        end-y="4.5rem"
                                                        :node-start="true"
                                                        :node-end="false"
                                                        color="zinc"
                                                    />
                                                    <x-translation-workbench::ui.tw-graph-protocol.primitives.dev-node-counter
                                                        id="catalog.primitive.dev-counter.vertical"
                                                        :dev="true"
                                                        :segment="$devCounterVerticalSegment"
                                                        counter="V"
                                                        side="right"
                                                        color="sky"
                                                    />
                                                @break
                                            @endswitch
                                        </div>
                                    </div>

                                    {{-- Component Call --}}
                                    <div
                                        class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-[0.7rem] dark:border-zinc-700"
                                    >
                                        <div>
                                            <flux:callout
                                                color="green"
                                                icon="code-xml"
                                            >
                                                <flux:callout.heading>
                                                    {{ __('Component Call') }}
                                                </flux:callout.heading>
                                                <flux:callout.text>
                                                    <flux:text
                                                        class="mt-2 font-mono"
                                                        color="sky"
                                                    >
                                                        {{ $row['component'] }}
                                                    </flux:text>
                                                </flux:callout.text>
                                            </flux:callout>
                                        </div>
                                        {{-- Component Purpose --}}
                                        <div>
                                            <flux:callout
                                                color="blue"
                                                icon="info"
                                            >
                                                <flux:callout.heading>
                                                    {{ __('Purpose') }}
                                                </flux:callout.heading>
                                                <flux:callout.text>
                                                    <flux:text class="mt-2">
                                                        {{ __($row['purpose']) }}
                                                    </flux:text>
                                                </flux:callout.text>
                                            </flux:callout>
                                        </div>
                                        {{-- Component Props --}}
                                        <div>
                                            <flux:callout
                                                color="fuchsia"
                                                icon="sliders-horizontal"
                                            >
                                                <flux:callout.heading>
                                                    {{ __('Props') }}
                                                </flux:callout.heading>
                                                <flux:callout.text>
                                                    <div class="mt-2 space-y-1">
                                                        @foreach ($row['props'] as $prop)
                                                            @php
                                                                $propText = str_starts_with($prop, $tab)
                                                                    ? substr($prop, strlen($tab))
                                                                    : $prop;
                                                            @endphp
                                                            <flux:text class="font-mono leading-tight">
                                                                {{ $propText }}
                                                            </flux:text>
                                                        @endforeach
                                                    </div>
                                                </flux:callout.text>
                                            </flux:callout>
                                        </div>
                                    </div>
                                </flux:card>
                            </flux:tab.panel>
                        @endforeach
                    </flux:tab.group>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
