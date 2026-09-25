{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/99-graph-master.blade.php --}}

@php
    $ideaToPaperGraphId = $ideaToPaperGraphId ?? 'tw-graph-sample-idea-to-paper-master';
    $ideaToPaperDev = $ideaToPaperDev ?? false;
    $ideaToPaperCoordinates = $ideaToPaperCoordinates ?? false;
@endphp

{{--
    Master graph include:
    The authoring sections should eventually produce small section-final partials.
    Those section finals belong here, so the visible documentation and the final
    graph stay in the same directory and cannot drift into separate implementations.

    The thought draft must not be included here. The master output is assembled
    only from the latest ordinal step in each documented section.
--}}
@php
    $sections = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-final',
    ];
    $latestSectionView = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationViewResolver::latestOrdinalViewAcrossSections($sections);
@endphp

@if ($latestSectionView)
    @include($latestSectionView, [


        'ideaToPaperGraphId' => $ideaToPaperGraphId,
        'ideaToPaperDev' => $ideaToPaperDev,
        'ideaToPaperCoordinates' => $ideaToPaperCoordinates,
        'renderMode' => 'graph',
    ])
@endif
