{{-- resources/views/components/ui/safe-flux-icon.blade.php --}}

@props([
    'name' => '',
    'category' => 'role_user_management',
    'variant' => 'micro',
])

@php
    $registry = app(\App\Support\Icons\IconRegistry::class);

    $icon = $registry->resolve((string) $name, (string) $category);
    $fallback = $registry->fallback();

    $isFallback = ($icon['name'] ?? '') === ($fallback['name'] ?? 'file-x');

    $iconView = (string) ($icon['view'] ?? ($fallback['view'] ?? 'flux.icon.file-x'));

    if (!\Illuminate\Support\Facades\View::exists($iconView)) {
        $iconView = 'flux.icon.file-x';
        $isFallback = true;
    }
@endphp

@include($iconView, [
    'variant' => $variant,
    'attributes' => $attributes->class([
        'text-red-400' => $isFallback,
    ]),
])
