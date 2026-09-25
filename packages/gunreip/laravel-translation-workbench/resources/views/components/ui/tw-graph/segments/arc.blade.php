@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/arc.blade.php --}}
{{--
    Segment: arc

    Usage:
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment" />

    Segment role:
    Arc maps semantic start/end anchors to primitives.arc and optionally places
    labels at anchorStart/anchorEnd. Labels are composed by segments.label from
    primitives.connector and primitives.text.

    Required segment fields:
    id, startAnchor, endAnchor, anchorStart{x,y}, anchorEnd{x,y}

    Optional fields:
    nodeStart=true|false
    nodeEnd=true|false
    nodeStartDot=true|false
    nodeEndDot=true|false
    nodeStartZIndex/nodeEndZIndex: optional dot layer; defaults to path zIndex + 1
    jointArrowStart=true|false
    jointArrowEnd=true|false
    DEV diagnostics are controlled by the enclosing tw-graph canvas.
    devCounterColor
    startLabel{text, side, connectorLength, connectorGap, color, badgeColor}
    endLabel{text, side, connectorLength, connectorGap, color, badgeColor}
--}}

@props([
    'segment' => [],
])

@php
    $id = data_get($segment, 'id', 'segment.arc');
    $color = data_get($segment, 'color', 'zinc');
    $toColor = data_get($segment, 'toColor', $color);
    $tone = data_get($segment, 'tone', 'line');
    $zIndex = data_get($segment, 'zIndex');
    $nodeStart = (bool) data_get($segment, 'nodeStart', false);
    $nodeEnd = (bool) data_get($segment, 'nodeEnd', false);
    $nodeStartDotValue = data_get($segment, 'nodeStartDot');
    $nodeEndDotValue = data_get($segment, 'nodeEndDot');
    $nodeStartDot = $nodeStartDotValue === null ? $nodeStart : (bool) $nodeStartDotValue;
    $nodeEndDot = $nodeEndDotValue === null ? $nodeEnd : (bool) $nodeEndDotValue;
    $jointArrowStart = $nodeStart && ! $nodeStartDot && (bool) data_get($segment, 'jointArrowStart', false);
    $jointArrowEnd = $nodeEnd && ! $nodeEndDot && (bool) data_get($segment, 'jointArrowEnd', false);
    $jointArrowStartDirection = data_get($segment, 'jointArrowStartDirection', 'right');
    $jointArrowEndDirection = data_get($segment, 'jointArrowEndDirection', 'right');

    $counterSize = 'var(--tw-graph-protocol-dev-node-counter-width)';
    $counterDistance = 'calc(var(--tw-graph-protocol-node-half) + var(--tw-graph-protocol-dev-node-counter-half))';
    $negativeCounterDistance = 'calc((var(--tw-graph-protocol-node-half) + var(--tw-graph-protocol-dev-node-counter-half)) * -1)';
    $outsideOffset = fn (string $anchor): array => match ($anchor) {
        'n' => ['x' => $negativeCounterDistance, 'y' => $counterSize],
        's' => ['x' => '0rem', 'y' => $negativeCounterDistance],
        'e' => ['x' => $negativeCounterDistance, 'y' => $counterSize],
        'w' => ['x' => $negativeCounterDistance, 'y' => $counterSize],
        default => ['x' => $counterDistance, 'y' => $counterDistance],
    };
    $insideOffset = fn (string $anchor): array => match ($anchor) {
        'n' => ['x' => $negativeCounterDistance, 'y' => $counterSize],
        's' => ['x' => $counterDistance, 'y' => $counterDistance],
        'e' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
        'w' => ['x' => $negativeCounterDistance, 'y' => $counterSize],
        default => ['x' => $negativeCounterDistance, 'y' => $negativeCounterDistance],
    };
    $startAnchor = data_get($segment, 'startAnchor', 'n');
    $endAnchor = data_get($segment, 'endAnchor', 'w');
    $arcPair = $startAnchor . '-' . $endAnchor;
    $startX = data_get($segment, 'anchorStart.x', '0rem');
    $startY = data_get($segment, 'anchorStart.y', '0rem');
    $endX = data_get($segment, 'anchorEnd.x', '0rem');
    $endY = data_get($segment, 'anchorEnd.y', '0rem');
    $arcRadius = data_get($segment, 'arcRadius', 'var(--tw-graph-protocol-arc-radius)');
    $anchors = collect([
        $startAnchor => ['x' => $startX, 'y' => $startY],
        $endAnchor => ['x' => $endX, 'y' => $endY],
    ]);
    $corner = match (collect([$startAnchor, $endAnchor])->sort()->implode('-')) {
        'n-w' => 'nw',
        'e-n' => 'ne',
        's-w' => 'sw',
        default => 'se',
    };
    $rightTopCounterOffset = ['x' => $counterDistance, 'y' => $counterSize];
    $leftBottomCounterOffset = ['x' => $negativeCounterDistance, 'y' => $negativeCounterDistance];
    $rightBottomCounterOffset = ['x' => $counterDistance, 'y' => $negativeCounterDistance];
    $defaultStartCounterOffset = match ($arcPair) {
        'n-e', 'e-n' => $rightTopCounterOffset,
        'w-s', 's-w' => $leftBottomCounterOffset,
        'e-s', 's-e' => $rightBottomCounterOffset,
        default => $outsideOffset($startAnchor),
    };
    $defaultEndCounterOffset = match ($arcPair) {
        'n-e', 'e-n' => $rightTopCounterOffset,
        'w-s', 's-w' => $leftBottomCounterOffset,
        'e-s', 's-e' => $rightBottomCounterOffset,
        default => $insideOffset($endAnchor),
    };
    $startCounterOffset = data_get($segment, 'devCounterStartOffset', $defaultStartCounterOffset);
    $endCounterOffset = data_get($segment, 'devCounterEndOffset', $defaultEndCounterOffset);
    $boxPadding = '0.35rem';
    $boxX = match ($corner) {
        'se' => 'calc(' . data_get($anchors, 'e.x', $startX) . ' - ' . $arcRadius . ' + var(--tw-graph-protocol-path-half))',
        'sw' => 'calc(' . data_get($anchors, 'w.x', $startX) . ' - var(--tw-graph-protocol-path-half))',
        'nw' => 'calc(' . data_get($anchors, 'w.x', $endX) . ' - var(--tw-graph-protocol-path-half))',
        default => 'calc(' . data_get($anchors, 'e.x', $endX) . ' - ' . $arcRadius . ' + var(--tw-graph-protocol-path-half))',
    };
    $boxY = match ($corner) {
        'se', 'sw' => 'calc(' . data_get($anchors, 's.y', $endY) . ' - var(--tw-graph-protocol-path-half))',
        default => 'calc(' . data_get($anchors, 'n.y', $startY) . ' - ' . $arcRadius . ' + var(--tw-graph-protocol-path-half))',
    };
@endphp

<x-translation-workbench::ui.tw-graph.dev-box
    :id="$id . '.dev-box'"
    :x="'calc(' . $boxX . ' - ' . $boxPadding . ')'"
    :y="'calc(' . $boxY . ' - ' . $boxPadding . ')'"
    :width="'calc(' . $arcRadius . ' + (' . $boxPadding . ' * 2))'"
    :height="'calc(' . $arcRadius . ' + (' . $boxPadding . ' * 2))'"
    color="sky"
    :label="$id"
/>

    <x-translation-workbench::ui.tw-graph.primitives.arc
        :id="$id"
        :start-anchor="$startAnchor"
        :end-anchor="$endAnchor"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
        :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
        :arc-radius="$arcRadius"
        :node-start="false"
        :node-end="false"
        :node-start-size="data_get($segment, 'nodeStartSize')"
        :node-end-size="data_get($segment, 'nodeEndSize')"
        :dashed="data_get($segment, 'dashed', false)"
        :color="$color"
        :to-color="$toColor"
        :tone="$tone"
        :z-index="$zIndex"
    />

    {{-- Independent nodes sit above adjacent paths, outside the line's stacking context. --}}
    @foreach (['start' => $nodeStartDot, 'end' => $nodeEndDot] as $nodePosition => $showDot)
        @if ($showDot)
            <x-translation-workbench::ui.tw-graph.primitives.node
                :id="$id . '.node.' . $nodePosition"
                :anchor-x="data_get($segment, $nodePosition === 'start' ? 'anchorStart.x' : 'anchorEnd.x', '0rem')"
                :anchor-y="data_get($segment, $nodePosition === 'start' ? 'anchorStart.y' : 'anchorEnd.y', '0rem')"
                :size="data_get($segment, $nodePosition === 'start' ? 'nodeStartSize' : 'nodeEndSize')"
                :color="$nodePosition === 'end' ? ($toColor ?: $color) : $color"
                :tone="$tone"
                :z-index="data_get($segment, $nodePosition === 'start' ? 'nodeStartZIndex' : 'nodeEndZIndex', $zIndex === null ? null : $zIndex + 1)"
            />
        @endif
    @endforeach

@if ($jointArrowStart)
    <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        :id="$id . '.start.joint-arrow'"
        :direction="$jointArrowStartDirection"
        :anchor-x="data_get($segment, 'anchorStart.x', '0rem')"
        :anchor-y="data_get($segment, 'anchorStart.y', '0rem')"
        :color="$color"
        :tone="$tone"
        :z-index="$zIndex === null ? null : $zIndex + 1"
    />
@endif

@if ($jointArrowEnd)
    <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        :id="$id . '.end.joint-arrow'"
        :direction="$jointArrowEndDirection"
        :anchor-x="data_get($segment, 'anchorEnd.x', '0rem')"
        :anchor-y="data_get($segment, 'anchorEnd.y', '0rem')"
        :color="$color"
        :tone="$tone"
        :z-index="$zIndex === null ? null : $zIndex + 1"
    />
@endif

@if ($nodeStart)
    <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
        :id="$id . '.node.start'"
        :anchor-x="data_get($segment, 'anchorStart.x', '0rem')"
        :anchor-y="data_get($segment, 'anchorStart.y', '0rem')"
        :offset-x="data_get($startCounterOffset, 'x', '0rem')"
        :offset-y="data_get($startCounterOffset, 'y', '0rem')"
        :counter="data_get($segment, 'devCounterStart', 'S')"
        :color="data_get($segment, 'devCounterColor', 'zinc')"
    />
@endif

@if ($nodeEnd)
    <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
        :id="$id . '.node.end'"
        :anchor-x="data_get($segment, 'anchorEnd.x', '0rem')"
        :anchor-y="data_get($segment, 'anchorEnd.y', '0rem')"
        :offset-x="data_get($endCounterOffset, 'x', '0rem')"
        :offset-y="data_get($endCounterOffset, 'y', '0rem')"
        :counter="data_get($segment, 'devCounterEnd', 'E')"
        :color="data_get($segment, 'devCounterColor', 'zinc')"
    />
@endif

@if ($nodeStart && filled(data_get($segment, 'startLabel.text')))
    @php($startLabelSide = data_get($segment, 'startLabel.side', 'right'))
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.label.' . $startLabelSide . '.1'"
        :label="data_get($segment, 'startLabel')"
        :side="$startLabelSide"
        :anchor-x="data_get($segment, 'anchorStart.x', '0rem')"
        :anchor-y="data_get($segment, 'anchorStart.y', '0rem')"
        :color="$color"
    />
@endif

@if ($nodeEnd && filled(data_get($segment, 'endLabel.text')))
    @php($endLabelSide = data_get($segment, 'endLabel.side', 'left'))
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.label.' . $endLabelSide . '.1'"
        :label="data_get($segment, 'endLabel')"
        :side="$endLabelSide"
        :anchor-x="data_get($segment, 'anchorEnd.x', '0rem')"
        :anchor-y="data_get($segment, 'anchorEnd.y', '0rem')"
        :color="$color"
    />
@endif
