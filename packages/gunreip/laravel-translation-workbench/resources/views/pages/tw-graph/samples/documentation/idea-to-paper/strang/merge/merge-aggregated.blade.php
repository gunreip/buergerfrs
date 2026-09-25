<x-translation-workbench::ui.common.heading-counter-group group="strang-merge-aggregated">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Merge aggregated') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="strang.merge.merge-aggregated" />
            <flux:callout.text>
                {{ __('Aggregated merge stems keep many related origins readable without rendering every origin as a full merge strand. The aggregate label names the group; continuation labels identify representative rows.') }}
            </flux:callout.text>
            @php
                $mergeExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-aggregated',
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
                            example="merge-aggregated-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Merge aggregated') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $mergeExampleSource->example('merge-aggregated-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Merge props') }}</flux:callout.heading>
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
                                    {{ __('Stable identifier for the strand, its nodes and registered anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Registered target anchor. Resolves the merge position from the target and route dimensions.') }}
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
                                    {{ __('Manual starting coordinates when no attach-to target is used.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Strand color inherited from the graph unless explicitly set.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Horizontal distance between the two arcs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback length of the main vertical stem.') }}
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
                                    {{ __('Labels for concrete merge anchors. Use named text, width, align and color options; end labels the final anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:extension-count</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Number of additional side sources.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:stem-continuation</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Additional main stem sections with length, labels and optional compression.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:extension-stem-continuations</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Per-extension stem continuations. Each entry can include length, labels and compressed=true.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:extension-bridge-continuations</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Per-extension bridge continuation configuration.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:extension-node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Per-extension node label configuration.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>extension-stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>resolved stem length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback length of extension stems.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>extension-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>resolved bridge length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback length of extension bridges.') }}
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
                            example="merge-aggregated-example-1"
                            size="sm"
                        >{{ __('Merge aggregated') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- merge-aggregated-example-1:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-step-09-merge-aggregated"
                                :dev="true"
                                :coordinates="true"
                                color="amber"
                                arc-radius="2.75rem"
                                bridge-length="16rem"
                                stem-length="5rem"
                                horizontal-padding="6rem"
                                min-width="52rem"
                                min-height="66rem"
                            >
                                <div class="pointer-events-none opacity-25">
                                    <x-translation-workbench::ui.tw-graph.strang.trunk
                                        id="literature.center.1.merge-reference"
                                        color="zinc"
                                        :stem-count="4"
                                        start-length="4rem"
                                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                                        end-length="3rem"
                                        :start-label="[
                                            'text' => ['reference trunk'],
                                            'width' => 'default',
                                            'align' => 'center',
                                        ]"
                                    />
                                </div>

                                <x-translation-workbench::ui.tw-graph.strang.merge-left
                                    id="literature.left.1.aggregated-sources"
                                    attach-to="strang.trunk.node.2"
                                    bridge-length="8rem"
                                    :extension-count="1"
                                    extension-bridge-length="24rem"
                                    extension-stem-length="4rem"
                                    :extension-stem-continuations="[
                                        1 => [
                                            1 => ['length' => '4rem'],
                                            2 => ['length' => '4rem'],
                                        ],
                                    ]"
                                    :extension-node-labels="[
                                        1 => [
                                            1 => [
                                                'labels' => [
                                                    'left' => [
                                                        'text' => ['Aggregated origins #1', '2 sources'],
                                                        'align' => 'right',
                                                    ],
                                                    'right' => [
                                                        'text' => ['Aggregated origins #2', '3 sources'],
                                                        'align' => 'left',
                                                    ],
                                                ],
                                                'width' => 'default',
                                            ],
                                            2 => [
                                                'labels' => [
                                                    'left' => [
                                                        'text' => ['Aggregated origins #3', '2 sources'],
                                                        'align' => 'right',
                                                    ],
                                                    'right' => [
                                                        'text' => ['Aggregated origins #4', '8 sources'],
                                                        'align' => 'left',
                                                    ],
                                                ],
                                                'width' => 'default',
                                            ],
                                            3 => [
                                                'labels' => [
                                                    'left' => [
                                                        'text' => ['Finding ID #42', 'archive note'],
                                                        'align' => 'right',
                                                    ],
                                                    'right' => [
                                                        'text' => ['Finding ID #44', 'archive note'],
                                                        'align' => 'left',
                                                    ],
                                                ],
                                                'width' => 'default',
                                            ],
                                            4 => [
                                                'labels' => [
                                                    'left' => [
                                                        'text' => ['Finding ID #43', 'review note'],
                                                        'align' => 'right',
                                                    ],
                                                ],
                                                'width' => 'default',
                                            ],
                                        ],
                                    ]"
                                />

                                <x-translation-workbench::ui.tw-graph.strang.merge-right
                                    id="literature.right.1.aggregated-sources"
                                    attach-to="strang.trunk.node.3"
                                    bridge-length="12rem"
                                    :extension-count="1"
                                    extension-start-length="12rem"
                                    extension-start-shift-enabled="true"
                                    extension-start-shift-length="8rem"
                                    extension-bridge-length="28rem"
                                    extension-stem-length="4rem"
                                    :extension-stem-continuations="[
                                        1 => [
                                            1 => ['length' => '8rem'],
                                            2 => ['length' => '4rem'],
                                        ],
                                    ]"
                                    :extension-node-labels="[
                                        1 => [
                                            1 => [
                                                'labels' => [
                                                    'left' => [
                                                        'text' => ['Aggregated origins', '2 sources'],
                                                        'align' => 'right',
                                                    ],
                                                ],
                                                'width' => 'halfLong',
                                            ],
                                            3 => [
                                                'labels' => [
                                                    'right' => [
                                                        'text' => ['Finding ID #51', 'lab note'],
                                                        'align' => 'left',
                                                    ],
                                                ],
                                                'width' => 'default',
                                            ],
                                            4 => [
                                                'labels' => [
                                                    'left' => [
                                                        'text' => ['Finding ID #52', 'margin note'],
                                                        'align' => 'right',
                                                    ],
                                                ],
                                                'width' => 'default',
                                            ],
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- merge-aggregated-example-1:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/strang/merge/merge-aggregated.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
