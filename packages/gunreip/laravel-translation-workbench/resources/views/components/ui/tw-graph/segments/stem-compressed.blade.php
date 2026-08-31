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
    beforeLength=1rem
    gapLength=1rem (dotted stem length without anchorEnd)
    afterLength=1rem
    With anchorEnd, the segment uses a proportional 1/4 -> 2/4 -> 1/4
    split so the dotted stem stays visibly recognizable.
    nodeStart=false
    nodeEnd=true
--}}

@props([
    'segment' => [],
    'dev' => null,
])

@php
    $compressedSegment = array_replace(
        [
            'id' => 'segment.stem-compressed',
            'direction' => 'bottom-top',
            'beforeLength' => '2.5rem',
            'gapLength' => '0.5rem',
            'afterLength' => '1.5rem',
            'capLength' => null,
            'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
            'nodeStart' => false,
            'nodeEnd' => true,
            'devCounterStart' => 'S',
            'devCounterEnd' => 'E',
            'devCounterColor' => 'zinc',
            'color' => 'cyan',
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
    $beforeLength = (string) (data_get($compressedSegment, 'beforeLength') ?: '1rem');
    $gapLength = (string) (data_get($compressedSegment, 'gapLength') ?: '1rem');
    $afterLength = (string) (data_get($compressedSegment, 'afterLength') ?: '1rem');
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
        $beforeLength = 'calc(' . $totalLength . ' / 4)';
        $gapLength = 'calc(' . $totalLength . ' / 2)';
        $afterLength = $beforeLength;
    }

    $anchorBeforeEnd = $advance($anchorStart, $beforeLength);
    $anchorGapEnd = $advance($anchorBeforeEnd, $gapLength);
    $anchorEnd ??= $advance($anchorGapEnd, $afterLength);
    $devMode = (bool) ($dev ?? data_get($compressedSegment, 'dev', false));

    $beforeSegment = array_replace($compressedSegment, [
        'id' => data_get($compressedSegment, 'id', 'segment.stem-compressed') . '.stem.before',
        'length' => $beforeLength,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $anchorBeforeEnd,
        'nodeStart' => data_get($compressedSegment, 'nodeStart', false),
        'nodeEnd' => false,
        'capEnd' => true,
        'capLength' => data_get($compressedSegment, 'capLength', '1.25rem'),
        'devCounterStart' => data_get($compressedSegment, 'devCounterStart', 'S'),
        'devCounterEnd' => null,
    ]);
    $dottedSegment = array_replace($compressedSegment, [
        'id' => data_get($compressedSegment, 'id', 'segment.stem-compressed') . '.stem.dotted',
        'length' => $gapLength,
        'anchorStart' => $anchorBeforeEnd,
        'anchorEnd' => $anchorGapEnd,
        'nodeStart' => false,
        'nodeEnd' => false,
        'dashed' => true,
        'devCounterStart' => null,
        'devCounterEnd' => null,
    ]);
    $afterSegment = array_replace($compressedSegment, [
        'id' => data_get($compressedSegment, 'id', 'segment.stem-compressed') . '.stem.after',
        'length' => $afterLength,
        'anchorStart' => $anchorGapEnd,
        'anchorEnd' => $anchorEnd,
        'nodeStart' => false,
        'capStart' => true,
        'capLength' => data_get($compressedSegment, 'capLength', '1.25rem'),
        'nodeEnd' => data_get($compressedSegment, 'nodeEnd', true),
        'devCounterStart' => null,
        'devCounterEnd' => data_get($compressedSegment, 'devCounterEnd', 'E'),
    ]);
@endphp

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$beforeSegment"
    :dev="$devMode"
/>

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$dottedSegment"
    :dev="$devMode"
/>

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$afterSegment"
    :dev="$devMode"
/>
