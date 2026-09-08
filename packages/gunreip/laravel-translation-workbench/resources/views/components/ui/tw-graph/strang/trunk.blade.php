{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/trunk.blade.php --}}
{{--
    Strang: trunk

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.trunk
        direction="bottom-top"
        :stem-count="10"
        :stem-lengths="[1 => '3rem', 2 => null, 3 => '5rem']"
        start-label-space="3rem"
        start-label="Trunk start"
        end-label="Trunk end"
        :start-node-labels="['left' => 'Source', 'right' => 'Target']"
        :node-labels="[2 => ['left' => 'Left label', 'right' => 'Right label']]"
    />

    Component chain:
    tw-graph -> strang.trunk -> paths.trunk -> segments.* -> primitives.*

    Rule:
    Authoring should enter at strang.*, not paths.*, so the graph hierarchy
    stays explicit when merge and branch strangs are added later.
--}}

@aware([
    'graphId' => null,
    'color' => null,
    'dev' => false,
    'lineLength' => null,
    'stemLength' => null,
    'capLength' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'id' => null,
    'componentCounter' => 1,
    'direction' => 'bottom-top',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'startLength' => null,
    'stemLength' => null,
    'stemCount' => null,
    'stemLengths' => [],
    'nodeLabels' => [],
    'defaultPathSegments' => 10,
    'endLength' => null,
    'endCapLength' => null,
    'startLabel' => null,
    'endLabel' => null,
    'startNodeLabels' => [],
    'startLabelSpace' => '3rem',
    'startShiftEnabled' => null,
    'startShiftLength' => null,
    'zIndex' => 20,
    'counterStart' => 1,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.strang.trunk.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $stemLength ?? null,
        null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength),
    );
    $resolvedDefaultPathLength = in_array($direction, ['bottom-top', 'top-bottom'], true)
        ? $resolvedStemLength
        : $resolvedLineLength;
    $resolvedStemCount = max(0, (int) ($stemCount ?? $defaultPathSegments));
    $resolvedDev = $devMode ?? $dev;
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $resolvedStartLengthBase = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedDefaultPathLength, '4rem');
    $resolvedStartShiftEnabled = $startShiftEnabled === null
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphBool('trunk_start_shift_enabled', false)
        : (filter_var($startShiftEnabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
    $resolvedStartShiftLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $startShiftLength,
        null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('trunk_start_shift_length', '4rem'),
    );
    $resolvedStartLength = $resolvedStartLengthBase;
    $resolvedStartShiftSegmentLength = $resolvedStartShiftEnabled ? $resolvedStartShiftLength : '0rem';
    $resolvedEndLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($endLength, $resolvedDefaultPathLength, '4rem');

    $axisDelta = function (string $length) use ($direction): array {
        return match ($direction) {
            'top-bottom' => ['x' => '0rem', 'y' => 'calc(' . $length . ' * -1)'],
            'left-right' => ['x' => $length, 'y' => '0rem'],
            'right-left' => ['x' => 'calc(' . $length . ' * -1)', 'y' => '0rem'],
            default => ['x' => '0rem', 'y' => $length],
        };
    };
    $inverseAxisDelta = function (string $length) use ($direction): array {
        return match ($direction) {
            'top-bottom' => ['x' => '0rem', 'y' => $length],
            'left-right' => ['x' => 'calc(' . $length . ' * -1)', 'y' => '0rem'],
            'right-left' => ['x' => $length, 'y' => '0rem'],
            default => ['x' => '0rem', 'y' => 'calc(' . $length . ' * -1)'],
        };
    };
    $addAnchor = function (array $anchor, array $delta): array {
        return [
            'x' => $delta['x'] === '0rem' ? data_get($anchor, 'x', '0rem') : 'calc(' . data_get($anchor, 'x', '0rem') . ' + ' . $delta['x'] . ')',
            'y' => $delta['y'] === '0rem' ? data_get($anchor, 'y', '0rem') : 'calc(' . data_get($anchor, 'y', '0rem') . ' + ' . $delta['y'] . ')',
        ];
    };
    $lengthOf = function (mixed $entry) use ($resolvedDefaultPathLength): string {
        if (is_array($entry)) {
            return (string) (data_get($entry, 'length', data_get($entry, 0)) ?: $resolvedDefaultPathLength);
        }

        return filled($entry) ? (string) $entry : $resolvedDefaultPathLength;
    };
    $normalizeNodeLabels = function (mixed $labels) use ($resolvedColor): mixed {
        if (! is_array($labels)) {
            return $labels;
        }

        $normalizeLabelForSide = static fn (mixed $label, string $side): ?array => \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, $side, $resolvedColor);
        $left = data_get($labels, 'left', data_get($labels, 0));
        $right = data_get($labels, 'right', data_get($labels, 1));

        return [
            $normalizeLabelForSide($right, 'right'),
            $normalizeLabelForSide($left, 'left'),
        ];
    };
    $stemLengthWithLabels = function (mixed $entry, mixed $labels) use ($resolvedDefaultPathLength, $normalizeNodeLabels): mixed {
        if ($labels === null || $labels === false || $labels === '') {
            return $entry;
        }

        if (! is_array($entry)) {
            return [
                'length' => filled($entry) ? $entry : $resolvedDefaultPathLength,
                'labels' => $normalizeNodeLabels($labels),
            ];
        }

        if (array_key_exists('length', $entry) || array_key_exists('labels', $entry)) {
            $entry['length'] = data_get($entry, 'length') ?: $resolvedDefaultPathLength;
            $entry['labels'] = $normalizeNodeLabels($labels);

            return $entry;
        }

        $entry[0] = data_get($entry, 0) ?: $resolvedDefaultPathLength;
        $entry[1] = $normalizeNodeLabels($labels);

        return $entry;
    };

    $stemLengthOverrides = is_array($stemLengths) ? $stemLengths : [];
    $stemLengthOverridesAreList = array_is_list($stemLengthOverrides);
    $nodeLabelOverrides = is_array($nodeLabels) ? $nodeLabels : [];
    $nodeLabelOverridesAreList = array_is_list($nodeLabelOverrides);
    $stemNumbers = $resolvedStemCount > 0 ? range(1, $resolvedStemCount) : [];
    $resolvedStemLengthEntries = collect($stemNumbers)
        ->mapWithKeys(function (int $stemNumber) use ($stemLengthOverrides, $stemLengthOverridesAreList, $nodeLabelOverrides, $nodeLabelOverridesAreList, $resolvedDefaultPathLength, $stemLengthWithLabels): array {
            $lengthKey = $stemLengthOverridesAreList ? $stemNumber - 1 : $stemNumber;
            $labelKey = $nodeLabelOverridesAreList ? $stemNumber - 1 : $stemNumber;
            $lengthExists = array_key_exists($lengthKey, $stemLengthOverrides);
            $labelExists = array_key_exists($labelKey, $nodeLabelOverrides);
            $lengthEntry = $lengthExists ? $stemLengthOverrides[$lengthKey] : $resolvedDefaultPathLength;

            return [
                $stemNumber => $labelExists
                    ? $stemLengthWithLabels($lengthEntry, $nodeLabelOverrides[$labelKey])
                    : $lengthEntry,
            ];
        })
        ->all();
    $resolvedStemLengths = collect($stemNumbers)
        ->map(fn (int $stemNumber): string => $lengthOf($resolvedStemLengthEntries[$stemNumber] ?? $resolvedDefaultPathLength))
        ->all();

    $baseStartAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $pathStartAnchor = $resolvedStartShiftEnabled
        ? $addAnchor($baseStartAnchor, $inverseAxisDelta($resolvedStartShiftLength))
        : $baseStartAnchor;
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::forgetGraph($resolvedGraphId);
    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::forgetGraph($resolvedGraphId);
    $putAnchor = static function (array|string $keys, array $anchor) use ($resolvedGraphId): void {
        foreach ((array) $keys as $key) {
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, (string) $key, $anchor);
        }
    };
    $canonicalTrunkPrefix = 'strang.trunk.center.' . $resolvedComponentCounter;

    $putAnchor([
        'strang.trunk.start',
        $canonicalTrunkPrefix . '.start.anchorStart',
    ], $pathStartAnchor);

    $pathEndAnchor = collect([
        $resolvedStartLength,
        $resolvedStartShiftSegmentLength,
        ...$resolvedStemLengths,
        $resolvedEndLength,
    ])->reduce(
        fn (array $anchor, string $length): array => $addAnchor($anchor, $axisDelta($length)),
        $pathStartAnchor,
    );
    $nodeAnchor = $addAnchor($pathStartAnchor, $axisDelta($resolvedStartLength));
    $putAnchor([
        'strang.trunk.node.1',
        $canonicalTrunkPrefix . '.start',
        $canonicalTrunkPrefix . '.start.anchorNode-end',
        $canonicalTrunkPrefix . '.start.end',
    ], $nodeAnchor);

    if ($resolvedStartShiftEnabled && $resolvedStartShiftSegmentLength !== '0rem') {
        $shiftAnchorStart = $nodeAnchor;
        $nodeAnchor = $addAnchor($nodeAnchor, $axisDelta($resolvedStartShiftSegmentLength));
        $putAnchor([
            $canonicalTrunkPrefix . '.start-shift',
            $canonicalTrunkPrefix . '.start-shift.anchorStart',
        ], $shiftAnchorStart);
        $putAnchor([
            $canonicalTrunkPrefix . '.start-shift.anchorEnd',
            $canonicalTrunkPrefix . '.start-shift.end',
        ], $nodeAnchor);
    }

    foreach ($resolvedStemLengths as $nodeIndex => $stemLength) {
        $stemNumber = $nodeIndex + 1;
        $pathAnchorStart = $nodeAnchor;
        $nodeAnchor = $addAnchor($nodeAnchor, $axisDelta($stemLength));
        $putAnchor([
            'strang.trunk.node.' . ($stemNumber + 1),
            'strang.trunk.path.' . $stemNumber . '.end',
            $canonicalTrunkPrefix . '.stem-' . $stemNumber,
            $canonicalTrunkPrefix . '.stem-' . $stemNumber . '.anchorNode-end',
            $canonicalTrunkPrefix . '.stem-' . $stemNumber . '.end',
        ], $nodeAnchor);
        $putAnchor([
            'strang.trunk.path.' . $stemNumber . '.start',
            $canonicalTrunkPrefix . '.stem-' . $stemNumber . '.start',
        ], $pathAnchorStart);
    }

    $putAnchor([
        'strang.trunk.end',
        $canonicalTrunkPrefix . '.end',
        $canonicalTrunkPrefix . '.end.anchorNode-end',
    ], $pathEndAnchor);

    $pathBoxPadding = '1rem';
    $pathBoxX = data_get($pathStartAnchor, 'x', '0rem');
    $pathBoxY = data_get($pathStartAnchor, 'y', '0rem');
    $pathBoxWidth = 'var(--tw-graph-protocol-node-size)';
    $pathBoxHeight = '0rem';
    $resolvedStartLabelSpace = filled($startLabelSpace) ? (string) $startLabelSpace : '0rem';
    $pathBoxStartPadding = match ($direction) {
        'top-bottom' => ['x' => '0rem', 'y' => '0rem', 'width' => '0rem', 'height' => $resolvedStartLabelSpace],
        'left-right' => ['x' => $resolvedStartLabelSpace, 'y' => '0rem', 'width' => $resolvedStartLabelSpace, 'height' => '0rem'],
        'right-left' => ['x' => '0rem', 'y' => '0rem', 'width' => $resolvedStartLabelSpace, 'height' => '0rem'],
        default => ['x' => '0rem', 'y' => $resolvedStartLabelSpace, 'width' => '0rem', 'height' => $resolvedStartLabelSpace],
    };
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
    $devBoxX = 'calc(' . $pathBoxX . ' - ' . $pathBoxPadding . ' - ' . $pathBoxStartPadding['x'] . ')';
    $devBoxY = 'calc(' . $pathBoxY . ' - ' . $pathBoxPadding . ' - ' . $pathBoxStartPadding['y'] . ')';
    $devBoxWidth = 'calc(' . $pathBoxWidth . ' + (' . $pathBoxPadding . ' * 2) + ' . $pathBoxStartPadding['width'] . ')';
    $devBoxHeight = 'calc(' . $pathBoxHeight . ' + (' . $pathBoxPadding . ' * 2) + ' . $pathBoxStartPadding['height'] . ')';
@endphp

<x-translation-workbench::ui.tw-graph.dev-box
    :id="$id . '.dev-box'"
    :x="$devBoxX"
    :y="$devBoxY"
    :width="$devBoxWidth"
    :height="$devBoxHeight"
    :color="$resolvedColor"
    :label="$id"
    :dev="$resolvedDev"
    metrics-scope="canvas"
    metrics-side="center"
/>

    <x-translation-workbench::ui.tw-graph.paths.trunk
        :id="$id . '.paths.trunk'"
        :direction="$direction"
    :anchor-start="$pathStartAnchor"
    :line-length="$resolvedDefaultPathLength"
    :start-length="$resolvedStartLength"
    :start-shift-length="$resolvedStartShiftSegmentLength"
    :path-count="$resolvedStemCount"
    :path-lengths="$resolvedStemLengthEntries"
    :default-path-segments="$defaultPathSegments"
    :end-length="$resolvedEndLength"
    :end-cap-length="$endCapLength"
    :start-label="$startLabel"
    :end-label="$endLabel"
    :start-node-labels="$startNodeLabels"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :counter-start="$counterStart"
    :dev-mode="$resolvedDev"
    :show-dev-box="false"
    :show-layout-spacer="false"
/>
