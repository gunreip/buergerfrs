{{-- Ternary expression: delegates shared two-route geometry to flow-if-else. --}}
@aware(['graphId' => null, 'color' => null, 'dev' => false])
@php
    $inheritedColor = $color;
    $inheritedDev = $dev;
@endphp
@props([
    'id' => null,
    'componentCounter' => 1,
    'side' => 'left',
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'direction' => 'bottom-top',
    'beforeLength' => '2rem',
    'labelGap' => null,
    'afterLength' => '2rem',
    'stepCaps' => true,
    'capLength' => null,
    'arcRadius' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'trueBridgeLength' => null,
    'falseBridgeLength' => null,
    'stemLength' => '8rem',
    'nodeLabels' => [],
    'nodeEnd' => true,
    'color' => null,
    'devMode' => null,
    'counterStart' => 'D',
    'leftCounterEnd' => 1,
    'falseStemCounter' => 2,
    'rightCounterEnd' => 3,
    'zIndex' => 20,
    'conditionLabel' => ['text' => ['condition?'], 'width' => 'halfLong'],
    'ifStart' => ['text' => ['value if true'], 'width' => 'half'],
    'ifEnd' => ['text' => ['value if false'], 'width' => 'half'],
])

@php
    // Keep the authoring ID throughout the internal component chain.
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
@endphp@php
    $ternaryId = filled($id) ? (string) $id : ($graphId ?: 'tw-graph') . '.flow.ternary.' . max(1, (int) $componentCounter);
    $trueInformation = [($side === 'right' ? 'right' : 'left') => [
        'text' => ['True'], 'width' => 'half', 'badgeColor' => 'green',
    ]];
    $falseInformation = [($side === 'right' ? 'left' : 'right') => [
        'text' => ['False'], 'width' => 'half', 'badgeColor' => 'rose',
    ]];
@endphp

<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    :id="$ternaryId"
    :side="$side"
    :condition-label="$conditionLabel"
    :if-start="$ifStart"
    :if-end="$ifEnd"
    :true-node-labels="$trueInformation"
    :false-node-labels="$falseInformation"
    :attach-to="$attachTo"
    :anchor-start="$anchorStart"
    :direction="$direction"
    :before-length="$beforeLength"
    :label-gap="$labelGap"
    :after-length="$afterLength"
    :step-caps="$stepCaps"
    :cap-length="$capLength"
    :arc-radius="$arcRadius"
    :arc-size="$arcSize"
    :bridge-length="$bridgeLength"
    :true-bridge-length="$trueBridgeLength"
    :false-bridge-length="$falseBridgeLength"
    :stem-length="$stemLength"
    :node-labels="$nodeLabels"
    :node-end="$nodeEnd"
    :color="$color ?? $inheritedColor"
    :dev-mode="$devMode ?? $inheritedDev"
    :counter-start="$counterStart"
    :left-counter-end="$leftCounterEnd"
    :false-stem-counter="$falseStemCounter"
    :right-counter-end="$rightCounterEnd"
    :z-index="$zIndex"
/>

@php
    } finally {
        \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::restore($previousRootIdentifier);
    }
@endphp
