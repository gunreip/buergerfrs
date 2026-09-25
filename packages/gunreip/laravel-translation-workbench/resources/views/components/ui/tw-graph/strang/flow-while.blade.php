@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- Public WHILE semantics; flow-step owns the condition, paths.loop owns the closed route. --}}
@aware(['graphId' => null, 'color' => null, 'arcRadius' => null])
@php
    $inheritedColor = $color;
    $inheritedArcRadius = $arcRadius;
@endphp
@props([
    'id',
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'side' => 'left',
    'counterStart' => 1,
    'arcRadius' => null,
    'trueBridgeLength' => '2rem',
    'stemLength' => '4rem',
    'conditionLabel' => ['text' => ['WHILE pending items?'], 'width' => 'default'],
    'trueLabel' => ['text' => ['TRUE'], 'width' => 'half', 'side' => 'top'],
    'falseLabel' => ['text' => ['FALSE'], 'width' => 'half'],
    'actionLabel' => ['text' => ['Process next item'], 'width' => 'default'],
    'color' => null,

    'zIndex' => 20,
])
@php
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
        $graph = $graphId ?: 'tw-graph';
        $arcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor($arcRadius, $inheritedArcRadius, 'arc_radius', '2.75rem');
        $loopColor = $color ?? $inheritedColor ?? 'zinc';

        // Removed props must not silently survive as unused HTML attributes.
        foreach (['beforeLength', 'afterLength', 'labelGap', 'bridgeLength', 'bridgeOutLength'] as $removedProp) {
            if ($attributes->has($removedProp) || $attributes->has(\Illuminate\Support\Str::kebab($removedProp))) {
                throw new \InvalidArgumentException("flow-while {$removedProp} was removed; configure condition-label or action-label instead.");
            }
        }
        $trueLabelAnchor = data_get($trueLabel, 'anchor', 'bridge');
        if (! in_array($trueLabelAnchor, ['bridge', 'condition'], true)) {
            throw new \InvalidArgumentException('flow-while true-label.anchor must be bridge or condition.');
        }
        $condition = array_replace(['beforeLength' => '2rem', 'labelGap' => '4rem', 'afterLength' => '2rem'], $conditionLabel);
        $action = array_replace(['beforeLength' => '4rem', 'afterLength' => '4rem'], $actionLabel);
        foreach ([
            'condition-label.beforeLength' => $condition['beforeLength'],
            'condition-label.labelGap' => $condition['labelGap'],
            'condition-label.afterLength' => $condition['afterLength'],
            'action-label.beforeLength' => $action['beforeLength'],
            'action-label.afterLength' => $action['afterLength'],
            'arcRadius' => $arcRadius, 'trueBridgeLength' => $trueBridgeLength, 'stemLength' => $stemLength,
        ] as $prop => $length) {
            $value = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($length);
            if ($value === null || $value <= 0) {
                throw new \InvalidArgumentException("flow-while {$prop} must be a positive rem length.");
            }
        }
        $start = filled($attachTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graph, $attachTo) : $anchorStart;
        if (! is_array($start)) {
            throw new \InvalidArgumentException('flow-while attachTo does not resolve to an anchor.');
        }
@endphp
<x-translation-workbench::ui.tw-graph.strang.flow-step
    :id="$id . '.condition'"
    :anchor-start="$start"
    :step-label="$condition"
    :before-length="$condition['beforeLength']"
    :label-gap="$condition['labelGap']"
    :after-length="$condition['afterLength']"
    :counter-end="(int) $counterStart"
    :color="$loopColor"
    :z-index="$zIndex"
/>
@php
    $conditionEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graph, $id . '.condition.anchorNode-end');
@endphp
<x-translation-workbench::ui.tw-graph.paths.loop
    :id="$id"
    :counter-start="(int) $counterStart + 1"
    :anchor-start="$conditionEnd"
    :anchor-return="$start"
    :side="$side"
    :arc-radius="$arcRadius"
    :entry-bridge-length="$trueBridgeLength"
    :bridge-label="$action"
    :return="data_get($action, 'return', true)"
    :bridge-length="$action['beforeLength']"
    :bridge-out-length="$action['afterLength']"
    :exit-length="$stemLength"
    :entry-label="$trueLabel"
    :entry-label-anchor="$trueLabelAnchor === 'condition' ? $conditionEnd : null"
    :exit-label="$falseLabel"
    :color="$loopColor"
    :z-index="$zIndex"
/>
@php
        // The public loop starts before its condition; the path starts at the split.
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($graph, $id . '.anchorNode-start', array_replace($start, [
            'source' => $id, 'sourceType' => 'strang.flow-while', 'sourceAnchor' => 'anchorNode-start',
            'direction' => 'bottom-top', 'color' => $loopColor,
        ]));
    } finally {
        \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::restore($previousRootIdentifier);
    }
@endphp
