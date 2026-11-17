{{-- resources/views/components/ui/loading/lazy-indicator.blade.php --}}

{{-- Component-Calls
<x-ui.loading.lazy-indicator
    :target="$loadingTargets"
    text="{{ __('Loading data...') }}"
/>

<x-ui.loading.lazy-indicator
    target="findingsActiveTab, gotoPage"
    icon-class="size-5"
    text-class="text-sm"
/>
--}}

@props([
    'target' => null,
    'text' => __('ui.loading.lazy_indicator.loading'),
    'iconClass' => 'size-6',
    'textClass' => 'text-2xl font-extralight',
    'colorClass' => 'text-sky-700 dark:text-sky-300',
    'boxClass' => 'min-h-24 w-full rounded-lg border border-zinc-800/10 dark:border-white/20',
    'wrapperClass' => null,
    'contentClass' => null,
    'overlay' => false,
    'viewport' => false,
    'delay' => true,
])

@php
    $targetAttribute = filled($target) ? (string) $target : null;
@endphp

<div
    {{ $attributes->class([
        'hidden' => !$attributes->has('x-show'),
        'fixed inset-0 z-50 bg-black/40 backdrop-blur-xs' => $viewport,
        'absolute inset-0 z-40 rounded-lg bg-white/75 dark:bg-zinc-900/75' => $overlay && !$viewport,
        $wrapperClass => filled($wrapperClass),
    ]) }}
    @unless ($attributes->has('x-show'))
        @if ($delay)
            wire:loading.delay.class.remove="hidden"
            wire:loading.delay.class="block"
        @else
            wire:loading.class.remove="hidden"
        @endif
    @endunless
    @if ($targetAttribute !== null) wire:target="{{ $targetAttribute }}" @endif
>
    <div @class([
        'flex items-center justify-center px-4 py-3',
        'absolute inset-0' => $overlay || $viewport,
        $boxClass => !$overlay && !$viewport,
    ])>
        <div @class([
            'inline-flex items-center gap-2 whitespace-nowrap rounded-md px-3 py-2',
            'bg-white dark:bg-zinc-900' => $viewport,
            $colorClass,
            $contentClass => filled($contentClass),
        ])>
            <flux:icon.loading class="{{ $iconClass }} mr-2 size-12 shrink-0" />
            <flux:text
                class="{{ $textClass }}"
                inline
            >
                {{ $text }}
            </flux:text>
        </div>
    </div>
</div>
