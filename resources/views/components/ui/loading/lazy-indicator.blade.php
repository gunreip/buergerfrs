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
    'textClass' => 'text-lg font-extralight',
    'colorClass' => 'text-sky-700 dark:text-sky-300',
    'boxClass' => 'min-h-24 w-full rounded-lg border border-zinc-800/10 dark:border-white/20',
    'wrapperClass' => null,
    'contentClass' => null,
])

@php
    $targetAttribute = filled($target) ? (string) $target : null;
@endphp

<div
    {{ $attributes->class(['hidden', $wrapperClass => filled($wrapperClass)]) }}
    wire:loading.delay.class.remove="hidden"
    wire:loading.delay.class="block"
    @if ($targetAttribute !== null) wire:target="{{ $targetAttribute }}" @endif
>
    <div @class(['flex items-center justify-center px-4 py-3', $boxClass])>
        <div @class([
            'inline-flex items-center gap-2 whitespace-nowrap rounded-md px-3 py-2',
            $colorClass,
            $contentClass => filled($contentClass),
        ])>
            <flux:icon.loading class="{{ $iconClass }} shrink-0" />
            <flux:text
                class="{{ $textClass }}"
                inline
            >
                {{ $text }}
            </flux:text>
        </div>
    </div>
</div>
