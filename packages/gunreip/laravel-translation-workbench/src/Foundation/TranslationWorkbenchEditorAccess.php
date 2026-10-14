<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TranslationWorkbenchEditorAccess
{
    public function authorize(): void
    {
        $user = Auth::user();
        if ($user instanceof Model) {
            $user->unsetRelation('roles');
        }
        if (! $user || ! method_exists($user, 'hasAnyRole') || ! $user->hasAnyRole(['Admin', 'Super-Admin'])) {
            throw new AuthorizationException;
        }
    }

    /** @return array{0: TranslationWorkbenchKey, 1: TranslationWorkbenchFinding} */
    public function assignment(int $keyId, int $findingId): array
    {
        $key = TranslationWorkbenchKey::query()->find($keyId);
        $finding = TranslationWorkbenchFinding::query()->find($findingId);
        $linked = TranslationWorkbenchKeyFinding::query()->where('key_id', $keyId)
            ->where('finding_id', $findingId)->where('status', 'active')->exists();
        if (! $key || ! $finding || ! $linked) {
            throw ValidationException::withMessages(['editFindingId' => __('The finding and translation key must have an active assignment. Refresh the editor and try again.')]);
        }

        return [$key, $finding];
    }
}
