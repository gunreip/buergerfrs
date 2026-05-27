{{-- resources/views/components/ui/country/flag-locale.blade.php --}}

{{--
/**
 * Country flag from locale component.
 *
 * Extracts the country part (CC) from a locale (ll-CC) and renders the
 * rectangular country flag component. If no country part is present, it
 * gracefully falls back to the locale flag component.
 *
 * Props:
 * - locale: string Locale code in ll or ll-CC format.
 * - size: string Visual size token (xs|sm|md|lg|xl) or custom class string.
 * - title: ?string Optional accessible label.
 */
--}}

@props(['locale', 'size' => 'sm', 'title' => null])

@php
    $normalizedLocale = \App\Support\Locale\LocaleCode::normalize((string) $locale);
    $localeParts = \App\Support\Locale\LocaleCode::parts($normalizedLocale);

    $country = strtolower((string) ($localeParts['country'] ?? ''));
    $label = $title ?: strtoupper((string) $locale);
@endphp

@if ($country !== '')
    <x-ui.country.flag
        :country="$country"
        :size="$size"
        :title="$label"
        {{ $attributes }}
    />
@else
    <x-ui.locale.flag
        :locale="$normalizedLocale"
        :size="$size"
        :title="$label"
        {{ $attributes }}
    />
@endif
