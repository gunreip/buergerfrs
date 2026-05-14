<?php

// app/Livewire/Admin/AppSettings.php

namespace App\Livewire\Admin;

use App\Settings\AppDisplaySettings;
use App\Support\Icons\IconRegistry;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AppSettings extends Component
{
    private const KNOWN_PSEUDO_ROLE_BADGE_KEYS = [
        '__without_role__',
    ];

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
            ->reject(fn (Role $role): bool => array_key_exists($role->name, $roleBadges))
            ->values();

        $badgeConfigsWithoutRole = collect($roleBadgeRows)
            ->filter(fn (array $row): bool => ! $row['roleExists'] && ! $row['isPseudoRoleBadgeKey'])
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
        ]);
    }
}
