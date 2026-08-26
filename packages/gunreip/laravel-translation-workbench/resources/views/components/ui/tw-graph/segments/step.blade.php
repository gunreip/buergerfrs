{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/step.blade.php --}}
{{--
    Segment: step

    Usage:
    <x-translation-workbench::ui.tw-graph.segments.step :segment="$segment" />

    Segment role:
    Step is a centered path description between two line segments. It is meant
    for common strang-level reasons/statuses such as "source inactive" while
    node labels remain reserved for concrete data-record information.

    Structure:
    segments.step
    |-- segments.path before
    |   |-- primitives.line
    |       nodeStart optional, default false
    |       nodeEnd false
    |-- primitives.text step-label
    |-- segments.path after
        |-- primitives.line
            nodeStart false
            nodeEnd optional, default true

    Defaults:
    direction=bottom-top
    beforeLength=2rem
    labelGap=auto from stepLabel line count
    afterLength=2rem
    stepCaps=true
    nodeStart=false
    nodeEnd=true

    Optional fields:
    stepLabel{text, side, offset, badgeColor, long}
    stepCaps, capLength
--}}

@props([
    'segment' => [],
    'dev' => null,
])

@php
    $stepSegment = array_replace([
        'id' => 'segment.step',
        'direction' => 'bottom-top',
        'beforeLength' => '2rem',
        'labelGap' => null,
        'afterLength' => '2rem',
        'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
        'nodeStart' => false,
        'nodeEnd' => true,
        'gradient' => false,
        'cap' => false,
        'stepCaps' => true,
        'capLength' => '1.25rem',
        'color' => 'green',
    ], $segment);

    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $advance = function (array $anchor, string $length) use ($add, $neg, $stepSegment): array {
        $x = data_get($anchor, 'x', '0rem');
        $y = data_get($anchor, 'y', '0rem');

        return match (data_get($stepSegment, 'direction', 'bottom-top')) {
            'left-right' => ['x' => $add($x, $length), 'y' => $y],
            'right-left' => ['x' => $add($x, $neg($length)), 'y' => $y],
            'top-bottom' => ['x' => $x, 'y' => $add($y, $neg($length))],
            default => ['x' => $x, 'y' => $add($y, $length)],
        };
    };

    $stepLabelConfig = data_get($stepSegment, 'stepLabel');
    if (! is_array($stepLabelConfig) && filled($stepLabelConfig)) {
        $stepLabelConfig = ['text' => $stepLabelConfig];
    }
    $stepLabelLines = collect(is_iterable(data_get($stepLabelConfig, 'text')) && ! is_string(data_get($stepLabelConfig, 'text')) ? data_get($stepLabelConfig, 'text') : [data_get($stepLabelConfig, 'text')])
        ->filter(fn (mixed $line): bool => filled($line))
        ->take(3)
        ->count();
    $autoLabelGap = match ($stepLabelLines) {
        1 => '2.75rem',
        2 => '3.75rem',
        3 => '4.75rem',
        default => '3.75rem',
    };

    $anchorStart = [
        'x' => data_get($stepSegment, 'anchorStart.x', '0rem'),
        'y' => data_get($stepSegment, 'anchorStart.y', '0rem'),
    ];
    $beforeLength = (string) data_get($stepSegment, 'beforeLength', '2rem');
    $labelGap = (string) (data_get($stepSegment, 'labelGap') ?: $autoLabelGap);
    $afterLength = (string) data_get($stepSegment, 'afterLength', '2rem');
    $anchorStep = data_get($stepSegment, 'anchorStep');
    $anchorBeforeEnd = $advance($anchorStart, $beforeLength);
    $anchorMiddle = is_array($anchorStep)
        ? ['x' => data_get($anchorStep, 'x', '0rem'), 'y' => data_get($anchorStep, 'y', '0rem')]
        : $advance($anchorBeforeEnd, 'calc(' . $labelGap . ' / 2)');
    $anchorAfterStart = $advance($anchorBeforeEnd, $labelGap);
    $anchorEnd = data_get($stepSegment, 'anchorEnd');
    $anchorEnd = is_array($anchorEnd)
        ? ['x' => data_get($anchorEnd, 'x', '0rem'), 'y' => data_get($anchorEnd, 'y', '0rem')]
        : $advance($anchorAfterStart, $afterLength);

    $devMode = (bool) ($dev ?? data_get($stepSegment, 'dev', false));
    $direction = data_get($stepSegment, 'direction', 'bottom-top');
    $stepLabelSide = 'center';

    $beforeSegment = array_replace($stepSegment, [
        'id' => data_get($stepSegment, 'id', 'segment.step') . '.stem.before',
        'length' => $beforeLength,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $anchorBeforeEnd,
        'nodeStart' => data_get($stepSegment, 'nodeStart', false),
        'nodeEnd' => false,
        'capStart' => false,
        'capEnd' => (bool) data_get($stepSegment, 'stepCaps', true),
        'capLength' => data_get($stepSegment, 'capLength', '1.25rem'),
        'devCounterStart' => data_get($stepSegment, 'devCounterStart', 'S'),
        'devCounterEnd' => null,
    ]);

    $afterSegment = array_replace($stepSegment, [
        'id' => data_get($stepSegment, 'id', 'segment.step') . '.stem.after',
        'length' => $afterLength,
        'anchorStart' => $anchorAfterStart,
        'anchorEnd' => $anchorEnd,
        'nodeStart' => false,
        'nodeEnd' => data_get($stepSegment, 'nodeEnd', true),
        'capStart' => (bool) data_get($stepSegment, 'stepCaps', true),
        'capEnd' => false,
        'capLength' => data_get($stepSegment, 'capLength', '1.25rem'),
        'devCounterStart' => null,
        'devCounterEnd' => data_get($stepSegment, 'devCounterEnd', 'E'),
    ]);

@endphp

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$beforeSegment"
    :dev="$devMode"
/>

@if (is_array($stepLabelConfig) && filled(data_get($stepLabelConfig, 'text')))
    <x-translation-workbench::ui.tw-graph.primitives.text
        :id="data_get($stepSegment, 'id', 'segment.step') . '.label'"
        :text="data_get($stepLabelConfig, 'text')"
        :anchor-x="data_get($anchorMiddle, 'x', '0rem')"
        :anchor-y="data_get($anchorMiddle, 'y', '0rem')"
        :side="data_get($stepLabelConfig, 'side', $stepLabelSide)"
        :offset="data_get($stepLabelConfig, 'offset', '0rem')"
        :badge="data_get($stepLabelConfig, 'badge', true)"
        :badge-color="data_get($stepLabelConfig, 'badgeColor', data_get($stepSegment, 'color', 'green'))"
        :long="data_get($stepLabelConfig, 'long', false)"
    />
@endif

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$afterSegment"
    :dev="$devMode"
/>
