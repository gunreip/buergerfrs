@php
    $canvasSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-props-line',
    );
@endphp

<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>
            {{ __('Line width') }}
        </flux:callout.heading>
        <flux:callout.text class="hyphens-auto text-justify text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('line-width is the central/common thickness used as the fallback for stems, bridges, caps, connectors, and similar line-based pieces. In the more specific strang examples this same idea may appear with contextual names such as stem-width or bridge-width.') }}
        </flux:callout.text>

        <flux:separator
            class="mt-4"
            :text="__('Deep reference links')"
        />

        <x-translation-workbench::ui.tw-graph.documentation-links example="canvas.canvas-props-line" />

        <flux:separator :text="__('Code examples')" />

        <flux:accordion
            transition
            exclusive
        >
            <flux:accordion.item expanded>
                <flux:callout
                    icon="tag"
                    color="indigo"
                >
                    <flux:accordion.heading>
                        {{ __('Line width · default') }}
                    </flux:accordion.heading>
                </flux:callout>
                <flux:accordion.content>
                    <x-translation-workbench::ui.tw-graph.code-box>
                        {{ $canvasSource->example('canvas-props-line-1') }}
                    </x-translation-workbench::ui.tw-graph.code-box>
                </flux:accordion.content>
            </flux:accordion.item>

            <flux:accordion.item>
                <flux:callout
                    icon="tag"
                    color="indigo"
                >
                    <flux:accordion.heading>
                        {{ __('Line width · custom') }}
                    </flux:accordion.heading>
                </flux:callout>
                <flux:accordion.content>
                    <x-translation-workbench::ui.tw-graph.code-box>
                        {{ $canvasSource->example('canvas-props-line-2') }}
                    </x-translation-workbench::ui.tw-graph.code-box>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>

        <flux:separator
            class="mt-4"
            :text="__('Props used in this example')"
        />

        {{-- Line Width Props --}}
        <flux:callout color="indigo">
            <flux:callout.heading icon="variable">
                {{ __('Line width props') }}
            </flux:callout.heading>
            <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
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
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>{{ __('auto id') }}</code>
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
                                    colors.graph /
                                    &#x27;zinc&#x27;</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Base semantic color inherited by child components unless they set their own color.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>line-width</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>0.25rem</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Central/common thickness for graph lines. Specific strangs may expose this as stem-width, bridge-width, cap-width, or connector-width in their own context.') }}
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
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>{{ __('calculated') }}</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Optional hard minimum width override for the graph viewport.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>min-height</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>{{ __('config / 52rem; protocol: calculated') }}</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Minimum canvas height. Handmade graphs default to config / 52rem; protocol graphs use their geometry. Content bounds and padding can require more space.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>id (strang.trunk)</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Explicit root ID of the trunk; used to derive its child component and anchor IDs.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>
    </flux:callout>

    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="square-dashed-text"
    >
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div class="grid gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <flux:heading size="sm">{{ __('Default') }}</flux:heading>
                        {{-- canvas-props-line-1:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-step-01-props-line-default"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="24rem"
                            min-width="40rem"
                            min-height="88rem"
                        >
                            {{-- Default line-width comes from the graph defaults. --}}
                            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- canvas-props-line-1:end --}}
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">{{ __('Custom') }}</flux:heading>
                        {{-- canvas-props-line-2:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-step-01-props-line-custom"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="24rem"
                            min-width="40rem"
                            min-height="88rem"
                            color="emerald"
                            line-width="0.5rem"
                        >
                            {{-- The trunk has no own props here; it inherits the canvas line width. --}}
                            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- canvas-props-line-2:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-props-line.blade.php
        </flux:field>
    </flux:callout>
</section>
