{{-- resources/views/components/ui/locale/flag.blade.php --}}

{{--
/**
 * Locale flag component.
 *
 * Renders a circular country marker from the CC part of a locale input.
 * Examples:
 * - de-AT -> AT
 * - de_CH -> CH
 * - AT    -> AT
 *
 * If the icon is unavailable, the component falls back to a circular text badge.
 *
 * Props:
 * - locale: string Locale/country input where only the CC part is used.
 * - size: string Visual size token (xs|sm|md|lg|xl) or custom class string.
 * - title: ?string Optional accessible label (aria-label fallback).
 */
--}}

@props(['locale', 'size' => 'sm', 'title' => null])

@php
    $rawInput = trim((string) $locale);
    $normalizedInput = strtolower(str_replace('_', '-', $rawInput));
    $parts = array_values(array_filter(explode('-', $normalizedInput), static fn (string $part): bool => $part !== ''));
    $alphaParts = array_values(array_filter(array_map(
        static fn(string $part): string => preg_replace('/[^a-z]/i', '', $part) ?? '',
        $parts,
    )));

    $language = $alphaParts !== [] ? strtolower((string) $alphaParts[0]) : '';
    $regionRaw = count($parts) >= 2 ? strtolower((string) end($parts)) : '';
    $regionAlpha = preg_replace('/[^a-z]/i', '', $regionRaw) ?? '';

    $countryAliases = (array) config('buergerfrs-flags.country_aliases', [
        'eu' => 'european_union',
        'uk' => 'gb',
    ]);

    $macroregionAliases = (array) config('buergerfrs-flags.macroregion_aliases', []);
    $numericRegionOverrides = (array) config('buergerfrs-flags.language_numeric_region_overrides', []);

    $resolveCountryToken = static function (string $token) use ($countryAliases): string {
        $normalized = strtolower(trim($token));

        return $countryAliases[$normalized] ?? $normalized;
    };

    static $languageDefaultCountries = null;

    if ($languageDefaultCountries === null) {
        $languageDefaultCountries = [];

        $mapPath = base_path('vendor/outhebox/blade-flags/config/language-countries.json');

        if (is_file($mapPath)) {
            $decoded = json_decode((string) file_get_contents($mapPath), true);

            if (is_array($decoded)) {
                foreach ($decoded as $lang => $mapping) {
                    $defaultCountry = strtolower((string) ($mapping['default'] ?? ''));

                    if ($defaultCountry !== '') {
                        $languageDefaultCountries[strtolower((string) $lang)] = $defaultCountry;
                    }
                }
            }
        }

        foreach ((array) config('blade-flags.language_overrides', []) as $lang => $override) {
            $overrideCountry = strtolower((string) ($override['default'] ?? ''));

            if ($overrideCountry !== '') {
                $languageDefaultCountries[strtolower((string) $lang)] = $overrideCountry;
            }
        }
    }

    $sizeClass = match ($size) {
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        'xl' => 'h-8 w-8',
        default => (string) $size,
    };

    $isUppercaseCcInput = preg_match('/^[A-Z]{2}$/', $rawInput) === 1;
    $candidates = [];

    if ($isUppercaseCcInput) {
        $cc = strtolower($rawInput);
        $resolvedCc = $resolveCountryToken($cc);

        $candidates[] = 'flag-flat-country-' . $cc;
        $candidates[] = 'flag-flat-country-' . $resolvedCc;
        $candidates[] = 'flag-country-' . $cc;
        $candidates[] = 'flag-country-' . $resolvedCc;
        $candidates[] = 'flag-circle-country-' . $cc;
        $candidates[] = 'flag-circle-country-' . $resolvedCc;
    } elseif (count($parts) >= 2) {
        if (preg_match('/^[0-9]{3}$/', $regionRaw) === 1) {
            $languageOverrides = (array) ($numericRegionOverrides[$language] ?? []);
            $mappedCountry = strtolower((string) ($languageOverrides[$regionRaw] ?? ($macroregionAliases[$regionRaw] ?? '')));

            if ($mappedCountry !== '') {
                $resolvedMappedCountry = $resolveCountryToken($mappedCountry);
                $candidates[] = 'flag-circle-country-' . $mappedCountry;
                $candidates[] = 'flag-circle-country-' . $resolvedMappedCountry;
            }
        }

        if ($regionAlpha !== '') {
            $resolvedRegion = $resolveCountryToken($regionAlpha);
            $candidates[] = 'flag-circle-country-' . $regionAlpha;
            $candidates[] = 'flag-circle-country-' . $resolvedRegion;
        }

        if ($language !== '' && $regionRaw !== '') {
            $candidates[] = 'flag-circle-language-' . $language . '-' . $regionRaw;
        }

        if ($language !== '') {
            $candidates[] = 'flag-circle-language-' . $language;

            $languageDefaultCountry = strtolower((string) ($languageDefaultCountries[$language] ?? ''));

            if ($languageDefaultCountry !== '') {
                $candidates[] = 'flag-circle-country-' . $languageDefaultCountry;
            }
        }
    } elseif ($language !== '') {
        // Keep the agreed behavior for ll inputs, with language/country fallback for special sets.
        $candidates[] = 'flag-circle-country-' . $language;
        $candidates[] = 'flag-circle-language-' . $language;

        $languageDefaultCountry = strtolower((string) ($languageDefaultCountries[$language] ?? ''));

        if ($languageDefaultCountry !== '') {
            $candidates[] = 'flag-circle-country-' . $languageDefaultCountry;
        }
    }

    $candidates = array_values(array_unique(array_filter($candidates, static fn(string $name): bool => $name !== '')));
    $component = null;
    $label = $title ?: strtoupper((string) $locale);

    static $availableIconTokens = null;

    if ($availableIconTokens === null) {
        $scanTokens = static function (string $directory, string $filenamePrefix): array {
            if (! is_dir($directory)) {
                return [];
            }

            $entries = scandir($directory) ?: [];
            $tokens = [];

            foreach ($entries as $entry) {
                if (! str_ends_with($entry, '.svg') || ! str_starts_with($entry, $filenamePrefix)) {
                    continue;
                }

                $tokens[] = strtolower(substr($entry, strlen($filenamePrefix), -4));
            }

            return array_values(array_unique(array_filter($tokens, static fn (string $token): bool => $token !== '')));
        };

        $availableIconTokens = [
            'flag-country-' => array_fill_keys($scanTokens(base_path('vendor/outhebox/blade-flags/resources/svg'), 'country-'), true),
            'flag-flat-country-' => array_fill_keys($scanTokens(base_path('vendor/outhebox/blade-flags/resources/svg-flat'), 'flat-country-'), true),
            'flag-circle-country-' => array_fill_keys($scanTokens(base_path('vendor/outhebox/blade-flags/resources/svg-circle'), 'circle-country-'), true),
            'flag-circle-language-' => array_fill_keys($scanTokens(base_path('vendor/outhebox/blade-flags/resources/svg-circle'), 'circle-language-'), true),
        ];
    }

    $isAvailableCandidate = static function (string $candidate) use ($availableIconTokens): bool {
        foreach ($availableIconTokens as $prefix => $tokens) {
            if (! str_starts_with($candidate, $prefix)) {
                continue;
            }

            $token = strtolower(substr($candidate, strlen($prefix)));

            return isset($tokens[$token]);
        }

        return false;
    };

    $fallbackText = '--';

    if (count($alphaParts) >= 2) {
        $fallbackText = strtoupper(substr((string) $alphaParts[0], 0, 1) . substr((string) $alphaParts[1], 0, 1));
    } elseif (count($alphaParts) === 1) {
        $fallbackText = strtoupper(substr((string) $alphaParts[0], 0, 2));
    }

    if ($fallbackText === '') {
        $fallbackText = '--';
    }

    $iconSvg = null;

    foreach ($candidates as $candidate) {
        if (! $isAvailableCandidate($candidate)) {
            continue;
        }

        try {
            $iconSvg = svg($candidate, 'h-full w-full')->toHtml();
            $component = $candidate;
            break;
        } catch (\Throwable) {
            $iconSvg = null;
        }
    }
@endphp

@if ($iconSvg !== null)
    <span
        role="img"
        aria-label="{{ $label }}"
        {{ $attributes->class('inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full align-middle ' . $sizeClass) }}
    >
        {!! $iconSvg !!}
    </span>
@else
    <span
        role="img"
        aria-label="{{ $label }}"
        {{ $attributes->class('inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-100 text-[10px] font-semibold leading-none text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 ' . $sizeClass) }}
    >
        {{ $fallbackText }}
    </span>
@endif
