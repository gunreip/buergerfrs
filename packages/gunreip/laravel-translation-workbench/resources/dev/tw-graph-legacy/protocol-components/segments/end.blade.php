{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/segments/end.blade.php --}}
{{--
    Segment: end

    Usage:
    <x-translation-workbench::ui.tw-graph-protocol.segments.end :segment="$segment" />

    Segment role:
    End is a path segment with end-specific defaults. It keeps the same
    primitives.line ownership model as segments.path for line/nodeStart, but
    its end is a capEnd, not a nodeEnd.

    Defaults:
    gradient=false
    cap=true
    nodeStart=true
    nodeEnd=false

    Optional fields:
    devCounterEnd
    endLabel{text, side, offset, badgeColor}
--}}

@props([
    'segment' => [],
    'dev' => null,
])

@php
    $endSegment = array_replace([
        'id' => 'segment.end',
        'direction' => 'bottom-top',
        'length' => '4rem',
        'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
        'nodeStart' => true,
        'nodeEnd' => false,
        'gradient' => false,
        'cap' => true,
        'capLength' => '1.25rem',
        'color' => 'green',
    ], $segment);
    $endSegment['nodeEnd'] = false;
    $endSegment['cap'] = true;
    $devMode = (bool) ($dev ?? data_get($endSegment, 'dev', false));
    $counterDistance = 'calc(var(--tw-graph-protocol-node-half) + var(--tw-graph-protocol-dev-node-counter-half))';
    $negativeCounterDistance = 'calc((var(--tw-graph-protocol-node-half) + var(--tw-graph-protocol-dev-node-counter-half)) * -1)';
    $capCounterOffset = match (data_get($endSegment, 'direction', 'bottom-top')) {
        'left-right' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
        'right-left' => ['x' => $counterDistance, 'y' => $counterDistance],
        'top-bottom' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
        default => ['x' => $counterDistance, 'y' => $negativeCounterDistance],
    };

    $endLabelSide = match (data_get($endSegment, 'direction', 'bottom-top')) {
        'left-right' => 'right',
        'right-left' => 'left',
        'top-bottom' => 'bottom',
        default => 'top',
    };
@endphp

<x-translation-workbench::ui.tw-graph-protocol.segments.path
    :segment="$endSegment"
    :dev="$dev"
/>

<x-translation-workbench::ui.tw-graph-protocol.primitives.dev-node-counter
    :id="data_get($endSegment, 'id', 'segment.end') . '.cap.end'"
    :dev="$devMode"
    :anchor-x="data_get($endSegment, 'anchorEnd.x', '0rem')"
    :anchor-y="data_get($endSegment, 'anchorEnd.y', '0rem')"
    :offset-x="data_get($endSegment, 'devCounterEndOffset.x', data_get($capCounterOffset, 'x', '0rem'))"
    :offset-y="data_get($endSegment, 'devCounterEndOffset.y', data_get($capCounterOffset, 'y', '0rem'))"
    :counter="data_get($endSegment, 'devCounterEnd', 'E')"
    :color="data_get($endSegment, 'devCounterColor', data_get($endSegment, 'color', 'green'))"
/>

@if (filled(data_get($endSegment, 'endLabel.text')))
    <x-translation-workbench::ui.tw-graph-protocol.primitives.text
        :id="data_get($endSegment, 'id', 'segment.end') . '.end-label'"
        :text="data_get($endSegment, 'endLabel.text')"
        :anchor-x="data_get($endSegment, 'anchorEnd.x', '0rem')"
        :anchor-y="data_get($endSegment, 'anchorEnd.y', '0rem')"
        :side="data_get($endSegment, 'endLabel.side', $endLabelSide)"
        :offset="data_get($endSegment, 'endLabel.offset', '0.75rem')"
        :badge="data_get($endSegment, 'endLabel.badge', true)"
        :badge-color="data_get($endSegment, 'endLabel.badgeColor', data_get($endSegment, 'color', 'green'))"
    />
@endif
