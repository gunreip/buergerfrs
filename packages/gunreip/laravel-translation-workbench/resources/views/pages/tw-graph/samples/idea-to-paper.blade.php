{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/idea-to-paper.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Idea To Paper')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Idea To Paper')"
            :description="__(
                'Hand-authored tw-graph authoring story for notes, drafts, review, revision, and publication without database-backed timeline data.',
            )"
        />

        @php
            $ideaToPaperDev = true;
            $ideaToPaperCoordinates = false;
        @endphp

        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index', [
            'dev' => $ideaToPaperDev,
            'coordinates' => $ideaToPaperCoordinates,
        ])

        {{--
            Hidden thought draft:
            This full graph is the current target shape for the authoring story.
            It stays out of the visible page so the documentation can rebuild the graph
            from first idea to finished visual, step by step.

            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-graph-final', [
                'ideaToPaperGraphId' => 'tw-graph-sample-idea-to-paper-final-draft',
                'ideaToPaperDev' => false,
                'ideaToPaperCoordinates' => false,
            ])
        --}}
    </flux:card>
</x-layouts::app>
