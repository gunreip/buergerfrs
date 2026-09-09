{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/02-strang-merge/04-merge-aggregated.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/02-strang-merge/04-merge-aggregated.blade.php';
@endphp

<section class="grid gap-4 lg:grid-cols-2">
    <flux:callout
        color="amber"
        icon="list-collapse"
    >
        <flux:callout.heading>
            {{ __('8. Merge aggregated') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Aggregated merge stems keep many related origins readable without rendering every origin as a full merge strand. The aggregate label names the group; continuation labels identify representative rows.') }}
        </flux:callout.text>

        <div
            class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
    id="literature.left.1.aggregated-sources"
    attach-to="strang.trunk.node.2"
    <span class="text-amber-300">bridge-length="8rem"</span>
    <span class="text-lime-300">:extension-count="1"
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
            ...
            ...
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
    ]"</span>
/&gt;

...

&lt;x-translation-workbench::ui.tw-graph.strang.merge-right
    id="literature.right.1.aggregated-sources"
    attach-to="strang.trunk.node.3"
    <span class="text-amber-300">bridge-length="12rem"</span>
    <span class="text-lime-300">:extension-count="1"
    extension-start-length="12rem"
    extension-start-shift-enabled="true"
    extension-start-shift-length="8rem"
    extension-bridge-length="28rem"
    extension-stem-length="4rem"
    :extension-stem-continuations="[...]"
    :extension-node-labels="[...]"</span>
/&gt;</code></pre>
        </div>
    </flux:callout>

    <flux:callout
        color="zinc"
        icon="square-dashed-text"
    >
        <flux:callout.heading>
            {{ __('Step 8 preview') }}
        </flux:callout.heading>

        <div
            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-24 py-16"
                graph-id="idea-to-paper-step-08-merge-aggregated"
                :dev="$dev"
                :coordinates="$coordinates"
                color="amber"
                arc-size="2.75rem"
                bridge-length="16rem"
                stem-length="5rem"
                slot-min-height="66rem"
                horizontal-padding="58rem"
                min-width="102rem"
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
                        :dev-mode="false"
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
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                {{ $thisPath }}
            </flux:field>
        </div>
    </flux:callout>
</section>
