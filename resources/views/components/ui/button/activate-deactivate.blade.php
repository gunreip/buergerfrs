{{-- resources/views/components/ui/button/activate-deactivate.blade.php --}}

{{-- Component-Calls
<x-ui.button.activate-deactivate :active="false" language="Deutsch" />
<x-ui.button.activate-deactivate :active="true" language="Deutsch" />
<x-ui.button.activate-deactivate :active="false" language="Deutsch" icon="check" />
<x-ui.button.activate-deactivate :active="true" language="Deutsch" :icon="false" />
--}}

@props([
    'active' => false,
    'language' => '',
    'icon' => true,
    'type' => 'button',
    'size' => null,
])

@php
    $isActive = (bool) $active;
    $languageLabel = trim((string) $language);
    $languageLabel = $languageLabel !== '' ? $languageLabel : __('admin.translation_list.meta.language');

    $label = $isActive
        ? __('ui.button.activate_deactivate.deactivate_language', ['language' => $languageLabel])
        : __('ui.button.activate_deactivate.activate_language', ['language' => $languageLabel]);

    $resolvedIcon = match (true) {
        $icon === false => null,
        is_string($icon) && trim($icon) !== '' => trim($icon),
        $isActive => 'x',
        default => 'check',
    };

    $variant = $isActive ? 'danger' : 'primary';
    $color = $isActive ? 'bg-red-500' : 'bg-green-500';
@endphp

@if ($resolvedIcon !== null)
    <flux:button.group class="flex w-full">
        <flux:button
            class="w-12 justify-center hover:cursor-pointer"
            type="{{ $type }}"
            icon="{{ $resolvedIcon }}"
            {{ $attributes }}
            :variant="$variant"
            :color="$color"
            :size="$size"
        ></flux:button>
        <flux:separator vertical />
        <flux:button
            class="ellipsis min-w-0 flex-1 justify-start overflow-hidden hover:cursor-pointer"
            type="{{ $type }}"
            {{ $attributes }}
            :variant="$variant"
            :color="$color"
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
        :variant="$variant"
        :color="$color"
        :size="$size"
    >
        {{ $label }}
    </flux:button>
@endif
