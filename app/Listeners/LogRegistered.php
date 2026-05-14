<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;

class LogRegistered
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
    public function handle(Registered $event): void
    {
        activity('auth')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->event('registered')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('User registered');
    }
}
