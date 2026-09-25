<x-translation-workbench::ui.common.heading-counter-group group="primitives-arc">
    @php
        $arcSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
            'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-arc',
        );
    @endphp
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Arc') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Four corners, each in both directions. The arc uses start-anchor and end-anchor; line and joint-arrow use direction. Each example contains a stem, a bridge, and a joint arrow at both arc endpoints.') }}
            </flux:callout.text>
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
                            example="west-north"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            {{ __('west-north') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $arcSource->example('source-arc-west-north') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:callout.heading
                class="mt-4"
                size="sm"
            >{{ __('Changed props for the other examples') }}</flux:callout.heading>
            <flux:callout.text class="mt-2">
                {{ __('Only arc props that differ from the complete west-north example are listed below. Stems, bridges, and joint arrows are zinc-colored visual aids. Each preview uses its own graph-id and component IDs named after the displayed direction.') }}
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
                            example="east-north"
                            variant="accordion"
                        >
                            {{ __('east-north') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-east-north', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="south-west"
                            variant="accordion"
                        >
                            {{ __('south-west') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-south-west', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="south-east"
                            variant="accordion"
                        >
                            {{ __('south-east') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-south-east', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="north-west"
                            variant="accordion"
                        >
                            {{ __('north-west') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-north-west', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="north-east"
                            variant="accordion"
                        >
                            {{ __('north-east') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-north-east', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="west-south"
                            variant="accordion"
                        >
                            {{ __('west-south') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-west-south', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="east-south"
                            variant="accordion"
                        >
                            {{ __('east-south') }}
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-2">{{ $arcSource->changedProps('source-arc-east-south', 'source-arc-west-north', 'x-translation-workbench::ui.tw-graph.primitives.arc') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />

            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">
                    {{ __('Arc props') }}
                </flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns sticky>
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
                                    <code>arc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the element and diagnostics.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-anchor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>n</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('First arc endpoint: n, e, s, or w. Use adjacent endpoints.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-anchor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>w</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Second arc endpoint. Swapping the endpoints reverses the traversal.') }}
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
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Inherits the canvas arc-radius. Coordinates must match the chosen arc size.') }}
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
                                    {{ __('Renders a dot at the start endpoint.') }}
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
                                    {{ __('Renders a dot at the end endpoint.') }}
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
                                    <code>:dashed</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Renders the arc with a dashed border.') }}
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
                                    {{ __('Arc color.') }}
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
                                    {{ __('Optional color for the other side of the arc.') }}
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
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Arc preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div
                    class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2"
                    data-arc-examples
                    style="min-width: 50rem;"
                >
                    <div
                        class="min-w-0"
                        data-arc-example="west-north"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="west-north"
                            size="sm"
                        >
                            {{ __('west-north') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Arc-West-North --}}
                            {{-- source-arc-west-north:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-west-north"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.west-north"
                                    start-anchor="w"
                                    end-anchor="n"
                                    start-x="-2.75rem"
                                    start-y="4rem"
                                    end-x="0rem"
                                    end-y="6.75rem"
                                    arc-radius="2.75rem"
                                    color="cyan"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.west-north.stem"
                                    direction="bottom-top"
                                    length="2rem"
                                    start-x="-2.75rem"
                                    start-y="2rem"
                                    end-x="-2.75rem"
                                    end-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.west-north.bridge"
                                    direction="left-right"
                                    length="2rem"
                                    start-x="0rem"
                                    start-y="6.75rem"
                                    end-x="2rem"
                                    end-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.west-north.start.joint-arrow"
                                    direction="top"
                                    anchor-x="-2.75rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.west-north.end.joint-arrow"
                                    direction="right"
                                    anchor-x="0rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-west-north:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="east-north"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="east-north"
                            size="sm"
                        >
                            {{ __('east-north') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-arc-east-north:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-east-north"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.east-north"
                                    start-anchor="e"
                                    end-anchor="n"
                                    start-x="2.75rem"
                                    start-y="4rem"
                                    end-x="0rem"
                                    end-y="6.75rem"
                                    arc-radius="2.75rem"
                                    color="red"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.east-north.stem"
                                    direction="bottom-top"
                                    length="2rem"
                                    start-x="2.75rem"
                                    start-y="2rem"
                                    end-x="2.75rem"
                                    end-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.east-north.bridge"
                                    direction="right-left"
                                    length="2rem"
                                    start-x="0rem"
                                    start-y="6.75rem"
                                    end-x="-2rem"
                                    end-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.east-north.start.joint-arrow"
                                    direction="top"
                                    anchor-x="2.75rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.east-north.end.joint-arrow"
                                    direction="left"
                                    anchor-x="0rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-east-north:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="south-west"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="south-west"
                            size="sm"
                        >
                            {{ __('south-west') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Arc-South-West --}}
                            {{-- source-arc-south-west:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-south-west"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.south-west"
                                    start-anchor="s"
                                    end-anchor="w"
                                    start-x="0rem"
                                    start-y="4rem"
                                    end-x="-2.75rem"
                                    end-y="6.75rem"
                                    arc-radius="2.75rem"
                                    color="green"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.south-west.stem"
                                    direction="bottom-top"
                                    length="2rem"
                                    start-x="-2.75rem"
                                    start-y="6.75rem"
                                    end-x="-2.75rem"
                                    end-y="8.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.south-west.bridge"
                                    direction="right-left"
                                    length="2rem"
                                    start-x="2rem"
                                    start-y="4rem"
                                    end-x="0rem"
                                    end-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.south-west.start.joint-arrow"
                                    direction="left"
                                    anchor-x="0rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.south-west.end.joint-arrow"
                                    direction="top"
                                    anchor-x="-2.75rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-south-west:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="south-east"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="south-east"
                            size="sm"
                        >
                            {{ __('south-east') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Arc-South-East --}}
                            {{-- source-arc-south-east:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-south-east"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.south-east"
                                    start-anchor="s"
                                    end-anchor="e"
                                    start-x="0rem"
                                    start-y="4rem"
                                    end-x="2.75rem"
                                    end-y="6.75rem"
                                    arc-radius="2.75rem"
                                    color="yellow"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.south-east.stem"
                                    direction="bottom-top"
                                    length="2rem"
                                    start-x="2.75rem"
                                    start-y="6.75rem"
                                    end-x="2.75rem"
                                    end-y="8.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.south-east.bridge"
                                    direction="left-right"
                                    length="2rem"
                                    start-x="-2rem"
                                    start-y="4rem"
                                    end-x="0rem"
                                    end-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.south-east.start.joint-arrow"
                                    direction="right"
                                    anchor-x="0rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.south-east.end.joint-arrow"
                                    direction="top"
                                    anchor-x="2.75rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-south-east:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="north-west"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="north-west"
                            size="sm"
                        >
                            {{ __('north-west') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-arc-north-west:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-north-west"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.north-west"
                                    start-anchor="n"
                                    end-anchor="w"
                                    start-x="0rem"
                                    start-y="6.75rem"
                                    end-x="-2.75rem"
                                    end-y="4rem"
                                    arc-radius="2.75rem"
                                    color="fuchsia"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.north-west.stem"
                                    direction="top-bottom"
                                    length="2rem"
                                    start-x="-2.75rem"
                                    start-y="4rem"
                                    end-x="-2.75rem"
                                    end-y="2rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.north-west.bridge"
                                    direction="right-left"
                                    length="2rem"
                                    start-x="2rem"
                                    start-y="6.75rem"
                                    end-x="0rem"
                                    end-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.north-west.start.joint-arrow"
                                    direction="left"
                                    anchor-x="0rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.north-west.end.joint-arrow"
                                    direction="bottom"
                                    anchor-x="-2.75rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-north-west:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="north-east"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="north-east"
                            size="sm"
                        >
                            {{ __('north-east') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Arc-North-East --}}
                            {{-- source-arc-north-east:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-north-east"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.north-east"
                                    start-anchor="n"
                                    end-anchor="e"
                                    start-x="0rem"
                                    start-y="6.75rem"
                                    end-x="2.75rem"
                                    end-y="4rem"
                                    arc-radius="2.75rem"
                                    color="rose"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.north-east.stem"
                                    direction="top-bottom"
                                    length="2rem"
                                    start-x="2.75rem"
                                    start-y="4rem"
                                    end-x="2.75rem"
                                    end-y="2rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.north-east.bridge"
                                    direction="left-right"
                                    length="2rem"
                                    start-x="-2rem"
                                    start-y="6.75rem"
                                    end-x="0rem"
                                    end-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.north-east.start.joint-arrow"
                                    direction="right"
                                    anchor-x="0rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.north-east.end.joint-arrow"
                                    direction="bottom"
                                    anchor-x="2.75rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-north-east:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="west-south"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="west-south"
                            size="sm"
                        >
                            {{ __('west-south') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Arc-West-South --}}
                            {{-- source-arc-west-south:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-west-south"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.west-south"
                                    start-anchor="w"
                                    end-anchor="s"
                                    start-x="-2.75rem"
                                    start-y="6.75rem"
                                    end-x="0rem"
                                    end-y="4rem"
                                    arc-radius="2.75rem"
                                    color="blue"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.west-south.stem"
                                    direction="top-bottom"
                                    length="2rem"
                                    start-x="-2.75rem"
                                    start-y="8.75rem"
                                    end-x="-2.75rem"
                                    end-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.west-south.bridge"
                                    direction="left-right"
                                    length="2rem"
                                    start-x="0rem"
                                    start-y="4rem"
                                    end-x="2rem"
                                    end-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.west-south.start.joint-arrow"
                                    direction="bottom"
                                    anchor-x="-2.75rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.west-south.end.joint-arrow"
                                    direction="right"
                                    anchor-x="0rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-west-south:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="east-south"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="east-south"
                            size="sm"
                        >
                            {{ __('east-south') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- Arc-East-South --}}
                            {{-- source-arc-east-south:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-arc-east-south"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="4rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.arc
                                    id="literature.primitives.arc.east-south"
                                    start-anchor="e"
                                    end-anchor="s"
                                    start-x="2.75rem"
                                    start-y="6.75rem"
                                    end-x="0rem"
                                    end-y="4rem"
                                    arc-radius="2.75rem"
                                    color="violet"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.east-south.stem"
                                    direction="top-bottom"
                                    length="2rem"
                                    start-x="2.75rem"
                                    start-y="8.75rem"
                                    end-x="2.75rem"
                                    end-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.arc.east-south.bridge"
                                    direction="right-left"
                                    length="2rem"
                                    start-x="0rem"
                                    start-y="4rem"
                                    end-x="-2rem"
                                    end-y="4rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.east-south.start.joint-arrow"
                                    direction="bottom"
                                    anchor-x="2.75rem"
                                    anchor-y="6.75rem"
                                    color="zinc"
                                />

                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.arc.east-south.end.joint-arrow"
                                    direction="left"
                                    anchor-x="0rem"
                                    anchor-y="4rem"
                                    color="zinc"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-arc-east-south:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-arc.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
