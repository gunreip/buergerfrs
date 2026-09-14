<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Compressed stem segment') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('A compressed stem is a graphical omission marker: a solid section, a cap, a dashed section, another cap, and a solid section. This individually authored example sets all three lengths explicitly. Alternatively, anchorEnd defines the total distance and replaces those lengths with a one-quarter, one-half, one-quarter split. The marker has no status text; use segments.step when a reason or status needs to be shown.') }}
        </flux:callout.text>
        @php
            $compressedStemExampleSource = file_get_contents(\Illuminate\Support\Facades\View::getFinder()->find(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-stem-compressed',
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-stem-compressed-example:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-stem-compressed-example:end --\}\}/ms', $compressedStemExampleSource, $compressedStemMatch) !== 1) {
                throw new \LogicException('The segment-stem-compressed-example requires start and end markers.');
            }
            $compressedStemLines = explode("\n", rtrim($compressedStemMatch[1]));
            $compressedStemIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($compressedStemLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $compressedStemCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $compressedStemIndent),
                $compressedStemLines,
            ));
        @endphp
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $compressedStemCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">{{ __('Compressed stem segment props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop / segment key') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:segment</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Component prop: configuration array. The following rows describe keys inside this array.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Component prop: overrides segment.dev.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.stem-compressed</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the three line sections and their child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top, top-bottom, left-right, or right-left. The example shows a vertical stem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates for the compressed stem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">beforeLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2.5rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the solid section before the first cap, when anchorEnd is omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">gapLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0.5rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the dashed section, when anchorEnd is omitted. This is a drawn section, not an empty gap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">afterLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1.5rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the solid section after the second cap, when anchorEnd is omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">calculated</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional explicit endpoint. When supplied, overrides all three section lengths with 25%, 50%, and 25% of the distance along direction. Both anchors must lie on the same axis.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">capLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Width of the caps on either side of the dashed section. Explicitly set to 1.25rem in this example.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional start dot or node-label array.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">End dot or node-label array. The two inner joins have no nodes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of all three sections, caps, and nodes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zIndex</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Diagnostic boxes and counters from the three segments.path children; overridden by :dev.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Compressed stem segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- segment-stem-compressed-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-segments-stem-compressed"
                    :dev="true"
                    :coordinates="true"
                    slot-min-height="22rem"
                    min-height="22rem"
                    min-width="24rem"
                    horizontal-padding="10rem"
                >
                    <x-translation-workbench::ui.tw-graph.segments.stem-compressed
                        :segment="[
                            'id' => 'literature.segments.stem-compressed',
                            'direction' => 'bottom-top',
                            'anchorStart' => ['x' => '0rem', 'y' => '4rem'],
                            'beforeLength' => '2rem',
                            'gapLength' => '4rem',
                            'afterLength' => '2rem',
                            'capLength' => '1.25rem',
                            'nodeStart' => true,
                            'nodeEnd' => true,
                            'color' => 'cyan',
                        ]"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- segment-stem-compressed-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-stem-compressed.blade.php
        </flux:field>
    </flux:callout>
</section>
