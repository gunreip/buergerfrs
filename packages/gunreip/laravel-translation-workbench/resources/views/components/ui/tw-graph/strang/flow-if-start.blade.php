{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-if-start.blade.php --}}
{{--
    Strang: flow-if-start

    Opens a handmade IF/ELSE flow section. This is intentionally only a
    semantic wrapper around arcs and a direct text primitive.
--}}

@aware([
    'graphId' => null,
    'color' => null,
    'dev' => false,
])

@php
    $inheritedColor = $color ?? null;
@endphp

@props([
    'side' => 'left',
    'id' => null,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcSize' => null,
    'bridgeLength' => null,
    'introLabel' => ['text' => ['IF / ELSE flow', 'section starts']],
    'color' => null,
    'pathTone' => 'surface',
    'zIndex' => 20,
    'devMode' => null,
    'counterStart' => 'S',
    'counterArcInEnd' => 1,
    'counterBridgeEnd' => 2,
    'counterEnd' => 3,
    'devCounterColor' => 'zinc',
])

@php
    $isRight = $side === 'right';
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $id = filled($id) ? (string) $id : 'strang.flow.if-start';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedDev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(
        $devMode,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev),
    );
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $arcSize,
        'arc_size',
        '2.75rem',
    );
    $introLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($introLabel, null, $resolvedColor)
        ?? ['text' => ['IF / ELSE flow', 'section starts'], 'color' => $resolvedColor];
    $introLabelWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($introLabel);
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $anchorStart = $attachTarget ?: (is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem']);
    $anchorStart = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $horizontal = fn (string $value): string => $isRight ? $neg($value) : $value;
    $arcEastNorthEnd = [
        'x' => $add($anchorStart['x'], $horizontal($neg($resolvedArcSize))),
        'y' => $add($anchorStart['y'], $resolvedArcSize),
    ];
    $introBridge = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
        $arcEastNorthEnd,
        $isRight ? 'left-right' : 'right-left',
        $introLabelWidth,
        $resolvedBridgeLength,
    );
    $arcSouthWestStart = $introBridge['anchorEnd'];
    $arcSouthWestEnd = [
        'x' => $add($arcSouthWestStart['x'], $horizontal($neg($resolvedArcSize))),
        'y' => $add($arcSouthWestStart['y'], $resolvedArcSize),
    ];

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', [
        'x' => $arcSouthWestEnd['x'],
        'y' => $arcSouthWestEnd['y'],
        'source' => $id,
        'sourceType' => 'strang.flow-if-start',
        'sourceAnchor' => 'anchorNode-end',
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);
@endphp

<x-translation-workbench::ui.tw-graph.segments.arc :segment="[
    'id' => $id . ($isRight ? '.arc-west-north' : '.arc-east-north'),
    'startAnchor' => $isRight ? 'w' : 'e',
    'endAnchor' => 'n',
    'arcSize' => $resolvedArcSize,
    'anchorStart' => $anchorStart,
    'anchorEnd' => $arcEastNorthEnd,
    'nodeStart' => true,
    'nodeEnd' => true,
    'nodeEndDot' => false,
    'jointArrowEnd' => true,
    'jointArrowEndDirection' => $isRight ? 'right' : 'left',
    'devCounterStart' => $counterStart,
    'devCounterEnd' => $counterArcInEnd,
    'devCounterColor' => $devCounterColor,
    'color' => $resolvedColor,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $resolvedDev,
]" />

<x-translation-workbench::ui.tw-graph.segments.label-bridge
    :id="$id"
    :label-id="$id . '.intro.label.center.1'"
    :label="$introLabel"
    :anchor-start="$arcEastNorthEnd"
    :direction="$isRight ? 'left-right' : 'right-left'"
    :bridge-length="$resolvedBridgeLength"
    :label-width="$introLabelWidth"
    :geometry="$introBridge"
    :color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev="$resolvedDev"
    :dev-counter-end="$counterBridgeEnd"
    :dev-counter-color="$devCounterColor"
/>

<x-translation-workbench::ui.tw-graph.segments.arc :segment="[
    'id' => $id . ($isRight ? '.arc-south-east' : '.arc-south-west'),
    'startAnchor' => 's',
    'endAnchor' => $isRight ? 'e' : 'w',
    'arcSize' => $resolvedArcSize,
    'anchorStart' => $arcSouthWestStart,
    'anchorEnd' => $arcSouthWestEnd,
    'nodeStart' => false,
    'nodeEnd' => true,
    'devCounterEnd' => $counterEnd,
    'devCounterColor' => $devCounterColor,
    'color' => $resolvedColor,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $resolvedDev,
]" />
