{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/flow-step.blade.php --}}
{{--
    Strang: flow-step

    Process-oriented wrapper around segments.step. Flow-step names the public
    authoring intent; segments.step remains the single geometry implementation.
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
    'direction' => 'bottom-top',
    'attachTo' => null,
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
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : 'strang.flow.center.' . $resolvedComponentCounter . '.step';
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
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $advance = function (array $anchor, string $length) use ($add, $neg, $direction): array {
        $x = data_get($anchor, 'x', '0rem');
        $y = data_get($anchor, 'y', '0rem');

        return match ($direction) {
            'left-right' => ['x' => $add($x, $length), 'y' => $y],
            'right-left' => ['x' => $add($x, $neg($length)), 'y' => $y],
            'top-bottom' => ['x' => $x, 'y' => $add($y, $neg($length))],
            default => ['x' => $x, 'y' => $add($y, $length)],
        };
    };
    $stepLabelConfig = $stepLabel;
    if (! is_array($stepLabelConfig) && filled($stepLabelConfig)) {
        $stepLabelConfig = ['text' => $stepLabelConfig];
    }
    $stepLabelLines = collect(is_iterable(data_get($stepLabelConfig, 'text')) && ! is_string(data_get($stepLabelConfig, 'text')) ? data_get($stepLabelConfig, 'text') : [data_get($stepLabelConfig, 'text')])
        ->filter(fn (mixed $line): bool => filled($line))
        ->take(3)
        ->count();
    $resolvedLabelGap = $labelGap ?: 'calc('
        . \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::stepLabelContentGap($stepLabelLines)
        . ' + ('
        . \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_offset', '0.75rem')
        . ' * 2))';
    $anchorBeforeEnd = $advance($anchorStart, (string) ($beforeLength ?? '2rem'));
    $anchorAfterStart = $advance($anchorBeforeEnd, $resolvedLabelGap);
    $anchorEnd = $advance($anchorAfterStart, (string) ($afterLength ?? '2rem'));

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', [
        'x' => $anchorEnd['x'],
        'y' => $anchorEnd['y'],
        'source' => $id,
        'sourceType' => 'strang.flow-step',
        'sourceAnchor' => 'anchorNode-end',
        'direction' => $direction,
        'color' => $resolvedColor,
        'zIndex' => (string) $zIndex,
    ]);
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
