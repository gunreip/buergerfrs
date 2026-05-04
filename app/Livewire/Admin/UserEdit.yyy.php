<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserEdit extends Component
{
    public $user;
    public $roles;
    public $selectedRoles = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->roles = Role::all();
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
    }

    public function save()
    {
        $this->user->syncRoles($this->selectedRoles);
        session()->flash('success', 'Rollen aktualisiert!');
    }

    public function render()
    {
        return view('components.admin.⚡user-edit', [
            'user' => $this->user,
            'roles' => $this->roles,
            'selectedRoles' => $this->selectedRoles,
        ]);
    }
}
