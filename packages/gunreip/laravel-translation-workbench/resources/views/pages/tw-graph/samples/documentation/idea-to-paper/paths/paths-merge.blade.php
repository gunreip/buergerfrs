<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Merge path') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Two individually authored merge paths, shown one below the other. side="left" routes the bridge left-right; side="right" routes it right-left. Both use a start section, one stem, an incoming arc, a bridge, and an outgoing arc. The path calculates their connections from the starting anchor and lengths.') }}
        </flux:callout.text>
        @php
            $mergeExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-merge',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- merge-left-example:start --\}\}\R(.*?)^[ \t]*\{\{-- merge-left-example:end --\}\}/ms',
                    $mergeExampleSource,
                    $mergeLeftMatch,
                ) !== 1
            ) {
                throw new \LogicException('The merge-left-example requires start and end markers.');
            }
            $mergeLeftLines = explode("\n", rtrim($mergeLeftMatch[1]));
            $mergeLeftIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($mergeLeftLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $mergeLeftCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $mergeLeftIndent), $mergeLeftLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- merge-right-example:start --\}\}\R(.*?)^[ \t]*\{\{-- merge-right-example:end --\}\}/ms',
                    $mergeExampleSource,
                    $mergeRightMatch,
                ) !== 1
            ) {
                throw new \LogicException('The merge-right-example requires start and end markers.');
            }
            $mergeRightLines = explode("\n", rtrim($mergeRightMatch[1]));
            $mergeRightIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($mergeRightLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $mergeRightCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $mergeRightIndent), $mergeRightLines),
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="left"</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $mergeLeftCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="right"</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $mergeRightCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Merge path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.merge
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line-width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph line_width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line width used by the
                            path geometry.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback size for both
                            arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:arc-sizes
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional separate
                            sizes: keys 1/in for the incoming arc and 2/out for the outgoing arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Horizontal distance
                            between the arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-lengths
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit stem lengths
                            before the incoming arc; positive entries create stems.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional stem
                            sections, optionally with compressed markers.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:compressed-stem-parts
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Compression options for
                            individual stem continuations.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Merge / start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label at the start;
                            falls back to node-labels.start and then the default label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Numbered node labels.
                            The explicit end key labels the final anchor and takes precedence over a numeric end entry.
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
        <flux:callout.heading>{{ __('Merge path preview') }}</flux:callout.heading>
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
                {{-- merge-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-merge-left-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    slot-min-height="28rem"
                    min-height="28rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    <x-translation-workbench::ui.tw-graph.paths.merge
                        id="literature.paths.merge.left-right"
                        side="left"
                        :anchor-start="['x' => '-4.75rem', 'y' => '5rem']"
                        start-length="2.5rem"
                        :stem-lengths="[1 => '4rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        :start-label="['text' => ['Merge start'], 'width' => 'half']"
                        :node-labels="[
                            'end' => [
                                'right' => [
                                    'text' => ['Merge end'],
                                    'width' => 'half',
                                    'align' => 'left',
                                ],
                            ],
                        ]"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- merge-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-merge-right-left"
                    :dev="true"
                    :coordinates="true"
                    color="emerald"
                    slot-min-height="28rem"
                    min-height="28rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    <x-translation-workbench::ui.tw-graph.paths.merge
                        id="literature.paths.merge.right-left"
                        side="right"
                        :anchor-start="['x' => '14.75rem', 'y' => '5rem']"
                        start-length="4.5rem"
                        :stem-lengths="[1 => '4rem']"
                        arc-size="2.75rem"
                        bridge-length="10rem"
                        :start-label="['text' => ['Merge start'], 'width' => 'half']"
                        :node-labels="[
                            'end' => [
                                'left' => [
                                    'text' => ['Merge end'],
                                    'width' => 'half',
                                    'align' => 'right',
                                ],
                            ],
                        ]"
                        {{-- color="cyan" --}}
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- merge-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-merge.blade.php
        </flux:field>
    </flux:callout>
</section>
