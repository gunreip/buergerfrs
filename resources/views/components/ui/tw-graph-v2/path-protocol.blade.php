{{-- resources/views/components/ui/tw-graph-v2/path-protocol.blade.php --}}
{{--
    Frozen anchor-points successor: render from a path protocol instead of
    recreating coordinates in each CSS selector.

    Usage:
    <x-ui.tw-graph-v2.path-protocol
        :protocol="$protocol"
        :show-json="true"
    />

    Protocol contract:
    Every segment owns anchorStart and anchorEnd. The next segment should use
    the previous segment's anchorEnd as its anchorStart. Coordinates are CSS
    lengths relative to the graph center/bottom origin.
--}}

@props([
    'protocol' => [],
    'showJson' => true,
])

@php
    $flattenSegments = function (array $protocol): array {
        $segments = [];
        $appendSegment = function (?array $segment, ?string $fallbackId = null) use (&$segments): void {
            if (! $segment) {
                return;
            }

            if ($fallbackId && blank(data_get($segment, 'id'))) {
                $segment['id'] = $fallbackId;
            }

            $segments[] = $segment;
        };

        $appendSegment(data_get($protocol, 'trunk.start.segment'), data_get($protocol, 'trunk.start.id'));

        foreach (data_get($protocol, 'trunk.nodes', []) as $node) {
            $appendSegment(data_get($node, 'segment'), data_get($node, 'id'));

            foreach (['left', 'right'] as $side) {
                foreach (data_get($node, "merge.{$side}.chain", []) as $segment) {
                    $appendSegment($segment);
                }

                foreach (data_get($node, "merge.{$side}.extensions", []) as $extension) {
                    foreach (data_get($extension, 'chain', []) as $segment) {
                        $appendSegment($segment);
                    }
                }

                foreach (data_get($node, "branches.{$side}", []) as $branch) {
                    foreach (data_get($branch, 'chain', []) as $segment) {
                        $appendSegment($segment);
                    }

                    foreach (data_get($branch, 'extensions', []) as $extension) {
                        foreach (data_get($extension, 'chain', []) as $segment) {
                            $appendSegment($segment);
                        }
                    }
                }
            }
        }

        $appendSegment(data_get($protocol, 'trunk.end.segment'), data_get($protocol, 'trunk.end.id'));

        return $segments;
    };

    $segments = collect(data_get($protocol, 'segments', []) ?: $flattenSegments((array) $protocol));
@endphp

<div class="tw-graph-v2-path-protocol">
    <div class="tw-graph-v2-path-protocol-canvas">
        @foreach ($segments as $segment)
            @php
                $type = data_get($segment, 'type', 'path');
                $direction = data_get($segment, 'direction', 'bottom-top');
                $id = data_get($segment, 'id', 'segment-' . $loop->iteration);
                $color = data_get($segment, 'color', data_get($protocol, 'color', 'cyan'));
                $colorRgb = \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '6 182 212');
                $startX = data_get($segment, 'anchorStart.x', '0rem');
                $startY = data_get($segment, 'anchorStart.y', '0rem');
                $endX = data_get($segment, 'anchorEnd.x', $startX);
                $endY = data_get($segment, 'anchorEnd.y', $startY);
                $length = data_get($segment, 'length', '4rem');
                $label = data_get($segment, 'label');
            @endphp

            @if ($type === 'node')
                <span
                    class="tw-graph-v2-path-protocol-node"
                    style="--tw-graph-v2-path-protocol-x: {{ $startX }}; --tw-graph-v2-path-protocol-y: {{ $startY }}; --tw-graph-v2-local-color-rgb: {{ $colorRgb }};"
                    title="{{ $id }}"
                    data-tw-graph-path="{{ $id }}"
                    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                >
                    @if (filled($label))
                        <span class="tw-graph-v2-path-protocol-label">
                            {{ $label }}
                        </span>
                    @endif
                </span>
            @elseif ($type === 'path')
                <span
                    class="tw-graph-v2-path-protocol-path tw-graph-v2-path-protocol-path-{{ $direction }}"
                    style="--tw-graph-v2-path-protocol-start-x: {{ $startX }}; --tw-graph-v2-path-protocol-start-y: {{ $startY }}; --tw-graph-v2-path-protocol-end-x: {{ $endX }}; --tw-graph-v2-path-protocol-end-y: {{ $endY }}; --tw-graph-v2-local-path-length: {{ $length }}; --tw-graph-v2-local-color-rgb: {{ $colorRgb }};"
                    title="{{ $id }} | {{ $direction }}"
                    data-tw-graph-path="{{ $id }}"
                    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                ></span>
            @elseif ($type === 'arc')
                <span
                    class="tw-graph-v2-path-protocol-arc tw-graph-v2-path-protocol-arc-{{ $direction }}"
                    style="--tw-graph-v2-path-protocol-start-x: {{ $startX }}; --tw-graph-v2-path-protocol-start-y: {{ $startY }}; --tw-graph-v2-path-protocol-end-x: {{ $endX }}; --tw-graph-v2-path-protocol-end-y: {{ $endY }}; --tw-graph-v2-local-color-rgb: {{ $colorRgb }};"
                    title="{{ $id }} | {{ $direction }}"
                    data-tw-graph-path="{{ $id }}"
                    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
                ></span>
            @endif
        @endforeach
    </div>

    @if (filter_var($showJson, FILTER_VALIDATE_BOOLEAN))
        <pre class="tw-graph-v2-path-protocol-json">{{ json_encode($protocol, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
    @endif
</div>
