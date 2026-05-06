<?php

// app/Livewire/Admin/RoleList.php

namespace App\Livewire\Admin;

use App\Settings\AppDisplaySettings;
use App\Support\Audit\AdminActivity;
use App\Support\Icons\IconRegistry;
use App\Support\Settings\RoleBadgeResolver;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleList extends Component
{
    public bool $showEditRoleModal = false;
    public ?int $editingRoleId = null;

    public string $editingRoleName = '';
    public string $editingGuardName = '';
    public string $editingCategory = '';
    public int $editingSortOrder = 100;
    public string $editingDescription = '';
    public bool $editingIsSystem = false;
    public bool $editingIsAssignable = true;
    public int $editingUsersCount = 0;

    public string $editingBadgeColor = 'zinc';
    public string $editingBadgeVariant = 'subtle';
    public string $editingBadgeIcon = 'tag';

    public bool $showCreateRoleModal = false;

    public string $creatingRoleName = '';
    public string $creatingGuardName = 'web';
    public string $creatingCategory = 'user';
    public int $creatingSortOrder = 100;
    public string $creatingDescription = '';
    public bool $creatingIsAssignable = true;

    public string $creatingBadgeColor = 'zinc';
    public string $creatingBadgeVariant = 'subtle';
    public string $creatingBadgeIcon = 'tag';

    public string $sortField = 'sort_order';
    public string $sortDirection = 'asc';

    public string $search = '';
    public string $categoryFilter = '';
    public string $assignableFilter = '';
    public string $systemFilter = '';

    public function openCreateRoleModal(): void
    {
        $this->resetValidation();

        $this->creatingRoleName = '';
        $this->creatingGuardName = 'web';
        $this->creatingCategory = 'user';
        $this->creatingSortOrder = 100;
        $this->creatingDescription = '';
        $this->creatingIsAssignable = true;

        $this->creatingBadgeColor = 'zinc';
        $this->creatingBadgeVariant = 'subtle';
        $this->creatingBadgeIcon = 'tag';

        $this->showCreateRoleModal = true;
    }

    public function closeCreateRoleModal(): void
    {
        $this->resetCreateRoleModal();
    }

    public function createRole(AppDisplaySettings $settings, IconRegistry $iconRegistry, AdminActivity $adminActivity): void
    {
        if (! auth()->user()?->hasRole('Super-Admin')) {
            Flux::toast(
                heading: __('Not allowed'),
                text: __('Only Super-Admins may create roles.'),
                variant: 'danger',
                duration: 4000,
            );

            return;
        }

        $this->validate([
            'creatingRoleName' => [
                'required',
                'string',
                'max:128',
                'regex:/^[\pL\pN _-]+$/u',
                Rule::unique('roles', 'name')->where('guard_name', $this->creatingGuardName),
            ],
            'creatingGuardName' => ['required', 'string', 'max:64'],
            'creatingCategory' => ['nullable', 'string', 'max:64'],
            'creatingSortOrder' => ['required', 'integer', 'min:0', 'max:65535'],
            'creatingDescription' => ['nullable', 'string', 'max:2000'],
            'creatingIsAssignable' => ['boolean'],
            'creatingBadgeColor' => [
                'required',
                'string',
                Rule::in(array_keys($iconRegistry->roleUserManagementBadgeColors())),
            ],
            'creatingBadgeVariant' => [
                'required',
                'string',
                Rule::in(array_keys($iconRegistry->roleUserManagementBadgeVariants())),
            ],
            'creatingBadgeIcon' => [
                'required',
                'string',
                Rule::in(array_keys($iconRegistry->roleUserManagementOptions())),
            ],
        ]);

        $roleName = trim($this->creatingRoleName);

        $role = Role::query()->create([
            'name' => $roleName,
            'guard_name' => $this->creatingGuardName,
            'category' => trim($this->creatingCategory) !== '' ? trim($this->creatingCategory) : null,
            'sort_order' => $this->creatingSortOrder,
            'description' => trim($this->creatingDescription) !== '' ? trim($this->creatingDescription) : null,
            'is_system' => false,
            'is_assignable' => $this->creatingIsAssignable,
        ]);

        $roleBadges = $settings->roleBadges;
        $roleBadges[$role->name] = [
            'color' => $this->creatingBadgeColor,
            'variant' => $this->creatingBadgeVariant,
            'icon' => $this->creatingBadgeIcon,
        ];

        $settings->roleBadges = $roleBadges;
        $settings->save();

        $adminActivity->roleCreated(
            role: $role,
            metadata: [
                'category' => $role->category,
                'sort_order' => $role->sort_order,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'is_assignable' => $role->is_assignable,
            ],
            badge: $roleBadges[$role->name] ?? [],
        );

        $this->resetCreateRoleModal();

        Flux::toast(
            heading: __('Role created'),
            text: __('Role :role has been created.', [
                'role' => $role->name,
            ]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

    private function resetCreateRoleModal(): void
    {
        $this->resetValidation();

        $this->showCreateRoleModal = false;

        $this->creatingRoleName = '';
        $this->creatingGuardName = 'web';
        $this->creatingCategory = 'user';
        $this->creatingSortOrder = 100;
        $this->creatingDescription = '';
        $this->creatingIsAssignable = true;

        $this->creatingBadgeColor = 'zinc';
        $this->creatingBadgeVariant = 'subtle';
        $this->creatingBadgeIcon = 'tag';
    }

    public function openEditRoleModal(int $roleId, AppDisplaySettings $settings): void
    {
        $this->resetValidation();

        $role = Role::query()
            ->withCount('users')
            ->findOrFail($roleId);

        $badge = $settings->roleBadges[$role->name] ?? [];

        $this->editingRoleId = $role->id;
        $this->editingRoleName = (string) $role->name;
        $this->editingGuardName = (string) $role->guard_name;
        $this->editingCategory = (string) ($role->category ?? '');
        $this->editingSortOrder = (int) $role->sort_order;
        $this->editingDescription = (string) ($role->description ?? '');
        $this->editingIsSystem = (bool) $role->is_system;
        $this->editingIsAssignable = (bool) $role->is_assignable;
        $this->editingUsersCount = (int) $role->users_count;

        $this->editingBadgeColor = (string) ($badge['color'] ?? 'zinc');
        $this->editingBadgeVariant = (string) ($badge['variant'] ?? 'subtle');
        $this->editingBadgeIcon = (string) ($badge['icon'] ?? 'tag');

        $this->showEditRoleModal = true;
    }

    public function closeEditRoleModal(): void
    {
        $this->resetEditRoleModal();
    }

    public function saveRole(AppDisplaySettings $settings, IconRegistry $iconRegistry, AdminActivity $adminActivity): void
    {
        if ($this->editingRoleId === null) {
            return;
        }

        $this->validate([
            'editingCategory' => ['nullable', 'string', 'max:64'],
            'editingSortOrder' => ['required', 'integer', 'min:0', 'max:65535'],
            'editingDescription' => ['nullable', 'string', 'max:2000'],
            'editingIsAssignable' => ['boolean'],
            'editingBadgeColor' => [
                'required',
                'string',
                Rule::in(array_keys($iconRegistry->roleUserManagementBadgeColors())),
            ],
            'editingBadgeVariant' => [
                'required',
                'string',
                Rule::in(array_keys($iconRegistry->roleUserManagementBadgeVariants())),
            ],
            'editingBadgeIcon' => [
                'required',
                'string',
                Rule::in(array_keys($iconRegistry->roleUserManagementOptions())),
            ],
        ]);

        $role = Role::query()->findOrFail($this->editingRoleId);

        $beforeBadge = $settings->roleBadges[$role->name] ?? [];

        $before = [
            'metadata' => [
                'category' => $role->category,
                'sort_order' => $role->sort_order,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'is_assignable' => $role->is_assignable,
            ],
            'badge' => [
                'color' => $beforeBadge['color'] ?? null,
                'variant' => $beforeBadge['variant'] ?? null,
                'icon' => $beforeBadge['icon'] ?? null,
            ],
        ];

        $role->forceFill([
            'category' => trim($this->editingCategory) !== '' ? trim($this->editingCategory) : null,
            'sort_order' => $this->editingSortOrder,
            'description' => trim($this->editingDescription) !== '' ? trim($this->editingDescription) : null,
            'is_assignable' => $this->editingIsAssignable,
        ])->save();

        $roleBadges = $settings->roleBadges;
        $roleBadges[$role->name] = [
            'color' => $this->editingBadgeColor,
            'variant' => $this->editingBadgeVariant,
            'icon' => $this->editingBadgeIcon,
        ];

        $settings->roleBadges = $roleBadges;
        $settings->save();

        $role->refresh();

        $after = [
            'metadata' => [
                'category' => $role->category,
                'sort_order' => $role->sort_order,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'is_assignable' => $role->is_assignable,
            ],
            'badge' => [
                'color' => $roleBadges[$role->name]['color'] ?? null,
                'variant' => $roleBadges[$role->name]['variant'] ?? null,
                'icon' => $roleBadges[$role->name]['icon'] ?? null,
            ],
        ];

        $adminActivity->roleUpdated(
            role: $role,
            before: $before,
            after: $after,
        );

        $savedRoleName = (string) $role->name;

        $this->resetEditRoleModal();

        Flux::toast(
            heading: __('Role saved'),
            text: __('Role metadata and badge settings for :role have been updated.', [
                'role' => $savedRoleName,
            ]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

    private function resetEditRoleModal(): void
    {
        $this->resetValidation();

        $this->showEditRoleModal = false;
        $this->editingRoleId = null;

        $this->editingRoleName = '';
        $this->editingGuardName = '';
        $this->editingCategory = '';
        $this->editingSortOrder = 100;
        $this->editingDescription = '';
        $this->editingIsSystem = false;
        $this->editingIsAssignable = true;
        $this->editingUsersCount = 0;

        $this->editingBadgeColor = 'zinc';
        $this->editingBadgeVariant = 'subtle';
        $this->editingBadgeIcon = 'tag';
    }

    public function sortBy(string $field): void
    {
        $allowedFields = [
            'name',
            'category',
            'sort_order',
            'users_count',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->assignableFilter = '';
        $this->systemFilter = '';
    }

    public function render(RoleBadgeResolver $roleBadgeResolver, IconRegistry $iconRegistry)
    {
        $rolesQuery = Role::query()
            ->withCount('users');

        $rolesQuery
            ->when(trim($this->search) !== '', function ($query): void {
                $search = trim($this->search);

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%");
                });
            })
            ->when($this->categoryFilter !== '', function ($query): void {
                $query->where('category', $this->categoryFilter);
            })
            ->when($this->assignableFilter !== '', function ($query): void {
                $query->where('is_assignable', $this->assignableFilter === 'yes');
            })
            ->when($this->systemFilter !== '', function ($query): void {
                $query->where('is_system', $this->systemFilter === 'yes');
            });

        if ($this->sortField === 'users_count') {
            $rolesQuery
                ->orderBy('users_count', $this->sortDirection)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('name');
        } else {
            $rolesQuery
                ->orderBy($this->sortField, $this->sortDirection)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('name');
        }

        $roles = $rolesQuery->get();

        $roleBadges = $roles
            ->mapWithKeys(fn(Role $role): array => [
                $role->name => $roleBadgeResolver->forRole($role->name),
            ])
            ->all();

        $roleCategories = Role::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return view('components.admin.⚡role-list', [
            'roles' => $roles,
            'roleBadges' => $roleBadges,
            'roleBadgeColorOptions' => $iconRegistry->roleUserManagementBadgeColors(),
            'roleBadgeVariantOptions' => $iconRegistry->roleUserManagementBadgeVariants(),
            'roleBadgeIconOptions' => $iconRegistry->roleUserManagementOptions(),
            'roleCategories' => $roleCategories,
        ]);
    }
}
