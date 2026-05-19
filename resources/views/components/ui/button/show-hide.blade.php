{{-- resources/views/components/ui/button/show-hide.blade.php --}}

<flux:button
    type="button"
    :variant="$variant"
    :size="$size"
    x-on:click="{{ $state }} = ! {{ $state }}"
    x-bind:aria-expanded="{{ $state }}.toString()"
    {{ $attributes->class('shrink-0') }}
>
    <span class="inline-flex items-center gap-2">
        <flux:icon.chevron-down
            class="size-4 items-center transition-transform"
            x-bind:class="{ 'rotate-180': {{ $state }} }"
        />
        <flux:separator vertical />
        <span
            @class([$width])
            x-text="{{ $state }} ? @js(__($hideLabel)) : @js(__($showLabel))"
        ></span>
    </span>
</flux:button>
