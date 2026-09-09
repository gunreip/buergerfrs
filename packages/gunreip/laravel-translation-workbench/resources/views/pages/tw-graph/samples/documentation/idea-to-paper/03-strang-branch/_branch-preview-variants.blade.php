@if ($renderMode === 'documentation')
    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="branchVariant === 'default'"
    >
        <div class="flex justify-end px-4 pt-3">
            <flux:badge
                size="sm"
                color="zinc"
            >
                {{ __('Branch default') }}
            </flux:badge>
        </div>
@endif
<x-translation-workbench::ui.tw-graph
    class="px-24 py-16"
    :graph-id="$graphId"
    :dev="$dev"
    :coordinates="$coordinates"
    color="rose"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-size="2.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
    slot-min-height="46rem"
    horizontal-padding="46rem"
    min-width="82rem"
    min-height="46rem"
>
    <div class="pointer-events-none opacity-25">
        <x-translation-workbench::ui.tw-graph.strang.trunk
            id="literature.center.1.branch-reference"
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

    <x-translation-workbench::ui.tw-graph.strang.branch-left
        id="literature.left.1.side-thought"
        attach-to="strang.trunk.node.2"
        bridge-length="18rem"
        stem-length="5rem"
        :node-labels="[
            3 => [
                'left' => [
                    'text' => ['side thought', 'kept separate'],
                    'width' => 'default',
                    'align' => 'right',
                ],
            ],
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.branch-right
        id="literature.right.1.side-thought"
        attach-to="strang.trunk.node.2"
        bridge-length="18rem"
        stem-length="5rem"
        :node-labels="[
            3 => [
                'right' => [
                    'text' => ['parallel thought', 'kept separate'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="branchVariant === 'offset'"
    >
        <div class="flex justify-end px-4 pt-3">
            <flux:badge
                size="sm"
                color="rose"
            >
                {{ __('Different anchors') }}
            </flux:badge>
        </div>
        <x-translation-workbench::ui.tw-graph
            class="px-24 py-16"
            graph-id="idea-to-paper-step-06-branch-offset"
            :dev="$dev"
            :coordinates="$coordinates"
            color="rose"
            arc-size="2.75rem"
            bridge-length="18rem"
            stem-length="5rem"
            slot-min-height="58rem"
            horizontal-padding="52rem"
            min-width="92rem"
            min-height="58rem"
        >
            <div class="pointer-events-none opacity-25">
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    id="literature.center.1.branch-reference"
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

            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="literature.left.1.question"
                attach-to="strang.trunk.node.2"
                bridge-length="20rem"
                :node-labels="[
                    3 => [
                        'left' => [
                            'text' => ['question raised', 'before drafting'],
                            'width' => 'default',
                            'align' => 'right',
                        ],
                    ],
                ]"
            />

            <x-translation-workbench::ui.tw-graph.strang.branch-right
                id="literature.right.1.review"
                attach-to="strang.trunk.node.4"
                bridge-length="24rem"
                :node-labels="[
                    3 => [
                        'right' => [
                            'text' => ['review branch', 'later in the chain'],
                            'width' => 'default',
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

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="branchVariant === 'mismatch'"
    >
        <div class="flex justify-end px-4 pt-3">
            <flux:badge
                size="sm"
                color="red"
            >
                {{ __('Node label mismatch') }}
            </flux:badge>
        </div>
        <x-translation-workbench::ui.tw-graph
            class="px-24 py-16"
            graph-id="idea-to-paper-step-06-branch-mismatch"
            :dev="$dev"
            :coordinates="$coordinates"
            color="rose"
            arc-size="2.75rem"
            bridge-length="18rem"
            stem-length="5rem"
            slot-min-height="46rem"
            horizontal-padding="50rem"
            min-width="88rem"
            min-height="46rem"
        >
            <div class="pointer-events-none opacity-25">
                <x-translation-workbench::ui.tw-graph.strang.trunk
                    id="literature.center.1.branch-reference"
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

            <x-translation-workbench::ui.tw-graph.strang.branch-left
                id="literature.left.1.label-mismatch"
                attach-to="strang.trunk.node.2"
                bridge-length="18rem"
                stem-length="5rem"
                :node-labels="[
                    3 => [
                        'left' => [
                            'text' => ['Valid label', 'branch arc end'],
                            'width' => 'default',
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
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            {{ $thisPath }}
        </flux:field>
    </div>
@endif

<div
    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
    x-show="branchVariant === 'step'"
>
    <div class="flex justify-end px-4 pt-3">
        <flux:badge
            size="sm"
            color="amber"
        >
            {{ __('Branch step') }}
        </flux:badge>
    </div>
    <x-translation-workbench::ui.tw-graph
        class="px-24 py-16"
        graph-id="idea-to-paper-step-06-branch-step"
        :dev="$dev"
        :coordinates="$coordinates"
        color="rose"
        arc-size="2.75rem"
        bridge-length="18rem"
        stem-length="5rem"
        slot-min-height="52rem"
        horizontal-padding="50rem"
        min-width="88rem"
        min-height="52rem"
    >
        <div class="pointer-events-none opacity-25">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.branch-reference"
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

        <x-translation-workbench::ui.tw-graph.strang.branch-right
            id="literature.right.1.decision"
            attach-to="strang.trunk.node.3"
            bridge-length="22rem"
            :step="[
                'beforeLength' => '1.5rem',
                'afterLength' => '2.5rem',
                'stepLabel' => [
                    'text' => ['Decision', 'keep as appendix'],
                    'width' => 'halfLong',
                    'align' => 'center',
                ],
            ]"
        />
    </x-translation-workbench::ui.tw-graph>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
        {{ $thisPath }}
    </flux:field>
</div>

<div
    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
    x-show="branchVariant === 'continuation'"
>
    <div class="flex justify-end px-4 pt-3">
        <flux:badge
            size="sm"
            color="pink"
        >
            {{ __('Branch continuation') }}
        </flux:badge>
    </div>
    <x-translation-workbench::ui.tw-graph
        class="px-24 py-16"
        graph-id="idea-to-paper-step-06-branch-continuation"
        :dev="$dev"
        :coordinates="$coordinates"
        color="rose"
        arc-size="2.75rem"
        bridge-length="18rem"
        stem-length="5rem"
        slot-min-height="66rem"
        horizontal-padding="52rem"
        min-width="92rem"
        min-height="66rem"
    >
        <div class="pointer-events-none opacity-25">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.branch-reference"
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

        <x-translation-workbench::ui.tw-graph.strang.branch-left
            id="literature.left.1.archive-path"
            attach-to="strang.trunk.node.2"
            bridge-length="20rem"
            :stem-continuation="[
                1 => [
                    'length' => '5rem',
                    'left' => [
                        'text' => ['Archive copy', 'kept for traceability'],
                        'width' => 'halfLong',
                        'align' => 'right',
                    ],
                ],
                2 => [
                    'length' => '5rem',
                    'right' => [
                        'text' => ['Later cited', 'by reviewer'],
                        'width' => 'default',
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

<div
    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
    x-show="branchVariant === 'return'"
>
    <div class="flex justify-end px-4 pt-3">
        <flux:badge
            size="sm"
            color="orange"
        >
            {{ __('Branch return') }}
        </flux:badge>
    </div>
    <x-translation-workbench::ui.tw-graph
        class="px-24 py-16"
        graph-id="idea-to-paper-step-06-branch-return"
        :dev="$dev"
        :coordinates="$coordinates"
        color="rose"
        arc-size="2.75rem"
        bridge-length="18rem"
        stem-length="5rem"
        slot-min-height="58rem"
        horizontal-padding="54rem"
        min-width="96rem"
        min-height="58rem"
    >
        <div class="pointer-events-none opacity-25">
            <x-translation-workbench::ui.tw-graph.strang.trunk
                id="literature.center.1.branch-reference"
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

        <x-translation-workbench::ui.tw-graph.strang.branch-right
            id="literature.right.1.review-loop"
            attach-to="strang.trunk.node.2"
            bridge-length="22rem"
            :stem-continuation="[
                1 => [
                    'length' => '4.1rem',
                    'right' => [
                        'text' => ['Review loop', 'comment resolved'],
                        'width' => 'default',
                        'align' => 'left',
                    ],
                ],
            ]"
            :branch-return="[
                1 => [
                    'attachTo' => 'stem.1.end',
                    'closeTo' => '+3',
                    'bridgeLength' => '22rem',
                    'fallback' => false,
                ],
            ]"
        />
    </x-translation-workbench::ui.tw-graph>
    <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
        {{ $thisPath }}
    </flux:field>
</div>
