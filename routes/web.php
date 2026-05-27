<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    // Beispiel: Nur Admins
    Route::view('admin', 'dashboard')->middleware('role:Admin|Super-Admin')->name('admin.dashboard');

    // User- und Rollenverwaltung (Livewire)
    // Route::get('admin/users', \App\Livewire\Admin\UserList::class)
    //     ->middleware('role:Admin|Super-Admin')
    //     ->name('admin.users');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/management.php';
