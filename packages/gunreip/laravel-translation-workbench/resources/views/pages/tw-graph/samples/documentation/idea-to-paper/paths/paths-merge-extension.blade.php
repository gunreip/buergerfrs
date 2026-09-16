<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Merge extension path') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Two individually authored merge-extension paths, shown one below the other. side="left" routes the bridge left-right; side="right" routes it right-left. Each path connects a start section, a stem, one arc, and a final bridge. The path calculates their connections from the starting anchor and lengths. Each zinc merge path is a reference: the colored extension adds another source and joins the merge where its incoming arc meets its bridge. The merge then continues through its outgoing arc.') }}
        </flux:callout.text>
        @php
            $mergeExtensionExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-merge-extension',
            );
            $mergeExtensionLeftCode = $mergeExtensionExampleSource->example('merge-extension-left-example');
            $mergeExtensionRightCode = $mergeExtensionExampleSource->example('merge-extension-right-example');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $mergeExtensionLeftCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $mergeExtensionRightCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Merge extension path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.merge-extension
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            route and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left creates a
                            left-right bridge; right creates a right-left bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates
                            for the path; subsequent anchors are calculated.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved incoming
                            arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the start
                            section.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-shift-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional vertical
                            shift after the start. Values below 1rem resolve to zero.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph line_length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback line length
                            used to resolve path dimensions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of the arc before the bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the final horizontal bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph stem_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the first vertical stem before the arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional vertical stems; entries accept a length string or an array with a length key.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Start label via start (default: Merge extension / start). Numeric keys label the following anchors: 1 after start, 2 after stem, 3 after arc, and 4 after bridge. Each stem continuation shifts the arc and bridge numbers by one.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color inherited from
                            the parent when omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order
                            override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enables DEV output for
                            the path segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:show-dev-box
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Includes the path
                            bounding box when DEV output is enabled.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Merge extension path preview') }}</flux:callout.heading>
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
                {{-- merge-extension-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-merge-extension-left-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-height="28rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    {{-- Reference merge: its incoming arc ends at x=6rem, y=14.25rem. --}}
                    <x-translation-workbench::ui.tw-graph.paths.merge
                        id="literature.paths.merge-extension.left-right.reference"
                        side="left"
                        :anchor-start="['x' => '3.25rem', 'y' => '5rem']"
                        start-length="2.5rem"
                        :stem-lengths="[1 => '4rem']"
                        arc-size="2.75rem"
                        bridge-length="4rem"
                        :start-label="['text' => ['Merge reference'], 'width' => 'half']"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.merge-extension
                        id="literature.paths.merge-extension.left-right"
                        side="left"
                        :anchor-start="['x' => '-4.75rem', 'y' => '5rem']"
                        start-length="2.5rem"
                        stem-length="4rem"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        :node-labels="[
                            'start' => ['text' => ['Merge extension start'], 'width' => 'half'],
                            4 => [
                                'top' => [
                                    'text' => ['Extension joins', 'merge path'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ],
                            ],
                        ]"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-extension-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- merge-extension-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-merge-extension-right-left"
                    :dev="true"
                    :coordinates="true"
                    color="emerald"
                    min-height="28rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    {{-- Reference merge: its incoming arc ends at x=2rem, y=16.25rem. --}}
                    <x-translation-workbench::ui.tw-graph.paths.merge
                        id="literature.paths.merge-extension.right-left.reference"
                        side="right"
                        :anchor-start="['x' => '4.75rem', 'y' => '5rem']"
                        start-length="4.5rem"
                        :stem-lengths="[1 => '4rem']"
                        arc-size="2.75rem"
                        bridge-length="4rem"
                        :start-label="['text' => ['Merge reference'], 'width' => 'half']"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.merge-extension
                        id="literature.paths.merge-extension.right-left"
                        side="right"
                        :anchor-start="['x' => '14.75rem', 'y' => '5rem']"
                        start-length="4.5rem"
                        stem-length="4rem"
                        arc-size="2.75rem"
                        bridge-length="10rem"
                        :node-labels="[
                            'start' => ['text' => ['Merge extension start'], 'width' => 'half'],
                            4 => [
                                'top' => [
                                    'text' => ['Extension joins', 'merge path'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ],
                            ],
                        ]"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-extension-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-merge-extension.blade.php
        </flux:field>
    </flux:callout>
</section>
