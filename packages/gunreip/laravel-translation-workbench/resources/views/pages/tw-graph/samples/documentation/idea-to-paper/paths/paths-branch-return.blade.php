<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Branch return path') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Two individually authored return paths, shown one below the other. A branch return leads back toward the trunk: side="left" runs left-right, while side="right" runs right-left. Each path consists of an incoming arc, a bridge, and an outgoing arc. Each zinc reference branch leads outward and upwards. The colored return starts at its stem endpoint and leads back to the x coordinate of the branch origin. Both components calculate their own route from explicitly configured anchors and lengths.') }}
        </flux:callout.text>
        @php
            $branchReturnExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- branch-return-left-example:start --\}\}\R(.*?)^[ \t]*\{\{-- branch-return-left-example:end --\}\}/ms',
                    $branchReturnExampleSource,
                    $branchReturnLeftMatch,
                ) !== 1
            ) {
                throw new \LogicException('The branch-return-left-example requires start and end markers.');
            }
            $branchReturnLeftLines = explode("\n", rtrim($branchReturnLeftMatch[1]));
            $branchReturnLeftIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($branchReturnLeftLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $branchReturnLeftCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $branchReturnLeftIndent), $branchReturnLeftLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- branch-return-right-example:start --\}\}\R(.*?)^[ \t]*\{\{-- branch-return-right-example:end --\}\}/ms',
                    $branchReturnExampleSource,
                    $branchReturnRightMatch,
                ) !== 1
            ) {
                throw new \LogicException('The branch-return-right-example requires start and end markers.');
            }
            $branchReturnRightLines = explode("\n", rtrim($branchReturnRightMatch[1]));
            $branchReturnRightIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($branchReturnRightLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $branchReturnRightCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $branchReturnRightIndent), $branchReturnRightLines),
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="left"</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $branchReturnLeftCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="right"</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $branchReturnRightCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Branch return path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.branch-return</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the return and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left returns left-right; right returns right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates for the incoming arc; subsequent anchors are calculated.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of both arcs. The total vertical rise is twice this size.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Horizontal bridge length. Total horizontal displacement includes both arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the arcs, bridge, and joint arrows.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:fallback</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Allow dashed fallback styling when the owning component reports that a fallback anchor was used. Does not select or calculate a fallback anchor itself.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the path bounding box and segment diagnostics.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Branch return path preview') }}</flux:callout.heading>
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
                {{-- branch-return-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-branch-return-left-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    slot-min-height="30rem"
                    min-height="30rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    {{-- Reference stem ends at x=-6.75rem, y=16.5rem, where the return starts. --}}
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="literature.paths.branch-return.left-right.reference"
                        side="left"
                        :anchor-start="['x' => '6.75rem', 'y' => '5rem']"
                        entry-stem-length="2rem"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'render' => true,
                            ],
                        ]"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.branch-return
                        id="literature.paths.branch-return.left-right"
                        side="left"
                        :anchor-start="['x' => '-6.75rem', 'y' => '16.5rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-return-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- branch-return-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-branch-return-right-left"
                    :dev="true"
                    :coordinates="true"
                    color="emerald"
                    slot-min-height="30rem"
                    min-height="30rem"
                    min-width="36rem"
                    horizontal-padding="14rem"
                >
                    {{-- Reference stem ends at x=6.75rem, y=16.5rem, where the return starts. --}}
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="literature.paths.branch-return.right-left.reference"
                        side="right"
                        :anchor-start="['x' => '-6.75rem', 'y' => '5rem']"
                        entry-stem-length="2rem"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'render' => true,
                            ],
                        ]"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.branch-return
                        id="literature.paths.branch-return.right-left"
                        side="right"
                        :anchor-start="['x' => '6.75rem', 'y' => '16.5rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        color="emerald"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-return-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-branch-return.blade.php
        </flux:field>
    </flux:callout>
</section>
