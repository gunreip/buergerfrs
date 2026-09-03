{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/segments/path.blade.php --}}
{{--
    Segment: path

    Usage:
    <x-translation-workbench::ui.tw-graph.segments.path :segment="$segment" />

    <x-translation-workbench::ui.tw-graph.segments.path
        id="example.path"
        direction="bottom-top"
        length="3rem"
        :anchor-start="$start"
        :anchor-end="$end"
        :node-end="true"
    />

    Segment role:
    A concrete graph path segment built from the neutral line primitive.
    Paths decide direction, length, anchors, optional path nodes, gradient,
    cap behavior, and optional node labels. Labels are delegated to
    segments.label; line, connector, and text remain neutral primitives.

    Required segment fields:
    id, direction, length, anchorStart{x,y}, anchorEnd{x,y}

    Node / label fields:
    nodeStart and nodeEnd control anchor presence, labels, and DEV counters.
    nodeStartDot/nodeEndDot may hide only the visual dot while keeping the
    technical anchor and existing DEV-counter behavior intact.
    false|null|'' = no node, no labels
    true = node, no labels
    [null, null] = node, no labels
    ['Label A', null] = node + first label slot
    [null, 'Label B'] = node + second label slot

    DEV mode:
    Every visible node automatically gets a dev-node-counter. The counter
    belongs to the node/anchor, not to the line primitive. It receives a
    direction-aware offset so consecutive path segments do not stack all
    counters on one point.
--}}

@props([
    'segment' => [],
    'id' => 'segment.path',
    'direction' => 'bottom-top',
    'length' => '4rem',
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
    'nodeStart' => false,
    'nodeEnd' => false,
    'devCounterStart' => 'S',
    'devCounterEnd' => 'E',
    'devCounterColor' => 'zinc',
    'gradient' => false,
    'cap' => false,
    'capStart' => false,
    'capEnd' => null,
    'capLength' => '1.25rem',
    'dashed' => false,
    'color' => 'cyan',
    'zIndex' => null,
    'dev' => null,
])

@php
    if ($segment === []) {
        $segment = [
            'id' => $id,
            'direction' => $direction,
            'length' => $length,
            'anchorStart' => $anchorStart,
            'anchorEnd' => $anchorEnd,
            'nodeStart' => $nodeStart,
            'nodeEnd' => $nodeEnd,
            'nodeStartSize' => data_get($segment, 'nodeStartSize'),
            'nodeEndSize' => data_get($segment, 'nodeEndSize'),
            'devCounterStart' => $devCounterStart,
            'devCounterEnd' => $devCounterEnd,
            'devCounterColor' => $devCounterColor,
            'gradient' => $gradient,
            'cap' => $cap,
            'capStart' => $capStart,
            'capEnd' => $capEnd,
            'capLength' => $capLength,
            'dashed' => $dashed,
            'color' => $color,
            'zIndex' => $zIndex,
            'dev' => $dev,
        ];
    }

    $id = data_get($segment, 'id', 'segment.path');
    $direction = data_get($segment, 'direction', 'bottom-top');
    $color = data_get($segment, 'color', 'cyan');
    $zIndex = data_get($segment, 'zIndex');
    $nodeStartValue = data_get($segment, 'nodeStart', false);
    $nodeEndValue = data_get($segment, 'nodeEnd', false);
    $nodeIsVisible = function (mixed $value): bool {
        if (is_array($value)) {
            return true;
        }

        return filled($value) && (bool) $value;
    };
    $nodeLabels = function (mixed $value): array {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->take(2)
            ->map(function (mixed $label): ?array {
                if (blank($label)) {
                    return null;
                }

                if (is_array($label)) {
                    return $label;
                }

                return ['text' => $label];
            })
            ->all();
    };
    $nodeStart = $nodeIsVisible($nodeStartValue);
    $nodeEnd = $nodeIsVisible($nodeEndValue);
    $nodeStartDot = (bool) data_get($segment, 'nodeStartDot', $nodeStart);
    $nodeEndDot = (bool) data_get($segment, 'nodeEndDot', $nodeEnd);
    $devMode = (bool) ($dev ?? data_get($segment, 'dev', false));
    $isHorizontal = in_array($direction, ['left-right', 'right-left'], true);
    $counterDistance = 'calc(var(--tw-graph-protocol-node-half) + var(--tw-graph-protocol-dev-node-counter-half))';
    $negativeCounterDistance = 'calc((var(--tw-graph-protocol-node-half) + var(--tw-graph-protocol-dev-node-counter-half)) * -1)';
    $devCounterOffset = match ($direction) {
        'left-right' => [
            'start' => ['x' => $counterDistance, 'y' => $counterDistance],
            'end' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
        ],
        'right-left' => [
            'start' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
            'end' => ['x' => $counterDistance, 'y' => $counterDistance],
        ],
        'bottom-top' => [
            'start' => ['x' => $counterDistance, 'y' => $counterDistance],
            'end' => ['x' => $counterDistance, 'y' => $negativeCounterDistance],
        ],
        'top-bottom' => [
            'start' => ['x' => $negativeCounterDistance, 'y' => $negativeCounterDistance],
            'end' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
        ],
        default => [
            'start' => ['x' => $counterDistance, 'y' => $counterDistance],
            'end' => ['x' => $negativeCounterDistance, 'y' => $counterDistance],
        ],
    };
    $labelPairs = collect([
        [
            'anchor' => 'start',
            'visible' => $nodeStart,
            'x' => data_get($segment, 'anchorStart.x', '0rem'),
            'y' => data_get($segment, 'anchorStart.y', '0rem'),
            'labels' => $nodeLabels($nodeStartValue),
        ],
        [
            'anchor' => 'end',
            'visible' => $nodeEnd,
            'x' => data_get($segment, 'anchorEnd.x', '0rem'),
            'y' => data_get($segment, 'anchorEnd.y', '0rem'),
            'labels' => $nodeLabels($nodeEndValue),
        ],
    ]);
    $devCounterPairs = collect([
        [
            'anchor' => 'start',
            'visible' => $nodeStart,
            'counter' => data_get($segment, 'devCounterStart', 'S'),
            'id' => $id . '.node.start',
            'x' => data_get($segment, 'anchorStart.x', '0rem'),
            'y' => data_get($segment, 'anchorStart.y', '0rem'),
            'offsetX' => $devCounterOffset['start']['x'],
            'offsetY' => $devCounterOffset['start']['y'],
        ],
        [
            'anchor' => 'end',
            'visible' => $nodeEnd,
            'counter' => data_get($segment, 'devCounterEnd', 'E'),
            'id' => $id . '.node.end',
            'x' => data_get($segment, 'anchorEnd.x', '0rem'),
            'y' => data_get($segment, 'anchorEnd.y', '0rem'),
            'offsetX' => $devCounterOffset['end']['x'],
            'offsetY' => $devCounterOffset['end']['y'],
        ],
    ]);
    $boxPadding = '0.35rem';
    $pathBoxX = match ($direction) {
        'left-right' => data_get($segment, 'anchorStart.x', '0rem'),
        'right-left' => data_get($segment, 'anchorEnd.x', '0rem'),
        default => 'calc(' . data_get($segment, 'anchorStart.x', '0rem') . ' - var(--tw-graph-protocol-node-half))',
    };
    $pathBoxY = match ($direction) {
        'top-bottom' => data_get($segment, 'anchorEnd.y', '0rem'),
        'left-right', 'right-left' => 'calc(' . data_get($segment, 'anchorStart.y', '0rem') . ' - var(--tw-graph-protocol-node-half))',
        default => data_get($segment, 'anchorStart.y', '0rem'),
    };
    $pathBoxWidth = $isHorizontal
        ? data_get($segment, 'length', '4rem')
        : 'var(--tw-graph-protocol-node-size)';
    $pathBoxHeight = $isHorizontal
        ? 'var(--tw-graph-protocol-node-size)'
        : data_get($segment, 'length', '4rem');
@endphp

<x-translation-workbench::ui.tw-graph.dev-box
    :id="$id . '.dev-box'"
    :x="'calc(' . $pathBoxX . ' - ' . $boxPadding . ')'"
    :y="'calc(' . $pathBoxY . ' - ' . $boxPadding . ')'"
    :width="'calc(' . $pathBoxWidth . ' + (' . $boxPadding . ' * 2))'"
    :height="'calc(' . $pathBoxHeight . ' + (' . $boxPadding . ' * 2))'"
    color="sky"
    :label="$id"
    :dev="$devMode"
/>

<x-translation-workbench::ui.tw-graph.primitives.line
    :id="$id"
    :direction="$direction"
    :length="data_get($segment, 'length', '4rem')"
    :start-x="data_get($segment, 'anchorStart.x', '0rem')"
    :start-y="data_get($segment, 'anchorStart.y', '0rem')"
    :end-x="data_get($segment, 'anchorEnd.x', '0rem')"
    :end-y="data_get($segment, 'anchorEnd.y', '0rem')"
    :node-start="$nodeStartDot"
    :node-end="$nodeEndDot"
    :node-start-size="data_get($segment, 'nodeStartSize')"
    :node-end-size="data_get($segment, 'nodeEndSize')"
    :gradient="data_get($segment, 'gradient', false)"
    :cap="data_get($segment, 'cap', false)"
    :cap-start="data_get($segment, 'capStart', false)"
    :cap-end="data_get($segment, 'capEnd')"
    :cap-length="data_get($segment, 'capLength', '1.25rem')"
    :dashed="data_get($segment, 'dashed', false)"
    :color="$color"
    :z-index="$zIndex"
/>

@foreach ($devCounterPairs as $devCounterPair)
    <x-translation-workbench::ui.tw-graph.primitives.dev-node-counter
        :id="$devCounterPair['id']"
        :dev="$devMode && $devCounterPair['visible']"
        :anchor-x="$devCounterPair['x']"
        :anchor-y="$devCounterPair['y']"
        :offset-x="$devCounterPair['offsetX']"
        :offset-y="$devCounterPair['offsetY']"
        :counter="$devCounterPair['counter']"
        :color="data_get($segment, 'devCounterColor', 'zinc')"
    />
@endforeach

@foreach ($labelPairs as $labelPair)
    @if ($labelPair['visible'])
        @foreach (collect($labelPair['labels'])->take(2) as $labelIndex => $label)
            @continue(blank($label))

            @php
                $requestedSide = data_get($label, 'side');
                $side = $requestedSide ?: ($isHorizontal ? ($labelIndex === 0 ? 'top' : 'bottom') : ($labelIndex === 0 ? 'right' : 'left'));
                if ($isHorizontal && ! in_array($side, ['top', 'bottom'], true)) {
                    $side = $labelIndex === 0 ? 'top' : 'bottom';
                }
                if (! $isHorizontal && ! in_array($side, ['left', 'right'], true)) {
                    $side = $labelIndex === 0 ? 'right' : 'left';
                }
                $labelId = $id . '.label.' . $labelPair['anchor'] . '.' . ($labelIndex + 1);
            @endphp

            <x-translation-workbench::ui.tw-graph.segments.label
                :id="$labelId"
                :label="$label"
                :side="$side"
                :anchor-x="$labelPair['x']"
                :anchor-y="$labelPair['y']"
                :color="$color"
            />
        @endforeach
    @endif
@endforeach
