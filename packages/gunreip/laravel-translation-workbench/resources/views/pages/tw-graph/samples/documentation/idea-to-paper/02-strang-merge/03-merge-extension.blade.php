{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/02-strang-merge/03-merge-extension.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/02-strang-merge/03-merge-extension.blade.php';
@endphp

<section class="grid gap-4 lg:grid-cols-2">
    <flux:callout
        color="orange"
        icon="git-pull-request-arrow"
    >
        <flux:callout.heading>
            {{ __('7. Merge extension') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Merge extensions continue an existing merge side outward. Use them when multiple sources belong to the same merge relation but should still remain visually inspectable.') }}
        </flux:callout.text>

        <div
            class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.merge-left
    id="literature.left.1.source-note"
    attach-to="strang.trunk.node.2"
    <span class="text-lime-300">:extension-count="1"
    extension-bridge-length="8rem"
    extension-stem-length="5rem"
    :extension-node-labels="[
        1 => [
            1 => [
                'left' => [
                    'text' => ['Second source', 'same idea'],
                    'width' => 'default',
                    'align' => 'right',
                ],
            ],
            4 => [
                'right' => [
                    'text' => ['extension joins', 'the merge path'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ],
    ]"</span>
/&gt;

...

&lt;x-translation-workbench::ui.tw-graph.strang.merge-right
    id="literature.right.1.source-note"
    attach-to="strang.trunk.node.2"
    <span class="text-lime-300">:extension-count="1"
    extension-bridge-length="8rem"
    extension-stem-length="5rem"
    :extension-node-labels="[...]"</span>
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
                graph-id="idea-to-paper-step-07-merge-extension"
                :dev="$dev"
                :coordinates="$coordinates"
                color="amber"
                arc-size="2.75rem"
                bridge-length="8rem"
                stem-length="5rem"
                slot-min-height="52rem"
                horizontal-padding="56rem"
                min-width="96rem"
                min-height="52rem"
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
                    id="literature.left.1.source-note"
                    attach-to="strang.trunk.node.2"
                    :extension-count="1"
                    extension-bridge-length="8rem"
                    extension-stem-length="5rem"
                    :extension-node-labels="[
                        1 => [
                            1 => [
                                'left' => [
                                    'text' => ['Second source', 'same idea'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                            4 => [
                                'right' => [
                                    'text' => ['extension joins', 'the merge path'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                        ],
                    ]"
                />

                <x-translation-workbench::ui.tw-graph.strang.merge-right
                    id="literature.right.1.source-note"
                    attach-to="strang.trunk.node.2"
                    :extension-count="1"
                    extension-bridge-length="8rem"
                    extension-stem-length="5rem"
                    :extension-node-labels="[
                        1 => [
                            1 => [
                                'right' => [
                                    'text' => ['Second review', 'same conclusion'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                            4 => [
                                'left' => [
                                    'text' => ['extension joins', 'the merge path'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
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
