<?php

// app/Livewire/Admin/AppSettings.php

namespace App\Livewire\Admin;

use App\Settings\AppDisplaySettings;
use App\Settings\AppGeneralSettings;
use App\Support\Icons\IconRegistry;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AppSettings extends Component
{
    private const KNOWN_PSEUDO_ROLE_BADGE_KEYS = [
        '__without_role__',
    ];

    public string $locale = 'de';

    /**
     * @var array<int, string>
     */
    public array $availableLocales = [];

    public function mount(AppGeneralSettings $appGeneralSettings): void
    {
        $this->locale = $appGeneralSettings->locale;
        $this->availableLocales = $this->normaliseAvailableLocales($appGeneralSettings->availableLocales);
    }

    public function setLocale(string $locale, AppGeneralSettings $appGeneralSettings): void
    {
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
        ]);
    }

    /**
     * @param  array<int, mixed>  $availableLocales
     *
     * @return array<int, string>
     */
    private function normaliseAvailableLocales(array $availableLocales): array
    {
        $locales = array_values(array_filter(
            $availableLocales,
            static fn(mixed $locale): bool => is_string($locale) && $locale !== ''
        ));

        return $locales !== [] ? $locales : ['de', 'en'];
    }
}
