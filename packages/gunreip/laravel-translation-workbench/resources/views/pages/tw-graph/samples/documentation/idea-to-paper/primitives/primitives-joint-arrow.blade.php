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
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Complete example') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $source->example('primitive-joint-arrow-complete') }}</x-translation-workbench::ui.tw-graph.code-box>
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
        >left · canvas default</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-joint-arrow-left-default', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >top · canvas default</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-joint-arrow-top-default', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom · canvas default</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ $source->changedProps('source-joint-arrow-bottom-default', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >right · 1.75rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-right-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas').":\n".$source->changedProps('source-joint-arrow-right-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >left · 1.75rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-left-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas').":\n".$source->changedProps('source-joint-arrow-left-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >top · 1.75rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-top-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas').":\n".$source->changedProps('source-joint-arrow-top-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom · 1.75rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-2">{{ implode("\n\n", array_filter([$source->changedProps('source-joint-arrow-bottom-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph.primitives.joint-arrow'), __('Canvas').":\n".$source->changedProps('source-joint-arrow-bottom-large', 'primitive-joint-arrow-complete', 'x-translation-workbench::ui.tw-graph')])) }}</x-translation-workbench::ui.tw-graph.code-box>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">joint-arrow
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            element and diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Direction of the arrow:
                            right, left, top, or bottom.</flux:table.cell>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Use surface for the
                            surface color variant.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order
                            override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Canvas: node-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Graph default
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the transverse
                            size of the arrow. This is a canvas prop, not a joint-arrow prop.</flux:table.cell>
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
                    data-primitive-example="joint-arrow-right-default"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >right · canvas default</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-left-default"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >left · canvas default</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-top-default"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >top · canvas default</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-bottom-default"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >bottom · canvas default</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-right-large"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >right · 1.75rem</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-left-large"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >left · 1.75rem</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-top-large"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >top · 1.75rem</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-primitive-example="joint-arrow-bottom-large"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >bottom · 1.75rem</flux:heading>
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
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/primitives/primitives-joint-arrow.blade.php
        </flux:field>
    </flux:callout>
</section>
