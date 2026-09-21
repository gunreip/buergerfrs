<section class="min-w-0 space-y-4" id="reference-strang-merge-left">
    <flux:heading size="lg">strang.merge-left — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.merge-left" />
    <flux:text>Builds an incoming merge from the left, aligning its output to an existing target.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">start-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Text near the starting end; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-labels</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Node annotation definitions; expand below for this component’s shape.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">arc-sizes</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Individual incoming/outgoing arc-size overrides; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-lengths</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-stem length overrides, indexed by one-based number or supplied as a list.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-count</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">0</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Number of merge extension lanes; per-extension arrays are indexed from 1.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-start-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Start length shared by merge extensions; null uses arc size.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-start-shift-enabled</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Enable each extension’s start spacer; null reads the canvas setting.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-start-shift-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Extension spacer length; null reads canvas merge-extension start-shift length.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-continuation</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Ordered vertical continuation entries; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base extension stem length; null uses base stem length.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-stem-lengths</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-extension stem-length overrides.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-extension lists of continuation entries.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default extension bridge length; null uses the main bridge length.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-extension bridge continuation lists.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-arc-size</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base extension arc size; null uses canvas arc size.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-arc-sizes</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-extension arc-size overrides.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension-node-labels</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Per-extension numbered node labels.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">counter-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">First DEV counter caption/number.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">z-index</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">10</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">dev-mode</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit DEV override; null inherits canvas dev.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Canvas bridge-length → line-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Supported local attribute consumed outside the declared prop list.</flux:table.cell>
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
                <flux:accordion.heading>node-labels.start</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.side</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-labels.start.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
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
            <flux:accordion.item>
                <flux:accordion.heading>arc-sizes</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Independent arc sizes. Numeric keys 1/2 take precedence over in/out.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">arc-sizes.in</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas arc-size → 2.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Incoming arc size.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">arc-sizes.out</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas arc-size → 2.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Outgoing arc size.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">arc-sizes.1</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">in → canvas</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Numeric incoming override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">arc-sizes.2</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">out → canvas</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Numeric outgoing override.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>stem-lengths[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>One-based entries (ordinary lists are mapped to 1, 2, …). Scalars supply the length directly.</flux:text>
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
                                <flux:table.cell class="whitespace-normal align-top">Inherited/base length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit length; positional [0] is also accepted.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>stem-continuation[]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Sequence entries; lists are numbered from 1. Prefer length and labels over positional shorthand.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas stem-length → 4rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Length of this entry. Scalar strings and positional [0] lengths are also accepted.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit node-label slots; expand below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].compressed</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Render a compressed visual stem while preserving its logical endpoint.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].beforeLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">0.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Visible stem before the compressed gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].gapLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">1rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Compressed gap size.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].afterLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Path-specific remainder/default</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Visible segment after the compressed gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].force</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Branch: keep a labelled first continuation after a step.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].render</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Branch: prevent promotion to the step endpoint.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">stem-continuation[].spacer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Branch: retain this explicit spacer.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>stem-continuation[].labels</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-continuation[].labels.left</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.left.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-continuation[].labels.right</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">stem-continuation[].labels.right.half</flux:table.cell>
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
                <flux:accordion.heading>extension-stem-lengths</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Per-extension stem lengths.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">extension-stem-lengths.[n]</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">extension-stem-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Override the stem length of extension n; indices start at 1.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-stem-continuations[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>One-based extension index. The value is a continuation list.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>extension-stem-continuations[n][]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Sequence entries; lists are numbered from 1. Prefer length and labels over positional shorthand.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].length</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas stem-length → 4rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of this entry. Scalar strings and positional [0] lengths are also accepted.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit node-label slots; expand below.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-stem-continuations[n][].labels</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                            <flux:accordion class="mt-4">
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-stem-continuations[n][].labels.left</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.text</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.width</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.align</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.justify</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.badge</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.badgeColor</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.maxLines</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.connectorLength</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.connectorGap</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.halfLong</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.left.half</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                            </flux:table.rows>
                                                        </flux:table>
                                                    </flux:accordion.content>
                                                </flux:accordion.item>
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-stem-continuations[n][].labels.right</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.text</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.width</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.align</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.justify</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.badge</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.badgeColor</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.maxLines</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.connectorLength</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.connectorGap</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.halfLong</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-stem-continuations[n][].labels.right.half</flux:table.cell>
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
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-bridge-continuations[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>One-based extension index. The value is a continuation list.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>extension-bridge-continuations[n][]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Sequence entries; lists are numbered from 1. Prefer length and labels over positional shorthand.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].length</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas stem-length → 4rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of this entry. Scalar strings and positional [0] lengths are also accepted.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit node-label slots; expand below.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-bridge-continuations[n][].labels</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                            <flux:accordion class="mt-4">
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-bridge-continuations[n][].labels.left</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.text</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.width</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.align</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.justify</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.badge</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.badgeColor</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.maxLines</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.connectorLength</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.connectorGap</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.halfLong</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.left.half</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                            </flux:table.rows>
                                                        </flux:table>
                                                    </flux:accordion.content>
                                                </flux:accordion.item>
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-bridge-continuations[n][].labels.right</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.text</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.width</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.align</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.justify</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.badge</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.badgeColor</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.maxLines</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.connectorLength</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.connectorGap</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.long</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.halfLong</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top">extension-bridge-continuations[n][].labels.right.half</flux:table.cell>
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
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-arc-sizes</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Per-extension arc sizes.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">extension-arc-sizes.[n]</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">extension-arc-size</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Override the arc size of extension n; indices start at 1.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-node-labels[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>One-based extension index.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>extension-node-labels[n][n]</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional left label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | label array | false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Optional right label; expand below.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-node-labels[n][n].left</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].left.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-node-labels[n][n].right</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">extension-node-labels[n][n].right.half</flux:table.cell>
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
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
    id=&quot;example.merge-left&quot;
    :start-label=&quot;[&#x27;text&#x27; =&gt; [&#x27;Incoming route&#x27;], &#x27;width&#x27; =&gt; &#x27;half&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.</flux:text>
        <flux:text>attach-to aligns the outgoing end to the target. The starting coordinate is calculated backwards from the complete route dimensions.</flux:text>
        <flux:text>These traditional components publish family-specific named anchors. Node numbering changes when continuations/extensions are added. Render referenced anchors before dependent components.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">strang.merge-left.start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Input</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Resolved starting coordinate.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">strang.merge-left.end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Named final connection.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">strang.merge-left.bridge.end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Bridge endpoint</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Reference for further routes.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">strang.merge-left.stem.end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Stem endpoint</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">End of configured continuations.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">strang.merge-left.extension.{n}.end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Extension output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">One-based extension number.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>Missing attachment references can render a DEV mismatch and fall back to anchor-start or a calculated family fallback. Fixed left/right wrappers do not accept an independent side prop.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../props-and-connections/strang/merge-left.blade.php</flux:field>
</section>
