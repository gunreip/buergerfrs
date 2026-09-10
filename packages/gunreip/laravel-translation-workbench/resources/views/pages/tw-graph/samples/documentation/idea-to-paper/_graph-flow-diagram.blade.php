{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/_graph-flow-diagram.blade.php --}}

@php
    $ideaToPaperGraphId = $ideaToPaperGraphId ?? 'tw-graph-sample-idea-to-paper-flow-diagram';
    $ideaToPaperDev = $ideaToPaperDev ?? false;
    $ideaToPaperCoordinates = $ideaToPaperCoordinates ?? false;
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/_graph-flow-diagram.blade.php';
@endphp

<x-translation-workbench::ui.tw-graph
    class="px-24 py-14"
    :graph-id="$ideaToPaperGraphId"
    :dev="$ideaToPaperDev"
    :coordinates="$ideaToPaperCoordinates"
    color="cyan"
    stem-length="5rem"
    slot-min-height="72rem"
    horizontal-padding="46rem"
    min-width="92rem"
    min-height="72rem"
>
    {{-- Flow diagram result, expanded as the flow section gains new components. --}}
    <x-translation-workbench::ui.tw-graph.strang.flow-start
        id="literature.flow.1.paper-process"
        start-length="7rem"
        :node-end-dot="false"
        :start-label="[
            'text' => ['Paper process', 'start'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :start-node-labels="[
            'left' => [
                'text' => ['Input', 'raw thought'],
                'width' => 'default',
                'align' => 'right',
            ],
            'right' => [
                'text' => ['Next', 'draft decision'],
                'width' => 'default',
                'align' => 'left',
            ],
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.flow-step
        id="literature.flow.1.paper-process.step-1"
        :anchor-start="['x' => '0rem', 'y' => '7rem']"
        before-length="2rem"
        after-length="3rem"
        :step-label="[
            'text' => ['Draft prepared', 'structure exists'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :node-labels="[
            'end' => [
                'right' => [
                    'text' => ['Next', 'review choice'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.flow-decision
        id="literature.flow.1.paper-process.decision-1"
        :anchor-start="['x' => '0rem', 'y' => '16.25rem']"
        bridge-length="21rem"
        :decision-label="[
            'text' => ['Peer review', 'decision'],
            'width' => 'halfLong',
            'align' => 'center',
            'connectorLength' => '3.5rem',
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.flow-step
        id="literature.flow.1.paper-process.revision-step"
        :anchor-start="['x' => '-26.5rem', 'y' => '21.75rem']"
        before-length="1.5rem"
        after-length="2.5rem"
        :step-label="[
            'text' => ['Revision loop', 'comments resolved'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.flow-step
        id="literature.flow.1.paper-process.accepted-step"
        :anchor-start="['x' => '26.5rem', 'y' => '21.75rem']"
        before-length="1.5rem"
        after-length="2.5rem"
        :step-label="[
            'text' => ['Accepted path', 'publication prep'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>
