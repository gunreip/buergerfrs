<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Connector') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Four placements, each with two lengths and gaps: the first four examples use length 2rem and gap 0.25rem; the next four use length 4rem and gap 0.75rem. The zinc dots are visual aids marking the reference points. A connector begins outside the node radius plus gap and draws only the connecting line, without a text label.') }}
        </flux:callout.text>
        @php
            $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-connector',
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Complete example') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $source->example('primitive-connector-complete') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Changed props for the other examples') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Changes are relative to the complete example. Each preview has its own graph-id and component IDs. Canvas changes are identified separately.') }}
        </flux:text>
        <flux:heading
            class="mt-4"
            size="sm"
        >left · 2rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-left-2', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >top · 2rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-top-2', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom · 2rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-bottom-2', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >right · 4rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-right-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >left · 4rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-left-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >top · 4rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-top-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom · 4rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-connector-bottom-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">connector
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            element and diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">placement
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Position relative to
                            the reference point: right, left, top, or bottom.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-x
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">X coordinate of the
                            reference point.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-y
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Y coordinate of the
                            reference point; positive values point up.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the primitive.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the connector
                            line.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">gap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Gap after the canvas
                            node radius. Inherits the canvas connector anchor gap when omitted.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-right-2"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >right · 2rem</flux:heading>
                    {{-- primitive-connector-complete:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-right-2"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.right-2"
                            placement="right"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="2rem"
                            gap="0.25rem"
                            color="cyan"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.right-2.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- primitive-connector-complete:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-left-2"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >left · 2rem</flux:heading>
                    {{-- source-connector-left-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-left-2"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.left-2"
                            placement="left"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="2rem"
                            gap="0.25rem"
                            color="red"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.left-2.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-left-2:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-top-2"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >top · 2rem</flux:heading>
                    {{-- source-connector-top-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-top-2"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.top-2"
                            placement="top"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="2rem"
                            gap="0.25rem"
                            color="green"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.top-2.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-top-2:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-bottom-2"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >bottom · 2rem</flux:heading>
                    {{-- source-connector-bottom-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-bottom-2"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.bottom-2"
                            placement="bottom"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="2rem"
                            gap="0.25rem"
                            color="violet"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.bottom-2.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-bottom-2:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-right-4"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >right · 4rem</flux:heading>
                    {{-- source-connector-right-4:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-right-4"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.right-4"
                            placement="right"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="4rem"
                            gap="0.75rem"
                            color="cyan"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.right-4.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-right-4:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-left-4"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >left · 4rem</flux:heading>
                    {{-- source-connector-left-4:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-left-4"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.left-4"
                            placement="left"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="4rem"
                            gap="0.75rem"
                            color="red"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.left-4.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-left-4:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-top-4"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >top · 4rem</flux:heading>
                    {{-- source-connector-top-4:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-top-4"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.top-4"
                            placement="top"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="4rem"
                            gap="0.75rem"
                            color="green"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.top-4.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-top-4:end --}}
                </div>
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="connector-bottom-4"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >bottom · 4rem</flux:heading>
                    {{-- source-connector-bottom-4:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-primitives-connector-bottom-4"
                        :dev="true"
                        :coordinates="true"
                        min-height="12rem"
                        min-width="14rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.primitives.connector
                            id="literature.primitives.connector.bottom-4"
                            placement="bottom"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            length="4rem"
                            gap="0.75rem"
                            color="violet"
                        />
                        <x-translation-workbench::ui.tw-graph.primitives.node
                            id="literature.primitives.connector.bottom-4.anchor"
                            anchor-x="0rem"
                            anchor-y="5rem"
                            color="zinc"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- source-connector-bottom-4:end --}}
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/primitives/primitives-connector.blade.php
        </flux:field>
    </flux:callout>
</section>
