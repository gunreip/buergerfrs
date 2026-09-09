{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/branch.blade.php --}}
{{--
    Path: branch

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        entry-stem-length="1rem"
        bridge-length="3rem"
        :bridge-continuation="[1 => ['3rem'], 2 => ['2rem', 'top' => 'Bridge label']]"
        :step="['stepLabel' => ['text' => ['Source inactive', 'shared obsolete', '9 rows']]]"
        stem-length="2rem"
        :stem-continuation="[1 => ['2rem'], 2 => ['3rem', 'left' => 'Left label', 'right' => 'Right label']]"
        :node-labels="[3 => ['top' => 'Branch turn']]"
    />

    Path role:
    Branch owns one outbound branch chain:
    left:  segments.arc east-north -> segments.path right-left -> segments.arc south-west -> segments.path bottom-top
    right: segments.arc west-north -> segments.path left-right -> segments.arc south-east -> segments.path bottom-top
--}}

@aware([
    'color' => null,
    'labelGap' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'id' => 'path.branch',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'lineLength' => null,
    'arcSize' => null,
    'entryStemLength' => '0rem',
    'bridgeLength' => null,
    'bridgeContinuation' => [],
    'step' => null,
    'stemLength' => null,
    'stemContinuation' => [],
    'color' => null,
    'zIndex' => null,
    'nodeLabels' => [],
    'counterStart' => 1,
    'dev' => false,
])

@php
    $add = fn (string $value, string $delta): string => $delta === '0rem' ? $value : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn (string $value): string => 'calc(' . $value . ' * -1)';
    $currentAnchor = [
        'x' => data_get($anchorStart, 'x', '0rem'),
        'y' => data_get($anchorStart, 'y', '0rem'),
    ];
    $counter = (int) $counterStart;
    $isLeft = $side === 'left';
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($lineLength ?? null, 'line_length', '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString($arcSize ?? null, 'arc_size', '2.75rem');
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($color, $inheritedColor ?? null, 'zinc');
    $resolvedEntryStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($entryStemLength, null, '0rem');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength), '4rem');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength), '4rem');
    $normalizeLabel = fn (mixed $label, ?string $side = null): ?array => \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, $side, $resolvedColor);
    $arcNodeLabel = fn (int $nodeNumber, string $defaultSide): ?array => $normalizeLabel(
        data_get($nodeLabels, $nodeNumber),
        $defaultSide,
    );
    $nodeLabelGeometryKeys = [
        'length',
        'component',
        'compressed',
        'beforeLength',
        'gapLength',
        'afterLength',
        'capLength',
        'force',
        'render',
        'spacer',
    ];
    $nodeLabelsForEntry = function (mixed $entry, string $defaultSide) use ($resolvedColor, $nodeLabelGeometryKeys): array {
        if (! is_array($entry)) {
            return [null, null];
        }

        if (array_is_list($entry)) {
            return [null, null];
        }

        $labels = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::nodeLabels(
            $entry,
            $defaultSide,
            $resolvedColor,
            $nodeLabelGeometryKeys,
        );

        return $labels === true ? [null, null] : $labels;
    };
    $stemNodeLabels = fn (mixed $entry): array => $nodeLabelsForEntry($entry, 'right');
    $bridgeNodeLabels = fn (mixed $entry): array => $nodeLabelsForEntry($entry, 'top');
    $hasLabels = static fn (mixed $node): bool => is_array($node) && collect($node)->filter()->isNotEmpty();
    $hasVisibleStemPayload = function (mixed $entry, array $labels): bool {
        if (! is_array($entry)) {
            return false;
        }

        $hasLabels = collect($labels)
            ->filter(fn (mixed $label): bool => filled($label))
            ->isNotEmpty();

        return $hasLabels
            || (bool) data_get($entry, 'compressed', false)
            || (bool) data_get($entry, 'force', false)
            || (bool) data_get($entry, 'render', false)
            || (bool) data_get($entry, 'spacer', false);
    };

    $arcInStartAnchor = $isLeft ? 'e' : 'w';
    $arcInEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'right-left' : 'left-right';
    $bridgeJointArrowDirection = $isLeft ? 'left' : 'right';
    $stemJointArrowDirection = 'top';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'w' : 'e';
    $arcDelta = $isLeft ? $neg($resolvedArcSize) : $resolvedArcSize;
    $branchStartAnchor = [
        'x' => $currentAnchor['x'],
        'y' => $add($currentAnchor['y'], $resolvedEntryStemLength),
    ];
    $arcInEnd = [
        'x' => $add($branchStartAnchor['x'], $arcDelta),
        'y' => $add($branchStartAnchor['y'], $resolvedArcSize),
    ];
    $bridgeEntries = is_array($bridgeContinuation) && $bridgeContinuation !== []
        ? $bridgeContinuation
        : [1 => [$resolvedBridgeLength]];
    $bridgeEntriesAreList = array_is_list($bridgeEntries);
    $bridgeSegments = [];
    $bridgeEnd = $arcInEnd;
    $bridgeCounter = $counter + 1; // arc.in owns the first visible branch node.

    foreach ($bridgeEntries as $bridgeIndex => $bridgeEntry) {
        $bridgeNumber = $bridgeEntriesAreList ? ((int) $bridgeIndex + 1) : (int) $bridgeIndex;
        $bridgeLength = is_array($bridgeEntry)
            ? (string) (data_get($bridgeEntry, 'length', data_get($bridgeEntry, 0)) ?: $resolvedBridgeLength)
            : (filled($bridgeEntry) ? (string) $bridgeEntry : $resolvedBridgeLength);
        $bridgeDelta = $isLeft ? $neg($bridgeLength) : $bridgeLength;
        $nextBridgeEnd = [
            'x' => $add($bridgeEnd['x'], $bridgeDelta),
            'y' => $bridgeEnd['y'],
        ];
        $bridgeLabels = $bridgeNodeLabels($bridgeEntry);
        $bridgeNodeEnd = $hasLabels($bridgeLabels)
            ? $bridgeLabels
            : true;

        $bridgeSegments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $id . '.bridge.' . $bridgeNumber,
                'direction' => $bridgeDirection,
                'length' => $bridgeLength,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $nextBridgeEnd,
                'nodeStart' => false,
                'nodeEnd' => $bridgeNodeEnd,
                'nodeEndDot' => $hasLabels($bridgeNodeEnd),
                'jointArrowEnd' => ! $hasLabels($bridgeNodeEnd),
                'devCounterEnd' => $bridgeCounter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];

        $bridgeEnd = $nextBridgeEnd;
    }

    $arcOutEnd = [
        'x' => $add($bridgeEnd['x'], $arcDelta),
        'y' => $add($bridgeEnd['y'], $resolvedArcSize),
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
    $stepLabelOffset = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        data_get($stepConfig, 'stepLabel.offset'),
        $labelGap ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('label_offset', '0.75rem'),
    );
    $autoStepLabelContentGap = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::stepLabelContentGap($stepLabelLines);
    $autoStepLabelGap = 'calc(' . $autoStepLabelContentGap . ' + (' . $stepLabelOffset . ' * 2))';
    if ($hasStep && blank(data_get($stepConfig, 'stepLabel.offset'))) {
        $stepConfig['stepLabel']['offset'] = $stepLabelOffset;
    }
    $stepBeforeLength = (string) data_get($stepConfig, 'beforeLength', '1.5rem');
    $stepLabelGap = (string) (data_get($stepConfig, 'labelGap') ?: $autoStepLabelGap);
    $stepAfterLength = (string) data_get($stepConfig, 'afterLength', '2.5rem');
    $stepAnchorEnd = [
        'x' => $arcOutEnd['x'],
        'y' => $add($add($add($arcOutEnd['y'], $stepBeforeLength), $stepLabelGap), $stepAfterLength),
    ];
    $pathEndAnchor = $hasStep ? $stepAnchorEnd : $arcOutEnd;
    $stemEntries = is_array($stemContinuation) ? $stemContinuation : [];
    if ($stemEntries === [] && filled($stemLength)) {
        $stemEntries = [1 => [$resolvedStemLength]];
    }
    $stemEntriesAreList = array_is_list($stemEntries);
    $stemSegments = [];
    $baseNodeCounterCount = 2 + count($bridgeSegments); // arc.in, bridge-continuation, arc.out
    $stemCounter = $counter + $baseNodeCounterCount + ($hasStep ? 1 : 0);
    $arcOutCounter = $counter + 1 + count($bridgeSegments);
    $stepCounter = $arcOutCounter + 1;
    $stepNodeEnd = true;
    $arcInEndLabel = $arcNodeLabel(1, 'top');
    $arcOutEndLabel = $arcNodeLabel(3, 'top');
    $configuredNodeLabelNumbers = collect(is_array($nodeLabels) ? array_keys($nodeLabels) : [])
        ->filter(static fn (mixed $key): bool => is_int($key) || (is_string($key) && ctype_digit($key)))
        ->map(static fn (mixed $key): int => (int) $key)
        ->filter(static fn (int $nodeNumber): bool => $nodeNumber > 0)
        ->unique()
        ->sort()
        ->values();
    $availableNodeLabelNumbers = collect([1, 3]);
    $ignoredNodeLabelNumbers = $configuredNodeLabelNumbers
        ->diff($availableNodeLabelNumbers)
        ->values();
    $nodeLabelMismatch = $ignoredNodeLabelNumbers->isNotEmpty();
    $nodeLabelMismatchText = 'nodeLabel-Mismatch | labels: '
        . $configuredNodeLabelNumbers->count()
        . ' | anchors: '
        . $availableNodeLabelNumbers->count()
        . ' | ignored: '
        . $ignoredNodeLabelNumbers->count();
    $nodeLabelMismatchTitle = $nodeLabelMismatchText
        . ' | ignored nodes: '
        . $ignoredNodeLabelNumbers->implode(', ');

    if ($hasStep && $stemEntries !== []) {
        $firstStemKey = array_key_first($stemEntries);
        $firstStemEntry = $stemEntries[$firstStemKey];
        $firstStemLabels = $stemNodeLabels($firstStemEntry);
        $firstStemHasLabels = collect($firstStemLabels)
            ->filter(fn (mixed $label): bool => filled($label))
            ->isNotEmpty();
        $firstStemIsPromotable = is_array($firstStemEntry)
            && $firstStemHasLabels
            && ! (bool) data_get($firstStemEntry, 'compressed', false)
            && ! (bool) data_get($firstStemEntry, 'force', false)
            && ! (bool) data_get($firstStemEntry, 'render', false)
            && ! (bool) data_get($firstStemEntry, 'spacer', false);

        if ($firstStemIsPromotable) {
            $stepNodeEnd = $firstStemLabels;
            unset($stemEntries[$firstStemKey]);
        }
    }

    foreach ($stemEntries as $stemIndex => $stemEntry) {
        $stemNumber = $stemEntriesAreList ? ((int) $stemIndex + 1) : (int) $stemIndex;
        $stemLengthValue = is_array($stemEntry)
            ? (string) (data_get($stemEntry, 'length', data_get($stemEntry, 0)) ?: $resolvedStemLength)
            : (filled($stemEntry) ? (string) $stemEntry : $resolvedStemLength);
        $stemEnd = [
            'x' => $pathEndAnchor['x'],
            'y' => $add($pathEndAnchor['y'], $stemLengthValue),
        ];
        $stemId = $id . '.stem.' . $stemNumber;

        $stemLabels = $stemNodeLabels($stemEntry);
        if (! $hasVisibleStemPayload($stemEntry, $stemLabels)) {
            continue;
        }

        $stemNodeEnd = $hasLabels($stemLabels)
            ? $stemLabels
            : true;

        $compressedStem = is_array($stemEntry) && (bool) data_get($stemEntry, 'compressed', false);
        $stemSegments[] = [
            'component' => $compressedStem ? 'stem-compressed' : 'path',
            'segment' => [
                'id' => $stemId,
                'direction' => 'bottom-top',
                'length' => $stemLengthValue,
                'beforeLength' => is_array($stemEntry) ? data_get($stemEntry, 'beforeLength', '1rem') : '1rem',
                'gapLength' => is_array($stemEntry) ? data_get($stemEntry, 'gapLength', '1rem') : '1rem',
                'afterLength' => is_array($stemEntry) ? data_get($stemEntry, 'afterLength', '1rem') : '1rem',
                'capLength' => is_array($stemEntry) ? data_get($stemEntry, 'capLength', '1.25rem') : '1.25rem',
                'anchorStart' => $pathEndAnchor,
                'anchorEnd' => $stemEnd,
                'nodeStart' => false,
                'nodeEnd' => $stemNodeEnd,
                'nodeEndDot' => $hasLabels($stemNodeEnd),
                'jointArrowEnd' => ! $hasLabels($stemNodeEnd),
                'devCounterEnd' => $stemCounter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ];

        $pathEndAnchor = $stemEnd;
    }
    $pathBoxPadding = '0.75rem';
    $pathBoxX = $isLeft ? $pathEndAnchor['x'] : $currentAnchor['x'];
    $pathBoxY = $currentAnchor['y'];
    $pathBoxWidth = $isLeft
        ? 'calc(' . $currentAnchor['x'] . ' - ' . $pathEndAnchor['x'] . ')'
        : 'calc(' . $pathEndAnchor['x'] . ' - ' . $currentAnchor['x'] . ')';
    $pathBoxHeight = 'calc(' . $pathEndAnchor['y'] . ' - ' . $currentAnchor['y'] . ')';

    $segments = [
        ...($resolvedEntryStemLength !== '0rem' ? [[
            'component' => 'path',
            'segment' => [
                'id' => $id . '.entry-stem',
                'direction' => 'bottom-top',
                'length' => $resolvedEntryStemLength,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $branchStartAnchor,
                'nodeStart' => false,
                'nodeEnd' => false,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ]] : []),
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.in',
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'arcSize' => $resolvedArcSize,
                'anchorStart' => $branchStartAnchor,
                'anchorEnd' => $arcInEnd,
                'nodeStart' => false,
                'nodeEnd' => $arcInEndLabel === null ? true : [$arcInEndLabel, null],
                'nodeEndDot' => filled($arcInEndLabel),
                'jointArrowEnd' => blank($arcInEndLabel),
                'jointArrowEndDirection' => $bridgeJointArrowDirection,
                'endLabel' => null,
                'devCounterEnd' => $counter++,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        ...$bridgeSegments,
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.out',
                'startAnchor' => $arcOutStartAnchor,
                'endAnchor' => $arcOutEndAnchor,
                'arcSize' => $resolvedArcSize,
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
                'nodeStart' => false,
                'nodeEnd' => $arcOutEndLabel === null ? true : [$arcOutEndLabel, null],
                'nodeEndDot' => filled($arcOutEndLabel),
                'jointArrowEnd' => blank($arcOutEndLabel),
                'jointArrowEndDirection' => $stemJointArrowDirection,
                'endLabel' => null,
                'devCounterEnd' => $arcOutCounter,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
        ...($hasStep ? [[
            'component' => 'step',
            'segment' => [
                'id' => $id . '.step',
                'direction' => 'bottom-top',
                'beforeLength' => $stepBeforeLength,
                'labelGap' => $stepLabelGap,
                'afterLength' => $stepAfterLength,
                'anchorStart' => $arcOutEnd,
                'anchorEnd' => $stepAnchorEnd,
                'nodeStart' => false,
                'nodeEnd' => $stepNodeEnd,
                'stepLabel' => data_get($stepConfig, 'stepLabel'),
                'devCounterEnd' => $stepCounter,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ]] : []),
        ...$stemSegments,
    ];
@endphp

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

@if ($dev && $nodeLabelMismatch)
    <span
        class="tw-graph-protocol-dev-only absolute z-50"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $pathEndAnchor['x'] }} + 1rem);
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $pathEndAnchor['y'] }} + 1rem);
        "
        title="{{ $nodeLabelMismatchTitle }}"
    >
        <flux:badge color="red">
            {{ $nodeLabelMismatchText }}
        </flux:badge>
    </span>
@endif

@foreach ($segments as $segment)
    @if ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @elseif ($segment['component'] === 'step')
        <x-translation-workbench::ui.tw-graph.segments.step
            :segment="$segment['segment']"
            :dev="$dev"
        />
    @elseif ($segment['component'] === 'stem-compressed')
        <x-translation-workbench::ui.tw-graph.segments.stem-compressed
            :segment="$segment['segment']"
            :dev="$dev"
        />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
