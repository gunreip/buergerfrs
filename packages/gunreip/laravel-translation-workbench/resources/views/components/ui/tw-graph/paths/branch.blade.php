{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/paths/branch.blade.php --}}
{{--
    Path: branch

    Usage:
    <x-translation-workbench::ui.tw-graph.paths.branch
        side="left"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
        bridge-length="3rem"
        :bridge-continuation="[1 => ['3rem'], 2 => ['2rem', 'top' => 'Bridge label']]"
        stem-length="2rem"
        :stem-continuation="[1 => ['2rem'], 2 => ['3rem', 'left' => 'Left label', 'right' => 'Right label']]"
        :node-labels="[3 => ['top' => 'Branch turn']]"
    />

    Path role:
    Branch owns one outbound branch chain:
    left:  segments.arc east-north -> segments.path right-left -> segments.arc south-west -> segments.path bottom-top
    right: segments.arc west-north -> segments.path left-right -> segments.arc south-east -> segments.path bottom-top
--}}

@props([
    'id' => 'path.branch',
    'side' => 'left',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'lineLength' => '4rem',
    'arcSize' => '2.75rem',
    'bridgeLength' => null,
    'bridgeContinuation' => [],
    'stemLength' => null,
    'stemContinuation' => [],
    'color' => 'pink',
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
    $resolvedLineLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($lineLength ?? null, '4rem');
    $resolvedArcSize = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrFallback($arcSize ?? null, '2.75rem');
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($bridgeLength, $resolvedLineLength, '4rem');
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string($stemLength, $resolvedLineLength, '4rem');
    $normalizeLabel = function (mixed $label, ?string $side = null) use ($color): ?array {
        if (blank($label)) {
            return null;
        }

        if (is_array($label)) {
            $text = data_get($label, 'text');
            $left = data_get($label, 'left');
            $right = data_get($label, 'right');
            $top = data_get($label, 'top');
            $bottom = data_get($label, 'bottom');

            if (filled($left)) {
                $text = $left;
                $side = 'left';
            } elseif (filled($right)) {
                $text = $right;
                $side = 'right';
            } elseif (filled($top)) {
                $text = $top;
                $side = 'top';
            } elseif (filled($bottom)) {
                $text = $bottom;
                $side = 'bottom';
            }

            if (blank($text)) {
                return null;
            }

            return array_replace([
                'text' => $text,
                'side' => $side,
                'badgeColor' => $color,
            ], collect($label)->except(['left', 'right', 'top', 'bottom'])->all());
        }

        return [
            'text' => $label,
            'side' => $side,
            'badgeColor' => $color,
        ];
    };
    $arcNodeLabel = fn (int $nodeNumber, string $defaultSide): ?array => $normalizeLabel(
        data_get($nodeLabels, $nodeNumber),
        $defaultSide,
    );
    $stemNodeLabels = function (mixed $entry) use ($normalizeLabel): array {
        if (! is_array($entry)) {
            return [null, null];
        }

        $left = data_get($entry, 'left');
        $right = data_get($entry, 'right');
        $top = data_get($entry, 'top');
        $bottom = data_get($entry, 'bottom');

        if (filled($left) || filled($right) || filled($top) || filled($bottom)) {
            return [
                filled($right) ? $normalizeLabel(['right' => $right]) : (filled($top) ? $normalizeLabel(['top' => $top]) : null),
                filled($left) ? $normalizeLabel(['left' => $left]) : (filled($bottom) ? $normalizeLabel(['bottom' => $bottom]) : null),
            ];
        }

        $labels = data_get($entry, 'labels');
        if (is_array($labels)) {
            return [
                $normalizeLabel(data_get($labels, 'right', data_get($labels, 'top', data_get($labels, 0)))),
                $normalizeLabel(data_get($labels, 'left', data_get($labels, 'bottom', data_get($labels, 1)))),
            ];
        }

        return [
            $normalizeLabel(data_get($entry, 'labelA', data_get($entry, 'label', data_get($entry, 1)))),
            $normalizeLabel(data_get($entry, 'labelB', data_get($entry, 2))),
        ];
    };
    $bridgeNodeLabels = function (mixed $entry) use ($normalizeLabel): array {
        if (! is_array($entry)) {
            return [null, null];
        }

        $top = data_get($entry, 'top');
        $bottom = data_get($entry, 'bottom');
        $left = data_get($entry, 'left');
        $right = data_get($entry, 'right');

        if (filled($top) || filled($bottom) || filled($left) || filled($right)) {
            return [
                filled($top) ? $normalizeLabel(['top' => $top]) : (filled($right) ? $normalizeLabel(['right' => $right]) : null),
                filled($bottom) ? $normalizeLabel(['bottom' => $bottom]) : (filled($left) ? $normalizeLabel(['left' => $left]) : null),
            ];
        }

        $labels = data_get($entry, 'labels');
        if (is_array($labels)) {
            return [
                $normalizeLabel(data_get($labels, 'top', data_get($labels, 'right', data_get($labels, 0)))),
                $normalizeLabel(data_get($labels, 'bottom', data_get($labels, 'left', data_get($labels, 1)))),
            ];
        }

        return [
            $normalizeLabel(data_get($entry, 'labelA', data_get($entry, 'label', data_get($entry, 1)))),
            $normalizeLabel(data_get($entry, 'labelB', data_get($entry, 2))),
        ];
    };

    $arcInStartAnchor = $isLeft ? 'e' : 'w';
    $arcInEndAnchor = 'n';
    $bridgeDirection = $isLeft ? 'right-left' : 'left-right';
    $arcOutStartAnchor = 's';
    $arcOutEndAnchor = $isLeft ? 'w' : 'e';
    $arcDelta = $isLeft ? $neg($resolvedArcSize) : $resolvedArcSize;
    $arcInEnd = [
        'x' => $add($currentAnchor['x'], $arcDelta),
        'y' => $add($currentAnchor['y'], $resolvedArcSize),
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
        $bridgeNodeEnd = collect($bridgeLabels)->filter(fn (mixed $label): bool => filled($label))->isNotEmpty()
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
    $pathEndAnchor = $arcOutEnd;
    $stemEntries = is_array($stemContinuation) ? $stemContinuation : [];
    if ($stemEntries === [] && filled($stemLength)) {
        $stemEntries = [1 => [$resolvedStemLength]];
    }
    $stemEntriesAreList = array_is_list($stemEntries);
    $stemSegments = [];
    $baseNodeCounterCount = 2 + count($bridgeSegments); // arc.in, bridge-continuation, arc.out
    $stemCounter = $counter + $baseNodeCounterCount;
    $arcOutCounter = $counter + 1 + count($bridgeSegments);

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
        $stemNodeEnd = collect($stemLabels)->filter(fn (mixed $label): bool => filled($label))->isNotEmpty()
            ? $stemLabels
            : true;

        $stemSegments[] = [
            'component' => 'path',
            'segment' => [
                'id' => $stemId,
                'direction' => 'bottom-top',
                'length' => $stemLengthValue,
                'anchorStart' => $pathEndAnchor,
                'anchorEnd' => $stemEnd,
                'nodeStart' => false,
                'nodeEnd' => $stemNodeEnd,
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
        [
            'component' => 'arc',
            'segment' => [
                'id' => $id . '.arc.in',
                'startAnchor' => $arcInStartAnchor,
                'endAnchor' => $arcInEndAnchor,
                'anchorStart' => $currentAnchor,
                'anchorEnd' => $arcInEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
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
                'anchorStart' => $bridgeEnd,
                'anchorEnd' => $arcOutEnd,
                'nodeStart' => false,
                'nodeEnd' => true,
                'endLabel' => $arcNodeLabel(3, 'top'),
                'devCounterEnd' => $arcOutCounter,
                'devCounterColor' => $color,
                'color' => $color,
                'zIndex' => $zIndex,
                'dev' => $dev,
            ],
        ],
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

@foreach ($segments as $segment)
    @if ($segment['component'] === 'arc')
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="$segment['segment']" />
    @else
        <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment['segment']" />
    @endif
@endforeach
