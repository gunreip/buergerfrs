<section class="min-w-0 space-y-4" id="reference-parts-split">
    <flux:heading size="lg">{{ __('parts.split — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="parts.split" />
    <flux:callout color="indigo" icon="information-circle">
        <flux:callout.heading>{{ __('Binary split') }}</flux:callout.heading>
        <flux:text>{{ __('One horizontal input branches into two horizontal outputs. Existing segments.fusion build the outgoing bends; the split itself evaluates no condition. DEV and coordinates come exclusively from the canvas.') }}</flux:text>
    </flux:callout>
    <flux:callout color="indigo" icon="variable" class="min-w-0">
        <flux:callout.heading>{{ __('Public component props') }}</flux:callout.heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default') }}</flux:table.column>
                <flux:table.column>{{ __('Purpose') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>id</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>part.split</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Prefix for input and named output anchors.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>anchor-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>required</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Input coordinate with x and y resolving to rem.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>direction</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>right-left</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Horizontal flow: right-left or left-right. Both outputs lie ahead of the input.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>arc-radius</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>1.375rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Preferred radius. FusionGeometry resolves one common radius for both outward bends.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>min-stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>1rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Minimum nonzero compensator length. Invalid unequal offsets that leave a short compensator are rejected.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>canvas / zinc</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Input and fallback route color.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Input number; outputs receive the next two numbers in declaration order.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>20</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Layer for the split paths; endpoint dots sit one level higher.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>required</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Exactly two entries: one positive and one negative offset. No additional outputs are silently accepted.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout color="indigo" icon="variable" class="min-w-0">
        <flux:callout.heading>{{ __('Array props') }}</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>outputs[]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default') }}</flux:table.column>
                <flux:table.column>{{ __('Purpose') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].key</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>required</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Unique nonempty key; creates id.outputs.KEY.anchorNode-end.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].offset</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>required</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Nonzero vertical displacement from the input, positive upward and negative downward.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>component color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Explicit color of this outgoing route and endpoint.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>array / null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Optional informational text label and connector at the output Dot.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].label.text</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>string / array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>required for label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('One or more text lines.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].label.width</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>textLabel default</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('half, default, halfLong or long.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].label.side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>top</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Position around the horizontal output; top or bottom.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].label.align</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>textLabel default</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('left, center or right text alignment.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs[].label.connectorLength / connectorGap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>canvas defaults</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Length and gap of the informational label connector.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
                    <pre class="overflow-x-auto"><code>:outputs="[
    ['key' =&gt; 'true', 'offset' =&gt; '4rem', 'color' =&gt; 'green'],
    ['key' =&gt; 'false', 'offset' =&gt; '-4rem', 'color' =&gt; 'red'],
]"</code></pre>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:callout>
    <flux:callout color="indigo" icon="variable" class="min-w-0">
        <flux:callout.heading>{{ __('Connections') }}</flux:callout.heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default') }}</flux:table.column>
                <flux:table.column>{{ __('Purpose') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>anchorNode-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>input</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('The single input. There is deliberately no ambiguous shared anchorNode-end.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal"><code>outputs.KEY.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal"><code>output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Attach each independently authored continuation to its own named output.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('The canvas supplies diagnostic settings and the fallback color. Each output may set its own color. Both bends use one calculated radius, resolved from the smaller absolute offset, arc-radius and min-stem-length. The horizontal displacement is twice that radius; vertical positions follow the signed output offsets.') }}</flux:text>
    </flux:callout>
    <flux:callout color="fuchsia" icon="information-circle">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('Exactly two outputs with unique nonempty keys and nonzero offsets are required, one above and one below the input. Coordinates and lengths must resolve to rem values. The radius must be positive and min-stem-length nonnegative. Unequal offsets that leave a short compensator are rejected; adjust the authored offsets or radius.') }}</flux:text>
    </flux:callout>
</section>
