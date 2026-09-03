{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/start.blade.php --}}
{{--
    Segment: start

    Usage:
    <x-translation-workbench::ui.tw-graph.segments.start :segment="$segment" />

    Segment role:
    Start is a path segment with start-specific defaults. It keeps the same
    primitives.line ownership model as segments.path: the line primitive owns
    line/nodeStart/nodeEnd, and optional attachments are handled by
    segments.path.

    Defaults:
    gradient=true
    nodeStart=false
    nodeEnd=true

    Optional fields:
    startLabel{text, side, offset, badgeColor}
--}}

@props([
    'segment' => [],
    'dev' => null,
])

@php
    $startSegment = array_replace([
        'id' => 'segment.start',
        'direction' => 'bottom-top',
        'length' => '4rem',
        'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
        'nodeStart' => false,
        'nodeEnd' => true,
        'gradient' => true,
        'cap' => false,
        'color' => 'green',
    ], $segment);
@endphp

<x-translation-workbench::ui.tw-graph.segments.path
    :segment="$startSegment"
    :dev="$dev"
/>

@if (filled(data_get($startSegment, 'startLabel.text')))
    <x-translation-workbench::ui.tw-graph.primitives.text
        :id="data_get($startSegment, 'id', 'segment.start') . '.start-label'"
        :text="data_get($startSegment, 'startLabel.text')"
        :anchor-x="data_get($startSegment, 'anchorStart.x', '0rem')"
        :anchor-y="data_get($startSegment, 'anchorStart.y', '0rem')"
        :side="data_get($startSegment, 'startLabel.side', 'bottom')"
        :offset="data_get($startSegment, 'startLabel.offset', '0.75rem')"
        :badge="data_get($startSegment, 'startLabel.badge', true)"
        :badge-color="data_get($startSegment, 'startLabel.badgeColor', data_get($startSegment, 'color', 'green'))"
        :long="data_get($startSegment, 'startLabel.long', false) || data_get($startSegment, 'startLabel.width') === 'long'"
        :half-long="data_get($startSegment, 'startLabel.halfLong', false) || in_array(data_get($startSegment, 'startLabel.width'), ['halfLong', 'half-long', 'half_long'], true)"
        :half="data_get($startSegment, 'startLabel.half', false) || in_array(data_get($startSegment, 'startLabel.width'), ['half', 'halfWidth', 'half-width', 'half_width'], true)"
        :align="data_get($startSegment, 'startLabel.align', 'center')"
        :justify="data_get($startSegment, 'startLabel.justify', false)"
    />
@endif
