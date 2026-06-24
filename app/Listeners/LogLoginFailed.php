<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Arr;

class LogLoginFailed
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $identifier = collect(Arr::except($event->credentials, ['password']))
            ->first(fn (mixed $value): bool => is_scalar($value) && trim((string) $value) !== '');

        activity('auth')
            ->event('login_failed')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'credential_keys' => array_values(array_keys(Arr::except($event->credentials, ['password']))),
                'login_identifier_hash' => is_scalar($identifier)
                    ? hash('sha256', mb_strtolower(trim((string) $identifier)))
                    : null,
                'actor' => [
                    'type' => 'anonymous',
                ],
            ])
            ->log('Login failed');
    }
}
