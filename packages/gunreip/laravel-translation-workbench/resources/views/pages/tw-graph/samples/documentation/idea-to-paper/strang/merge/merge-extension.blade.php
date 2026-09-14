<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Merge extension') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Merge extensions continue an existing merge side outward. Use them when multiple sources belong to the same merge relation but should still remain visually inspectable.') }}
        </flux:callout.text>
        @php
            $mergeExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-extension',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- merge-extension-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- merge-extension-example-1:end --\}\}/ms',
                    $mergeExampleSource,
                    $mergeExample1Match,
                ) !== 1
            ) {
                throw new \LogicException('Missing merge-extension-example-1 source markers.');
            }
            $mergeExample1Lines = explode("\n", rtrim($mergeExample1Match[1]));
            $mergeExample1Indent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($mergeExample1Lines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $mergeExample1Code = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $mergeExample1Indent), $mergeExample1Lines),
            );
        @endphp
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $mergeExample1Code }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Merge props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">auto id
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable identifier for
                            the strand, its nodes and registered anchors.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">attach-to
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Registered target
                            anchor. Resolves the merge position from the target and route dimensions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Manual starting
                            coordinates when no attach-to target is used.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Strand color inherited
                            from the graph unless explicitly set.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Horizontal distance
                            between the two arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback length of the
                            main vertical stem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Labels for concrete
                            merge anchors. Use named text, width, align and color options; end labels the final anchor.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev-mode
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited dev
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Local diagnostic
                            override; normally inherited from the graph.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:extension-count
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Number of additional
                            side sources.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">extension-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved bridge length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback bridge length
                            for extensions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">extension-stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved stem length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback vertical stem
                            length for extensions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:extension-node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Per-extension node
                            labels, keyed by extension number and node number.</flux:table.cell>
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
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- merge-extension-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-08-merge-extension"
                    :dev="true"
                    :coordinates="true"
                    color="amber"
                    arc-size="2.75rem"
                    bridge-length="8rem"
                    stem-length="5rem"
                    slot-min-height="52rem"
                    horizontal-padding="16rem"
                    min-width="36rem"
                    min-height="32rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.merge-reference"
                            color="zinc"
                            :stem-count="4"
                            start-length="4rem"
                            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                            end-length="3rem"
                            :dev-mode="false"
                            :start-label="[
                                'text' => ['reference trunk'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                        />
                    </div>

                    <x-translation-workbench::ui.tw-graph.strang.merge-left
                        id="literature.left.1.source-note"
                        attach-to="strang.trunk.node.2"
                        :extension-count="1"
                        extension-bridge-length="8rem"
                        extension-stem-length="5rem"
                        :extension-node-labels="[
                            1 => [
                                1 => [
                                    'left' => [
                                        'text' => ['Second source', 'same idea'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                                4 => [
                                    'right' => [
                                        'text' => ['extension joins', 'the merge path'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.merge-right
                        id="literature.right.1.source-note"
                        attach-to="strang.trunk.node.2"
                        :extension-count="1"
                        extension-bridge-length="8rem"
                        extension-stem-length="5rem"
                        :extension-node-labels="[
                            1 => [
                                1 => [
                                    'right' => [
                                        'text' => ['Second review', 'same conclusion'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                4 => [
                                    'left' => [
                                        'text' => ['extension joins', 'the merge path'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-extension-example-1:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../strang/merge/merge-extension.blade.php</flux:field>
    </flux:callout>
</section>
