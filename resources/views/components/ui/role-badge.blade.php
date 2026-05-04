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
    @if ($icon !== null && $icon !== '')
        <x-ui.safe-flux-icon
            :name="$icon"
            category="role_user_management"
            variant="micro"
        />
        &nbsp;
    @endif

    {{ $label }}
</flux:badge>
