{{-- resources/views/components/ui/role-badge.blade.php --}}

@props([
    'label' => '',
    'badge' => null,
])

@php
    $badge = is_array($badge) ? $badge : [];

    $color = $badge['color'] ?? 'zinc';
    $variant = $badge['variant'] ?? 'subtle';
    $icon = $badge['icon'] ?? null;
@endphp

<flux:badge
    {{ $attributes->class('mr-1') }}
    :color="$color"
    :variant="$variant"
>
    @if ($icon === 'refresh-cw-off')
        <flux:icon.refresh-cw-off variant="micro" />
        &nbsp;
    @elseif ($icon === 'shield-check')
        <flux:icon.shield-check variant="micro" />
        &nbsp;
    @elseif ($icon === 'crown')
        <flux:icon.crown variant="micro" />
        &nbsp;
    @elseif ($icon === 'user')
        <flux:icon.user variant="micro" />
        &nbsp;
    @elseif ($icon === 'tag')
        <flux:icon.tag variant="micro" />
        &nbsp;
    @endif

    {{ $label }}
</flux:badge>
