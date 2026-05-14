{{-- resources/views/components/ui/flux-icon.blade.php --}}

@php
    $reportIfFallback();
@endphp

@include($iconView(), [
    'variant' => $variant,
    'attributes' => $attributes->class([
        'text-red-400' => $isFallback(),
    ]),
])
