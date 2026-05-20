{{-- resources/views/components/ui/badge/context.blade.php --}}

@props(['context', 'value', 'label' => null, 'size' => null])

@php
    $normalizedValue = strtolower((string) $value);

    $fallback = config('buergerfrs-badges.fallback', [
        'color' => 'zinc',
        'variant' => 'subtle',
    ]);

    $contexts = config('buergerfrs-badges.contexts', []);

    $badge = is_array($contexts) ? $contexts[$context][$normalizedValue] ?? $fallback : $fallback;

    $color = $badge['color'] ?? ($fallback['color'] ?? 'zinc');
    $variant = $badge['variant'] ?? ($fallback['variant'] ?? 'subtle');
    $displayLabel = $label ?? $value;
@endphp

<flux:badge
    :color="$color"
    :variant="$variant"
    :size="$size"
>
    {{ $displayLabel }}
</flux:badge>
