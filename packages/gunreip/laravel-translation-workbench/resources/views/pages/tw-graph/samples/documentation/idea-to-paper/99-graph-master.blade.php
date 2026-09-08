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
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey',
    ];
    $latestSectionView = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationViewResolver::latestOrdinalViewAcrossSections($sections);
@endphp

@if ($latestSectionView)
    @include($latestSectionView, [
        'dev' => $ideaToPaperDev,
        'coordinates' => $ideaToPaperCoordinates,
        'ideaToPaperGraphId' => $ideaToPaperGraphId,
        'ideaToPaperDev' => $ideaToPaperDev,
        'ideaToPaperCoordinates' => $ideaToPaperCoordinates,
        'renderMode' => 'graph',
    ])
@endif
