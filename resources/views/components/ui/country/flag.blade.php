{{-- resources/views/components/ui/country/flag.blade.php --}}

{{--
/**
 * Country flag component.
 *
 * Renders a rectangular country flag icon using a two-letter ISO country code
 * (e.g. "de", "fr", "us"). If no icon is available, a text fallback badge
 * with the uppercase country code is rendered.
 *
 * Props:
 * - country: ?string Country code used for flag-country-* icon lookup.
 * - size: string Visual size token (xs|sm|md|lg|xl) or custom class string.
 * - title: ?string Optional accessible label (aria-label/title fallback).
 */
--}}

@props(['country' => null, 'size' => 'sm', 'title' => null])

@php
    $normalizedCountry = strtolower(trim((string) $country));

    $sizeClass = match ($size) {
        'xs' => 'h-3 w-4',
        'sm' => 'h-4 w-6',
        'md' => 'h-5 w-7',
        'lg' => 'h-6 w-9',
        'xl' => 'h-8 w-12',
        default => (string) $size,
    };

    $component = $normalizedCountry !== '' ? 'flag-country-' . $normalizedCountry : null;
    $label = $title ?: strtoupper((string) $country);

    $iconSvg = null;

    if ($component) {
        try {
            static $availableFlagIcons = null;

            if ($availableFlagIcons === null) {
                $factory = app(\BladeUI\Icons\Factory::class);
                $manifest = app(\BladeUI\Icons\IconsManifest::class)->getManifest($factory->all());

                $availableFlagIcons = [];

                foreach ($manifest as $sets) {
                    foreach ($sets as $icons) {
                        foreach ($icons as $iconName) {
                            $availableFlagIcons[$iconName] = true;
                        }
                    }
                }
            }

            $iconName = \Illuminate\Support\Str::after($component, 'flag-');

            if (isset($availableFlagIcons[$iconName])) {
                $iconSvg = svg($component, $sizeClass, ['title' => $label])->toHtml();
            }
        } catch (\Throwable) {
            $iconSvg = null;
        }
    }
@endphp

@if ($iconSvg !== null)
    <span
        aria-label="{{ $label }}"
        {{ $attributes->class('inline-flex shrink-0 items-center align-middle') }}
    >
        {!! $iconSvg !!}
    </span>
@else
    <span
        aria-label="{{ $label }}"
        {{ $attributes->class('inline-flex shrink-0 items-center rounded bg-zinc-100 px-1.5 py-0.5 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400') }}
    >
        {{ strtoupper((string) $country) }}
    </span>
@endif
