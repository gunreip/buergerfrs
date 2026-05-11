<?php

// app/Support/Auth/GeneratedPasswordLogger.php

namespace App\Support\Auth;

use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class GeneratedPasswordLogger
{
    private const DISK = 'local';

    private const PATH = 'dev/generated-user-passwords.jsonl';

    /**
     * Write a generated password to a local-only dev log file.
     */
    public function write(User $user, Person $person, string $password, ?User $createdByUser = null): bool
    {
        if (! app()->environment('local')) {
            return false;
        }

        Storage::disk(self::DISK)->append(self::PATH, json_encode([
            'created_at' => now()->toIso8601String(),
            'expires_at' => now()->addDay()->toIso8601String(),
            'user_id' => $user->id,
            'person_id' => $person->id,
            'email' => $user->email,
            'password' => $password,
            'created_by_user_id' => $createdByUser?->id,
        ], JSON_THROW_ON_ERROR));

        return true;
    }
}
