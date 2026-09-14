<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Line') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Four directions with 4rem lines in the first row and 8rem lines in the second row. Each zinc joint arrow is a visual aid marking the line endpoint and direction. The endpoints are set explicitly to match the direction and length; positive Y coordinates point up.') }}
        </flux:callout.text>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Complete example: bottom-top · 4rem') }}</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>&lt;x-translation-workbench::ui.tw-graph
    graph-id="idea-to-paper-primitives-line-bottom-top-4"
    :dev="true"
    :coordinates="true"
    slot-min-height="16rem"
    min-height="16rem"
    min-width="14rem"
    horizontal-padding="6rem"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.primitives.line
        id="literature.primitives.line.bottom-top-4"
        direction="bottom-top"
        length="4rem"
        start-x="0rem"
        start-y="2rem"
        end-x="0rem"
        end-y="6rem"
        color="cyan"
    /&gt;
    &lt;x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        id="literature.primitives.line.bottom-top-4.end.joint-arrow"
        direction="top"
        anchor-x="0rem"
        anchor-y="6rem"
        color="zinc"
    /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Changed props for the other examples') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Only line props that differ from the complete bottom-top 4rem example are listed below. The joint arrows are visual aids. Each preview has its own graph-id and component IDs named after its direction and length.') }}
        </flux:text>
        <flux:heading
            class="mt-4"
            size="sm"
        >top-bottom · 4rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>direction="top-bottom"
start-y="6rem"
end-y="2rem"
color="red"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · 4rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>direction="left-right"
start-x="-2rem"
start-y="6rem"
end-x="2rem"
color="green"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · 4rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>direction="right-left"
start-x="2rem"
start-y="6rem"
end-x="-2rem"
color="yellow"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom-top · 8rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>length="8rem"
end-y="10rem"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >top-bottom · 8rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>direction="top-bottom"
length="8rem"
start-y="10rem"
end-y="2rem"
color="red"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · 8rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>direction="left-right"
length="8rem"
start-x="-4rem"
start-y="6rem"
end-x="4rem"
color="green"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · 8rem</flux:heading>
        <div class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 p-3 text-xs leading-5 text-zinc-100">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>direction="right-left"
length="8rem"
start-x="4rem"
start-y="6rem"
end-x="-4rem"
color="yellow"</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Line props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            element and diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Direction: bottom-top,
                            top-bottom, left-right, or right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">4rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Visible line length.
                            Keep the endpoint coordinates consistent with this length.</flux:table.cell>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Renders a dot at the
                            line start.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Renders a dot at the
                            line end.</flux:table.cell>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:gradient
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fades the line in
                            along its direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:cap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enables an end cap
                            unless cap-end overrides it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:cap-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enables a cap at the
                            start.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:cap-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional override for
                            the end cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1.25rem
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the cap
                            across the line.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dashed
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Renders a dashed line.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line color.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">to-color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional target color
                            for a color gradient.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:color-gradient
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enables a gradient
                            from color to to-color.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Line preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div
                    class="grid grid-cols-4 gap-4"
                    data-line-examples
                    style="min-width: 60rem;"
                >
                    <div
                        class="min-w-0"
                        data-line-example="bottom-top-4"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >bottom-top · 4rem</flux:heading>
                        {{-- Line Bottom-Top 4rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-bottom-top-4"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="top-bottom-4"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >top-bottom · 4rem</flux:heading>
                        {{-- Line Top-Bottom 4rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-top-bottom-4"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="left-right-4"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >left-right · 4rem</flux:heading>
                        {{-- Line Left-Right 4rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-left-right-4"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="right-left-4"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >right-left · 4rem</flux:heading>
                        {{-- Line Right-Left 4rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-right-left-4"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="bottom-top-8"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >bottom-top · 8rem</flux:heading>
                        {{-- Line Bottom-Top 8rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-bottom-top-8"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="top-bottom-8"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >top-bottom · 8rem</flux:heading>
                        {{-- Line Top-Bottom 8rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-top-bottom-8"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="left-right-8"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >left-right · 8rem</flux:heading>
                        {{-- Line Left-Right 8rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-left-right-8"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-line-example="right-left-8"
                    >
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >right-left · 8rem</flux:heading>
                        {{-- Line Right-Left 8rem --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-primitives-line-right-left-8"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="16rem"
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
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/primitives/primitives-line.blade.php
        </flux:field>
    </flux:callout>
</section>
