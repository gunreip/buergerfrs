<x-translation-workbench::ui.common.heading-counter-group group="primitives-joint-arrow">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Joint Arrow') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('All four directions are shown with two sizes. The first four examples inherit the default canvas node-size; the next four set node-size to 1.75rem on each canvas. Joint arrows have no separate size prop. They mark the direction of a technical transition.') }}
            </flux:callout.text>
            @php
                $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-joint-arrow',
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
                            example="right-default"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            right · canvas default
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->example('primitive-joint-arrow-complete') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                {{ __('Changes are relative to the complete example. Each preview has its own graph-id and component IDs. Canvas changes are identified separately.') }}
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
                            example="left-default"
                            variant="accordion"
                        >
                            left · canvas default
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-joint-arrow-left-default', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-default"
                            variant="accordion"
                        >
                            top · canvas default
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-joint-arrow-top-default', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-default"
                            variant="accordion"
                        >
                            bottom · canvas default
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('source-joint-arrow-bottom-default', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-large"
                            variant="accordion"
                        >
                            right · 1.75rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-right-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas') . ":\n" . $source->changedProps('source-joint-arrow-right-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-large"
                            variant="accordion"
                        >
                            left · 1.75rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-left-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas') . ":\n" . $source->changedProps('source-joint-arrow-left-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-large"
                            variant="accordion"
                        >
                            top · 1.75rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-top-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas') . ":\n" . $source->changedProps('source-joint-arrow-top-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-large"
                            variant="accordion"
                        >
                            bottom · 1.75rem
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-bottom-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas') . ":\n" . $source->changedProps('source-joint-arrow-bottom-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Joint-arrow props') }}</flux:callout.heading>
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
                                    <code>joint-arrow</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the element and diagnostics.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Direction of the arrow: right, left, top, or bottom.') }}</flux:table.cell>
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
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('Canvas') }}: node-size</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('Graph default') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls the transverse size of the arrow. This is a canvas prop, not a joint-arrow prop.') }}
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
                    data-joint-arrow-examples
                >
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-right-default"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-default"
                            size="sm"
                        >
                            {{ __('right · canvas default') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-joint-arrow-complete:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-right-default"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.right-default"
                                    direction="right"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- primitive-joint-arrow-complete:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-left-default"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-default"
                            size="sm"
                        >
                            {{ __('left · canvas default') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-left-default:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-left-default"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.left-default"
                                    direction="left"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="red"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-left-default:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-top-default"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-default"
                            size="sm"
                        >
                            {{ __('top · canvas default') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-top-default:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-top-default"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.top-default"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="green"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-top-default:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-bottom-default"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-default"
                            size="sm"
                        >
                            {{ __('bottom · canvas default') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-bottom-default:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-bottom-default"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.bottom-default"
                                    direction="bottom"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="violet"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-bottom-default:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-right-large"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-large"
                            size="sm"
                        >
                            {{ __('right · 1.75rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-right-large:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-right-large"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                                node-size="1.75rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.right-large"
                                    direction="right"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-right-large:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-left-large"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-large"
                            size="sm"
                        >
                            {{ __('left · 1.75rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-left-large:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-left-large"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                                node-size="1.75rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.left-large"
                                    direction="left"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="red"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-left-large:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-top-large"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-large"
                            size="sm"
                        >
                            {{ __('top · 1.75rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-top-large:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-top-large"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                                node-size="1.75rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.top-large"
                                    direction="top"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="green"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-top-large:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-primitive-example="joint-arrow-bottom-large"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-large"
                            size="sm"
                        >
                            {{ __('bottom · 1.75rem') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- source-joint-arrow-bottom-large:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-primitives-joint-arrow-bottom-large"
                                :dev="true"
                                :coordinates="true"
                                min-height="12rem"
                                min-width="14rem"
                                horizontal-padding="6rem"
                                node-size="1.75rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
                                    id="literature.primitives.joint-arrow.bottom-large"
                                    direction="bottom"
                                    anchor-x="0rem"
                                    anchor-y="5rem"
                                    color="violet"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- source-joint-arrow-bottom-large:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-joint-arrow.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
