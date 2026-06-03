<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait InteractsWithUserSettings
{
    protected function userSetting(string $key, mixed $default = null): mixed
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return $default;
        }

        return $user->setting($key, $default);
    }

    protected function setUserSetting(string $key, mixed $value): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $user->setSetting($key, $value);
        $user->save();
    }
}
