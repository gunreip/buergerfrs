<?php

// app/Support/Locale/LocaleCode.php

namespace App\Support\Locale;

class LocaleCode
{
    public static function normalize(string $locale): string
    {
        $locale = trim(str_replace('_', '-', $locale));

        if ($locale === '') {
            return '';
        }

        $parts = explode('-', $locale);

        $normalized = [];

        if (isset($parts[0])) {
            $normalized[] = strtolower($parts[0]);
        }

        foreach (array_slice($parts, 1) as $part) {
            if (strlen($part) === 2 || strlen($part) === 3) {
                $normalized[] = strtoupper($part);

                continue;
            }

            if (strlen($part) === 4) {
                $normalized[] = ucfirst(strtolower($part));

                continue;
            }

            $normalized[] = $part;
        }

        return implode('-', $normalized);
    }

    public static function toIcu(string $locale): string
    {
        return str_replace('-', '_', self::normalize($locale));
    }

    /**
     * @return array{language: ?string, script: ?string, country: ?string, variant: ?string}
     */
    public static function parts(string $locale): array
    {
        $normalized = self::normalize($locale);

        if ($normalized === '') {
            return [
                'language' => null,
                'script' => null,
                'country' => null,
                'variant' => null,
            ];
        }

        $parts = explode('-', $normalized);

        $language = $parts[0] ?? null;
        $script = null;
        $country = null;
        $variant = null;

        foreach (array_slice($parts, 1) as $part) {
            if ($script === null && strlen($part) === 4) {
                $script = $part;

                continue;
            }

            if ($country === null && (strlen($part) === 2 || strlen($part) === 3)) {
                $country = $part;

                continue;
            }

            $variant = $variant === null ? $part : $variant . '-' . $part;
        }

        return [
            'language' => $language,
            'script' => $script,
            'country' => $country,
            'variant' => $variant,
        ];
    }
}
