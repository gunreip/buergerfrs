<?php

// routes/admin.php

// use App\Livewire\Admin\UserEdit;
use App\Livewire\Admin\AppSettings;
use App\Livewire\Admin\ClientList;
use App\Livewire\Admin\PermissionList;
use App\Livewire\Admin\PersonList;
use App\Livewire\Admin\RoleList;
use App\Livewire\Admin\UserList;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Admin|Super-Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::view('/', 'dashboard')->name('dashboard');

        Route::get('users', UserList::class)->name('users');

        Route::get('people', PersonList::class)->name('people');

        Route::get('clients', ClientList::class)->name('clients');

        // Route::get('users/{user}/edit', UserEdit::class)->name('users.edit');

        Route::get('roles', RoleList::class)->name('roles');

        Route::get('permissions', PermissionList::class)->name('permissions');

        Route::get('app-settings', AppSettings::class)->name('app-settings');
    });
