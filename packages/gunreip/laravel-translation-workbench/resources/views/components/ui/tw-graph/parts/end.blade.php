{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/parts/end.blade.php --}}
{{--
    Part: end

    Usage:
    <x-translation-workbench::ui.tw-graph.parts.end
        id="resume.center.1.end"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        length="2rem"
    />

    Part role:
    A manual authoring wrapper around segments.end. It closes a part chain with
    a short stem and a cap without forcing the sample view to calculate the cap
    coordinates itself.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => null,
    'stemLength' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'length' => null,
    'capLength' => null,
    'color' => null,
    'nodeStart' => false,
    'devCounterEnd' => 'E',
    'devCounterColor' => null,
    'endLabel' => null,
    'zIndex' => 20,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : 'part.center.' . $resolvedComponentCounter . '.end';
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $defaultColor ?? null,
        'zinc',
    );
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $lineLength ?? null,
        'line_length',
        '4rem',
    );
    $resolvedLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $length,
        null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('part_end_length', '2rem'),
    );
    $resolvedCapLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor(
        $capLength,
        null,
        'cap_length',
        '1.75rem',
    );
    $resolvedDev = $devMode ?? $dev;
    $anchorStart = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $anchorStart = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $anchorEnd = match ($direction) {
        'top-bottom' => [
            'x' => $anchorStart['x'],
            'y' => 'calc(' . $anchorStart['y'] . ' - ' . $resolvedLength . ')',
        ],
        'left-right' => [
            'x' => 'calc(' . $anchorStart['x'] . ' + ' . $resolvedLength . ')',
            'y' => $anchorStart['y'],
        ],
        'right-left' => [
            'x' => 'calc(' . $anchorStart['x'] . ' - ' . $resolvedLength . ')',
            'y' => $anchorStart['y'],
        ],
        default => [
            'x' => $anchorStart['x'],
            'y' => 'calc(' . $anchorStart['y'] . ' + ' . $resolvedLength . ')',
        ],
    };
    $geometryBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([$anchorStart, $anchorEnd], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::put(
        $resolvedGraphId,
        $id . '.bounds',
        $geometryBounds['left'],
        $geometryBounds['bottom'],
        $geometryBounds['width'],
        $geometryBounds['height'],
        'center',
    );

    if (is_array($endLabel)) {
        $endLabelSide = data_get($endLabel, 'side');

        if ($direction === 'top-bottom' && in_array($endLabelSide, ['top', 'bottom'], true)) {
            $endLabel['side'] = $endLabelSide === 'top' ? 'bottom' : 'top';
        }
    }

    $segment = [
        'id' => $id,
        'direction' => $direction,
        'length' => $resolvedLength,
        'anchorStart' => $anchorStart,
        'anchorEnd' => $anchorEnd,
        'nodeStart' => $nodeStart,
        'nodeEnd' => false,
        'gradient' => false,
        'cap' => true,
        'capLength' => $resolvedCapLength,
        'color' => $resolvedColor,
        'zIndex' => $zIndex,
        'dev' => $resolvedDev,
        'devCounterEnd' => $devCounterEnd,
        'devCounterColor' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            $devCounterColor,
            $resolvedColor,
            'zinc',
        ),
        'endLabel' => $endLabel,
    ];
@endphp

<x-translation-workbench::ui.tw-graph.segments.end
    :segment="$segment"
    :dev="$resolvedDev"
/>
