<?php

// app/Livewire/Admin/AppSettings.php

namespace App\Livewire\Admin;

use App\Settings\AppDisplaySettings;
use Illuminate\Support\Facades\DB;
use App\Settings\AppGeneralSettings;
use App\Support\Icons\IconRegistry;
use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Permission\Models\Role;

/**
 * Administrative settings component for locale and role-badge configuration.
 *
 * Manages:
 * - globally available primary locales
 * - active sub-locales for a selected primary language
 * - global default app locale
 */
class AppSettings extends Component
{
    private const KNOWN_PSEUDO_ROLE_BADGE_KEYS = [
        '__without_role__',
    ];

    /**
     * These primary locales must always stay enabled.
     *
     * @var array<int, string>
     */
    private const MANDATORY_AVAILABLE_LOCALES = ['en', 'de'];

    /**
     * Fallback top 10 languages by estimated total speakers (L1 + L2).
     */
    private const DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_10 = [
        'zh',
        'en',
        'hi',
        'es',
        'ar',
        'bn',
        'fr',
        'ru',
        'pt',
        'ur',
    ];

    /**
     * Fallback top 20 languages by estimated total speakers (L1 + L2).
     *
     * Includes DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_10.
     */
    private const DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_20 = [
        'zh',
        'en',
        'hi',
        'es',
        'ar',
        'bn',
        'fr',
        'ru',
        'pt',
        'ur',
        'id',
        'de',
        'ja',
        'sw',
        'tr',
        'ta',
        'vi',
        'ko',
        'te',
        'fa',
    ];

    public string $locale = 'de';

    /**
     * @var array<int, string>
     */
    public array $availableLocales = [];

    public string $selectedPrimaryLanguageCode = '';

    public string $primaryLanguageScope = 'all';

    /**
     * @var array<int, string>
     */
    public array $selectedSubLocaleCodes = [];

    /**
     * Bootstrap component state from persisted application settings.
     */
    public function mount(AppGeneralSettings $appGeneralSettings): void
    {
        $this->locale = $appGeneralSettings->locale;
        $this->availableLocales = $this->normaliseAvailableLocales($appGeneralSettings->availableLocales);
        $this->primaryLanguageScope = $this->configuredDefaultPrimaryLanguageScope();
    }

    /**
     * React to a changed primary language and load active sub-locales for it.
     */
    public function updatedSelectedPrimaryLanguageCode(mixed $value): void
    {
        $this->selectedPrimaryLanguageCode = is_string($value)
            ? LocaleCode::normalize($value)
            : '';

        $this->syncSelectedSubLocaleCodesFromDatabase();
    }

    /**
     * Keep the primary-language scope in a known state.
     */
    public function updatedPrimaryLanguageScope(mixed $value): void
    {
        $scope = is_string($value) ? strtolower(trim($value)) : 'all';
        $this->primaryLanguageScope = $this->normalizePrimaryLanguageScope($scope);
    }

    /**
     * Toggle one sub-locale in UI state and persist the resulting selection.
     */
    public function toggleSelectedSubLocale(string $locale): void
    {
        $locale = LocaleCode::normalize($locale);

        if ($locale === '') {
            return;
        }

        if (in_array($locale, $this->selectedSubLocaleCodes, true)) {
            $this->selectedSubLocaleCodes = array_values(array_diff($this->selectedSubLocaleCodes, [$locale]));
            $this->persistSelectedSubLocaleCodesForSelectedPrimaryLanguage();

            return;
        }

        $this->selectedSubLocaleCodes[] = $locale;
        $this->selectedSubLocaleCodes = array_values(array_unique($this->selectedSubLocaleCodes));

        $this->persistSelectedSubLocaleCodesForSelectedPrimaryLanguage();
    }

    /**
     * Toggle all sub-locales for the selected primary language.
     *
     * If all are currently selected, all will be deactivated.
     * Otherwise, all available sub-locales will be activated.
     */
    public function toggleAllSelectedSubLocales(): void
    {
        $subLocaleCodes = $this->subLocaleCodesForSelectedPrimaryLanguage();
        $selectedSubLocaleCodes = collect($this->selectedSubLocaleCodes)
            ->map(static fn(mixed $code): string => is_string($code) ? LocaleCode::normalize($code) : '')
            ->filter()
            ->values()
            ->all();

        if ($subLocaleCodes === []) {
            $this->selectedSubLocaleCodes = [];

            return;
        }

        $allSelected = count(array_intersect($subLocaleCodes, $selectedSubLocaleCodes)) === count($subLocaleCodes);

        if ($allSelected) {
            $this->selectedSubLocaleCodes = [];

            $this->persistSelectedSubLocaleCodesForSelectedPrimaryLanguage();

            return;
        }

        $this->selectedSubLocaleCodes = $subLocaleCodes;
        $this->persistSelectedSubLocaleCodesForSelectedPrimaryLanguage();
    }

    /**
     * Set the global application locale if it is currently available.
     */
    public function setLocale(string $locale, AppGeneralSettings $appGeneralSettings): void
    {
        $locale = LocaleCode::normalize($locale);

        $availableLocales = $this->normaliseAvailableLocales($appGeneralSettings->availableLocales);

        if (! in_array($locale, $availableLocales, true)) {
            $this->dispatch('toast', type: 'error', message: __('Invalid application language.'));

            return;
        }

        $appGeneralSettings->locale = $locale;
        $appGeneralSettings->save();

        app()->setLocale($locale);

        $this->locale = $locale;
        $this->availableLocales = $availableLocales;

        $this->dispatch('toast', type: 'success', message: __('Application language updated.'));
    }

    /**
     * Select a primary language and immediately sync its sub-language context.
     */
    public function selectPrimaryLanguage(string $locale): void
    {
        $locale = LocaleCode::normalize($locale);

        if ($locale === '') {
            return;
        }

        if (! in_array($locale, $this->selectablePrimaryLocaleCodes(), true)) {
            return;
        }

        $this->selectedPrimaryLanguageCode = $locale;
        $this->syncSelectedSubLocaleCodesFromDatabase();
    }

    /**
     * Set the global locale and keep primary-language selection in sync.
     */
    public function setLocaleAndSelectPrimary(string $locale, AppGeneralSettings $appGeneralSettings): void
    {
        $this->setLocale($locale, $appGeneralSettings);
        $this->selectPrimaryLanguage($locale);
    }

    /**
     * Activate/deactivate a primary locale and persist available locales.
     */
    public function toggleAvailableLocale(string $locale, AppGeneralSettings $appGeneralSettings): void
    {
        $locale = LocaleCode::normalize($locale);

        if ($locale === '') {
            $this->dispatch('toast', type: 'error', message: __('Invalid application language.'));

            return;
        }

        $isSelectableLocale = in_array($locale, $this->selectablePrimaryLocaleCodes(), true);

        if (! $isSelectableLocale) {
            $this->dispatch('toast', type: 'error', message: __('Invalid application language.'));

            return;
        }

        $currentLocale = LocaleCode::normalize($appGeneralSettings->locale);
        $availableLocales = $this->normaliseAvailableLocales($appGeneralSettings->availableLocales);

        if (in_array($locale, $availableLocales, true)) {
            if (in_array($locale, self::MANDATORY_AVAILABLE_LOCALES, true)) {
                $this->dispatch('toast', type: 'error', message: __('This application language is mandatory and cannot be disabled.'));

                return;
            }

            if ($locale === $currentLocale) {
                $this->dispatch('toast', type: 'error', message: __('The current application language cannot be disabled.'));

                return;
            }

            $availableLocales = array_values(array_diff($availableLocales, [$locale]));

            if ($availableLocales === []) {
                $this->dispatch('toast', type: 'error', message: __('At least one application language must remain enabled.'));

                return;
            }
        } else {
            $availableLocales[] = $locale;
        }

        $availableLocales = $this->normaliseAvailableLocales($availableLocales);

        $appGeneralSettings->availableLocales = $availableLocales;
        $appGeneralSettings->save();

        $this->availableLocales = $availableLocales;

        $this->dispatch('toast', type: 'success', message: __('Available application languages updated.'));
    }

    /**
     * Persist updated available locales from bound input values.
     *
     * Ensures:
     * - only selectable locales are stored
     * - current app locale remains available
     * - at least one locale remains enabled
     */
    public function updatedAvailableLocales(mixed $value): void
    {
        $appGeneralSettings = app(AppGeneralSettings::class);

        $selectableLocales = $this->selectablePrimaryLocaleCodes();

        $availableLocales = $this->normaliseAvailableLocales(
            is_array($value) ? $value : [$value]
        );

        $availableLocales = array_values(array_intersect($availableLocales, $selectableLocales));

        $currentLocale = LocaleCode::normalize($appGeneralSettings->locale);

        if ($currentLocale !== '' && ! in_array($currentLocale, $availableLocales, true)) {
            $availableLocales[] = $currentLocale;

            $this->dispatch(
                'toast',
                type: 'error',
                message: __('The current application language cannot be removed from the available languages.')
            );
        }

        foreach (self::MANDATORY_AVAILABLE_LOCALES as $mandatoryLocale) {
            if (! in_array($mandatoryLocale, $availableLocales, true)) {
                $availableLocales[] = $mandatoryLocale;
            }
        }

        $availableLocales = $this->normaliseAvailableLocales($availableLocales);

        if ($availableLocales === []) {
            $availableLocales = $this->normaliseAvailableLocales([$currentLocale ?: 'de']);

            $this->dispatch(
                'toast',
                type: 'error',
                message: __('At least one application language must remain enabled.')
            );
        }

        $appGeneralSettings->availableLocales = $availableLocales;
        $appGeneralSettings->save();

        $this->availableLocales = $availableLocales;

        $this->dispatch('toast', type: 'success', message: __('Available application languages updated.'));
    }

    /**
     * Build all data needed by the admin app settings view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(AppDisplaySettings $appDisplaySettings, IconRegistry $iconRegistry)
    {
        $roleBadges = (array) $appDisplaySettings->roleBadges;

        $roles = Role::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'category',
                'sort_order',
                'is_system',
                'is_assignable',
            ]);

        $roleNames = $roles
            ->pluck('name')
            ->all();

        $registeredIconOptions = $iconRegistry->roleUserManagementOptions();

        $roleBadgeRows = collect($roleBadges)
            ->map(function (array $badge, string $roleName) use ($roleNames, $iconRegistry): array {
                $iconName = (string) ($badge['icon'] ?? '');
                $resolvedIcon = $iconRegistry->resolveRoleUserManagement($iconName);
                $isPseudoRoleBadgeKey = in_array($roleName, self::KNOWN_PSEUDO_ROLE_BADGE_KEYS, true);

                return [
                    'roleName' => $roleName,
                    'displayLabel' => $isPseudoRoleBadgeKey ? __('Without role') : $roleName,
                    'isPseudoRoleBadgeKey' => $isPseudoRoleBadgeKey,
                    'roleExists' => in_array($roleName, $roleNames, true),
                    'color' => (string) ($badge['color'] ?? ''),
                    'variant' => (string) ($badge['variant'] ?? ''),
                    'icon' => $iconName,
                    'iconView' => (string) ($resolvedIcon['view'] ?? ''),
                    'iconAvailable' => isset($resolvedIcon['view']) && View::exists((string) $resolvedIcon['view']),
                    'usesFallbackIcon' => ($resolvedIcon['name'] ?? '') === ($iconRegistry->fallback()['name'] ?? 'file-x'),
                ];
            })
            ->sortBy('displayLabel', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $rolesWithoutBadge = $roles
            ->reject(fn(Role $role): bool => array_key_exists($role->name, $roleBadges))
            ->values();

        $badgeConfigsWithoutRole = collect($roleBadgeRows)
            ->filter(fn(array $row): bool => ! $row['roleExists'] && ! $row['isPseudoRoleBadgeKey'])
            ->values()
            ->all();

        $iconRegistryRows = collect($registeredIconOptions)
            ->map(function (array $icon, string $iconName): array {
                $view = (string) ($icon['view'] ?? '');

                return [
                    'name' => $iconName,
                    'label' => (string) ($icon['label'] ?? $iconName),
                    'view' => $view,
                    'available' => $view !== '' && View::exists($view),
                ];
            })
            ->values()
            ->all();

        $uiLanguageLocale = LocaleCode::normalize($this->locale);
        $uiLanguageLocale = str_starts_with($uiLanguageLocale, 'de') ? 'de' : 'en';

        $localeRows = DB::table('languages as language')
            ->leftJoin('language_names as language_name_ui', function ($join) use ($uiLanguageLocale): void {
                $join
                    ->on('language_name_ui.language_id', '=', 'language.id')
                    ->where('language_name_ui.locale', '=', $uiLanguageLocale);
            })
            ->leftJoin('language_names as language_name_en', function ($join): void {
                $join
                    ->on('language_name_en.language_id', '=', 'language.id')
                    ->where('language_name_en.locale', '=', 'en');
            })
            ->where('is_active', true)
            ->whereRaw('COALESCE(iso639_1, iso639_3) IS NOT NULL')
            ->orderBy('language.sort_order')
            ->orderByRaw('COALESCE(language_name_ui.name, language_name_en.name, language.name, language.native_name)')
            ->orderByRaw('COALESCE(iso639_1, iso639_3)')
            ->get([
                DB::raw('COALESCE(iso639_1, iso639_3) as code'),
                DB::raw('COALESCE(iso639_1, iso639_3) as normalized_code'),
                DB::raw('language.name as display_name'),
                DB::raw('language.native_name as native_display_name'),
                DB::raw('COALESCE(language_name_ui.name, language_name_en.name, language.name, language.native_name) as localized_display_name'),
                DB::raw('false as is_default'),
                'language.sort_order',
            ]);

        $primaryLocaleRows = $this->filterPrimaryLocaleRows($localeRows);
        $primaryLanguageScopeOptions = $this->primaryLanguageScopeOptions();

        $selectedPrimaryLanguageCode = LocaleCode::normalize($this->selectedPrimaryLanguageCode);

        $selectedPrimaryLanguageId = $this->selectedPrimaryLanguageId();

        $subLocaleRows = $selectedPrimaryLanguageId
            ? DB::table('locales')
            ->where('language_id', $selectedPrimaryLanguageId)
            ->where('code', '<>', $selectedPrimaryLanguageCode)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get([
                'code',
                'normalized_code',
                'display_name',
                'native_display_name',
                'is_active',
                'sort_order',
            ])
            : collect();

        $subLocaleStatsByPrimary = DB::table('languages as language')
            ->leftJoin('locales as locale', function ($join): void {
                $join
                    ->on('locale.language_id', '=', 'language.id')
                    ->whereRaw('LOWER(locale.code) <> LOWER(COALESCE(language.iso639_1, language.iso639_3))');
            })
            ->where('language.is_active', true)
            ->whereRaw('COALESCE(language.iso639_1, language.iso639_3) IS NOT NULL')
            ->groupBy(DB::raw('COALESCE(language.iso639_1, language.iso639_3)'))
            ->get([
                DB::raw('COALESCE(language.iso639_1, language.iso639_3) as code'),
                DB::raw('COUNT(locale.id) as total'),
                DB::raw('COALESCE(SUM(CASE WHEN locale.is_active IS TRUE THEN 1 ELSE 0 END), 0) as selected'),
            ])
            ->mapWithKeys(static function (object $row): array {
                $code = LocaleCode::normalize((string) $row->code);

                return [
                    $code => [
                        'total' => (int) $row->total,
                        'selected' => (int) $row->selected,
                    ],
                ];
            })
            ->all();

        $summary = [
            'settingsGroup' => 'app_display',
            'generalSettingsGroup' => 'app_general',
            'locale' => $this->locale,
            'availableLocales' => $this->availableLocales,
            'availableLocaleCount' => count($this->availableLocales),
            'roleBadgeEntries' => count($roleBadges),
            'registeredIconCategories' => count((array) config('buergerfrs-icons.categories', [])),
            'registeredRoleUserIcons' => count($registeredIconOptions),
            'rolesWithoutBadge' => $rolesWithoutBadge->count(),
            'badgeConfigsWithoutRole' => count($badgeConfigsWithoutRole),
            'missingRegisteredIcons' => collect($iconRegistryRows)
                ->where('available', false)
                ->count(),
        ];

        return view('components.admin.⚡app-settings', [
            'summary' => $summary,
            'roles' => $roles,
            'roleBadgeRows' => $roleBadgeRows,
            'rolesWithoutBadge' => $rolesWithoutBadge,
            'badgeConfigsWithoutRole' => $badgeConfigsWithoutRole,
            'iconRegistryRows' => $iconRegistryRows,
            'fallbackIcon' => $iconRegistry->fallback(),
            'locale' => $this->locale,
            'availableLocales' => $this->availableLocales,
            'localeRows' => $localeRows,
            'primaryLocaleRows' => $primaryLocaleRows,
            'primaryLanguageScopeOptions' => $primaryLanguageScopeOptions,
            'subLocaleStatsByPrimary' => $subLocaleStatsByPrimary,
            'selectedPrimaryLanguageCode' => $selectedPrimaryLanguageCode,
            'selectedSubLocaleCodes' => $this->selectedSubLocaleCodes,
            'subLocaleRows' => $subLocaleRows,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function selectablePrimaryLocaleCodes(): array
    {
        return DB::table('languages')
            ->where('is_active', true)
            ->whereRaw('COALESCE(iso639_1, iso639_3) IS NOT NULL')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->orderByRaw('COALESCE(iso639_1, iso639_3)')
            ->selectRaw('COALESCE(iso639_1, iso639_3) as code')
            ->pluck('code')
            ->map(static fn(string $locale): string => LocaleCode::normalize($locale))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function subLocaleCodesForSelectedPrimaryLanguage(): array
    {
        $selectedPrimaryLanguageCode = LocaleCode::normalize($this->selectedPrimaryLanguageCode);
        $selectedPrimaryLanguageId = $this->selectedPrimaryLanguageId();

        if ($selectedPrimaryLanguageCode === '' || $selectedPrimaryLanguageId === null) {
            return [];
        }

        return DB::table('locales')
            ->where('language_id', (int) $selectedPrimaryLanguageId)
            ->where('code', '<>', $selectedPrimaryLanguageCode)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->pluck('code')
            ->map(static fn(string $code): string => LocaleCode::normalize($code))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Resolve the currently selected primary language database id.
     */
    private function selectedPrimaryLanguageId(): ?int
    {
        $selectedPrimaryLanguageCode = LocaleCode::normalize($this->selectedPrimaryLanguageCode);

        if ($selectedPrimaryLanguageCode === '') {
            return null;
        }

        $selectedPrimaryLanguageId = DB::table('languages')
            ->where('is_active', true)
            ->where(function ($query) use ($selectedPrimaryLanguageCode): void {
                $query
                    ->where('iso639_1', $selectedPrimaryLanguageCode)
                    ->orWhere('iso639_3', $selectedPrimaryLanguageCode);
            })
            ->value('id');

        if (! is_int($selectedPrimaryLanguageId) && ! is_numeric($selectedPrimaryLanguageId)) {
            return null;
        }

        return (int) $selectedPrimaryLanguageId;
    }

    /**
     * Load active sub-locales from database into component state.
     */
    private function syncSelectedSubLocaleCodesFromDatabase(): void
    {
        $selectedPrimaryLanguageCode = LocaleCode::normalize($this->selectedPrimaryLanguageCode);
        $selectedPrimaryLanguageId = $this->selectedPrimaryLanguageId();

        if ($selectedPrimaryLanguageCode === '' || $selectedPrimaryLanguageId === null) {
            $this->selectedSubLocaleCodes = [];

            return;
        }

        $this->selectedSubLocaleCodes = DB::table('locales')
            ->where('language_id', $selectedPrimaryLanguageId)
            ->where('is_active', true)
            ->whereRaw('LOWER(code) <> ?', [strtolower($selectedPrimaryLanguageCode)])
            ->orderBy('sort_order')
            ->orderBy('code')
            ->pluck('code')
            ->map(static fn(string $code): string => LocaleCode::normalize($code))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Persist the selected sub-locales to locales.is_active for the selected primary language.
     *
     * All sub-locales for that primary language are first deactivated and then
     * the currently selected subset is activated inside a transaction.
     */
    private function persistSelectedSubLocaleCodesForSelectedPrimaryLanguage(): void
    {
        $selectedPrimaryLanguageCode = LocaleCode::normalize($this->selectedPrimaryLanguageCode);
        $selectedPrimaryLanguageId = $this->selectedPrimaryLanguageId();

        if ($selectedPrimaryLanguageCode === '' || $selectedPrimaryLanguageId === null) {
            $this->selectedSubLocaleCodes = [];

            return;
        }

        $subLocaleRows = DB::table('locales')
            ->where('language_id', $selectedPrimaryLanguageId)
            ->whereRaw('LOWER(code) <> ?', [strtolower($selectedPrimaryLanguageCode)])
            ->get(['id', 'code']);

        if ($subLocaleRows->isEmpty()) {
            $this->selectedSubLocaleCodes = [];

            return;
        }

        $availableSubLocaleIdsByCode = [];
        $allSubLocaleIds = [];

        foreach ($subLocaleRows as $subLocaleRow) {
            $subLocaleCode = LocaleCode::normalize((string) $subLocaleRow->code);

            if ($subLocaleCode === '') {
                continue;
            }

            $subLocaleId = (int) $subLocaleRow->id;

            $availableSubLocaleIdsByCode[$subLocaleCode] = $subLocaleId;
            $allSubLocaleIds[] = $subLocaleId;
        }

        if ($allSubLocaleIds === []) {
            $this->selectedSubLocaleCodes = [];

            return;
        }

        $selectedSubLocaleCodes = collect($this->selectedSubLocaleCodes)
            ->map(static fn(mixed $code): string => is_string($code) ? LocaleCode::normalize($code) : '')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $selectedSubLocaleIds = array_values(array_filter(
            array_map(
                static fn(string $code): ?int => $availableSubLocaleIdsByCode[$code] ?? null,
                $selectedSubLocaleCodes,
            ),
            static fn(?int $id): bool => $id !== null,
        ));

        DB::transaction(static function () use ($allSubLocaleIds, $selectedSubLocaleIds): void {
            DB::table('locales')
                ->whereIn('id', $allSubLocaleIds)
                ->update(['is_active' => false]);

            if ($selectedSubLocaleIds === []) {
                return;
            }

            DB::table('locales')
                ->whereIn('id', $selectedSubLocaleIds)
                ->update(['is_active' => true]);
        });

        $this->syncSelectedSubLocaleCodesFromDatabase();
    }

    /**
     * @param  Collection<int, object>  $localeRows
     * @return Collection<int, object>
     */
    private function filterPrimaryLocaleRows(Collection $localeRows): Collection
    {
        $scopeCodes = $this->primaryLanguageScopeCodes();

        if ($scopeCodes === []) {
            return $localeRows;
        }

        $filteredLocaleRows = $localeRows
            ->filter(static function (object $localeRow) use ($scopeCodes): bool {
                $code = LocaleCode::normalize((string) ($localeRow->normalized_code ?? $localeRow->code ?? ''));

                return in_array($code, $scopeCodes, true);
            })
            ->values();

        $selectedCode = LocaleCode::normalize($this->selectedPrimaryLanguageCode);

        if ($selectedCode === '' || $filteredLocaleRows->contains(static fn(object $row): bool => LocaleCode::normalize((string) $row->code) === $selectedCode)) {
            return $filteredLocaleRows;
        }

        $selectedRow = $localeRows->first(static fn(object $row): bool => LocaleCode::normalize((string) $row->code) === $selectedCode);

        if ($selectedRow === null) {
            return $filteredLocaleRows;
        }

        return $filteredLocaleRows
            ->prepend($selectedRow)
            ->unique(static fn(object $row): string => LocaleCode::normalize((string) $row->code))
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function primaryLanguageScopeCodes(): array
    {
        $configuredScopes = $this->configuredPrimaryLanguageScopes();

        return $configuredScopes[$this->primaryLanguageScope] ?? [];
    }

    private function configuredDefaultPrimaryLanguageScope(): string
    {
        $configuredDefault = config('buergerfrs-locales.primary_language_scope_default', 'all');
        $scope = is_string($configuredDefault) ? strtolower(trim($configuredDefault)) : 'all';

        return $this->normalizePrimaryLanguageScope($scope);
    }

    private function normalizePrimaryLanguageScope(string $scope): string
    {
        $validScopes = array_keys($this->configuredPrimaryLanguageScopes());
        $validScopes[] = 'all';

        if (! in_array($scope, $validScopes, true)) {
            return 'all';
        }

        return $scope;
    }

    /**
     * @return array<int, array{value: string, label: string, icon: string}>
     */
    private function primaryLanguageScopeOptions(): array
    {
        $scopes = $this->configuredPrimaryLanguageScopes();
        $labels = $this->configuredPrimaryLanguageScopeLabels();
        $icons = $this->configuredPrimaryLanguageScopeIcons();

        $options = [];

        foreach ($scopes as $scope => $codes) {
            if ($codes === []) {
                continue;
            }

            $options[] = [
                'value' => $scope,
                'label' => $labels[$scope] ?? $this->formatPrimaryLanguageScopeLabel($scope),
                'icon' => $icons[$scope] ?? 'list-ordered',
            ];
        }

        $options[] = [
            'value' => 'all',
            'label' => $labels['all'] ?? 'All',
            'icon' => $icons['all'] ?? 'arrow-down-a-z',
        ];

        return $options;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function configuredPrimaryLanguageScopes(): array
    {
        $configuredScopes = config('buergerfrs-locales.primary_language_scopes', []);

        if (! is_array($configuredScopes) || $configuredScopes === []) {
            return [
                'top10' => self::DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_10,
                'top20' => self::DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_20,
            ];
        }

        $normalizedScopes = [];

        foreach ($configuredScopes as $scope => $codes) {
            if (! is_string($scope) || ! is_array($codes)) {
                continue;
            }

            $normalizedCodes = array_values(array_unique(array_filter(
                array_map(
                    static fn(mixed $code): string => is_string($code) ? LocaleCode::normalize($code) : '',
                    $codes,
                ),
                static fn(string $code): bool => $code !== ''
            )));

            if ($normalizedCodes === []) {
                continue;
            }

            $normalizedScopes[strtolower(trim($scope))] = $normalizedCodes;
        }

        if ($normalizedScopes === []) {
            return [
                'top10' => self::DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_10,
                'top20' => self::DEFAULT_TOP_PRIMARY_LANGUAGE_CODES_20,
            ];
        }

        return $normalizedScopes;
    }

    /**
     * @return array<string, string>
     */
    private function configuredPrimaryLanguageScopeLabels(): array
    {
        $configuredLabels = config('buergerfrs-locales.primary_language_scope_labels', []);

        if (! is_array($configuredLabels) || $configuredLabels === []) {
            return [];
        }

        $normalizedLabels = [];

        foreach ($configuredLabels as $scope => $label) {
            if (! is_string($scope) || ! is_string($label)) {
                continue;
            }

            $normalizedScope = strtolower(trim($scope));
            $normalizedLabel = trim($label);

            if ($normalizedScope === '' || $normalizedLabel === '') {
                continue;
            }

            $normalizedLabels[$normalizedScope] = $normalizedLabel;
        }

        return $normalizedLabels;
    }

    /**
     * @return array<string, string>
     */
    private function configuredPrimaryLanguageScopeIcons(): array
    {
        $configuredIcons = config('buergerfrs-locales.primary_language_scope_icons', []);

        if (! is_array($configuredIcons) || $configuredIcons === []) {
            return [];
        }

        $normalizedIcons = [];

        foreach ($configuredIcons as $scope => $icon) {
            if (! is_string($scope) || ! is_string($icon)) {
                continue;
            }

            $normalizedScope = strtolower(trim($scope));
            $normalizedIcon = trim($icon);

            if ($normalizedScope === '' || $normalizedIcon === '') {
                continue;
            }

            $normalizedIcons[$normalizedScope] = $normalizedIcon;
        }

        return $normalizedIcons;
    }

    private function formatPrimaryLanguageScopeLabel(string $scope): string
    {
        $formatted = preg_replace('/([a-zA-Z])(\d+)/', '$1 $2', $scope);
        $formatted = str_replace(['-', '_'], ' ', (string) $formatted);
        $formatted = trim($formatted);

        if ($formatted === '') {
            return strtoupper($scope);
        }

        return ucwords($formatted);
    }

    /**
     * @param  array<int, mixed>  $availableLocales
     *
     * @return array<int, string>
     */
    private function normaliseAvailableLocales(array $availableLocales): array
    {
        $locales = array_values(array_unique(array_filter(
            array_map(
                static fn(mixed $locale): string => is_string($locale) ? LocaleCode::normalize($locale) : '',
                $availableLocales,
            ),
            static fn(string $locale): bool => $locale !== ''
        )));

        foreach (self::MANDATORY_AVAILABLE_LOCALES as $mandatoryLocale) {
            if (! in_array($mandatoryLocale, $locales, true)) {
                $locales[] = $mandatoryLocale;
            }
        }

        return $locales;
    }
}
