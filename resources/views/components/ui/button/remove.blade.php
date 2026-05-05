{{-- resources/views/components/ui/button/remove.blade.php --}}

{{-- Component-Calls
<x-ui.button.remove />
<x-ui.button.remove icon="trash" />
<x-ui.button.remove :icon="false" />
<x-ui.button.remove label="{{ __('Remove') }}" />
--}}

{{-- remove  → red/ghost, trash, Remove --}}

@props([
    'label' => __('Remove'),
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        default => 'trash',
    };
@endphp

@if ($resolvedIcon !== null)
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="ghost"
            color="red"
            :size="$size"
        ></flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="ghost"
            color="red"
            :size="$size"
        >
            {{ $label }}
        </flux:button>
    </flux:button.group>
@else
    <flux:button.group>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            variant="ghost"
            color="red"
            :size="$size"
        >
            {{ $label }}
        </flux:button>
        <flux:button
            class="hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            variant="ghost"
            color="red"
            :size="$size"
        >
            {{ $label }}
        </flux:button>
    </flux:button.group>
@endif
