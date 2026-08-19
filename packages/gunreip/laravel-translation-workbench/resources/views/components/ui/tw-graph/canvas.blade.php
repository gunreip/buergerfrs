{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph-protocol/canvas.blade.php --}}
{{--
    Package-local graph protocol canvas.

    Rendering rule:
    Canvas reads resolved JSON protocol data only. It does not calculate graph
    geometry and does not call App-level graph components.
--}}

@props([
    'protocol' => [],
    'direction' => 'bottom-top',
    'dev' => false,
])

@php
    $flattenSegments = function (array $protocol): array {
        $segments = [];
        $append = function (?array $segment, ?string $fallbackId = null) use (&$segments): void {
            if (! $segment) {
                return;
            }

            if ($fallbackId && blank(data_get($segment, 'id'))) {
                $segment['id'] = $fallbackId;
            }

            $segments[] = $segment;
        };

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            $append(data_get($path, 'segments.start'));

            foreach (data_get($path, 'segments.paths', []) as $segment) {
                $append($segment);
            }

            $append(data_get($path, 'segments.end'));
        }

        foreach (['left', 'right'] as $side) {
            foreach (data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.merge.segments", []) as $segment) {
                $append($segment);
            }

            $append(data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd.segment"));

            foreach (data_get($protocol, "twGraph.strang.merge.{$side}.extensions", []) as $extension) {
                foreach (data_get($extension, 'segments', []) as $segment) {
                    $append($segment);
                }
            }

            foreach (data_get($protocol, "twGraph.strang.branch.{$side}", []) as $branch) {
                foreach (data_get($branch, 'segments', []) as $segment) {
                    $append($segment);
                }

                foreach (data_get($branch, 'extensions', []) as $extension) {
                    foreach (data_get($extension, 'segments', []) as $segment) {
                        $append($segment);
                    }
                }
            }
        }

        return $segments;
    };

    $terminalTexts = function (array $protocol) use ($direction): array {
        $texts = [];

        foreach (data_get($protocol, 'twGraph.strang.trunk.trunk.paths', []) as $path) {
            $startSegment = data_get($path, 'segments.start') ?: collect(data_get($path, 'segments.paths', []))->first();
            $endSegment = data_get($path, 'segments.end') ?: collect(data_get($path, 'segments.paths', []))->last();

            if (filled(data_get($path, 'textStart')) && is_array($startSegment)) {
                $texts[] = [
                    'id' => data_get($path, 'id', 'trunk.path') . '.text-start',
                    'text' => data_get($path, 'textStart'),
                    'anchorX' => data_get($startSegment, 'anchorStart.x', '0rem'),
                    'anchorY' => data_get($startSegment, 'anchorStart.y', '0rem'),
                    'side' => data_get($path, 'textStartConnectorPlacement', $direction === 'top-bottom' ? 'top' : 'bottom'),
                    'connectorLength' => data_get($path, 'textStartConnectorLength', '2rem'),
                    'connectorGap' => data_get($path, 'textStartConnectorGap', '0.75rem'),
                    'badgeColor' => data_get($startSegment, 'color', data_get($path, 'color', 'green')),
                    'color' => data_get($startSegment, 'color', data_get($path, 'color', 'green')),
                ];
            }

            if (filled(data_get($path, 'textEnd')) && is_array($endSegment)) {
                $texts[] = [
                    'id' => data_get($path, 'id', 'trunk.path') . '.text-end',
                    'text' => data_get($path, 'textEnd'),
                    'anchorX' => data_get($endSegment, 'anchorEnd.x', '0rem'),
                    'anchorY' => data_get($endSegment, 'anchorEnd.y', '0rem'),
                    'side' => data_get($path, 'textEndConnectorPlacement', $direction === 'top-bottom' ? 'bottom' : 'top'),
                    'connectorLength' => data_get($path, 'textEndConnectorLength', '2rem'),
                    'connectorGap' => data_get($path, 'textEndConnectorGap', '0.75rem'),
                    'badgeColor' => data_get($endSegment, 'color', data_get($path, 'color', 'green')),
                    'color' => data_get($endSegment, 'color', data_get($path, 'color', 'green')),
                ];
            }
        }

        foreach (['left', 'right'] as $side) {
            $mergeEnd = data_get($protocol, "twGraph.strang.merge.{$side}.merge.paths.mergeEnd");
            $segment = data_get($mergeEnd, 'segment');

            if (filled(data_get($mergeEnd, 'textEnd')) && is_array($segment)) {
                $texts[] = [
                    'id' => data_get($mergeEnd, 'id', "merge.{$side}.path.merge-end") . '.text-end',
                    'text' => data_get($mergeEnd, 'textEnd'),
                    'anchorX' => data_get($segment, data_get($mergeEnd, 'textEndAnchor', 'anchorEnd') . '.x', data_get($segment, 'anchorEnd.x', '0rem')),
                    'anchorY' => data_get($segment, data_get($mergeEnd, 'textEndAnchor', 'anchorEnd') . '.y', data_get($segment, 'anchorEnd.y', '0rem')),
                    'side' => data_get($mergeEnd, 'textEndConnectorPlacement', 'bottom'),
                    'connectorLength' => data_get($mergeEnd, 'textEndConnectorLength', '1.5rem'),
                    'connectorGap' => data_get($mergeEnd, 'textEndConnectorGap', '0.35rem'),
                    'badgeColor' => data_get($segment, 'color', 'amber'),
                    'color' => data_get($segment, 'color', 'amber'),
                ];
            }
        }

        return $texts;
    };

    $segments = collect($flattenSegments((array) $protocol));
    $texts = collect($terminalTexts((array) $protocol));
@endphp

<div class="tw-graph-protocol-canvas content-center">
    @foreach ($segments as $segment)
        @php
            $type = data_get($segment, 'type', 'path');
            $segmentName = data_get($segment, 'segment');
        @endphp

        @if ($type === 'node')
            {{-- Path nodes are rendered by their owning segments via ::before/::after. --}}
        @elseif ($type === 'arc')
            <x-translation-workbench::ui.tw-graph.segments.arc
                :segment="$segment"
                :dev="$dev"
            />
        @elseif ($segmentName === 'trunk-start')
            <x-translation-workbench::ui.tw-graph.segments.start
                :segment="$segment"
                :dev="$dev"
            />
        @elseif ($segmentName === 'trunk-end' || (bool) data_get($segment, 'cap', false))
            <x-translation-workbench::ui.tw-graph.segments.end
                :segment="$segment"
                :dev="$dev"
            />
        @else
            <x-translation-workbench::ui.tw-graph.segments.path
                :segment="$segment"
                :dev="$dev"
            />
        @endif
    @endforeach

    @foreach ($texts as $text)
        <x-translation-workbench::ui.tw-graph.segments.label
            :id="$text['id']"
            :label="[
                'text' => $text['text'],
                'connectorLength' => $text['connectorLength'],
                'connectorGap' => $text['connectorGap'],
                'badgeColor' => $text['badgeColor'],
                'color' => $text['color'],
            ]"
            :anchor-x="$text['anchorX']"
            :anchor-y="$text['anchorY']"
            :side="$text['side']"
            :color="$text['color']"
        />
    @endforeach
</div>
