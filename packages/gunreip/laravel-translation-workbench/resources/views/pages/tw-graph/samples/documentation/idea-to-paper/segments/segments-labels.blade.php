<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Label segments') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('A label attaches information to a visible anchor through a connector. The zinc path and dot are reference geometry. A label bridge places text inside the horizontal flow: bridge-in, text label, bridge-out. These individually authored examples show all four label positions and both label-bridge directions. Text alignment is independent of placement and traversal direction.') }}
        </flux:callout.text>
        @php
            $labelsExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-labels',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- segment-label-right:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-label-right:end --\}\}/ms',
                    $labelsExampleSource,
                    $labelsLabelRightMatch,
                ) !== 1
            ) {
                throw new \LogicException('The segment-label-right requires start and end markers.');
            }
            $labelsLabelRightLines = explode("\n", rtrim($labelsLabelRightMatch[1]));
            $labelsLabelRightIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($labelsLabelRightLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $labelsLabelRightCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $labelsLabelRightIndent), $labelsLabelRightLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- segment-label-left:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-label-left:end --\}\}/ms',
                    $labelsExampleSource,
                    $labelsLabelLeftMatch,
                ) !== 1
            ) {
                throw new \LogicException('The segment-label-left requires start and end markers.');
            }
            $labelsLabelLeftLines = explode("\n", rtrim($labelsLabelLeftMatch[1]));
            $labelsLabelLeftIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($labelsLabelLeftLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $labelsLabelLeftCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $labelsLabelLeftIndent), $labelsLabelLeftLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- segment-label-top:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-label-top:end --\}\}/ms',
                    $labelsExampleSource,
                    $labelsLabelTopMatch,
                ) !== 1
            ) {
                throw new \LogicException('The segment-label-top requires start and end markers.');
            }
            $labelsLabelTopLines = explode("\n", rtrim($labelsLabelTopMatch[1]));
            $labelsLabelTopIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($labelsLabelTopLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $labelsLabelTopCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $labelsLabelTopIndent), $labelsLabelTopLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- segment-label-bottom:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-label-bottom:end --\}\}/ms',
                    $labelsExampleSource,
                    $labelsLabelBottomMatch,
                ) !== 1
            ) {
                throw new \LogicException('The segment-label-bottom requires start and end markers.');
            }
            $labelsLabelBottomLines = explode("\n", rtrim($labelsLabelBottomMatch[1]));
            $labelsLabelBottomIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($labelsLabelBottomLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $labelsLabelBottomCode = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $labelsLabelBottomIndent), $labelsLabelBottomLines),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- segment-bridge-left-right:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-bridge-left-right:end --\}\}/ms',
                    $labelsExampleSource,
                    $labelsBridgeLeftRightMatch,
                ) !== 1
            ) {
                throw new \LogicException('The segment-bridge-left-right requires start and end markers.');
            }
            $labelsBridgeLeftRightLines = explode("\n", rtrim($labelsBridgeLeftRightMatch[1]));
            $labelsBridgeLeftRightIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($labelsBridgeLeftRightLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $labelsBridgeLeftRightCode = implode(
                "\n",
                array_map(
                    fn(string $line): string => substr($line, $labelsBridgeLeftRightIndent),
                    $labelsBridgeLeftRightLines,
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- segment-bridge-right-left:start --\}\}\R(.*?)^[ \t]*\{\{-- segment-bridge-right-left:end --\}\}/ms',
                    $labelsExampleSource,
                    $labelsBridgeRightLeftMatch,
                ) !== 1
            ) {
                throw new \LogicException('The segment-bridge-right-left requires start and end markers.');
            }
            $labelsBridgeRightLeftLines = explode("\n", rtrim($labelsBridgeRightLeftMatch[1]));
            $labelsBridgeRightLeftIndent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($labelsBridgeRightLeftLines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $labelsBridgeRightLeftCode = implode(
                "\n",
                array_map(
                    fn(string $line): string => substr($line, $labelsBridgeRightLeftIndent),
                    $labelsBridgeRightLeftLines,
                ),
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >Label · right</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $labelsLabelRightCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label · left</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $labelsLabelLeftCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label · top</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $labelsLabelTopCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label · bottom</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $labelsLabelBottomCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label bridge · left-right</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $labelsBridgeLeftRightCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label bridge · right-left</flux:heading>
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $labelsBridgeRightLeftCode }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label props</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop / label field') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the text
                            and its connector.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text configuration; see
                            the shared label fields below.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-x
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">X coordinate of an
                            existing visible anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-y
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Y coordinate of that
                            anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label and connector
                            position: right, left, top, bottom. Set on the component, not in label.side.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback label and
                            connector color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable the text
                            diagnostic box.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.connectorLength
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / graph
                            connector_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Connector length;
                            fallback 2rem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.connectorGap
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / graph
                            connector_gap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Gap around the
                            connector; fallback 0.25rem.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Label bridge props</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop / label field') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.label-bridge
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the
                            bridge and child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label-id
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id + .label.center.1
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional custom text
                            identifier.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text configuration;
                            side remains centered inside the bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates;
                            the remaining bridge anchors are calculated.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-right
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-right or
                            right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null → 1.15rem
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of each bridge
                            section. Omitted value 0.75rem resolves to 1.15rem; rem values are clamped by LabelBridge.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label-width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">from label.width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Reserved width for the
                            label. Normally use label.width to keep geometry and rendering consistent.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Bridge color, blending
                            toward the label color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path-tone
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Palette tone of the
                            bridge sections.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stacking-order
                            override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable path diagnostics
                            and the text box.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >Shared label fields</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop / label field') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.text
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text content as a
                            string or an array of lines.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.width
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">half, default,
                            halfLong, or long.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.align
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">center
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Alignment of text
                            inside its box; independent of side and direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.justify
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Justify the text.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.maxLines
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Maximum visible lines.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.badge
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Render the label as a
                            badge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">component color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label color; also
                            colors the connector for segments.label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label.badgeColor
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Override badge color;
                            label-bridge blends toward this color.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Label segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                <div class="min-w-0">
                    <flux:heading size="sm">Label · right</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-label-right:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-label-right"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="20rem"
                            min-height="20rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            {{-- Reference path owns the visible anchor for the label. --}}
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.label-right.reference"
                                direction="bottom-top"
                                length="4rem"
                                :anchor-start="['x' => '-4rem', 'y' => '4rem']"
                                :anchor-end="['x' => '-4rem', 'y' => '8rem']"
                                :node-end="true"
                                color="zinc"
                                :dev="true"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.segments.label-right"
                                anchor-x="-4rem"
                                anchor-y="8rem"
                                side="right"
                                :label="[
                                    'text' => ['Information', 'at the anchor'],
                                    'width' => 'half',
                                    'align' => 'center',
                                    'connectorLength' => '2rem',
                                    'connectorGap' => '0.25rem',
                                ]"
                                color="cyan"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-label-right:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">Label · left</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-label-left:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-label-left"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="20rem"
                            min-height="20rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            {{-- Reference path owns the visible anchor for the label. --}}
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.label-left.reference"
                                direction="bottom-top"
                                length="4rem"
                                :anchor-start="['x' => '4rem', 'y' => '4rem']"
                                :anchor-end="['x' => '4rem', 'y' => '8rem']"
                                :node-end="true"
                                color="zinc"
                                :dev="true"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.segments.label-left"
                                anchor-x="4rem"
                                anchor-y="8rem"
                                side="left"
                                :label="[
                                    'text' => ['Information', 'at the anchor'],
                                    'width' => 'half',
                                    'align' => 'center',
                                    'connectorLength' => '2rem',
                                    'connectorGap' => '0.25rem',
                                ]"
                                color="emerald"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-label-left:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">Label · top</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-label-top:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-label-top"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="20rem"
                            min-height="20rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            {{-- Reference path owns the visible anchor for the label. --}}
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.label-top.reference"
                                direction="left-right"
                                length="4rem"
                                :anchor-start="['x' => '-4rem', 'y' => '8rem']"
                                :anchor-end="['x' => '0rem', 'y' => '8rem']"
                                :node-end="true"
                                color="zinc"
                                :dev="true"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.segments.label-top"
                                anchor-x="0rem"
                                anchor-y="8rem"
                                side="top"
                                :label="[
                                    'text' => ['Information', 'at the anchor'],
                                    'width' => 'half',
                                    'align' => 'center',
                                    'connectorLength' => '2rem',
                                    'connectorGap' => '0.25rem',
                                ]"
                                color="fuchsia"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-label-top:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">Label · bottom</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-label-bottom:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-label-bottom"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="20rem"
                            min-height="20rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            {{-- Reference path owns the visible anchor for the label. --}}
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.label-bottom.reference"
                                direction="left-right"
                                length="4rem"
                                :anchor-start="['x' => '-4rem', 'y' => '8rem']"
                                :anchor-end="['x' => '0rem', 'y' => '8rem']"
                                :node-end="true"
                                color="zinc"
                                :dev="true"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.segments.label-bottom"
                                anchor-x="0rem"
                                anchor-y="8rem"
                                side="bottom"
                                :label="[
                                    'text' => ['Information', 'at the anchor'],
                                    'width' => 'half',
                                    'align' => 'center',
                                    'connectorLength' => '2rem',
                                    'connectorGap' => '0.25rem',
                                ]"
                                color="amber"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-label-bottom:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">Label bridge · left-right</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-bridge-left-right:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-bridge-left-right"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="20rem"
                            min-height="20rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.segments.bridge-left-right"
                                direction="left-right"
                                :anchor-start="['x' => '-6rem', 'y' => '8rem']"
                                bridge-length="3rem"
                                :label="[
                                    'text' => ['Action', 'inside the flow'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ]"
                                color="cyan"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-bridge-left-right:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">Label bridge · right-left</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-bridge-right-left:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-bridge-right-left"
                            :dev="true"
                            :coordinates="true"
                            slot-min-height="20rem"
                            min-height="20rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.label-bridge
                                id="literature.segments.bridge-right-left"
                                direction="right-left"
                                :anchor-start="['x' => '6rem', 'y' => '8rem']"
                                bridge-length="3rem"
                                :label="[
                                    'text' => ['Action', 'inside the flow'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ]"
                                color="emerald"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-bridge-right-left:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-labels.blade.php
        </flux:field>
    </flux:callout>
</section>
