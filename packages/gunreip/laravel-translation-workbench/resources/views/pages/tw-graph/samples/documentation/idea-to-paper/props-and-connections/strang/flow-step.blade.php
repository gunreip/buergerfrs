<section class="min-w-0 space-y-4" id="reference-strang-flow-step">
    <flux:heading size="lg">strang.flow-step — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-step" />
    <flux:text>Places an action label between two independently sized stems using segments.step.</flux:text>
    <flux:callout icon="information-circle" color="indigo">
        <flux:callout.heading>Reading this reference</flux:callout.heading>
        <flux:text>Attributes use kebab-case; nested keys use camelCase. Open each array to see its children with complete paths. [n] denotes a numbered map and [] a list item. The declarations here are separate from the example-specific Props and connections tables.</flux:text>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="red" icon="book-open-check">
        <flux:callout.heading>Public component props</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>Prop / path</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Default / fallback</flux:table.column>
                <flux:table.column>Meaning and limits</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">id</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Unique authoring ID and prefix for anchors/tooltips. Null uses the component-specific generated ID.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">component-counter</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Number used in generated IDs; normalized to at least 1. Supply explicit IDs for repeated components.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">attach-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Existing anchor in the same canvas; see Connections for whether this attaches the input or aligns the output.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">anchor-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{&quot;x&quot;: &quot;0rem&quot;, &quot;y&quot;: &quot;0rem&quot;}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Fallback/input coordinates. Expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">before-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Stem before the step text; flow-step null becomes 2rem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">before-color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">color name | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit incoming-stem color; null keeps component color. Independent continuation steps do not automatically inherit previous action colors.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">label-gap</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit space around step text. Null computes a gap from text lines and label offset.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">after-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Stem after the step text; flow-step null becomes 2rem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">step-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Action text between before/after stems; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-labels</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Node annotation definitions; expand below for this component’s shape.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Render endpoint marker; supplied node labels can require a visible node.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-end-dot</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit endpoint Dot choice. Null derives it from node/label configuration.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">step-caps</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Draw caps bordering the step text.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">cap-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Cap length; null uses canvas cap-length (1.75rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">color name | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Overrides inherited canvas color; final fallback zinc.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">counter-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">S</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">First DEV counter caption/number.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">counter-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Endpoint DEV counter caption/number.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">z-index</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">joint-arrow-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Render a technical end arrow when node-end is true and node-end-dot is false. Its direction follows the step direction; the anchor and DEV counter remain available.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="green" icon="brackets">
        <flux:callout.heading>Array props</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>anchor-start</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Coordinates in the current canvas; positive y points upwards.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">anchor-start.x</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">0rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Horizontal coordinate.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">anchor-start.y</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">0rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Vertical coordinate.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>step-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Options accepted for this step label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.side</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">step-label.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>node-labels</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Node labels at the decision/step endpoint. The end wrapper is optional where supported.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.end</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">direct left/right slots</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Endpoint labels.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>node-labels.end</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Endpoint annotation slots. Each side contains its own text-label array.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels.end.left</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels.end.right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>node-labels.end.left</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>Options accepted for this node label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>Prop / path</flux:table.column>
                                                    <flux:table.column>Type</flux:table.column>
                                                    <flux:table.column>Default / fallback</flux:table.column>
                                                    <flux:table.column>Meaning and limits</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.left.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>node-labels.end.right</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>Options accepted for this node label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>Prop / path</flux:table.column>
                                                    <flux:table.column>Type</flux:table.column>
                                                    <flux:table.column>Default / fallback</flux:table.column>
                                                    <flux:table.column>Meaning and limits</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">node-labels.end.right.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
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
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.flow-step
    id=&quot;example.flow-step&quot;
    :step-label=&quot;[&#x27;text&#x27; =&gt; [&#x27;Validate input&#x27;], &#x27;width&#x27; =&gt; &#x27;default&#x27;]&quot;
    before-length=&quot;2rem&quot;
    after-length=&quot;3rem&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.</flux:text>
        <flux:text>before-length and after-length independently default to 2rem. Automatic gap uses text line count and label offset; an explicit label-gap overrides it. Explicit before-color controls only the incoming stem.</flux:text>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="amber" icon="cable">
        <flux:callout.heading>Connections</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns sticky>
                <flux:table.column>Prop / path</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Default / fallback</flux:table.column>
                <flux:table.column>Meaning and limits</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Endpoint after the after-length stem.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>Use resolvable lengths and unique IDs. This reference describes fields consumed by this component chain; unknown array keys are not generally validated and may have no effect.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../props-and-connections/strang/flow-step.blade.php</flux:field>
</section>
