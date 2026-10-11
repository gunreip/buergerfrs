@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/stem-compressed.blade.php --}}
{{--
    Segment: stem-compressed

    Usage:
    <x-translation-workbench::ui.tw-graph.segments.stem-compressed :segment="$segment" />

    Segment role:
    A purely graphical omission marker for a vertical lifecycle stem:
    short stem -> cap -> dotted stem -> cap -> short stem.

    Structure:
    segments.stem-compressed
    |-- segments.path before
    |   capEnd=true
    |-- segments.path dotted
    |   dashed=true
    |-- segments.path after
        capStart=true

    Defaults:
    direction=bottom-top
    beforeLength=2rem
    gapLength=4rem (dotted stem length without anchorEnd)
    afterLength=2rem
    With anchorEnd, the segment uses a proportional 1/4 -> 2/4 -> 1/4
    split so the dotted stem stays visibly recognizable.
    nodeStart=false
    nodeEnd=true
--}}

@props([
    'segment' => [],
])

@php
    $defaultSections = \Gunreip\TranslationWorkbench\Support\TwGraph\CompressedStemLayout::sections();
    $compressedSegment = array_replace(
        [
            'id' => 'segment.stem-compressed',
            'direction' => 'bottom-top',
            ...$defaultSections,
            'capLength' => null,
            'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
            'nodeStart' => false,
            'nodeEnd' => true,
            'devCounterStart' => 'S',
            'devCounterEnd' => 'E',
            'devCounterColor' => 'zinc',
            'color' => 'zinc',
            'zIndex' => null,
        ],
        $segment,
    );

    $add = fn(string $value, string $delta): string => $delta === '0rem'
        ? $value
        : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn(string $value): string => 'calc(' . $value . ' * -1)';
    $advance = function (array $anchor, string $length) use ($add, $compressedSegment, $neg): array {
        $x = data_get($anchor, 'x', '0rem');
        $y = data_get($anchor, 'y', '0rem');

        return match (data_get($compressedSegment, 'direction', 'bottom-top')) {
            'left-right' => ['x' => $add($x, $length), 'y' => $y],
            'right-left' => ['x' => $add($x, $neg($length)), 'y' => $y],
            'top-bottom' => ['x' => $x, 'y' => $add($y, $neg($length))],
            default => ['x' => $x, 'y' => $add($y, $length)],
        };
    };

    $anchorStart = [
        'x' => data_get($compressedSegment, 'anchorStart.x', '0rem'),
        'y' => data_get($compressedSegment, 'anchorStart.y', '0rem'),
    ];
    $beforeLength = (string) (data_get($compressedSegment, 'beforeLength') ?: $defaultSections['beforeLength']);
    $gapLength = (string) (data_get($compressedSegment, 'gapLength') ?: $defaultSections['gapLength']);
    $afterLength = (string) (data_get($compressedSegment, 'afterLength') ?: $defaultSections['afterLength']);
    $configuredAnchorEnd = data_get($compressedSegment, 'anchorEnd');
    $anchorEnd = is_array($configuredAnchorEnd)
        ? ['x' => data_get($configuredAnchorEnd, 'x', '0rem'), 'y' => data_get($configuredAnchorEnd, 'y', '0rem')]
        : null;

    if (is_array($configuredAnchorEnd)) {
        $totalLength = match (data_get($compressedSegment, 'direction', 'bottom-top')) {
            'left-right' => 'calc(' .
                data_get($anchorEnd, 'x', '0rem') .
                ' - ' .
                data_get($anchorStart, 'x', '0rem') .
                ')',
            'right-left' => 'calc(' .
                data_get($anchorStart, 'x', '0rem') .
                ' - ' .
                data_get($anchorEnd, 'x', '0rem') .
                ')',
            'top-bottom' => 'calc(' .
                data_get($anchorStart, 'y', '0rem') .
                ' - ' .
                data_get($anchorEnd, 'y', '0rem') .
                ')',
            default => 'calc(' . data_get($anchorEnd, 'y', '0rem') . ' - ' . data_get($anchorStart, 'y', '0rem') . ')',
        };
        $sections = \Gunreip\TranslationWorkbench\Support\TwGraph\CompressedStemLayout::sections($totalLength);
        $beforeLength = $sections['beforeLength'];
        $gapLength = $sections['gapLength'];
        $afterLength = $sections['afterLength'];
    }

    $anchorBeforeEnd = $advance($anchorStart, $beforeLength);
    $anchorGapEnd = $advance($anchorBeforeEnd, $gapLength);
    $anchorEnd ??= $advance($anchorGapEnd, $afterLength);


    $fromColor = data_get($compressedSegment, 'color', 'zinc');
    $toColor = data_get($compressedSegment, 'toColor') ?: $fromColor;
    $hasColorTransition = (bool) data_get($compressedSegment, 'colorGradient', false);
    $afterColor = $hasColorTransition ? $toColor : $fromColor;

    $beforeSegment = array_replace($compressedSegment, [
        'id' => data_get($compressedSegment, 'id', 'segment.stem-compressed') . '.stem.before',
        'length' => $beforeLength,
        'color' => $fromColor,
        'colorGradient' => false,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $anchorBeforeEnd,
        'nodeStart' => data_get($compressedSegment, 'nodeStart', false),
        'nodeEnd' => false,
        'nodeEndDot' => false,
        'jointArrowEnd' => false,
        'capEnd' => true,
        'capLength' => data_get($compressedSegment, 'capLength', '1.25rem'),
        'devCounterStart' => data_get($compressedSegment, 'devCounterStart', 'S'),
        'devCounterEnd' => null,
    ]);
    $dottedSegment = array_replace($compressedSegment, [
        'id' => data_get($compressedSegment, 'id', 'segment.stem-compressed') . '.stem.dotted',
        'length' => $gapLength,
        'color' => $fromColor,
        'toColor' => $toColor,
        'colorGradient' => $hasColorTransition,
        'anchorStart' => $anchorBeforeEnd,
        'anchorEnd' => $anchorGapEnd,
        'nodeStart' => false,
        'nodeStartDot' => false,
        'jointArrowStart' => false,
        'nodeEnd' => false,
        'nodeEndDot' => false,
        'jointArrowEnd' => false,
        'dashed' => true,
        'devCounterStart' => null,
        'devCounterEnd' => null,
    ]);
    $afterSegment = array_replace($compressedSegment, [
        'id' => data_get($compressedSegment, 'id', 'segment.stem-compressed') . '.stem.after',
        'length' => $afterLength,
        'color' => $afterColor,
        'devCounterColor' => $afterColor,
        'colorGradient' => false,
        'anchorStart' => $anchorGapEnd,
        'anchorEnd' => $anchorEnd,
        'nodeStart' => false,
        'nodeStartDot' => false,
        'jointArrowStart' => false,
        'capStart' => true,
        'capLength' => data_get($compressedSegment, 'capLength', '1.25rem'),
        'nodeEnd' => data_get($compressedSegment, 'nodeEnd', true),
        'devCounterStart' => null,
        'devCounterEnd' => data_get($compressedSegment, 'devCounterEnd', 'E'),
    ]);
@endphp

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$beforeSegment"
/>

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$dottedSegment"
/>

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$afterSegment"
/>
