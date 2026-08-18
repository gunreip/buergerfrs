{{-- resources/views/components/ui/tw-graph-protocol/strangs/merge.blade.php --}}
{{--
    Strang: merge

    Usage:
    <x-ui.tw-graph-protocol.strangs.merge :merge="$protocol['twGraph']['strang']['merge']" />

    Composition:
    paths.merge
    paths.merge-end

    Layer rule:
    Merge owns the left/right merge strangs. Each side is still split into
    explicit path records so the protocol does not jump from strang directly to
    loose segments.
--}}

@props([
    'merge' => [],
])

@foreach (['left', 'right'] as $side)
    @php($sideMerge = data_get($merge, "{$side}.merge"))

    @continue(! is_array($sideMerge))

    <x-ui.tw-graph-protocol.paths.merge :merge="$sideMerge" :side="$side" />
    <x-ui.tw-graph-protocol.paths.merge-end :merge="$sideMerge" :side="$side" />
@endforeach
