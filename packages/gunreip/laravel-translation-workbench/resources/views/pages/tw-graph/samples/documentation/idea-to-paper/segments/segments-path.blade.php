<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Path segment') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Four individually authored path segments with the same length in all four directions. Each combines a line with anchor dots, an end label and connector, and DEV diagnostics. Set both anchors to match direction and length: unlike a complete path, this segment expects its endpoint coordinates explicitly. Canvas y coordinates increase upwards.') }}
        </flux:callout.text>
        @php
            $pathExampleSource = file_get_contents(\Illuminate\Support\Facades\View::getFinder()->find(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-path',
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-path-bottom-top:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-path-bottom-top:end --\}\}/ms', $pathExampleSource, $pathBottomTopMatch) !== 1) {
                throw new \LogicException('The segment-path-bottom-top requires start and end markers.');
            }
            $pathBottomTopLines = explode("\n", rtrim($pathBottomTopMatch[1]));
            $pathBottomTopIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($pathBottomTopLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $pathBottomTopCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $pathBottomTopIndent),
                $pathBottomTopLines,
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-path-top-bottom:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-path-top-bottom:end --\}\}/ms', $pathExampleSource, $pathTopBottomMatch) !== 1) {
                throw new \LogicException('The segment-path-top-bottom requires start and end markers.');
            }
            $pathTopBottomLines = explode("\n", rtrim($pathTopBottomMatch[1]));
            $pathTopBottomIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($pathTopBottomLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $pathTopBottomCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $pathTopBottomIndent),
                $pathTopBottomLines,
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-path-left-right:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-path-left-right:end --\}\}/ms', $pathExampleSource, $pathLeftRightMatch) !== 1) {
                throw new \LogicException('The segment-path-left-right requires start and end markers.');
            }
            $pathLeftRightLines = explode("\n", rtrim($pathLeftRightMatch[1]));
            $pathLeftRightIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($pathLeftRightLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $pathLeftRightCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $pathLeftRightIndent),
                $pathLeftRightLines,
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-path-right-left:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-path-right-left:end --\}\}/ms', $pathExampleSource, $pathRightLeftMatch) !== 1) {
                throw new \LogicException('The segment-path-right-left requires start and end markers.');
            }
            $pathRightLeftLines = explode("\n", rtrim($pathRightLeftMatch[1]));
            $pathRightLeftIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($pathRightLeftLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $pathRightLeftCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $pathRightLeftIndent),
                $pathRightLeftLines,
            ));
        @endphp
        <flux:heading class="mt-4" size="sm">bottom-top</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $pathBottomTopCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">top-bottom</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $pathTopBottomCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">left-right</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $pathLeftRightCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">right-left</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $pathRightLeftCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">{{ __('Path segment props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.path</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the line and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top, top-bottom, left-right, or right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">4rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the line; must match the distance between the anchors.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit starting coordinates.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-end</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;4rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit endpoint; changing direction or length does not recalculate it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show the start node with true, or provide up to two label entries in an array.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-end</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show the end node with true, or provide up to two label entries in an array. Label entries accept text, width, align, and side; horizontal lines use top/bottom, vertical lines use left/right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line, node, and label color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dashed</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Draw a dashed line.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:gradient</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fade the line along its direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">to-color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Target color for a color gradient.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:color-gradient</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the gradient between color and to-color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:cap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the end cap unless cap-end explicitly overrides it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:cap-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show a cap at the starting anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:cap-end</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Override the end-cap setting.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1.25rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Palette tone used for the line and nodes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable diagnostic boxes and node counters. Falls back to segment.dev, then false.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:segment</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Alternative complete segment configuration. A non-empty array replaces the individual geometry and styling props; dev can still override segment.dev.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Path segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                <div class="min-w-0">
                    <flux:heading size="sm">bottom-top</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-bottom-top:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-bottom-top"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.bottom-top"
                                direction="bottom-top"
                                length="8rem"
                                :anchor-start="['x' => '0rem', 'y' => '4rem']"
                                :anchor-end="['x' => '0rem', 'y' => '12rem']"
                                :node-start="true"
                                :node-end="[
                                    ['text' => ['End'], 'width' => 'half', 'align' => 'center'],
                                ]"
                                color="cyan"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-bottom-top:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">top-bottom</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-top-bottom:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-top-bottom"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.top-bottom"
                                direction="top-bottom"
                                length="8rem"
                                :anchor-start="['x' => '0rem', 'y' => '12rem']"
                                :anchor-end="['x' => '0rem', 'y' => '4rem']"
                                :node-start="true"
                                :node-end="[
                                    ['text' => ['End'], 'width' => 'half', 'align' => 'center'],
                                ]"
                                color="emerald"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-top-bottom:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">left-right</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-left-right:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-left-right"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.left-right"
                                direction="left-right"
                                length="8rem"
                                :anchor-start="['x' => '-4rem', 'y' => '8rem']"
                                :anchor-end="['x' => '4rem', 'y' => '8rem']"
                                :node-start="true"
                                :node-end="[
                                    ['text' => ['End'], 'width' => 'half', 'align' => 'center'],
                                ]"
                                color="fuchsia"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-left-right:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">right-left</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-right-left:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-right-left"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.right-left"
                                direction="right-left"
                                length="8rem"
                                :anchor-start="['x' => '4rem', 'y' => '8rem']"
                                :anchor-end="['x' => '-4rem', 'y' => '8rem']"
                                :node-start="true"
                                :node-end="[
                                    ['text' => ['End'], 'width' => 'half', 'align' => 'center'],
                                ]"
                                color="amber"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-right-left:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-path.blade.php
        </flux:field>
    </flux:callout>
</section>
