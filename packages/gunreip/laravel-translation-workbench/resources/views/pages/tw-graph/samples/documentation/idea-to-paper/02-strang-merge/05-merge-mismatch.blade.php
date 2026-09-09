{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/02-strang-merge/05-merge-mismatch.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/02-strang-merge/05-merge-mismatch.blade.php';
@endphp

<section class="grid gap-4 lg:grid-cols-2">
    <flux:callout
        color="red"
        icon="badge-alert"
    >
        <flux:callout.heading>
            {{ __('7. Merge mismatch') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('A merge can only render node labels for existing label anchors. If numeric node-label keys exceed the available merge anchors, DEV mode reports nodeLabel-Mismatch. If a numeric end label and the explicit end alias are both set, end wins and DEV mode reports nodeLabel-EndOverride.') }}
        </flux:callout.text>

        <div
            class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
    id="literature.left.1.label-mismatch"
    attach-to="strang.trunk.node.2"
    <span class="text-amber-300">bridge-length="12rem"
    start-length="3rem"
    :stem-lengths="[1 => '4rem']"</span>
    :node-labels="[
        1 => [
            'right' => ['text' => ['Valid label', 'anchor 1']],
        ],
        'end' => [
            'left' => ['text' => ['Valid end label', 'explicit end']],
        ],
        ...
        <
        span class="text-lime-300" > 4 => [
            'right' => ['text' => ['Ignored label', 'anchor 4 missing']],
        ],
        5 => [
            'left' => ['text' => ['Ignored label', 'anchor 5 missing']],
        ], < /span>
    ]"
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.merge-right
    id="literature.right.1.end-override"
    attach-to="strang.trunk.node.3"
    <span class="text-amber-300">bridge-length="12rem"
    start-length="3rem"
    :stem-lengths="[1 => '4rem']"</span>
    :node-labels="[
        1 => [
            'left' => ['text' => ['Valid label', 'anchor 1']],
        ], <
        span class="text-lime-300" > 3 => [
            'right' => ['text' => ['Numeric end label', 'overridden']],
        ],
        'end' => [
            'right' => ['text' => ['Explicit end label', 'end wins']],
        ], < /span>
    ]"
/&gt;</code></pre>
        </div>
    </flux:callout>

    <flux:callout
        color="zinc"
        icon="square-dashed-text"
    >
        <flux:callout.heading>
            {{ __('Step 7 preview') }}
        </flux:callout.heading>

        <div
            class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
            <x-translation-workbench::ui.tw-graph
                class="px-24 py-16"
                graph-id="idea-to-paper-step-07-merge-mismatch"
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
                        id="literature.center.1.merge-mismatch-reference"
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
                    id="literature.left.1.label-mismatch"
                    attach-to="strang.trunk.node.2"
                    bridge-length="12rem"
                    start-length="3rem"
                    :stem-lengths="[1 => '4rem']"
                    :node-labels="[
                        1 => [
                            'right' => [
                                'text' => ['Valid label', 'anchor 1'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                        'end' => [
                            'left' => [
                                'text' => ['Valid end label', 'explicit end'],
                                'width' => 'halfLong',
                                'align' => 'right',
                            ],
                        ],
                        4 => [
                            'right' => [
                                'text' => ['Ignored label', 'anchor 4 missing'],
                                'width' => 'default',
                                'align' => 'left',
                            ],
                        ],
                        5 => [
                            'left' => [
                                'text' => ['Ignored label', 'anchor 5 missing'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.merge-right
                    id="literature.right.1.end-override"
                    attach-to="strang.trunk.node.3"
                    bridge-length="12rem"
                    start-length="3rem"
                    :stem-lengths="[1 => '4rem']"
                    :node-labels="[
                        1 => [
                            'left' => [
                                'text' => ['Valid label', 'anchor 1'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ],
                        3 => [
                            'right' => [
                                'text' => ['Numeric end label', 'overridden'],
                                'width' => 'halfLong',
                                'align' => 'left',
                            ],
                        ],
                        'end' => [
                            'right' => [
                                'text' => ['Explicit end label', 'end wins'],
                                'width' => 'halfLong',
                                'align' => 'left',
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
