{{-- resources/views/components/ui/tw-graph/path-end.blade.php --}}
{{--
    Usage:
    <x-ui.tw-graph.path-end text="Trunk" />
    <x-ui.tw-graph.path-end text="Trunk" length="h-6" />

    Optional:
    text="Trunk"
    length="h-3|h-6|h-16|..."
    cap-length="w-6"
    cap-width="h-1"
    width="w-1.5"
    color="zinc|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose" Default: zinc
    class="..."

    Slot content is rendered after the default trunk end pieces.
--}}

@props([
    'text' => null,
    'length' => 'h-6',
    'capLength' => 'w-6',
    'capWidth' => 'h-1',
    'width' => 'w-1.5',
    'color' => 'zinc',
])

<div {{ $attributes->class('tw-graph-path-end') }}>
    @if (filled($text))
        <flux:badge color="{{ $color }}">
            {{ $text }}
        </flux:badge>
    @endif

    <x-ui.tw-graph.path-segment
        class="{{ filled($text) ? 'mt-2' : '' }}"
        height="{{ $capWidth }}"
        width="{{ $capLength }}"
    />
    <x-ui.tw-graph.path-segment
        height="{{ $length }}"
        width="{{ $width }}"
    />

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif
</div>
