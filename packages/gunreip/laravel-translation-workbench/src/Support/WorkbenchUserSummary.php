<?php

namespace Gunreip\TranslationWorkbench\Support;

use Illuminate\Support\Facades\Auth;

class WorkbenchUserSummary
{
    /** Current account details, not a historical snapshot of the actor. */
    public function find(int $id): ?array
    {
        $user = Auth::getProvider()->retrieveById($id);
        if ($user === null) {
            return null;
        }

        return [
            'id' => $user->getAuthIdentifier(),
            'name' => (string) ($user->name ?? ''),
            'email' => (string) ($user->email ?? ''),
            'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->all() : [],
        ];
    }
}
