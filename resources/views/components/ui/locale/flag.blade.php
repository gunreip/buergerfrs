{{-- resources/views/components/ui/locale/flag.blade.php --}}

{{--
/**
 * Locale flag component.
 *
 * Renders a circular locale marker from a locale code (e.g. "de", "fr-BE").
 * If a region part exists, a country-based icon variant may be selected;
 * otherwise the language-circle icon is used. If no icon is available, the
 * component falls back to a circular text badge.
 *
 * Props:
 * - locale: string Locale code in ll or ll-CC format.
 * - size: string Visual size token (xs|sm|md|lg|xl) or custom class string.
 * - title: ?string Optional accessible label (aria-label fallback).
 */
--}}

@props(['locale', 'size' => 'sm', 'title' => null])

@php
    $normalizedLocale = str_replace('_', '-', strtolower((string) $locale));
    $localeParts = array_values(array_filter(explode('-', $normalizedLocale)));

    $language = $localeParts[0] ?? '';
    $region = $localeParts[1] ?? null;

    $sizeClass = match ($size) {
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        'xl' => 'h-8 w-8',
        default => (string) $size,
    };

    $languageComponent = $language !== '' ? 'flag-circle-language-' . $language : null;

    $countryComponent = $region !== null && $region !== '' ? 'flag-country-' . strtolower($region) : null;

    $component = $countryComponent ?: $languageComponent;
    $class = $sizeClass;

    $label = $title ?: strtoupper((string) $locale);

    $fallbackTextSource = $language !== '' ? $language : preg_replace('/[^a-z]/i', '', (string) $normalizedLocale);
    $fallbackText = strtoupper(substr((string) $fallbackTextSource, 0, 2));
    $fallbackText = $fallbackText !== '' ? $fallbackText : '--';

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
                $iconSvg = svg($component, 'h-full w-full')->toHtml();
            }
        } catch (\Throwable) {
            $iconSvg = null;
        }
    }
@endphp

@if ($iconSvg !== null)
    <span
        role="img"
        aria-label="{{ $label }}"
        {{ $attributes->class('inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full align-middle ' . $class) }}
    >
        {!! $iconSvg !!}
    </span>
@else
    <span
        role="img"
        aria-label="{{ $label }}"
        {{ $attributes->class('inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-100 text-[10px] font-semibold leading-none text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 ' . $class) }}
    >
        {{ $fallbackText }}
    </span>
@endif
