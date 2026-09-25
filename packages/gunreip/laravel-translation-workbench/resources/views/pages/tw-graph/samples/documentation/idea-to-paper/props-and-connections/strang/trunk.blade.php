<section class="min-w-0 space-y-4" id="reference-strang-trunk">
    <flux:heading size="lg">strang.trunk — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.trunk" />
    <flux:text>Main numbered graph line: start, configurable stems, then capped end.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">anchor-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{&quot;x&quot;: &quot;0rem&quot;, &quot;y&quot;: &quot;0rem&quot;}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Fallback/input coordinates. Expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">color name | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Overrides inherited canvas color; final fallback zinc.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">start-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Initial segment length; see geometry section for family-specific resolution.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default vertical stem length. Null resolves through canvas stem-length / line-length (normally 4rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-count</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Number of trunk stems. Omitted count uses default-path-segments (10); values are clamped to at least zero.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-lengths</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-stem length overrides, indexed by one-based number or supplied as a list.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-labels</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Node annotation definitions; expand below for this component’s shape.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">default-path-segments</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">10</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default number of numbered trunk positions when stem-count is omitted.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">end-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Final capped segment length; see geometry section.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">end-cap-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">End cap length passed to the trunk end segment.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">start-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Text near the starting end; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">end-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Text near the capped end; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">start-node-labels</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Annotation slots at the start segment’s output.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">start-label-space</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">3rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Extra space reserved around the trunk start label.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">start-shift-enabled</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Enable a distinct starting spacer. Null reads this family’s canvas start-shift setting.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">start-shift-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Starting spacer length; applied only when shifting is enabled.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">z-index</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">counter-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">First DEV counter caption/number.</flux:table.cell>
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
                <flux:accordion.heading>stem-lengths[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>One-based trunk stem definitions. Each can carry its own label configuration.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Default path length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Logical stem length.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].component</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">path | stem-compressed</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">path</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Rendering component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].beforeLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Renderer default</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Visible piece before a compressed gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].gapLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Renderer default</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Compressed gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].afterLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Renderer default</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Visible piece after the gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].capLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas cap-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Cap override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool | array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node visibility or label slots.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>stem-lengths[n].labels</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional top label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional bottom label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.left</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.left.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.right</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.right.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.top</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.top.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.bottom</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-lengths[n].labels.bottom.half</flux:table.cell>
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
            <flux:accordion.item>
                <flux:accordion.heading>node-labels[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>One-based node numbers.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels[n].left</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels[n].right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>node-labels[n].left</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].left.half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>node-labels[n].right</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">node-labels[n].right.half</flux:table.cell>
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
            <flux:accordion.item>
                <flux:accordion.heading>start-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Options accepted for this start label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.side</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-label.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>end-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Options accepted for this end label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.side</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">end-label.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>start-node-labels</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">start-node-labels.left</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">start-node-labels.right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>start-node-labels.left</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.left.half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>start-node-labels.right</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.connectorLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.connectorGap</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">start-node-labels.right.half</flux:table.cell>
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
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id=&quot;example.trunk&quot;
    stem-count=&quot;3&quot;
    :stem-lengths=&quot;[1 =&gt; &#x27;4rem&#x27;, 2 =&gt; &#x27;6rem&#x27;, 3 =&gt; &#x27;4rem&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.</flux:text>
        <flux:text>Terminal start-label/end-label null produces the default Path/start and Path/end captions. Use false to hide them. Unlike most custom labels, supplied terminal label arrays are merged with these terminal defaults.</flux:text>
        <flux:text>A numbered sequence combines start, stems and end. Explicit stem-count/stem-lengths control the number/length of stems; start shifts and label space add to total bounds.</flux:text>
        <flux:text>Generated canonical IDs use trunk.center.{componentCounter}. These numbered anchors are used by the traditional merge/branch/rekey families.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">trunk.center.{componentCounter}.start.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Start output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Start/stem boundary.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">trunk.center.{componentCounter}.stem-{n}.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Numbered stem end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Attachment target for branches or merges.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">trunk.center.{componentCounter}.end.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Trunk end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Capped endpoint.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>Use resolvable lengths and unique IDs. This reference describes fields consumed by this component chain; unknown array keys are not generally validated and may have no effect.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../props-and-connections/strang/trunk.blade.php</flux:field>
</section>
