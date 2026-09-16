<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Node') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Four dots with different colors and sizes. The first inherits node-size from the canvas; the other three set size directly on the node primitive. A node draws only the dot, without a connector or text label.') }}</flux:callout.text>
        <flux:heading class="mt-4" size="sm">{{ __('Complete example') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph
    graph-id="idea-to-paper-primitives-node-default"
    :dev="true"
    :coordinates="true"
    min-height="12rem"
    min-width="14rem"
    horizontal-padding="6rem"
&gt;
    &lt;x-translation-workbench::ui.tw-graph.primitives.node
        id="literature.primitives.node.default"
        anchor-x="0rem"
        anchor-y="5rem"
        color="cyan"
    /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Changed props for the other examples') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Changes are relative to the complete example. Each preview has its own graph-id and component IDs. Canvas changes are identified separately.') }}</flux:text>
        <flux:heading class="mt-4" size="sm">0.5rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">size="0.5rem"
color="red"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">1.5rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">size="1.5rem"
color="green"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">2rem</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-2">size="2rem"
color="violet"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">id</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">node</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Identifier for the element and diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">anchor-x</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">X coordinate of the reference point.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">anchor-y</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Y coordinate of the reference point; positive values point up.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">color</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Color of the primitive.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">size</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">null</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Dot diameter. Inherits canvas node-size when omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">tone</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">line</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Use surface for the surface color variant.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">null</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div class="grid grid-cols-4 gap-4" style="min-width: 60rem;">
                    <div class="min-w-0" data-primitive-example="node-default">
                        <flux:heading class="px-3 pt-3" size="sm">Canvas default</flux:heading>
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
                    </div>
                    <div class="min-w-0" data-primitive-example="node-small">
                        <flux:heading class="px-3 pt-3" size="sm">0.5rem</flux:heading>
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
                    </div>
                    <div class="min-w-0" data-primitive-example="node-medium">
                        <flux:heading class="px-3 pt-3" size="sm">1.5rem</flux:heading>
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
                    </div>
                    <div class="min-w-0" data-primitive-example="node-large">
                        <flux:heading class="px-3 pt-3" size="sm">2rem</flux:heading>
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
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/primitives/primitives-node.blade.php
        </flux:field>
    </flux:callout>
</section>
