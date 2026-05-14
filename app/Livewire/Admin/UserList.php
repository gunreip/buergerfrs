<?php

// app/Livewire/Admin/UserList.php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Support\Audit\AdminActivity;
use App\Support\Settings\RoleBadgeResolver;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    private const PER_PAGE_SETTING_KEY = 'ui.per_page.admin_users';

    use WithoutUrlPagination;
    use WithPagination;

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

    public function mount(): void
    {
        $this->perPage = $this->normalizePerPage(
            auth()->user()?->setting(self::PER_PAGE_SETTING_KEY, $this->perPage)
        );

        $this->setPage(1);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->setPage(1);
    }

    public function updatedPerPage(): void
    {
        $this->perPage = $this->normalizePerPage($this->perPage);

        $user = auth()->user();

        if ($user instanceof User) {
            $user->setSetting(self::PER_PAGE_SETTING_KEY, $this->perPage);
            $user->save();
        }

        $this->setPage(1);
    }

    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        $allowedValues = [10, 25, 50, 100];

        if (! in_array($value, $allowedValues, true)) {
            return 50;
        }

        return $value;
    }

    public function goToFirstPage(): void
    {
        $this->setPage(1);
    }

    public function goToPreviousPage(): void
    {
        $this->previousPage();
    }

    public function goToNextPage(): void
    {
        $this->nextPage();
    }

    public function goToLastPage(): void
    {
        $this->setPage($this->getPageCount());
    }

    public function goToPage(int $page): void
    {
        $this->setPage(max(1, min($page, $this->getPageCount())));
    }

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

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

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

    public function closeEditRolesModal(): void
    {
        $this->resetEditRolesModal();
    }

    public function saveEditRoles(AdminActivity $adminActivity): void
    {
        if ($this->editingUserId === null) {
            return;
        }

        if ($this->editingRoleName === '') {
            $this->addError('editingRoleName', __('Please select a role.'));

            return;
        }

        $validRoleName = Role::query()
            ->where('name', $this->editingRoleName)
            ->where('is_assignable', true)
            ->value('name');

        if ($validRoleName === null) {
            $this->addError('editingRoleName', __('Please select a valid role.'));

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
            heading: __('User role updated'),
            text: __('The role for :user has been changed to :role.', [
                'user' => $savedUserName,
                'role' => $savedRoleName,
            ]),
            variant: 'success',
            duration: 3000,
        );

        $this->dispatch('$refresh');
    }

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

    private function getPageCount(): int
    {
        $total = $this->getFilteredUserQuery()->count();

        return max(1, (int) ceil($total / (int) $this->perPage));
    }

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
            ->join('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
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
            'totalUsers' => User::query()->count(),
            'withoutRoleUsers' => User::query()->doesntHave('roles')->count(),
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
