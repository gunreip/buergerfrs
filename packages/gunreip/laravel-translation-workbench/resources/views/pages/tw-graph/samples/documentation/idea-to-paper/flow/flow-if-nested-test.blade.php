<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Flow if nested test') }}</flux:callout.heading>
        <flux:callout.text>{{ __('The outer IF is assembled from individual flow components. Its THEN output enters an independent inner IF / ELSEIF / ENDIF block, whose ENDIF returns to the outer ENDIF. The outer ELSEIF and ELSE branches skip the inner block and join its result before the common outer ENDIF.') }}</flux:callout.text>
        @php
            $docExampleSource = file_get_contents(\Illuminate\Support\Facades\View::getFinder()->find(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-nested-test',
            ));
            if (preg_match('/^[ \t]*\{\{-- flow-if-nested-test-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- flow-if-nested-test-example-1:end --\}\}/ms', $docExampleSource, $docExample1Match) !== 1) {
                throw new \LogicException('Missing flow-if-nested-test-example-1 source markers.');
            }
            $docExample1Lines = explode("\n", rtrim($docExample1Match[1]));
            $docExample1Indent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($docExample1Lines, fn (string $line): bool => trim($line) !== ''),
            ));
            $docExample1Code = implode("\n", array_map(
                fn (string $line): string => substr($line, $docExample1Indent),
                $docExample1Lines,
            ));
        @endphp
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{!! str_replace(
                ['side=&quot;left&quot;', 'side=&quot;right&quot;'],
                ['<span class="text-lime-300">side=&quot;left&quot;</span>', '<span class="text-lime-300">side=&quot;right&quot;</span>'],
                e($docExample1Code),
            ) !!}</code></pre>
        </div>
        <flux:heading class="mt-4" size="sm">{{ __('Flow if nested test props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Array keys') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">auto id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable element prefix for every flow strand, including labels, DEV identifiers, bounds, and attach targets.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">component-counter</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional DEV/component counter value used by flow-start, flow-step, and flow-decision.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Main flow direction. The first flow examples stay vertical; later decision branches can turn left or right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">attach-to</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Existing anchor id to continue from. Used by flow-step, flow-decision, flow-if-* and if-else-endif.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">x, y</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Manual start coordinate when no attach-to anchor is used.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited graph color / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Flow color inherited from tw-graph unless the flow component overrides it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">20</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Layer order for the flow component and its segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-mode</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited :dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional local DEV override for counters and debug helpers.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-counter-color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">zinc / inherited</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Badge color for DEV node counters where the component exposes it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, side, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Flow-start label that names the entry point, for example a process start, first milestone, or initial state.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left, right -&gt; text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional left/right facts at the flow-start end anchor, using the same text-label structure as trunk and branch labels.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-label-left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Shorthand single-side labels for flow-start when a full start-node-labels array would be too much.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-label-right</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Shorthand single-side labels for flow-start when a full start-node-labels array would be too much.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the first visible flow-start stem before the first anchor node.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the first visible flow-start stem before the first anchor node.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-end</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true / null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls whether the end anchor exists and whether its visible dot is rendered.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-end-dot</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true / null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls whether the end anchor exists and whether its visible dot is rendered.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-image</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">src, size, alt, color, zIndex</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional image marker at a flow-start node, following the generic tw-graph node image structure.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">before-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem / 2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Lengths around a flow-step label. The label gap is calculated from the step label height unless label-gap is set explicitly.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">after-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem / 2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Lengths around a flow-step label. The label gap is calculated from the step label height unless label-gap is set explicitly.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label-gap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph label-gap / calculated</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Explicit gap around a flow-step label; otherwise the step uses the label height.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:step-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Centered label that names the process step, status, or decision reason.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end -&gt; left/right -&gt; text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional facts at the flow-step or flow-decision end anchors. For flow-step the public anchor is usually end.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">step-caps</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true / graph cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the small caps around a flow-step label and their length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true / graph cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the small caps around a flow-step label and their length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Radius/size of flow-decision and flow-if arcs. arc-size is the current canonical prop.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Radius/size of flow-decision and flow-if arcs. arc-size is the current canonical prop.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge-length / label-bridge minimum</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Common bridge length for flow-decision and the flow-if label bridges unless a more specific bridge prop overrides it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Side-specific bridge lengths for flow-decision.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right-bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Side-specific bridge lengths for flow-decision.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">extension</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional additional reach for flow-decision branches.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-extension</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional additional reach for flow-decision branches.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right-extension</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional additional reach for flow-decision branches.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:decision-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, side, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Centered label at the decision anchor. The flow continues only through the left/right sideways branches.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">decision-label-side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default side for a flow-decision label when the label itself does not set side.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path-tone</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">surface</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Tone used by flow-if paths, arcs, and label bridges.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Specific bridge lengths for the if-else-endif wrapper: intro, condition rows, and ENDIF.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">condition-bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Specific bridge lengths for the if-else-endif wrapper: intro, condition rows, and ENDIF.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Specific bridge lengths for the if-else-endif wrapper: intro, condition rows, and ENDIF.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bypass</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Connects the IF entry rail directly to the ENDIF output when the condition is not met.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">then-arc-reach</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Horizontal reach of the THEN/output arc in a flow-if condition row.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical lengths for the ELSE/next-condition rail and the THEN/output continuation.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">then-stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical lengths for the ELSE/next-condition rail and the THEN/output continuation.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-stem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enables or suppresses the left rail continuation in a flow-if condition row.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">then-continuation</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem, arc-east-north, none</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Defines how the THEN/output side continues after the condition label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:intro-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;text&#x27; =&gt; [&#x27;IF / ELSE flow&#x27;, &#x27;section starts&#x27;]]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, side, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Opening label for flow-if-start or the if-else-endif wrapper. Optional side (left/right) overrides the direction of the intro arcs and bridges; omitted side preserves the component direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:if-condition-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label for the first IF condition row inside flow-if-condition-set or if-else-endif.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:elseif-conditions</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">key, id, color, label, conditionLabel, conditionRailWidth, arcSize, bridgeLength, thenArcReach, leftStemLength, thenStemLength, leftStem, thenContinuation, pathTone, zIndex, devMode, devCounterColor</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Rows after the first IF row. Each entry may be ELSEIF, ELSE, DEFAULT, or any handmade condition label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:conditions</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">key, id, color, label, conditionLabel, conditionRailWidth, arcSize, bridgeLength, thenArcReach, leftStemLength, thenStemLength, leftStem, thenContinuation, pathTone, zIndex, devMode, devCounterColor</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Rows after the first IF row. Each entry may be ELSEIF, ELSE, DEFAULT, or any handmade condition label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">condition-rail-width</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">widest condition label width</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Shared label width for IF/ELSEIF/ELSE rows so the condition rail remains aligned.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:end-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;text&#x27; =&gt; [&#x27;ENDIF&#x27;]]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, side, align, justify, color, badgeColor, connectorLength, connectorGap, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Closing label rendered by flow-if-end or the if-else-endif wrapper. Optional side (left/right) overrides the ENDIF direction, including the final THEN turn in the wrapper. Omitted side preserves existing behavior.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">if-id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">derived from id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stable id prefixes for the generated IF and ELSEIF condition rows.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">elseif-id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">derived from id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stable id prefixes for the generated IF and ELSEIF condition rows.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <div
        data-nested-flow-tools
        x-data="{ nestedDev: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev ?? true))).as('tw-graph-preview-dev'), nestedBoxes: $persist(true).as('tw-graph-preview-boxes'), nestedCoordinates: $persist(@js(\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($coordinates ?? false))).as('tw-graph-preview-coordinates') }"
        :class="{ 'tw-graph-protocol-dev-disabled': !nestedDev, 'tw-graph-protocol-coordinates-disabled': !nestedCoordinates }"
        :data-boxes="nestedBoxes ? 'on' : 'off'"
    >
        <div class="sticky top-0 z-50 flex flex-wrap items-center gap-4 bg-white p-3 text-sm dark:bg-zinc-900">
            <label class="flex items-center gap-2"><input
                    type="checkbox"
                    x-model="nestedDev"
                > {{ __('DEV mode') }}</label>
            <label class="flex items-center gap-2"><input
                    type="checkbox"
                    x-model="nestedBoxes"
                > {{ __('Bounding boxes') }}</label>
            <label class="flex items-center gap-2"><input
                    type="checkbox"
                    x-model="nestedCoordinates"
                > {{ __('Coordinates') }}</label>
            <span class="text-zinc-500">{{ __('Cyan: outer IF · Amber: inner IF · Red: outer IF not met') }}</span>
            <div class="ml-auto shrink-0">
                <flux:button
                    type="button"
                    variant="ghost"
                    size="sm"
                    square
                    icon="arrow-path"
                    :aria-label="__('Refresh preview')"
                    :tooltip="__('Refresh preview')"
                    wire:click="$refresh"
                    wire:loading.attr="disabled"
                    wire:target="$refresh"
                />
            </div>
        </div>
        <style>
            [data-nested-flow-tools][data-boxes="off"] [data-tw-graph-dev-box] {
                display: none;
            }
        </style>
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- flow-if-nested-test-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-test"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    slot-min-height="64rem"
                    horizontal-padding="12rem"
                    min-width="72rem"
                    min-height="64rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-test-origin"
                        :anchor-start="['x' => '0rem', 'y' => '0rem']"
                        :start-label="['text' => ['Flow start'], 'width' => 'half', 'align' => 'center']"
                    />

                    {{-- Outer IF: its THEN output enters the inner section. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-start
                        id="literature.flow.1.if-nested-test.start"
                        attach-to="literature.flow.1.if-nested-test-origin.anchorNode-end"
                        side="left"
                        bridge-length="9.75rem"
                        :intro-label="['text' => ['Review request'], 'width' => 'half', 'align' => 'center']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-condition
                        id="literature.flow.1.if-nested-test.if"
                        attach-to="literature.flow.1.if-nested-test.start.anchorNode-end"
                        side="left"
                        bridge-length="1.75rem"
                        :left-stem="false"
                        then-continuation="stem"
                        then-stem-length="4rem"
                        :condition-label="[
                            'text' => ['IF review is required', 'THEN enter result evaluation'],
                            'width' => 'halfLong',
                            'align' => 'left',
                        ]"
                    />

                    {{-- Inner IF / ELSEIF / ENDIF: independently authored and adjustable. --}}
                    <x-translation-workbench::ui.tw-graph.strang.if-else-endif
                        id="literature.flow.1.if-nested-test.inner"
                        attach-to="literature.flow.1.if-nested-test.if.then.anchorNode-end"
                        side="left"
                        color="amber"
                        start-bridge-length="5.75rem"
                        condition-bridge-length="1.75rem"
                        end-bridge-length="1.75rem"
                        :intro-label="['text' => ['Evaluate review result'], 'width' => 'half', 'align' => 'center']"
                        :if-condition-label="[
                            'text' => ['IF result is green', 'THEN approve'],
                            'width' => 'default',
                            'align' => 'left',
                        ]"
                        :elseif-conditions="[
                            [
                                'key' => 'revise',
                                'label' => [
                                    'text' => ['ELSEIF result is amber', 'THEN revise'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                            [
                                'key' => 'else',
                                'leftStemLength' => '0rem',
                                'thenContinuation' => 'arc-west-north',
                                'label' => ['text' => ['ELSE reject'], 'width' => 'default', 'align' => 'left'],
                            ],
                        ]"
                        :end-label="['text' => ['Inner ENDIF'], 'side' => 'right', 'width' => 'half', 'align' => 'center']"
                    />

                    {{-- Only an unmet outer IF condition continues here to the outer ELSEIF branches. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-test';
                        $innerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-test.inner.endif.anchorNode-end',
                        );
                        $outerRailStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-test.start.anchorNode-end',
                        );
                        $outerElseifStart = [
                            'x' => $outerRailStart['x'],
                            'y' => 'calc(' . $innerExit['y'] . ' + 6rem)',
                        ];
                    @endphp
                    <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                        'id' => 'literature.flow.1.if-nested-test.condition-rail',
                        'direction' => 'bottom-top',
                        'anchorStart' => $outerRailStart,
                        'anchorEnd' => $outerElseifStart,
                        'length' => 'calc(' . $outerElseifStart['y'] . ' - ' . $outerRailStart['y'] . ')',
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'devCounterEnd' => 10,
                        'color' => 'red',
                        'tone' => 'surface',
                        'dev' => true,
                    ]" />
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-condition
                        id="literature.flow.1.if-nested-test.elseif.automatic"
                        :counter-condition="11"
                        :counter-bridge-end="12"
                        :counter-then-arc-end="13"
                        :counter-then-end="14"
                        :counter-left-end="15"
                        :anchor-start="$outerElseifStart"
                        side="left"
                        bridge-length="1.75rem"
                        condition-rail-width="halfLong"
                        left-stem-length="6rem"
                        then-stem-length="6rem"
                        then-continuation="stem"
                        :condition-label="[
                            'text' => ['ELSEIF automatic approval is allowed', 'THEN approve directly'],
                            'width' => 'halfLong',
                            'align' => 'left',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-if-condition
                        id="literature.flow.1.if-nested-test.elseif.deferred"
                        :counter-condition="16"
                        :counter-bridge-end="17"
                        :counter-then-arc-end="18"
                        :counter-then-end="19"
                        :counter-left-end="20"
                        attach-to="literature.flow.1.if-nested-test.elseif.automatic.left.anchorNode-end"
                        side="left"
                        bridge-length="1.75rem"
                        condition-rail-width="halfLong"
                        left-stem-length="6rem"
                        then-stem-length="6rem"
                        then-continuation="stem"
                        :condition-label="[
                            'text' => ['ELSEIF review can wait', 'THEN defer'],
                            'width' => 'halfLong',
                            'align' => 'left',
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.flow-if-condition
                        id="literature.flow.1.if-nested-test.else"
                        :counter-condition="21"
                        :counter-bridge-end="22"
                        :counter-then-arc-end="23"
                        :counter-then-end="24"
                        :counter-left-end="25"
                        attach-to="literature.flow.1.if-nested-test.elseif.deferred.left.anchorNode-end"
                        side="left"
                        bridge-length="1.75rem"
                        condition-rail-width="halfLong"
                        left-stem-length="6rem"
                        then-stem-length="6rem"
                        then-continuation="stem"
                        :left-stem="false"
                        :condition-label="[
                            'text' => ['ELSE reject the request'],
                            'width' => 'halfLong',
                            'align' => 'left',
                        ]"
                    />

                    @php
                        $outerJoin = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-test.else.then.anchorNode-end',
                        );
                        $returnRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                            'arc_size',
                            '2.75rem',
                        );
                        $outerEndStart = [
                            'x' => 'calc(' . $outerJoin['x'] . ' - ' . $returnRadius . ')',
                            'y' => 'calc(' . $outerJoin['y'] . ' + ' . $returnRadius . ')',
                        ];
                    @endphp
                    {{-- Completed inner branch joins the outer result rail, never the next condition. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-bypass
                        id="literature.flow.1.if-nested-test.inner-return"
                        attach-to="literature.flow.1.if-nested-test.inner.endif.anchorNode-end"
                        merge-to="literature.flow.1.if-nested-test.else.then.anchorNode-end"
                        side="right"
                    />
                    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                        'id' => 'literature.flow.1.if-nested-test.return.arc-east-north',
                        'startAnchor' => 'e',
                        'endAnchor' => 'n',
                        'arcSize' => $returnRadius,
                        'anchorStart' => $outerJoin,
                        'anchorEnd' => $outerEndStart,
                        'nodeEnd' => true,
                        'nodeEndDot' => false,
                        'jointArrowEnd' => true,
                        'jointArrowEndDirection' => 'left',
                        'devCounterEnd' => 'R',
                        'color' => 'cyan',
                        'tone' => 'surface',
                        'dev' => true,
                    ]" />
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-end
                        id="literature.flow.1.if-nested-test.endif"
                        :anchor-start="$outerEndStart"
                        side="left"
                        bridge-length="1.75rem"
                        :end-label="['text' => ['Outer ENDIF'], 'width' => 'half', 'align' => 'center']"
                    />

                    @php
                        $anchor = fn(
                            string $suffix,
                        ): array => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-test.' . $suffix,
                        );
                        $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [
                                $anchor('if.then.anchorNode-end'),
                                $anchor('inner.start.anchorNode-end'),
                                $anchor('inner.if.then.anchorNode-end'),
                                $anchor('inner.elseif.revise.then.anchorNode-end'),
                                $anchor('inner.elseif.else.then.anchorNode-end'),
                                $anchor('inner.endif.anchorNode-end'),
                            ],
                            '1.5rem',
                        );
                        $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [
                                $anchor('start.anchorNode-end'),
                                $anchor('if.then.anchorNode-end'),
                                $anchor('inner.start.anchorNode-end'),
                                $anchor('inner.endif.anchorNode-end'),
                                ['x' => $innerExit['x'], 'y' => $outerJoin['y']],
                                $outerElseifStart,
                                $anchor('elseif.automatic.then.anchorNode-end'),
                                $anchor('elseif.deferred.then.anchorNode-end'),
                                $anchor('else.then.anchorNode-end'),
                                $outerEndStart,
                                $anchor('endif.anchorNode-end'),
                            ],
                            '3rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-test.inner.bounds"
                        label="Inner IF / ELSEIF / ENDIF"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                        metrics-scope="canvas"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-test.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                        metrics-scope="canvas"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-test-example-1:end --}}
                    </div>
                </div>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/flow-if-nested-test.blade.php</flux:field>
    </flux:callout>
</section>
