<?php

// app/Support/Locale/LocaleReferenceImporter.php

namespace App\Support\Locale;

use App\Models\AddressFormat;
use App\Models\Country;
use App\Models\CountryName;
use App\Models\CountrySubdivision;
use App\Models\Language;
use App\Models\LanguageName;
use App\Models\Locale;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Subdivision\Subdivision;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Intl\Locales;

class LocaleReferenceImporter
{
    private const DEFAULT_IMPORTED_SORT_ORDER = 1000;

    private const EU_MEMBER_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'HR',
        'CY',
        'CZ',
        'DK',
        'EE',
        'FI',
        'FR',
        'DE',
        'GR',
        'HU',
        'IE',
        'IT',
        'LV',
        'LT',
        'LU',
        'MT',
        'NL',
        'PL',
        'PT',
        'RO',
        'SK',
        'SI',
        'ES',
        'SE',
    ];

    private const EEA_MEMBER_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'HR',
        'CY',
        'CZ',
        'DK',
        'EE',
        'FI',
        'FR',
        'DE',
        'GR',
        'HU',
        'IE',
        'IT',
        'LV',
        'LT',
        'LU',
        'MT',
        'NL',
        'PL',
        'PT',
        'RO',
        'SK',
        'SI',
        'ES',
        'SE',
        'IS',
        'LI',
        'NO',
    ];

    private const SCHENGEN_MEMBER_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'HR',
        'CZ',
        'DK',
        'EE',
        'FI',
        'FR',
        'DE',
        'GR',
        'HU',
        'IS',
        'IT',
        'LV',
        'LI',
        'LT',
        'LU',
        'MT',
        'NL',
        'NO',
        'PL',
        'PT',
        'RO',
        'SK',
        'SI',
        'ES',
        'SE',
        'CH',
    ];

    private const COUNTRY_META = [
        'DE' => [
            'capital' => 'Berlin',
            'continent_code' => 'EU',
            'region' => 'Europe',
            'subregion' => 'Western Europe',
            'latitude' => 51.0000000,
            'longitude' => 9.0000000,
            'emoji_flag' => '🇩🇪',
            'tld' => '.de',
            'is_independent' => true,
        ],
        'AT' => [
            'capital' => 'Vienna',
            'continent_code' => 'EU',
            'region' => 'Europe',
            'subregion' => 'Central Europe',
            'latitude' => 47.3333333,
            'longitude' => 13.3333333,
            'emoji_flag' => '🇦🇹',
            'tld' => '.at',
            'is_independent' => true,
        ],
        'CH' => [
            'capital' => 'Bern',
            'continent_code' => 'EU',
            'region' => 'Europe',
            'subregion' => 'Western Europe',
            'latitude' => 47.0000000,
            'longitude' => 8.0000000,
            'emoji_flag' => '🇨🇭',
            'tld' => '.ch',
            'is_independent' => true,
        ],
    ];

    /**
     * @return array<string, array{created: int, updated: int, skipped: int}>
     */
    public function import(
        array $displayLocales = ['de', 'en'],
        bool $dryRun = false,
        bool $withCountryMeta = false,
        bool $withAddressing = false,
        bool $withSubdivisions = false,
    ): array {
        $result = $this->emptyResult();

        if ($dryRun) {
            DB::beginTransaction();

            try {
                $result = $this->runImport($displayLocales, $withCountryMeta, $withAddressing, $withSubdivisions);
            } finally {
                DB::rollBack();
            }

            return $result;
        }

        return DB::transaction(
            fn(): array => $this->runImport($displayLocales, $withCountryMeta, $withAddressing, $withSubdivisions)
        );
    }

    /**
     * @return array<string, array{created: int, updated: int, skipped: int}>
     */
    private function runImport(
        array $displayLocales,
        bool $withCountryMeta,
        bool $withAddressing,
        bool $withSubdivisions,
    ): array {
        $result = $this->emptyResult();

        $countryMeta = $withCountryMeta
            ? $this->loadRestCountryMeta()
            : [];

        $this->importCountries($displayLocales, $result, $countryMeta, $withAddressing);
        $this->importLanguages($displayLocales, $result);
        $this->importLocales($displayLocales, $result);

        if ($withSubdivisions) {
            $this->importCountrySubdivisions($result);
        }

        return $result;
    }

    /**
     * @return array<string, array{created: int, updated: int, skipped: int}>
     */
    private function emptyResult(): array
    {
        return [
            'countries' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
            'country_names' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
            'country_subdivisions' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
            'languages' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
            'language_names' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
            'locales' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $restCountryMeta
     */
    private function importCountries(array $displayLocales, array &$result, array $restCountryMeta, bool $withAddressing): void
    {
        foreach (Countries::getCountryCodes() as $countryCode) {
            $countryCode = strtoupper($countryCode);

            $country = Country::query()->firstOrNew([
                'iso2' => $countryCode,
            ]);

            $wasRecentlyCreated = ! $country->exists;

            $countryMeta = array_replace(
                $this->countryMeta($countryCode),
                $restCountryMeta[$countryCode] ?? [],
            );

            $addressingMeta = $withAddressing
                ? $this->countryAddressingMeta($countryCode)
                : [];

            $country->fill([
                'iso3' => $countryMeta['iso3'] ?? $this->countryAlpha3($countryCode),
                'iso_numeric' => $countryMeta['iso_numeric'] ?? $this->countryNumeric($countryCode),
                'name' => $countryMeta['common_name'] ?? $this->countryName($countryCode, 'en'),
                'official_name' => $countryMeta['official_name'] ?? null,
                'common_name' => $countryMeta['common_name'] ?? $this->countryName($countryCode, 'en'),
                'native_name' => $country->native_name ?: ($countryMeta['native_name'] ?? $this->countryName($countryCode, strtolower($countryCode))),
                'phone_code' => $countryMeta['phone_code'] ?? $country->phone_code,
                'capital' => $countryMeta['capital'] ?? null,
                'continent_code' => $countryMeta['continent_code'] ?? null,
                'region' => $countryMeta['region'] ?? null,
                'subregion' => $countryMeta['subregion'] ?? null,
                'latitude' => $countryMeta['latitude'] ?? null,
                'longitude' => $countryMeta['longitude'] ?? null,
                'emoji_flag' => $countryMeta['emoji_flag'] ?? $this->emojiFlag($countryCode),
                'tld' => $countryMeta['tld'] ?? strtolower('.' . $countryCode),
                'is_independent' => $countryMeta['is_independent'] ?? null,
                'is_eu_member' => $this->isEuMember($countryCode),
                'is_eea_member' => $this->isEeaMember($countryCode),
                'is_schengen_member' => $this->isSchengenMember($countryCode),
                'postal_code_required' => $addressingMeta['postal_code_required'] ?? $country->postal_code_required,
                'postal_code_regex' => $addressingMeta['postal_code_regex'] ?? $country->postal_code_regex,
                'address_format_key' => $addressingMeta['address_format_key'] ?? $country->address_format_key,
            ]);

            if (! $country->exists) {
                $country->is_active = true;
            }

            if ((int) $country->sort_order === 0) {
                $country->sort_order = self::DEFAULT_IMPORTED_SORT_ORDER;
            }

            $country->save();

            if ($withAddressing && $addressingMeta !== []) {
                $this->upsertAddressFormat($country, $countryCode, $addressingMeta);
            }

            $result['countries'][$wasRecentlyCreated ? 'created' : 'updated']++;

            foreach ($displayLocales as $displayLocale) {
                $this->upsertCountryName($country, $displayLocale, $result);
            }
        }
    }

    private function upsertCountryName(Country $country, string $displayLocale, array &$result): void
    {
        $locale = LocaleCode::normalize($displayLocale);

        if ($locale === '') {
            $result['country_names']['skipped']++;

            return;
        }

        $name = $this->countryName($country->iso2, $locale);

        if ($name === null) {
            $result['country_names']['skipped']++;

            return;
        }

        $countryName = CountryName::query()->firstOrNew([
            'country_id' => $country->id,
            'locale' => $locale,
        ]);

        $wasRecentlyCreated = ! $countryName->exists;

        $countryName->fill([
            'name' => $name,
            'official_name' => null,
            'common_name' => $name,
            'source' => 'symfony/intl',
            'is_default' => $locale === 'en',
        ]);

        $countryName->save();

        $result['country_names'][$wasRecentlyCreated ? 'created' : 'updated']++;
    }

    private function importLanguages(array $displayLocales, array &$result): void
    {
        foreach (Languages::getNames('en') as $languageCode => $englishName) {
            $languageCode = strtolower((string) $languageCode);

            if (! preg_match('/^[a-z]{2,3}$/', $languageCode)) {
                $result['languages']['skipped']++;

                continue;
            }

            $iso6391 = strlen($languageCode) === 2 ? $languageCode : null;
            $iso6393 = $this->languageAlpha3($languageCode);

            $language = Language::query()
                ->when($iso6393 !== null, fn($query) => $query->where('iso639_3', $iso6393))
                ->when($iso6393 === null && $iso6391 !== null, fn($query) => $query->where('iso639_1', $iso6391))
                ->first();

            if (! $language) {
                $language = new Language();
            }

            $wasRecentlyCreated = ! $language->exists;

            $language->fill([
                'iso639_1' => $iso6391,
                'iso639_3' => $iso6393,
                'name' => $englishName,
                'native_name' => $language->native_name ?: $this->languageName($languageCode, $languageCode),
            ]);

            if (! $language->exists) {
                $language->is_active = true;
            }

            if ((int) $language->sort_order === 0) {
                $language->sort_order = self::DEFAULT_IMPORTED_SORT_ORDER;
            }

            $language->save();

            $result['languages'][$wasRecentlyCreated ? 'created' : 'updated']++;

            foreach ($displayLocales as $displayLocale) {
                $this->upsertLanguageName($language, $languageCode, $displayLocale, $result);
            }
        }
    }

    private function upsertLanguageName(Language $language, string $languageCode, string $displayLocale, array &$result): void
    {
        $locale = LocaleCode::normalize($displayLocale);

        if ($locale === '') {
            $result['language_names']['skipped']++;

            return;
        }

        $name = $this->languageName($languageCode, $locale);

        if ($name === null) {
            $result['language_names']['skipped']++;

            return;
        }

        $languageName = LanguageName::query()->firstOrNew([
            'language_id' => $language->id,
            'locale' => $locale,
        ]);

        $wasRecentlyCreated = ! $languageName->exists;

        $languageName->fill([
            'name' => $name,
            'native_name' => $this->languageName($languageCode, $languageCode),
            'source' => 'symfony/intl',
            'is_default' => $locale === 'en',
        ]);

        $languageName->save();

        $result['language_names'][$wasRecentlyCreated ? 'created' : 'updated']++;
    }

    private function importLocales(array $displayLocales, array &$result): void
    {
        foreach (Locales::getNames('en') as $localeCode => $displayName) {
            $code = LocaleCode::normalize((string) $localeCode);

            if ($code === '') {
                $result['locales']['skipped']++;

                continue;
            }

            $parts = LocaleCode::parts($code);

            $language = $parts['language']
                ? Language::query()
                ->where('iso639_1', $parts['language'])
                ->orWhere('iso639_3', $parts['language'])
                ->first()
                : null;

            $country = $parts['country']
                ? Country::query()->where('iso2', strtoupper($parts['country']))->first()
                : null;

            $locale = Locale::query()->firstOrNew([
                'code' => $code,
            ]);

            $wasRecentlyCreated = ! $locale->exists;

            $locale->fill([
                'normalized_code' => LocaleCode::toIcu($code),
                'language_id' => $language?->id,
                'country_id' => $country?->id,
                'script_code' => $parts['script'],
                'variant' => $parts['variant'],
                'display_name' => $this->localeDisplayName($code, 'en') ?: $displayName,
                'native_display_name' => $parts['language']
                    ? $this->localeDisplayName($code, $parts['language'])
                    : null,
            ]);

            if (! $locale->exists) {
                $locale->is_active = false;
                $locale->is_default = false;
            }

            if ((int) $locale->sort_order === 0) {
                $locale->sort_order = self::DEFAULT_IMPORTED_SORT_ORDER;
            }

            $locale->save();

            $result['locales'][$wasRecentlyCreated ? 'created' : 'updated']++;
        }

        foreach ($displayLocales as $displayLocale) {
            $this->ensureDisplayLocale($displayLocale, $result);
        }
    }

    private function ensureDisplayLocale(string $displayLocale, array &$result): void
    {
        $code = LocaleCode::normalize($displayLocale);

        if ($code === '') {
            return;
        }

        $parts = LocaleCode::parts($code);

        $language = $parts['language']
            ? Language::query()
            ->where('iso639_1', $parts['language'])
            ->orWhere('iso639_3', $parts['language'])
            ->first()
            : null;

        $country = $parts['country']
            ? Country::query()->where('iso2', strtoupper($parts['country']))->first()
            : null;

        $locale = Locale::query()->firstOrNew([
            'code' => $code,
        ]);

        $wasRecentlyCreated = ! $locale->exists;

        $locale->fill([
            'normalized_code' => LocaleCode::toIcu($code),
            'language_id' => $language?->id,
            'country_id' => $country?->id,
            'script_code' => $parts['script'],
            'variant' => $parts['variant'],
            'display_name' => $this->localeDisplayName($code, 'en'),
            'native_display_name' => $parts['language']
                ? $this->localeDisplayName($code, $parts['language'])
                : null,
            'is_active' => true,
        ]);

        if ((int) $locale->sort_order === 0) {
            $locale->sort_order = self::DEFAULT_IMPORTED_SORT_ORDER;
        }

        $locale->save();

        $result['locales'][$wasRecentlyCreated ? 'created' : 'updated']++;
    }

    private function importCountrySubdivisions(array &$result): void
    {
        $repository = new SubdivisionRepository();

        Country::query()
            ->where('is_active', true)
            ->orderBy('iso2')
            ->each(function (Country $country) use ($repository, &$result): void {
                $this->importSubdivisionLevel(
                    repository: $repository,
                    country: $country,
                    parents: [$country->iso2],
                    parentModel: null,
                    result: $result,
                    depth: 1,
                );
            });
    }

    /**
     * @param array<int, string> $parents
     */
    private function importSubdivisionLevel(
        SubdivisionRepository $repository,
        Country $country,
        array $parents,
        ?CountrySubdivision $parentModel,
        array &$result,
        int $depth,
    ): void {
        if ($depth > 3) {
            return;
        }

        try {
            $subdivisions = $repository->getAll($parents);
        } catch (\Throwable) {
            $result['country_subdivisions']['skipped']++;

            return;
        }

        foreach ($subdivisions as $subdivision) {
            if (! $subdivision instanceof Subdivision) {
                $result['country_subdivisions']['skipped']++;

                continue;
            }

            $subdivisionModel = $this->upsertCountrySubdivision(
                country: $country,
                subdivision: $subdivision,
                parentModel: $parentModel,
                depth: $depth,
                result: $result,
            );

            if ($subdivision->hasChildren()) {
                $this->importSubdivisionLevel(
                    repository: $repository,
                    country: $country,
                    parents: [...$parents, $subdivision->getId()],
                    parentModel: $subdivisionModel,
                    result: $result,
                    depth: $depth + 1,
                );
            }
        }
    }

    private function upsertCountrySubdivision(
        Country $country,
        Subdivision $subdivision,
        ?CountrySubdivision $parentModel,
        int $depth,
        array &$result,
    ): CountrySubdivision {
        $code = $this->subdivisionCode($subdivision);
        $isoCode = $this->subdivisionIsoCode($country, $subdivision);

        $subdivisionModel = CountrySubdivision::query()->firstOrNew([
            'country_id' => $country->id,
            'parent_id' => $parentModel?->id,
            'code' => $code,
        ]);

        $wasRecentlyCreated = ! $subdivisionModel->exists;

        $subdivisionModel->fill([
            'parent_id' => $parentModel?->id,
            'iso_code' => $isoCode,
            'type' => $this->subdivisionType($depth),
            'name' => $subdivision->getName(),
            'local_name' => $subdivision->getLocalName(),
            'postal_code_pattern' => $subdivision->getPostalCodePattern(),
            'is_active' => true,
        ]);

        if ((int) $subdivisionModel->sort_order === 0) {
            $subdivisionModel->sort_order = self::DEFAULT_IMPORTED_SORT_ORDER;
        }

        $subdivisionModel->save();

        $result['country_subdivisions'][$wasRecentlyCreated ? 'created' : 'updated']++;

        return $subdivisionModel;
    }

    private function subdivisionCode(Subdivision $subdivision): string
    {
        return (string) $subdivision->getId();
    }

    private function subdivisionIsoCode(Country $country, Subdivision $subdivision): ?string
    {
        $id = (string) $subdivision->getId();

        if (! preg_match('/^[A-Z0-9]{1,3}$/', $id)) {
            return null;
        }

        return $country->iso2 . '-' . $id;
    }

    private function subdivisionType(int $depth): string
    {
        return match ($depth) {
            1 => 'administrative_area',
            2 => 'locality',
            3 => 'dependent_locality',
            default => 'subdivision',
        };
    }

    /**
     * @param array{
     *     address_format_key?: string|null,
     *     format?: string,
     *     local_format?: string|null,
     *     required_fields?: array<int, string>,
     *     uppercase_fields?: array<int, string>,
     *     postal_code_regex?: string|null,
     *     administrative_area_type?: string|null,
     *     locality_type?: string|null,
     *     dependent_locality_type?: string|null,
     *     postal_code_type?: string|null
     * } $addressingMeta
     */
    private function upsertAddressFormat(Country $country, string $countryCode, array $addressingMeta): void
    {
        $key = $addressingMeta['address_format_key'] ?? null;
        $format = $addressingMeta['format'] ?? null;

        if (! is_string($key) || $key === '' || ! is_string($format) || $format === '') {
            return;
        }

        AddressFormat::query()->updateOrCreate(
            ['country_code' => strtoupper($countryCode)],
            [
                'key' => $key,
                'country_id' => $country->id,
                'format' => $format,
                'local_format' => $addressingMeta['local_format'] ?? null,
                'required_fields' => $addressingMeta['required_fields'] ?? [],
                'uppercase_fields' => $addressingMeta['uppercase_fields'] ?? [],
                'postal_code_pattern' => $addressingMeta['postal_code_regex'] ?? null,
                'administrative_area_type' => $addressingMeta['administrative_area_type'] ?? null,
                'locality_type' => $addressingMeta['locality_type'] ?? null,
                'dependent_locality_type' => $addressingMeta['dependent_locality_type'] ?? null,
                'postal_code_type' => $addressingMeta['postal_code_type'] ?? null,
                'source' => 'commerceguys/addressing',
            ],
        );
    }

    /**
     * @return array{
     *     postal_code_required?: bool,
     *     postal_code_regex?: string|null,
     *     address_format_key?: string|null,
     *     format?: string,
     *     local_format?: string|null,
     *     required_fields?: array<int, string>,
     *     uppercase_fields?: array<int, string>,
     *     administrative_area_type?: string|null,
     *     locality_type?: string|null,
     *     dependent_locality_type?: string|null,
     *     postal_code_type?: string|null
     * }
     */
    private function countryAddressingMeta(string $countryCode): array
    {
        try {
            $addressFormat = (new AddressFormatRepository())->get(strtoupper($countryCode));
        } catch (\Throwable) {
            return [];
        }

        $requiredFields = $addressFormat->getRequiredFields();
        $format = $addressFormat->getFormat();

        return [
            'postal_code_required' => in_array(AddressField::POSTAL_CODE, $requiredFields, true),
            'postal_code_regex' => $addressFormat->getPostalCodePattern(),
            'address_format_key' => $this->addressFormatKey($format),
            'format' => $format,
            'local_format' => $addressFormat->getLocalFormat(),
            'required_fields' => $requiredFields,
            'uppercase_fields' => $addressFormat->getUppercaseFields(),
            'administrative_area_type' => $addressFormat->getAdministrativeAreaType(),
            'locality_type' => $addressFormat->getLocalityType(),
            'dependent_locality_type' => $addressFormat->getDependentLocalityType(),
            'postal_code_type' => $addressFormat->getPostalCodeType(),
        ];
    }

    private function addressFormatKey(string $format): string
    {
        return sha1($format);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function loadRestCountryMeta(): array
    {
        $primary = $this->readReferenceJson('database/reference/restcountries.v3.1.json');
        $extra = $this->readReferenceJson('database/reference/restcountries.v3.1.extra.json');

        $meta = [];

        foreach ($primary as $row) {
            $countryCode = strtoupper((string) ($row['cca2'] ?? ''));

            if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
                continue;
            }

            $meta[$countryCode] = array_filter([
                'iso3' => $row['cca3'] ?? null,
                'iso_numeric' => $row['ccn3'] ?? null,
                'official_name' => $row['name']['official'] ?? null,
                'common_name' => $row['name']['common'] ?? null,
                'native_name' => $this->firstNativeCommonName($row['name']['nativeName'] ?? null),
                'capital' => $this->firstString($row['capital'] ?? null),
                'continent_code' => $this->continentCode($row['region'] ?? null, $row['subregion'] ?? null),
                'region' => $row['region'] ?? null,
                'subregion' => $row['subregion'] ?? null,
                'latitude' => $row['latlng'][0] ?? null,
                'longitude' => $row['latlng'][1] ?? null,
                'tld' => $this->firstString($row['tld'] ?? null),
                'phone_code' => $this->phoneCode($row['idd'] ?? null),
            ], fn($value): bool => $value !== null && $value !== '');
        }

        foreach ($extra as $row) {
            $countryCode = strtoupper((string) ($row['cca2'] ?? ''));

            if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
                continue;
            }

            $meta[$countryCode] ??= [];

            $meta[$countryCode] = array_replace(
                $meta[$countryCode],
                array_filter([
                    'is_independent' => is_bool($row['independent'] ?? null) ? $row['independent'] : null,
                    'continent_code' => $this->continentCodeFromContinents($row['continents'] ?? null),
                ], fn($value): bool => $value !== null && $value !== ''),
            );
        }

        return $meta;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readReferenceJson(string $relativePath): array
    {
        $path = base_path($relativePath);

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function firstString(mixed $value): ?string
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (is_string($item) && $item !== '') {
                    return $item;
                }
            }
        }

        return null;
    }

    private function firstNativeCommonName(mixed $nativeNames): ?string
    {
        if (! is_array($nativeNames)) {
            return null;
        }

        foreach ($nativeNames as $nativeName) {
            if (is_array($nativeName) && isset($nativeName['common']) && is_string($nativeName['common'])) {
                return $nativeName['common'];
            }
        }

        return null;
    }

    private function phoneCode(mixed $idd): ?string
    {
        if (! is_array($idd)) {
            return null;
        }

        $root = $idd['root'] ?? null;
        $suffixes = $idd['suffixes'] ?? null;

        if (! is_string($root) || $root === '') {
            return null;
        }

        if (! is_array($suffixes) || $suffixes === []) {
            return $root;
        }

        $firstSuffix = $this->firstString($suffixes);

        return $firstSuffix !== null ? $root . $firstSuffix : $root;
    }

    private function continentCodeFromContinents(mixed $continents): ?string
    {
        $continent = $this->firstString($continents);

        return match ($continent) {
            'Africa' => 'AF',
            'Antarctica' => 'AN',
            'Asia' => 'AS',
            'Europe' => 'EU',
            'North America' => 'NA',
            'Oceania' => 'OC',
            'South America' => 'SA',
            default => null,
        };
    }

    private function continentCode(mixed $region, mixed $subregion): ?string
    {
        if (is_string($region)) {
            $code = match ($region) {
                'Africa' => 'AF',
                'Antarctic' => 'AN',
                'Americas' => $subregion === 'South America' ? 'SA' : 'NA',
                'Asia' => 'AS',
                'Europe' => 'EU',
                'Oceania' => 'OC',
                default => null,
            };

            if ($code !== null) {
                return $code;
            }
        }

        return null;
    }

    /**
     * @return array{
     *     capital?: string,
     *     continent_code?: string,
     *     region?: string,
     *     subregion?: string,
     *     latitude?: float,
     *     longitude?: float,
     *     emoji_flag?: string,
     *     tld?: string,
     *     is_independent?: bool
     * }
     */
    private function countryMeta(string $countryCode): array
    {
        return self::COUNTRY_META[strtoupper($countryCode)] ?? [];
    }

    private function emojiFlag(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);

        if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return null;
        }

        return mb_chr(127397 + ord($countryCode[0]), 'UTF-8')
            . mb_chr(127397 + ord($countryCode[1]), 'UTF-8');
    }

    private function isEuMember(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), self::EU_MEMBER_COUNTRIES, true);
    }

    private function isEeaMember(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), self::EEA_MEMBER_COUNTRIES, true);
    }

    private function isSchengenMember(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), self::SCHENGEN_MEMBER_COUNTRIES, true);
    }

    private function countryName(string $countryCode, string $locale): ?string
    {
        try {
            return Countries::getName(strtoupper($countryCode), LocaleCode::toIcu($locale));
        } catch (\Throwable) {
            return null;
        }
    }

    private function countryAlpha3(string $countryCode): ?string
    {
        try {
            return Countries::getAlpha3Code(strtoupper($countryCode));
        } catch (\Throwable) {
            return null;
        }
    }

    private function countryNumeric(string $countryCode): ?string
    {
        try {
            return Countries::getNumericCode(strtoupper($countryCode));
        } catch (\Throwable) {
            return null;
        }
    }

    private function languageName(string $languageCode, string $locale): ?string
    {
        try {
            return Languages::getName(strtolower($languageCode), LocaleCode::toIcu($locale));
        } catch (\Throwable) {
            return null;
        }
    }

    private function languageAlpha3(string $languageCode): ?string
    {
        try {
            return Languages::getAlpha3Code(strtolower($languageCode));
        } catch (\Throwable) {
            return strlen($languageCode) === 3 ? strtolower($languageCode) : null;
        }
    }

    private function localeDisplayName(string $localeCode, string $displayLocale): ?string
    {
        try {
            return Locales::getName(LocaleCode::toIcu($localeCode), LocaleCode::toIcu($displayLocale));
        } catch (\Throwable) {
            return null;
        }
    }
}
