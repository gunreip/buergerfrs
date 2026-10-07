<section class="min-w-0 space-y-4" id="reference-strang-merge-right">
    <flux:heading size="lg">{{ __('strang.merge-right — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.merge-right" />
    <flux:text>{{ __('Builds an incoming merge from the right, aligning its output to an existing target.') }}</flux:text>
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
                    <flux:table.cell><code>direction</code></flux:table.cell>
                    <flux:table.cell>{{ __('String: bottom-top or top-bottom') }}</flux:table.cell>
                    <flux:table.cell><code>bottom-top</code></flux:table.cell>
                    <flux:table.cell>{{ __('Reverses traversal of the merge and its extensions without moving their layout connections. top-bottom swaps segment start/end and moves the stem junction arrow to the outgoing arc. Terminal decorations and numbered layout anchors retain their positions.') }}</flux:table.cell>
                </flux:table.row>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Overrides inherited canvas color; final fallback zinc.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Initial segment length; see geometry section for family-specific resolution.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-shift-enabled</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Enable a distinct starting spacer. Null reads this family’s canvas start-shift setting.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-shift-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Starting spacer length; applied only when shifting is enabled.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text near the starting end; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node annotation definitions; expand below for this component’s shape.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>arc-radiuss</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Individual incoming/outgoing arc-radius overrides; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-stem length overrides, indexed by one-based number or supplied as a list.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-colors</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Colors continue from index 1 to N. A supplied color applies to that extension and following extensions until the next override. Earlier extensions retain the merge color. Example: [3 => "fuchsia", 5 => "cyan"]. Anchor metadata carries the resolved color for connected consumers.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-count</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>0</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Number of merge extension lanes; per-extension arrays are indexed from 1.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-start-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Start length shared by merge extensions; null uses arc size.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-start-shift-enabled</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Enable each extension’s start spacer; null reads the canvas setting.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-start-shift-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Extension spacer length; null reads canvas merge-extension start-shift length.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Ordered vertical continuation entries; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base extension stem length; null uses base stem length.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-lengths</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-extension stem-length overrides.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-extension lists of continuation entries.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default extension bridge length; null uses the main bridge length.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-extension bridge continuation lists.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-arc-radius</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base extension arc size; null uses canvas arc size.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-arc-radiuss</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-extension arc-radius overrides.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-extension numbered node labels.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('First DEV counter caption/number.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>10</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base drawing layer for this component.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas bridge-length → line-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Supported local attribute consumed outside the declared prop list.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Indexed end captions for outer extensions. A nonempty caption replaces that extension start with a top-bottom end; omitted entries retain the start.') }}</flux:table.cell>
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
                <flux:accordion.heading>node-labels[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('One-based node numbers.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>node-labels[n].left</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].left.half</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>
                            <flux:accordion.heading>node-labels[n].right</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.text</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.width</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.align</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.justify</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.badge</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.badgeColor</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.maxLines</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.connectorLength</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.connectorGap</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.long</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.halfLong</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>node-labels[n].right.half</code></flux:table.cell>
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
                <flux:accordion.heading>node-labels.start</flux:accordion.heading>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | right | top | bottom | center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>step: center; start/end: derived from direction</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Placement relative to the owning anchor; does not mirror the component.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas label-offset → 0.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text offset; for step labels also affects calculated text gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>node-labels.start.half</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
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
            <flux:accordion.item>
                <flux:accordion.heading>arc-radiuss</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Independent arc sizes. Numeric keys 1/2 take precedence over in/out.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>arc-radiuss.in</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas arc-radius → 2.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Incoming arc size.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>arc-radiuss.out</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas arc-radius → 2.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Outgoing arc size.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>arc-radiuss.1</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>in → canvas</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numeric incoming override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>arc-radiuss.2</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>out → canvas</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numeric outgoing override.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>stem-lengths[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('One-based entries (ordinary lists are mapped to 1, 2, …). Scalars supply the length directly.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Inherited/base length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit length; positional [0] is also accepted.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>stem-continuation[]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Sequence entries; lists are numbered from 1. Prefer length and labels over positional shorthand.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas stem-length → 4rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Length of this entry. Scalar strings and positional [0] lengths are also accepted.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit node-label slots; expand below.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].compressed</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Render a compressed visual stem while preserving its logical endpoint.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].beforeLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Visible stem before the compressed gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].gapLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>1rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Compressed gap size.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].afterLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Path-specific remainder/default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Visible segment after the compressed gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].force</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Branch: keep a labelled first continuation after a step.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].render</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Branch: prevent promotion to the step endpoint.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].spacer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Branch: retain this explicit spacer.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>stem-continuation[].labels</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-continuation[].labels.left</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.left.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-continuation[].labels.right</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-continuation[].labels.right.half</code></flux:table.cell>
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
                <flux:accordion.heading>extension-stem-lengths</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Per-extension stem lengths.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-lengths.[n]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Override the stem length of extension n; indices start at 1.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-stem-continuations[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('One-based extension index. The value is a continuation list.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>extension-stem-continuations[n][]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Sequence entries; lists are numbered from 1. Prefer length and labels over positional shorthand.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].length</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas stem-length → 4rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Length of this entry. Scalar strings and positional [0] lengths are also accepted.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit node-label slots; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-stem-continuations[n][].labels</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                            <flux:accordion class="mt-4">
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-stem-continuations[n][].labels.left</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.text</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.width</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.align</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.justify</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.badge</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.badgeColor</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.maxLines</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.connectorLength</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.connectorGap</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.halfLong</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.left.half</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                            </flux:table.rows>
                                                        </flux:table>
                                                    </flux:accordion.content>
                                                </flux:accordion.item>
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-stem-continuations[n][].labels.right</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.text</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.width</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.align</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.justify</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.badge</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.badgeColor</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.maxLines</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.connectorLength</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.connectorGap</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.halfLong</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-stem-continuations[n][].labels.right.half</code></flux:table.cell>
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
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-bridge-continuations[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('One-based extension index. The value is a continuation list.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>extension-bridge-continuations[n][]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('Sequence entries; lists are numbered from 1. Prefer length and labels over positional shorthand.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].length</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>Canvas stem-length → 4rem</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Length of this entry. Scalar strings and positional [0] lengths are also accepted.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit node-label slots; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-bridge-continuations[n][].labels</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                            <flux:accordion class="mt-4">
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-bridge-continuations[n][].labels.left</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.text</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.width</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.align</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.justify</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.badge</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.badgeColor</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.maxLines</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.connectorLength</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.connectorGap</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.halfLong</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.left.half</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                            </flux:table.rows>
                                                        </flux:table>
                                                    </flux:accordion.content>
                                                </flux:accordion.item>
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>extension-bridge-continuations[n][].labels.right</flux:accordion.heading>
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
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.text</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.width</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.align</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.justify</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.badge</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.badgeColor</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.maxLines</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.connectorLength</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.connectorGap</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.long</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.halfLong</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                                    <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                                </flux:table.row>
                                                                <flux:table.row>
                                                                    <flux:table.cell class="whitespace-normal align-top"><code>extension-bridge-continuations[n][].labels.right.half</code></flux:table.cell>
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
                    </flux:accordion>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-arc-radiuss</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Per-extension arc sizes.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>extension-arc-radiuss.[n]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>extension-arc-radius</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Override the arc size of extension n; indices start at 1.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
            <flux:accordion.item>
                <flux:accordion.heading>extension-node-labels[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('One-based extension index.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>extension-node-labels[n][n]</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:text>{{ __('One-based node numbers.') }}</flux:text>
                                <flux:table class="mt-3">
                                    <flux:table.columns sticky>
                                        <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                                        <flux:table.column>{{ __('Type') }}</flux:table.column>
                                        <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                                        <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-node-labels[n][n].left</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].left.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>extension-node-labels[n][n].right</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>extension-node-labels[n][n].right.half</code></flux:table.cell>
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
                <flux:accordion.heading>extension-end-labels</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:table>
                        <flux:table.columns><flux:table.column>{{ __('Prop / path') }}</flux:table.column><flux:table.column>{{ __('Type') }}</flux:table.column><flux:table.column>{{ __('Default / fallback') }}</flux:table.column><flux:table.column>{{ __('Meaning and limits') }}</flux:table.column></flux:table.columns>
                        <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].text</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string | array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>required for a caption</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption content. An empty caption leaves the extension start unchanged.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].width</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>default</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption width preset.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].align</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text alignment: left, center or right.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].side</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>string enum</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bottom</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption placement relative to the endpoint.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].offset</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>0.75rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Distance between caption and endpoint.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Caption color.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].badgeColor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color name | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit caption badge color.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].badge</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Render the caption as a badge.') }}</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>extension-end-labels[n].maxLines</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Displayed line limit.') }}</flux:table.cell>
                </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.merge-right
    id=&quot;example.merge-right&quot;
    :start-label=&quot;[&#x27;text&#x27; =&gt; [&#x27;Incoming route&#x27;], &#x27;width&#x27; =&gt; &#x27;half&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.') }}</flux:text>
        <flux:text>{{ __('attach-to aligns the outgoing end to the target. The starting coordinate is calculated backwards from the complete route dimensions.') }}</flux:text>
        <flux:text>{{ __('These traditional components publish family-specific named anchors. Node numbering changes when continuations/extensions are added. Render referenced anchors before dependent components.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>strang.merge-right.start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Input</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Resolved starting coordinate.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>strang.merge-right.end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Named final connection.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>strang.merge-right.bridge.end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Bridge endpoint</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Reference for further routes.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>strang.merge-right.stem.end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Stem endpoint</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('End of configured continuations.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>strang.merge-right.extension.{n}.end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Extension output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('One-based extension number.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('Missing attachment references can render a DEV mismatch and fall back to anchor-start or a calculated family fallback. Fixed left/right wrappers do not accept an independent side prop.') }}</flux:text>
    </flux:callout>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/strang/merge-right.blade.php"
        segments="3"
    />
</section>
