<x-translation-workbench::ui.common.heading-counter-group group="primitives-node">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>
                {{ __('Node') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Four dots with different colors and sizes. The first inherits node-size from the canvas; the other three set size directly on the node primitive. A node draws only the dot, without a connector or text label.') }}
            </flux:callout.text>

            @php
                $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-node',
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
                            example="default"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            default
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->example('primitive-node-complete') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:callout.heading
                class="mt-4"
                size="sm"
                icon="asterisk"
            >
                {{ __('Changed props for the other examples') }}
            </flux:callout.heading>
            <flux:callout.text class="mt-2">
                {{ __('Only node props that differ from the complete example are listed. Each preview has its own graph-id and component ID.') }}
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
                            example="small"
                            variant="accordion"
                        >
                            0.5rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-node-small', 'primitive-node-complete', 'x-translation-workbench::ui.tw-graph.primitives.node') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="medium"
                            variant="accordion"
                        >
                            1.5rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-node-medium', 'primitive-node-complete', 'x-translation-workbench::ui.tw-graph.primitives.node') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="large"
                            variant="accordion"
                        >
                            2rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-node-large', 'primitive-node-complete', 'x-translation-workbench::ui.tw-graph.primitives.node') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Node props') }}</flux:callout.heading>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>node</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the element and diagnostics.') }}</flux:table.cell>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>size</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Dot diameter. Inherits canvas node-size when omitted.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>tone</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Use surface for the surface color variant.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}</flux:table.cell>
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
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div
                    class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2"
                    data-node-examples
                >
                    <div
                        class="min-w-0"
                        data-primitive-example="node-default"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="default"
                            size="sm"
                        >default</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-node-complete:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-node-default"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.node
                                    id="literature.primitives.node.default"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- primitive-node-complete:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="node-small"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="small"
                            size="sm"
                        >0.5rem</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-node-small:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-node-small"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.node
                                    id="literature.primitives.node.small"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    size="0.5rem"
                                    color="red"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-node-small:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="node-medium"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="medium"
                            size="sm"
                        >1.5rem</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-node-medium:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-node-medium"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.node
                                    id="literature.primitives.node.medium"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    size="1.5rem"
                                    color="green"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-node-medium:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="node-large"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="large"
                            size="sm"
                        >2rem</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-node-large:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-node-large"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.node
                                    id="literature.primitives.node.large"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    size="2rem"
                                    color="violet"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-node-large:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-node.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
