{{-- resources/views/components/ui/button/tab-scroll.blade.php --}}

{{-- Component-Calls
<x-ui.button.tab-scroll direction="first" />
<x-ui.button.tab-scroll direction="previous" />
<x-ui.button.tab-scroll direction="next" />
<x-ui.button.tab-scroll direction="last" />
--}}

@props([
    'direction',
    'type' => 'button',
    'size' => 'xs',
    'variant' => 'ghost',
    'title' => null,
    'ariaLabel' => null,
])

@php
    $resolvedTitle = $title ?: match ($direction) {
        'first' => __('First tab'),
        'previous' => __('Previous tab'),
        'next' => __('Next tab'),
        'last' => __('Last tab'),
        default => __('Scroll tabs'),
    };
    $resolvedAriaLabel = $ariaLabel ?: $resolvedTitle;
@endphp

<flux:button
    {{ $attributes->class('h-7 w-7 shrink-0 p-0 hover:cursor-pointer') }}
    type="{{ $type }}"
    :size="$size"
    :variant="$variant"
    :title="$resolvedTitle"
    aria-label="{{ $resolvedAriaLabel }}"
>
    @if ($direction === 'first')
        <flux:icon.chevrons-left class="size-4" />
    @elseif ($direction === 'previous')
        <flux:icon.chevron-left class="size-4" />
    @elseif ($direction === 'next')
        <flux:icon.chevron-right class="size-4" />
    @elseif ($direction === 'last')
        <flux:icon.chevrons-right class="size-4" />
    @else
        <flux:icon.arrow-left-right class="size-4" />
    @endif
</flux:button>
