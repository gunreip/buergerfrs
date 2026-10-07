<section class="min-w-0 space-y-4" id="reference-strang-trunk">
    <flux:heading size="lg">{{ __('strang.trunk — Deep Reference') }}</flux:heading>
    <x-translation-workbench::ui.tw-graph.documentation-links reference="strang.trunk" />
    <flux:text>{{ __('Main numbered graph line: start, configurable stems, then capped end.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default vertical stem length. Null resolves through canvas stem-length / line-length (normally 4rem).') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-count</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Number of trunk stems. Omitted count uses default-path-segments (10); values are clamped to at least zero.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Per-stem length overrides, indexed by one-based number or supplied as a list.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>node-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Node annotation definitions; expand below for this component’s shape.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>default-path-segments</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>10</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Default number of numbered trunk positions when stem-count is omitted.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>end-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Final capped segment length; see geometry section.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>end-cap-length</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('End cap length passed to the trunk end segment.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text near the starting end; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>end-label</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Text near the capped end; expand below.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-node-labels</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Annotation slots at the start segment’s output.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>start-label-space</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>array | string | null</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>3rem</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Extra space reserved around the trunk start label.') }}</flux:table.cell>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>z-index</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>20</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Base drawing layer for this component.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>counter-start</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>1</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('First DEV counter caption/number.') }}</flux:table.cell>
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
                <flux:accordion.heading>stem-lengths[n]</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('One-based trunk stem definitions. Each can carry its own label configuration.') }}</flux:text>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>Default path length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Logical stem length.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].component</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>path | stem-compressed</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>path</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Rendering component.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].beforeLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Renderer default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Visible piece before a compressed gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].gapLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Renderer default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Compressed gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].afterLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Renderer default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Visible piece after the gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].capLength</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string | null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas cap-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Cap override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool | array</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node visibility or label slots.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                    <flux:accordion class="mt-4">
                        <flux:accordion.item>
                            <flux:accordion.heading>stem-lengths[n].labels</flux:accordion.heading>
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
                                            <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional left label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional right label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional top label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                        <flux:table.row>
                                            <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>string | label array | false</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top"><code>none</code></flux:table.cell>
                                            <flux:table.cell class="whitespace-normal align-top">{{ __('Optional bottom label; expand below.') }}</flux:table.cell>
                                        </flux:table.row>
                                    </flux:table.rows>
                                </flux:table>
                                <flux:accordion class="mt-4">
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.left</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.left.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.right</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.right.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.top</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.top.half</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                </flux:table.rows>
                                            </flux:table>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                    <flux:accordion.item>
                                        <flux:accordion.heading>stem-lengths[n].labels.bottom</flux:accordion.heading>
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
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.text</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.width</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.align</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.justify</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.badge</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.badgeColor</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.maxLines</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.connectorLength</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 2rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Node-to-label connector length.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.connectorGap</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>Canvas → 0.25rem</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Connector-to-text spacing.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.long</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.halfLong</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                                        <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                                                    </flux:table.row>
                                                    <flux:table.row>
                                                        <flux:table.cell class="whitespace-normal align-top"><code>stem-lengths[n].labels.bottom.half</code></flux:table.cell>
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
                <flux:accordion.heading>end-label</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:text>{{ __('Options accepted for this end label. Omit the label to use the parent default; partial arrays are not recursively merged.') }}</flux:text>
                    <flux:table class="mt-3">
                        <flux:table.columns sticky>
                            <flux:table.column>{{ __('Prop / path') }}</flux:table.column>
                            <flux:table.column>{{ __('Type') }}</flux:table.column>
                            <flux:table.column>{{ __('Default / fallback') }}</flux:table.column>
                            <flux:table.column>{{ __('Meaning and limits') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>string | list&lt;string&gt;</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>No text unless supplied</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Explicit text; | or list entries create separate lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.width</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>half | default | halfLong | long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>default unless parent default says otherwise</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Canvas width presets: normally 6 / 12 / 18 / 24rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.align</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | center | right</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Alignment inside the box; independent of route direction.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.justify</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Justify text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.badge</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Draw badge behind text.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.badgeColor</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component / label color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Badge-only color override.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.maxLines</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>integer</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>3</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Maximum displayed text lines.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>color name</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Component color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Node label: connector/badge color. Action: route color only where the owning component reads it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left | right | top | bottom | center</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>step: center; start/end: derived from direction</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Placement relative to the owning anchor; does not mirror the component.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.offset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>length string</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>Canvas label-offset → 0.75rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Text offset; for step labels also affects calculated text gap.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.long</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.halfLong</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bool</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>false</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Width flag accepted by this renderer; prefer width.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>end-label.half</code></flux:table.cell>
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
        </flux:accordion>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.strang.trunk
    id=&quot;example.trunk&quot;
    stem-count=&quot;3&quot;
    :stem-lengths=&quot;[1 =&gt; &#x27;4rem&#x27;, 2 =&gt; &#x27;6rem&#x27;, 3 =&gt; &#x27;4rem&#x27;]&quot;
/&gt;</x-translation-workbench::ui.tw-graph.code-box>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="sky" icon="variable">
        <flux:callout.heading>{{ __('Inheritance and calculated geometry') }}</flux:callout.heading>
        <flux:text>{{ __('Declared defaults below are the component’s own values. null delegates to inherited canvas settings or the resolution described here; it does not mean zero. Canvas controls shared line thickness, node size, label widths, connector defaults and path tone.') }}</flux:text>
        <flux:text>{{ __('Terminal start-label/end-label null produces the default Path/start and Path/end captions. Use false to hide them. Unlike most custom labels, supplied terminal label arrays are merged with these terminal defaults.') }}</flux:text>
        <flux:text>{{ __('A numbered sequence combines start, stems and end. Explicit stem-count/stem-lengths control the number/length of stems; start shifts and label space add to total bounds.') }}</flux:text>
        <flux:text>{{ __('Generated canonical IDs use trunk.center.{componentCounter}. These numbered anchors are used by the traditional merge/branch/rekey families.') }}</flux:text>
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
                    <flux:table.cell class="whitespace-normal align-top"><code>trunk.center.{componentCounter}.start.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Start output</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Start/stem boundary.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>trunk.center.{componentCounter}.stem-{n}.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Numbered stem end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Attachment target for branches or merges.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal align-top"><code>trunk.center.{componentCounter}.end.anchorNode-end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>anchor</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top"><code>Trunk end</code></flux:table.cell>
                    <flux:table.cell class="whitespace-normal align-top">{{ __('Capped endpoint.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0 space-y-3" color="fuchsia" icon="infinity">
        <flux:callout.heading>{{ __('Validation and practical limits') }}</flux:callout.heading>
        <flux:text>{{ __('Use resolvable lengths and unique IDs. This reference describes fields consumed by this component chain; unknown array keys are not generally validated and may have no effect.') }}</flux:text>
    </flux:callout>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/props-and-connections/strang/trunk.blade.php"
        segments="3"
    />
</section>
