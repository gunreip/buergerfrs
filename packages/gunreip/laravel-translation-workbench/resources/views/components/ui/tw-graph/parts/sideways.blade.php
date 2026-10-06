@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/parts/sideways.blade.php --}}
{{--
    Part: sideways

    Usage:
    <x-translation-workbench::ui.tw-graph.parts.sideways
        id="resume.left.1.sideways"
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '4rem']"
        arc-radius="2.75rem"
        bridge-length="12rem"
        extension="3rem"
        :node-label-left="['text' => 'Label', 'width' => 'halfLong']"
    />

    side selects the destination: left = negative x, right = positive x, center = unchanged x.
    center uses one stem of 2 * arcRadius; bridgeLength is inactive, extension remains supported.
    center requires matching entry/exit directions and no bridgeLabel.

    extensionLength: one extra stem after the arc; 0rem disables it.
    nodeEnd/nodeEndDot: arc-exit marker. extensionEnd{nodeEnd,nodeEndDot}: extension-exit marker.
    extension is the existing two-stem decoration and cannot be combined with extensionLength.

    Part role:
    A manual authoring part that routes sideways through arc -> bridge -> arc.
    The first arc and bridge keep their anchors technical only; the final arc
    owns the visible end anchor and optional left/right labels.
    An optional bridgeLabel replaces the plain bridge with segments.label-bridge;
    its shared LabelBridge geometry determines the following arc and end anchor.
--}}

@aware([
    'graphId' => null,
    'color' => null,

    'lineLength' => null,
    'arcRadius' => null,
    'bridgeLength' => null,
    'connectorLength' => null,
    'connectorGap' => null,
])

@php
    $inheritedColor = $color ?? null;


@endphp

@props([
    'id' => null,
    'componentCounter' => 1,
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcRadius' => null,
    'bridgeLength' => null,
    'bridgeLabel' => null,
    'bridgeOutLength' => null,
    'bridgeInJoinLength' => null,
    'devCounterJoin' => 'J',
    'lineJumps' => [],
    'extension' => null,
    'extensionLength' => '0rem',
    'extensionEnd' => ['nodeEnd' => true, 'nodeEndDot' => true],
    'nodeEndDot' => null,
    'direction' => 'bottom-top',
    'exitDirection' => null,
    'color' => null,
    'nodeEnd' => true,
    'jointArrowEnd' => false,
    'nodeImage' => null,
    'nodeLabelLeft' => null,
    'nodeLabelRight' => null,
    'devCounterEnd' => 1,
    'devCounterColor' => null,
    'zIndex' => 20,
])

@php
    $twGraphRegionFrame = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::begin(
        '...::ui.tw-graph.parts.sideways',
        \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env),
    );
    try {
@endphp


@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $isCenter = $side === 'center';
    $travelsRight = $side === 'right';
    $isTopBottom = $direction === 'top-bottom';
    $resolvedExitDirection = $exitDirection ?? $direction;
    if (! in_array($resolvedExitDirection, ['bottom-top', 'top-bottom'], true)) {
        throw new \InvalidArgumentException('parts.sideways exitDirection must be bottom-top or top-bottom.');
    }
    $exitTopBottom = $resolvedExitDirection === 'top-bottom';
    $id = filled($id)
        ? (string) $id
        : 'part.' . $side . '.' . $resolvedComponentCounter . '.sideways';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $lineLength ?? null,
        'line_length',
        '4rem',
    );
    $resolvedArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $arcRadius,
        'arc_radius',
        '2.75rem',
    );
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $bridgeLength,
        $bridgeLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength),
    );
    $resolvedExtension = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extension, null, '0rem');
    $hasExtension = filled($extension) && ! in_array($resolvedExtension, ['0', '0rem'], true);

    $hasStraightExtension = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($extensionLength) > 0;
    if ($hasExtension && $hasStraightExtension) {
        throw new \InvalidArgumentException('parts.sideways: use extension or extensionLength, not both.');
    }

    $anchorStart = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $anchorStart = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $add = fn(string $value, string $delta): string => $delta === '0rem'
        ? $value
        : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn(string $value): string => 'calc(' . $value . ' * -1)';
    $arcDelta = $travelsRight ? $resolvedArcRadius : $neg($resolvedArcRadius);
    $bridgeDelta = $travelsRight ? $resolvedBridgeLength : $neg($resolvedBridgeLength);
    $verticalDelta = $isTopBottom ? $neg($resolvedArcRadius) : $resolvedArcRadius;
    $exitVerticalDelta = $exitTopBottom ? $neg($resolvedArcRadius) : $resolvedArcRadius;
    $extensionDelta = $exitTopBottom ? $neg($resolvedExtension) : $resolvedExtension;
    $arcInStartAnchor = $travelsRight ? 'w' : 'e';
    $arcInEndAnchor = $isTopBottom ? 's' : 'n';
    $bridgeDirection = $travelsRight ? 'left-right' : 'right-left';
    $arcOutStartAnchor = $exitTopBottom ? 'n' : 's';
    $arcOutEndAnchor = $travelsRight ? 'e' : 'w';
    $arcInName = $travelsRight
        ? ($isTopBottom ? 'west-south' : 'west-north')
        : ($isTopBottom ? 'east-south' : 'east-north');
    $arcOutName = $travelsRight
        ? ($exitTopBottom ? 'north-east' : 'south-east')
        : ($exitTopBottom ? 'north-west' : 'south-west');
    $arcInEnd = [
        'x' => $add($anchorStart['x'], $arcDelta),
        'y' => $add($anchorStart['y'], $verticalDelta),
    ];
    $bridgeLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($bridgeLabel, 'center', $resolvedColor);
    if ($isCenter && ($resolvedExitDirection !== $direction || $bridgeLabel !== null)) {
        throw new \InvalidArgumentException('parts.sideways side=center requires matching entry/exit directions and no bridgeLabel; there is no horizontal bridge.');
    }
    if (! $isCenter && $bridgeLabel === null) {
        $__env->getConsumableComponentData('twGraphCalculatedLengths')?->recordProp(
            $id . '.bridge1', 'parts.sideways', $id, 'bridge-length', $bridgeLength !== null,
        );
    }

    $labelBridgeGeometry = null;
    if ($bridgeLabel !== null) {
        $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
        $labelBridgeGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
            $arcInEnd,
            $bridgeDirection,
            \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($bridgeLabel),
            $resolvedBridgeLength,
            $bridgeOutLength,
        );
    }
    $bridgeEnd = $labelBridgeGeometry['anchorEnd'] ?? [
        'x' => $add($arcInEnd['x'], $bridgeDelta),
        'y' => $arcInEnd['y'],
    ];
    $arcOutEnd = [
        'x' => $add($bridgeEnd['x'], $arcDelta),
        'y' => $add($bridgeEnd['y'], $exitVerticalDelta),
    ];
    $centerLength = 'calc(' . $resolvedArcRadius . ' + ' . $resolvedArcRadius . ')';
    if ($isCenter) {
        $arcOutEnd = [
            'x' => $anchorStart['x'],
            'y' => $add($anchorStart['y'], $isTopBottom ? $neg($centerLength) : $centerLength),
        ];
        $__env->getConsumableComponentData('twGraphCalculatedLengths')?->record(
            $id . '.stem', 'parts.sideways', $id, 'length',
            ['arcRadius' => $resolvedArcRadius],
            'The straight connection spans the combined height of both arcs.',
        );
    }
    $labelAnchor = $arcOutEnd;
    $continuationEnd = $arcOutEnd;

    if ($hasExtension) {
        $labelAnchor = [
            'x' => $arcOutEnd['x'],
            'y' => $add($arcOutEnd['y'], $extensionDelta),
        ];
        $continuationEnd = [
            'x' => $labelAnchor['x'],
            'y' => $add($labelAnchor['y'], $extensionDelta),
        ];
    }

    if ($hasStraightExtension) {
        $labelAnchor = [
            'x' => $arcOutEnd['x'],
            'y' => $add($arcOutEnd['y'], $exitTopBottom ? $neg($extensionLength) : $extensionLength),
        ];
        $continuationEnd = $labelAnchor;
        $__env->getConsumableComponentData('twGraphCalculatedLengths')?->recordProp(
            $id . '.extension-stem', 'parts.sideways', $id, 'extension-length', true,
        );
    }

    $jointArrowDirection = $travelsRight ? 'right' : 'left';
    $normalizeLabel = fn (mixed $label): ?array => \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, null, $resolvedColor);
    $normalizeNodeLabel = function (mixed $label, string $side) use ($normalizeLabel): ?array {
        $normalized = $normalizeLabel($label);

        if ($normalized === null) {
            return null;
        }

        $width = data_get($normalized, 'width', data_get($normalized, 'boxWidth'));
        $normalized['side'] = $side;

        if ($width === 'long') {
            $normalized['long'] = true;
            $normalized['halfLong'] = false;
            $normalized['half'] = false;
        }

        if (in_array($width, ['halfLong', 'half-long', 'half_long'], true)) {
            $normalized['halfLong'] = true;
            $normalized['long'] = false;
            $normalized['half'] = false;
        }

        if (in_array($width, ['half', 'halfWidth', 'half-width', 'half_width'], true)) {
            $normalized['half'] = true;
            $normalized['halfLong'] = false;
            $normalized['long'] = false;
        }

        return $normalized;
    };
    $normalizeNodeImage = function (mixed $image): ?array {
        if ($image === false || blank($image)) {
            return null;
        }

        if (is_array($image)) {
            return filled(data_get($image, 'source', data_get($image, 'src'))) ? $image : null;
        }

        return ['source' => (string) $image];
    };
    $nodeLabelRight = $normalizeNodeLabel($nodeLabelRight, 'right');
    $nodeLabelLeft = $normalizeNodeLabel($nodeLabelLeft, 'left');
    $nodeImage = $normalizeNodeImage($nodeImage);

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', [
        'x' => $labelAnchor['x'],
        'y' => $labelAnchor['y'],
        'source' => $id,
        'sourceType' => 'parts.sideways',
        'sourceAnchor' => 'anchorNode-end',
        'direction' => $resolvedExitDirection,
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);

    $segments = [
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc1-' . $arcInName,
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'arcRadius' => $resolvedArcRadius,
                'anchorStart' => $anchorStart,
                'anchorEnd' => $arcInEnd,
                'nodeStart' => false,
                'nodeEnd' => false,
                'devCounterEnd' => false,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.bridge1',
                'lineJumps' => $lineJumps,
                'direction' => $bridgeDirection,
                'length' => $resolvedBridgeLength,
                'anchorStart' => $arcInEnd,
                'anchorEnd' => $bridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => false,
                'devCounterStart' => false,
                'devCounterEnd' => false,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc2-' . $arcOutName,
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'arcRadius' => $resolvedArcRadius,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
                'nodeStart' => false,
                'devCounterStart' => false,
                'nodeEnd' => $hasExtension ? true : $nodeEnd,
                'nodeEndSize' => null,
                'nodeEndDot' => ! $hasExtension && $jointArrowEnd && ! $nodeLabelRight && ! $nodeLabelLeft ? false : null,
                'jointArrowEnd' => ! $hasExtension && $jointArrowEnd,
                'jointArrowEndDirection' => $exitTopBottom ? 'bottom' : 'top',
                'devCounterEnd' => $hasExtension ? false : $devCounterEnd,
                'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                    $devCounterColor,
                    $resolvedColor,
                    'zinc',
                ),
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ],
    ];

    if ($hasExtension) {
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.extension-stem1',
                'direction' => $resolvedExitDirection,
                'length' => $resolvedExtension,
                'anchorStart' => $arcOutEnd,
                'anchorEnd' => $labelAnchor,
                'nodeStart' => false,
                'nodeEnd' => $nodeEnd,
                'nodeEndDot' => $jointArrowEnd && ! $nodeLabelRight && ! $nodeLabelLeft ? false : $nodeEnd,
                'jointArrowEnd' => $jointArrowEnd,
                'devCounterStart' => false,
                'devCounterEnd' => $devCounterEnd,
                'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                    $devCounterColor,
                    $resolvedColor,
                    'zinc',
                ),
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ];
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.extension-stem2',
                'direction' => $resolvedExitDirection,
                'length' => $resolvedExtension,
                'anchorStart' => $labelAnchor,
                'anchorEnd' => $continuationEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
    'jointArrowEnd' => false,
                'nodeEndSize' => null,
                'devCounterStart' => false,
                'devCounterEnd' => false,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,

            ],
        ];
    }

    // New single-stem extension: the arc exit and extension exit own separate markers.
    if ($hasStraightExtension || $nodeEndDot !== null) {
        $arcMarker = \Gunreip\TranslationWorkbench\Support\TwGraph\NodeMarker::resolve(
            (bool) $nodeEnd, $nodeEndDot ?? ! $jointArrowEnd,
            $hasStraightExtension || $hasExtension ? [] : [$nodeLabelRight, $nodeLabelLeft],
        );
        $segments[2]['segment']['nodeEnd'] = $arcMarker['visible'];
        $segments[2]['segment']['nodeEndDot'] = $arcMarker['dot'];
        $segments[2]['segment']['jointArrowEnd'] = $arcMarker['arrow'];
        $segments[2]['segment']['devCounterEnd'] = $devCounterEnd;
    }
    if ($hasStraightExtension) {
        $extensionMarker = \Gunreip\TranslationWorkbench\Support\TwGraph\NodeMarker::resolve(
            (bool) data_get($extensionEnd, 'nodeEnd', true),
            (bool) data_get($extensionEnd, 'nodeEndDot', true),
            [$nodeLabelRight, $nodeLabelLeft],
        );
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.extension-stem',
                'direction' => $resolvedExitDirection,
                'length' => $extensionLength,
                'anchorStart' => $arcOutEnd,
                'anchorEnd' => $labelAnchor,
                'nodeStart' => false,
                'nodeEnd' => $extensionMarker['visible'],
                'nodeEndDot' => $extensionMarker['dot'],
                'jointArrowEnd' => $extensionMarker['arrow'],
                'devCounterStart' => false,
                'devCounterEnd' => $devCounterEnd,
                'devCounterColor' => $devCounterColor ?: $resolvedColor,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
            ],
        ];
    }
    if ($nodeImage !== null) {
        if ($hasExtension || $hasStraightExtension) {
            $segments[3]['segment']['nodeEndDot'] = false;
        } else {
            $segments[2]['segment']['nodeEndDot'] = false;
        }
    }
    if ($isCenter) {
        $centerSegment = array_replace($segments[2]['segment'], [
            'id' => $id . '.stem',
            'direction' => $direction,
            'length' => $centerLength,
            'anchorStart' => $anchorStart,
            'anchorEnd' => $arcOutEnd,
        ]);
        $centerSegment['nodeEndDot'] = $centerSegment['nodeEndDot'] ?? (bool) $centerSegment['nodeEnd'];
        unset($centerSegment['startAnchor'], $centerSegment['endAnchor'], $centerSegment['arcRadius']);
        $segments = [
            ['component' => 'path', 'segment' => $centerSegment],
            ...array_slice($segments, 3),
        ];
    }
@endphp

@foreach ($segments as $segment)
    @if ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc
            :segment="$segment['segment']['id'] === $id . '.bridge1' ? array_replace($segment['segment'], ['joinLength' => $bridgeInJoinLength, 'devCounterJoin' => $devCounterJoin]) : $segment['segment']"
        />
    @elseif ($segment['segment']['id'] === $id . '.bridge1' && $bridgeLabel !== null)
        <x-translation-workbench::ui.tw-graph.segments.label-bridge
            :id="$id . '.bridge1'"
            :label="$bridgeLabel"
            :bridge-in-join-length="$bridgeInJoinLength" :dev-counter-join="$devCounterJoin"
            :line-jumps="data_get($bridgeLabel, 'lineJumps', $lineJumps)"
            :anchor-start="$arcInEnd"
            :direction="$bridgeDirection"
            :bridge-length="$resolvedBridgeLength"
            :geometry="$labelBridgeGeometry"
            :color="$resolvedColor"
            :z-index="$zIndex"
            :dev-counter-end="false"
        />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path
            :segment="$segment['segment']['id'] === $id . '.bridge1' ? array_replace($segment['segment'], ['joinLength' => $bridgeInJoinLength, 'devCounterJoin' => $devCounterJoin]) : $segment['segment']"
        />
    @endif
@endforeach

@if (! $isCenter)
<x-translation-workbench::ui.tw-graph.primitives.joint-arrow
    :id="$id . '.arc-in.bridge.joint-arrow'"
    :direction="$jointArrowDirection"
    :anchor-x="$arcInEnd['x']"
    :anchor-y="$arcInEnd['y']"
    :color="$resolvedColor"
    :z-index="$zIndex + 1"
/>

@if ($bridgeLabel === null)
    <x-translation-workbench::ui.tw-graph.primitives.joint-arrow
        :id="$id . '.bridge.arc-out.joint-arrow'"
        :direction="$jointArrowDirection"
        :anchor-x="$bridgeEnd['x']"
        :anchor-y="$bridgeEnd['y']"
        :color="$resolvedColor"
        :z-index="$zIndex + 1"
    />

@endif

@endif

@if (($hasStraightExtension || $nodeEnd) && $nodeLabelRight)
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.anchorNode-end.label-1'"
        :label="$nodeLabelRight"
        side="right"
        :anchor-x="$labelAnchor['x']"
        :anchor-y="$labelAnchor['y']"
        :color="$resolvedColor"
    />
@endif

@if (($hasStraightExtension || $nodeEnd) && $nodeLabelLeft)
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.anchorNode-end.label-2'"
        :label="$nodeLabelLeft"
        side="left"
        :anchor-x="$labelAnchor['x']"
        :anchor-y="$labelAnchor['y']"
        :color="$resolvedColor"
    />
@endif

@if (($hasStraightExtension || $nodeEnd) && $nodeImage !== null)
    <x-translation-workbench::ui.tw-graph.primitives.node-image
        :id="$id . '.anchorNode-end.image'"
        :source="data_get($nodeImage, 'source', data_get($nodeImage, 'src'))"
        :size="data_get($nodeImage, 'size', \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('node_image_size', '3rem'))"
        :anchor-x="$labelAnchor['x']"
        :anchor-y="$labelAnchor['y']"
        :alt="data_get($nodeImage, 'alt', '')"
        :color="data_get($nodeImage, 'color', $resolvedColor)"
        :z-index="data_get($nodeImage, 'zIndex')"
    />
@endif

@php
    } finally {
        $twGraphRegionDefinition = \Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion::finish(
            $twGraphRegionFrame, $id ?? null, $resolvedColor ?? $color ?? 'sky',
        );
    }
@endphp
@if ($twGraphRegionDefinition !== null)
    <script type="application/json" data-tw-graph-component-region>{!! json_encode($twGraphRegionDefinition, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
