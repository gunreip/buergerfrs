<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Support\Settings\RoleBadgeResolver;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    public $search = '';
    public $roleFilter = '';
    public $perPage = 50;
    public $sortField = 'id';
    public $sortDirection = 'asc';

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
        $this->setPage(1);
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
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            $this->resetPage();

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    private function getFilteredUserQuery()
    {
        $query = User::query()
            ->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.email', 'like', "%{$this->search}%");
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

        $users = $query->paginate((int) $this->perPage);

        return view('components.admin.⚡user-list', [
            'users' => $users,
            'roles' => Role::query()
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        ]);
    }
}
