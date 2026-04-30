<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    // Beispiel: Nur Admins
    Route::view('admin', 'dashboard')->middleware('role:Admin|Super-Admin')->name('admin.dashboard');
});

require __DIR__ . '/settings.php';
