<x-translation-workbench::ui.common.heading-counter-group group="paths-branch-return-bridge">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Branch return bridge path') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Two individually authored open return bridges, shown one below the other. The zinc branch extension provides the outgoing route. Its stem endpoint is the starting anchor of the colored return: one arc followed by a horizontal bridge. The labeled endpoint remains open for a subsequent connection. side="left" returns left-right; side="right" returns right-left.') }}
            </flux:callout.text>
            @php
                $branchReturnBridgeExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return-bridge',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-bridge-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('left-right · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchReturnBridgeExampleSource->example('branch-return-bridge-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-bridge-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('right-left · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchReturnBridgeExampleSource->example('branch-return-bridge-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Branch return bridge path props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
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
                                    <code>path.branch-return-bridge</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the return bridge and its child elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('left returns left-right; right returns right-left.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starting coordinates of the arc, matching the reference extension stem endpoint in these examples.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph arc_radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Size of the incoming arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph bridge_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the horizontal section after the arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Key 1 labels the arc endpoint (default side top); key 2 labels the bridge endpoint with top/bottom entries. Unlabeled endpoints show joint arrows; labeled endpoints show dots and connectors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Color of the arc, bridge, nodes and default labels.') }}
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
        <flux:callout class="min-w-0" color="emerald">
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-bridge-left-example"
                            size="sm"
                        >{{ __('left-right · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-return-bridge-left-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-return-bridge-left-right"
                                :dev="true"
                                :coordinates="true"
                                min-height="26rem"
                                min-width="36rem"
                                horizontal-padding="14rem"
                            >
                                <x-translation-workbench::ui.tw-graph.paths.branch-extension
                                    id="literature.paths.branch-return-bridge.left-right.reference"
                                    side="left"
                                    :anchor-start="['x' => '6.75rem', 'y' => '5rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    stem-length="4rem"
                                    :end-length="null"
                                    color="zinc"
                                />
                                {{-- Reference stem ends at x=-4rem, y=11.75rem, where the return starts. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
                                    id="literature.paths.branch-return-bridge.left-right"
                                    side="left"
                                    :anchor-start="['x' => '-4rem', 'y' => '11.75rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :node-labels="[
                                        2 => [
                                            'top' => [
                                                'text' => ['Open return', 'connection'],
                                                'width' => 'half',
                                                'align' => 'center',
                                            ],
                                        ],
                                    ]"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-return-bridge-left-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-bridge-right-example"
                            size="sm"
                        >{{ __('right-left · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-return-bridge-right-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-return-bridge-right-left"
                                :dev="true"
                                :coordinates="true"
                                min-height="26rem"
                                min-width="36rem"
                                horizontal-padding="14rem"
                            >
                                <x-translation-workbench::ui.tw-graph.paths.branch-extension
                                    id="literature.paths.branch-return-bridge.right-left.reference"
                                    side="right"
                                    :anchor-start="['x' => '-6.75rem', 'y' => '5rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    stem-length="4rem"
                                    :end-length="null"
                                    color="zinc"
                                />
                                {{-- Reference stem ends at x=4rem, y=11.75rem, where the return starts. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
                                    id="literature.paths.branch-return-bridge.right-left"
                                    side="right"
                                    :anchor-start="['x' => '4rem', 'y' => '11.75rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :node-labels="[
                                        2 => [
                                            'top' => [
                                                'text' => ['Open return', 'connection'],
                                                'width' => 'half',
                                                'align' => 'center',
                                            ],
                                        ],
                                    ]"
                                    color="emerald"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-return-bridge-right-example:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/paths/paths-branch-return-bridge.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
