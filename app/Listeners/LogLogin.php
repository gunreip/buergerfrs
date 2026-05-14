<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogLogin
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
    public function handle(Login $event): void
    {
        activity('auth')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->event('login')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('User logged in');
    }
}
