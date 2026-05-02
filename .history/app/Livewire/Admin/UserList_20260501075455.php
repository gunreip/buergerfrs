<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use WithPagination;
    public $search = '';
    public $perPage = 50;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 50],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
    ];

    // Setze Pagination zurück, wenn Suche oder perPage geändert wird
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = User::with('roles')
            ->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.email', 'like', "%{$this->search}%");
            });

        // Sortierung
        if ($this->sortField === 'roles.name') {
            $query = $query->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $this->sortDirection)
                ->select('users.*');
        } else {
            $query = $query->orderBy('users.' . $this->sortField, $this->sortDirection);
        }

        $users = $query->paginate($this->perPage);
        // Livewire Pagination-Links korrekt rendern
        $users->appends(['search' => $this->search, 'perPage' => $this->perPage]);

        return view('components.admin.⚡user-list', [
            'users' => $users,
        ]);
    }
}
