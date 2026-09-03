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

    Part role:
    A manual authoring part that routes sideways through arc -> bridge -> arc.
    The first arc and bridge keep their anchors technical only; the final arc
    owns the visible end anchor and optional left/right labels.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'connectorLength' => null,
    'connectorGap' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcRadius' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'extension' => null,
    'color' => null,
    'nodeEnd' => true,
    'nodeImage' => null,
    'nodeLabelLeft' => null,
    'nodeLabelRight' => null,
    'devCounterEnd' => 1,
    'devCounterColor' => null,
    'zIndex' => 20,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $isLeft = $side !== 'right';
    $id = filled($id)
        ? (string) $id
        : 'part.' . ($isLeft ? 'left' : 'right') . '.' . $resolvedComponentCounter . '.sideways';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $defaultColor ?? null,
        'zinc',
    );
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $lineLength ?? null,
        'line_length',
        '4rem',
    );
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $arcRadius ?? ($arcSize ?? null),
        'arc_size',
        '2.75rem',
    );
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $bridgeLength,
        $bridgeLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength),
    );
    $resolvedExtension = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($extension, null, '0rem');
    $hasExtension = filled($extension) && ! in_array($resolvedExtension, ['0', '0rem'], true);
    $resolvedDev = $devMode ?? $dev;
    $anchorStart = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $anchorStart = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $add = fn(string $value, string $delta): string => $delta === '0rem'
        ? $value
        : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn(string $value): string => 'calc(' . $value . ' * -1)';
    $arcDelta = $isLeft ? $resolvedArcSize : $neg($resolvedArcSize);
    $bridgeDelta = $isLeft ? $resolvedBridgeLength : $neg($resolvedBridgeLength);
    $arcInStartAnchor = $isLeft ? 'w' : 'e';
    $arcInEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'left-right' : 'right-left';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'e' : 'w';
    $arcInEnd = [
        'x' => $add($anchorStart['x'], $arcDelta),
        'y' => $add($anchorStart['y'], $resolvedArcSize),
    ];
    $bridgeEnd = [
        'x' => $add($arcInEnd['x'], $bridgeDelta),
        'y' => $arcInEnd['y'],
    ];
    $arcOutEnd = [
        'x' => $add($bridgeEnd['x'], $arcDelta),
        'y' => $add($bridgeEnd['y'], $resolvedArcSize),
    ];
    $labelAnchor = $arcOutEnd;
    $continuationEnd = $arcOutEnd;

    if ($hasExtension) {
        $labelAnchor = [
            'x' => $arcOutEnd['x'],
            'y' => $add($arcOutEnd['y'], $resolvedExtension),
        ];
        $continuationEnd = [
            'x' => $labelAnchor['x'],
            'y' => $add($labelAnchor['y'], $resolvedExtension),
        ];
    }

    $jointArrowDirection = $isLeft ? 'right' : 'left';
    $normalizeLabel = function (mixed $label): ?array {
        if ($label === false || blank($label)) {
            return null;
        }

        if (is_array($label)) {
            return filled(data_get($label, 'text')) ? $label : null;
        }

        return ['text' => (string) $label];
    };
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
    $labelWidth = function (?array $label): string {
        if ($label === null) {
            return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_width.default', '12rem');
        }

        if ((bool) data_get($label, 'long', false) || data_get($label, 'width') === 'long') {
            return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_width.long', '20rem');
        }

        if ((bool) data_get($label, 'halfLong', false) || in_array(data_get($label, 'width'), ['halfLong', 'half-long', 'half_long'], true)) {
            return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_width.half_long', '16rem');
        }

        if ((bool) data_get($label, 'half', false) || in_array(data_get($label, 'width'), ['half', 'halfWidth', 'half-width', 'half_width'], true)) {
            return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_width.half', '6rem');
        }

        return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_width.default', '12rem');
    };
    $labelHeight = fn (?array $label): string => ((1.75 + (max(1, (int) data_get($label, 'maxLines', 3)) * 1.25)) . 'rem');
    $resolvedConnectorLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor(
        $connectorLength ?? null,
        null,
        'connector_length',
        '2rem',
    );
    $resolvedConnectorGap = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor(
        $connectorGap ?? null,
        null,
        'connector_gap',
        '0.25rem',
    );
    $nodeHalf = 'calc(' . \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('node_size', '0.95rem') . ' / 2)';
    $putSideLabelBounds = function (string $boundsId, ?array $label, string $side, array $anchor) use ($resolvedGraphId, $labelWidth, $labelHeight, $resolvedConnectorLength, $resolvedConnectorGap, $nodeHalf): void {
        if ($label === null) {
            return;
        }

        $reach = 'calc(' . $nodeHalf . ' + ' . $resolvedConnectorLength . ' + ' . $resolvedConnectorGap . ' + ' . $labelWidth($label) . ')';
        $height = $labelHeight($label);

        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
            $resolvedGraphId,
            $boundsId,
            $side === 'left' ? 'calc(' . $anchor['x'] . ' - ' . $reach . ')' : $anchor['x'],
            'calc(' . $anchor['y'] . ' - (' . $height . ' / 2))',
            $reach,
            $height,
            $side,
        );
    };
    $geometryBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
        [$anchorStart, $arcInEnd, $bridgeEnd, $arcOutEnd, $labelAnchor, $continuationEnd],
        '1rem',
    );

    // Slot-mode canvas metrics depend on explicit bounds registration for manual parts.
    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
        $resolvedGraphId,
        $id . '.bounds',
        $geometryBounds['left'],
        $geometryBounds['bottom'],
        $geometryBounds['width'],
        $geometryBounds['height'],
        $isLeft ? 'left' : 'right',
    );
    $putSideLabelBounds($id . '.anchorNode-end.label-1.bounds', $nodeLabelRight, 'right', $labelAnchor);
    $putSideLabelBounds($id . '.anchorNode-end.label-2.bounds', $nodeLabelLeft, 'left', $labelAnchor);

    if ($nodeImage !== null) {
        $nodeImageSize = data_get($nodeImage, 'size', \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('node_image_size', '3rem'));
        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
            $resolvedGraphId,
            $id . '.anchorNode-end.image.bounds',
            'calc(' . $labelAnchor['x'] . ' - (' . $nodeImageSize . ' / 2))',
            'calc(' . $labelAnchor['y'] . ' - (' . $nodeImageSize . ' / 2))',
            $nodeImageSize,
            $nodeImageSize,
            'center',
        );
    }
    $segments = [
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc1-' . ($isLeft ? 'west-north' : 'east-north'),
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'arcSize' => $resolvedArcSize,
                'anchorStart' => $anchorStart,
                'anchorEnd' => $arcInEnd,
                'nodeStart' => false,
                'nodeEnd' => false,
                'devCounterEnd' => false,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ],
        [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.bridge1',
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
                'dev' => $resolvedDev,
            ],
        ],
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc2-' . ($isLeft ? 'south-east' : 'south-west'),
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'arcSize' => $resolvedArcSize,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
                'nodeStart' => false,
                'devCounterStart' => false,
                'nodeEnd' => $hasExtension ? true : $nodeEnd,
                'nodeEndSize' => null,
                'devCounterEnd' => $hasExtension ? false : $devCounterEnd,
                'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                    $devCounterColor,
                    $resolvedColor,
                    'zinc',
                ),
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ],
    ];

    if ($hasExtension) {
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.extension-stem1',
                'direction' => 'bottom-top',
                'length' => $resolvedExtension,
                'anchorStart' => $arcOutEnd,
                'anchorEnd' => $labelAnchor,
                'nodeStart' => false,
                'nodeEnd' => $nodeEnd,
                'devCounterStart' => false,
                'devCounterEnd' => $devCounterEnd,
                'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                    $devCounterColor,
                    $resolvedColor,
                    'zinc',
                ),
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ];
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.extension-stem2',
                'direction' => 'bottom-top',
                'length' => $resolvedExtension,
                'anchorStart' => $labelAnchor,
                'anchorEnd' => $continuationEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'nodeEndSize' => null,
                'devCounterStart' => false,
                'devCounterEnd' => false,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ];
    }

    if ($nodeImage !== null) {
        if ($hasExtension) {
            $segments[3]['segment']['nodeEndDot'] = false;
        } else {
            $segments[2]['segment']['nodeEndDot'] = false;
        }
    }
@endphp

@foreach ($segments as $segment)
    @if ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc
            :segment="$segment['segment']"
            :dev="$resolvedDev"
        />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path
            :segment="$segment['segment']"
            :dev="$resolvedDev"
        />
    @endif
@endforeach

<x-translation-workbench::ui.tw-graph.primitives.joint-arrow
    :id="$id . '.arc-in.bridge.joint-arrow'"
    :direction="$jointArrowDirection"
    :anchor-x="$arcInEnd['x']"
    :anchor-y="$arcInEnd['y']"
    :color="$resolvedColor"
    :z-index="$zIndex + 1"
/>

<x-translation-workbench::ui.tw-graph.primitives.joint-arrow
    :id="$id . '.bridge.arc-out.joint-arrow'"
    :direction="$jointArrowDirection"
    :anchor-x="$bridgeEnd['x']"
    :anchor-y="$bridgeEnd['y']"
    :color="$resolvedColor"
    :z-index="$zIndex + 1"
/>

@if ($nodeEnd && $nodeLabelRight)
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.anchorNode-end.label-1'"
        :label="$nodeLabelRight"
        side="right"
        :anchor-x="$labelAnchor['x']"
        :anchor-y="$labelAnchor['y']"
        :color="$resolvedColor"
    />
@endif

@if ($nodeEnd && $nodeLabelLeft)
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.anchorNode-end.label-2'"
        :label="$nodeLabelLeft"
        side="left"
        :anchor-x="$labelAnchor['x']"
        :anchor-y="$labelAnchor['y']"
        :color="$resolvedColor"
    />
@endif

@if ($nodeEnd && $nodeImage !== null)
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
