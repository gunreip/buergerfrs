{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/trunk.blade.php --}}
{{--
    Path: trunk

    Internal usage:
    <x-translation-workbench::ui.tw-graph.paths.trunk
        direction="bottom-top"
        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
        start-length="2.5rem"
        :path-count="10"
        :path-lengths="[1 => '2rem', 2 => null, 3 => '3rem']"
        end-length="1rem"
        start-label="Path start"
        :start-node-labels="['left' => 'Source', 'right' => 'Target']"
        end-label="Path end"
    />

    Path role:
    Trunk owns the segment chain. It calculates each segment anchorStart and
    anchorEnd from the previous segment end anchor, so callers provide lengths
    instead of hand-wiring every segment coordinate.

    Authoring rule:
    Use tw-graph.strang.trunk from authoring views. This path component is the
    lower-level renderer owned by the strang layer.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => '4rem',
    'capLength' => '1.75rem',
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'startLength' => null,
    'pathLengths' => [],
    'pathCount' => null,
    'defaultPathSegments' => 10,
    'endLength' => null,
    'endCapLength' => null,
    'startLabel' => null,
    'endLabel' => null,
    'startNodeLabels' => [],
    'color' => null,
    'zIndex' => null,
    'counterStart' => 1,
    'devMode' => null,
    'showDevBox' => true,
    'showLayoutSpacer' => true,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.paths.trunk.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'zinc');
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedPathCount = max(0, (int) ($pathCount ?? $defaultPathSegments));
    $resolvedDev = $devMode ?? $dev;
    $startLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedLineLength, '4rem');
    $pathLengthOverrides = is_array($pathLengths) ? $pathLengths : [];
    $pathLengthOverridesAreList = array_is_list($pathLengthOverrides);
    $pathNumbers = $resolvedPathCount > 0 ? range(1, $resolvedPathCount) : [];
    $pathLengths = collect($pathNumbers)
        ->map(function (int $pathNumber) use ($pathLengthOverrides, $pathLengthOverridesAreList, $resolvedLineLength): mixed {
            $overrideKey = $pathLengthOverridesAreList ? $pathNumber - 1 : $pathNumber;
            $overrideExists = array_key_exists($overrideKey, $pathLengthOverrides);
            $override = $overrideExists ? $pathLengthOverrides[$overrideKey] : null;

            if (! $overrideExists || $override === null || $override === '') {
                return $resolvedLineLength;
            }

            if (is_array($override)) {
                $override['length'] = data_get($override, 'length') ?: $resolvedLineLength;
            }

            return $override;
        })
        ->all();
    $endLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($endLength, $resolvedLineLength, '4rem');
    $endCapLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($endCapLength, $capLength ?? null, '1.75rem');
    $startLabelSide = match ($direction) {
        'left-right' => 'left',
        'right-left' => 'right',
        'top-bottom' => 'top',
        default => 'bottom',
    };
    $endLabelSide = match ($direction) {
        'left-right' => 'right',
        'right-left' => 'left',
        'top-bottom' => 'bottom',
        default => 'top',
    };
    $axisDelta = function (string $length) use ($direction): array {
        return match ($direction) {
            'top-bottom' => ['x' => '0rem', 'y' => 'calc(' . $length . ' * -1)'],
            'left-right' => ['x' => $length, 'y' => '0rem'],
            'right-left' => ['x' => 'calc(' . $length . ' * -1)', 'y' => '0rem'],
            default => ['x' => '0rem', 'y' => $length],
        };
    };
    $addAnchor = function (array $anchor, array $delta): array {
        return [
            'x' => $delta['x'] === '0rem' ? data_get($anchor, 'x', '0rem') : 'calc(' . data_get($anchor, 'x', '0rem') . ' + ' . $delta['x'] . ')',
            'y' => $delta['y'] === '0rem' ? data_get($anchor, 'y', '0rem') : 'calc(' . data_get($anchor, 'y', '0rem') . ' + ' . $delta['y'] . ')',
        ];
    };
    $normalizeLabel = function (mixed $label): ?array {
        if ($label === false) {
            return null;
        }

        if (blank($label) || $label === 'null') {
            return null;
        }

        if (is_array($label)) {
            return $label;
        }

        [$text, $side] = array_pad(explode('|', (string) $label, 2), 2, null);

        return [
            'text' => trim($text),
            'side' => filled($side) ? trim($side) : null,
        ];
    };
    $resolveTerminalLabel = function (mixed $label, array $default) use ($normalizeLabel): ?array {
        if ($label === false) {
            return null;
        }

        if ($label === null) {
            return $default;
        }

        $normalizedLabel = $normalizeLabel($label);

        if ($normalizedLabel === null) {
            return null;
        }

        return array_replace(
            $default,
            collect($normalizedLabel)
                ->reject(fn (mixed $value): bool => $value === null)
                ->all(),
        );
    };
    $normalizePathLength = function (mixed $pathLength) use ($normalizeLabel): array {
        if (! is_array($pathLength)) {
            return ['length' => $pathLength, 'labels' => true];
        }

        $length = data_get($pathLength, 'length', data_get($pathLength, 0));
        $labels = data_get($pathLength, 'labels', data_get($pathLength, 1, true));

        if (is_array($labels)) {
            $labels = collect($labels)
                ->take(2)
                ->map(fn (mixed $label): ?array => $normalizeLabel($label))
                ->all();
        }

        return ['length' => $length, 'labels' => $labels];
    };
    $segments = [];
    $counter = (int) $counterStart;
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $pathStartAnchor = $currentAnchor;
    $resolvedStartLabel = $resolveTerminalLabel($startLabel, [
        'text' => ['Path', 'start'],
        'side' => $startLabelSide,
        'offset' => '0.75rem',
        'badgeColor' => $resolvedColor,
    ]);
    $resolvedEndLabel = $resolveTerminalLabel($endLabel, [
        'text' => ['Path', 'end'],
        'side' => $endLabelSide,
        'offset' => '0.75rem',
        'badgeColor' => $resolvedColor,
    ]);

    if (filled($startLength)) {
        $nextAnchor = $addAnchor($currentAnchor, $axisDelta($startLength));
        $normalizedStartNodeLabels = is_array($startNodeLabels)
            ? collect(['left', 'right', 'top', 'bottom'])
            ->map(static function (string $side) use ($startNodeLabels, $normalizeLabel): ?array {
                if (! array_key_exists($side, $startNodeLabels) || blank($startNodeLabels[$side])) {
                    return null;
                }

                return array_replace([
                    'side' => $side,
                ], $normalizeLabel([
                    'text' => $startNodeLabels[$side],
                    'side' => $side,
                ]) ?? []);
            })
            ->filter()
            ->values()
            ->all()
            : [];
        $segments[] = [
            'component' => 'start',
            'segment' => [
                'id' => $id . '.start',
                'direction' => $direction,
                'length' => $startLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $nextAnchor,
                'nodeEnd' => $normalizedStartNodeLabels !== [] ? $normalizedStartNodeLabels : true,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'startLabel' => $resolvedStartLabel,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ];
        $currentAnchor = $nextAnchor;
    }

    foreach (collect($pathLengths)->values() as $pathIndex => $pathLengthEntry) {
        $normalizedPathLength = $normalizePathLength($pathLengthEntry);
        $pathLength = data_get($normalizedPathLength, 'length', '0rem');
        $nodeEnd = data_get($normalizedPathLength, 'labels', true);
        $nextAnchor = $addAnchor($currentAnchor, $axisDelta($pathLength));
        $segments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.path.' . ($pathIndex + 1),
                'direction' => $direction,
                'length' => $pathLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $nextAnchor,
                'nodeStart' => false,
                'nodeEnd' => $nodeEnd,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ];
        $currentAnchor = $nextAnchor;
    }

    if (filled($endLength)) {
        $nextAnchor = $addAnchor($currentAnchor, $axisDelta($endLength));
        $segments[] = [
            'component' => 'end',
            'segment' => [
                'id' => $id . '.end',
                'direction' => $direction,
                'length' => $endLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $nextAnchor,
                'nodeStart' => false,
                'capLength' => $endCapLength,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $resolvedColor,
                'endLabel' => $resolvedEndLabel,
                'color' => $resolvedColor,
                'zIndex' => $zIndex,
                'dev' => $resolvedDev,
            ],
        ];
        $currentAnchor = $nextAnchor;
    }

    $pathEndAnchor = $currentAnchor;
    $pathBoxPadding = '0.75rem';
    $isHorizontalPath = in_array($direction, ['left-right', 'right-left'], true);
    $pathBoxX = match ($direction) {
        'right-left' => data_get($pathEndAnchor, 'x', '0rem'),
        'left-right' => data_get($pathStartAnchor, 'x', '0rem'),
        default => 'calc(' . data_get($pathStartAnchor, 'x', '0rem') . ' - var(--tw-graph-protocol-node-half))',
    };
    $pathBoxY = match ($direction) {
        'top-bottom' => data_get($pathEndAnchor, 'y', '0rem'),
        'left-right', 'right-left' => 'calc(' . data_get($pathStartAnchor, 'y', '0rem') . ' - var(--tw-graph-protocol-node-half))',
        default => data_get($pathStartAnchor, 'y', '0rem'),
    };
    $pathBoxWidth = $isHorizontalPath
        ? 'calc(' . data_get($pathEndAnchor, 'x', '0rem') . ' - ' . data_get($pathStartAnchor, 'x', '0rem') . ')'
        : 'var(--tw-graph-protocol-node-size)';
    if ($direction === 'right-left') {
        $pathBoxWidth = 'calc(' . data_get($pathStartAnchor, 'x', '0rem') . ' - ' . data_get($pathEndAnchor, 'x', '0rem') . ')';
    }
    $pathBoxHeight = $isHorizontalPath
        ? 'var(--tw-graph-protocol-node-size)'
        : 'calc(' . data_get($pathEndAnchor, 'y', '0rem') . ' - ' . data_get($pathStartAnchor, 'y', '0rem') . ')';
    if ($direction === 'top-bottom') {
        $pathBoxHeight = 'calc(' . data_get($pathStartAnchor, 'y', '0rem') . ' - ' . data_get($pathEndAnchor, 'y', '0rem') . ')';
    }
@endphp

@if ($showDevBox)
    <x-translation-workbench::ui.tw-graph.dev-box
        :id="$id . '.dev-box'"
        :x="'calc(' . $pathBoxX . ' - ' . $pathBoxPadding . ')'"
        :y="'calc(' . $pathBoxY . ' - ' . $pathBoxPadding . ')'"
        :width="'calc(' . $pathBoxWidth . ' + (' . $pathBoxPadding . ' * 2))'"
        :height="'calc(' . $pathBoxHeight . ' + (' . $pathBoxPadding . ' * 2))'"
        color="amber"
        :label="$id"
        :dev="$dev"
    />
@endif

@if ($showLayoutSpacer)
    <span
        aria-hidden="true"
        class="block pointer-events-none invisible"
        style="
            width: calc({{ $pathBoxWidth }} + ({{ $pathBoxPadding }} * 2));
            height: calc({{ $pathBoxHeight }} + ({{ $pathBoxPadding }} * 2));
        "
    ></span>
@endif

@foreach ($segments as $segment)
    @if ($segment['component'] === 'start')
        <x-translation-workbench::ui.tw-graph.segments.start :segment="$segment['segment']" />
    @elseif ($segment['component'] === 'end')
        <x-translation-workbench::ui.tw-graph.segments.end :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
