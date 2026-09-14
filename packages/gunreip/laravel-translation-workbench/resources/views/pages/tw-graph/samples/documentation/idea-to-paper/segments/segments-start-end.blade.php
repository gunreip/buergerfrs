<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Start and end segments') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Start and end segments shown in both vertical directions. Start fades in and ends at a dot; end starts at a dot and finishes with a cap. Both delegate the line and nodes to segments.path. Their configuration is passed through the segment array; anchor coordinates must match direction and length. The labels mark the starting or ending anchor.') }}
        </flux:callout.text>
        @php
            $startEndExampleSource = file_get_contents(\Illuminate\Support\Facades\View::getFinder()->find(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-start-end',
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-start-bottom-top:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-start-bottom-top:end --\}\}/ms', $startEndExampleSource, $startBottomTopMatch) !== 1) {
                throw new \LogicException('The segment-start-bottom-top requires start and end markers.');
            }
            $startBottomTopLines = explode("\n", rtrim($startBottomTopMatch[1]));
            $startBottomTopIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($startBottomTopLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $startBottomTopCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $startBottomTopIndent),
                $startBottomTopLines,
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-start-top-bottom:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-start-top-bottom:end --\}\}/ms', $startEndExampleSource, $startTopBottomMatch) !== 1) {
                throw new \LogicException('The segment-start-top-bottom requires start and end markers.');
            }
            $startTopBottomLines = explode("\n", rtrim($startTopBottomMatch[1]));
            $startTopBottomIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($startTopBottomLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $startTopBottomCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $startTopBottomIndent),
                $startTopBottomLines,
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-end-bottom-top:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-end-bottom-top:end --\}\}/ms', $startEndExampleSource, $endBottomTopMatch) !== 1) {
                throw new \LogicException('The segment-end-bottom-top requires start and end markers.');
            }
            $endBottomTopLines = explode("\n", rtrim($endBottomTopMatch[1]));
            $endBottomTopIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($endBottomTopLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $endBottomTopCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $endBottomTopIndent),
                $endBottomTopLines,
            ));
            if (preg_match('/^[ \t]*\{\{-- segment-end-top-bottom:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-end-top-bottom:end --\}\}/ms', $startEndExampleSource, $endTopBottomMatch) !== 1) {
                throw new \LogicException('The segment-end-top-bottom requires start and end markers.');
            }
            $endTopBottomLines = explode("\n", rtrim($endTopBottomMatch[1]));
            $endTopBottomIndent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($endTopBottomLines, fn (string $line): bool => trim($line) !== ''),
            ));
            $endTopBottomCode = implode("\n", array_map(
                fn (string $line): string => substr($line, $endTopBottomIndent),
                $endTopBottomLines,
            ));
        @endphp
        <flux:heading class="mt-4" size="sm">Start · bottom-top</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $startBottomTopCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">Start · top-bottom</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $startTopBottomCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">End · bottom-top</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $endBottomTopCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">End · top-bottom</flux:heading>
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $endTopBottomCode }}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">{{ __('Start and end segment props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop / segment key') }}</flux:table.column>
                    <flux:table.column>{{ __('Start default') }}</flux:table.column>
                    <flux:table.column>{{ __('End default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:segment</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Component prop: configuration array. The following rows document keys inside this array.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Component prop: overrides segment.dev; otherwise defaults to false.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.end</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the segment and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Flow direction. These examples show bottom-top and top-bottom.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">4rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">4rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line length; must agree with the distance between the explicit anchors.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;4rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;4rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Endpoint coordinates; not recalculated when direction or length changes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show a start dot; an array can supply up to two node labels.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false (enforced)</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Start can show an end dot or node labels. End always disables its end node.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">gradient</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fade in the line from its starting anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true (enforced)</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">End enables the cap; capEnd can explicitly override its visibility.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">capStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional cap at the starting anchor, passed through to segments.path.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">capEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Overrides the end-cap visibility; null follows cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">capLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1.25rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1.25rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of a visible cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line and node color; also the default label badge color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Start-only label configuration at anchorStart: text, side, offset, width, align, badge, badgeColor, justify.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel.side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Set top explicitly for a downward start to keep the label outside the line.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">End-only label configuration at anchorEnd: text, side, offset, width, align, badge, badgeColor, justify, maxLines.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">from direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Defaults to top for bottom-top and bottom for top-bottom; can be overridden.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel.offset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited labelGap / graph label_offset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Distance between the start label and its anchor; fallback 0.75rem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.offset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited labelGap / graph label_offset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Distance between the end label and its anchor; fallback 0.75rem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel.width</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label widths: half, default, halfLong, long.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.width</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label widths: half, default, halfLong, long.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">startLabel.align</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">center</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text alignment inside the label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.align</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">center</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text alignment inside the label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">endLabel.maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">not used</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Maximum visible lines in the end label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dashed</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional dashed line, passed through to segments.path.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">toColor</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Target color when colorGradient is enabled.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">colorGradient</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the gradient between color and toColor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Palette tone passed through to segments.path.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zIndex</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Diagnostic mode inside the segment array; overridden by the component :dev prop.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Start and end segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                <div class="min-w-0">
                    <flux:heading size="sm">Start · bottom-top</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-start-bottom-top:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-start-bottom-top"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.start
                                :segment="[
                                    'id' => 'literature.segments.start.bottom-top',
                                    'direction' => 'bottom-top',
                                    'length' => '8rem',
                                    'anchorStart' => ['x' => '0rem', 'y' => '4rem'],
                                    'anchorEnd' => ['x' => '0rem', 'y' => '12rem'],
                                    'startLabel' => [
                                        'text' => ['Start'],
                                        'side' => 'bottom',
                                        'width' => 'half',
                                        'align' => 'center',
                                    ],
                                    'color' => 'cyan',
                                ]"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-start-bottom-top:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">Start · top-bottom</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-start-top-bottom:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-start-top-bottom"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.start
                                :segment="[
                                    'id' => 'literature.segments.start.top-bottom',
                                    'direction' => 'top-bottom',
                                    'length' => '8rem',
                                    'anchorStart' => ['x' => '0rem', 'y' => '12rem'],
                                    'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
                                    'startLabel' => [
                                        'text' => ['Start'],
                                        'side' => 'top',
                                        'width' => 'half',
                                        'align' => 'center',
                                    ],
                                    'color' => 'emerald',
                                ]"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-start-top-bottom:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">End · bottom-top</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-end-bottom-top:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-end-bottom-top"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.end
                                :segment="[
                                    'id' => 'literature.segments.end.bottom-top',
                                    'direction' => 'bottom-top',
                                    'length' => '8rem',
                                    'anchorStart' => ['x' => '0rem', 'y' => '4rem'],
                                    'anchorEnd' => ['x' => '0rem', 'y' => '12rem'],
                                    'endLabel' => [
                                        'text' => ['End'],
                                        'side' => 'top',
                                        'width' => 'half',
                                        'align' => 'center',
                                    ],
                                    'color' => 'fuchsia',
                                ]"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-end-bottom-top:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">End · top-bottom</flux:heading>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-end-top-bottom:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-end-top-bottom"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="18rem"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.end
                                :segment="[
                                    'id' => 'literature.segments.end.top-bottom',
                                    'direction' => 'top-bottom',
                                    'length' => '8rem',
                                    'anchorStart' => ['x' => '0rem', 'y' => '12rem'],
                                    'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
                                    'endLabel' => [
                                        'text' => ['End'],
                                        'side' => 'bottom',
                                        'width' => 'half',
                                        'align' => 'center',
                                    ],
                                    'color' => 'amber',
                                ]"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-end-top-bottom:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-start-end.blade.php
        </flux:field>
    </flux:callout>
</section>
