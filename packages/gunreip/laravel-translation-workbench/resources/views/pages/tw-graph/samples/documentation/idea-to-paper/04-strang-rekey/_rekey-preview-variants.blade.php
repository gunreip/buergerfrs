@if ($renderMode === 'documentation')
            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="rekeyVariant === 'default'"
            >
@endif
<x-translation-workbench::ui.tw-graph
    class="px-24 py-16"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    color="violet"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-size="2.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
    slot-min-height="54rem"
    horizontal-padding="52rem"
    min-width="92rem"
    min-height="54rem"
>
    <div class="pointer-events-none opacity-25">
        <x-translation-workbench::ui.tw-graph.strang.trunk
            id="literature.center.1.rekey-reference"
            color="zinc"
            :stem-count="5"
            start-length="4rem"
            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem', 5 => '5rem']"
            end-length="3rem"
            :dev-mode="false"
            :start-label="[
                'text' => ['reference trunk'],
                'width' => 'default',
                'align' => 'center',
            ]"
        />
    </div>

    <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
        id="literature.left.source.1.old-title"
        attach-to="strang.trunk.node.3"
        bridge-length="18rem"
        stem-length="5rem"
        :start-label="[
            'text' => ['rekey source', 'from note ID #12'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
        id="literature.right.target.1.new-paper"
        attach-to="strang.trunk.node.4"
        bridge-length="18rem"
        stem-length="5rem"
        :end-label="[
            'text' => ['rekey target', 'continues as paper ID #42'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />
</x-translation-workbench::ui.tw-graph>

@if ($renderMode === 'documentation')
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="rekeyVariant === 'source'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-07-rekey-source"
                    :dev="$dev"
                    :coordinates="$coordinates"
                    color="violet"
                    arc-size="2.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    slot-min-height="54rem"
                    horizontal-padding="54rem"
                    min-width="96rem"
                    min-height="54rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.rekey-reference"
                            color="zinc"
                            :stem-count="5"
                            start-length="4rem"
                            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem', 5 => '5rem']"
                            end-length="3rem"
                            :dev-mode="false"
                            :start-label="[
                                'text' => ['reference trunk'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                        />
                    </div>

                    <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
                        id="literature.left.source.1.old-title"
                        attach-to="strang.trunk.node.3"
                        bridge-length="22rem"
                        stem-length="6rem"
                        :start-label="[
                            'text' => ['rekey source', 'from notebook ID #12'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :node-labels="[
                            1 => [
                                'right' => [
                                    'text' => ['Origin key', 'notes.gravity.first'],
                                    'width' => 'halfLong',
                                    'align' => 'left',
                                ],
                            ],
                            'end' => [
                                'left' => [
                                    'text' => ['rekeyed into', 'current paper key'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>
@endif

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="rekeyVariant === 'target'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-07-rekey-target"
                    :dev="$dev"
                    :coordinates="$coordinates"
                    color="violet"
                    arc-size="2.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    slot-min-height="58rem"
                    horizontal-padding="58rem"
                    min-width="102rem"
                    min-height="58rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.rekey-reference"
                            color="zinc"
                            :stem-count="5"
                            start-length="4rem"
                            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem', 5 => '5rem']"
                            end-length="3rem"
                            :dev-mode="false"
                            :start-label="[
                                'text' => ['reference trunk'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                        />
                    </div>

                    <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
                        id="literature.right.target.1.new-paper"
                        attach-to="strang.trunk.node.4"
                        bridge-length="24rem"
                        stem-length="6rem"
                        end-length="4rem"
                        cap-length="2rem"
                        :end-label="[
                            'text' => ['rekey target to ID #42', '1905-06-30'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :node-labels="[
                            3 => [
                                'right' => [
                                    'text' => ['New key', 'papers.relativity.special'],
                                    'width' => 'halfLong',
                                    'align' => 'left',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>

            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                x-show="rekeyVariant === 'compressed'"
            >
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-07-rekey-compressed"
                    :dev="$dev"
                    :coordinates="$coordinates"
                    color="violet"
                    arc-size="2.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    slot-min-height="62rem"
                    horizontal-padding="58rem"
                    min-width="102rem"
                    min-height="62rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.rekey-reference"
                            color="zinc"
                            :stem-count="5"
                            start-length="4rem"
                            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem', 5 => '5rem']"
                            end-length="3rem"
                            :dev-mode="false"
                            :start-label="[
                                'text' => ['reference trunk'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                        />
                    </div>

                    <x-translation-workbench::ui.tw-graph.strang.rekey-source-right
                        id="literature.right.source.1.archive-key"
                        attach-to="strang.trunk.node.4"
                        bridge-length="22rem"
                        stem-length="6rem"
                        :start-label="[
                            'text' => ['rekey source', 'history gap'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :compressed-stem-parts="[
                            'beforeLength' => '1rem',
                            'gapLength' => '2rem',
                            'afterLength' => '1rem',
                            'capLength' => '1.25rem',
                        ]"
                        :stem-continuation="[
                            1 => [
                                'length' => '5rem',
                                'compressed' => true,
                                'left' => [
                                    'text' => ['omitted history', 'several draft steps'],
                                    'width' => 'halfLong',
                                    'align' => 'right',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
            </div>
