{{-- resources/views/components/ui/button/close.blade.php --}}

{{-- Component-Calls
<x-ui.button.close />
<x-ui.button.close icon="x-mark" />
<x-ui.button.close icon="x-circle" />
<x-ui.button.close :icon="false" />
<x-ui.button.close label="{{ __('ui.actions.close') }}" />
--}}

{{-- close   → zinc/primary, x-mark, Close --}}

@props([
    'label' => __('ui.actions.close'),
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
            variant="primary"
            color="zinc"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="primary"
            color="zinc"
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
        variant="primary"
        color="zinc"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
