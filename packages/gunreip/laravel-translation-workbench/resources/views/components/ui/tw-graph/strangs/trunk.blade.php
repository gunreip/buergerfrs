{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/strangs/trunk.blade.php --}}
{{--
    Strang: trunk

    Usage:
    <x-translation-workbench::ui.tw-graph.strangs.trunk
        direction="bottom-top"
        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
        start-length="2.5rem"
        :path-lengths="['3rem', '4rem']"
        end-length="2rem"
    />

    Strang role:
    Keeps the trunk layer explicit and passes its geometry intent to
    paths.trunk. There is normally only one trunk strang, but the level stays
    consistent with merge and branch strangs.
--}}

@props([
    // Identity / rendering state
    'id' => 'strang.trunk',
    'dev' => false,

    // Base anchor / shared geometry
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => 'emerald',

    // Trunk path: paths.trunk
    'startLength' => null,
    'pathLengths' => [],
    'endLength' => null,
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $lengthOf = function (mixed $entry): string {
        if (is_array($entry)) {
            return (string) data_get($entry, 'length', data_get($entry, 0, '0rem'));
        }

        return filled($entry) ? (string) $entry : '0rem';
    };
    $sumLengths = function (array $lengths) use ($add): string {
        $sum = '0rem';

        foreach ($lengths as $length) {
            $sum = $add($sum, $length);
        }

        return $sum;
    };
    $anchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $pathLengthValues = collect($pathLengths)
        ->map(fn (mixed $entry): string => $lengthOf($entry))
        ->all();
    $totalLength = $sumLengths(array_filter([
        filled($startLength) ? (string) $startLength : '0rem',
        ...$pathLengthValues,
        filled($endLength) ? (string) $endLength : '0rem',
    ]));
    $borderPadding = '1rem';
    $borderThickness = 'calc(var(--tw-graph-protocol-node-size) + 1rem)';
    $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '16 185 129');

    if ($direction === 'left-right') {
        $strangBorderLeft = $subtract($anchor['x'], $borderPadding);
        $strangBorderBottom = $subtract($anchor['y'], $borderPadding);
        $strangBorderWidth = $add($totalLength, $add($borderPadding, $borderPadding));
        $strangBorderHeight = $borderThickness;
        $labelClass = 'left-2 top-0 -translate-y-1/2';
    } elseif ($direction === 'right-left') {
        $strangBorderLeft = $subtract($subtract($anchor['x'], $totalLength), $borderPadding);
        $strangBorderBottom = $subtract($anchor['y'], $borderPadding);
        $strangBorderWidth = $add($totalLength, $add($borderPadding, $borderPadding));
        $strangBorderHeight = $borderThickness;
        $labelClass = 'right-2 top-0 -translate-y-1/2';
    } elseif ($direction === 'top-bottom') {
        $strangBorderLeft = $subtract($anchor['x'], $borderPadding);
        $strangBorderBottom = $subtract($subtract($anchor['y'], $totalLength), $borderPadding);
        $strangBorderWidth = $borderThickness;
        $strangBorderHeight = $add($totalLength, $add($borderPadding, $borderPadding));
        $labelClass = 'left-2 top-0 -translate-y-1/2';
    } else {
        $strangBorderLeft = $subtract($anchor['x'], $borderPadding);
        $strangBorderBottom = $subtract($anchor['y'], $borderPadding);
        $strangBorderWidth = $borderThickness;
        $strangBorderHeight = $add($totalLength, $add($borderPadding, $borderPadding));
        $labelClass = 'left-2 top-0 -translate-y-1/2';
    }
@endphp

@if ($dev)
    <span
        class="tw-graph-protocol-dev-only pointer-events-none absolute rounded-lg border border-dashed"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $strangBorderLeft }});
            bottom: {{ $strangBorderBottom }};
            width: {{ $strangBorderWidth }};
            height: {{ $strangBorderHeight }};
            border-color: rgb({{ $colorRgb }} / 0.6);
        "
        title="{{ $id }} | strang.trunk"
    >
        <span class="absolute {{ $labelClass }}">
            <flux:badge
                size="sm"
                color="{{ $color }}"
            >
                {{ $id }}
            </flux:badge>
        </span>
    </span>
@endif

<x-translation-workbench::ui.tw-graph.paths.trunk
    :id="$id . '.trunk'"
    :direction="$direction"
    :anchor-start="$anchorStart"
    :start-length="$startLength"
    :path-lengths="$pathLengths"
    :end-length="$endLength"
    :color="$color"
    :dev="$dev"
/>
