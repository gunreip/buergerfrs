{{-- resources/views/components/ui/button/show-hide.blade.php --}}

{{-- Component-Calls
<x-ui.button.show-hide state="showSourceValue" />
<x-ui.button.show-hide state="showSourceValue" show-label="Show" hide-label="Hide" />
<x-ui.button.show-hide state="showSourceValue" size="xs" />
--}}

@props([
    'state',
    'showLabel' => __('ui.button.show-hide.show'),
    'hideLabel' => __('ui.button.show-hide.hide'),
    'variant' => 'ghost',
    'size' => 'xs',
    'width' => 'min-w-10 text-left',
])

<flux:button
    type="button"
    :variant="$variant"
    :size="$size"
    x-on:click="
        {{ $state }} = ! {{ $state }};
        window.dispatchEvent(new CustomEvent('buergerfrs:refresh-show-hide-layout'));
    "
    x-bind:aria-expanded="{{ $state }}.toString()"
    {{ $attributes->class('shrink-0 hover:cursor-pointer') }}
>
    <span class="inline-flex items-center gap-2">
        <flux:icon.chevron-down
            class="size-4 items-center transition-transform"
            x-bind:class="{ 'rotate-180': {{ $state }} }"
        />

        <flux:separator vertical />

        <span
            @class(['inline-block whitespace-nowrap', $width])
            x-text="{{ $state }} ? @js($hideLabel) : @js($showLabel)"
        >
            {{ $showLabel }}
        </span>
    </span>
</flux:button>
