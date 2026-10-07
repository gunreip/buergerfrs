<section class="min-w-0 space-y-4" id="reference-strang-flow-start">
    <flux:heading size="lg">{{ __('strang.flow-start — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.flow-start" />
    <flux:text>{{ __('Starts a process graph through parts.start; optional gradient, start caption and endpoint annotations.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>direction</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bottom-top</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Flow direction. Vertical: bottom-top/top-bottom. Start, end, step, trunk and chain also support left-right/right-left.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>{&quot;x&quot;: &quot;0rem&quot;, &quot;y&quot;: &quot;0rem&quot;}</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Fallback/input coordinates. Expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Initial segment length; see geometry section for family-specific resolution.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Length of the owned line. See geometry section for its null fallback.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides inherited canvas color; final fallback zinc.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render endpoint marker; supplied node labels can require a visible node.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-end-dot</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit endpoint Dot choice. Null derives it from node/label configuration.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-image</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | array | false | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Endpoint image; expand its options below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text near the starting end; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Annotation slots at the start segment’s output.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-label-left</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Left endpoint annotation; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-label-right</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Right endpoint annotation; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('First DEV counter caption/number.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>dev-counter-color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer | string | false | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('DEV counter color; null uses component color.') }}</flux:table.cell>
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
                <flux:accordion.heading>node-image</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Image at the endpoint. A nonempty source/src is required.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-image.source</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Image source URL/path accepted by the image component.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-image.src</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Fallback when source is omitted.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-image.size</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas node-image-size → 3rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Rendered image size.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-image.alt</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>empty</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alternative text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-image.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Image frame color.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-image.zIndex</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer | null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Renderer default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Optional image layer override.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>start-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Options accepted for this start label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | right | top | bottom | center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>step: center; start/end: derived from direction</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Placement relative to the owning anchor; does not mirror the component.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas label-offset → 0.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text offset; for step labels also affects calculated text gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-label.half</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>start-node-labels</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>start-node-labels.left</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.left.half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>start-node-labels.right</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels.right.half</code></flux:table.cell>
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
            <flux:accordion.item>
                <flux:accordion.heading>node-label-left</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.connectorLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.connectorGap</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-left.half</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>node-label-right</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.connectorLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.connectorGap</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-label-right.half</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.flow-start
    id=&quot;example.flow-start&quot;
    :start-label=&quot;[&#x27;text&#x27; =&gt; [&#x27;Begin process&#x27;], &#x27;width&#x27; =&gt; &#x27;half&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.') }}</flux:text>
        <flux:text>{{ __('For flow-start, explicit node-label-left/right override the corresponding start-node-labels slots. Endpoint label side follows its slot.') }}</flux:text>
        <flux:text>{{ __('The endpoint is the starting coordinate advanced by the resolved length. Length falls back to canvas stem-length, then line-length (normally 4rem). flow-start gives start-length precedence over length.') }}</flux:text>
        <flux:text>{{ __('A start label does not create a separate routing action. Endpoint annotations/image can change the visible marker and bounds.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>{id}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Attach the next component here.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('Use resolvable lengths and unique IDs. This reference describes fields consumed by this component chain; unknown array keys are not generally validated and may have no effect.') }}</flux:text>
    </flux:callout>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/strang/flow-start.blade.php"
        segments="3"
    />
</section>
