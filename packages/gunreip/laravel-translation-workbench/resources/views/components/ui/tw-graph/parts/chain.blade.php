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
    'color' => null,
    'dev' => false,
    'lineLength' => null,
    'stemLength' => null,
    'bridgeLength' => null,
    'capLength' => null,
])

@php
    $inheritedColor = $color ?? null;



@endphp

@props([
    'parts' => [],
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'color' => null,
    'arcRadius' => null,
    'bridgeLength' => null,
    'stemLength' => null,
    'capLength' => null,
    'direction' => 'bottom-top',
])

@php
    $resolvedDirection = in_array($direction, ['bottom-top', 'top-bottom', 'left-right', 'right-left'], true)
        ? $direction
        : 'bottom-top';
    $cursor = is_array($anchorStart) ? $anchorStart : ['x' => '0rem', 'y' => '0rem'];
    $cursor = [
        'x' => data_get($cursor, 'x', '0rem'),
        'y' => data_get($cursor, 'y', '0rem'),
    ];
    $resolvedColor = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
        $color,
        $inheritedColor ?? null,
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
    $partValue = static function (array $part, string|array $keys, mixed $default = null): mixed {
        foreach ((array) $keys as $key) {
            $value = data_get($part, $key);

            if ($value !== null) {
                return $value;
            }
        }

        return $default;
    };
    $partAnchor = static fn(array $part, array $cursor): array => [
        'x' => data_get($part, 'anchorStart.x', $cursor['x']),
        'y' => data_get($part, 'anchorStart.y', $cursor['y']),
    ];
    $advanceStart = function (array $anchor, array $part) use ($add, $neg, $partValue, $resolvedStemLength, $resolvedDirection): array {
        $length = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            $partValue($part, ['length', 'stem-length', 'stem_length']),
            $partValue($part, ['stemLength', 'stem-length', 'stem_length']),
            $resolvedStemLength,
        );

        return match ($partValue($part, 'direction', $resolvedDirection)) {
            'top-bottom' => ['x' => $anchor['x'], 'y' => $add($anchor['y'], $neg($length))],
            'left-right' => ['x' => $add($anchor['x'], $length), 'y' => $anchor['y']],
            'right-left' => ['x' => $add($anchor['x'], $neg($length)), 'y' => $anchor['y']],
            default => ['x' => $anchor['x'], 'y' => $add($anchor['y'], $length)],
        };
    };
    $advanceSideways = function (array $anchor, array $part) use ($add, $neg, $partValue, $resolvedArcRadius, $resolvedBridgeLength, $resolvedDirection): array {
        $isLeft = $partValue($part, 'side', 'left') !== 'right';
        $arcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            $partValue($part, ['arcRadius', 'arc-radius', 'arc_radius', 'arcSize', 'arc-size', 'arc_size']),
            null,
            $resolvedArcRadius,
        );
        $bridge = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::string(
            $partValue($part, ['bridgeLength', 'bridge-length', 'bridge_length']),
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
        $rawYDelta = in_array($extension, ['0', '0rem'], true)
            ? 'calc(' . $arcRadius . ' + ' . $arcRadius . ')'
            : 'calc(' . $arcRadius . ' + ' . $arcRadius . ' + ' . $extension . ' + ' . $extension . ')';
        $yDelta = $partValue($part, 'direction', $resolvedDirection) === 'top-bottom'
            ? $neg($rawYDelta)
            : $rawYDelta;

        return [
            'x' => $add($anchor['x'], $xDelta),
            'y' => $add($anchor['y'], $yDelta),
        ];
    };
@endphp

@foreach ($parts as $part)
    @php
        $part = is_array($part) ? $part : [];
        $type = $partValue($part, 'type', 'sideways');
        $anchor = $partAnchor($part, $cursor);
        $partColor = $partValue($part, 'color', $resolvedColor);
    @endphp

    @if ($type === 'start')
        <x-translation-workbench::ui.tw-graph.parts.start
            :id="$partValue($part, 'id')"
            :color="$partColor"
            :direction="$partValue($part, 'direction', $resolvedDirection)"
            :anchor-start="$anchor"
            :length="$partValue($part, ['length', 'stem-length', 'stem_length'], $resolvedStemLength)"
            :node-end="$partValue($part, ['nodeEnd', 'node-end', 'node_end'], true)"
            :node-end-dot="$partValue($part, ['nodeEndDot', 'node-end-dot', 'node_end_dot'])"
            :node-image="$partValue($part, ['nodeImage', 'node-image', 'node_image'])"
            :node-label-left="$partValue($part, ['nodeLabelLeft', 'node-label-left', 'node_label_left'])"
            :node-label-right="$partValue($part, ['nodeLabelRight', 'node-label-right', 'node_label_right'])"
            :dev-counter-end="$partValue($part, ['devCounterEnd', 'dev-counter-end', 'dev_counter_end'], 1)"
            :dev-counter-color="$partValue($part, ['devCounterColor', 'dev-counter-color', 'dev_counter_color'])"
            :start-label="$partValue($part, ['startLabel', 'start-label', 'start_label'])"
            :z-index="$partValue($part, ['zIndex', 'z-index', 'z_index'], 20)"
            :dev-mode="$partValue($part, ['devMode', 'dev-mode', 'dev_mode'])"
        />

        @php
            $cursor = $advanceStart($anchor, $part);
        @endphp
    @elseif ($type === 'sideways')
        <x-translation-workbench::ui.tw-graph.parts.sideways
            :id="$partValue($part, 'id')"
            :color="$partColor"
            :direction="$partValue($part, 'direction', $resolvedDirection)"
            :side="$partValue($part, 'side', 'left')"
            :anchor-start="$anchor"
            :arc-radius="$partValue($part, ['arcRadius', 'arc-radius', 'arc_radius', 'arcSize', 'arc-size', 'arc_size'])"
            :bridge-length="$partValue($part, ['bridgeLength', 'bridge-length', 'bridge_length'], $resolvedBridgeLength)"
            :extension="data_get($part, 'extension')"
            :node-end="$partValue($part, ['nodeEnd', 'node-end', 'node_end'], true)"
            :node-image="$partValue($part, ['nodeImage', 'node-image', 'node_image'])"
            :node-label-left="$partValue($part, ['nodeLabelLeft', 'node-label-left', 'node_label_left'])"
            :node-label-right="$partValue($part, ['nodeLabelRight', 'node-label-right', 'node_label_right'])"
            :dev-counter-end="$partValue($part, ['devCounterEnd', 'dev-counter-end', 'dev_counter_end'], 1)"
            :dev-counter-color="$partValue($part, ['devCounterColor', 'dev-counter-color', 'dev_counter_color'])"
            :z-index="$partValue($part, ['zIndex', 'z-index', 'z_index'], 20)"
            :dev-mode="$partValue($part, ['devMode', 'dev-mode', 'dev_mode'])"
        />

        @php
            $cursor = $advanceSideways($anchor, $part);
        @endphp
    @elseif ($type === 'end')
        <x-translation-workbench::ui.tw-graph.parts.end
            :id="$partValue($part, 'id')"
            :color="$partColor"
            :direction="$partValue($part, 'direction', $resolvedDirection)"
            :anchor-start="$anchor"
            :length="$partValue($part, ['length', 'stem-length', 'stem_length'], $resolvedStemLength)"
            :cap-length="$partValue($part, ['capLength', 'cap-length', 'cap_length'], $resolvedCapLength)"
            :node-start="$partValue($part, ['nodeStart', 'node-start', 'node_start'], false)"
            :dev-counter-end="$partValue($part, ['devCounterEnd', 'dev-counter-end', 'dev_counter_end'], 'E')"
            :dev-counter-color="$partValue($part, ['devCounterColor', 'dev-counter-color', 'dev_counter_color'])"
            :end-label="$partValue($part, ['endLabel', 'end-label', 'end_label'])"
            :z-index="$partValue($part, ['zIndex', 'z-index', 'z_index'], 20)"
            :dev-mode="$partValue($part, ['devMode', 'dev-mode', 'dev_mode'])"
        />

        @php
            $cursor = $advanceStart($anchor, $part);
        @endphp
    @endif
@endforeach
