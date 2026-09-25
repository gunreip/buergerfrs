@php
    $canvasSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-node-size',
    );
@endphp

<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>
            {{ __('Node size') }}
        </flux:callout.heading>
        <flux:callout.text class="hyphens-auto text-justify text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('node-size controls the visible dot diameter and joint-arrow size. Labels at anchors 1 and 2 make these anchors render as dots; the other transitions remain joint arrows. Both marker types inherit their size from the canvas.') }}
        </flux:callout.text>

        <flux:separator
            class="mt-4"
            :text="__('Deep reference links')"
        />

        <x-translation-workbench::ui.tw-graph.documentation-links example="canvas.canvas-props-node-size" />

        <flux:separator :text="__('Code examples')" />

        <flux:accordion
            transition
            exclusive
        >
            <flux:accordion.item expanded>
                <flux:callout
                    icon="code"
                    color="indigo"
                >
                    <flux:accordion.heading>
                        {{ __('Node size · default') }}
                    </flux:accordion.heading>
                </flux:callout>
                <flux:accordion.content>
                    <x-translation-workbench::ui.tw-graph.code-box>
                        {{ $canvasSource->example('canvas-props-node-size-1') }}
                    </x-translation-workbench::ui.tw-graph.code-box>
                </flux:accordion.content>
            </flux:accordion.item>

            <flux:accordion.item>
                <flux:callout
                    icon="code"
                    color="indigo"
                >
                    <flux:accordion.heading>
                        {{ __('Node size · custom') }}
                    </flux:accordion.heading>
                </flux:callout>
                <flux:accordion.content>
                    <x-translation-workbench::ui.tw-graph.code-box>{{ $canvasSource->example('canvas-props-node-size-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>

        <flux:separator
            class="mt-4"
            :text="__('Props used in this example')"
        />

        {{-- Node Size Props --}}
        <flux:callout color="indigo">
            <flux:callout.heading icon="variable">
                {{ __('Node size props') }}
            </flux:callout.heading>

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column>{{ __('Prop') }}</flux:table.column>
                        <flux:table.column>{{ __('Default') }}</flux:table.column>
                        <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>graph-id</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>auto id</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Stable DOM id and registry scope for anchors, bounds, DEV counters, and canvas metrics.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>:dev</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>false</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Enables DEV rendering such as node counters, debug boxes, and reduced graph opacity.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>:coordinates</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>false</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Renders coordinate guides and measurement badges when DEV mode is enabled. Preview tools control their visibility; geometry calculations run independently.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>color</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>config
                                    colors.graph / &#x27;zinc&#x27;</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Base semantic color inherited by child components unless they set their own color.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>node-size</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>0.95rem</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Diameter of visible anchor dots and transverse size of joint arrows.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>horizontal-padding</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>12rem</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Space outside the calculated graph bounds, including labels. The coordinate origin stays anchored by this padding; min-width or a wider viewport may leave extra space on the right.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>min-width</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>{{ __('calculated') }}</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Optional hard minimum width override for the graph viewport.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>min-height</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>{{ __('config / 52rem; protocol: calculated') }}</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Minimum canvas height. Handmade graphs default to config / 52rem; protocol graphs use their geometry. Content bounds and padding can require more space.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>id
                                    (strang.trunk)</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Explicit root ID of the trunk; used to derive its child component and anchor IDs.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>:node-labels
                                    (strang.trunk)</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Labels at anchors 1 and 2 make those anchors render as dots. Each entry selects left or right and supplies text, width and align; node-size still comes from the canvas.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>
    </flux:callout>

    <flux:callout
        class="min-w-0"
        color="emerald"
    >
        <flux:callout.heading icon="square-dashed-text">
            {{ __('Preview') }}
        </flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                <div class="min-w-0">
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >{{ __('Default') }}</flux:heading>
                    <div
                        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- canvas-props-node-size-1:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-step-01-props-node-default"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-width="20rem"
                            min-height="88rem"
                        >
                            {{-- Default node-size comes from the graph defaults. --}}
                            <x-translation-workbench::ui.tw-graph.strang.trunk
                                id="literature.center.1.paper"
                                :node-labels="[
                                    1 => [
                                        'right' => [
                                            'text' => [__('Anchor 1')],
                                            'width' => 'half',
                                            'align' => 'left',
                                        ],
                                    ],
                                    2 => [
                                        'left' => [
                                            'text' => [__('Anchor 2')],
                                            'width' => 'half',
                                            'align' => 'right',
                                        ],
                                    ],
                                ]"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- canvas-props-node-size-1:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >{{ __('Custom') }}</flux:heading>
                    <div
                        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- canvas-props-node-size-2:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-step-01-props-node-custom"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="6rem"
                            min-width="20rem"
                            min-height="88rem"
                            color="violet"
                            node-size="1.75rem"
                        >
                            {{-- Both labeled anchors render dots; their size is inherited from the canvas. --}}
                            <x-translation-workbench::ui.tw-graph.strang.trunk
                                id="literature.center.1.paper"
                                :node-labels="[
                                    1 => [
                                        'right' => [
                                            'text' => [__('Anchor 1')],
                                            'width' => 'half',
                                            'align' => 'left',
                                        ],
                                    ],
                                    2 => [
                                        'left' => [
                                            'text' => [__('Anchor 2')],
                                            'width' => 'half',
                                            'align' => 'right',
                                        ],
                                    ],
                                ]"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- canvas-props-node-size-2:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-props-node-size.blade.php
        </flux:field>
    </flux:callout>
</section>
