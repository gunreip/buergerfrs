{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-if-condition.blade.php --}}
{{--
    Strang: flow-if-condition

    Renders one handmade IF/ELSEIF/ELSE row: left rail arc, direct condition
    label, right result arc, and optional continuation stems.
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
    'conditionLabel' => null,
    'conditionRailWidth' => null,
    'bridgeLength' => null,
    'thenArcReach' => null,
    'leftStemLength' => null,
    'thenStemLength' => null,
    'leftStem' => true,
    'thenContinuation' => 'stem',
    'color' => null,
    'fromColor' => null,
    'pathTone' => 'surface',
    'zIndex' => 20,
    'devMode' => null,
    'counterCondition' => 1,
    'counterBridgeEnd' => 2,
    'counterThenArcEnd' => 3,
    'counterThenEnd' => 4,
    'counterLeftEnd' => 5,
    'devCounterColor' => 'zinc',
])

@php
    $isRight = $side === 'right';
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $id = filled($id) ? (string) $id : 'strang.flow.if-condition';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedFromColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $fromColor,
        null,
        $resolvedColor,
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
    $conditionLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($conditionLabel, null, $resolvedColor)
        ?? ['text' => ['IF'], 'color' => $resolvedColor];
    $resolvedConditionColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        data_get($conditionLabel, 'badgeColor', data_get($conditionLabel, 'color')),
        null,
        $resolvedColor,
    );
    $conditionRailWidth = filled($conditionRailWidth)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::widthValue($conditionRailWidth)
        : \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($conditionLabel);
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
    $resolvedThenArcReach = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $thenArcReach,
        null,
        $resolvedArcSize,
    );
    $resolvedLeftStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $leftStemLength,
        'stem_length',
        '4rem',
    );
    $resolvedThenStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $thenStemLength,
        'stem_length',
        '4rem',
    );
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
    $conditionAnchor = [
        'x' => $add($anchorStart['x'], $horizontal($resolvedArcSize)),
        'y' => $add($anchorStart['y'], $resolvedArcSize),
    ];
    $conditionBridge = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry(
        $conditionAnchor,
        $isRight ? 'right-left' : 'left-right',
        $conditionRailWidth,
        $resolvedBridgeLength,
    );
    $leftStemEnd = [
        'x' => $anchorStart['x'],
        'y' => $add($anchorStart['y'], $resolvedLeftStemLength),
    ];
    $thenArcStart = $conditionBridge['anchorEnd'];
    $thenArcEnd = [
        'x' => $add($thenArcStart['x'], $horizontal($resolvedThenArcReach)),
        'y' => $add($thenArcStart['y'], $resolvedArcSize),
    ];
    $thenStemEnd = [
        'x' => $thenArcEnd['x'],
        'y' => $add($thenArcEnd['y'], $resolvedThenStemLength),
    ];
    $thenEndArcEnd = [
        'x' => $add($thenArcEnd['x'], $horizontal($neg($resolvedArcSize))),
        'y' => $add($thenArcEnd['y'], $resolvedArcSize),
    ];
    $thenContinuation = $thenContinuation === 'arc-west-north' ? 'arc-east-north' : $thenContinuation;
    $thenContinuation = in_array($thenContinuation, ['stem', 'arc-east-north', 'none'], true) ? $thenContinuation : 'stem';
    $renderLeftStem = $leftStem !== false && ! in_array(trim($resolvedLeftStemLength), ['0', '0rem'], true);
    $thenAnchorEnd = match ($thenContinuation) {
        'arc-east-north' => $thenEndArcEnd,
        'none' => $thenArcEnd,
        default => $thenStemEnd,
    };

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.condition.anchorNode-end', [
        'x' => $conditionAnchor['x'],
        'y' => $conditionAnchor['y'],
        'source' => $id,
        'sourceType' => 'strang.flow-if-condition',
        'sourceAnchor' => 'condition.anchorNode-end',
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.left.anchorNode-end', [
        'x' => $leftStemEnd['x'],
        'y' => $leftStemEnd['y'],
        'source' => $id,
        'sourceType' => 'strang.flow-if-condition',
        'sourceAnchor' => 'left.anchorNode-end',
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.then.anchorNode-end', [
        'x' => $thenAnchorEnd['x'],
        'y' => $thenAnchorEnd['y'],
        'source' => $id,
        'sourceType' => 'strang.flow-if-condition',
        'sourceAnchor' => 'then.anchorNode-end',
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);
@endphp

<x-translation-workbench::ui.tw-graph.segments.arc :segment="[
    'id' => $id . ($isRight ? '.arc-east-north' : '.arc-west-north'),
    'startAnchor' => $isRight ? 'e' : 'w',
    'endAnchor' => 'n',
    'arcSize' => $resolvedArcSize,
    'anchorStart' => $anchorStart,
    'anchorEnd' => $conditionAnchor,
    'nodeStart' => false,
    'nodeEnd' => true,
    'nodeEndDot' => false,
    'jointArrowEnd' => true,
    'jointArrowEndDirection' => $isRight ? 'left' : 'right',
    'devCounterEnd' => $counterCondition,
    'devCounterColor' => $devCounterColor,
    'color' => $resolvedColor,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $resolvedDev,
]" />

<x-translation-workbench::ui.tw-graph.segments.label-bridge
    :id="$id"
    :label-id="$id . '.label.center.1'"
    :label="$conditionLabel"
    :anchor-start="$conditionAnchor"
    :direction="$isRight ? 'right-left' : 'left-right'"
    :bridge-length="$resolvedBridgeLength"
    :label-width="$conditionRailWidth"
    :geometry="$conditionBridge"
    :color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev="$resolvedDev"
    :dev-counter-end="$counterBridgeEnd"
    :dev-counter-color="$devCounterColor"
/>

<x-translation-workbench::ui.tw-graph.segments.arc :segment="[
    'id' => $id . ($isRight ? '.arc-south-west' : '.arc-south-east'),
    'startAnchor' => 's',
    'endAnchor' => $isRight ? 'w' : 'e',
    'arcSize' => $resolvedArcSize,
    'anchorStart' => $thenArcStart,
    'anchorEnd' => $thenArcEnd,
    'nodeStart' => false,
    'nodeEnd' => true,
    'nodeEndDot' => true,
    'jointArrowEnd' => false,
    'jointArrowEndDirection' => $isRight ? 'left' : 'right',
    'devCounterEnd' => $counterThenArcEnd,
    'devCounterColor' => $devCounterColor,
    'color' => $resolvedColor,
    'tone' => $pathTone,
    'zIndex' => $zIndex,
    'dev' => $resolvedDev,
]" />

@if ($thenContinuation === 'stem')
    <x-translation-workbench::ui.tw-graph.segments.path :segment="[
        'id' => $id . ($isRight ? '.arc-south-west.stem' : '.arc-south-east.stem'),
        'direction' => 'bottom-top',
        'length' => $resolvedThenStemLength,
        'anchorStart' => $thenArcEnd,
        'anchorEnd' => $thenStemEnd,
        'nodeStart' => false,
        'nodeEnd' => true,
        'devCounterEnd' => $counterThenEnd,
        'devCounterColor' => $devCounterColor,
        'color' => $resolvedColor,
        'tone' => $pathTone,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
    ]" />
@elseif ($thenContinuation === 'arc-east-north')
    <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
        'id' => $id . ($isRight ? '.arc-west-north' : '.arc-east-north'),
        'startAnchor' => $isRight ? 'w' : 'e',
        'endAnchor' => 'n',
        'arcSize' => $resolvedArcSize,
        'anchorStart' => $thenArcEnd,
        'anchorEnd' => $thenEndArcEnd,
        'nodeStart' => false,
        'nodeEnd' => true,
        'nodeEndDot' => false,
        'jointArrowEnd' => true,
        'jointArrowEndDirection' => $isRight ? 'right' : 'left',
        'devCounterEnd' => $counterThenEnd,
        'devCounterColor' => $devCounterColor,
        'color' => $resolvedColor,
        'tone' => $pathTone,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
    ]" />
@endif

@if ($renderLeftStem)
    <x-translation-workbench::ui.tw-graph.segments.path :segment="[
        'id' => $id . '.stem',
        'direction' => 'bottom-top',
        'length' => $resolvedLeftStemLength,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $leftStemEnd,
        'nodeStart' => false,
        'nodeEnd' => true,
        'devCounterEnd' => $counterLeftEnd,
        'devCounterColor' => $devCounterColor,
        'color' => $resolvedColor,
        'tone' => $pathTone,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
    ]" />
@endif
