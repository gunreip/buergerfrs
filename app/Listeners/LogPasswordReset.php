<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;

class LogPasswordReset
{
    /**
     * Record a successful password reset without persisting secret material.
     */
    public function handle(PasswordReset $event): void
    {
        activity('auth')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->event('password_reset')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('User password reset');
    }
}
