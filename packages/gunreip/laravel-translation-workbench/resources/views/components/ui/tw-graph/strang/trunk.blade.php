{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/trunk.blade.php --}}
{{--
    Strang: trunk

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.trunk
        direction="bottom-top"
        :path-count="10"
        :path-lengths="[1 => '3rem', 2 => null, 3 => '5rem']"
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
    'color' => null,
    'startLength' => null,
    'pathCount' => null,
    'pathLengths' => [],
    'nodeLabels' => [],
    'defaultPathSegments' => 10,
    'endLength' => null,
    'endCapLength' => null,
    'startLabel' => null,
    'endLabel' => null,
    'startNodeLabels' => [],
    'startLabelSpace' => '3rem',
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
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'zinc');
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedPathCount = max(0, (int) ($pathCount ?? $defaultPathSegments));
    $resolvedDev = $devMode ?? $dev;
    $resolvedStartLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($startLength, $resolvedLineLength, '4rem');
    $resolvedEndLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($endLength, $resolvedLineLength, '4rem');

    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
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
    $lengthOf = function (mixed $entry) use ($resolvedLineLength): string {
        if (is_array($entry)) {
            return (string) (data_get($entry, 'length', data_get($entry, 0)) ?: $resolvedLineLength);
        }

        return filled($entry) ? (string) $entry : $resolvedLineLength;
    };
    $normalizeNodeLabels = function (mixed $labels): mixed {
        if (! is_array($labels)) {
            return $labels;
        }

        $left = data_get($labels, 'left', data_get($labels, 0));
        $right = data_get($labels, 'right', data_get($labels, 1));

        return [
            filled($right) ? ['text' => $right, 'side' => 'right'] : null,
            filled($left) ? ['text' => $left, 'side' => 'left'] : null,
        ];
    };
    $pathLengthWithLabels = function (mixed $entry, mixed $labels) use ($resolvedLineLength, $normalizeNodeLabels): mixed {
        if ($labels === null || $labels === false || $labels === '') {
            return $entry;
        }

        if (! is_array($entry)) {
            return [
                'length' => filled($entry) ? $entry : $resolvedLineLength,
                'labels' => $normalizeNodeLabels($labels),
            ];
        }

        if (array_key_exists('length', $entry) || array_key_exists('labels', $entry)) {
            $entry['length'] = data_get($entry, 'length') ?: $resolvedLineLength;
            $entry['labels'] = $normalizeNodeLabels($labels);

            return $entry;
        }

        $entry[0] = data_get($entry, 0) ?: $resolvedLineLength;
        $entry[1] = $normalizeNodeLabels($labels);

        return $entry;
    };

    $pathLengthOverrides = is_array($pathLengths) ? $pathLengths : [];
    $pathLengthOverridesAreList = array_is_list($pathLengthOverrides);
    $nodeLabelOverrides = is_array($nodeLabels) ? $nodeLabels : [];
    $nodeLabelOverridesAreList = array_is_list($nodeLabelOverrides);
    $pathNumbers = $resolvedPathCount > 0 ? range(1, $resolvedPathCount) : [];
    $resolvedPathLengthEntries = collect($pathNumbers)
        ->mapWithKeys(function (int $pathNumber) use ($pathLengthOverrides, $pathLengthOverridesAreList, $nodeLabelOverrides, $nodeLabelOverridesAreList, $resolvedLineLength, $pathLengthWithLabels): array {
            $lengthKey = $pathLengthOverridesAreList ? $pathNumber - 1 : $pathNumber;
            $labelKey = $nodeLabelOverridesAreList ? $pathNumber - 1 : $pathNumber;
            $lengthExists = array_key_exists($lengthKey, $pathLengthOverrides);
            $labelExists = array_key_exists($labelKey, $nodeLabelOverrides);
            $lengthEntry = $lengthExists ? $pathLengthOverrides[$lengthKey] : $resolvedLineLength;

            return [
                $pathNumber => $labelExists
                    ? $pathLengthWithLabels($lengthEntry, $nodeLabelOverrides[$labelKey])
                    : $lengthEntry,
            ];
        })
        ->all();
    $resolvedPathLengths = collect($pathNumbers)
        ->map(fn (int $pathNumber): string => $lengthOf($resolvedPathLengthEntries[$pathNumber] ?? $resolvedLineLength))
        ->all();

    $pathStartAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::forgetGraph($resolvedGraphId);
    \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::forgetGraph($resolvedGraphId);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.trunk.start', $pathStartAnchor);

    $pathEndAnchor = collect([
        $resolvedStartLength,
        ...$resolvedPathLengths,
        $resolvedEndLength,
    ])->reduce(
        fn (array $anchor, string $length): array => $addAnchor($anchor, $axisDelta($length)),
        $pathStartAnchor,
    );
    $nodeAnchor = $addAnchor($pathStartAnchor, $axisDelta($resolvedStartLength));
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.trunk.node.1', $nodeAnchor);

    foreach ($resolvedPathLengths as $nodeIndex => $pathLength) {
        $pathNumber = $nodeIndex + 1;
        $pathAnchorStart = $nodeAnchor;
        $nodeAnchor = $addAnchor($nodeAnchor, $axisDelta($pathLength));
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put(
            $resolvedGraphId,
            'strang.trunk.node.' . ($pathNumber + 1),
            $nodeAnchor,
        );
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.trunk.path.' . $pathNumber . '.start', $pathAnchorStart);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.trunk.path.' . $pathNumber . '.end', $nodeAnchor);
    }

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.trunk.end', $pathEndAnchor);

    $pathBoxPadding = '1rem';
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
@endphp

<x-translation-workbench::ui.tw-graph.dev-box
    :id="$id . '.dev-box'"
    :x="'calc(' . $pathBoxX . ' - ' . $pathBoxPadding . ' - ' . $pathBoxStartPadding['x'] . ')'"
    :y="'calc(' . $pathBoxY . ' - ' . $pathBoxPadding . ' - ' . $pathBoxStartPadding['y'] . ')'"
    :width="'calc(' . $pathBoxWidth . ' + (' . $pathBoxPadding . ' * 2) + ' . $pathBoxStartPadding['width'] . ')'"
    :height="'calc(' . $pathBoxHeight . ' + (' . $pathBoxPadding . ' * 2) + ' . $pathBoxStartPadding['height'] . ')'"
    :color="$resolvedColor"
    :label="$id"
    :dev="$resolvedDev"
    metrics-scope="canvas"
    metrics-side="center"
/>

<x-translation-workbench::ui.tw-graph.paths.trunk
    :id="$id . '.paths.trunk'"
    :direction="$direction"
    :anchor-start="$anchorStart"
    :start-length="$startLength"
    :path-count="$pathCount"
    :path-lengths="$resolvedPathLengthEntries"
    :default-path-segments="$defaultPathSegments"
    :end-length="$endLength"
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
