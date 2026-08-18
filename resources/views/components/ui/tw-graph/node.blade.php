{{-- resources/views/components/ui/tw-graph/node.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.node />

    With branches/label:
    <x-ui.tw-graph.node>
        ...
    </x-ui.tw-graph.node>

    Optional:
    class="..."
--}}

<div {{ $attributes->class('tw-graph-node') }}>
    {{ $slot }}
    <div class="tw-graph-node-dot"></div>
</div>
