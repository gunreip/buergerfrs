{{-- resources/views/components/ui/button/copy.blade.php --}}

{{-- Component-Calls
<x-ui.button.copy />
<x-ui.button.copy icon="clipboard" />
<x-ui.button.copy icon="copy" />
<x-ui.button.copy :icon="false" />
<x-ui.button.copy label="{{ __('ui.button.copy.copy') }}" />
--}}

{{-- copy    → sky/primary, clipboard, Copy --}}

@props([
    'label' => __('ui.button.copy.copy'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'copy',
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
