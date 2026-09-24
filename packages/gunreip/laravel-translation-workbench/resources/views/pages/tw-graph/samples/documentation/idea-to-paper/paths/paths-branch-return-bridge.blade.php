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
            $branchReturnBridgeLeftCode = $branchReturnBridgeExampleSource->example('branch-return-bridge-left-example');
            $branchReturnBridgeRightCode = $branchReturnBridgeExampleSource->example('branch-return-bridge-right-example');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchReturnBridgeLeftCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchReturnBridgeRightCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Branch return bridge path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.branch-return-bridge</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the return bridge and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left returns left-right; right returns right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates of the arc, matching the reference extension stem endpoint in these examples.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_radius</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of the incoming arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the horizontal section after the arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Key 1 labels the arc endpoint (default side top); key 2 labels the bridge endpoint with top/bottom entries. Unlabeled endpoints show joint arrows; labeled endpoints show dots and connectors.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the arc, bridge, nodes and default labels.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable path and segment diagnostic boxes and node counters.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Branch return bridge path preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-4"
                size="sm"
            >left-right · side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                        :dev="true"
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
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-return-bridge-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                        :dev="true"
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
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-return-bridge-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-branch-return-bridge.blade.php
        </flux:field>
    </flux:callout>
</section>
