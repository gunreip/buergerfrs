{{-- resources/views/components/ui/button/reset.blade.php --}}

{{-- Component-Calls
<x-ui.button.reset />
<x-ui.button.reset icon="rotate-ccw" />
<x-ui.button.reset :icon="false" />
<x-ui.button.reset label="{{ __('ui.button.reset.reset') }}" />
--}}

{{-- reset  → green/filled, rotate-ccw, Reset --}}

{{--
TODO: check tooltip.trigger-functionaltity, hover state
--}}

@props([
    'label' => __('ui.button.reset.reset'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'rotate-ccw',
    };
@endphp

@if ($resolvedIcon !== null)
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="filled"
            color="green"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="filled"
            color="green"
            :size="$size"
        >
            <x-ui.tooltip.trigger
                :title="__('ui.button.reset.reset')"
                :text="__('Reset the form to its initial state.')"
            >
                {{ $label }}
            </x-ui.tooltip.trigger>
        </flux:button>
    </flux:button.group>
@else
    <flux:button
        class="hover:cursor-pointer"
        type="{{ $type }}"
        {{ $attributes }}
        variant="filled"
        color="green"
        :size="$size"
    >
        <x-ui.tooltip.trigger
            :title="__('ui.button.reset.reset')"
            :text="__('Reset the form to its initial state.')"
        >
            {{ $label }}
        </x-ui.tooltip.trigger>
    </flux:button>
@endif
