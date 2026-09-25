@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- Simple IF with no ELSE: delegates shared two-route geometry to flow-if-else. --}}
@aware(['graphId' => null, 'color' => null])
@php
    $inheritedColor = $color;

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
    'bridgeLength' => null,
    'trueBridgeLength' => null,
    'stemLength' => '8rem',
    'ifEnd' => [],
    'nodeLabels' => [],
    'nodeEnd' => true,
    'color' => null,
    // Internal composition override for the shared return stem.
    'returnColor' => null,

    'counterStart' => 'D',
    'leftCounterEnd' => 1,
    'falseStemCounter' => 2,
    'rightCounterEnd' => 3,
    'zIndex' => 20,
    'conditionLabel' => ['text' => ['condition?'], 'width' => 'halfLong'],
    'ifStart' => ['text' => ['Action'], 'width' => 'half'],
])

@php
    // Keep the authoring ID throughout the internal component chain.
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
@endphp@php
    $simpleIfId = filled($id) ? (string) $id : ($graphId ?: 'tw-graph') . '.flow.if.' . max(1, (int) $componentCounter);
    $trueInformation = [($side === 'right' ? 'right' : 'left') => [
        'text' => ['True'], 'width' => 'half', 'badgeColor' => 'green',
    ]];
    $falseInformation = [($side === 'right' ? 'left' : 'right') => [
        'text' => ['False'], 'width' => 'half', 'badgeColor' => 'rose',
    ]];
@endphp

<x-translation-workbench::ui.tw-graph.strang.flow-if-else
    :id="$simpleIfId"
    :side="$side"
    :condition-label="$conditionLabel"
    :if-start="$ifStart"
    :if-end="$ifEnd"
    :return-color="$returnColor"
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
    :bridge-length="$bridgeLength"
    :true-bridge-length="$trueBridgeLength"
    :stem-length="$stemLength"
    :node-labels="$nodeLabels"
    :node-end="$nodeEnd"
    :color="$color ?? $inheritedColor"
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
