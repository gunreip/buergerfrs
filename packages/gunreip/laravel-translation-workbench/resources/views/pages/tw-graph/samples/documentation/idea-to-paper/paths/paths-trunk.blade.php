<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Trunk path') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Two individually authored trunk paths demonstrate bottom-top and top-bottom. Each connects a start section, three middle sections, and an end section. The middle sections are 3rem, 4rem, and 5rem long. The second carries a Checkpoint label. Each path calculates its next anchors and delegates drawing to its segments. The upward path starts at y = 5rem and ends at y = 22rem; the downward path starts at y = 22rem and ends at y = 5rem.') }}
        </flux:callout.text>
        @php
            $trunkExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-trunk',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- trunk-example:start --\}\}\R(.*?)^[ \t]*\{\{-- trunk-example:end --\}\}/ms',
                    $trunkExampleSource,
                    $trunkExampleMatch,
                ) !== 1
            ) {
                throw new \LogicException('The trunk example requires start and end markers.');
            }
            $trunkExampleLines = explode("\n", rtrim($trunkExampleMatch[1]));
            $trunkExampleIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($trunkExampleLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $trunkExampleCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $trunkExampleIndent), $trunkExampleLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- trunk-top-bottom-example:start --\}\}\R(.*?)^[ \t]*\{\{-- trunk-top-bottom-example:end --\}\}/ms',
                    $trunkExampleSource,
                    $trunkTopBottomMatch,
                ) !== 1
            ) {
                throw new \LogicException('The trunk example requires start and end markers.');
            }
            $trunkTopBottomLines = explode("\n", rtrim($trunkTopBottomMatch[1]));
            $trunkTopBottomIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($trunkTopBottomLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $trunkTopBottomCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $trunkTopBottomIndent), $trunkTopBottomLines),
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom-top</flux:heading>
        <div
            class="mt-4 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $trunkExampleCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >top-bottom</flux:heading>
        <div
            class="mt-4 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $trunkTopBottomCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Trunk path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">generated
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable identifier for
                            the path and its child segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Path direction:
                            bottom-top, top-bottom, left-right, or right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates.
                            Subsequent anchors are calculated from the section lengths.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas line-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the start
                            section.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-shift-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional distance
                            after the start section and before the middle sections.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:path-count
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default-path-segments
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Number of middle
                            sections, excluding start and end.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:default-path-segments
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">10</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback middle-section
                            count when path-count is omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:path-lengths
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Lengths and optional
                            node labels for each middle section. Missing lengths use canvas line-length. A numbered map
                            starts at 1; a list uses its natural order.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas line-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the end
                            section.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-cap-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas cap-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Width of the closing
                            cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Path / start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Start label
                            configuration. Use false to hide it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:end-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Path / end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">End label
                            configuration. Use false to hide it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional left, right,
                            top, or bottom labels at the end of the start section.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Path color inherited
                            from the canvas unless explicitly set.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order
                            override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev-mode
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Overrides inherited DEV
                            mode for the path segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:show-dev-box
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Includes the path
                            bounding box for DEV display.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:show-layout-spacer
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Reserves layout space
                            for the calculated path dimensions.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Trunk path preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div
                    class="grid grid-cols-2 gap-4"
                    style="min-width: 48rem;"
                >
                    <div class="min-w-0">
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >bottom-top</flux:heading>
                        {{-- trunk-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-paths-trunk-preview"
                            :dev="true"
                            :coordinates="true"
                            color="cyan"
                            slot-min-height="30rem"
                            min-height="30rem"
                            min-width="24rem"
                            horizontal-padding="12rem"
                        >
                            <x-translation-workbench::ui.tw-graph.paths.trunk
                                id="literature.paths.trunk"
                                direction="bottom-top"
                                :anchor-start="['x' => '0rem', 'y' => '5rem']"
                                start-length="2.5rem"
                                :path-count="3"
                                :path-lengths="[
                                    1 => '3rem',
                                    2 => [
                                        'length' => '4rem',
                                        'labels' => [
                                            'right' => [
                                                'text' => ['Checkpoint'],
                                                'width' => 'half',
                                                'align' => 'left',
                                            ],
                                        ],
                                    ],
                                    3 => '5rem',
                                ]"
                                end-length="2.5rem"
                                :start-label="['text' => ['Path start'], 'width' => 'half']"
                                :end-label="['text' => ['Path end'], 'width' => 'half']"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- trunk-example:end --}}
                    </div>
                    <div class="min-w-0">
                        <flux:heading
                            class="px-3 pt-3"
                            size="sm"
                        >top-bottom</flux:heading>
                        {{-- trunk-top-bottom-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-paths-trunk-top-bottom-preview"
                            :dev="true"
                            :coordinates="true"
                            color="cyan"
                            slot-min-height="30rem"
                            min-height="30rem"
                            min-width="24rem"
                            horizontal-padding="12rem"
                        >
                            <x-translation-workbench::ui.tw-graph.paths.trunk
                                id="literature.paths.trunk-top-bottom"
                                direction="top-bottom"
                                :anchor-start="['x' => '0rem', 'y' => '22rem']"
                                start-length="2.5rem"
                                :path-count="3"
                                :path-lengths="[
                                    1 => '3rem',
                                    2 => [
                                        'length' => '4rem',
                                        'labels' => [
                                            'right' => [
                                                'text' => ['Checkpoint'],
                                                'width' => 'half',
                                                'align' => 'left',
                                            ],
                                        ],
                                    ],
                                    3 => '5rem',
                                ]"
                                end-length="2.5rem"
                                :start-label="['text' => ['Path start'], 'width' => 'half']"
                                :end-label="['text' => ['Path end'], 'width' => 'half']"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- trunk-top-bottom-example:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-trunk.blade.php
        </flux:field>
    </flux:callout>
</section>
