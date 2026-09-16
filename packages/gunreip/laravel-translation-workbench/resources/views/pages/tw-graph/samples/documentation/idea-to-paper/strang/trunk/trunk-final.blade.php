{{-- Graph-only final snapshot for the documentation master. --}}
<x-translation-workbench::ui.tw-graph
    class="px-20 py-12"
    :graph-id="$ideaToPaperGraphId ?? 'idea-to-paper-step-04-trunk-end'"
    :dev="$dev ?? false"
    :coordinates="$coordinates ?? false"
    color="indigo"
    line-length="4rem"
    line-width="0.25rem"
    node-size="0.95rem"
    arc-size="2.75rem"
    cap-length="1.75rem"
    bridge-length="18rem"
    stem-length="5rem"
    connector-length="2rem"
    connector-gap="0.25rem"
    horizontal-padding="28rem"
    min-width="48rem"
    min-height="42rem"
>
    <x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        :stem-count="4"
        start-length="4rem"
        :stem-lengths="[
            1 => '5rem',
            2 => '5rem',
            3 => '5rem',
            4 => '5rem',
        ]"
        end-length="3rem"
        end-cap-length="1.75rem"
        :start-label="[
            'text' => ['Idea development', 'notes to paper'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :end-label="[
            'text' => ['draft complete', 'ready for review'],
            'width' => 'default',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['1879 notebook', 'raw observation'],
                'width' => 'default',
                'align' => 'right',
                'color' => 'indigo',
            ],
            'right' => [
                'text' => ['A loose idea is captured as a short note.'],
                'width' => 'long',
                'align' => 'left',
                'justify' => true,
                'color' => 'zinc',
            ],
        ]"
        :node-labels="[
            2 => [
                'left' => [
                    'text' => ['1888 outline', 'first structure'],
                    'width' => 'default',
                    'align' => 'right',
                ],
                'right' => [
                    'text' => ['The note receives a rough order and becomes a working outline.'],
                    'width' => 'long',
                    'align' => 'left',
                    'justify' => true,
                ],
            ],
        ]"
    />
</x-translation-workbench::ui.tw-graph>
