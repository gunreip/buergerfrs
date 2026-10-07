<section class="min-w-0 space-y-4" id="reference-parts-fusion">
    <flux:heading size="lg">{{ __('parts.fusion — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="parts.fusion" />
    <flux:text>{{ __('Merges two or more parallel horizontal inputs into one output; outer lanes converge into inner lanes.') }}</flux:text>
    <flux:callout icon="information-circle" color="indigo">
        <flux:callout.heading>{{ __('Reading this reference') }}</flux:callout.heading>
        <flux:text>{{ __('Attributes use kebab-case; nested keys use camelCase. Open each array to see its children with complete paths. [n] denotes a numbered map and [] a list item. The declarations here are separate from the example-specific Props and connections tables.') }}</flux:text>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="red" icon="book-open-check">
        <flux:callout.heading>{{ __('Public component props') }}</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>id</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>part.fusion</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Unique authoring ID and prefix for anchors/tooltips. Null uses the component-specific generated ID.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>inputs</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Two or more horizontal input anchors; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>direction</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>right-left</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>arc-radius</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1.375rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Arc radius; null uses the shared arc radius (2.75rem).') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>min-stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Minimum compensator stem in fusion planning; short stems may collapse to zero with adjusted radius.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides inherited canvas color; final fallback zinc.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>dev-counter-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Endpoint DEV counter caption; false suppresses it.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>20</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base drawing layer for this component.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="green" icon="brackets">
        <flux:callout.heading>{{ __('Array props') }}</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>inputs[]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('At least two parallel horizontal inputs; each input owns its key, anchor and optional color.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>inputs[].key</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Input index</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Identifier used beneath {id}.inputs.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>inputs[].anchor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>array{x,y}</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Input coordinates; expand below.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>inputs[].color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Lane color before the shared output.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>inputs[].anchor</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Coordinates in the current canvas; positive y points upwards.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>inputs[].anchor.x</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal coordinate.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>inputs[].anchor.y</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Vertical coordinate.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.parts.fusion
    id=&quot;example.fusion&quot;
    :inputs=&quot;[
        [&#x27;key&#x27; =&gt; &#x27;a&#x27;, &#x27;anchor&#x27; =&gt; [&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]],
        [&#x27;key&#x27; =&gt; &#x27;b&#x27;, &#x27;anchor&#x27; =&gt; [&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;6rem&#x27;]],
    ]&quot;
    direction=&quot;left-right&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.') }}</flux:text>
        <flux:text>{{ __('The output lies between the outer inputs. Odd groups have a straight center lane; even groups join between the middle inputs. Radius and compensator stem are planned together; short compensators can collapse to zero with a larger radius.') }}</flux:text>
        <flux:text>{{ __('Direction must be horizontal. Input ordering and coordinates determine outer/inner lanes; output color uses the component color while input arms can keep individual colors.') }}</flux:text>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="amber" icon="cable">
        <flux:callout.heading>{{ __('Connections') }}</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.inputs.{key}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Input coordinate</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Registered input anchor.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Shared output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Attach the common action/continuation here.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('Requires at least two inputs, resolvable rem coordinates/radius/min-stem-length, positive radius and left-right or right-left direction.') }}</flux:text>
    </flux:callout>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/parts/fusion.blade.php"
        segments="3"
    />
</section>
