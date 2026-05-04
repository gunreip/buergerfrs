<?php

// app/Support/Icons/IconRegistry.php

namespace App\Support\Icons;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;

class IconRegistry
{
    public function fallback(): array
    {
        return (array) config('buergerfrs-icons.fallback', [
            'name' => 'file-x',
            'label' => 'Missing icon',
            'view' => 'flux.icon.file-x',
        ]);
    }

    public function options(string $category): array
    {
        return $this->sortOptionsByLabel(
            (array) config("buergerfrs-icons.categories.{$category}.icons", [])
        );
    }

    public function roleUserManagementOptions(): array
    {
        return $this->options('role_user_management');
    }

    public function badgeColors(string $category): array
    {
        return $this->sortLabels(
            (array) config("buergerfrs-icons.categories.{$category}.badge.colors", [])
        );
    }

    public function badgeVariants(string $category): array
    {
        return $this->sortLabels(
            (array) config("buergerfrs-icons.categories.{$category}.badge.variants", [])
        );
    }

    public function roleUserManagementBadgeColors(): array
    {
        return $this->badgeColors('role_user_management');
    }

    public function roleUserManagementBadgeVariants(): array
    {
        return $this->badgeVariants('role_user_management');
    }

    public function isValidBadgeColor(string $color, string $category): bool
    {
        return array_key_exists($color, $this->badgeColors($category));
    }

    public function isValidBadgeVariant(string $variant, string $category): bool
    {
        return array_key_exists($variant, $this->badgeVariants($category));
    }

    public function names(string $category): array
    {
        return array_keys($this->options($category));
    }

    public function roleUserManagementNames(): array
    {
        return $this->names('role_user_management');
    }

    public function resolve(string $name, string $category): array
    {
        $name = trim($name);

        if ($name === '') {
            return $this->fallback();
        }

        $icon = Arr::get($this->options($category), $name);

        if (! is_array($icon)) {
            return $this->fallback();
        }

        $view = (string) ($icon['view'] ?? '');

        if ($view === '' || ! View::exists($view)) {
            return $this->fallback();
        }

        return [
            'name' => $name,
            'label' => (string) ($icon['label'] ?? $name),
            'view' => $view,
        ];
    }

    public function resolveRoleUserManagement(string $name): array
    {
        return $this->resolve($name, 'role_user_management');
    }

    private function sortLabels(array $items): array
    {
        asort($items, SORT_NATURAL | SORT_FLAG_CASE);

        return $items;
    }

    private function sortOptionsByLabel(array $items): array
    {
        uasort($items, static function (array $left, array $right): int {
            return strnatcasecmp(
                (string) ($left['label'] ?? ''),
                (string) ($right['label'] ?? ''),
            );
        });

        return $items;
    }
}
