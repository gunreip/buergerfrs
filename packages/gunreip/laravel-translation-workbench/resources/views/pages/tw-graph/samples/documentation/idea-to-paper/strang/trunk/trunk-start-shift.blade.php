<x-translation-workbench::ui.common.heading-counter-group group="strang-trunk-start-shift">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Start shift') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="strang.trunk.trunk-start-shift" />
            <flux:callout.text>
                {{ __('Start shift is off by default. When enabled explicitly, it moves only the trunk-start origin away from the first regular trunk stem; downstream attach-to points stay stable. The merge is shown only as a reference attached to stem-1.') }}
            </flux:callout.text>
            @php
                $trunkExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-shift',
                );
            @endphp
            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-start-shift-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Default configuration') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $trunkExampleSource->example('trunk-start-shift-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-start-shift-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Adjusted configuration') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $trunkExampleSource->example('trunk-start-shift-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Trunk props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>auto id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stable element prefix for labels, DEV identifiers, anchors, and later attach-to targets.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>component-counter</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback counter when no explicit id is supplied. Handmade graphs should usually set id explicitly.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Direction of the trunk timeline. The usual vertical authoring flow grows from bottom to top.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt;
                                        &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Manual start coordinate. Most handmade graphs can keep the default and let tw-graph provide the canvas origin.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited graph color / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Trunk color inherited from tw-graph unless the trunk sets its own color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the trunk-start segment before the first nodeEnd anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>tw-graph stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default length for every trunk stem unless stem-lengths overrides a specific one.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:stem-count</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>default-path-segments</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('How many trunk stems are rendered after the start segment.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:stem-lengths</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Per-stem length overrides. Missing or null entries keep the default stem length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Text labels attached to numbered trunk stem anchor nodes.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>default-path-segments</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>10</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback stem count when stem-count is not set.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the final trunk-end segment.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Cap size used by the trunk end segment.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:start-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Centered label at the trunk start. Accepts text, width, align, color, and related text-label options.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:end-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Centered label at the trunk end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:start-node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Left/right labels attached to the trunk-start nodeEnd anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-label-space</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>3rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Extra space considered for start-label placement and bounds.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:start-shift-enabled</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>config trunk_start_shift_enabled</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Allows collision compensation to extend the start area when needed.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-shift-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>config trunk_start_shift_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Minimum visible delta used when trunk-start collision compensation is applied.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>20</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Layer position of the trunk relative to side strangs and DEV overlays.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>

        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-start-shift-example-1"
                            size="sm"
                        >{{ __('Default configuration') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- trunk-start-shift-example-1:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-step-02-trunk-start-shift-default"
                                :dev="true"
                                :coordinates="true"
                                horizontal-padding="6rem"
                                min-width="52rem"
                                min-height="36rem"
                            >
                                <x-translation-workbench::ui.tw-graph.strang.trunk
                                    id="literature.center.1.paper"
                                    color="zinc"
                                    :stem-count="3"
                                    :node-labels="[
                                        1 => [
                                            'right' => [
                                                'text' => ['Draft state', 'before merge'],
                                                'width' => 'default',
                                                'align' => 'left',
                                            ],
                                        ],
                                    ]"
                                />

                                <x-translation-workbench::ui.tw-graph.strang.merge-left
                                    id="literature.left.1.review-note"
                                    attach-to="strang.trunk.center.1.stem-1"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- trunk-start-shift-example-1:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-start-shift-example-2"
                            size="sm"
                        >{{ __('Adjusted configuration') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- trunk-start-shift-example-2:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-step-02-trunk-start-shift-props"
                                :dev="true"
                                :coordinates="true"
                                horizontal-padding="6rem"
                                min-width="52rem"
                                min-height="36rem"
                            >
                                <x-translation-workbench::ui.tw-graph.strang.trunk
                                    id="literature.center.1.paper"
                                    color="violet"
                                    :stem-count="3"
                                    :start-shift-enabled="true"
                                    start-shift-length="14rem"
                                    :node-labels="[
                                        1 => [
                                            'right' => [
                                                'text' => ['Draft state', 'before merge'],
                                                'width' => 'default',
                                                'align' => 'left',
                                            ],
                                        ],
                                    ]"
                                />

                                <x-translation-workbench::ui.tw-graph.strang.merge-left
                                    id="literature.left.1.review-note"
                                    attach-to="strang.trunk.center.1.stem-1"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- trunk-start-shift-example-2:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/strang/trunk/trunk-start-shift.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
