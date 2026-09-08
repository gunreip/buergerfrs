{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/02-strang-merge/04-merge-aggregated.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
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

        <div class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
    id="literature.left.1.aggregated-sources"
    attach-to="strang.trunk.node.2"
    :extension-count="1"
    extension-bridge-length="18rem"
    extension-stem-length="4rem"
    :extension-stem-continuations="[
        1 => [
            1 => [
                'length' => '4rem',
                'left' => [
                    'text' => ['Finding ID #42', 'archive note'],
                    'width' => 'default',
                    'align' => 'right',
                ],
            ],
            2 => [
                'length' => '4rem',
                'right' => [
                    'text' => ['Finding ID #43', 'review note'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ],
    ]"
    :extension-node-labels="[
        1 => [
            1 => [
                'right' => [
                    'text' => ['Aggregated origins', '2 sources'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
        ],
    ]"
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

        <div class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                    :extension-count="1"
                    extension-bridge-length="18rem"
                    extension-stem-length="4rem"
                    :extension-stem-continuations="[
                        1 => [
                            1 => [
                                'length' => '4rem',
                                'left' => [
                                    'text' => ['Finding ID #42', 'archive note'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                            2 => [
                                'length' => '4rem',
                                'right' => [
                                    'text' => ['Finding ID #43', 'review note'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                        ],
                    ]"
                    :extension-node-labels="[
                        1 => [
                            1 => [
                                'right' => [
                                    'text' => ['Aggregated origins', '2 sources'],
                                    'width' => 'halfLong',
                                    'align' => 'left',
                                ],
                            ],
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.merge-right
                    id="literature.right.1.aggregated-sources"
                    attach-to="strang.trunk.node.3"
                    :extension-count="1"
                    extension-bridge-length="18rem"
                    extension-stem-length="4rem"
                    :extension-stem-continuations="[
                        1 => [
                            1 => [
                                'length' => '4rem',
                                'right' => [
                                    'text' => ['Finding ID #51', 'lab note'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                            2 => [
                                'length' => '4rem',
                                'left' => [
                                    'text' => ['Finding ID #52', 'margin note'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                        ],
                    ]"
                    :extension-node-labels="[
                        1 => [
                            1 => [
                                'left' => [
                                    'text' => ['Aggregated origins', '2 sources'],
                                    'width' => 'halfLong',
                                    'align' => 'right',
                                ],
                            ],
                        ],
                    ]"
                />
            </x-translation-workbench::ui.tw-graph>
        </div>
    </flux:callout>
</section>
