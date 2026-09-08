{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/02-strang-merge/02-merge-start.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
@endphp

<section class="grid gap-4 lg:grid-cols-2">
    <flux:callout
        color="amber"
        icon="tag"
    >
        <flux:callout.heading>
            {{ __('6. Merge start') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('The merge start is the semantic entry point of a merge strand. It can carry its own centered label and node labels before the path turns into the bridge.') }}
        </flux:callout.text>

        <div
            class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
    id="literature.left.1.archive-finding"
    attach-to="strang.trunk.node.2"
    :start-label="[
        'text' => ['Archive finding', '1905-03-17'],
        'width' => 'halfLong',
        'align' => 'center',
        'color' => 'amber',
    ]"
    :node-labels="[
        1 => [
            'right' => [
                'text' => ['Finding ID #42', 'annotated source'],
                'width' => 'default',
                'align' => 'left',
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
            {{ __('Step 6 preview') }}
        </flux:callout.heading>

        <div
            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-24 py-16"
                graph-id="idea-to-paper-step-06-merge-start"
                :dev="$dev"
                :coordinates="$coordinates"
                color="amber"
                arc-size="2.75rem"
                bridge-length="18rem"
                stem-length="5rem"
                slot-min-height="34rem"
                horizontal-padding="42rem"
                min-width="72rem"
                min-height="34rem"
            >
                <div class="pointer-events-none opacity-25">
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        id="literature.center.1.merge-reference"
                        color="zinc"
                        :stem-count="3"
                        start-length="4rem"
                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem']"
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
                    id="literature.left.1.archive-finding"
                    attach-to="strang.trunk.node.2"
                    bridge-length="6rem"
                    start-shift-enabled="true"
                    start-shift-length="5rem"
                    start-length="3rem"
                    :start-label="[
                        'text' => ['Archive finding', '1905-03-17'],
                        'width' => 'halfLong',
                        'align' => 'center',
                        'color' => 'amber',
                    ]"
                    {{-- :stem-lengths="[1 => '4rem']" --}}
                    :stem-lengths="[1 => '4rem', 2 => '4rem', 3 => '4rem', 4 => '4rem']"
                    :node-labels="[
                        1 => [
                            'right' => [
                                'text' => ['Finding ID #42', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                            'left' => [
                                'text' => ['Finding ID #43', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                        2 => [
                            'right' => [
                                'text' => ['Finding ID #44', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                            'left' => [
                                'text' => ['Finding ID #45', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                        3 => [
                            'right' => [
                                'text' => ['Finding ID #46', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                            'left' => [
                                'text' => ['Finding ID #47', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                        // 4 => [
                        //     'right' => [
                        //         'text' => ['Finding ID #48', 'annotated source'],
                        //         'width' => 'default',
                        //         'align' => 'left',
                        //     ],
                        //     'left' => [
                        //         'text' => ['Finding ID #49', 'annotated source'],
                        //         'width' => 'default',
                        //         'align' => 'left',
                        //     ],
                        // ],
                        5 => [
                            'right' => [
                                'text' => ['Finding ID #50', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                            'left' => [
                                'text' => ['Finding ID #51', 'annotated source'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                        'end' => [
                            'right' => [
                                'text' => ['Finding ID #998', 'annotated end'],
                                'width' => 'halfLong',
                                'align' => 'left',
                            ],
                            'left' => [
                                'text' => ['Finding ID #999', 'annotated end'],
                                'width' => 'halfLong',
                                'align' => 'left',
                            ],
                        ],
                    ]"
                />
            </x-translation-workbench::ui.tw-graph>
        </div>
    </flux:callout>
</section>
