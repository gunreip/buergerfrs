{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/parts/chain.blade.php --}}
{{--
    Part: chain

    Usage:
    <x-translation-workbench::ui.tw-graph.parts.chain
        :parts="$parts"
        :anchor-start="['x' => '0rem', 'y' => '0rem']"
    />

    Part role:
    Coordinate-only wrapper for hand-authored part sequences. It keeps manual
    authoring focused on parts, labels, and lengths while this wrapper advances
    the next anchor from the previous part's continuation point.
--}}

@aware([
    'graphId' => null,
    'dev' => false,
    'defaultColor' => null,
    'lineLength' => null,
    'stemLength' => null,
    'bridgeLength' => null,
    'capLength' => null,
])

@props([
    'parts' => [],
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'arcRadius' => null,
    'bridgeLength' => null,
    'stemLength' => null,
    'capLength' => null,
])

@php
    $cursor = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $cursor = [
        'x' => data_get($cursor, 'x', '0rem'),
        'y' => data_get($cursor, 'y', '0rem'),
    ];
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
    $resolvedStemLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $stemLength,
        $stemLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('stem_length', $resolvedLineLength),
    );
    $resolvedArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::localOrGraphString(
        $arcRadius ?? null,
        'arc_size',
        '2.75rem',
    );
    $resolvedBridgeLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $bridgeLength,
        $bridgeLength ?? null,
        \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('bridge_length', $resolvedLineLength),
    );
    $resolvedCapLength = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphStringFor(
        $capLength ?? null,
        null,
        'cap_length',
        '1.75rem',
    );
    $add = fn(string $value, string $delta): string => in_array($delta, ['0', '0rem'], true)
        ? $value
        : 'calc(' . $value . ' + ' . $delta . ')';
    $neg = fn(string $value): string => 'calc(' . $value . ' * -1)';
    $partAnchor = static fn(array $part, array $cursor): array => [
        'x' => data_get($part, 'anchorStart.x', $cursor['x']),
        'y' => data_get($part, 'anchorStart.y', $cursor['y']),
    ];
    $advanceStart = function (array $anchor, array $part) use ($add, $neg, $resolvedStemLength): array {
        $length = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            data_get($part, 'length'),
            data_get($part, 'stemLength'),
            $resolvedStemLength,
        );

        return match (data_get($part, 'direction', 'bottom-top')) {
            'top-bottom' => ['x' => $anchor['x'], 'y' => $add($anchor['y'], $neg($length))],
            'left-right' => ['x' => $add($anchor['x'], $length), 'y' => $anchor['y']],
            'right-left' => ['x' => $add($anchor['x'], $neg($length)), 'y' => $anchor['y']],
            default => ['x' => $anchor['x'], 'y' => $add($anchor['y'], $length)],
        };
    };
    $advanceSideways = function (array $anchor, array $part) use ($add, $neg, $resolvedArcRadius, $resolvedBridgeLength): array {
        $isLeft = data_get($part, 'side', 'left') !== 'right';
        $arcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            data_get($part, 'arcRadius', data_get($part, 'arcSize')),
            null,
            $resolvedArcRadius,
        );
        $bridge = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            data_get($part, 'bridgeLength'),
            null,
            $resolvedBridgeLength,
        );
        $extension = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            data_get($part, 'extension'),
            null,
            '0rem',
        );
        $xDelta = $isLeft
            ? 'calc(' . $arcRadius . ' + ' . $bridge . ' + ' . $arcRadius . ')'
            : 'calc((' . $arcRadius . ' + ' . $bridge . ' + ' . $arcRadius . ') * -1)';
        $yDelta = in_array($extension, ['0', '0rem'], true)
            ? 'calc(' . $arcRadius . ' + ' . $arcRadius . ')'
            : 'calc(' . $arcRadius . ' + ' . $arcRadius . ' + ' . $extension . ' + ' . $extension . ')';

        return [
            'x' => $add($anchor['x'], $xDelta),
            'y' => $add($anchor['y'], $yDelta),
        ];
    };
@endphp

@foreach ($parts as $part)
    @php
        $part = is_array($part) ? $part : [];
        $type = data_get($part, 'type', 'sideways');
        $anchor = $partAnchor($part, $cursor);
        $partColor = data_get($part, 'color', $resolvedColor);
    @endphp

    @if ($type === 'start')
        <x-translation-workbench::ui.tw-graph.parts.start
            :id="data_get($part, 'id')"
            :color="$partColor"
            :direction="data_get($part, 'direction', 'bottom-top')"
            :anchor-start="$anchor"
            :length="data_get($part, 'length')"
            :node-end="data_get($part, 'nodeEnd', true)"
            :node-end-dot="data_get($part, 'nodeEndDot')"
            :node-image="data_get($part, 'nodeImage')"
            :node-label-left="data_get($part, 'nodeLabelLeft')"
            :node-label-right="data_get($part, 'nodeLabelRight')"
            :dev-counter-end="data_get($part, 'devCounterEnd', 1)"
            :dev-counter-color="data_get($part, 'devCounterColor')"
            :start-label="data_get($part, 'startLabel')"
            :z-index="data_get($part, 'zIndex', 20)"
            :dev-mode="data_get($part, 'devMode')"
        />

        @php
            $cursor = $advanceStart($anchor, $part);
        @endphp
    @elseif ($type === 'sideways')
        <x-translation-workbench::ui.tw-graph.parts.sideways
            :id="data_get($part, 'id')"
            :color="$partColor"
            :side="data_get($part, 'side', 'left')"
            :anchor-start="$anchor"
            :arc-radius="data_get($part, 'arcRadius', data_get($part, 'arcSize'))"
            :bridge-length="data_get($part, 'bridgeLength')"
            :extension="data_get($part, 'extension')"
            :node-end="data_get($part, 'nodeEnd', true)"
            :node-image="data_get($part, 'nodeImage')"
            :node-label-left="data_get($part, 'nodeLabelLeft')"
            :node-label-right="data_get($part, 'nodeLabelRight')"
            :dev-counter-end="data_get($part, 'devCounterEnd', 1)"
            :dev-counter-color="data_get($part, 'devCounterColor')"
            :z-index="data_get($part, 'zIndex', 20)"
            :dev-mode="data_get($part, 'devMode')"
        />

        @php
            $cursor = $advanceSideways($anchor, $part);
        @endphp
    @elseif ($type === 'end')
        <x-translation-workbench::ui.tw-graph.parts.end
            :id="data_get($part, 'id')"
            :color="$partColor"
            :direction="data_get($part, 'direction', 'bottom-top')"
            :anchor-start="$anchor"
            :length="data_get($part, 'length')"
            :cap-length="data_get($part, 'capLength', $resolvedCapLength)"
            :node-start="data_get($part, 'nodeStart', false)"
            :dev-counter-end="data_get($part, 'devCounterEnd', 'E')"
            :dev-counter-color="data_get($part, 'devCounterColor')"
            :end-label="data_get($part, 'endLabel')"
            :z-index="data_get($part, 'zIndex', 20)"
            :dev-mode="data_get($part, 'devMode')"
        />

        @php
            $cursor = $advanceStart($anchor, $part);
        @endphp
    @endif
@endforeach
