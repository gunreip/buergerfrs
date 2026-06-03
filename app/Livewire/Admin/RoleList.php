<?php

// app/Livewire/Admin/RoleList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Settings\AppDisplaySettings;
use App\Support\Audit\AdminActivity;
use App\Support\Icons\IconRegistry;
use App\Support\Settings\RoleBadgeResolver;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * Administrative role list with role creation/editing workflows and badge settings.
 */
class RoleList extends Component
{
    use InteractsWithUserSettings;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_role_list';

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

    public int $perPage = 25;

    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->categoryFilter = trim((string) ($state['categoryFilter'] ?? $this->categoryFilter));
            $this->assignableFilter = trim((string) ($state['assignableFilter'] ?? $this->assignableFilter));
            $this->systemFilter = trim((string) ($state['systemFilter'] ?? $this->systemFilter));
            $this->perPage = (int) ($state['perPage'] ?? $this->perPage);
            $this->perPage = $this->normalizedPerPage();

            $sortField = trim((string) ($state['sortField'] ?? $this->sortField));
            $this->sortField = in_array($sortField, [
                'name',
                'category',
                'sort_order',
                'users_count',
            ], true) ? $sortField : 'sort_order';

            $sortDirection = trim((string) ($state['sortDirection'] ?? $this->sortDirection));
            $this->sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc';
        }

        $this->setPage(1);
    }

    /**
     * Reset pagination when a filter changes.
     */
    public function updating(string $property): void
    {
        if (in_array($property, [
            'search',
            'categoryFilter',
            'assignableFilter',
            'systemFilter',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'categoryFilter',
            'assignableFilter',
            'systemFilter',
        ], true)) {
            $this->persistUiState();
        }
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizedPerPage();
        $this->persistUiState();
    }

    /**
     * Open and initialize the create-role modal state.
     */
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

    /**
     * Close and reset the create-role modal state.
     */
    public function closeCreateRoleModal(): void
    {
        $this->resetCreateRoleModal();
    }

    /**
     * Validate input, create a role, persist badge configuration and log audit activity.
     */
    public function createRole(AppDisplaySettings $settings, IconRegistry $iconRegistry, AdminActivity $adminActivity): void
    {
        if (! Auth::user()?->hasRole('Super-Admin')) {
            Flux::toast(
                // i18n-native: __('Not allowed')
                heading: __('admin.roles.messages.not_allowed.heading'),
                // i18n-native: __('Only Super-Admins may create roles.')
                text: __('admin.roles.messages.not_allowed.only_super_admins_may_create_roles'),
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
            // i18n-native: __('Role created')
            heading: __('admin.roles.messages.role_created.heading'),
            // i18n-native: __('Role :role has been created.')
            text: __('admin.roles.messages.role_created.text', [
                'role' => $role->name,
            ]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

    /**
     * Reset create-role modal fields to defaults.
     */
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

    /**
     * Open and hydrate edit-role modal state for a specific role.
     */
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

    /**
     * Close and reset the edit-role modal state.
     */
    public function closeEditRoleModal(): void
    {
        $this->resetEditRoleModal();
    }

    /**
     * Save editable role metadata and badge settings, then log audit activity.
     */
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
            // i18n-native: __('Role saved')
            heading: __('admin.roles.messages.role_saved.heading'),
            // i18n-native: __('Role metadata and badge settings for :role have been updated.')
            text: __('admin.roles.messages.role_saved.text', [
                'role' => $savedRoleName,
            ]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

    /**
     * Reset edit-role modal fields to defaults.
     */
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

    /**
     * Sort by a whitelisted field and toggle direction on repeated selection.
     */
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
            $this->resetPage();
            $this->persistUiState();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
        $this->persistUiState();
    }

    /**
     * Reset all active list filters to defaults.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->assignableFilter = '';
        $this->systemFilter = '';
        $this->perPage = 10;

        $this->resetPage();
        $this->persistUiState();
    }

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'categoryFilter' => $this->categoryFilter,
            'assignableFilter' => $this->assignableFilter,
            'systemFilter' => $this->systemFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
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

    /**
     * Build list, badge metadata and option payloads for rendering.
     *
     * @return \Illuminate\Contracts\View\View
     */
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

        $roles = $rolesQuery->paginate($this->normalizedPerPage());

        $roleBadges = $roles
            ->getCollection()
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
