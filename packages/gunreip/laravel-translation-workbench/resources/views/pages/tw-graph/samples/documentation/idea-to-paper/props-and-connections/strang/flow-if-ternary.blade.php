<section class="min-w-0 space-y-4" id="reference-strang-flow-if-ternary">
    <flux:heading size="lg">{{ __('strang.flow-if-ternary — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-if-ternary" />
    <flux:text>{{ __('Two value-producing alternatives. A semantic wrapper around flow-if-else; the graph itself does not execute the expression.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Unique authoring ID and prefix for anchors/tooltips. Null uses the component-specific generated ID.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>component-counter</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Number used in generated IDs; normalized to at least 1. Supply explicit IDs for repeated components.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Destination side: left moves left; right moves right, independently of the vertical direction.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>attach-to</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Existing anchor in the same canvas; see Connections for whether this attaches the input or aligns the output.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>{&quot;x&quot;: &quot;0rem&quot;, &quot;y&quot;: &quot;0rem&quot;}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Fallback/input coordinates. Expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>direction</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bottom-top</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>before-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Stem before the step text; flow-step null becomes 2rem.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>label-gap</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit space around step text. Null computes a gap from text lines and label offset.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>after-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Stem after the step text; flow-step null becomes 2rem.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>step-caps</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Draw caps bordering the step text.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>cap-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Cap length; null uses canvas cap-length (1.75rem).') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>arc-radius</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Arc radius; null uses the shared arc radius (2.75rem).') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default bridge length. Null normally resolves through canvas bridge-length / line-length; IF uses normalized label-bridge lengths.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>true-bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('True lane bridge baseline; takes precedence over leftBridgeLength and bridgeLength.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('False lane bridge baseline; takes precedence over rightBridgeLength and bridgeLength.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>8rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default vertical stem length. Null resolves through canvas stem-length / line-length (normally 4rem).') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node annotation definitions; expand below for this component’s shape.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render endpoint marker; supplied node labels can require a visible node.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides inherited canvas color; final fallback zinc.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer | string | false | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>D</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('First DEV counter caption/number.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>left-counter-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('DEV counter for the true route endpoint; naming is independent of mirrored side.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>false-stem-counter</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>2</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('DEV counter for the false entry stem.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>right-counter-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('DEV counter for the false route endpoint.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>20</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base drawing layer for this component.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>condition-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>{&quot;text&quot;: [&quot;condition?&quot;], &quot;width&quot;: &quot;halfLong&quot;}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Condition/question step label; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>if-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>{&quot;text&quot;: [&quot;value if true&quot;], &quot;width&quot;: &quot;half&quot;}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('First successful IF action; expand routing and label options below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>if-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>{&quot;text&quot;: [&quot;value if false&quot;], &quot;width&quot;: &quot;half&quot;}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Final ELSE action or text-free bypass; expand below.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="green" icon="brackets">
        <flux:callout.heading>{{ __('Array props') }}</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>anchor-start</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start.x</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal coordinate.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start.y</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Vertical coordinate.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>node-labels</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Node labels at the decision/step endpoint. The end wrapper is optional where supported.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>direct left/right slots</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Endpoint labels.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>node-labels.end</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Endpoint annotation slots. Each side contains its own text-label array.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>node-labels.end.left</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>{{ __('Options accepted for this node label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.left.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>node-labels.end.right</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>{{ __('Options accepted for this node label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                                    <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>node-labels.end.right.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                </flux:accordion>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>condition-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Options accepted for this step label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | right | top | bottom | center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>step: center; start/end: derived from direction</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Placement relative to the owning anchor; does not mirror the component.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas label-offset → 0.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text offset; for step labels also affects calculated text gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label.half</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>if-start</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Options accepted for this action label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.return</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('false opens this route for a nested block. Author the return explicitly.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.returnOffset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>False/open ELSEIF: 12rem; first true route: no offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal extra space when return=false. Single ELSEIF first action does not read this field.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>if-start.lineJumps[]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-start.lineJumps[].over</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('ID of an existing perpendicular stem/bridge intersecting this line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-start.lineJumps[].radius</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>0.5rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Must fit between endpoints and exceed half the line thickness.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-start.lineJumps[].side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>top | bottom</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>top</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Side of the semicircle relative to its owning line.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>if-start.stemLineJumps[]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-start.stemLineJumps[].over</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('ID of an existing perpendicular stem/bridge intersecting this line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-start.stemLineJumps[].radius</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>0.5rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Must fit between endpoints and exceed half the line thickness.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-start.stemLineJumps[].side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Side of the semicircle relative to its owning line.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>if-end</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Options accepted for this action label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.return</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('false opens this route for a nested block. Author the return explicitly.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.returnOffset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>False/open ELSEIF: 12rem; first true route: no offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Horizontal extra space when return=false. Single ELSEIF first action does not read this field.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.stemLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('False entry stem; layout may enforce minimum clearance.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-end.returnLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>8rem when open</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Extra vertical return distance when return=false.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>if-end.lineJumps[]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-end.lineJumps[].over</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>required</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('ID of an existing perpendicular stem/bridge intersecting this line.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-end.lineJumps[].radius</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>0.5rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Must fit between endpoints and exceed half the line thickness.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>if-end.lineJumps[].side</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>top | bottom</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>top</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Side of the semicircle relative to its owning line.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.flow-if-ternary
    id=&quot;example.flow-if-ternary&quot;
    :condition-label=&quot;[&#x27;text&#x27; =&gt; [&#x27;IF ready?&#x27;]]&quot;
    :if-start=&quot;[&#x27;text&#x27; =&gt; [&#x27;Publish&#x27;], &#x27;color&#x27; =&gt; &#x27;green&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.') }}</flux:text>
        <flux:text>{{ __('Label widths are aligned into a common route span. False stem clearance is at least the requested stemLength and may grow to fit action text. LabelBridge normalizes explicit plain-rem bridge lengths (minimum 0.25rem; small intermediate values become 1.15rem).') }}</flux:text>
        <flux:text>{{ __('The successful action and fallback can leave open outputs via return=false. ReturnColorRegistry propagates explicitly connected return colors through the outer rail. The internal return-color composition prop is not a public authoring option.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Decision anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Published after the question, not the incoming coordinate.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-decision</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Decision anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Branch point after condition text.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Common output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connect the next independent component here.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.false.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Fallback action output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('When open, attach nested work here.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.false.anchorNode-return</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Fallback return target</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Join the explicit fallback return here.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.true.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Successful action output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Attach nested work when return=false.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.true.anchorNode-return</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Return target</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit nested return rejoins here.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('IF diagrams do not evaluate expressions or execute actions. Changing label text does not change routing. Use return, if-end text and elseifs to describe topology.') }}</flux:text>
    </flux:callout>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/strang/flow-if-ternary.blade.php"
        segments="3"
    />
</section>
