{{-- resources/views/components/ui/tw-graph/path-segment.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.path-segment />
    <x-ui.tw-graph.path-segment height="h-3" width="w-1.5" />
    <x-ui.tw-graph.path-segment height="h-1" width="w-6" class="mt-2" />

    Props:
    height="h-1|h-3|h-16|..."
    width="w-1.5|w-6|..."

    Purpose:
    Reusable straight trunk/path segment. Use this for vertical or short cap
    pieces instead of writing raw tw-graph-path-body divs in graph previews.
--}}

@props([
    'height' => 'h-16',
    'width' => 'w-1.5',
])

<div {{ $attributes->class(['tw-graph-path-body', $height, $width]) }}></div>
