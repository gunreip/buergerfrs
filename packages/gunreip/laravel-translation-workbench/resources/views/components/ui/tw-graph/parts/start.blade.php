{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/parts/start.blade.php --}}
{{--
    Part: start

    Usage:
    <x-translation-workbench::ui.tw-graph.parts.start
        id="resume.center.1.start"
        start-label="1879"
        node-label-left="Ulm"
        :node-label-right="['text' => 'Geburt', 'width' => 'halfLong']"
    />

    Part role:
    A manual authoring wrapper around segments.start. The part layer stays
    parallel to strang.* and exposes small, chainable graph pieces without
    rebuilding segment geometry in the sample view.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => null,
    'stemLength' => null,
    'connectorLength' => null,
    'connectorGap' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'length' => null,
    'color' => null,
    'nodeEnd' => true,
    'nodeEndDot' => null,
    'nodeImage' => null,
    'nodeLabelLeft' => null,
    'nodeLabelRight' => null,
    'devCounterEnd' => 1,
    'devCounterColor' => null,
    'startLabel' => null,
    'zIndex' => 20,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : 'part.center.' . $resolvedComponentCounter . '.start';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'zinc');
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $length,
        $stemLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength),
    );
    $resolvedDev = $devMode ?? $dev;
    $anchorStart = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $anchorStart = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $anchorEnd = match ($direction) {
        'top-bottom' => [
            'x' => $anchorStart['x'],
            'y' => 'calc(' . $anchorStart['y'] . ' - ' . $resolvedLength . ')',
        ],
        'left-right' => [
            'x' => 'calc(' . $anchorStart['x'] . ' + ' . $resolvedLength . ')',
            'y' => $anchorStart['y'],
        ],
        'right-left' => [
            'x' => 'calc(' . $anchorStart['x'] . ' - ' . $resolvedLength . ')',
            'y' => $anchorStart['y'],
        ],
        default => [
            'x' => $anchorStart['x'],
            'y' => 'calc(' . $anchorStart['y'] . ' + ' . $resolvedLength . ')',
        ],
    };
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
    $startLabel = $normalizeLabel($startLabel);
    $geometryBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([$anchorStart, $anchorEnd], '1rem');

    // Slot-mode canvas metrics depend on explicit bounds registration for manual parts.
    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
        $resolvedGraphId,
        $id . '.bounds',
        $geometryBounds['left'],
        $geometryBounds['bottom'],
        $geometryBounds['width'],
        $geometryBounds['height'],
        'center',
    );
    $putSideLabelBounds($id . '.anchorNode-end.label-1.bounds', $nodeLabelRight, 'right', $anchorEnd);
    $putSideLabelBounds($id . '.anchorNode-end.label-2.bounds', $nodeLabelLeft, 'left', $anchorEnd);

    if ($nodeImage !== null) {
        $nodeImageSize = data_get($nodeImage, 'size', \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('node_image_size', '3rem'));
        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
            $resolvedGraphId,
            $id . '.anchorNode-end.image.bounds',
            'calc(' . $anchorEnd['x'] . ' - (' . $nodeImageSize . ' / 2))',
            'calc(' . $anchorEnd['y'] . ' - (' . $nodeImageSize . ' / 2))',
            $nodeImageSize,
            $nodeImageSize,
            'center',
        );
    }

    if ($startLabel !== null) {
        $startLabelSide = data_get($startLabel, 'side', 'bottom');
        $startLabelWidth = $labelWidth($startLabel);
        $startLabelHeight = $labelHeight($startLabel);
        $startLabelX = match ($startLabelSide) {
            'left' => 'calc(' . $anchorStart['x'] . ' - ' . $startLabelWidth . ')',
            'right' => $anchorStart['x'],
            default => 'calc(' . $anchorStart['x'] . ' - (' . $startLabelWidth . ' / 2))',
        };
        $startLabelY = match ($startLabelSide) {
            'top' => $anchorStart['y'],
            'bottom' => 'calc(' . $anchorStart['y'] . ' - ' . $startLabelHeight . ')',
            default => 'calc(' . $anchorStart['y'] . ' - (' . $startLabelHeight . ' / 2))',
        };

        \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
            $resolvedGraphId,
            $id . '.startLabel.bounds',
            $startLabelX,
            $startLabelY,
            $startLabelWidth,
            $startLabelHeight,
            in_array($startLabelSide, ['left', 'right'], true) ? $startLabelSide : 'center',
        );
    }
    $nodeEnd = $nodeEnd === true && ($nodeLabelRight || $nodeLabelLeft)
        ? [$nodeLabelRight, $nodeLabelLeft]
        : $nodeEnd;
    $segment = [
        'id' => $id,
        'direction' => $direction,
        'length' => $resolvedLength,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $anchorEnd,
        'nodeStart' => false,
        'nodeEnd' => $nodeEnd,
        'gradient' => true,
        'cap' => false,
        'color' => $resolvedColor,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
        'devCounterEnd' => $devCounterEnd,
        'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($devCounterColor, $resolvedColor, 'zinc'),
        'startLabel' => $startLabel,
    ];

    if ($nodeEndDot !== null) {
        $segment['nodeEndDot'] = $nodeEndDot;
    } elseif ($nodeImage !== null) {
        $segment['nodeEndDot'] = false;
    }
@endphp

<x-translation-workbench::ui.tw-graph.segments.start
    :segment="$segment"
    :dev="$resolvedDev"
/>

@if ($nodeImage !== null)
    <x-translation-workbench::ui.tw-graph.primitives.node-image
        :id="$id . '.anchorNode-end.image'"
        :source="data_get($nodeImage, 'source', data_get($nodeImage, 'src'))"
        :size="data_get($nodeImage, 'size', \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('node_image_size', '3rem'))"
        :anchor-x="$anchorEnd['x']"
        :anchor-y="$anchorEnd['y']"
        :alt="data_get($nodeImage, 'alt', '')"
        :color="data_get($nodeImage, 'color', $resolvedColor)"
        :z-index="data_get($nodeImage, 'zIndex')"
    />
@endif
