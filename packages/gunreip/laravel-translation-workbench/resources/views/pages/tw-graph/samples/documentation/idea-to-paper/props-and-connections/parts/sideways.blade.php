<section class="min-w-0 space-y-4" id="reference-parts-sideways">
    <flux:heading size="lg">parts.sideways — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="parts.sideways" />
    <flux:text>Incoming arc, optional extension, bridge, optional extension and outgoing arc; supports an action label inside the bridge.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Route/endpoint side. For parts.sideways this names the incoming arc side: left moves right; right moves left.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">anchor-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{&quot;x&quot;: &quot;0rem&quot;, &quot;y&quot;: &quot;0rem&quot;}</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Fallback/input coordinates. Expand below.</flux:table.cell>
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
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default bridge length. Null normally resolves through canvas bridge-length / line-length; IF uses normalized label-bridge lengths.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">bridge-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Optional action label inside the bridge; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">bridge-out-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit outgoing bridge after an action label; otherwise matches incoming bridge.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">bridge-in-join-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Optional join inside bridge-in, measured from its start. Must fit the bridge.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">dev-counter-join</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">J</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">DEV counter at the explicit bridge-in join.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">line-jumps</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Explicit crossings on this part’s line; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">extension</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Additional vertical extension between each arc and bridge; null means 0rem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">color name | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Overrides inherited canvas color; final fallback zinc.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Render endpoint marker; supplied node labels can require a visible node.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">joint-arrow-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Draw an endpoint joint-arrow instead of a Dot when applicable.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-image</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string | array | false | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Endpoint image; expand its options below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-label-left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Left endpoint annotation; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">node-label-right</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Right endpoint annotation; expand below.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">dev-counter-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Endpoint DEV counter caption; false suppresses it.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">dev-counter-color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">DEV counter color; null uses component color.</flux:table.cell>
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
                <flux:accordion.heading>bridge-label</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">bridge-label.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion><flux:accordion.item>
                    <flux:accordion.heading>bridge-label.lineJumps[]</flux:accordion.heading>
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
                                    <flux:table.cell class="whitespace-normal align-top">bridge-label.lineJumps[].over</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                                </flux:table.row>
                                <flux:table.row>
                                    <flux:table.cell class="whitespace-normal align-top">bridge-label.lineJumps[].radius</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                                </flux:table.row>
                                <flux:table.row>
                                    <flux:table.cell class="whitespace-normal align-top">bridge-label.lineJumps[].side</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">top | bottom</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">top</flux:table.cell>
                                    <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
                                </flux:table.row>
                            </flux:table.rows>
                        </flux:table>
                    </flux:accordion.content>
                </flux:accordion.item>
                </flux:accordion></flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>line-jumps[]</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">line-jumps[].over</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">required</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">ID of an existing perpendicular stem/bridge intersecting this line.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">line-jumps[].radius</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">0.5rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Must fit between endpoints and exceed half the line thickness.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">line-jumps[].side</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">top | bottom</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">top</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Side of the semicircle relative to its owning line.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>node-image</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Image at the endpoint. A nonempty source/src is required.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-image.source</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Image source URL/path accepted by the image component.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-image.src</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Fallback when source is omitted.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-image.size</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas node-image-size → 3rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Rendered image size.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-image.alt</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">empty</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alternative text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-image.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Image frame color.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-image.zIndex</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer | null</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Renderer default</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Optional image layer override.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>node-label-left</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.connectorLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.connectorGap</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-left.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>node-label-right</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.text</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.width</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.align</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.justify</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.badge</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.badgeColor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.maxLines</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.connectorLength</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.connectorGap</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.long</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.halfLong</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">node-label-right.half</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.parts.sideways
    id=&quot;example.sideways&quot;
    side=&quot;left&quot;
    :bridge-label=&quot;[&#x27;text&#x27; =&gt; [&#x27;Action&#x27;], &#x27;width&#x27; =&gt; &#x27;half&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.</flux:text>
        <flux:text>side=left moves to increasing x; side=right moves to decreasing x. This is the incoming-arc convention, opposite the destination-side naming of Flow IF.</flux:text>
        <flux:text>Without bridge-label, horizontal displacement is two radii plus bridge-length. With bridge-label, text width and both bridges participate. extension adds equal vertical pieces at the two arcs.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">End after the outgoing arc.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">{id}.bridge1.bridge-in.anchorNode-join</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Optional join</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Available with an action label and bridge-in-join-length; plain bridge uses {id}.bridge1.anchorNode-join.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>Use resolvable lengths and unique IDs. This reference describes fields consumed by this component chain; unknown array keys are not generally validated and may have no effect.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../props-and-connections/parts/sideways.blade.php</flux:field>
</section>
