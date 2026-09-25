@php
    $canvasSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-coordinates',
    );
@endphp

<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

    {{-- CodeBox And Props Table --}}
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>
            {{ __('Canvas + coord') }}
        </flux:callout.heading>
        <flux:callout.text class="mt-4 hyphens-auto text-justify text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('This builds on the default trunk example. Only coordinate and dimension props are added so the same trunk can be framed without changing the trunk itself.') }}
        </flux:callout.text>

        <flux:separator
            class="mt-4"
            :text="__('Deep reference links')"
        />

        <x-translation-workbench::ui.tw-graph.documentation-links example="canvas.canvas-coordinates" />

        <flux:separator :text="__('Code examples')" />

        <x-translation-workbench::ui.tw-graph.code-box
            class="">{{ $canvasSource->example('canvas-coordinates-1') }}</x-translation-workbench::ui.tw-graph.code-box>

        <flux:separator
            class="mt-4"
            :text="__('Props used in this example')"
        />

        <flux:callout color="indigo">
            <flux:callout.heading icon="variable">
                {{ __('Props for Canvas Coordinates') }}
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
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>graph-id</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>auto id</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Stable DOM id and registry scope for anchors, bounds, DEV counters, and canvas metrics.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>:dev</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>false</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Enables DEV rendering such as node counters, debug boxes, and reduced graph opacity.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>:coordinates</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>false</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Renders coordinate guides and measurement badges when DEV mode is enabled. Preview tools control their visibility; geometry calculations run independently.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>horizontal-padding</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>12rem</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Space outside the calculated graph bounds, including labels. The coordinate origin stays anchored by this padding; min-width or a wider viewport may leave extra space on the right.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>min-width</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>{{ __('calculated') }}</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Optional hard minimum width override for the graph viewport.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>min-height</code>
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
            <div
                class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                {{-- class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1" --}}
            >
                <div class="min-w-0">
                    {{-- canvas-coordinates-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-01-coordinates"
                        :dev="true"
                        :coordinates="true"
                        horizontal-padding="6rem"
                        min-width="32rem"
                        min-height="42rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- canvas-coordinates-1:end --}}
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-coordinates.blade.php
        </flux:field>
    </flux:callout>
</section>
