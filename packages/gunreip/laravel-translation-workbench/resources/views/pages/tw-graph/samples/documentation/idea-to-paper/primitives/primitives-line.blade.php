<x-translation-workbench::ui.common.heading-counter-group group="primitives-line">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>
                {{ __('Line') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Four directions with 4rem and 8rem lines, followed by additional bottom-top 4rem endpoint combinations. Each zinc joint arrow is a visual aid marking the line endpoint and direction. The endpoints are set explicitly to match the direction and length; positive Y coordinates point up.') }}
            </flux:callout.text>

            @php
                $lineSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line',
                );
            @endphp

            {{-- <flux:separator
            class="mt-4"
            :text="__('Deep reference links')"
        /> --}}

            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />

            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-top-4"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            {{ __('bottom-top · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $lineSource->example('line-complete-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:callout.heading
                class="mt-4"
                size="sm"
                icon="square-pen"
            >
                {{ __('Changed props for the other examples') }}
            </flux:callout.heading>
            <flux:callout.text class="mt-2">
                {{ __('Only line props that differ from the complete bottom-top 4rem example are listed below. The joint arrows are visual aids. Each preview has its own graph-id and component IDs named after its direction and length.') }}
            </flux:callout.text>

            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-bottom-4"
                            variant="accordion"
                        >
                            {{ __('top-bottom · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-top-bottom-4', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-right-4"
                            variant="accordion"
                        >
                            {{ __('left-right · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-left-right-4', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-left-4"
                            variant="accordion"
                        >
                            {{ __('right-left · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-right-left-4', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-top-8"
                            variant="accordion"
                        >
                            {{ __('bottom-top · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-bottom-top-8', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-bottom-8"
                            variant="accordion"
                        >
                            {{ __('top-bottom · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-top-bottom-8', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-right-8"
                            variant="accordion"
                        >
                            {{ __('left-right · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-left-right-8', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-left-8"
                            variant="accordion"
                        >
                            {{ __('right-left · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->changedProps('source-line-right-left-8', 'line-complete-example', 'x-translation-workbench::ui.tw-graph.primitives.line') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

            </flux:accordion>

            <flux:callout.heading
                class="mt-4"
                size="sm"
                icon="square-pen"
            >
                {{ __('Endpoint variants · bottom-top · 4rem') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-4 mt-2">
                {{ __('The complete example above already shows joint-arrow end. These eight additions keep the same length and coordinates. node-start/node-end render Dots in the line color. Joint-arrows are separate primitives in zinc, placed at the matching start/end coordinates; both point along the line\'s direction. primitives.line itself has no joint-arrow-start or joint-arrow-end prop.') }}
            </flux:callout.text>

            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="no-markers"
                            variant="accordion"
                        >
                            {{ __('Line only') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-plain') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="joint-arrow-start"
                            variant="accordion"
                        >
                            {{ __('Joint-arrow start') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-arrow-start') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="joint-arrows-both"
                            variant="accordion"
                        >
                            {{ __('Joint-arrow start + end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-arrows-both') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dot-start"
                            variant="accordion"
                        >
                            {{ __('Dot start') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-dot-start') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dot-end"
                            variant="accordion"
                        >
                            {{ __('Dot end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-dot-end') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dots-both"
                            variant="accordion"
                        >
                            {{ __('Dot start + end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-dots-both') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dot-start-joint-arrow-end"
                            variant="accordion"
                        >
                            {{ __('Dot start + joint-arrow end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-dot-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="joint-arrow-start-dot-end"
                            variant="accordion"
                        >
                            {{ __('Joint-arrow start + dot end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $lineSource->example('source-line-arrow-dot') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:separator
                class="mt-4"
                text="Props used in this examples"
            />

            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">
                    {{ __('Line props') }}
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the element and diagnostics.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Direction: bottom-top, top-bottom, left-right, or right-left.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Visible line length. Keep the endpoint coordinates consistent with this length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-x</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('X coordinate of the start endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-y</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Y coordinate of the start endpoint; positive values point up.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-x</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('X coordinate of the end endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-y</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Y coordinate of the end endpoint; positive values point up.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Renders a dot at the line start.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Renders a dot at the line end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-start-size</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional size override for the start dot.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-end-size</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional size override for the end dot.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:gradient</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fades the line in along its direction.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:cap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enables an end cap unless cap-end overrides it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:cap-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enables a cap at the start.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:cap-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional override for the end cap.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1.25rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the cap across the line.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:dashed</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Renders a dashed line.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>to-color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional target color for a color gradient.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:color-gradient</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enables a gradient from color to to-color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>tone</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Use surface for the surface color variant.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="square-dashed-text">{{ __('Primitives line preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div
                    class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2"
                    data-line-examples
                >
                    <div
                        class="min-w-0"
                        data-line-example="bottom-top-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-top-4"
                            size="sm"
                        >
                            {{ __('bottom-top · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Bottom-Top 4rem --}}
                            {{-- line-complete-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-bottom-top-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.bottom-top-4"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    color="cyan"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.bottom-top-4.end.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- line-complete-example:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="top-bottom-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-bottom-4"
                            size="sm"
                        >
                            {{ __('top-bottom · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Top-Bottom 4rem --}}
                            {{-- source-line-top-bottom-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-top-bottom-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.top-bottom-4"
                                    direction="top-bottom"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="6rem"
                                    end-x="0rem"
                                    end-y="2rem"
                                    color="red"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.top-bottom-4.end.joint-arrow"
                                    direction="bottom"
                                    anchor-x="0rem"
                                    anchor-y="2rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-top-bottom-4:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="left-right-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-right-4"
                            size="sm"
                        >
                            {{ __('left-right · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Left-Right 4rem --}}
                            {{-- source-line-left-right-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-left-right-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.left-right-4"
                                    direction="left-right"
                                    length="4rem"
                                    start-x="-2rem"
                                    start-y="6rem"
                                    end-x="2rem"
                                    end-y="6rem"
                                    color="green"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.left-right-4.end.joint-arrow"
                                    direction="right"
                                    anchor-x="2rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-left-right-4:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="right-left-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-left-4"
                            size="sm"
                        >
                            {{ __('right-left · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Right-Left 4rem --}}
                            {{-- source-line-right-left-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-right-left-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.right-left-4"
                                    direction="right-left"
                                    length="4rem"
                                    start-x="2rem"
                                    start-y="6rem"
                                    end-x="-2rem"
                                    end-y="6rem"
                                    color="yellow"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.right-left-4.end.joint-arrow"
                                    direction="left"
                                    anchor-x="-2rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-right-left-4:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="bottom-top-8"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-top-8"
                            size="sm"
                        >
                            {{ __('bottom-top · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Bottom-Top 8rem --}}
                            {{-- source-line-bottom-top-8:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-bottom-top-8"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.bottom-top-8"
                                    direction="bottom-top"
                                    length="8rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="10rem"
                                    color="cyan"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.bottom-top-8.end.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="10rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-bottom-top-8:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="top-bottom-8"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-bottom-8"
                            size="sm"
                        >
                            {{ __('top-bottom · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Top-Bottom 8rem --}}
                            {{-- source-line-top-bottom-8:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-top-bottom-8"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.top-bottom-8"
                                    direction="top-bottom"
                                    length="8rem"
                                    start-x="0rem"
                                    start-y="10rem"
                                    end-x="0rem"
                                    end-y="2rem"
                                    color="red"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.top-bottom-8.end.joint-arrow"
                                    direction="bottom"
                                    anchor-x="0rem"
                                    anchor-y="2rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-top-bottom-8:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="left-right-8"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-right-8"
                            size="sm"
                        >
                            {{ __('left-right · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Left-Right 8rem --}}
                            {{-- source-line-left-right-8:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-left-right-8"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.left-right-8"
                                    direction="left-right"
                                    length="8rem"
                                    start-x="-4rem"
                                    start-y="6rem"
                                    end-x="4rem"
                                    end-y="6rem"
                                    color="green"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.left-right-8.end.joint-arrow"
                                    direction="right"
                                    anchor-x="4rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-left-right-8:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="right-left-8"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-left-8"
                            size="sm"
                        >
                            {{ __('right-left · 8rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Line Right-Left 8rem --}}
                            {{-- source-line-right-left-8:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-right-left-8"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.right-left-8"
                                    direction="right-left"
                                    length="8rem"
                                    start-x="4rem"
                                    start-y="6rem"
                                    end-x="-4rem"
                                    end-y="6rem"
                                    color="yellow"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.right-left-8.end.joint-arrow"
                                    direction="left"
                                    anchor-x="-4rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-right-left-8:end --}}
                        </div>
                    </div>
                </div>

                <flux:callout.heading
                    class="mt-6"
                    size="sm"
                    icon="package-plus"
                >
                    {{ __('Additional endpoints · bottom-top · 4rem') }}</flux:callout.heading>
                <flux:callout.text class="mb-4 mt-2">
                    {{ __('Compare with bottom-top · 4rem above, which already shows
                                                                                                                                                                                                                                                                                        joint-arrow end. Dots follow the cyan line color; zinc arrows point upward at either endpoint.') }}
                </flux:callout.text>

                <div
                    class="grid grid-cols-2 gap-4"
                    data-line-endpoint-examples
                >
                    <div
                        class="min-w-0"
                        data-line-endpoints="plain"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="no-markers"
                            size="sm"
                        >
                            {{ __('Line only') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-plain:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-plain"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-plain-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.plain"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    color="cyan"
                                />
                                {{-- line-plain-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-plain:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="arrow-start"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="joint-arrow-start"
                            size="sm"
                        >
                            {{ __('Joint-arrow start') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-arrow-start:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-arrow-start"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-arrow-start-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.arrow-start"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    color="cyan"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.endpoints.arrow-start.start.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="2rem"
                                    color="zinc"
                                />
                                {{-- line-arrow-start-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-arrow-start:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="arrows-both"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="joint-arrows-both"
                            size="sm"
                        >
                            {{ __('Joint-arrow start + end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-arrows-both:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-arrows-both"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-arrows-both-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.arrows-both"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    color="cyan"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.endpoints.arrows-both.start.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="2rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.endpoints.arrows-both.end.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                                {{-- line-arrows-both-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-arrows-both:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="dot-start"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dot-start"
                            size="sm"
                        >
                            {{ __('Dot start') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-dot-start:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-dot-start"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-dot-start-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.dot-start"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    :node-start="true"
                                    color="cyan"
                                />
                                {{-- line-dot-start-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-dot-start:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="dot-end"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dot-end"
                            size="sm"
                        >
                            {{ __('Dot end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-dot-end:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-dot-end"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-dot-end-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.dot-end"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    :node-end="true"
                                    color="cyan"
                                />
                                {{-- line-dot-end-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-dot-end:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="dots-both"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dots-both"
                            size="sm"
                        >
                            {{ __('Dot start + end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-dots-both:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-dots-both"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-dots-both-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.dots-both"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    :node-start="true"
                                    :node-end="true"
                                    color="cyan"
                                />
                                {{-- line-dots-both-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-dots-both:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="dot-arrow"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="dot-start-joint-arrow-end"
                            size="sm"
                        >
                            {{ __('Dot start + joint-arrow end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-dot-arrow:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-dot-arrow"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-dot-arrow-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.dot-arrow"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    :node-start="true"
                                    color="cyan"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.endpoints.dot-arrow.end.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="6rem"
                                    color="zinc"
                                />
                                {{-- line-dot-arrow-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-dot-arrow:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-endpoints="arrow-dot"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="joint-arrow-start-dot-end"
                            size="sm"
                        >
                            {{ __('Joint-arrow start + dot end') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-line-arrow-dot:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-line-arrow-dot"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                {{-- line-arrow-dot-example:start --}}
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line.endpoints.arrow-dot"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="2rem"
                                    end-x="0rem"
                                    end-y="6rem"
                                    :node-end="true"
                                    color="cyan"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.line.endpoints.arrow-dot.start.joint-arrow"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="2rem"
                                    color="zinc"
                                />
                                {{-- line-arrow-dot-example:end --}}
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-line-arrow-dot:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-line.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
