{{-- resources/views/components/ui/button/cancel.blade.php --}}

{{-- Component-Calls
<x-ui.button.cancel />
<x-ui.button.cancel icon="x-mark" />
<x-ui.button.cancel :icon="false" />
<x-ui.button.cancel label="{{ __('ui.button.cancel.cancel') }}" />
--}}

{{-- cancel  → red/danger, x-mark, Cancel --}}

@props([
    'label' => __('ui.button.cancel.cancel'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'x-mark',
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
            color="red"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="filled"
            color="red"
            :size="$size"
        >
            {{ $label }}
        </flux:button>
    </flux:button.group>
@else
    <flux:button
        class="hover:cursor-pointer"
        type="{{ $type }}"
        {{ $attributes }}
        variant="filled"
        color="red"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
