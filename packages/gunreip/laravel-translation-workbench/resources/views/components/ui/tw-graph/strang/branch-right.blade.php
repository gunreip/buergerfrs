{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/strang/branch-right.blade.php --}}
{{--
    Strang: branch-right

    Usage:
    <x-translation-workbench::ui.tw-graph.strang.branch-right
        attach-to="strang.trunk.node.5"
        bridge-length="4rem"
        :node-labels="[3 => ['top' => 'Branch turn']]"
        :bridge-continuation="[1 => ['4rem'], 2 => ['2rem', 'top' => 'Bridge label']]"
        :step="['stepLabel' => ['text' => ['Source inactive', 'shared obsolete']]]"
        stem-length="3rem"
        :stem-continuation="[1 => ['3rem']]"
        :branch-extension="[
            'stem.1' => [
                1 => ['bridgeLength' => '8rem', 'stemLength' => '3rem'],
            ],
        ]"
        :branch-return="[1 => ['attachTo' => 'stem.3', 'bridgeLength' => '8rem']]"
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
    'stemLength' => null,
])

@props([
    'id' => null,
    'componentCounter' => 1,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'bridgeLength' => null,
    'stemLength' => null,
    'nodeLabels' => [],
    'bridgeContinuation' => [],
    'step' => null,
    'stemContinuation' => [],
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
    $localStemLength = $attributes->get('stem-length');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localBridgeLength,
        $bridgeLength ?? null,
        $resolvedLineLength,
    );
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $localStemLength,
        $stemLength ?? null,
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
    $bridgeEntries = is_array($bridgeContinuation) && $bridgeContinuation !== []
        ? $bridgeContinuation
        : [1 => [$resolvedBridgeLength]];
    $bridgeEntriesAreList = array_is_list($bridgeEntries);
    $bridgeAnchors = [];
    $node2 = $node1;

    foreach ($bridgeEntries as $bridgeIndex => $bridgeEntry) {
        $bridgeNumber = $bridgeEntriesAreList ? ((int) $bridgeIndex + 1) : (int) $bridgeIndex;
        $bridgeLength = is_array($bridgeEntry)
            ? (string) (data_get($bridgeEntry, 'length', data_get($bridgeEntry, 0)) ?: $resolvedBridgeLength)
            : (filled($bridgeEntry) ? (string) $bridgeEntry : $resolvedBridgeLength);

        $node2 = [
            'x' => $add($node2['x'], $bridgeLength),
            'y' => $node2['y'],
            'source' => $id . '.paths.branch.bridge.' . $bridgeNumber,
            'sourceType' => 'bridge',
            'sourceAnchor' => 'end',
            'direction' => 'left-right',
        ];
        $bridgeAnchors[$bridgeNumber] = $node2;
    }

    $node3 = [
        'x' => $add($node2['x'], $resolvedArcSize),
        'y' => $add($node2['y'], $resolvedArcSize),
        'source' => $id . '.paths.branch.arc.out',
        'sourceType' => 'arc',
        'sourceAnchor' => 'end',
        'direction' => 'south-east',
    ];
    $stepConfig = is_array($step)
        ? $step
        : (filled($step) ? ['stepLabel' => ['text' => $step]] : null);
    if (is_array($stepConfig) && filled(data_get($stepConfig, 'text')) && blank(data_get($stepConfig, 'stepLabel.text'))) {
        $stepConfig['stepLabel'] = ['text' => data_get($stepConfig, 'text')];
    }
    $hasStep = is_array($stepConfig) && filled(data_get($stepConfig, 'stepLabel.text'));
    $stepLabelLines = collect(is_iterable(data_get($stepConfig, 'stepLabel.text')) && ! is_string(data_get($stepConfig, 'stepLabel.text')) ? data_get($stepConfig, 'stepLabel.text') : [data_get($stepConfig, 'stepLabel.text')])
        ->filter(fn (mixed $line): bool => filled($line))
        ->take(3)
        ->count();
    $autoStepLabelGap = match ($stepLabelLines) {
        1 => '2.75rem',
        2 => '3.75rem',
        3 => '4.75rem',
        default => '3.75rem',
    };
    $stepBeforeLength = (string) data_get($stepConfig, 'beforeLength', '1.5rem');
    $stepLabelGap = (string) (data_get($stepConfig, 'labelGap') ?: $autoStepLabelGap);
    $stepAfterLength = (string) data_get($stepConfig, 'afterLength', '1.5rem');
    $stepEnd = [
        'x' => $node3['x'],
        'y' => $add($add($add($node3['y'], $stepBeforeLength), $stepLabelGap), $stepAfterLength),
        'source' => $id . '.paths.branch.step',
        'sourceType' => 'step',
        'sourceAnchor' => 'end',
        'direction' => 'bottom-top',
    ];
    $stemEntries = is_array($stemContinuation) ? $stemContinuation : [];
    if ($stemEntries === [] && (filled($localStemLength) || filled($stemLength ?? null))) {
        $stemEntries = [1 => [$resolvedStemLength]];
    }
    $stemEntriesAreList = array_is_list($stemEntries);
    $stemAnchors = [];
    $stemEnd = $hasStep ? $stepEnd : $node3;

    foreach ($stemEntries as $stemIndex => $stemEntry) {
        $stemNumber = $stemEntriesAreList ? ((int) $stemIndex + 1) : (int) $stemIndex;
        $length = is_array($stemEntry)
            ? (string) (data_get($stemEntry, 'length', data_get($stemEntry, 0)) ?: $resolvedStemLength)
            : (filled($stemEntry) ? (string) $stemEntry : $resolvedStemLength);

        $stemEnd = [
            'x' => $stemEnd['x'],
            'y' => $add($stemEnd['y'], $length),
            'source' => $id . '.paths.branch.stem.' . $stemNumber,
            'sourceType' => 'stem',
            'sourceAnchor' => 'end',
            'direction' => 'bottom-top',
        ];
        $stemAnchors[$stemNumber] = $stemEnd;
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
    $branchExtensionReturnBridgeBoundsPoints = [];
    $branchExtensionReturnBridgeConfigs = [];
    $fallbackWarnings = [];
    $branchExtensionCounterStart = $counterStart + 2 + count($bridgeAnchors) + count($stemAnchors) + ($hasStep ? 1 : 0);
    $branchExtensionRenderIndex = 0;
    $resolveBranchRightAnchor = function (?string $attachTo, array $fallback) use (
        $resolvedGraphId,
        $node1,
        $node2,
        $node3,
        $bridgeAnchors,
        &$branchExtensionConfigs,
        &$branchExtensionAliases,
        $stemAnchors,
        $stemEnd,
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

        if ($attachTo === 'bridge.end') {
            return $resolved($node2);
        }

        if (preg_match('/^bridge\.(\d+)(?:\.end)?$/', $attachTo, $matches) === 1) {
            $bridgeNumber = (int) $matches[1];

            return isset($bridgeAnchors[$bridgeNumber])
                ? $resolved($bridgeAnchors[$bridgeNumber])
                : $fallbackResolved();
        }

        if ($attachTo === 'stem.end') {
            return $resolved($stemEnd);
        }

        if (preg_match('/^stem\.(\d+)(?:\.end)?$/', $attachTo, $matches) === 1) {
            $stemNumber = (int) $matches[1];

            return isset($stemAnchors[$stemNumber])
                ? $resolved($stemAnchors[$stemNumber])
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
            $extensionStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($extensionEntry) ? data_get($extensionEntry, 'stemLength', data_get($extensionEntry, 1)) : null,
                $resolvedStemLength,
                $resolvedLineLength,
            );
            $extensionColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($extensionEntry) ? data_get($extensionEntry, 'color') : null,
                $resolvedColor,
                'pink',
            );
            $extensionReturnBridge = is_array($extensionEntry)
                ? data_get($extensionEntry, 'returnBridge')
                : null;
            $extensionNodeLabels = is_array($extensionEntry)
                ? data_get($extensionEntry, 'nodeLabels', [])
                : [];
            $extensionEndLabel = is_array($extensionEntry)
                ? data_get($extensionEntry, 'endLabel')
                : null;
            $extensionEndLength = is_array($extensionEntry)
                ? data_get($extensionEntry, 'endLength')
                : null;
            $extensionCapLength = is_array($extensionEntry)
                ? data_get($extensionEntry, 'capLength')
                : null;
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
                'y' => $add($extensionArcEnd['y'], $extensionStemLength),
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
                'stemLength' => $extensionStemLength,
                'color' => $extensionColor,
                'returnBridge' => $extensionReturnBridge,
                'nodeLabels' => is_array($extensionNodeLabels) ? $extensionNodeLabels : [],
                'endLabel' => $extensionEndLabel,
                'endLength' => $extensionEndLength,
                'capLength' => $extensionCapLength,
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

    $branchExtensionReturnBridgeCounterStart = $branchExtensionCounterStart;
    foreach ($branchExtensionConfigs as $extensionNumber => $extensionConfig) {
        $returnBridge = data_get($extensionConfig, 'returnBridge');
        if (blank($returnBridge)) {
            continue;
        }

        $returnBridgeIsSingleConfig = is_array($returnBridge)
            && ! array_is_list($returnBridge)
            && (
                array_key_exists(0, $returnBridge)
                || array_key_exists('bridgeLength', $returnBridge)
                || array_key_exists('color', $returnBridge)
                || array_key_exists('nodeLabels', $returnBridge)
                || array_key_exists('labels', $returnBridge)
            );
        $returnBridgeEntries = is_array($returnBridge)
            ? ($returnBridgeIsSingleConfig ? [1 => $returnBridge] : $returnBridge)
            : [1 => $returnBridge];
        $returnBridgeEntriesAreList = array_is_list($returnBridgeEntries);

        foreach ($returnBridgeEntries as $returnBridgeIndex => $returnBridgeEntry) {
            $returnBridgeNumber = $returnBridgeEntriesAreList ? ((int) $returnBridgeIndex + 1) : (int) $returnBridgeIndex;
            $returnBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($returnBridgeEntry) ? data_get($returnBridgeEntry, 'bridgeLength', data_get($returnBridgeEntry, 0)) : $returnBridgeEntry,
                $resolvedBridgeLength,
                $resolvedLineLength,
            );
            $returnBridgeColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
                is_array($returnBridgeEntry) ? data_get($returnBridgeEntry, 'color') : null,
                data_get($extensionConfig, 'color'),
                $resolvedColor,
            );
            $returnBridgeNodeLabels = is_array($returnBridgeEntry)
                ? data_get($returnBridgeEntry, 'nodeLabels', data_get($returnBridgeEntry, 'labels', []))
                : [];
            $returnBridgeAnchor = data_get($extensionConfig, 'end');
            $returnBridgeArcEnd = [
                'x' => $subtract($returnBridgeAnchor['x'], $resolvedArcSize),
                'y' => $add($returnBridgeAnchor['y'], $resolvedArcSize),
                'source' => $id . '.extension.' . $extensionNumber . '.return-bridge.' . $returnBridgeNumber . '.arc',
                'sourceType' => 'arc',
                'sourceAnchor' => 'end',
                'direction' => 'east-north',
            ];
            $returnBridgeEnd = [
                'x' => $subtract($returnBridgeArcEnd['x'], $returnBridgeLength),
                'y' => $returnBridgeArcEnd['y'],
                'source' => $id . '.extension.' . $extensionNumber . '.return-bridge.' . $returnBridgeNumber . '.bridge',
                'sourceType' => 'bridge',
                'sourceAnchor' => 'end',
                'direction' => 'right-left',
            ];

            array_push($branchExtensionReturnBridgeBoundsPoints, $returnBridgeAnchor, $returnBridgeArcEnd, $returnBridgeEnd);
            $branchExtensionReturnBridgeConfigs[] = [
                'extensionNumber' => $extensionNumber,
                'returnBridgeNumber' => $returnBridgeNumber,
                'anchor' => $returnBridgeAnchor,
                'bridgeLength' => $returnBridgeLength,
                'color' => $returnBridgeColor,
                'nodeLabels' => is_array($returnBridgeNodeLabels) ? $returnBridgeNodeLabels : [],
                'arcEnd' => $returnBridgeArcEnd,
                'end' => $returnBridgeEnd,
                'counterStart' => $branchExtensionReturnBridgeCounterStart,
            ];
            $branchExtensionReturnBridgeCounterStart += 2;
        }
    }

    $branchReturnEntries = is_array($branchReturn) ? $branchReturn : [];
    $branchReturnBoundsPoints = [];
    $branchReturnConfigs = [];
    $branchReturnCounterStart = $branchExtensionReturnBridgeCounterStart;

    foreach ($branchReturnEntries as $returnIndex => $returnEntry) {
        $returnAttachTo = is_array($returnEntry)
            ? data_get($returnEntry, 'attachTo', data_get($returnEntry, 'anchor'))
            : null;
        $returnFallbackIndex = filled($returnAttachTo)
            ? ((int) $returnIndex + 1)
            : (int) $returnIndex;
        $returnAnchorResult = $resolveBranchRightAnchor(
            is_string($returnAttachTo) ? $returnAttachTo : null,
            $stemAnchors[$returnFallbackIndex] ?? $stemEnd,
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
        ...($hasStep ? [$stepEnd] : []),
        $stemEnd,
        ...$branchExtensionBoundsPoints,
        ...$branchExtensionReturnBridgeBoundsPoints,
        ...$branchReturnBoundsPoints,
    ], '1rem');

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.start', $anchor);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.1', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.2', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.node.3', $node3);
    if ($hasStep) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.step.end', $stepEnd);
    }
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.start', $node1);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.end', $node2);
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.end', $stemEnd);

    foreach ($bridgeAnchors as $bridgeNumber => $bridgeAnchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.' . $bridgeNumber . '.end', $bridgeAnchor);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.bridge.' . $bridgeNumber, $bridgeAnchor);
    }

    foreach ($stemAnchors as $stemNumber => $stemAnchor) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.stem.' . $stemNumber . '.end', $stemAnchor);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.stem.' . $stemNumber, $stemAnchor);
    }

    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.stem.end', $stemEnd);

    foreach ($branchExtensionConfigs as $extensionNumber => $extensionConfig) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.start', $extensionConfig['anchor']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.bridge.end', $extensionConfig['bridgeEnd']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.arc.end', $extensionConfig['arcEnd']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.stem.end', $extensionConfig['end']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $extensionNumber . '.end', $extensionConfig['end']);
    }

    foreach ($branchExtensionReturnBridgeConfigs as $returnBridgeConfig) {
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $returnBridgeConfig['extensionNumber'] . '.return-bridge.' . $returnBridgeConfig['returnBridgeNumber'] . '.start', $returnBridgeConfig['anchor']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $returnBridgeConfig['extensionNumber'] . '.return-bridge.' . $returnBridgeConfig['returnBridgeNumber'] . '.arc.end', $returnBridgeConfig['arcEnd']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $returnBridgeConfig['extensionNumber'] . '.return-bridge.' . $returnBridgeConfig['returnBridgeNumber'] . '.bridge.end', $returnBridgeConfig['end']);
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, 'strang.branch-right.extension.' . $returnBridgeConfig['extensionNumber'] . '.return-bridge.' . $returnBridgeConfig['returnBridgeNumber'] . '.end', $returnBridgeConfig['end']);
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
    :bridge-continuation="$bridgeContinuation"
    :step="$step"
    :stem-length="$resolvedStemLength"
    :stem-continuation="$stemContinuation"
    :arc-size="$resolvedArcSize"
    :color="$resolvedColor"
    :z-index="$zIndex"
    :node-labels="$nodeLabels"
    :counter-start="$counterStart"
    :dev="$resolvedDev"
/>

@foreach ($branchExtensionConfigs as $extensionNumber => $extensionConfig)
    <x-translation-workbench::ui.tw-graph.paths.branch-extension
        :id="$id . '.extension.' . $extensionNumber"
        side="right"
        :anchor-start="$extensionConfig['anchor']"
        :bridge-length="$extensionConfig['bridgeLength']"
        :stem-length="$extensionConfig['stemLength']"
        :arc-size="$resolvedArcSize"
        :color="$extensionConfig['color']"
        :z-index="$zIndex - (2 + (int) $extensionConfig['renderIndex'])"
        :counter-start="$extensionConfig['counterStart']"
        :node-labels="$extensionConfig['nodeLabels']"
        :end-label="$extensionConfig['endLabel']"
        :end-length="$extensionConfig['endLength']"
        :cap-length="$extensionConfig['capLength']"
        :dev="$resolvedDev"
    />
@endforeach

@foreach ($branchExtensionReturnBridgeConfigs as $returnBridgeConfig)
    <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
        :id="$id . '.extension.' . $returnBridgeConfig['extensionNumber'] . '.return-bridge.' . $returnBridgeConfig['returnBridgeNumber']"
        side="right"
        :anchor-start="$returnBridgeConfig['anchor']"
        :bridge-length="$returnBridgeConfig['bridgeLength']"
        :arc-size="$resolvedArcSize"
        :color="$returnBridgeConfig['color']"
        :node-labels="$returnBridgeConfig['nodeLabels']"
        :z-index="$zIndex - 1"
        :counter-start="$returnBridgeConfig['counterStart']"
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
