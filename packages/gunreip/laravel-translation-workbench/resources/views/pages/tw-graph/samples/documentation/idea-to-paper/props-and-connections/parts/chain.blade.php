<section class="min-w-0 space-y-4" id="reference-parts-chain">
    <flux:heading size="lg">parts.chain — Deep Reference</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="parts.chain" />
    <flux:text>Advances a coordinate cursor through an authored list of start, sideways and end parts. Only its explicitly forwarded fields are accepted.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">parts</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Ordered authored part definitions; only the fields listed for each type are forwarded.</flux:table.cell>
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
                    <flux:table.cell class="whitespace-normal align-top">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Default vertical stem length. Null resolves through canvas stem-length / line-length (normally 4rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">cap-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Cap length; null uses canvas cap-length (1.75rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="green" icon="brackets">
        <flux:callout.heading>Array props</flux:callout.heading>
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>parts[]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>Sequential parts. Unsupported types do not render. This wrapper does not forward every public part prop: bridgeLabel, lineJumps and returnTo are not forwarded.</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>Prop / path</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Default / fallback</flux:table.column>
                            <flux:table.column>Meaning and limits</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">parts[].type</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">start | sideways | end</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">sideways</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Selects the part implementation.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">parts[].id</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">string | null</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Part default</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Use a unique ID for every entry.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">parts[].anchorStart</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">array</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Current cursor</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Optional explicit start override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">parts[].color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Chain color</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Per-part color override.</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">parts[].direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">direction enum</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Chain direction</flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">Per-part direction override.</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>parts[].anchorStart</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top">parts[].anchorStart.x</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">0rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Horizontal coordinate.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].anchorStart.y</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">0rem</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Vertical coordinate.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>parts[] — type=start</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields forwarded only for type=start. Defaults resolve against the chain before the individual part.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].length</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of the owned line. See geometry section for its null fallback.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Render endpoint marker; supplied node labels can require a visible node.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].jointArrowEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw an endpoint joint-arrow instead of a Dot when applicable.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeEndDot</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit endpoint Dot choice. Null derives it from node/label configuration.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | array | false | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Endpoint image; expand its options below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Left endpoint annotation; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Right endpoint annotation; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devCounterEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Endpoint DEV counter caption; false suppresses it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devCounterColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">DEV counter color; null uses component color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].startLabel</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Text near the starting end; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].gradient</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Gradient start line when true; false gives an ordinary continuous line.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].zIndex</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devMode</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit DEV override; null inherits canvas dev.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].nodeImage</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.source</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Image source URL/path accepted by the image component.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.src</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Fallback when source is omitted.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.size</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas node-image-size → 3rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Rendered image size.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.alt</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">empty</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alternative text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Image frame color.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.zIndex</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer | null</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Renderer default</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Optional image layer override.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].nodeLabelLeft</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].nodeLabelRight</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].startLabel</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.offset</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].startLabel.half</flux:table.cell>
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
                            <flux:accordion.heading>parts[] — type=sideways</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields forwarded only for type=sideways. Defaults resolve against the chain before the individual part.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].side</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string enum</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">left</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Route/endpoint side. For parts.sideways this names the incoming arc side: left moves right; right moves left.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].arcRadius</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Arc radius; null uses the shared arc radius (2.75rem).</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].bridgeLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Default bridge length. Null normally resolves through canvas bridge-length / line-length; IF uses normalized label-bridge lengths.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].extension</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Additional vertical extension between each arc and bridge; null means 0rem.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Render endpoint marker; supplied node labels can require a visible node.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].jointArrowEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Draw an endpoint joint-arrow instead of a Dot when applicable.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">string | array | false | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Endpoint image; expand its options below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Left endpoint annotation; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Right endpoint annotation; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devCounterEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">1</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Endpoint DEV counter caption; false suppresses it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devCounterColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">DEV counter color; null uses component color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].zIndex</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devMode</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit DEV override; null inherits canvas dev.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].nodeImage</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.source</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Image source URL/path accepted by the image component.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.src</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">none</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Fallback when source is omitted.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.size</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas node-image-size → 3rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Rendered image size.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.alt</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">empty</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alternative text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Image frame color.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeImage.zIndex</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer | null</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Renderer default</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Optional image layer override.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].nodeLabelLeft</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelLeft.half</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].nodeLabelRight</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.connectorLength</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 2rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node-to-label connector length.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.connectorGap</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas → 0.25rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Connector-to-text spacing.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].nodeLabelRight.half</flux:table.cell>
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
                            <flux:accordion.heading>parts[] — type=end</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>Fields forwarded only for type=end. Defaults resolve against the chain before the individual part.</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>Prop / path</flux:table.column>
                                        <flux:table.column>Type</flux:table.column>
                                        <flux:table.column>Default / fallback</flux:table.column>
                                        <flux:table.column>Meaning and limits</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].length</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Length of the owned line. See geometry section for its null fallback.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].capLength</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">length string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Cap length; null uses canvas cap-length (1.75rem).</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].nodeStart</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Render the starting node of the end segment.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devCounterEnd</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">E</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Endpoint DEV counter caption; false suppresses it.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devCounterColor</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer | string | false | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">DEV counter color; null uses component color.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].endLabel</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">array | string | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Text near the capped end; expand below.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].zIndex</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">20</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Base drawing layer for this component.</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top">parts[].devMode</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">bool | null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">null</flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">Explicit DEV override; null inherits canvas dev.</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>parts[].endLabel</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.text</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">string | list&lt;string&gt;</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">No text unless supplied</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Explicit text; | or list entries create separate lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.width</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">half | default | halfLong | long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">default unless parent default says otherwise</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas width presets: normally 6 / 12 / 18 / 24rem.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.align</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | center | right</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Alignment inside the box; independent of route direction.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.justify</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Justify text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.badge</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">true</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Draw badge behind text.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.badgeColor</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component / label color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Badge-only color override.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.maxLines</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">integer</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">3</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Maximum displayed text lines.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">color name</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Component color</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Node label: connector/badge color. Action: route color only where the owning component reads it.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.side</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">left | right | top | bottom | center</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">step: center; start/end: derived from direction</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Placement relative to the owning anchor; does not mirror the component.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.offset</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">length string</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Canvas label-offset → 0.75rem</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Text offset; for step labels also affects calculated text gap.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.long</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.halfLong</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">bool</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">false</flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">Width flag accepted by this renderer; prefer width.</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top">parts[].endLabel.half</flux:table.cell>
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
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.parts.chain
    :parts=&quot;[
        [&#x27;type&#x27; =&gt; &#x27;start&#x27;, &#x27;id&#x27; =&gt; &#x27;example.entry&#x27;, &#x27;length&#x27; =&gt; &#x27;4rem&#x27;],
        [&#x27;type&#x27; =&gt; &#x27;sideways&#x27;, &#x27;id&#x27; =&gt; &#x27;example.turn&#x27;, &#x27;side&#x27; =&gt; &#x27;left&#x27;, &#x27;bridgeLength&#x27; =&gt; &#x27;4rem&#x27;],
        [&#x27;type&#x27; =&gt; &#x27;end&#x27;, &#x27;id&#x27; =&gt; &#x27;example.end&#x27;, &#x27;length&#x27; =&gt; &#x27;2rem&#x27;],
    ]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>Inheritance and calculated geometry</flux:callout.heading>
        <flux:text>Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.</flux:text>
        <flux:text>Each part advances the cursor using its geometric lengths. A supplied parts[].anchorStart overrides that entry’s starting coordinate. This wrapper does not create a graph-wide output ID.</flux:text>
        <flux:text>Use length for start/end entries. Legacy stem-length/stem_length spellings are accepted; stemLength is not consistently forwarded to the rendered start/end and should not be used here.</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top">{parts[].id}.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">anchor</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Individual part output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">Start and sideways entries publish an output under their ID; an end entry is terminal and publishes no anchorNode-end.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>Validation and practical limits</flux:callout.heading>
        <flux:text>Use resolvable lengths and unique IDs. This reference describes fields consumed by this component chain; unknown array keys are not generally validated and may have no effect.</flux:text>
    </flux:callout>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../props-and-connections/parts/chain.blade.php</flux:field>
</section>
