{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-if-end.blade.php --}}
{{--
    Strang: flow-if-end

    Closes a handmade IF/ELSE flow section with a direct ENDIF label and an
    outgoing south-west arc from that label area.
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
    'endLabel' => ['text' => ['ENDIF']],
    'color' => null,
    'pathTone' => 'surface',
    'zIndex' => 20,
    'devMode' => null,
    'counterBridgeEnd' => 1,
    'counterEnd' => 2,
    'devCounterColor' => 'zinc',
])

@php
    $isRight = $side === 'right';
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $id = filled($id) ? (string) $id : 'strang.flow.if-end';
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
    $label = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($endLabel, null, $resolvedColor)
        ?? ['text' => ['ENDIF'], 'color' => $resolvedColor];
    $labelWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($label);
    $labelBridge = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
        $anchorStart,
        $isRight ? 'left-right' : 'right-left',
        $labelWidth,
        $resolvedBridgeLength,
    );
    $outArcStart = $labelBridge['anchorEnd'];
    $outArcEnd = [
        'x' => $add($outArcStart['x'], $horizontal($neg($resolvedArcSize))),
        'y' => $add($outArcStart['y'], $resolvedArcSize),
    ];

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', [
        'x' => $outArcEnd['x'],
        'y' => $outArcEnd['y'],
        'source' => $id,
        'sourceType' => 'strang.flow-if-end',
        'sourceAnchor' => 'anchorNode-end',
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);
@endphp

<x-translation-workbench::ui.tw-graph.segments.label-bridge
    :id="$id"
    :label-id="$id . '.label.center.1'"
    :label="$label"
    :anchor-start="$anchorStart"
    :direction="$isRight ? 'left-right' : 'right-left'"
    :bridge-length="$resolvedBridgeLength"
    :label-width="$labelWidth"
    :geometry="$labelBridge"
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
    'anchorStart' => $outArcStart,
    'anchorEnd' => $outArcEnd,
    'nodeStart' => false,
    'nodeEnd' => true,
    'nodeEndDot' => true,
    'devCounterEnd' => $counterEnd,
    'devCounterColor' => $devCounterColor,
    'color' => $resolvedColor,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $resolvedDev,
]" />
