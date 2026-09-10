{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-decision.blade.php --}}
{{--
    Strang: flow-decision

    A decision point splits the flow into left and right sideways parts. The
    wrapper owns only the semantic composition; geometry stays in parts.sideways.
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
    'id' => null,
    'componentCounter' => 1,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcRadius' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'leftBridgeLength' => null,
    'rightBridgeLength' => null,
    'extension' => null,
    'leftExtension' => null,
    'rightExtension' => null,
    'direction' => 'bottom-top',
    'decisionLabel' => null,
    'decisionLabelSide' => 'top',
    'nodeLabels' => [],
    'nodeEnd' => true,
    'color' => null,
    'counterStart' => 'D',
    'leftCounterEnd' => 1,
    'rightCounterEnd' => 2,
    'zIndex' => 20,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.flow.center.' . $resolvedComponentCounter . '.decision';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
        'zinc',
    );
    $resolvedDev = $devMode ?? $dev;
    $anchorStart = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $anchorStart = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $nodeLabels = is_array($nodeLabels) ? $nodeLabels : [];
    $leftNodeLabel = data_get($nodeLabels, 'left', data_get($nodeLabels, 'end.left'));
    $rightNodeLabel = data_get($nodeLabels, 'right', data_get($nodeLabels, 'end.right'));
    $decisionLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($decisionLabel, null, $resolvedColor);
    $decisionLabelSide = in_array($decisionLabelSide, ['top', 'bottom'], true) ? $decisionLabelSide : 'top';

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-decision', $anchorStart);
@endphp

<x-translation-workbench::ui.tw-graph.primitives.node
    :id="$id . '.anchorNode-decision'"
    :anchor-x="$anchorStart['x']"
    :anchor-y="$anchorStart['y']"
    :color="$resolvedColor"
    :z-index="$zIndex + 2"
/>

@if ($resolvedDev)
    <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
        :id="$id . '.anchorNode-decision.counter'"
        :dev="$resolvedDev"
        :anchor-x="$anchorStart['x']"
        :anchor-y="$anchorStart['y']"
        counter="{{ $counterStart }}"
        :color="$resolvedColor"
    />
@endif

@if ($decisionLabel !== null)
    <x-translation-workbench::ui.tw-graph.segments.label
        :id="$id . '.decision-label'"
        :label="$decisionLabel"
        :side="$decisionLabelSide"
        :anchor-x="$anchorStart['x']"
        :anchor-y="$anchorStart['y']"
        :color="$resolvedColor"
    />
@endif

<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.left'"
    side="left"
    :anchor-start="$anchorStart"
    :arc-radius="$arcRadius ?? $arcSize"
    :bridge-length="$leftBridgeLength ?? $bridgeLength"
    :extension="$leftExtension ?? $extension"
    :direction="$direction"
    :color="$resolvedColor"
    :node-end="$nodeEnd"
    :node-label-left="$leftNodeLabel"
    :dev-counter-end="$leftCounterEnd"
    :dev-counter-color="$resolvedColor"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
/>

<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.right'"
    side="right"
    :anchor-start="$anchorStart"
    :arc-radius="$arcRadius ?? $arcSize"
    :bridge-length="$rightBridgeLength ?? $bridgeLength"
    :extension="$rightExtension ?? $extension"
    :direction="$direction"
    :color="$resolvedColor"
    :node-end="$nodeEnd"
    :node-label-right="$rightNodeLabel"
    :dev-counter-end="$rightCounterEnd"
    :dev-counter-color="$resolvedColor"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
/>
