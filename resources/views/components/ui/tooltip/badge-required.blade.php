{{-- resources/views/components/ui/tooltip/badge-required.blade.php --}}

@props([
    'label' => __('Required'),
])

<flux:badge
    {{ $attributes->class(['ml-2 text-xs'])->merge([
        'color' => 'red',
        'inset' => 'top bottom',
    ]) }}
>
    {{ $label }}
</flux:badge>
