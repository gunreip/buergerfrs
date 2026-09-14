<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Merge aggregated') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Aggregated merge stems keep many related origins readable without rendering every origin as a full merge strand. The aggregate label names the group; continuation labels identify representative rows.') }}
        </flux:callout.text>
        @php
            $mergeExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-aggregated',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- merge-aggregated-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- merge-aggregated-example-1:end --\}\}/ms',
                    $mergeExampleSource,
                    $mergeExample1Match,
                ) !== 1
            ) {
                throw new \LogicException('Missing merge-aggregated-example-1 source markers.');
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional main stem
                            sections with length, labels and optional compression.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">
                            :extension-stem-continuations</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Per-extension stem
                            continuations. Each entry can include length, labels and compressed=true.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">
                            :extension-bridge-continuations</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Per-extension bridge
                            continuation configuration.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:extension-node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Per-extension node
                            label configuration.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">extension-stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved stem length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback length of
                            extension stems.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">extension-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved bridge length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback length of
                            extension bridges.</flux:table.cell>
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
                {{-- merge-aggregated-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-09-merge-aggregated"
                    :dev="true"
                    :coordinates="true"
                    color="amber"
                    arc-size="2.75rem"
                    bridge-length="16rem"
                    stem-length="5rem"
                    slot-min-height="66rem"
                    horizontal-padding="28rem"
                    min-width="52rem"
                    min-height="66rem"
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
                        id="literature.left.1.aggregated-sources"
                        attach-to="strang.trunk.node.2"
                        bridge-length="8rem"
                        :extension-count="1"
                        extension-bridge-length="24rem"
                        extension-stem-length="4rem"
                        :extension-stem-continuations="[
                            1 => [
                                1 => ['length' => '4rem'],
                                2 => ['length' => '4rem'],
                            ],
                        ]"
                        :extension-node-labels="[
                            1 => [
                                1 => [
                                    'labels' => [
                                        'left' => [
                                            'text' => ['Aggregated origins #1', '2 sources'],
                                            'align' => 'right',
                                        ],
                                        'right' => [
                                            'text' => ['Aggregated origins #2', '3 sources'],
                                            'align' => 'left',
                                        ],
                                    ],
                                    'width' => 'default',
                                ],
                                2 => [
                                    'labels' => [
                                        'left' => [
                                            'text' => ['Aggregated origins #3', '2 sources'],
                                            'align' => 'right',
                                        ],
                                        'right' => [
                                            'text' => ['Aggregated origins #4', '8 sources'],
                                            'align' => 'left',
                                        ],
                                    ],
                                    'width' => 'default',
                                ],
                                3 => [
                                    'labels' => [
                                        'left' => [
                                            'text' => ['Finding ID #42', 'archive note'],
                                            'align' => 'right',
                                        ],
                                        'right' => [
                                            'text' => ['Finding ID #44', 'archive note'],
                                            'align' => 'left',
                                        ],
                                    ],
                                    'width' => 'default',
                                ],
                                4 => [
                                    'labels' => [
                                        'left' => [
                                            'text' => ['Finding ID #43', 'review note'],
                                            'align' => 'right',
                                        ],
                                    ],
                                    'width' => 'default',
                                ],
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.merge-right
                        id="literature.right.1.aggregated-sources"
                        attach-to="strang.trunk.node.3"
                        bridge-length="12rem"
                        :extension-count="1"
                        extension-start-length="12rem"
                        extension-start-shift-enabled="true"
                        extension-start-shift-length="8rem"
                        extension-bridge-length="28rem"
                        extension-stem-length="4rem"
                        :extension-stem-continuations="[
                            1 => [
                                1 => ['length' => '8rem'],
                                2 => ['length' => '4rem'],
                            ],
                        ]"
                        :extension-node-labels="[
                            1 => [
                                1 => [
                                    'labels' => [
                                        'left' => [
                                            'text' => ['Aggregated origins', '2 sources'],
                                            'align' => 'right',
                                        ],
                                    ],
                                    'width' => 'halfLong',
                                ],
                                3 => [
                                    'labels' => [
                                        'right' => [
                                            'text' => ['Finding ID #51', 'lab note'],
                                            'align' => 'left',
                                        ],
                                    ],
                                    'width' => 'default',
                                ],
                                4 => [
                                    'labels' => [
                                        'left' => [
                                            'text' => ['Finding ID #52', 'margin note'],
                                            'align' => 'right',
                                        ],
                                    ],
                                    'width' => 'default',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-aggregated-example-1:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../strang/merge/merge-aggregated.blade.php</flux:field>
    </flux:callout>
</section>
