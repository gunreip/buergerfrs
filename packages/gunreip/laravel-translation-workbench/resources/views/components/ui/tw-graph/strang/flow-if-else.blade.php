{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-if-else.blade.php --}}
{{--
    Strang: flow-if-else

    A question step leads to two stacked alternative action bridges and one shared end.
    Geometry stays in flow-step / segments.step and parts.sideways / label-bridge.
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
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'arcRadius' => null,
    'arcSize' => null,
    'bridgeLength' => null,
    'trueBridgeLength' => null,
    'falseBridgeLength' => null,
    'side' => 'left',
    'stemLength' => '8rem',
    'leftBridgeLength' => null,
    'rightBridgeLength' => null,
    'direction' => 'bottom-top',
    'conditionLabel' => ['text' => ['IF condition?'], 'width' => 'halfLong', 'align' => 'center'],
    'beforeLength' => '2rem',
    'labelGap' => null,
    'afterLength' => '2rem',
    'stepCaps' => true,
    'capLength' => null,
    'ifStart' => ['text' => ['True'], 'width' => 'half', 'align' => 'center'],
    'falseBypass' => false,
    'ifEnd' => ['text' => ['False'], 'width' => 'half', 'align' => 'center'],
    'nodeLabels' => [],
    'trueNodeLabels' => [],
    'falseNodeLabels' => [],
    'nodeEnd' => true,
    'color' => null,
    // Internal composition override for the shared return stem.
    'returnColor' => null,
    'counterStart' => 'D',
    'leftCounterEnd' => 1,
    'falseStemCounter' => 2,
    'rightCounterEnd' => 3,
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
    $resolvedDev = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(
        $devMode,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($dev),
    );
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $anchorStart = $attachTarget ?: (is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem']);

    $nodeLabels = is_array($nodeLabels) ? $nodeLabels : [];
    $leftNodeLabel = data_get($nodeLabels, 'left', data_get($nodeLabels, 'end.left'));
    $rightNodeLabel = data_get($nodeLabels, 'right', data_get($nodeLabels, 'end.right'));
    $trueColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        is_array($ifStart) ? data_get($ifStart, 'color') : null,
        $resolvedColor,
        'zinc',
    );
    $ifStart = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($ifStart, 'center', $trueColor)
        ?? ['text' => ['True'], 'width' => 'half'];
    // Lane options survive even when the label configuration has no text.
    $falseColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        is_array($ifEnd) ? data_get($ifEnd, 'color') : null,
        $resolvedColor,
        'zinc',
    );
    $requestedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        is_array($ifEnd) ? data_get($ifEnd, 'stemLength') : null,
        $stemLength,
        '8rem',
    );
    $normalizedFalseLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($ifEnd, 'center', $falseColor);
    $falseBypass = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($falseBypass) || $normalizedFalseLabel === null;
    $ifEnd = $normalizedFalseLabel ?? ['text' => [], 'width' => 'half'];
    $trueWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($ifStart);
    $falseWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($ifEnd);
    $trueLength = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($trueBridgeLength ?? $leftBridgeLength ?? $bridgeLength);
    $falseLength = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($falseBridgeLength ?? $rightBridgeLength ?? $bridgeLength);
    // Equal span, not equal label width: each label keeps its own authored width.
    $sharedSpan = 'max(calc(' . $trueWidth . ' + (' . $trueLength . ' * 2)), calc(' . $falseWidth . ' + (' . $falseLength . ' * 2)))';
    if ($falseBypass) {
        $sharedSpan = 'calc(' . $trueWidth . ' + (' . $trueLength . ' * 2))';
    }
    $alignedTrueLength = 'calc((' . $sharedSpan . ' - ' . $trueWidth . ') / 2)';
    $alignedFalseLength = 'calc((' . $sharedSpan . ' - ' . $falseWidth . ') / 2)';
    $labelHeight = static function (array $label): string {
        $lines = min(count(\Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::lines($label['text'])), max(1, (int) data_get($label, 'maxLines', 3)));
        return \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::stepLabelContentGap($lines);
    };
    $resolvedStemLength = 'max(' . $requestedStemLength . ', calc((' . $labelHeight($ifStart) . ' + ' . ($falseBypass ? '0rem' : $labelHeight($ifEnd)) . ') / 2 + 2rem))';
    // parts.sideways names its incoming arc side; graph side names the destination.
    $routeSide = $side === 'right' ? 'left' : 'right';
@endphp

<x-translation-workbench::ui.tw-graph.strang.flow-step
    :id="$id . '.question'"
    :anchor-start="$anchorStart"
    :direction="$direction"
    :step-label="$conditionLabel"
    :before-length="$beforeLength"
    :label-gap="$labelGap"
    :after-length="$afterLength"
    :step-caps="$stepCaps"
    :cap-length="$capLength"
    :counter-end="$counterStart"
    :color="$resolvedColor"
    :dev-mode="$resolvedDev"
    :z-index="$zIndex"
/>

@php
    $decisionBranchAnchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.question.anchorNode-end');
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-decision', $decisionBranchAnchor);
@endphp

{{-- True: immediate sideways connection, then the stem to the shared end. --}}
<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.true'"
    :node-label-left="data_get($trueNodeLabels, 'left')"
    :node-label-right="data_get($trueNodeLabels, 'right')"
    :side="$routeSide"
    :anchor-start="$decisionBranchAnchor"
    :arc-radius="$arcRadius ?? $arcSize"
    :bridge-length="$alignedTrueLength"
    :bridge-out-length="! \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($ifStart, 'return'), true) && filled(data_get($ifStart, 'returnOffset')) ? 'calc(' . $alignedTrueLength . ' + ' . $ifStart['returnOffset'] . ')' : null"
    :bridge-label="$ifStart"
    :direction="$direction"
    :color="$trueColor"
    :node-end="true"
    :joint-arrow-end="true"
    :dev-counter-end="$leftCounterEnd"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
/>
@if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($ifStart, 'return'), true))
<x-translation-workbench::ui.tw-graph.parts.start
    :id="$id . '.true.stem'"
    :line-jumps="data_get($ifStart, 'stemLineJumps', [])"
    :gradient="false"
    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.true.anchorNode-end')"
    :length="$resolvedStemLength"
    :direction="$direction"
    :color="\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($returnColor, $trueColor, 'zinc')"
    :node-end="false"
    :dev-counter-end="false"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
/>
@endif

{{-- False: first the stem, then the same sideways connection. --}}
<x-translation-workbench::ui.tw-graph.parts.start
    :id="$id . '.false.stem'"
    :node-label-left="data_get($falseNodeLabels, 'left')"
    :node-label-right="data_get($falseNodeLabels, 'right')"
    :gradient="false"
    :anchor-start="$decisionBranchAnchor"
    :length="$resolvedStemLength"
    :direction="$direction"
    :color="$falseColor"
    :node-end="true"
    :joint-arrow-end="true"
    :dev-counter-end="$falseStemCounter"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
/>
<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.false'"
    :side="$routeSide"
    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.false.stem.anchorNode-end')"
    :arc-radius="$arcRadius ?? $arcSize"
    :bridge-length="$falseBypass ? $sharedSpan : $alignedFalseLength"
    :bridge-label="$falseBypass ? null : $ifEnd"
    :direction="$direction"
    :color="$falseColor"
    :node-end="$nodeEnd"
    :node-label-left="$leftNodeLabel"
    :node-label-right="$rightNodeLabel"
    :dev-counter-end="$rightCounterEnd"
    :dev-counter-color="$falseColor"
    :z-index="$zIndex"
    :dev-mode="$resolvedDev"
/>

@php
    $sharedEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.false.anchorNode-end');
    // Preserve the owning False node color; expose the shared True rail separately.
    $sharedEnd['returnColor'] = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($returnColor, $trueColor, 'zinc');
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-start', $decisionBranchAnchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', $sharedEnd);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.true.anchorNode-return', $sharedEnd);
    \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::rail($resolvedGraphId, $id . '.true.anchorNode-return', [], [$id . '.anchorNode-end']);
    // Former public output IDs remain attachable and now represent the common continuation.
    foreach (['anchorNode-true', 'anchorNode-false', 'left.anchorNode-end', 'right.anchorNode-end'] as $alias) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.' . $alias, $sharedEnd);
    }
@endphp
