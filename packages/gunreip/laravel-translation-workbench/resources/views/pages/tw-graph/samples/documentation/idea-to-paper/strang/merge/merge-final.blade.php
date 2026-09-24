{{-- Graph-only snapshot of the former latest Merge documentation step. --}}
<x-translation-workbench::ui.tw-graph
    class="px-24 py-16"
    :graph-id="$ideaToPaperGraphId ?? 'idea-to-paper-step-07-merge-mismatch'"
    :dev="$dev ?? false"
    :coordinates="$coordinates ?? false"
    color="amber"
    arc-radius="2.75rem"
    bridge-length="18rem"
    stem-length="5rem"
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
