<?php

// app/Livewire/Admin/PermissionList.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class PermissionList extends Component
{
    public string $search = '';
    public string $guardFilter = '';
    public string $roleFilter = '';
    public string $assignmentFilter = '';
    public string $categoryFilter = '';
    public string $systemFilter = '';

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

    public bool $showRolePermissionsModal = false;

    public string $selectedRoleName = '';
    public array $selectedPermissionNames = [];
    public array $originalPermissionNames = [];

    public string $selectedRoleCategory = '';
    public int $selectedRoleCurrentPermissionCount = 0;

    public function clearFilters(): void
    {
        $this->search = '';
        $this->guardFilter = '';
        $this->roleFilter = '';
        $this->assignmentFilter = '';
        $this->categoryFilter = '';
        $this->systemFilter = '';
    }

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

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function hasRolePermissionChanges(): bool
    {
        $selected = $this->normalizedPermissionNames($this->selectedPermissionNames);
        $original = $this->normalizedPermissionNames($this->originalPermissionNames);

        return $selected !== $original;
    }

    private function normalizedPermissionNames(array $permissionNames): array
    {
        $permissionNames = array_values(array_unique(array_map('strval', $permissionNames)));

        sort($permissionNames);

        return $permissionNames;
    }

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

        $this->resetValidation();

        $this->showEditPermissionModal = true;
    }

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

        $this->resetValidation();
    }

    public function openRolePermissionsModal(?string $roleName = null): void
    {
        $role = Role::query()
            ->when($roleName !== null && $roleName !== '', fn($query) => $query->where('name', $roleName))
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

    public function saveRolePermissions(): void
    {
        $this->validate([
            'selectedRoleName' => ['required', 'string', 'exists:roles,name'],
            'selectedPermissionNames' => ['array'],
            'selectedPermissionNames.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::query()
            ->where('name', $this->selectedRoleName)
            ->firstOrFail();

        if (! $this->hasRolePermissionChanges()) {
            Flux::toast(
                heading: __('No changes'),
                text: __('The permissions for :role have not changed.', [
                    'role' => $role->name,
                ]),
                variant: 'warning',
                duration: 3000,
            );

            return;
        }

        $role->syncPermissions($this->selectedPermissionNames);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->dispatch('$refresh');

        $this->closeRolePermissionsModal();

        Flux::toast(
            heading: __('Role permissions saved'),
            text: __('Permissions for :role have been updated.', [
                'role' => $role->name,
            ]),
            variant: 'success',
            duration: 3000,
        );
    }

    public function savePermissionMetadata(): void
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

        $permission->category = trim($this->editingCategory) !== ''
            ? trim($this->editingCategory)
            : null;

        $permission->sort_order = $this->editingSortOrder;
        $permission->description = trim($this->editingDescription) !== ''
            ? trim($this->editingDescription)
            : null;

        $permission->is_system = $this->editingIsSystem;
        $permission->save();

        $this->closeEditPermissionModal();

        Flux::toast(
            heading: __('Permission saved'),
            text: __('Permission metadata for :permission has been updated.', [
                'permission' => $permission->name,
            ]),
            variant: 'success',
            duration: 3000,
        );
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

        $permissions = $permissionsQuery
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('guard_name')
            ->orderBy('name')
            ->get();

        $summary = [
            'totalPermissions' => $permissions->count(),
            'guardCount' => $permissions
                ->pluck('guard_name')
                ->unique()
                ->count(),
            'assignedPermissions' => $permissions
                ->where('roles_count', '>', 0)
                ->count(),
            'unassignedPermissions' => $permissions
                ->where('roles_count', 0)
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
