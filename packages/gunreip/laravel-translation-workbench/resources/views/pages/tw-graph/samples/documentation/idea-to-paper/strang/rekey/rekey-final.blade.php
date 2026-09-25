{{-- Graph-only final snapshot for the documentation master. --}}
<x-translation-workbench::ui.tw-graph
    :graph-id="$ideaToPaperGraphId ?? 'idea-to-paper-step-07-rekey'"
    :dev="$dev ?? false"
    :coordinates="$coordinates ?? false"
    color="violet"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-radius="2.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
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
