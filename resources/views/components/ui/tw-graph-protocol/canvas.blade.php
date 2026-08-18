{{-- resources/views/components/ui/tw-graph-protocol/canvas.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph-protocol.canvas :protocol="$protocol" />

    Rendering rule:
    The protocol stays chain-shaped for humans. strangs.trunk and strangs.merge
    own their compositions; canvas only flattens legacy extension/branch strangs.
--}}

@props([
    'protocol' => [],
    'direction' => 'bottom-top',
])

@php
    $flattenSegments = function (array $protocol): array {
        $segments = [];
        $append = function (?array $segment, ?string $fallbackId = null) use (&$segments): void {
            if (!$segment) {
                return;
            }

            if ($fallbackId && blank(data_get($segment, 'id'))) {
                $segment['id'] = $fallbackId;
            }

            $segments[] = $segment;
        };

        foreach (['left', 'right'] as $side) {
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

    $segments = collect($flattenSegments((array) $protocol));
@endphp

<div class="tw-graph-protocol-canvas content-center">
    <x-ui.tw-graph-protocol.strangs.trunk
        :trunk="data_get($protocol, 'twGraph.strang.trunk.trunk', [])"
        :direction="$direction"
    />

    <x-ui.tw-graph-protocol.strangs.merge :merge="data_get($protocol, 'twGraph.strang.merge', [])" />

    @foreach ($segments as $segment)
        @php($type = data_get($segment, 'type', 'path'))
        @php($segmentName = data_get($segment, 'segment'))

        @if ($type === 'node')
            {{-- PathNodes are rendered by their owning segments via ::before/::after. --}}
        @elseif ($type === 'arc')
            <x-ui.tw-graph-protocol.segment.arc :segment="$segment" />
        @else
            <x-ui.tw-graph-protocol.segment.path :segment="$segment" />
        @endif
    @endforeach
</div>
