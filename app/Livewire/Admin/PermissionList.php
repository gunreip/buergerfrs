<?php

// app/Livewire/Admin/PermissionList.php

namespace App\Livewire\Admin;

use App\Support\Audit\AdminActivity;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Administrative permission list with metadata editing and role-permission assignment.
 */
class PermissionList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $guardFilter = '';

    public string $roleFilter = '';

    public string $assignmentFilter = '';

    public string $categoryFilter = '';

    public string $systemFilter = '';

    public int $perPage = 25;

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showEditPermissionModal = false;

    public ?int $editingPermissionId = null;

    public string $editingPermissionName = '';

    public string $editingPermissionGuard = '';

    public string $editingCategory = '';

    public int $editingSortOrder = 100;

    public string $editingDescription = '';

    public bool $editingIsSystem = false;

    public int $editingRolesCount = 0;

    public array $originalPermissionMetadata = [];

    public bool $showRolePermissionsModal = false;

    public string $selectedRoleName = '';

    public array $selectedPermissionNames = [];

    public array $originalPermissionNames = [];

    public string $selectedRoleCategory = '';

    public int $selectedRoleCurrentPermissionCount = 0;

    /**
     * Reset pagination when a filter changes.
     */
    public function updating(string $property): void
    {
        if (in_array($property, [
            'search',
            'guardFilter',
            'roleFilter',
            'assignmentFilter',
            'categoryFilter',
            'systemFilter',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
    }

    /**
     * Reset active filters to defaults.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->guardFilter = '';
        $this->roleFilter = '';
        $this->assignmentFilter = '';
        $this->categoryFilter = '';
        $this->systemFilter = '';
        $this->perPage = 10;

        $this->resetPage();
    }

    /**
     * Sort by a whitelisted column and toggle sort direction on repeat selection.
     */
    public function sortBy(string $field): void
    {
        $allowedFields = [
            'name',
            'guard_name',
            'category',
            'sort_order',
            'roles_count',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            $this->resetPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    /**
     * Determine whether role-permission assignments differ from original state.
     */
    public function hasRolePermissionChanges(): bool
    {
        $selected = $this->normalizedPermissionNames($this->selectedPermissionNames);
        $original = $this->normalizedPermissionNames($this->originalPermissionNames);

        return $selected !== $original;
    }

    /**
     * Determine whether editable permission metadata changed.
     */
    public function hasPermissionMetadataChanges(): bool
    {
        if ($this->editingPermissionId === null) {
            return false;
        }

        return $this->normalizedEditingPermissionMetadata() !== $this->originalPermissionMetadata;
    }

    /**
     * Normalize editable permission metadata for dirty checks and persistence.
     *
     * @return array{category: string|null, sort_order: int, description: string|null, is_system: bool}
     */
    private function normalizedEditingPermissionMetadata(): array
    {
        return [
            'category' => trim($this->editingCategory) !== ''
                ? trim($this->editingCategory)
                : null,
            'sort_order' => (int) $this->editingSortOrder,
            'description' => trim($this->editingDescription) !== ''
                ? trim($this->editingDescription)
                : null,
            'is_system' => (bool) $this->editingIsSystem,
        ];
    }

    private function normalizedPermissionNames(array $permissionNames): array
    {
        $permissionNames = array_values(array_unique(array_map('strval', $permissionNames)));

        sort($permissionNames);

        return $permissionNames;
    }

    /**
     * Open permission metadata modal and hydrate editable fields.
     */
    public function openEditPermissionModal(int $permissionId): void
    {
        $permission = Permission::query()
            ->withCount('roles')
            ->findOrFail($permissionId);

        $this->editingPermissionId = $permission->id;
        $this->editingPermissionName = $permission->name;
        $this->editingPermissionGuard = $permission->guard_name;
        $this->editingCategory = (string) ($permission->category ?? '');
        $this->editingSortOrder = (int) ($permission->sort_order ?? 100);
        $this->editingDescription = (string) ($permission->description ?? '');
        $this->editingIsSystem = (bool) $permission->is_system;
        $this->editingRolesCount = (int) $permission->roles_count;
        $this->originalPermissionMetadata = $this->normalizedEditingPermissionMetadata();

        $this->resetValidation();

        $this->showEditPermissionModal = true;
    }

    /**
     * Close permission metadata modal and clear state.
     */
    public function closeEditPermissionModal(): void
    {
        $this->showEditPermissionModal = false;

        $this->editingPermissionId = null;
        $this->editingPermissionName = '';
        $this->editingPermissionGuard = '';
        $this->editingCategory = '';
        $this->editingSortOrder = 100;
        $this->editingDescription = '';
        $this->editingIsSystem = false;
        $this->editingRolesCount = 0;
        $this->originalPermissionMetadata = [];

        $this->resetValidation();
    }

    /**
     * Open role-permission assignment modal for a role or first available role.
     */
    public function openRolePermissionsModal(?string $roleName = null): void
    {
        $roleQuery = Role::query();

        if ($roleName !== null && $roleName !== '') {
            $roleQuery->where('name', $roleName);
        }

        /** @var Role|null $role */
        $role = $roleQuery
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->first();

        if ($role === null) {
            $this->selectedRoleName = '';
            $this->selectedPermissionNames = [];
            $this->originalPermissionNames = [];
            $this->selectedRoleCategory = '';
            $this->selectedRoleCurrentPermissionCount = 0;
        } else {
            $this->selectedRoleName = $role->name;
            $this->selectedRoleCategory = (string) ($role->category ?? '');
            $this->selectedRoleCurrentPermissionCount = $role->permissions()->count();

            $this->selectedPermissionNames = $role
                ->permissions()
                ->pluck('permissions.name')
                ->all();

            $this->originalPermissionNames = $this->selectedPermissionNames;
        }

        $this->resetValidation();

        $this->showRolePermissionsModal = true;
    }

    /**
     * Close role-permission assignment modal and reset selection state.
     */
    public function closeRolePermissionsModal(): void
    {
        $this->showRolePermissionsModal = false;

        $this->selectedRoleName = '';
        $this->selectedPermissionNames = [];
        $this->originalPermissionNames = [];

        $this->selectedRoleCategory = '';
        $this->selectedRoleCurrentPermissionCount = 0;

        $this->resetValidation();
    }

    /**
     * Reload selected role permission names when role selection changes.
     */
    public function updatedSelectedRoleName(): void
    {
        if ($this->selectedRoleName === '') {
            $this->selectedPermissionNames = [];
            $this->originalPermissionNames = [];
            $this->selectedRoleCategory = '';
            $this->selectedRoleCurrentPermissionCount = 0;

            return;
        }

        $role = Role::query()
            ->where('name', $this->selectedRoleName)
            ->firstOrFail();

        $this->selectedRoleCategory = (string) ($role->category ?? '');
        $this->selectedRoleCurrentPermissionCount = $role->permissions()->count();

        $this->selectedPermissionNames = $role
            ->permissions()
            ->pluck('permissions.name')
            ->all();

        $this->originalPermissionNames = $this->selectedPermissionNames;
    }

    /**
     * Persist selected permission names for the chosen role and write audit activity.
     */
    public function saveRolePermissions(AdminActivity $adminActivity): void
    {
        $this->validate([
            'selectedRoleName' => ['required', 'string', 'exists:roles,name'],
            'selectedPermissionNames' => ['array'],
            'selectedPermissionNames.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::query()
            ->where('name', $this->selectedRoleName)
            ->firstOrFail();

        $beforePermissions = $this->normalizedPermissionNames($this->originalPermissionNames);
        $afterPermissions = $this->normalizedPermissionNames($this->selectedPermissionNames);

        if (! $this->hasRolePermissionChanges()) {
            Flux::toast(
                heading: __('admin.permissions.messages.no_changes.heading'),
                text: __('admin.permissions.messages.no_changes.role_permissions_unchanged', [
                    'role' => $role->name,
                ]),
                variant: 'warning',
                duration: 3000,
            );

            return;
        }

        $role->syncPermissions($afterPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminActivity->rolePermissionsUpdated(
            role: $role,
            beforePermissions: $beforePermissions,
            afterPermissions: $afterPermissions,
        );

        $this->dispatch('$refresh');

        $this->closeRolePermissionsModal();

        Flux::toast(
            heading: __('admin.permissions.messages.role_permissions_saved.heading'),
            text: __('admin.permissions.messages.role_permissions_saved.text', [
                'role' => $role->name,
            ]),
            variant: 'success',
            duration: 3000,
        );
    }

    /**
     * Persist editable permission metadata and write audit activity.
     */
    public function savePermissionMetadata(AdminActivity $adminActivity): void
    {
        $this->validate([
            'editingPermissionId' => ['required', 'integer', 'exists:permissions,id'],
            'editingCategory' => ['nullable', 'string', 'max:80'],
            'editingSortOrder' => ['required', 'integer', 'min:0', 'max:999999'],
            'editingDescription' => ['nullable', 'string', 'max:2000'],
            'editingIsSystem' => ['boolean'],
        ]);

        $permission = Permission::query()
            ->findOrFail($this->editingPermissionId);

        $before = [
            'category' => $permission->category,
            'sort_order' => (int) $permission->sort_order,
            'description' => $permission->description,
            'is_system' => (bool) $permission->is_system,
        ];

        $after = $this->normalizedEditingPermissionMetadata();

        if ($before === $after) {
            Flux::toast(
                heading: __('admin.permissions.messages.no_changes.heading'),
                text: __('admin.permissions.messages.no_changes.role_permissions_unchanged', [
                    'permission' => $permission->name,
                ]),
                variant: 'warning',
                duration: 3000,
            );

            return;
        }

        $permission->category = $after['category'];
        $permission->sort_order = $after['sort_order'];
        $permission->description = $after['description'];
        $permission->is_system = $after['is_system'];
        $permission->save();

        $permission->refresh();

        $after = [
            'category' => $permission->category,
            'sort_order' => (int) $permission->sort_order,
            'description' => $permission->description,
            'is_system' => (bool) $permission->is_system,
        ];

        $adminActivity->permissionMetadataUpdated(
            permission: $permission,
            before: $before,
            after: $after,
        );

        $this->closeEditPermissionModal();

        Flux::toast(
            heading: __('admin.permissions.messages.permission_saved.heading'),
            text: __('admin.permissions.messages.permission_saved.text', [
                'permission' => $permission->name,
            ]),
            variant: 'success',
            duration: 3000,
        );
    }

    /**
     * Normalize selectable pagination size.
     */
    private function normalizedPerPage(): int
    {
        return in_array($this->perPage, [10, 25, 50, 100], true)
            ? $this->perPage
            : 10;
    }

    public function render()
    {
        $permissionsQuery = Permission::query()
            ->with([
                'roles' => fn($query) => $query
                    ->select('roles.id', 'roles.name', 'roles.guard_name')
                    ->orderBy('roles.name'),
            ])
            ->withCount('roles');

        $permissionsQuery
            ->when(trim($this->search) !== '', function ($query): void {
                $search = trim($this->search);

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%");
                });
            })
            ->when($this->guardFilter !== '', function ($query): void {
                $query->where('guard_name', $this->guardFilter);
            })
            ->when($this->categoryFilter !== '', function ($query): void {
                $query->where('category', $this->categoryFilter);
            })
            ->when($this->systemFilter !== '', function ($query): void {
                $query->where('is_system', $this->systemFilter === 'yes');
            })
            ->when($this->roleFilter !== '', function ($query): void {
                $query->whereHas('roles', function ($query): void {
                    $query->where('roles.name', $this->roleFilter);
                });
            })
            ->when($this->assignmentFilter === 'assigned', function ($query): void {
                $query->has('roles');
            })
            ->when($this->assignmentFilter === 'unassigned', function ($query): void {
                $query->doesntHave('roles');
            });

        $filteredPermissionsQuery = clone $permissionsQuery;

        $permissions = $permissionsQuery
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('guard_name')
            ->orderBy('name')
            ->paginate($this->normalizedPerPage());

        $summary = [
            'totalPermissions' => (clone $filteredPermissionsQuery)->count(),
            'guardCount' => (clone $filteredPermissionsQuery)
                ->select('guard_name')
                ->distinct()
                ->count('guard_name'),
            'assignedPermissions' => (clone $filteredPermissionsQuery)
                ->has('roles')
                ->count(),
            'unassignedPermissions' => (clone $filteredPermissionsQuery)
                ->doesntHave('roles')
                ->count(),
        ];

        $guardOptions = Permission::query()
            ->select('guard_name')
            ->distinct()
            ->orderBy('guard_name')
            ->pluck('guard_name')
            ->all();

        $categoryOptions = Permission::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        $roleOptions = Role::query()
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $roles = Role::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'sort_order']);

        $permissionsByCategory = Permission::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'guard_name', 'category', 'sort_order', 'description'])
            ->groupBy(fn(Permission $permission): string => (string) ($permission->category ?: 'other'));

        return view('components.admin.⚡permission-list', [
            'permissions' => $permissions,
            'summary' => $summary,
            'guardOptions' => $guardOptions,
            'roleOptions' => $roleOptions,
            'categoryOptions' => $categoryOptions,
            'roles' => $roles,
            'permissionsByCategory' => $permissionsByCategory,
        ]);
    }
}
