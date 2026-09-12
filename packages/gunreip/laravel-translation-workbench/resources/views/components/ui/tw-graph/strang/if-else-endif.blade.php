{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/if-else-endif.blade.php --}}
{{--
    Strang: if-else-endif

    Coordinates a complete handmade IF / ELSEIF / ENDIF section from the
    existing atomic flow-if-* strangs. The wrapper owns shared props and anchor
    wiring; the atomic components keep their local geometry responsibilities.
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
    'startBridgeLength' => null,
    'conditionBridgeLength' => null,
    'endBridgeLength' => null,
    'thenArcReach' => null,
    'leftStemLength' => null,
    'thenStemLength' => null,
    'leftStem' => true,
    'thenContinuation' => 'stem',
    'introLabel' => ['text' => ['IF / ELSE flow', 'section starts']],
    'ifConditionLabel' => null,
    'elseifConditions' => [],
    'conditionRailWidth' => null,
    'endLabel' => ['text' => ['ENDIF']],
    'bypass' => false,
    'color' => null,
    'pathTone' => 'surface',
    'zIndex' => 20,
    'devMode' => null,
    'counterStart' => 1,
    'devCounterColor' => 'zinc',
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $id = filled($id) ? (string) $id : 'strang.if-else-endif';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedDev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(
        $devMode,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev),
    );
    $resolvedStartBridgeLength = filled($startBridgeLength) ? $startBridgeLength : $bridgeLength;
    $resolvedConditionBridgeLength = filled($conditionBridgeLength) ? $conditionBridgeLength : $bridgeLength;
    $resolvedEndBridgeLength = filled($endBridgeLength) ? $endBridgeLength : $bridgeLength;
    $startId = $id . '.start';
    $conditionsId = $id . '.conditions';
    $endId = $id . '.endif';
    $counterStart = is_numeric($counterStart) ? (int) $counterStart : 1;
    $conditionCounterStart = $counterStart + 4;
    $conditionRows = 1 + (is_iterable($elseifConditions) ? collect($elseifConditions)->count() : 0);
    $endCounterStart = $conditionCounterStart + ($conditionRows * 5);
@endphp

<x-translation-workbench::ui.tw-graph.strang.flow-if-start
    :side="$side"
    :id="$startId"
    :attach-to="$attachTo"
    :anchor-start="$anchorStart"
    :arc-size="$arcSize"
    :bridge-length="$resolvedStartBridgeLength"
    :intro-label="$introLabel"
    :color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
    :counter-start="$counterStart"
    :counter-arc-in-end="$counterStart + 1"
    :counter-bridge-end="$counterStart + 2"
    :counter-end="$counterStart + 3"
    :dev-counter-color="$devCounterColor"
/>

<x-translation-workbench::ui.tw-graph.strang.flow-if-condition-set
    :side="$side"
    :id="$conditionsId"
    :if-id="$id . '.if'"
    :elseif-id="$id . '.elseif'"
    :attach-to="$startId . '.anchorNode-end'"
    :if-condition-label="$ifConditionLabel"
    :elseif-conditions="$elseifConditions"
    :condition-rail-width="$conditionRailWidth"
    :arc-size="$arcSize"
    :bridge-length="$resolvedConditionBridgeLength"
    :then-arc-reach="$thenArcReach"
    :left-stem-length="$leftStemLength"
    :then-stem-length="$thenStemLength"
    :left-stem="$leftStem"
    :then-continuation="$thenContinuation"
    :color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
    :counter-start="$conditionCounterStart"
    :dev-counter-color="$devCounterColor"
/>

<x-translation-workbench::ui.tw-graph.strang.flow-if-end
    :side="$side"
    :id="$endId"
    :attach-to="$conditionsId . '.then.anchorNode-end'"
    :arc-size="$arcSize"
    :bridge-length="$resolvedEndBridgeLength"
    :end-label="$endLabel"
    :color="$resolvedColor"
    :path-tone="$pathTone"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
    :counter-bridge-end="$endCounterStart"
    :counter-end="$endCounterStart + 1"
    :dev-counter-color="$devCounterColor"
/>


@if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($bypass))
    <x-translation-workbench::ui.tw-graph.strang.flow-if-bypass
        :side="$side"
        :id="$id . '.bypass'"
        :attach-to="$startId . '.anchorNode-end'"
        :merge-to="$endId . '.anchorNode-end'"
        :counter-start="$endCounterStart + 2"
        :dev-counter-color="$devCounterColor"
        :arc-size="$arcSize"
        :color="$resolvedColor"
        :path-tone="$pathTone"
        :z-index="$zIndex"
        :dev-mode="$resolvedDev"
    />
@endif

@php
    foreach ([
        'start.anchorNode-end' => $startId . '.anchorNode-end',
        'conditions.left.anchorNode-end' => $conditionsId . '.left.anchorNode-end',
        'conditions.then.anchorNode-end' => $conditionsId . '.then.anchorNode-end',
        'conditions.condition.anchorNode-end' => $conditionsId . '.condition.anchorNode-end',
        'endif.anchorNode-end' => $endId . '.anchorNode-end',
    ] as $alias => $source) {
        $anchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $source);

        if ($anchor !== null) {
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
                $resolvedGraphId,
                $id . '.' . $alias,
                $anchor,
            );
        }
    }
@endphp
