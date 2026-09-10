{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-step.blade.php --}}
{{--
    Strang: flow-step

    Process-oriented wrapper around segments.step. Flow-step names the public
    authoring intent; segments.step remains the single geometry implementation.
--}}

@aware([
    'color' => null,
    'dev' => false,
])

@php
    $inheritedColor = $color ?? null;
@endphp

@props([
    'id' => null,
    'componentCounter' => 1,
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'beforeLength' => null,
    'labelGap' => null,
    'afterLength' => null,
    'stepLabel' => null,
    'nodeLabels' => [],
    'nodeEnd' => true,
    'nodeEndDot' => null,
    'stepCaps' => true,
    'capLength' => null,
    'color' => null,
    'counterStart' => 'S',
    'counterEnd' => 1,
    'zIndex' => 20,
    'devMode' => null,
])

@php
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : 'strang.flow.center.' . $resolvedComponentCounter . '.step';
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
    $normalizeLabel = fn (mixed $label, string $side): ?array => tap(
        \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, null, $resolvedColor),
        function (?array &$normalized) use ($side): void {
            if ($normalized !== null) {
                $normalized['side'] = $side;
            }
        },
    );
    $nodeLabels = is_array($nodeLabels) ? $nodeLabels : [];
    $endNodeLabels = data_get($nodeLabels, 'end', $nodeLabels);
    $endRightLabel = $normalizeLabel(data_get($endNodeLabels, 'right'), 'right');
    $endLeftLabel = $normalizeLabel(data_get($endNodeLabels, 'left'), 'left');
    $nodeEndValue = ($endRightLabel !== null || $endLeftLabel !== null)
        ? [$endRightLabel, $endLeftLabel]
        : $nodeEnd;
    $nodeEndDot = $nodeEndDot ?? (bool) $nodeEndValue;
    $segment = [
        'id' => $id,
        'direction' => $direction,
        'anchorStart' => $anchorStart,
        'beforeLength' => $beforeLength ?? '2rem',
        'labelGap' => $labelGap,
        'afterLength' => $afterLength ?? '2rem',
        'stepLabel' => $stepLabel,
        'nodeStart' => false,
        'nodeEnd' => $nodeEndValue,
        'nodeEndDot' => $nodeEndDot,
        'stepCaps' => $stepCaps,
        'capLength' => $capLength ?? \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('cap_length', '1.75rem'),
        'color' => $resolvedColor,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
        'devCounterStart' => $counterStart,
        'devCounterEnd' => $counterEnd,
        'devCounterColor' => $resolvedColor,
    ];
@endphp

<x-translation-workbench::ui.tw-graph.segments.step
    :segment="$segment"
    :dev="$resolvedDev"
/>
