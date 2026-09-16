<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Step segment') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('A step describes a status or reason inside the path, such as a source being inactive. It composes a line before the label, centered text, and a line after the label. Caps mark the interruption around the text. The label gap is calculated from the text line count and offset; subsequent anchors are calculated from the starting anchor and section lengths.') }}
        </flux:callout.text>
        @php
            $stepExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-step',
            );
            $stepCode = $stepExampleSource->example('segment-step-example');
        @endphp
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $stepCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Step segment props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment.step</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the step and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top, top-bottom, left-right, or right-left. For horizontal steps, reserve a labelGap matching the text width.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Start of the first line section.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">beforeLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the line before the label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">labelGap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">auto</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Gap between line sections, based on up to three non-empty text lines plus twice the label offset. Can be set explicitly.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">afterLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the line after the label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorStep</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">calculated</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional override of the label center; does not move the line sections.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">calculated</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional endpoint override. Keep it consistent with the after-section length and direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepCaps</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show caps on the two line ends beside the label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">capLength</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1.25rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of these caps.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeStart</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional start dot or node-label array.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">nodeEnd</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">End dot or node-label array.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text configuration or a string. Used for status/reason text within the path.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.text</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text as a string or an array of lines.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.width</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">half, default, halfLong, or long.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">center</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text placement relative to its calculated anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.align</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">center</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text alignment inside the label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.offset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited labelGap / graph label_offset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label offset and padding used in the automatic gap; fallback 0.75rem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.badge</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Render the text as a badge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.badgeColor</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">segment color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Override the badge color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.justify</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Justify the text.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stepLabel.maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Maximum displayed lines. The automatic gap calculation counts at most three lines.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the line sections and default label badge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">gradient</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fade the line sections along their direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dashed</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional dashed line sections, passed through to segments.path.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">toColor</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Target color for a color gradient.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">colorGradient</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable a gradient between color and toColor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">line</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Palette tone of the line sections.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zIndex</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stacking-order override for the line sections.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable line diagnostic boxes and node counters; overridden by :dev.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Step segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- segment-step-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-segments-step"
                    :dev="true"
                    :coordinates="true"
                    min-height="22rem"
                    min-width="24rem"
                    horizontal-padding="10rem"
                >
                    <x-translation-workbench::ui.tw-graph.segments.step
                        :segment="[
                            'id' => 'literature.segments.step',
                            'direction' => 'bottom-top',
                            'anchorStart' => ['x' => '0rem', 'y' => '4rem'],
                            'beforeLength' => '3rem',
                            'afterLength' => '3rem',
                            'nodeStart' => true,
                            'nodeEnd' => true,
                            'stepCaps' => true,
                            'stepLabel' => [
                                'text' => ['Source inactive', 'Awaiting review'],
                                'width' => 'default',
                                'align' => 'center',
                            ],
                            'color' => 'cyan',
                        ]"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- segment-step-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-step.blade.php
        </flux:field>
    </flux:callout>
</section>
