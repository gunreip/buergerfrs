<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    public $search = '';
    public $users;

    public function mount()
    {
        $this->users = User::with('roles')->get();
    }

    public function render()
    {
        $users = User::with('roles')
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->get();
        return view('components.admin.⚡user-list', [
            'users' => $users,
        ]);
    }
}
