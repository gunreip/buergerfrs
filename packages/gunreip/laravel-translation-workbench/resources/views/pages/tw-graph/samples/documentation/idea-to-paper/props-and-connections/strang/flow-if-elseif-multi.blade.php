<section class="min-w-0 space-y-4" id="reference-strang-flow-if-elseif-multi">
    <flux:heading size="lg">strang.flow-if-elseif-multi — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-if-elseif-multi" />
    <flux:text>One IF followed by one or more ordered ELSEIF branches and a final fallback/bypass. Each branch may contain an explicitly connected nested block.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Route/endpoint side. For parts.sideways this names the incoming arc side: left moves right; right moves left.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">condition-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{&quot;text&quot;: [&quot;IF condition?&quot;], &quot;width&quot;: &quot;halfLong&quot;}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Condition/question step label; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">if-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{&quot;text&quot;: [&quot;IF action&quot;], &quot;width&quot;: &quot;half&quot;}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">First successful IF action; expand routing and label options below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">elseifs</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">ELSEIF definitions; single variant requires exactly one, multi requires one or more.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">before-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Stem before the step text; flow-step null becomes 2rem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">after-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Stem after the step text; flow-step null becomes 2rem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">elseif-before-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">6rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default stem before an ELSEIF question; branch.beforeLength overrides it.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">elseif-after-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default stem after an ELSEIF question; branch.afterLength overrides it.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">8rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default vertical stem length. Null resolves through canvas stem-length / line-length (normally 4rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Arc radius; null uses the shared arc radius (2.75rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default bridge length. Null normally resolves through canvas bridge-length / line-length; IF uses normalized label-bridge lengths.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">if-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Final ELSE action or text-free bypass; expand below.</flux:table.cell>
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
                    <flux:table.cell class="whitespace-normal align-top">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">color name | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Overrides inherited canvas color; final fallback zinc.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">dev-mode</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit DEV override; null inherits canvas dev.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">z-index</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
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
                <flux:accordion.heading>condition-label</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">condition-label.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.side</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">condition-label.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>if-start</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Options accepted for this action label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.return</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false opens this route for a nested block. Author the return explicitly.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-start.returnOffset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">False/open ELSEIF: 12rem; first true route: no offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Horizontal extra space when return=false. Single ELSEIF first action does not read this field.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>if-start.lineJumps[]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-start.lineJumps[].over</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-start.lineJumps[].radius</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-start.lineJumps[].side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">top | bottom</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">top</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>if-start.stemLineJumps[]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-start.stemLineJumps[].over</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-start.stemLineJumps[].radius</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-start.stemLineJumps[].side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>elseifs[]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>At least one entry.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">elseifs[].key</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">1-based position</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Unique nonempty key used in this branch ID.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">array | string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">ELSEIF condition?</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Condition caption; expand below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">array | string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">ELSEIF action</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Successful action; expand below.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">elseifs[].color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Branch color; conditionLabel.color takes precedence.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">elseifs[].beforeLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">elseif-before-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Stem before this question.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">elseifs[].afterLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">elseif-after-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Stem after this question.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>elseifs[].conditionLabel</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.offset</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.halfLong</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.half</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].conditionLabel.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Branch color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Condition and branch color override.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>elseifs[].actionLabel</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Options accepted for this action label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.text</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.width</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.align</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.justify</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.badge</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.badgeColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.maxLines</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.return</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false opens this route for a nested block. Author the return explicitly.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.returnOffset</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">False/open ELSEIF: 12rem; first true route: no offset</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Horizontal extra space when return=false. Single ELSEIF first action does not read this field.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>elseifs[].actionLabel.lineJumps[]</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>Prop / path</flux:table.column>
                                                    <flux:table.column>Type</flux:table.column>
                                                    <flux:table.column>Default / fallback</flux:table.column>
                                                    <flux:table.column>Meaning and limits</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.lineJumps[].over</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.lineJumps[].radius</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.lineJumps[].side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">top | bottom</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">top</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>elseifs[].actionLabel.stemLineJumps[]</flux:accordion.heading>
                                        <flux:accordion.content>
                                            <flux:text>Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.</flux:text>
                                            <flux:table class="mt-3">
                                                <flux:table.columns sticky>
                                                    <flux:table.column>Prop / path</flux:table.column>
                                                    <flux:table.column>Type</flux:table.column>
                                                    <flux:table.column>Default / fallback</flux:table.column>
                                                    <flux:table.column>Meaning and limits</flux:table.column>
                                                </flux:table.columns>
                                                <flux:table.rows>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.stemLineJumps[].over</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.stemLineJumps[].radius</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">elseifs[].actionLabel.stemLineJumps[].side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
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
                <flux:accordion.heading>if-end</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Options accepted for this action label. Omit the label to use the parent default; partial arrays are not recursively merged.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.return</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false opens this route for a nested block. Author the return explicitly.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.returnOffset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">False/open ELSEIF: 12rem; first true route: no offset</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Horizontal extra space when return=false. Single ELSEIF first action does not read this field.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.stemLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component stem-length</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">False entry stem; layout may enforce minimum clearance.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">if-end.returnLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">8rem when open</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Extra vertical return distance when return=false.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion>
                        <flux:accordion.item>
                            <flux:accordion.heading>if-end.lineJumps[]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Explicit line crossings. Multiple jumps are allowed; DEV reports missing targets, invalid intersections and overlapping jumps.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-end.lineJumps[].over</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-end.lineJumps[].radius</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">if-end.lineJumps[].side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">top | bottom</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">top</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
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
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
    id=&quot;example.flow-if-elseif-multi&quot;
    side=&quot;left&quot;
    :elseifs=&quot;[
        [&#x27;key&#x27; =&gt; &#x27;ready&#x27;, &#x27;conditionLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;ELSEIF ready?&#x27;]], &#x27;actionLabel&#x27; =&gt; [&#x27;text&#x27; =&gt; [&#x27;Publish&#x27;]]],
    ]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.</flux:text>
        <flux:text>Label widths are aligned into a common route span. False stem clearance is at least the requested stemLength and may grow to fit action text. LabelBridge normalizes explicit plain-rem bridge lengths (minimum 0.25rem; small intermediate values become 1.15rem).</flux:text>
        <flux:text>The successful action and fallback can leave open outputs via return=false. ReturnColorRegistry propagates explicitly connected return colors through the outer rail. The internal return-color composition prop is not a public authoring option.</flux:text>
        <flux:text>Open ELSEIF actionLabel.return=false supplies returnOffset=12rem when omitted. Set returnOffset explicitly when you need different geometry. This fallback does not apply to the first IF action.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">Common output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Connect the next independent component here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.false.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Fallback action output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">When open, attach nested work here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.false.anchorNode-return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Fallback return target</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Join the explicit fallback return here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.if.true.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Successful action output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Attach nested work when return=false.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.if.true.anchorNode-return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Return target</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit nested return rejoins here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.elseif.{key}.true.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">ELSEIF output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Successful branch output.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.elseif.{key}.true.anchorNode-return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">ELSEIF return target</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Return a nested block to the correct outer level.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>IF diagrams do not evaluate expressions or execute actions. Changing label text does not change routing. Use return, if-end text and elseifs to describe topology.</flux:text>
        <flux:text>elseifs must contain at least one array; keys must be unique and nonempty.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../props-and-connections/strang/flow-if-elseif-multi.blade.php</flux:field>
</section>
