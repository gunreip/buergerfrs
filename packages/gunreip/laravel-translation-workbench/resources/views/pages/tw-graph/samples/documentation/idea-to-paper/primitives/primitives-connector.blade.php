<x-translation-workbench::ui.common.heading-counter-group group="primitives-connector">
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
                            example="right-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            right · 2rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->example('primitive-connector-complete') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:callout.heading
                class="mt-4"
                size="sm"
            >{{ __('Changed props for the other examples') }}</flux:callout.heading>
            <flux:callout.text class="mt-2">
                {{ __('Only connector props that differ from the complete example are listed. Each preview has its own graph-id and component IDs. The zinc reference dots are visual aids.') }}
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
                            example="left-2"
                            variant="accordion"
                        >
                            left · 2rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-left-2', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-2"
                            variant="accordion"
                        >
                            top · 2rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-top-2', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-2"
                            variant="accordion"
                        >
                            bottom · 2rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-bottom-2', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-4"
                            variant="accordion"
                        >
                            right · 4rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-right-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-4"
                            variant="accordion"
                        >
                            left · 4rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-left-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-4"
                            variant="accordion"
                        >
                            top · 4rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-top-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-4"
                            variant="accordion"
                        >
                            bottom · 4rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-connector-bottom-4', 'primitive-connector-complete', 'x-translation-workbench::ui.tw-graph.primitives.connector') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Connector props') }}</flux:callout.heading>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>connector</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the element and diagnostics.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>placement</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Position relative to the reference point: right, left, top, or bottom.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-x</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('X coordinate of the reference point.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-y</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Y coordinate of the reference point; positive values point up.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Color of the primitive.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the connector line.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>gap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Gap after the canvas node radius. Inherits the canvas connector anchor gap when omitted.') }}
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
            <flux:callout.heading icon="eye">
                {{ __('Preview') }}
            </flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div
                    class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2"
                    data-connector-examples
                >
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-right-2"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-2"
                            size="sm"
                        >
                            {{ __('right · 2rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-connector-complete:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-right-2"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-left-2"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-2"
                            size="sm"
                        >
                            {{ __('left · 2rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-left-2:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-left-2"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-top-2"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-2"
                            size="sm"
                        >
                            {{ __('top · 2rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-top-2:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-top-2"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-bottom-2"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-2"
                            size="sm"
                        >
                            {{ __('bottom · 2rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-bottom-2:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-bottom-2"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-right-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-4"
                            size="sm"
                        >
                            {{ __('right · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-right-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-right-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-left-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-4"
                            size="sm"
                        >
                            {{ __('left · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-left-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-left-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-top-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-4"
                            size="sm"
                        >
                            {{ __('top · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-top-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-top-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="connector-bottom-4"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-4"
                            size="sm"
                        >
                            {{ __('bottom · 4rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-connector-bottom-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-connector-bottom-4"
                                :dev="true"
                                :coordinates="true"
                                min-height="16rem"
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
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-connector.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
