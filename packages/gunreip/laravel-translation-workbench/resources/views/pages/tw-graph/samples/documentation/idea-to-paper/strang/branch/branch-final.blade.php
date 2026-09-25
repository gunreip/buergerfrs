{{-- Graph-only final snapshot for the documentation master. --}}
<x-translation-workbench::ui.tw-graph
    :graph-id="$ideaToPaperGraphId ?? 'idea-to-paper-step-06-branch'"
    :dev="$dev ?? false"
    :coordinates="$coordinates ?? false"
    color="rose"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-radius="2.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
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
