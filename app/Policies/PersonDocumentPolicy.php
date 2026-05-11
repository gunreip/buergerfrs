<?php

// app/Policies/PersonDocumentPolicy.php

namespace App\Policies;

use App\Models\PersonDocument;
use App\Models\User;

class PersonDocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Super-Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonDocument $personDocument): bool
    {
        return $user->hasAnyRole(['Admin', 'Super-Admin']);
    }

    /**
     * Determine whether the user can download the document file.
     */
    public function download(User $user, PersonDocument $personDocument): bool
    {
        return $this->view($user, $personDocument);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Super-Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonDocument $personDocument): bool
    {
        return $user->hasAnyRole(['Admin', 'Super-Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonDocument $personDocument): bool
    {
        return $user->hasAnyRole(['Admin', 'Super-Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PersonDocument $personDocument): bool
    {
        return $user->hasRole('Super-Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PersonDocument $personDocument): bool
    {
        return $user->hasRole('Super-Admin');
    }
}
