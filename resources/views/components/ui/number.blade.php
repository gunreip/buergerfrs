{{-- Locale-aware display only; stored values and technical attributes remain unchanged. --}}
@props([
    'value' => null,
    'locale' => app()->getLocale(),
    'precision' => null,
    'maxPrecision' => null,
    'empty' => '—',
])

@php
    $formattedNumber = $value === null || $value === ''
        ? $empty
        : \Illuminate\Support\Number::format(
            $value + 0,
            precision: $precision === null ? null : (int) $precision,
            maxPrecision: $maxPrecision === null ? null : (int) $maxPrecision,
            locale: str_replace('-', '_', $locale),
        );
@endphp

<span {{ $attributes->class('tabular-nums text-[color:var(--callout-text,inherit)]') }}>{{ $formattedNumber }}</span>
