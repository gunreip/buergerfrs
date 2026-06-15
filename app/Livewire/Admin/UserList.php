<?php

// app/Livewire/Admin/UserList.php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\User;
use App\Support\Audit\AdminActivity;
use App\Support\Settings\RoleBadgeResolver;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * Administrative user list with role filtering, pagination and role assignment modal.
 */
class UserList extends Component
{
    use InteractsWithUserSettings;

    use WithoutUrlPagination;
    use WithPagination;

    private const UI_STATE_SETTING_KEY = 'ui.pages.admin_user_list';

    private const LEGACY_PER_PAGE_SETTING_KEY = 'ui.per_page.admin_users';

    public $search = '';

    public $roleFilter = '';

    public $perPage = 50;

    public $sortField = 'id';

    public $sortDirection = 'asc';

    public bool $showEditRolesModal = false;

    public ?int $editingUserId = null;

    public string $editingUserName = '';

    public string $editingUserEmail = '';

    public string $editingCurrentRoleName = '';

    public string $editingRoleName = '';

    /**
     * Initialize persisted pagination preference.
     */
    public function mount(): void
    {
        $state = $this->userSetting(self::UI_STATE_SETTING_KEY, []);

        if (is_array($state)) {
            $this->search = trim((string) ($state['search'] ?? $this->search));
            $this->roleFilter = trim((string) ($state['roleFilter'] ?? $this->roleFilter));
            $this->perPage = $this->normalizePerPage($state['perPage'] ?? $this->perPage);

            $sortField = trim((string) ($state['sortField'] ?? $this->sortField));
            $this->sortField = in_array($sortField, [
                'id',
                'name',
                'email',
                'roles.name',
            ], true) ? $sortField : 'id';

            $sortDirection = trim((string) ($state['sortDirection'] ?? $this->sortDirection));
            $this->sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc';
        } else {
            $this->perPage = $this->normalizePerPage(
                $this->userSetting(self::LEGACY_PER_PAGE_SETTING_KEY, $this->perPage)
            );
        }

        $this->setPage(1);
    }

    /**
     * Reset pagination when search text changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Persist list state when search text changed.
     */
    public function updatedSearch(): void
    {
        $this->persistUiState();
    }

    /**
     * Reset pagination when role filter changes.
     */
    public function updatedRoleFilter(): void
    {
        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Normalize and persist page-size preference for the current user.
     */
    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizePerPage($this->perPage);

        $this->setPage(1);
        $this->persistUiState();
    }

    /**
     * Normalize selectable pagination size.
     */
    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        $allowedValues = [10, 25, 50, 100];

        if (! in_array($value, $allowedValues, true)) {
            return 50;
        }

        return $value;
    }

    /**
     * Jump to first paginated page.
     */
    public function goToFirstPage(): void
    {
        $this->setPage(1);
    }

    /**
     * Jump to previous paginated page.
     */
    public function goToPreviousPage(): void
    {
        $this->previousPage();
    }

    /**
     * Jump to next paginated page.
     */
    public function goToNextPage(): void
    {
        $this->nextPage();
    }

    /**
     * Jump to last available paginated page.
     */
    public function goToLastPage(): void
    {
        $this->setPage($this->getPageCount());
    }

    /**
     * Jump to a bounded paginated page index.
     */
    public function goToPage(int $page): void
    {
        $this->setPage(max(1, min($page, $this->getPageCount())));
    }

    /**
     * Sort by a whitelisted field and toggle direction on repeat selection.
     */
    public function sortBy($field): void
    {
        if (! in_array($field, [
            'id',
            'name',
            'email',
            'roles.name',
        ], true)) {
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

    private function persistUiState(): void
    {
        $this->setUserSetting(self::UI_STATE_SETTING_KEY, [
            'search' => $this->search,
            'roleFilter' => $this->roleFilter,
            'perPage' => $this->perPage,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    /**
     * Open modal for assigning a single role to a user.
     */
    public function openEditRolesModal(int $userId): void
    {
        $this->resetValidation('editingRoleName');

        $user = User::query()
            ->with('roles')
            ->findOrFail($userId);

        $currentRoleName = (string) ($user->roles->first()?->name ?? '');

        $defaultRoleName = Role::query()
            ->where('name', 'User')
            ->where('is_assignable', true)
            ->value('name');

        $this->editingUserId = $user->id;
        $this->editingUserName = (string) $user->name;
        $this->editingUserEmail = (string) $user->email;
        $this->editingCurrentRoleName = $currentRoleName;
        $this->editingRoleName = $currentRoleName !== ''
            ? $currentRoleName
            : (string) ($defaultRoleName ?? '');
        $this->showEditRolesModal = true;
    }

    /**
     * Close role-edit modal and clear state.
     */
    public function closeEditRolesModal(): void
    {
        $this->resetEditRolesModal();
    }

    /**
     * Persist selected role for the edited user and write audit activity.
     */
    public function saveEditRoles(AdminActivity $adminActivity): void
    {
        if ($this->editingUserId === null) {
            return;
        }

        if ($this->editingRoleName === '') {
            $this->addError('editingRoleName', __('admin.user_list.please_select_a_role'));

            return;
        }

        $validRoleName = Role::query()
            ->where('name', $this->editingRoleName)
            ->where('is_assignable', true)
            ->value('name');

        if ($validRoleName === null) {
            $this->addError('editingRoleName', __('admin.user_list.please_select_a_valid_role'));

            return;
        }

        $user = User::query()
            ->with('roles')
            ->findOrFail($this->editingUserId);

        $beforeRoles = $user
            ->roles
            ->pluck('name')
            ->map(fn($role): string => (string) $role)
            ->values()
            ->all();

        $user->syncRoles([$validRoleName]);

        $user->load('roles');

        $afterRoles = $user
            ->roles
            ->pluck('name')
            ->map(fn($role): string => (string) $role)
            ->values()
            ->all();

        $adminActivity->userRoleChanged(
            targetUser: $user,
            beforeRoles: $beforeRoles,
            afterRoles: $afterRoles,
        );

        $savedUserName = (string) $user->name;
        $savedRoleName = (string) $validRoleName;

        $this->resetEditRolesModal();

        Flux::toast(
            heading: __('admin.user_list.user_role_updated'),
            text: __('admin.user_list.the_role_for_user_has_been_changed_to_role', [
                'user' => $savedUserName,
                'role' => $savedRoleName,
            ]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

    /**
     * Reset role-edit modal fields.
     */
    private function resetEditRolesModal(): void
    {
        $this->resetValidation('editingRoleName');

        $this->showEditRolesModal = false;
        $this->editingUserId = null;
        $this->editingUserName = '';
        $this->editingUserEmail = '';
        $this->editingCurrentRoleName = '';
        $this->editingRoleName = '';
    }

    /**
     * Build base filtered user query for listing and pagination.
     */
    private function getFilteredUserQuery()
    {
        $query = User::query()
            ->where(function ($q) {
                $q->where('users.name', 'ilike', "%{$this->search}%")
                    ->orWhere('users.email', 'ilike', "%{$this->search}%");
            });

        if ($this->roleFilter === '__none__') {
            $query->doesntHave('roles');
        } elseif ($this->roleFilter !== '') {
            $query->whereHas('roles', function ($q) {
                $q->where('roles.name', $this->roleFilter);
            });
        }

        return $query;
    }

    /**
     * Resolve total page count for current filters and per-page setting.
     */
    private function getPageCount(): int
    {
        $total = $this->getFilteredUserQuery()->count('*');

        return max(1, (int) ceil($total / (int) $this->perPage));
    }

    /**
     * Build paginated users and role summary payload for rendering.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(RoleBadgeResolver $roleBadgeResolver)
    {
        $query = $this->getFilteredUserQuery()
            ->with('roles');

        if ($this->sortField === 'roles.name') {
            $query = $query
                ->leftJoin('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', '=', User::class);
                })
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $this->sortDirection)
                ->select('users.*');
        } else {
            $query = $query->orderBy('users.' . $this->sortField, $this->sortDirection);
        }

        $this->perPage = $this->normalizePerPage($this->perPage);

        $users = $query->paginate($this->perPage);

        $assignableRoleCountsByCategory = Role::query()
            ->where('is_assignable', true)
            ->selectRaw("COALESCE(category, 'other') as category, COUNT(*) as aggregate")
            ->groupByRaw("COALESCE(category, 'other')")
            ->pluck('aggregate', 'category')
            ->map(fn($value): int => (int) $value)
            ->all();

        $assignedUserCountsByRoleCategory = User::query()
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id', 'inner', false)
            ->where('model_has_roles.model_type', User::class)
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id', 'inner', false)
            ->selectRaw("COALESCE(roles.category, 'other') as category, COUNT(DISTINCT users.id) as aggregate")
            ->groupByRaw("COALESCE(roles.category, 'other')")
            ->pluck('aggregate', 'category')
            ->map(fn($value): int => (int) $value)
            ->all();

        $assignedUserCountsByRole = Role::query()
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->where('roles.is_assignable', true)
            ->selectRaw("
        roles.name,
        COALESCE(roles.category, 'other') as category,
        roles.sort_order,
        COUNT(DISTINCT model_has_roles.model_id) as users_count
    ")
            ->groupBy('roles.id', 'roles.name', 'roles.category', 'roles.sort_order')
            ->orderBy('roles.category')
            ->orderBy('roles.sort_order')
            ->orderBy('roles.name')
            ->get()
            ->groupBy('category')
            ->map(
                fn($roles) => $roles
                    ->map(fn($role): array => [
                        'name' => (string) $role->name,
                        'usersCount' => (int) $role->users_count,
                    ])
                    ->values()
                    ->all()
            )
            ->all();

        $summary = [
            'totalUsers' => User::query()->count('*'),
            'withoutRoleUsers' => User::query()->doesntHave('roles')->count('*'),
            'assignedUsersByRoleCategory' => $assignedUserCountsByRoleCategory,
            'assignableRolesByCategory' => $assignableRoleCountsByCategory,
            'assignedUsersByRole' => $assignedUserCountsByRole,
        ];

        $assignableRoles = Role::query()
            ->where('is_assignable', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'name',
                'category',
                'sort_order',
                'description',
            ]);

        $roles = $assignableRoles
            ->pluck('name')
            ->all();

        $roleGroups = $assignableRoles
            ->groupBy(fn(Role $role): string => (string) ($role->category ?: 'other'))
            ->map(fn($group) => $group->values())
            ->all();

        $roleBadges = collect($roles)
            ->mapWithKeys(fn(string $role): array => [
                $role => $roleBadgeResolver->forRole($role),
            ])
            ->all();

        return view('components.admin.⚡user-list', [
            'users' => $users,
            'roles' => $roles,
            'roleGroups' => $roleGroups,
            'roleBadges' => $roleBadges,
            'withoutRoleBadge' => $roleBadgeResolver->withoutRole(),
            'summary' => $summary,
        ]);
    }
}
