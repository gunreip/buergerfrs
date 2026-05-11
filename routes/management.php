<?php

// routes/management.php

use App\Http\Controllers\Management\People\PersonDocumentController;
use App\Livewire\Management\People\CreatePerson;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->name('management.')
    ->group(function (): void {
        Route::get('people/create', CreatePerson::class)->name('people.create');

        Route::get('people/{person}/documents/{document}/inline', [PersonDocumentController::class, 'inline'])
            ->name('people.documents.inline');

        Route::get('people/{person}/documents/{document}/download', [PersonDocumentController::class, 'download'])
            ->name('people.documents.download');
    });
