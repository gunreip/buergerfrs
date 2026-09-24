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
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Complete example: west → north') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph
    graph-id="idea-to-paper-primitives-arc-west-north"
    :dev="true"
    :coordinates="true"
    min-height="12rem"
    min-width="14rem"
    horizontal-padding="4rem"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.primitives.arc
        id="literature.primitives.arc.west-north"
        start-anchor="w"
        end-anchor="n"
        start-x="-2.75rem"
        start-y="4rem"
        end-x="0rem"
        end-y="6.75rem"
        arc-radius="2.75rem"
        color="cyan"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.primitives.line
        id="literature.primitives.arc.west-north.stem"
        direction="bottom-top"
        length="2rem"
        start-x="-2.75rem"
        start-y="2rem"
        end-x="-2.75rem"
        end-y="4rem"
        color="zinc"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.primitives.line
        id="literature.primitives.arc.west-north.bridge"
        direction="left-right"
        length="2rem"
        start-x="0rem"
        start-y="6.75rem"
        end-x="2rem"
        end-y="6.75rem"
        color="zinc"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        id="literature.primitives.arc.west-north.start.joint-arrow"
        direction="top"
        anchor-x="-2.75rem"
        anchor-y="4rem"
        color="zinc"
    /&gt;

    &lt;x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        id="literature.primitives.arc.west-north.end.joint-arrow"
        direction="right"
        anchor-x="0rem"
        anchor-y="6.75rem"
        color="zinc"
    /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Changed props for the other examples') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Only arc props that differ from the complete west-north example are listed below. Stems, bridges, and joint arrows are zinc-colored visual aids. Each preview uses its own graph-id and component IDs named after the displayed direction.') }}
        </flux:text>
        <flux:heading
            class="mt-4"
            size="sm"
        >east-north</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">start-anchor="e"
start-x="2.75rem"
color="red"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >south-west</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">start-anchor="s"
end-anchor="w"
start-x="0rem"
end-x="-2.75rem"
color="green"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >south-east</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">start-anchor="s"
end-anchor="e"
start-x="0rem"
end-x="2.75rem"
color="yellow"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >north-west</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">start-anchor="n"
end-anchor="w"
start-x="0rem"
start-y="6.75rem"
end-x="-2.75rem"
end-y="4rem"
color="fuchsia"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >north-east</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">start-anchor="n"
end-anchor="e"
start-x="0rem"
start-y="6.75rem"
end-x="2.75rem"
end-y="4rem"
color="rose"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >west-south</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">end-anchor="s"
start-y="6.75rem"
end-y="4rem"
color="blue"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >east-south</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">start-anchor="e"
end-anchor="s"
start-x="2.75rem"
start-y="6.75rem"
end-y="4rem"
color="violet"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Arc props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            element and diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-anchor
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">n</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">First arc endpoint: n,
                            e, s, or w. Use adjacent endpoints.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-anchor
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">w</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Second arc endpoint.
                            Swapping the endpoints reverses the traversal.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-x
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">X coordinate of the
                            start endpoint.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-y
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Y coordinate of the
                            start endpoint; positive values point up.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-x
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">X coordinate of the
                            end endpoint.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-y
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Y coordinate of the
                            end endpoint; positive values point up.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Inherits the canvas
                            arc-radius. Coordinates must match the chosen arc size.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Renders a dot at the
                            start endpoint.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Renders a dot at the
                            end endpoint.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-start-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional size override
                            for the start dot.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-end-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional size override
                            for the end dot.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dashed
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Renders the arc with a
                            dashed border.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Arc color.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">to-color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional color for the
                            other side of the arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Use surface for the
                            surface color variant.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional
                            stacking-order override.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Arc preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div
                    class="grid grid-cols-4 gap-4"
                    data-arc-examples
                    style="min-width: 48rem;"
                >
                    <div
                        class="min-w-0"
                        data-arc-example="west-north"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >west-north</flux:heading>
                        {{-- Arc-West-North --}}
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="east-north"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >east-north</flux:heading>
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="south-west"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >south-west</flux:heading>
                        {{-- Arc-South-West --}}
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="south-east"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >south-east</flux:heading>
                        {{-- Arc-South-East --}}
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="north-west"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >north-west</flux:heading>
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="north-east"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >north-east</flux:heading>
                        {{-- Arc-North-East --}}
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="west-south"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >west-south</flux:heading>
                        {{-- Arc-West-South --}}
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
                    </div>
                    <div
                        class="min-w-0"
                        data-arc-example="east-south"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >east-south</flux:heading>
                        {{-- Arc-East-South --}}
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
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/primitives/primitives-arc.blade.php
        </flux:field>
    </flux:callout>
</section>
