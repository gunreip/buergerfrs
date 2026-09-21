<x-translation-workbench::ui.tw-graph.documentation-links example="_graph-flow-diagram" />
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

    <x-translation-workbench::ui.tw-graph.strang.flow-if-else
        id="literature.flow.1.paper-process.decision-1"
        attach-to="literature.flow.1.paper-process.step-1.anchorNode-end"
        bridge-length="21rem"
        :condition-label="[
            'text' => ['IF reviewApproved?'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :if-start="[
            'text' => ['True', 'Accepted path', 'publication prep'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :if-end="[
            'text' => ['False', 'Revision loop', 'comments resolved'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.flow-step
        id="literature.flow.1.paper-process.continue-step"
        attach-to="literature.flow.1.paper-process.decision-1.anchorNode-end"
        before-length="1.5rem"
        after-length="2.5rem"
        :step-label="[
            'text' => ['Continue process'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />
</x-translation-workbench::ui.tw-graph>
<flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
    {{ $thisPath }}
</flux:field>
