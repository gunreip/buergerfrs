{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/branch-right.blade.php --}}
{{--
    Strang: branch-right

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.branch-right
        attach-to="strang.trunk.node.5"
        bridge-length="4rem"
        :node-labels="[3 => ['top' => 'Branch turn']]"
        :continuation-stem="[1 => ['3rem']]"
        :branch-extension="[
            'continuation.1' => [
                1 => ['bridgeLength' => '8rem', 'stemHeight' => '3rem'],
            ],
        ]"
        :branch-return="[1 => ['attachTo' => 'continuation.3', 'bridgeLength' => '8rem']]"
    />

    Component chain:
    tw-graph -> strang.branch-right -> paths.branch -> segments.* -> primitives.*

    Rule:
    Authoring enters through strang.*. This component owns the right branch
    bounds and delegates only path rendering to paths.branch.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => '4rem',
    'arcSize' => '2.75rem',
    'bridgeLength' => null,
    'stemHeight' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'bridgeLength' => null,
    'stemHeight' => null,
    'nodeLabels' => [],
    'continuationStem' => [],
    'branchExtension' => [],
    'branchReturn' => [],
    'counterStart' => 1,
    'zIndex' => 10,
    'devMode' => null,
])

@php
    $resolvedGraphId = filled($graphId ?? null) ? (string) $graphId : 'tw-graph';
    $resolvedComponentCounter = max(1, (int) $componentCounter);
    $id = filled($id)
        ? (string) $id
        : $resolvedGraphId . '.strang.branch-right.' . $resolvedComponentCounter;
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $defaultColor ?? null, 'rose');
    $resolvedDev = $devMode ?? $dev;
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $localBridgeLength = $attributes->get('bridge-length');
    $localStemHeight = $attributes->get('stem-height');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localBridgeLength,
        $bridgeLength ?? null,
        $resolvedLineLength,
    );
    $resolvedStemHeight = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localStemHeight,
        $stemHeight ?? null,
        $resolvedLineLength,
    );

    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $subtract = fn (string $value, string $delta): string => $add($value, $neg($delta));
    $attachTarget = filled($attachTo)
        ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, (string) $attachTo)
        : null;
    $missingAttachTarget = filled($attachTo) && $attachTarget === null;
    $anchor = $attachTarget ?: [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];

    $node1 = [
        'x' => $add($anchor['x'], $resolvedArcSize),
        'y' => $add($anchor['y'], $resolvedArcSize),
        'source' => $id . '.paths.branch.arc.in',
        'sourceType' => 'arc',
        'sourceAnchor' => 'end',
        'direction' => 'west-north',
    ];
    $node2 = [
        'x' => $add($node1['x'], $resolvedBridgeLength),
        'y' => $node1['y'],
        'source' => $id . '.paths.branch.bridge',
        'sourceType' => 'bridge',
        'sourceAnchor' => 'end',
        'direction' => 'left-right',
    ];
    $node3 = [
        'x' => $add($node2['x'], $resolvedArcSize),
        'y' => $add($node2['y'], $resolvedArcSize),
        'source' => $id . '.paths.branch.arc.out',
        'sourceType' => 'arc',
        'sourceAnchor' => 'end',
        'direction' => 'south-east',
    ];
    $continuationEntries = is_array($continuationStem) ? $continuationStem : [];
    if ($continuationEntries === [] && filled($localStemHeight)) {
        $continuationEntries = [1 => [$resolvedStemHeight]];
    }
    $continuationEntriesAreList = array_is_list($continuationEntries);
    $continuationAnchors = [];
    $continuationEnd = $node3;

    foreach ($continuationEntries as $continuationIndex => $continuationEntry) {
        $continuationNumber = $continuationEntriesAreList ? ((int) $continuationIndex + 1) : (int) $continuationIndex;
        $length = is_array($continuationEntry)
            ? (string) (data_get($continuationEntry, 'length', data_get($continuationEntry, 0)) ?: $resolvedLineLength)
            : (filled($continuationEntry) ? (string) $continuationEntry : $resolvedLineLength);

        $continuationEnd = [
            'x' => $continuationEnd['x'],
            'y' => $add($continuationEnd['y'], $length),
            'source' => $id . '.paths.branch.continuation.' . $continuationNumber,
            'sourceType' => 'stem',
            'sourceAnchor' => 'end',
            'direction' => 'bottom-top',
        ];
        $continuationAnchors[$continuationNumber] = $continuationEnd;
    }

    $rawBranchExtensionEntries = is_array($branchExtension)
        ? $branchExtension
        : (is_numeric($branchExtension) ? array_fill(1, max(0, (int) $branchExtension), []) : []);
    $branchExtensionGroups = [];

    if (array_is_list($rawBranchExtensionEntries)) {
        $branchExtensionGroups['node.2'] = $rawBranchExtensionEntries;
    } else {
        foreach ($rawBranchExtensionEntries as $extensionGroupKey => $extensionGroupEntries) {
            if (is_array($extensionGroupEntries) && (array_is_list($extensionGroupEntries) || ! array_key_exists('bridgeLength', $extensionGroupEntries))) {
                $branchExtensionGroups[(string) $extensionGroupKey] = $extensionGroupEntries;
            } else {
                $branchExtensionGroups['node.2'][(int) $extensionGroupKey] = $extensionGroupEntries;
            }
        }
    }

    $branchExtensionBoundsPoints = [];
    $branchExtensionConfigs = [];
    $branchExtensionAliases = [];
    $fallbackWarnings = [];
    $branchExtensionCounterStart = $counterStart + 3 + count($continuationAnchors);
    $branchExtensionRenderIndex = 0;
    $resolveBranchRightAnchor = function (?string $attachTo, array $fallback) use (
        $resolvedGraphId,
        $node1,
        $node2,
        $node3,
        &$branchExtensionConfigs,
        &$branchExtensionAliases,
        $continuationAnchors,
    ): array {
        $resolved = fn (array $anchor): array => [
            'anchor' => $anchor,
            'fallbackUsed' => false,
            'requested' => $attachTo,
            'resolved' => data_get($anchor, 'source', 'resolved anchor'),
        ];
        $fallbackResolved = fn (): array => [
            'anchor' => $fallback,
            'fallbackUsed' => filled($attachTo),
            'requested' => $attachTo,
            'resolved' => data_get($fallback, 'source', 'fallback anchor'),
        ];

        if (blank($attachTo)) {
            return $fallbackResolved();
        }

        if ($attachTo === 'node.1') {
            return $resolved($node1);
        }

        if ($attachTo === 'node.2') {
            return $resolved($node2);
        }

        if ($attachTo === 'node.3') {
            return $resolved($node3);
        }

        if (str_starts_with($attachTo, 'continuation.')) {
            $continuationNumber = (int) substr($attachTo, strlen('continuation.'));

            return isset($continuationAnchors[$continuationNumber])
                ? $resolved($continuationAnchors[$continuationNumber])
                : $fallbackResolved();
        }

        if (preg_match('/^extension\.(.+?)\.(bridge\.end|arc\.end|stem\.end|end)$/', $attachTo, $matches) === 1) {
            $extensionNumber = (string) $matches[1];
            $extensionAnchorName = $matches[2];
            $extensionNumber = $branchExtensionAliases[$extensionNumber] ?? $extensionNumber;
            $extensionConfig = $branchExtensionConfigs[$extensionNumber] ?? null;

            if ($extensionConfig !== null) {
                $extensionAnchor = match ($extensionAnchorName) {
                    'bridge.end' => $extensionConfig['bridgeEnd'],
                    'arc.end' => $extensionConfig['arcEnd'],
                    'stem.end', 'end' => $extensionConfig['end'],
                    default => $fallback,
                };

                return $extensionAnchor === $fallback
                    ? $fallbackResolved()
                    : $resolved($extensionAnchor);
            }
        }

        if (str_starts_with($attachTo, 'strang.')) {
            $anchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $attachTo);

            return $anchor !== null
                ? $resolved($anchor)
                : $fallbackResolved();
        }

        return $fallbackResolved();
    };

    foreach ($branchExtensionGroups as $extensionGroupAttachTo => $extensionGroupEntries) {
        $extensionGroupEntries = is_array($extensionGroupEntries) ? $extensionGroupEntries : [];
        $extensionGroupEntriesAreList = array_is_list($extensionGroupEntries);
        $branchExtensionAnchorResult = $resolveBranchRightAnchor((string) $extensionGroupAttachTo, $node2);
        $branchExtensionAnchor = $branchExtensionAnchorResult['anchor'];
        if ($branchExtensionAnchorResult['fallbackUsed']) {
            $fallbackWarnings[] = [
                ...$branchExtensionAnchorResult,
                'component' => $id . '.extension-group.' . $extensionGroupAttachTo,
            ];
        }

        foreach ($extensionGroupEntries as $extensionIndex => $extensionEntry) {
            $localExtensionNumber = $extensionGroupEntriesAreList ? ((int) $extensionIndex + 1) : (int) $extensionIndex;
            $legacyExtensionKey = (string) $extensionGroupAttachTo . '.' . $localExtensionNumber;
            $extensionAttachTo = is_array($extensionEntry)
                ? data_get($extensionEntry, 'attachTo', data_get($extensionEntry, 'anchor'))
                : null;
            $extensionAnchorResult = $resolveBranchRightAnchor(
                is_string($extensionAttachTo) ? $extensionAttachTo : null,
                $branchExtensionAnchor,
            );
            $extensionAnchor = $extensionAnchorResult['anchor'];
            if ($extensionAnchorResult['fallbackUsed']) {
                $fallbackWarnings[] = [
                    ...$extensionAnchorResult,
                    'component' => $id . '.extension.' . ($branchExtensionRenderIndex + 1),
                ];
            }

            $extensionBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($extensionEntry) ? data_get($extensionEntry, 'bridgeLength', data_get($extensionEntry, 0)) : null,
                $resolvedBridgeLength,
                $resolvedLineLength,
            );
            $extensionStemHeight = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($extensionEntry) ? data_get($extensionEntry, 'stemHeight', data_get($extensionEntry, 'verticalLength', data_get($extensionEntry, 1))) : null,
                $resolvedStemHeight,
                $resolvedLineLength,
            );
            $extensionColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($extensionEntry) ? data_get($extensionEntry, 'color') : null,
                $resolvedColor,
                'pink',
            );
            $extensionStartsFromStem = data_get($extensionAnchor, 'sourceType') === 'stem';
            $extensionBridgeStart = $extensionAnchor;

            if ($extensionStartsFromStem) {
                $extensionBridgeStart = [
                    'x' => $add($extensionAnchor['x'], $resolvedArcSize),
                    'y' => $add($extensionAnchor['y'], $resolvedArcSize),
                    'source' => $id . '.extension.' . ($branchExtensionRenderIndex + 1) . '.arc.in',
                    'sourceType' => 'arc',
                    'sourceAnchor' => 'end',
                    'direction' => 'west-north',
                ];
            }

            $extensionBridgeEnd = [
                'x' => $add($extensionBridgeStart['x'], $extensionBridgeLength),
                'y' => $extensionBridgeStart['y'],
                'source' => $id . '.extension.' . ($branchExtensionRenderIndex + 1) . '.bridge',
                'sourceType' => 'bridge',
                'sourceAnchor' => 'end',
                'direction' => 'left-right',
            ];
            $extensionArcEnd = [
                'x' => $add($extensionBridgeEnd['x'], $resolvedArcSize),
                'y' => $add($extensionBridgeEnd['y'], $resolvedArcSize),
                'source' => $id . '.extension.' . ($branchExtensionRenderIndex + 1) . '.arc',
                'sourceType' => 'arc',
                'sourceAnchor' => 'end',
                'direction' => 'south-east',
            ];
            $extensionVerticalEnd = [
                'x' => $extensionArcEnd['x'],
                'y' => $add($extensionArcEnd['y'], $extensionStemHeight),
                'source' => $id . '.extension.' . ($branchExtensionRenderIndex + 1) . '.stem',
                'sourceType' => 'stem',
                'sourceAnchor' => 'end',
                'direction' => 'bottom-top',
            ];

            array_push($branchExtensionBoundsPoints, $extensionAnchor, $extensionBridgeStart, $extensionBridgeEnd, $extensionArcEnd, $extensionVerticalEnd);
            $branchExtensionRenderIndex++;
            $extensionKey = (string) $branchExtensionRenderIndex;
            $branchExtensionAliases[$legacyExtensionKey] = $extensionKey;
            $branchExtensionConfigs[$extensionKey] = [
                'anchor' => $extensionAnchor,
                'bridgeLength' => $extensionBridgeLength,
                'stemHeight' => $extensionStemHeight,
                'color' => $extensionColor,
                'bridgeEnd' => $extensionBridgeEnd,
                'arcEnd' => $extensionArcEnd,
                'end' => $extensionVerticalEnd,
                'counterStart' => $branchExtensionCounterStart,
                'nodeCount' => $extensionStartsFromStem ? 4 : 3,
                'renderIndex' => $branchExtensionRenderIndex,
            ];
            $branchExtensionAnchor = $extensionBridgeEnd;
            $branchExtensionCounterStart += $extensionStartsFromStem ? 4 : 3;
        }
    }

    $branchReturnEntries = is_array($branchReturn) ? $branchReturn : [];
    $branchReturnBoundsPoints = [];
    $branchReturnConfigs = [];
    $branchReturnCounterStart = $branchExtensionCounterStart;

    foreach ($branchReturnEntries as $returnIndex => $returnEntry) {
        $returnAttachTo = is_array($returnEntry)
            ? data_get($returnEntry, 'attachTo', data_get($returnEntry, 'anchor'))
            : null;
        $returnFallbackIndex = filled($returnAttachTo)
            ? ((int) $returnIndex + 1)
            : (int) $returnIndex;
        $returnAnchorResult = $resolveBranchRightAnchor(
            is_string($returnAttachTo) ? $returnAttachTo : null,
            $continuationAnchors[$returnFallbackIndex] ?? $continuationEnd,
        );
        $returnAnchor = $returnAnchorResult['anchor'];
        if ($returnAnchorResult['fallbackUsed']) {
            $fallbackWarnings[] = [
                ...$returnAnchorResult,
                'component' => $id . '.branch-return.' . $returnIndex,
            ];
        }

        $returnBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($returnEntry) ? data_get($returnEntry, 'bridgeLength') : null,
            $resolvedBridgeLength,
            $resolvedLineLength,
        );
        $returnColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            is_array($returnEntry) ? data_get($returnEntry, 'color') : null,
            $resolvedColor,
            'orange',
        );

        $returnNode1 = [
            'x' => $subtract($returnAnchor['x'], $resolvedArcSize),
            'y' => $add($returnAnchor['y'], $resolvedArcSize),
        ];
        $returnNode2 = [
            'x' => $subtract($returnNode1['x'], $returnBridgeLength),
            'y' => $returnNode1['y'],
        ];
        $returnNode3 = [
            'x' => $subtract($returnNode2['x'], $resolvedArcSize),
            'y' => $add($returnNode2['y'], $resolvedArcSize),
        ];

        array_push($branchReturnBoundsPoints, $returnAnchor, $returnNode1, $returnNode2, $returnNode3);
        $branchReturnConfigs[(int) $returnIndex] = [
            'anchor' => $returnAnchor,
            'bridgeLength' => $returnBridgeLength,
            'color' => $returnColor,
            'fallbackUsed' => $returnAnchorResult['fallbackUsed'],
            'counterStart' => $branchReturnCounterStart + ((count($branchReturnConfigs)) * 3),
        ];
    }

    $bounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints([
        $anchor,
        $node1,
        $node2,
        $node3,
        $continuationEnd,
        ...$branchExtensionBoundsPoints,
        ...$branchReturnBoundsPoints,
    ], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.1', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.2', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.3', $node3);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.start', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.end', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.end', $continuationEnd);

    foreach ($continuationAnchors as $continuationNumber => $continuationAnchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.continuation.' . $continuationNumber, $continuationAnchor);
    }

    foreach ($branchExtensionConfigs as $extensionNumber => $extensionConfig) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.start', $extensionConfig['anchor']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.bridge.end', $extensionConfig['bridgeEnd']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.arc.end', $extensionConfig['arcEnd']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.stem.end', $extensionConfig['end']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.end', $extensionConfig['end']);
    }
@endphp

@if ($resolvedDev && $missingAttachTarget)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($anchorStart, 'x', '0rem') }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($anchorStart, 'y', '0rem') }});
        "
        title="{{ $id }} | missing attach-to: {{ $attachTo }}"
    >
        <flux:badge color="red">
            {{ __('Missing anchor') }}: {{ $attachTo }}
        </flux:badge>
    </span>
@endif

@foreach ($fallbackWarnings as $fallbackWarning)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ data_get($fallbackWarning, 'anchor.x', '0rem') }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ data_get($fallbackWarning, 'anchor.y', '0rem') }});
            transform: translate(0.75rem, -0.75rem);
        "
        title="Fallback anchor used | component: {{ data_get($fallbackWarning, 'component') }} | requested: {{ data_get($fallbackWarning, 'requested') }} | resolved: {{ data_get($fallbackWarning, 'resolved') }}"
    >
        <flux:badge color="red">
            {{ __('Fallback anchor used') }}
        </flux:badge>
    </span>
@endforeach

<x-translation-workbench::ui.tw-graph.dev-box
    :id="$id . '.dev-box'"
    :x="$bounds['left']"
    :y="$bounds['bottom']"
    :width="$bounds['width']"
    :height="$bounds['height']"
    :color="$resolvedColor"
    :label="$id"
    :dev="$resolvedDev"
    metrics-scope="canvas"
    metrics-side="right"
/>

<x-translation-workbench::ui.tw-graph.paths.branch
    :id="$id . '.paths.branch'"
    side="right"
    :anchor-start="$anchor"
    :bridge-length="$resolvedBridgeLength"
    :arc-size="$resolvedArcSize"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :continuation-stem="$continuationStem"
    :counter-start="$counterStart"
    :dev="$resolvedDev"
/>

@foreach ($branchExtensionConfigs as $extensionNumber => $extensionConfig)
    <x-translation-workbench::ui.tw-graph.paths.branch-extension
        :id="$id . '.extension.' . $extensionNumber"
        side="right"
        :anchor-start="$extensionConfig['anchor']"
        :bridge-length="$extensionConfig['bridgeLength']"
        :stem-height="$extensionConfig['stemHeight']"
        :arc-size="$resolvedArcSize"
        :color="$extensionConfig['color']"
        :z-index="$zIndex - (2 + (int) $extensionConfig['renderIndex'])"
        :counter-start="$extensionConfig['counterStart']"
        :dev="$resolvedDev"
    />
@endforeach

@foreach ($branchReturnConfigs as $returnIndex => $returnConfig)
    <x-translation-workbench::ui.tw-graph.paths.branch-return
        :id="$id . '.branch-return.' . $returnIndex"
        side="right"
        :anchor-start="$returnConfig['anchor']"
        :bridge-length="$returnConfig['bridgeLength']"
        :arc-size="$resolvedArcSize"
        :color="$returnConfig['color']"
        :z-index="$zIndex - 1"
        :counter-start="$returnConfig['counterStart']"
        :fallback-used="$returnConfig['fallbackUsed']"
        :dev="$resolvedDev"
    />
@endforeach
