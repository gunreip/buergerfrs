@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- One IF followed by one ELSEIF. The second test belongs exclusively to the first False route. --}}
@aware(['graphId' => null, 'color' => null])
@php
    $inheritedColor = $color;

@endphp
@props([
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'side' => 'left',
    'direction' => 'bottom-top',
    'conditionLabel' => ['text' => ['IF condition?'], 'width' => 'halfLong'],
    'ifStart' => ['text' => ['IF action'], 'width' => 'half'],
    'elseifs' => [],
    'beforeLength' => '2rem',
    'afterLength' => '2rem',
    'elseifBeforeLength' => '6rem',
    'elseifAfterLength' => '2rem',
    'stemLength' => '8rem',
    'arcRadius' => null,
    'bridgeLength' => '2rem',
    'ifEnd' => [],
    'nodeLabels' => [],
    'nodeEnd' => true,
    'color' => null,

    'zIndex' => 20,
])

@php
    // Keep the authoring ID throughout the internal component chain.
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
@endphp@php
    if (! is_array($elseifs) || count($elseifs) !== 1 || ! is_array(array_values($elseifs)[0])) {
        throw new \InvalidArgumentException('flow-if-elseif requires exactly one elseifs entry.');
    }
    $elseif = array_values($elseifs)[0];
    $elseifConditionLabel = $elseif['conditionLabel'] ?? ['text' => ['ELSEIF condition?'], 'width' => 'halfLong'];
    $elseifActionLabel = $elseif['actionLabel'] ?? ['text' => ['ELSEIF action'], 'width' => 'half'];
    $elseifBeforeLength = $elseif['beforeLength'] ?? $elseifBeforeLength;
    $elseifAfterLength = $elseif['afterLength'] ?? $elseifAfterLength;
    $resolvedGraphId = $graphId ?: 'tw-graph';
    $id = filled($id) ? (string) $id : $resolvedGraphId . '.flow.if-elseif.' . max(1, (int) $componentCounter);
    $resolvedColor = $color ?? $inheritedColor ?? 'zinc';

    $questionAnchor = (filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null) ?: $anchorStart;
    $ifActionColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        is_array($ifStart) ? data_get($ifStart, 'color') : null, $resolvedColor, 'zinc',
    );
    $elseifColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        is_array($elseifConditionLabel) ? data_get($elseifConditionLabel, 'color') : null, $elseif['color'] ?? $resolvedColor, 'zinc',
    );
    $resolvedAction = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($ifStart, 'center', $ifActionColor)
        ?? ['text' => ['IF action'], 'width' => 'half'];
    $resolvedElseifAction = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($elseifActionLabel, 'center', $elseifColor)
        ?? ['text' => ['ELSEIF action'], 'width' => 'half'];
    $ifWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($resolvedAction);
    $elseifWidth = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($resolvedElseifAction);
    $bridge = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
    $normalizedFalseLabel = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($ifEnd, 'center', $resolvedColor);
    $falseWidth = $normalizedFalseLabel === null ? '0rem' : \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($normalizedFalseLabel);
    $span = 'calc(max(' . $ifWidth . ', ' . $elseifWidth . ', ' . $falseWidth . ') + (' . $bridge . ' * 2))';
    $ifBridge = 'calc((' . $span . ' - ' . $ifWidth . ') / 2)';
    $elseifBridge = 'calc((' . $span . ' - ' . $elseifWidth . ') / 2)';
    $routeSide = $side === 'right' ? 'left' : 'right';
    $falseSide = $side === 'right' ? 'left' : 'right';
    $trueLabel = ['text' => ['True'], 'width' => 'half', 'badgeColor' => 'green'];
    $falseInformation = ['text' => ['False'], 'width' => 'half', 'badgeColor' => 'rose'];
@endphp

<x-translation-workbench::ui.tw-graph.strang.flow-step
    :id="$id . '.question'"
    :attach-to="$attachTo"
    :anchor-start="$anchorStart"
    :direction="$direction"
    :before-length="$beforeLength"
    :before-color="data_get($questionAnchor, 'color')"
    :after-length="$afterLength"
    :step-label="$conditionLabel"
    :node-labels="[$falseSide => $falseInformation]"
    :counter-end="1"
    :color="$resolvedColor"
    :z-index="$zIndex"
/>

{{-- Only the False route reaches this next condition. Its simple IF owns the final bypass. --}}
<x-translation-workbench::ui.tw-graph.strang.flow-if
    :id="$id . '.elseif'"
    :attach-to="$id . '.question.anchorNode-end'"
    :side="$side"
    :direction="$direction"
    :condition-label="$elseifConditionLabel"
    :if-start="$resolvedElseifAction"
    :return-color="$ifActionColor"
    :if-end="$ifEnd"
    :before-length="$elseifBeforeLength"
    :after-length="$elseifAfterLength"
    :stem-length="$stemLength"
    :arc-radius="$arcRadius"
    :true-bridge-length="$elseifBridge"
    :bridge-length="$bridge"
    :node-labels="$nodeLabels"
    :node-end="$nodeEnd"
    :counter-start="3"
    :left-counter-end="4"
    :false-stem-counter="5"
    :right-counter-end="6"
    :color="$elseifColor"
    :z-index="$zIndex"
/>

<x-translation-workbench::ui.tw-graph.parts.sideways
    :id="$id . '.true'"
    :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.question.anchorNode-end')"
    :side="$routeSide"
    :direction="$direction"
    :arc-radius="$arcRadius"
    :bridge-length="$ifBridge"
    :bridge-label="$resolvedAction"
    :node-label-left="$side === 'left' ? $trueLabel : null"
    :node-label-right="$side === 'right' ? $trueLabel : null"
    :dev-counter-end="2"
    :color="$ifActionColor"
    :z-index="$zIndex"
/>
@php
    $firstActionEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.true.anchorNode-end');
    $secondActionEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.elseif.true.anchorNode-end');
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.true.anchorNode-return', $secondActionEnd);
    // Join the two action outputs; the simple IF already owns the remaining line to the common end.
    $joinLength = $direction === 'top-bottom'
        ? 'calc(' . $firstActionEnd['y'] . ' - ' . $secondActionEnd['y'] . ')'
        : 'calc(' . $secondActionEnd['y'] . ' - ' . $firstActionEnd['y'] . ')';
@endphp
@if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($resolvedAction, 'return'), true))
<x-translation-workbench::ui.tw-graph.parts.start
    :id="$id . '.true.stem'"
    :line-jumps="data_get($resolvedAction, 'stemLineJumps', [])"
    :anchor-start="$firstActionEnd"
    :direction="$direction"
    :length="$joinLength"
    :gradient="false"
    :node-end="false"
    :dev-counter-end="false"
    :color="$ifActionColor"
    :z-index="$zIndex"
/>
@endif
@php
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
        $resolvedGraphId,
        $id . '.anchorNode-end',
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.elseif.anchorNode-end'),
    );
    \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::rail(
        $resolvedGraphId, $id . '.true.anchorNode-return',
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($resolvedElseifAction, 'return'), true) ? [$id . '.elseif.true.stem'] : [],
        [$id . '.anchorNode-end', $id . '.elseif.anchorNode-end'],
    );
    // Publish the terminal fallback separately from the common continuation.
    $fallbackId = $id . '.elseif';
    foreach (['end', 'return'] as $fallbackAnchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
            $resolvedGraphId, $id . '.false.anchorNode-' . $fallbackAnchor,
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $fallbackId . '.false.anchorNode-' . $fallbackAnchor),
        );
    }
    \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::rail(
        $resolvedGraphId, $id . '.false.anchorNode-return', [], [$id . '.anchorNode-end', $fallbackId . '.anchorNode-end'],
    );
@endphp

@php
    } finally {
        \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::restore($previousRootIdentifier);
    }
@endphp
