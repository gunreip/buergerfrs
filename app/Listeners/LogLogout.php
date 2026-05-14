<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogLogout
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
    public function handle(Logout $event): void
    {
        activity('auth')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->event('logout')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('User logged out');
    }
}
