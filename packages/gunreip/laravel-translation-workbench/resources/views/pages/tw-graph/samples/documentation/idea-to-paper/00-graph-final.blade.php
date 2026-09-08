{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/idea-to-paper/graph-final.blade.php --}}

@php
    $ideaToPaperGraphId = $ideaToPaperGraphId ?? 'tw-graph-sample-idea-to-paper-final-draft';
    $ideaToPaperDev = $ideaToPaperDev ?? false;
    $ideaToPaperCoordinates = $ideaToPaperCoordinates ?? false;
@endphp

<x-translation-workbench::ui.tw-graph
    class="px-28 py-14"
    :graph-id="$ideaToPaperGraphId"
    :dev="$ideaToPaperDev"
    :coordinates="$ideaToPaperCoordinates"
    color="indigo"
    line-length="4rem"
    bridge-length="18rem"
    stem-length="5rem"
    slot-min-height="94rem"
    horizontal-padding="40rem"
>
    {{-- central idea lifecycle --}}
    <x-translation-workbench::ui.tw-graph.strang.trunk
        id="literature.center.1.paper"
        :stem-count="13"
        start-length="4rem"
        :stem-lengths="[
            1 => '6rem',
            2 => '4rem',
            3 => '5rem',
            4 => '6rem',
            5 => '5rem',
            6 => '8rem',
            7 => '5rem',
            8 => '6rem',
            9 => '6rem',
            10 => '5rem',
            11 => '7rem',
            12 => '5rem',
            13 => '5rem',
        ]"
        end-length="4rem"
        :start-label="[
            'text' => ['Idea development', 'notes to paper'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :end-label="[
            'text' => ['Publication', 'accepted paper', '- IDEA CHAIN END -'],
            'width' => 'long',
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
                'text' => [
                    'A loose idea is captured as a short note before it has structure, citations, or a working title.',
                ],
                'width' => 'long',
                'align' => 'left',
                'justify' => true,
                'color' => 'zinc',
            ],
        ]"
        :node-labels="[
            1 => [
                'left' => [
                    'text' => ['Research question', 'What explains the anomaly?'],
                    'width' => 'halfLong',
                    'align' => 'right',
                    'color' => 'indigo',
                ],
            ],
            2 => [
                'right' => [
                    'text' => ['Sources collected', 'articles and marginal notes'],
                    'width' => 'halfLong',
                    'align' => 'left',
                    'color' => 'sky',
                ],
            ],
            3 => [
                'left' => [
                    'text' => ['Outline', 'argument map'],
                    'width' => 'default',
                    'align' => 'right',
                    'color' => 'indigo',
                ],
                'right' => [
                    'text' => ['Sections', 'problem, method, evidence, claim'],
                    'width' => 'halfLong',
                    'align' => 'left',
                    'color' => 'zinc',
                ],
            ],
            5 => [
                'left' => [
                    'text' => ['First draft', 'complete but rough'],
                    'width' => 'default',
                    'align' => 'right',
                    'color' => 'amber',
                ],
            ],
            7 => [
                'right' => [
                    'text' => ['Review round', 'external critique'],
                    'width' => 'default',
                    'align' => 'left',
                    'color' => 'rose',
                ],
            ],
            9 => [
                'left' => [
                    'text' => ['Revision', 'claim narrowed'],
                    'width' => 'default',
                    'align' => 'right',
                    'color' => 'emerald',
                ],
                'right' => [
                    'text' => ['New title', 'from working note to manuscript identity'],
                    'width' => 'long',
                    'align' => 'left',
                    'justify' => true,
                    'color' => 'sky',
                ],
            ],
            12 => [
                'left' => [
                    'text' => ['Copyedit', 'figures, references, abstract'],
                    'width' => 'halfLong',
                    'align' => 'right',
                    'color' => 'zinc',
                ],
            ],
        ]"
    />

    {{-- source material is rekeyed into the manuscript chain --}}
    <x-translation-workbench::ui.tw-graph.strang.rekey-source-left
        id="literature.left.1.note-source"
        attach-to="strang.trunk.node.3"
        color="sky"
        bridge-length="20rem"
        stem-length="4rem"
        :start-label="[
            'text' => ['source note', 'notebook fragment'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :stem-continuation="[
            1 => [
                '4rem',
                'left' => [
                    'text' => ['Evidence cluster', 'quotes and citations'],
                    'width' => 'default',
                    'align' => 'right',
                    'color' => 'sky',
                ],
            ],
        ]"
        :node-labels="[
            1 => [
                'left' => [
                    'text' => ['Field note', 'unstructured context'],
                    'width' => 'default',
                    'align' => 'right',
                ],
            ],
            2 => [
                'left' => [
                    'text' => ['Compressed middle', 'more reading exists here'],
                    'width' => 'halfLong',
                    'align' => 'right',
                ],
            ],
            5 => [
                'right' => [
                    'text' => ['rekeyed into', 'outline node'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"
    />

    {{-- reviewer path that ends before publication --}}
    <x-translation-workbench::ui.tw-graph.strang.branch-right
        id="literature.right.1.review-objection"
        attach-to="strang.trunk.node.7"
        color="rose"
        entry-stem-length="0.35rem"
        bridge-length="24rem"
        stem-length="4rem"
        :node-labels="[
            3 => [
                'right' => [
                    'text' => ['Reviewer objection', 'evidence too broad'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
        ]"
        :step="[
            'beforeLength' => '3rem',
            'afterLength' => '3rem',
            'stepLabel' => [
                'text' => ['Decision', 'revise argument'],
                'width' => 'halfLong',
            ],
        ]"
        :stem-continuation="[
            1 => [
                '4rem',
                'right' => [
                    'text' => ['Scope reduced', 'one claim removed'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
        ]"
        :branch-return="[
            1 => [
                'attachTo' => 'stem.1',
                'bridgeLength' => '24rem',
                'color' => 'rose',
                'fallback' => false,
            ],
        ]"
    />

    <x-translation-workbench::ui.tw-graph.strang.branch-end
        id="literature.right.1.review-objection.end"
        side="right"
        attach-to="strang.branch-right.end"
        color="rose"
        length="3rem"
        :end-label="[
            'text' => ['Objection closed', 'revision accepted'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
    />

    {{-- collaborator feedback merges into the first draft --}}
    <x-translation-workbench::ui.tw-graph.strang.merge-left
        id="literature.left.1.peer-notes"
        attach-to="strang.trunk.node.6"
        color="amber"
        bridge-length="24rem"
        stem-length="4rem"
        :start-label="[
            'text' => ['peer notes', 'draft margin'],
            'width' => 'halfLong',
            'align' => 'center',
        ]"
        :node-labels="[
            1 => [
                'right' => [
                    'text' => ['Method note', 'tighten comparison'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
            2 => [
                'right' => [
                    'text' => ['Citation hint', 'add counterexample'],
                    'width' => 'default',
                    'align' => 'left',
                ],
            ],
            5 => [
                'left' => [
                    'text' => ['merged into', 'first draft'],
                    'width' => 'default',
                    'align' => 'right',
                ],
            ],
        ]"
    />

    {{-- manuscript identity continues into a publication identity --}}
    <x-translation-workbench::ui.tw-graph.strang.rekey-target-right
        id="literature.right.1.publication-target"
        attach-to="strang.trunk.node.11"
        color="emerald"
        bridge-length="26rem"
        stem-length="4rem"
        :stem-continuation="[
            1 => [
                '4rem',
                'right' => [
                    'text' => ['Journal record', 'accepted manuscript'],
                    'width' => 'halfLong',
                    'align' => 'left',
                    'color' => 'emerald',
                ],
            ],
        ]"
        :node-labels="[
            3 => [
                'right' => [
                    'text' => ['DOI assigned', 'publication identity'],
                    'width' => 'halfLong',
                    'align' => 'left',
                ],
            ],
        ]"
        :end-label="[
            'text' => ['Rekey target', 'paper continues as publication', '2026-09-05'],
            'width' => 'long',
            'align' => 'center',
            'color' => 'emerald',
        ]"
    />
</x-translation-workbench::ui.tw-graph>
