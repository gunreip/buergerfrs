{{-- SWITCH evaluates one expression; closed case routes merge; explicit fall-through enters the next action. --}}
@aware(['graphId' => null, 'color' => null, 'dev' => false])
@php
    $inheritedColor = $color;
    $inheritedDev = $dev;
@endphp
@props([
    'id' => null,
    'attachTo' => null,
    'anchorStart' => ['x' => '0rem', 'y' => '0rem'],
    'side' => 'left',
    'direction' => 'bottom-top',
    'caseExpression' => ['text' => ['SWITCH expression'], 'width' => 'halfLong'],
    'cases' => [],
    'caseDefault' => ['text' => ['Default action'], 'width' => 'halfLong'],
    'stemLength' => '10rem',
    'arcRadius' => '2.75rem',
    'bridgeLength' => '2rem',
    'color' => null,
    'devMode' => null,
    'zIndex' => 20,
])
@php
    $resolvedGraphId = $graphId ?: 'tw-graph';
    $id = filled($id) ? (string) $id : $resolvedGraphId . '.switch';
    $resolvedColor = $color ?? $inheritedColor ?? 'zinc';
    $resolvedDev = $devMode ?? $inheritedDev;
    $previousRootIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::enter($id);
    try {
@endphp
@php
    if (!in_array($side, ['left', 'right'], true) || !in_array($direction, ['bottom-top', 'top-bottom'], true)) {
        throw new \InvalidArgumentException('flow-switch-case requires left/right and bottom-top/top-bottom.');
    }
    if (!is_array($cases) || $cases === []) {
        throw new \InvalidArgumentException('flow-switch-case requires at least one case.');
    }
    $rows = [];
    $keys = ['default'];
    foreach ($cases as $case) {
        $key = is_array($case) ? (string) ($case['key'] ?? '') : '';
        if ($key === '' || in_array($key, $keys, true)) {
            throw new \InvalidArgumentException('Case keys must be nonempty and unique; default is reserved.');
        }
        $keys[] = $key;
        $rows[] = [
            'key' => $key,
            'label' => $case['label'] ?? 'CASE ' . $key,
            'entries' => $case['entries'] ?? [],
            'entryStemLength' => $case['entryStemLength'] ?? null,
            'exitLabel' => $case['exitLabel'] ?? (!empty($case['fallThrough']) ? 'fall-through' : 'break'),
            'bridgeOutLength' => data_get($case, 'actionLabel.bridgeOutLength'),
            'fallThroughJoinLength' => $case['fallThroughJoinLength'] ?? $arcRadius,
            'fallThrough' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($case['fallThrough'] ?? false),
            'return' => \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool(data_get($case, 'actionLabel.return', true)),
            'stemLength' => $case['stemLength'] ?? ($rows === [] ? data_get($caseExpression, 'stemLength') : null) ?? $stemLength,
            'action' => $case['actionLabel'] ?? ['text' => ['Case action']],
        ];
    }
    $rows[] = [
        'key' => 'default',
        'label' => $caseDefault === false ? false : (data_get($caseDefault, 'label') ?? 'DEFAULT'),
        'exitLabel' => data_get($caseDefault, 'exitLabel') ?? 'END SWITCH',
        'action' => $caseDefault === false ? null : $caseDefault,
        'bypass' => $caseDefault === false,
        'bridgeOutLength' => data_get($caseDefault, 'bridgeOutLength'),
        'stemLength' => data_get($caseDefault, 'stemLength') ?? $stemLength,
    ];
    foreach ($rows as $row) {
        if (!empty($row['fallThrough']) && !$row['return']) {
            throw new \InvalidArgumentException('A CASE cannot combine fallThrough with actionLabel.return = false.');
        }
    }
    $widths = [];
    foreach ($rows as &$row) {
        $row['color'] = data_get($row['action'], 'color') ?? $resolvedColor;
        $row['action'] = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($row['action'], 'center', $row['color']);
        if (!empty($row['bypass'])) {
            continue;
        }
        if ($row['action'] === null) {
            throw new \InvalidArgumentException('Each switch route requires an action label.');
        }
        $widths[] = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($row['action']);
    }
    unset($row);
    foreach ($rows as $row) {
        $entryKeys = [];
        foreach ($row['entries'] ?? [] as $entry) {
            if (array_key_exists('stemLength', $entry)) {
                throw new \InvalidArgumentException('Use cases[].entryStemLength for uniform CASE entry spacing; entries[].stemLength is not supported.');
            }
            $entryKey = (string) ($entry['key'] ?? '');
            if ($entryKey === '' || in_array($entryKey, $entryKeys, true)) {
                throw new \InvalidArgumentException('Grouped CASE entries require unique, nonempty keys.');
            }
            $entryKeys[] = $entryKey;
        }
    }
    $hasGroups = collect($rows)->contains(fn ($row) => !empty($row['entries']));
    $bridge = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::bridgeLength($bridgeLength);
    $span = 'calc(max(' . implode(', ', $widths) . ') + (' . $bridge . ' * 2))';
    $groupWidth = $bridge;
    $fusionRadius = 'calc(' . $arcRadius . ' / 2)';
    // Two opposed arcs per arm: symmetric inputs span four fusion radii.
    $fusionEntrySpacing = 'max(3rem, calc(' . $fusionRadius . ' * 4))';
    foreach ($rows as $row) {
        if (!empty($row['entries']) && $row['entryStemLength'] !== null) {
            $entrySpacing = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression((string) $row['entryStemLength']);
            if ($entrySpacing === null || $entrySpacing < 3) {
                throw new \InvalidArgumentException('cases[].entryStemLength must resolve to at least 3rem.');
            }
        }
    }
    if ($hasGroups) {
        $span = 'calc(' . $span . ' + ' . $groupWidth . ')';
    }
    $normalizeCaseLabel = static function (mixed $label, string $color, string $width = 'halfLong'): ?array {
        if (is_array($label) && array_is_list($label)) {
            $label = ['text' => $label];
        }
        $normalized = \Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel::normalize($label, null, $color);

        return $normalized === null ? null : array_replace(['width' => $width], $normalized);
    };
    $counter = 2;
    $input = filled($attachTo) ? \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $attachTo) : null;
    $input = $input ?: $anchorStart;
@endphp
<x-translation-workbench::ui.tw-graph.strang.flow-step
    :id="$id . '.expression'" :anchor-start="$input" :direction="$direction"
    :step-label="$caseExpression" :color="$resolvedColor" :dev-mode="$resolvedDev" :z-index="$zIndex"
/>
@php
    $cursor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $id . '.expression.anchorNode-end');
@endphp
@foreach ($rows as $index => $row)
    @php
        $routeId = !empty($row['bypass']) ? $id . '.bypass' : $id . '.case.' . $row['key'];
        $incomingJoinLength = $index > 0 && !empty($rows[$index - 1]['fallThrough']) ? $rows[$index - 1]['fallThroughJoinLength'] : null;
        $incomingJoinCounter = $incomingJoinLength !== null ? $counter++ : false;
        $caseLabel = $normalizeCaseLabel($row['label'], $row['color']);
        $exitLabel = $normalizeCaseLabel($row['exitLabel'], $resolvedColor, 'half');
        $rowBridge = !empty($row['bypass']) ? $span : 'calc((' . $span . ' - ' . \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($row['action']) . ') / 2)';
    @endphp
    @if (!empty($row['entries']))
        @php
            $fusionInputs = [];
            $rowBridge = 'calc(' . $rowBridge . ' - (' . $groupWidth . ' / 2))';
        @endphp
        @foreach ($row['entries'] as $entry)
            @php
                $entryId = $routeId . '.entries.' . $entry['key'];
                $entryColor = $entry['color'] ?? $row['color'];
                $entryLabel = $normalizeCaseLabel($entry['label'] ?? 'CASE ' . $entry['key'], $entryColor);
                $entryLength = $loop->first ? $row['stemLength'] : ($row['entryStemLength'] ?? $fusionEntrySpacing);
            @endphp
            <x-translation-workbench::ui.tw-graph.parts.start
                :id="$entryId . '.entry'" :anchor-start="$cursor" :direction="$direction"
                :length="$entryLength" :gradient="false"
                :node-label-left="$side === 'right' ? $entryLabel : null"
                :node-label-right="$side === 'left' ? $entryLabel : null"
                :color="$resolvedColor" :dev-counter-end="$counter++" :dev-mode="$resolvedDev" :z-index="$zIndex"
            />
            @php
                $cursor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $entryId . '.entry.anchorNode-end');
            @endphp
            @php
                $horizontal = $side === 'left' ? 'right-left' : 'left-right';
                $sx = $side === 'left' ? -1 : 1;
                $sy = $direction === 'bottom-top' ? 1 : -1;
                $laneEnd = ['x' => 'calc(' . $cursor['x'] . ' + (' . $bridge . ' * ' . $sx . '))', 'y' => $cursor['y']];
            @endphp
            <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                'id' => $entryId . '.bridge', 'anchorStart' => $cursor, 'anchorEnd' => $laneEnd,
                'direction' => $horizontal, 'length' => $bridge, 'color' => $entryColor,
                'dev' => $resolvedDev, 'zIndex' => $zIndex,
            ]" />
            @php
                $fusionInputs[] = ['key' => $entry['key'], 'anchor' => $laneEnd, 'color' => $entryColor];
                \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $entryId . '.anchorNode-end', $laneEnd);
            @endphp
        @endforeach
        @if (count($fusionInputs) > 1)
            <x-translation-workbench::ui.tw-graph.parts.fusion
                :id="$routeId . '.fusion'" :inputs="$fusionInputs" :direction="$horizontal"
                :arc-radius="$fusionRadius" :color="$row['color']"
                :dev-mode="$resolvedDev" :dev-counter-end="$counter++" :z-index="$zIndex"
            />
            @php
                $actionStart = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $routeId . '.fusion.anchorNode-end');
            @endphp
        @else
            @php
                $actionStart = $laneEnd;
                $rowBridge = 'calc(' . $rowBridge . ' + (' . $arcRadius . ' / 2))';
            @endphp
        @endif
    @else
    <x-translation-workbench::ui.tw-graph.parts.start
        :id="$routeId . '.entry'" :anchor-start="$cursor" :direction="$direction"
        :length="$row['stemLength']" :gradient="false"
        :node-label-left="$side === 'right' ? $caseLabel : null"
        :node-label-right="$side === 'left' ? $caseLabel : null"
        :color="$resolvedColor" :dev-counter-end="$counter++" :dev-mode="$resolvedDev" :z-index="$zIndex"
    />
    @php
        $cursor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $routeId . '.entry.anchorNode-end');
    @endphp
        @php $actionStart = $cursor; @endphp
    @endif
    @if (!empty($row['entries']))
        @php
            $fusionSaving = count($fusionInputs) > 1
                ? 'calc(' . $arcRadius . ' - ((' . $actionStart['x'] . ' - ' . $laneEnd['x'] . ') * ' . $sx . '))'
                : '0rem';
            $actionBridgeIn = 'calc(' . $rowBridge . ' + ' . $fusionSaving . ')';
            if (empty($row['fallThrough']) && $row['bridgeOutLength'] !== null) {
                $actionBridgeIn = 'calc(' . $actionBridgeIn . ' + ' . $rowBridge . ' - ' . $row['bridgeOutLength'] . ')';
            }
            $actionGeometry = \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::geometry($actionStart, $horizontal, \Gunreip\TranslationWorkbench\Support\TwGraph\LabelBridge::labelWidth($row['action']), $actionBridgeIn, $row['bridgeOutLength'] ?? $rowBridge);
            $actionEnd = $actionGeometry['anchorEnd'];
            $routeEnd = ['x' => 'calc(' . $actionEnd['x'] . ' + (' . $arcRadius . ' * ' . $sx . '))', 'y' => 'calc(' . $actionEnd['y'] . ' + (' . $arcRadius . ' * ' . $sy . '))'];
        @endphp
        <x-translation-workbench::ui.tw-graph.segments.label-bridge
            :id="$routeId . '.bridge1'" :anchor-start="$actionStart" :direction="$horizontal"
            :label="$row['action']" :bridge-in-join-length="$incomingJoinLength" :dev-counter-join="$incomingJoinCounter" :bridge-length="$actionBridgeIn" :geometry="$actionGeometry" :dev-counter-end="false"
            :color="$row['color']" :dev="$resolvedDev" :z-index="$zIndex"
        />
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
            'id' => $routeId . '.arc-out', 'anchorStart' => $actionEnd, 'anchorEnd' => $routeEnd,
            'startAnchor' => $sy > 0 ? 's' : 'n', 'endAnchor' => $sx > 0 ? 'e' : 'w',
            'arcSize' => $arcRadius, 'color' => $row['color'], 'dev' => $resolvedDev, 'zIndex' => $zIndex,
            'nodeEnd' => true, 'nodeEndDot' => $exitLabel !== null, 'jointArrowEnd' => $exitLabel === null,
            'jointArrowEndDirection' => $sy > 0 ? 'top' : 'bottom', 'devCounterEnd' => $counter++,
            'endLabel' => $exitLabel === null ? null : array_replace($exitLabel, ['side' => $side]),
        ]" />
        @php
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $routeId . '.anchorNode-end', $routeEnd);
        @endphp
    @else
    @php
        $actionBridgeIn = empty($row['fallThrough']) && $row['bridgeOutLength'] !== null
            ? 'calc((' . $rowBridge . ' * 2) - ' . $row['bridgeOutLength'] . ')'
            : $rowBridge;
    @endphp
    <x-translation-workbench::ui.tw-graph.parts.sideways
        :id="$routeId" :anchor-start="$actionStart" :side="$side === 'left' ? 'right' : 'left'"
        :direction="$direction" :arc-radius="$arcRadius" :bridge-length="$actionBridgeIn"
        :bridge-in-join-length="$incomingJoinLength" :dev-counter-join="$incomingJoinCounter"
        :bridge-label="$row['action']" :bridge-out-length="$row['bridgeOutLength']" :color="$row['color']"
        :node-label-left="$side === 'left' ? $exitLabel : null"
        :node-label-right="$side === 'right' ? $exitLabel : null"
        :dev-counter-end="$counter++" :dev-mode="$resolvedDev" :z-index="$zIndex"
    />
    @endif
    @php
        $rows[$index]['actionEntry'] = !empty($row['entries']) ? $actionStart : [
            'x' => 'calc(' . $actionStart['x'] . ($side === 'left' ? ' - ' : ' + ') . $arcRadius . ')',
            'y' => 'calc(' . $actionStart['y'] . ($direction === 'bottom-top' ? ' + ' : ' - ') . $arcRadius . ')',
        ];
        if ($incomingJoinLength !== null) {
            $targetBridgeId = $routeId . (!empty($row['bypass']) ? '.bridge1' : '.bridge1.bridge-in');
            $rows[$index]['actionEntry'] = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $targetBridgeId . '.anchorNode-join');
        }
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $routeId . '.anchorNode-action', $rows[$index]['actionEntry']);
        $rows[$index]['end'] = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $routeId . '.anchorNode-end');
    @endphp
@endforeach
@php
    // The shared break rail keeps its own X coordinate even when an action
    // leaves early through a separate fall-through connection.
    $railX = $rows[count($rows) - 1]['end']['x'];
    $railActive = false;
    foreach ($rows as &$railRow) {
        $railRow['railEnd'] = array_replace($railRow['end'], ['x' => $railX]);
        if (empty($railRow['fallThrough'])) {
            $railActive = $railActive || ($railRow['return'] ?? false);
        }
        $railRow['renderReturn'] = $railActive && ($railRow['return'] ?? false);
    }
    unset($railRow);
@endphp
@foreach ($rows as $index => $row)
    @continue($loop->last)
    @if (!empty($row['fallThrough']))
        @php
            $target = $rows[$index + 1]['actionEntry'];
            $turnRadius = $arcRadius;
            $joinStart = [
                'x' => 'calc(' . $target['x'] . ($side === 'left' ? ' + ' : ' - ') . $turnRadius . ')',
                'y' => 'calc(' . $target['y'] . ($direction === 'bottom-top' ? ' - ' : ' + ') . $turnRadius . ')',
            ];
            $crossLength = 'calc((' . $joinStart['x'] . ' - ' . $row['end']['x'] . ') * ' . ($side === 'left' ? '1' : '-1') . ' - (2 * ' . $turnRadius . '))';
            $riseLength = 'calc((' . $joinStart['y'] . ' - ' . $row['end']['y'] . ') * ' . ($direction === 'bottom-top' ? '1' : '-1') . ' - (2 * ' . $turnRadius . '))';
            foreach ([$crossLength, $riseLength] as $fallThroughLength) {
                if (\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression($fallThroughLength) < 0) {
                    throw new \InvalidArgumentException('Fall-through requires more room: increase the next CASE stemLength or the action bridge length.');
                }
            }
            $fallThroughId = $id . '.case.' . $row['key'] . '.fall-through';
        @endphp
        <x-translation-workbench::ui.tw-graph.parts.sideways
            :id="$fallThroughId" :anchor-start="$row['end']" :side="$side"
            :direction="$direction" :arc-radius="$turnRadius" :bridge-length="$crossLength"
            :color="$row['color']" :joint-arrow-end="true" :dev-counter-end="$counter++"
            :dev-mode="$resolvedDev" :z-index="$zIndex"
        />
        <x-translation-workbench::ui.tw-graph.parts.start
            :id="$fallThroughId . '.stem'"
            :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($resolvedGraphId, $fallThroughId . '.anchorNode-end')"
            :direction="$direction" :length="$riseLength" :gradient="false"
            :node-end="true" :joint-arrow-end="true" :dev-counter-end="$counter++" :color="$row['color']"
            :dev-mode="$resolvedDev" :z-index="$zIndex"
        />
        <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
            'id' => $fallThroughId . '.join', 'anchorStart' => $joinStart, 'anchorEnd' => $target,
            'startAnchor' => $side === 'left' ? 'e' : 'w',
            'endAnchor' => $direction === 'bottom-top' ? 'n' : 's',
            // The destination bridge owns the joining Dot; this arc passes underneath.
            'arcSize' => $turnRadius, 'color' => $row['color'], 'dev' => $resolvedDev, 'zIndex' => $zIndex - 1,
            'nodeEnd' => false, 'nodeEndDot' => false, 'jointArrowEnd' => false, 'devCounterEnd' => false,
        ]" />
        @php
            \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $fallThroughId . '.join.anchorNode-end', $target);
        @endphp
    @endif
    @php
        $next = $rows[$index + 1]['railEnd'];
        \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.case.' . $row['key'] . '.anchorNode-return', $next);
        $length = $direction === 'bottom-top'
            ? 'calc(' . $next['y'] . ' - ' . $row['end']['y'] . ')'
            : 'calc(' . $row['end']['y'] . ' - ' . $next['y'] . ')';
    @endphp
    @continue(!$row['renderReturn'])
    <x-translation-workbench::ui.tw-graph.parts.start
        :id="$id . '.case.' . $row['key'] . '.return'" :anchor-start="$row['railEnd']"
        :direction="$direction" :length="$length" :gradient="false"
        :node-end="false" :dev-counter-end="false" :color="$resolvedColor"
        :dev-mode="$resolvedDev" :z-index="$zIndex"
    />
@endforeach
@php
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-start', $input);
    $output = $rows[count($rows) - 1]['end'];
    $output['color'] = $resolvedColor;
    foreach (array_slice($rows, 0, -1) as $index => $returnRow) {
        $returnPaths = [];
        $returnOutputs = [$id . '.anchorNode-end'];
        foreach (array_slice($rows, $index + 1, -1) as $followingRow) {
            if ($followingRow['renderReturn']) {
                $returnPaths[] = $id . '.case.' . $followingRow['key'] . '.return';
            }
            $returnOutputs[] = $id . '.case.' . $followingRow['key'] . '.anchorNode-return';
        }
        \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::rail(
            $resolvedGraphId, $id . '.case.' . $returnRow['key'] . '.anchorNode-return', $returnPaths, $returnOutputs,
        );
    }
    \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::put($resolvedGraphId, $id . '.anchorNode-end', $output);
@endphp
@php
    } finally {
        \Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier::restore($previousRootIdentifier);
    }
@endphp
