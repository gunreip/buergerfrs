{{-- resources/views/components/ui/button/edit.blade.php --}}

{{-- Component-Calls
<x-ui.button.edit />
<x-ui.button.edit icon="square-pen" />
<x-ui.button.edit :icon="false" />
<x-ui.button.edit label="{{ __('Edit') }}" />
--}}

{{-- edit  → sky/primary, square-pen, Edit --}}

@props([
    'label' => __('Edit'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'square-pen',
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
            color="sky"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="primary"
            color="sky"
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
        color="sky"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
