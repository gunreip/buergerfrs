@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
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
    'color' => null,

    'lineLength' => null,
    'stemLength' => null,
    'connectorLength' => null,
    'connectorGap' => null,
])

@php
    $inheritedColor = $color ?? null;


@endphp

@props([
    'id' => null,
    'componentCounter' => 1,
    'returnTo' => null,
    'lineJumps' => [],
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'length' => null,
    'gradient' => true,
    'color' => null,
    'nodeEnd' => true,
    'jointArrowEnd' => false,
    'nodeEndDot' => null,
    'nodeImage' => null,
    'nodeLabelLeft' => null,
    'nodeLabelRight' => null,
    'devCounterEnd' => 1,
    'devCounterColor' => null,
    'startLabel' => null,
    'zIndex' => 20,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : 'part.center.' . $resolvedComponentCounter . '.start';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $length,
        $stemLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength),
    );

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
    $startLabel = $normalizeLabel($startLabel);

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', [
        'x' => $anchorEnd['x'],
        'y' => $anchorEnd['y'],
        'source' => $id,
        'sourceType' => 'parts.start',
        'sourceAnchor' => 'anchorNode-end',
        'direction' => $direction,
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);


    if ($startLabel !== null) {
        $startLabelSide = data_get($startLabel, 'side', 'bottom');
        $startLabelSide = match (true) {
            $direction === 'top-bottom' && $startLabelSide === 'bottom' => 'top',
            $direction === 'top-bottom' && $startLabelSide === 'top' => 'bottom',
            default => $startLabelSide,
        };
        $startLabel['side'] = $startLabelSide;
    }
    $nodeEnd = $nodeEnd === true && ($nodeLabelRight || $nodeLabelLeft)
        ? [$nodeLabelRight, $nodeLabelLeft]
        : $nodeEnd;
    $segment = [
        'id' => $id,
        'lineJumps' => $lineJumps,
        'direction' => $direction,
        'length' => $resolvedLength,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $anchorEnd,
        'nodeStart' => false,
        'nodeEnd' => $nodeEnd,
        'jointArrowEnd' => $jointArrowEnd,
        'gradient' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($gradient, true),
        'cap' => false,
        'color' => $resolvedColor,
        'zIndex' => $zIndex,

        'devCounterEnd' => $devCounterEnd,
        'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($devCounterColor, $resolvedColor, 'zinc'),
        'startLabel' => $startLabel,
    ];

    if ($nodeEndDot !== null) {
        $segment['nodeEndDot'] = $nodeEndDot;
    } elseif ($nodeImage !== null || ($jointArrowEnd && ! $nodeLabelRight && ! $nodeLabelLeft)) {
        $segment['nodeEndDot'] = false;
    }
@endphp

<x-translation-workbench::ui.tw-graph.segments.start
    :segment="$segment"
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

@php
    if (filled($returnTo)) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::connect($resolvedGraphId, (string) $returnTo, $resolvedColor);
    }
@endphp
