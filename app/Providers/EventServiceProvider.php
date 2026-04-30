<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        'Illuminate\Auth\Events\Registered' => [
            \App\Listeners\LogRegistered::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            \App\Listeners\LogLogin::class,
        ],
        'Illuminate\Auth\Events\Logout' => [
            \App\Listeners\LogLogout::class,
        ],
        'Illuminate\Auth\Events\Failed' => [
            \App\Listeners\LogLoginFailed::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
