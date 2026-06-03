<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$repoBase = base_path();

$collectColumnValues = static function (string $table, string $column): array {
    if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
        return [];
    }

    return DB::table($table)
        ->whereNotNull($column)
        ->distinct()
        ->pluck($column)
        ->map(static fn ($v): string => trim((string) $v))
        ->filter(static fn (string $v): bool => $v !== '')
        ->values()
        ->all();
};

$valuesBySource = [
    'translation_values.locale' => $collectColumnValues('translation_values', 'locale'),
    'translation_languages.locale' => $collectColumnValues('translation_languages', 'locale'),
    'locales.code' => $collectColumnValues('locales', 'code'),
    'country_names.locale' => $collectColumnValues('country_names', 'locale'),
    'language_names.locale' => $collectColumnValues('language_names', 'locale'),
];

$allDbCodes = collect($valuesBySource)
    ->flatten()
    ->map(static fn (string $v): string => trim($v))
    ->filter(static fn (string $v): bool => $v !== '')
    ->unique()
    ->values()
    ->all();

sort($allDbCodes);

$classifyCode = static function (string $code): string {
    $normalized = str_replace('_', '-', $code);

    if (preg_match('/^[a-z]{2}$/', $normalized) === 1) {
        return 'll';
    }

    if (preg_match('/^[A-Z]{2}$/', $code) === 1) {
        return 'CC';
    }

    if (preg_match('/^[a-z]{2}-[A-Z]{2}$/', $code) === 1) {
        return 'll-CC';
    }

    if (preg_match('/^[a-z]{2}-[0-9]{3}$/', $normalized) === 1) {
        return 'll-###';
    }

    if (preg_match('/^[a-z]{2}-[a-zA-Z0-9]{2,}$/', $normalized) === 1) {
        return 'll-special';
    }

    return 'other';
};

$circleCountry = [];
$flatCountry = [];
$circleLanguage = [];

$scanByPrefix = static function (string $dir, string $prefix): array {
    if (!is_dir($dir)) {
        return [];
    }

    $entries = scandir($dir) ?: [];
    $result = [];

    foreach ($entries as $entry) {
        if (!str_ends_with($entry, '.svg')) {
            continue;
        }

        if (!str_starts_with($entry, $prefix)) {
            continue;
        }

        $token = substr($entry, strlen($prefix), -4);

        if ($token !== '') {
            $result[] = strtolower($token);
        }
    }

    sort($result);

    return array_values(array_unique($result));
};

$circleCountry = $scanByPrefix($repoBase . '/vendor/outhebox/blade-flags/resources/svg-circle', 'circle-country-');
$flatCountry = $scanByPrefix($repoBase . '/vendor/outhebox/blade-flags/resources/svg-flat', 'flat-country-');
$circleLanguage = $scanByPrefix($repoBase . '/vendor/outhebox/blade-flags/resources/svg-circle', 'circle-language-');

$countryAliases = (array) config('buergerfrs-flags.country_aliases', [
    'eu' => 'european_union',
    'uk' => 'gb',
]);

$macroregionAliases = (array) config('buergerfrs-flags.macroregion_aliases', []);
$numericRegionOverrides = (array) config('buergerfrs-flags.language_numeric_region_overrides', []);

$languageDefaults = [];
$languageMapPath = $repoBase . '/vendor/outhebox/blade-flags/config/language-countries.json';
if (is_file($languageMapPath)) {
    $decoded = json_decode((string) file_get_contents($languageMapPath), true);
    if (is_array($decoded)) {
        foreach ($decoded as $lang => $mapping) {
            $defaultCountry = strtolower((string) ($mapping['default'] ?? ''));
            if ($defaultCountry !== '') {
                $languageDefaults[strtolower((string) $lang)] = $defaultCountry;
            }
        }
    }
}

$overrides = (array) config('blade-flags.language_overrides', []);
foreach ($overrides as $lang => $mapping) {
    $defaultCountry = strtolower((string) ($mapping['default'] ?? ''));
    if ($defaultCountry !== '') {
        $languageDefaults[strtolower((string) $lang)] = $defaultCountry;
    }
}

$analyzeCode = static function (string $code) use (
    $classifyCode,
    $countryAliases,
    $macroregionAliases,
    $numericRegionOverrides,
    $circleCountry,
    $flatCountry,
    $circleLanguage,
    $languageDefaults
): array {
    $type = $classifyCode($code);
    $raw = trim($code);
    $normalized = str_replace('_', '-', strtolower($raw));
    $parts = array_values(array_filter(explode('-', $normalized), static fn (string $p): bool => $p !== ''));

    $lang = $parts[0] ?? '';
    $regionRaw = count($parts) >= 2 ? (string) end($parts) : '';
    $regionAlpha = preg_replace('/[^a-z]/', '', $regionRaw) ?? '';

    $resolveCountryToken = static function (string $token) use ($countryAliases): string {
        $normalized = strtolower(trim($token));

        return (string) ($countryAliases[$normalized] ?? $normalized);
    };

    $candidates = [];

    if (preg_match('/^[A-Z]{2}$/', $raw) === 1) {
        $cc = strtolower($raw);
        $alias = $countryAliases[$cc] ?? $cc;

        $candidates[] = 'flat-country-' . $cc;
        $candidates[] = 'flat-country-' . $alias;
        $candidates[] = 'country-' . $cc;
        $candidates[] = 'country-' . $alias;
        $candidates[] = 'circle-country-' . $cc;
        $candidates[] = 'circle-country-' . $alias;
    } elseif (count($parts) >= 2) {
        if (preg_match('/^[0-9]{3}$/', $regionRaw) === 1) {
            $languageOverrides = (array) ($numericRegionOverrides[$lang] ?? []);
            $mappedCountry = strtolower((string) ($languageOverrides[$regionRaw] ?? ($macroregionAliases[$regionRaw] ?? '')));

            if ($mappedCountry !== '') {
                $resolvedMappedCountry = $resolveCountryToken($mappedCountry);
                $candidates[] = 'circle-country-' . $mappedCountry;
                $candidates[] = 'circle-country-' . $resolvedMappedCountry;
            }
        }

        if ($regionAlpha !== '') {
            $alias = $resolveCountryToken($regionAlpha);
            $candidates[] = 'circle-country-' . $regionAlpha;
            $candidates[] = 'circle-country-' . $alias;
        }

        if ($lang !== '' && $regionRaw !== '') {
            $candidates[] = 'circle-language-' . $lang . '-' . $regionRaw;
        }

        if ($lang !== '') {
            $candidates[] = 'circle-language-' . $lang;
            $defaultCountry = strtolower((string) ($languageDefaults[$lang] ?? ''));
            if ($defaultCountry !== '') {
                $candidates[] = 'circle-country-' . $defaultCountry;
            }
        }
    } elseif ($lang !== '') {
        $candidates[] = 'circle-country-' . $lang;
        $candidates[] = 'circle-language-' . $lang;

        $defaultCountry = strtolower((string) ($languageDefaults[$lang] ?? ''));
        if ($defaultCountry !== '') {
            $candidates[] = 'circle-country-' . $defaultCountry;
        }
    }

    $candidates = array_values(array_unique(array_filter($candidates, static fn (string $c): bool => $c !== '')));

    $resolved = null;
    foreach ($candidates as $candidate) {
        $token = str_contains($candidate, 'flat-country-') ? substr($candidate, 13)
            : (str_contains($candidate, 'circle-country-') ? substr($candidate, 15)
            : (str_contains($candidate, 'country-') ? substr($candidate, 8)
            : (str_contains($candidate, 'circle-language-') ? substr($candidate, 16) : null)));

        if ($token === null) {
            continue;
        }

        if (str_starts_with($candidate, 'flat-country-') && in_array($token, $flatCountry, true)) {
            $resolved = $candidate;
            break;
        }

        if (str_starts_with($candidate, 'circle-country-') && in_array($token, $circleCountry, true)) {
            $resolved = $candidate;
            break;
        }

        if (str_starts_with($candidate, 'country-') && (in_array($token, $flatCountry, true) || in_array($token, $circleCountry, true))) {
            $resolved = $candidate;
            break;
        }

        if (str_starts_with($candidate, 'circle-language-') && in_array($token, $circleLanguage, true)) {
            $resolved = $candidate;
            break;
        }
    }

    return [
        'code' => $code,
        'type' => $type,
        'candidates' => $candidates,
        'resolved' => $resolved,
        'needs_review' => $resolved === null,
    ];
};

$analysis = array_map($analyzeCode, $allDbCodes);

$result = [
    'summary' => [
        'db_code_count' => count($allDbCodes),
        'types' => collect($analysis)->groupBy('type')->map->count()->all(),
        'needs_review_count' => collect($analysis)->where('needs_review', true)->count(),
        'circle_country_count' => count($circleCountry),
        'flat_country_count' => count($flatCountry),
        'circle_language_count' => count($circleLanguage),
    ],
    'db_sources' => $valuesBySource,
    'analysis' => $analysis,
    'special_icons' => [
        'circle_country_contains_european_union' => in_array('european_union', $circleCountry, true),
        'flat_country_contains_european_union' => in_array('european_union', $flatCountry, true),
        'circle_language_examples_en' => array_values(array_filter($circleLanguage, static fn (string $token): bool => str_starts_with($token, 'en'))),
    ],
    'resolver_config' => [
        'country_aliases' => $countryAliases,
        'macroregion_aliases' => $macroregionAliases,
        'language_numeric_region_overrides' => $numericRegionOverrides,
    ],
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
