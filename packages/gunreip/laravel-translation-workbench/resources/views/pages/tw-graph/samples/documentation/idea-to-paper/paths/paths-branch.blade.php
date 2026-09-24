<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Branch path') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Two individually authored branch paths, shown one below the other. side="left" routes the bridge right-left; side="right" routes it left-right. Each connects an entry stem, an incoming arc, a bridge, an outgoing arc, and a labeled stem. The path calculates subsequent anchors from the starting coordinates and lengths.') }}
        </flux:callout.text>
        @php
            $branchExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch',
            );
            $branchLeftCode = $branchExampleSource->example('branch-left-example');
            $branchRightCode = $branchExampleSource->example('branch-right-example');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchLeftCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchRightCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Branch path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.branch
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            branch and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left branches
                            right-left; right branches left-right. Both continue upwards after the outgoing arc.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates.
                            All subsequent anchors are calculated.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">entry-stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional vertical
                            section before the incoming arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph line_length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback length used
                            when resolving path dimensions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_radius
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of both arcs.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the bridge
                            when no bridge-continuation is supplied.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:bridge-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Replaces the default
                            bridge with individually configured entries. Each accepts a length string or an array with
                            length and top/bottom labels.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback length for
                            stem entries. A length alone does not produce a visible stem; use stem-continuation with a
                            label or an explicit render flag.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical entries after
                            the outgoing arc or step. Use length and left/right labels, or force/render/spacer=true to
                            render an unlabeled stem. compressed=true uses segments.stem-compressed.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:step</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional status step
                            after the outgoing arc. Supply text or a configuration with stepLabel, beforeLength,
                            labelGap, and afterLength. A first plain labeled stem entry may supply the step endpoint
                            labels instead of an extra stem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Arc-node configuration:
                            keys 1 and 3. Other numeric keys are reported as mismatches in DEV mode. Bridge and stem
                            labels belong in their continuation entries.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the path and
                            default labels.</flux:table.cell>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the path
                            bounding box, segment diagnostics, and node counters.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Branch path preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- branch-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-branch-right-left"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-height="24rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="literature.paths.branch.right-left"
                        side="left"
                        :anchor-start="['x' => '6.75rem', 'y' => '5rem']"
                        entry-stem-length="2rem"
                        arc-radius="2.75rem"
                        bridge-length="8rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'right' => [
                                    'text' => ['Branch continues'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ],
                            ],
                        ]"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >left-right · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- branch-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-branch-left-right"
                    :dev="true"
                    :coordinates="true"
                    color="emerald"
                    min-height="24rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="literature.paths.branch.left-right"
                        side="right"
                        :anchor-start="['x' => '-6.75rem', 'y' => '5rem']"
                        entry-stem-length="4rem"
                        arc-radius="2.75rem"
                        bridge-length="12rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'left' => [
                                    'text' => ['Branch continues'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ],
                            ],
                        ]"
                        color="emerald"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-branch.blade.php
        </flux:field>
    </flux:callout>
</section>
