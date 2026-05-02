<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class UserList extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    public $search = '';
    public $perPage = 50;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
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

    private function getPageCount(): int
    {
        $total = User::query()
            ->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.email', 'like', "%{$this->search}%");
            })
            ->count();

        return max(1, (int) ceil($total / (int) $this->perPage));
    }

    public function render()
    {
        $query = User::with('roles')
            ->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.email', 'like', "%{$this->search}%");
            });

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
        ]);
    }
}
