{{-- resources/views/components/ui/tw-graph/path-main.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.path-main />

    Optional:
    height="h-16"
    width="w-1.5"
    class="..."
--}}

@props([
    'height' => 'h-16',
    'width' => 'w-1.5',
])

<div {{ $attributes->class('tw-graph-path-main flex justify-center') }}>
    <x-ui.tw-graph.path-segment
        height="{{ $height }}"
        width="{{ $width }}"
    />
</div>
