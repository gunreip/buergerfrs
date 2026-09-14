<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Merge mismatch') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('A merge can only render node labels for existing label anchors. If numeric node-label keys exceed the available merge anchors, DEV mode reports nodeLabel-Mismatch. If a numeric end label and the explicit end alias are both set, end wins and DEV mode reports nodeLabel-EndOverride.') }}
        </flux:callout.text>
        @php
            $mergeExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-mismatch',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- merge-mismatch-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- merge-mismatch-example-1:end --\}\}/ms',
                    $mergeExampleSource,
                    $mergeExample1Match,
                ) !== 1
            ) {
                throw new \LogicException('Missing merge-mismatch-example-1 source markers.');
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-lengths
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Defines the available
                            stem sections and their lengths.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved arc size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the initial
                            section.</flux:table.cell>
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
                {{-- merge-mismatch-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-07-merge-mismatch"
                    :dev="true"
                    :coordinates="true"
                    color="amber"
                    arc-size="2.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    slot-min-height="34rem"
                    horizontal-padding="12rem"
                    min-width="42rem"
                    min-height="24rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.merge-mismatch-reference"
                            color="zinc"
                            :stem-count="3"
                            start-length="7rem"
                            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem']"
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
                        id="literature.left.1.label-mismatch"
                        attach-to="strang.trunk.node.2"
                        bridge-length="12rem"
                        start-length="3rem"
                        :stem-lengths="[1 => '4rem']"
                        :node-labels="[
                            1 => [
                                'right' => [
                                    'text' => ['Valid label', 'anchor 1'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                            'end' => [
                                'left' => [
                                    'text' => ['Valid end label', 'explicit end'],
                                    'width' => 'halfLong',
                                    'align' => 'right',
                                ],
                            ],
                            4 => [
                                'right' => [
                                    'text' => ['Ignored label', 'anchor 4 missing'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                            5 => [
                                'left' => [
                                    'text' => ['Ignored label', 'anchor 5 missing'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.merge-right
                        id="literature.right.1.end-override"
                        attach-to="strang.trunk.node.3"
                        bridge-length="12rem"
                        start-length="3rem"
                        :stem-lengths="[1 => '4rem']"
                        :node-labels="[
                            1 => [
                                'left' => [
                                    'text' => ['Valid label', 'anchor 1'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                            3 => [
                                'right' => [
                                    'text' => ['Numeric end label', 'overridden'],
                                    'width' => 'halfLong',
                                    'align' => 'left',
                                ],
                            ],
                            'end' => [
                                'right' => [
                                    'text' => ['Explicit end label', 'end wins'],
                                    'width' => 'halfLong',
                                    'align' => 'left',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-mismatch-example-1:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../strang/merge/merge-mismatch.blade.php</flux:field>
    </flux:callout>
</section>
