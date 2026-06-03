{{-- resources/views/components/ui/country/flag.blade.php --}}

{{--
/**
 * Country flag component.
 *
 * Renders a rectangular country flag icon using only the CC part of input.
 * Examples:
 * - de-AT -> AT
 * - de_AT -> AT
 * - at    -> AT
 *
 * If no icon is available, a text fallback badge with uppercase CC is rendered.
 *
 * Props:
 * - country: ?string Country code used for flag-country-* icon lookup.
 * - size: string Visual size token (xs|sm|md|lg|xl) or custom class string.
 * - title: ?string Optional accessible label (aria-label/title fallback).
 */
--}}

@props(['country' => null, 'size' => 'sm', 'title' => null])

@php
    $rawInput = trim((string) $country);
    $normalizedInput = str_replace('_', '-', strtolower($rawInput));
    $parts = array_values(array_filter(explode('-', $normalizedInput)));
    $ccToken = $parts !== [] ? (string) end($parts) : '';
    $ccToken = preg_replace('/[^a-z]/', '', $ccToken) ?? '';

    if (strlen($ccToken) > 2) {
        $ccToken = substr($ccToken, -2);
    }

    $sizeClass = match ($size) {
        'xs' => 'h-3 w-4',
        'sm' => 'h-4 w-6',
        'md' => 'h-5 w-7',
        'lg' => 'h-6 w-9',
        'xl' => 'h-8 w-12',
        default => (string) $size,
    };

    $countryAliases = (array) config('buergerfrs-flags.country_aliases', [
        'eu' => 'european_union',
        'uk' => 'gb',
    ]);

    $resolvedCcToken = $countryAliases[$ccToken] ?? $ccToken;

    $candidates = [];

    if ($ccToken !== '') {
        $candidates[] = 'flag-country-' . $ccToken;
        $candidates[] = 'flag-country-' . $resolvedCcToken;
        $candidates[] = 'flag-flat-country-' . $ccToken;
        $candidates[] = 'flag-flat-country-' . $resolvedCcToken;
        $candidates[] = 'flag-circle-country-' . $ccToken;
        $candidates[] = 'flag-circle-country-' . $resolvedCcToken;
    }

    $candidates = array_values(array_unique(array_filter($candidates, static fn(string $name): bool => $name !== '')));
    $component = null;
    $label = $title ?: strtoupper((string) $country);

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

    if (count($parts) >= 2) {
        $partA = preg_replace('/[^a-z]/i', '', (string) $parts[0]) ?? '';
        $partB = preg_replace('/[^a-z]/i', '', (string) $parts[1]) ?? '';
        $fallbackText = strtoupper(substr($partA, 0, 1) . substr($partB, 0, 1));
    } elseif (count($parts) === 1) {
        $single = preg_replace('/[^a-z]/i', '', (string) $parts[0]) ?? '';
        $fallbackText = strtoupper(substr($single, 0, 2));
    }

    if ($fallbackText === '') {
        $sanitizedRaw = preg_replace('/[^a-z]/i', '', $rawInput) ?? '';
        $fallbackText = strtoupper(substr($sanitizedRaw, 0, 2));
        $fallbackText = $fallbackText !== '' ? $fallbackText : '--';
    }

    $iconSvg = null;

    if ($candidates !== []) {
        foreach ($candidates as $candidate) {
            if (! $isAvailableCandidate($candidate)) {
                continue;
            }

            try {
                $iconSvg = svg($candidate, $sizeClass, ['title' => $label])->toHtml();
                $component = $candidate;
                break;
            } catch (\Throwable) {
                $iconSvg = null;
            }
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
        {{ $fallbackText }}
    </span>
@endif
