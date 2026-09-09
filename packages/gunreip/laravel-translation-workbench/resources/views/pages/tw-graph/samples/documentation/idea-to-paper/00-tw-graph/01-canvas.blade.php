{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/00-tw-graph/01-canvas.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-01';
    $renderMode = $renderMode ?? 'documentation';
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/00-tw-graph/01-canvas.blade.php';
    $twGraphProps = [
        [
            'name' => 'protocol',
            'default' => '[]',
            'effect' =>
                'Data array for the older protocol renderer when no slot content is used. Handmade graphs normally leave this empty.',
        ],
        [
            'name' => 'graph-id',
            'default' => 'auto id',
            'effect' => 'Stable DOM id and registry scope for anchors, bounds, DEV counters, and canvas metrics.',
        ],
        [
            'name' => ':dev',
            'default' => 'false',
            'effect' => 'Enables DEV rendering such as node counters, debug boxes, and reduced graph opacity.',
        ],
        [
            'name' => ':coordinates',
            'default' => 'false',
            'effect' => 'Shows or hides coordinate badges. Calculations still run either way.',
        ],
        [
            'name' => 'color',
            'default' => "config colors.graph / 'zinc'",
            'effect' => 'Base semantic color inherited by child components unless they set their own color.',
        ],
        [
            'name' => 'line-length',
            'default' => '4rem',
            'effect' =>
                'Internal fallback length for line-based pieces. Authoring usually uses the more specific stem, bridge, start, or end length props.',
        ],
        [
            'name' => 'line-width',
            'default' => '0.25rem',
            'effect' =>
                'Central/common thickness for graph lines. Specific strangs may expose this as stem-width, bridge-width, cap-width, or connector-width in their own context.',
        ],
        ['name' => 'node-size', 'default' => '0.95rem', 'effect' => 'Diameter of visible anchor nodes.'],
        [
            'name' => 'arc-size',
            'default' => '2.75rem',
            'effect' => 'Radius footprint used by arc segments and by geometry calculations around arcs.',
        ],
        ['name' => 'cap-length', 'default' => '1.75rem', 'effect' => 'Length of end caps used by end-like segments.'],
        ['name' => 'bridge-length', 'default' => 'line-length', 'effect' => 'Default length for bridge segments.'],
        ['name' => 'stem-length', 'default' => 'line-length', 'effect' => 'Default length for stem segments.'],
        [
            'name' => 'connector-length',
            'default' => '2rem',
            'effect' => 'Default helper line length between an anchor node and a text label.',
        ],
        [
            'name' => 'connector-gap',
            'default' => '0.25rem',
            'effect' => 'Gap between a connector and its related label/node edge.',
        ],
        [
            'name' => 'slot-min-height',
            'default' => '52rem',
            'effect' => 'Minimum vertical canvas space used while slotted handmade components are rendered.',
        ],
        [
            'name' => 'horizontal-padding',
            'default' => '12rem',
            'effect' => 'Extra left/right canvas room for labels and side strangs.',
        ],
        [
            'name' => 'min-width',
            'default' => 'calculated',
            'effect' => 'Optional hard minimum width override for the graph viewport.',
        ],
        [
            'name' => 'min-height',
            'default' => 'calculated',
            'effect' => 'Optional hard minimum height override for the graph viewport.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ canvasVariant: 'default', canvasPropsVariant: 'line' }"
    >
        <flux:callout
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>
                {{ __('1. Canvas first') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Before a trunk, branch, merge, or rekey can exist, the outer graph canvas defines the shared defaults, registry scope, and DEV behavior for everything inside it.') }}
            </flux:callout.text>

            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="canvas-default"
                        x-on:click="canvasVariant = 'default'"
                    >
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab
                        name="canvas-default-trunk"
                        x-on:click="canvasVariant = 'defaultTrunk'"
                    >
                        {{ __('Default + trunk') }}
                    </flux:tab>
                    <flux:tab
                        name="canvas-coordinates"
                        x-on:click="canvasVariant = 'coordinates'"
                    >
                        {{ __('Canvas + coord') }}
                    </flux:tab>
                    <flux:tab
                        name="canvas-height"
                        x-on:click="canvasVariant = 'height'"
                    >
                        {{ __('Canvas height') }}
                    </flux:tab>
                    <flux:tab
                        name="canvas-props"
                        x-on:click="canvasVariant = 'props'"
                    >
                        {{ __('Canvas + props') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="canvas-default">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('Default renders an empty graph canvas exactly as configured by the graph defaults. No canvas prop changes the visual output here.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph&gt;&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="canvas-default-trunk">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('This keeps the canvas at its defaults and adds a default trunk. It shows why canvas sizing props become necessary once real graph content is added (missing coordinates, padding, margin, and related frame space).') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="canvas-coordinates">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('This builds on the default trunk example. Only coordinate and dimension props are added so the same trunk can be framed without changing the trunk itself.') }}
                    </p>
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-lime-300">graph-id="idea-to-paper-step-01-coordinates"</span>
    <span class="text-lime-300">:dev="true"</span>
    <span class="text-lime-300">:coordinates="true"</span>
    <span class="text-lime-300">slot-min-height="42rem"</span>
    <span class="text-lime-300">horizontal-padding="24rem"</span>
    <span class="text-lime-300">min-width="40rem"</span>
    <span class="text-lime-300">min-height="42rem"</span>
&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="canvas-height">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('This separates slot-min-height and min-height. The first graph sets slot-min-height below the calculated graph bounds, so the bounds still win. The second graph sets min-height above the calculated bounds, so the visible canvas grows.') }}
                    </p>
                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-slot-height"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="true"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-lime-300">slot-min-height="30rem"</span>
&gt;
    {{-- slot-min-height is the fallback room for slotted handmade graph content. --}}
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                        <div
                            class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                            <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-min-height"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="true"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-lime-300">min-height="88rem"</span>
&gt;
    {{-- min-height overrides the visible minimum height of the graph canvas. --}}
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                        </div>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="canvas-props">
                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        {{ __('This starts from the coordinated canvas and exposes the common canvas props. The trunk stays untouched, so the visible changes come from the graph wrapper only.') }}
                    </p>

                    <flux:tab.group class="mt-4 min-w-0 max-w-full">
                        <flux:tabs
                            scrollable
                            scrollable:fade
                            scrollable:scrollbar="hide"
                        >
                            <flux:tab
                                name="canvas-props-line"
                                x-on:click="canvasPropsVariant = 'line'"
                            >
                                {{ __('Line width') }}
                            </flux:tab>
                            <flux:tab
                                name="canvas-props-stem-length"
                                x-on:click="canvasPropsVariant = 'stemLength'"
                            >
                                {{ __('Stem length') }}
                            </flux:tab>
                            <flux:tab
                                name="canvas-props-node-size"
                                x-on:click="canvasPropsVariant = 'nodeSize'"
                            >
                                {{ __('Node size') }}
                            </flux:tab>
                            <flux:tab
                                name="canvas-props-cap-length"
                                x-on:click="canvasPropsVariant = 'capLength'"
                            >
                                {{ __('Cap length') }}
                            </flux:tab>
                            <flux:tab
                                name="canvas-props-min-width"
                                x-on:click="canvasPropsVariant = 'minWidth'"
                            >
                                {{ __('Min width') }}
                            </flux:tab>
                        </flux:tabs>

                        <flux:tab.panel name="canvas-props-line">
                            <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('line-width is the central/common thickness used as the fallback for stems, bridges, caps, connectors, and similar line-based pieces. In the more specific strang examples this same idea may appear with contextual names such as stem-width or bridge-width.') }}
                            </p>
                            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-line-default"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
&gt;
    &#123;&#123;-- Default line-width comes from the graph defaults. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-line-custom"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
    <span class="text-lime-300">color="emerald"</span>
    <span class="text-lime-300">line-width="0.5rem"</span>
&gt;
    &#123;&#123;-- The trunk has no own props here; it inherits the canvas line width. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                            </div>
                        </flux:tab.panel>

                        <flux:tab.panel name="canvas-props-stem-length">
                            <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('stem-length controls the default length of vertical stem segments. A plain trunk inherits this value for its repeated stem path unless a more specific stem length is set on the trunk itself.') }}
                            </p>
                            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-stem-default"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
&gt;
    &#123;&#123;-- Default stem-length comes from the graph defaults. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-stem-custom"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
    <span class="text-lime-300">color="sky"</span>
    <span class="text-lime-300">stem-length="8rem"</span>
&gt;
    &#123;&#123;-- The trunk has no own props here; it inherits the canvas stem length. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                            </div>
                        </flux:tab.panel>

                        <flux:tab.panel name="canvas-props-node-size">
                            <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('node-size controls the visible anchor node diameter. It affects how strongly segment boundaries appear, while the trunk itself still stays unchanged.') }}
                            </p>
                            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-node-default"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
&gt;
    &#123;&#123;-- Default node-size comes from the graph defaults. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-node-custom"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
    <span class="text-lime-300">color="violet"</span>
    <span class="text-lime-300">node-size="1.75rem"</span>
&gt;
    &#123;&#123;-- The trunk has no own props here; it inherits the canvas node size. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                            </div>
                        </flux:tab.panel>

                        <flux:tab.panel name="canvas-props-cap-length">
                            <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('cap-length controls the visible cap used by end-like segments. On this plain trunk it is easiest to see at the final trunk end.') }}
                            </p>
                            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-cap-default"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
&gt;
    &#123;&#123;-- Default cap-length comes from the graph defaults. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-cap-custom"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
    <span class="text-lime-300">color="rose"</span>
    <span class="text-lime-300">cap-length="4rem"</span>
&gt;
    &#123;&#123;-- The trunk has no own props here; it inherits the canvas cap length. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                            </div>
                        </flux:tab.panel>

                        <flux:tab.panel name="canvas-props-min-width">
                            <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('min-width controls the minimum visible canvas width. It does not change the trunk geometry itself; it changes how much horizontal room the graph canvas reserves around that geometry.') }}
                            </p>
                            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-width-default"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
&gt;
    &#123;&#123;-- Default min-width follows the calculated graph bounds. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                                <div
                                    class="overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                                    <pre><code>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-width-custom"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
    <span class="text-lime-300">color="cyan"</span>
    <span class="text-lime-300">min-width="72rem"</span>
&gt;
    &#123;&#123;-- The trunk is unchanged; only the reserved canvas width grows. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
                                </div>
                            </div>
                        </flux:tab.panel>
                    </flux:tab.group>
                </flux:tab.panel>
            </flux:tab.group>

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column class="w-32">{{ __('Prop') }}</flux:table.column>
                        <flux:table.column class="w-32">{{ __('Default') }}</flux:table.column>
                        <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($twGraphProps as $prop)
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
                    <span>{{ __('Step 1 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="canvasVariant === 'default'"
                    >
                        {{ __('empty canvas') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="canvasVariant === 'defaultTrunk'"
                    >
                        {{ __('default trunk') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="lime"
                        x-show="canvasVariant === 'coordinates'"
                    >
                        {{ __('coord + dimensions') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="canvasVariant === 'height'"
                    >
                        {{ __('height props') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="indigo"
                        x-show="canvasVariant === 'props'"
                    >
                        {{ __('canvas props') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="canvasVariant === 'default'"
            >
@endif

<x-translation-workbench::ui.tw-graph></x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="canvasVariant === 'defaultTrunk'"
    >
@endif

<x-translation-workbench::ui.tw-graph>
    <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="canvasVariant === 'coordinates'"
    >
@endif

<x-translation-workbench::ui.tw-graph
    graph-id="idea-to-paper-step-01-coordinates"
    :dev="true"
    :coordinates="true"
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
        x-show="canvasVariant === 'height'"
    >
@endif

<div class="grid gap-4 xl:grid-cols-2">
    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('slot-min-height only') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-slot-height"
            :dev="true"
            :coordinates="true"
            horizontal-padding="24rem"
            min-width="40rem"
            slot-min-height="30rem"
        >
            {{-- slot-min-height supplies the fallback canvas room for this slotted trunk. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../tw-graph/samples/documentation/idea-to-paper/00-tw-graph/01-canvas.blade.php
        </flux:field>
    </div>

    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('min-height only') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-min-height"
            :dev="true"
            :coordinates="true"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
        >
            {{-- min-height sets the visible minimum canvas height directly. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
</div>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="canvasVariant === 'props'"
    >
@endif

<div
    class="grid gap-4 xl:grid-cols-2"
    x-show="canvasPropsVariant === 'line'"
>
    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('default line') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-line-default"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
        >
            {{-- Default line-width comes from the graph defaults. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('custom line') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-line-custom"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
            color="emerald"
            line-width="0.5rem"
        >
            {{-- The trunk has no own props here; it inherits the canvas line width. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
</div>

<div
    class="grid gap-4 xl:grid-cols-2"
    x-show="canvasPropsVariant === 'stemLength'"
>
    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('default stem length') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-stem-default"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
        >
            {{-- Default stem-length comes from the graph defaults. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('custom stem length') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-stem-custom"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
            color="sky"
            stem-length="8rem"
        >
            {{-- The trunk has no own props here; it inherits the canvas stem length. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
</div>

<div
    class="grid gap-4 xl:grid-cols-2"
    x-show="canvasPropsVariant === 'nodeSize'"
>
    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('default node size') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-node-default"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
        >
            {{-- Default node-size comes from the graph defaults. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('custom node size') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-node-custom"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
            color="violet"
            node-size="1.75rem"
        >
            {{-- The trunk has no own props here; it inherits the canvas node size. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
</div>

<div
    class="grid gap-4 xl:grid-cols-2"
    x-show="canvasPropsVariant === 'capLength'"
>
    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('default cap length') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-cap-default"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
        >
            {{-- Default cap-length comes from the graph defaults. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('custom cap length') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-cap-custom"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-width="40rem"
            min-height="88rem"
            color="rose"
            cap-length="4rem"
        >
            {{-- The trunk has no own props here; it inherits the canvas cap length. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
</div>

<div
    class="grid gap-4 xl:grid-cols-2"
    x-show="canvasPropsVariant === 'minWidth'"
>
    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('default min width') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-width-default"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-height="88rem"
        >
            {{-- Default min-width follows the calculated graph bounds. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>

    <div class="min-w-0">
        <p class="px-3 pt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('custom min width') }}
        </p>
        <x-translation-workbench::ui.tw-graph
            graph-id="idea-to-paper-step-01-props-width-custom"
            :dev="true"
            :coordinates="false"
            horizontal-padding="24rem"
            min-height="88rem"
            color="cyan"
            min-width="72rem"
        >
            {{-- The trunk is unchanged; only the reserved canvas width grows. --}}
            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
</div>

@if ($renderMode === 'documentation')
    </div>
    </flux:callout>
    </section>
@endif
