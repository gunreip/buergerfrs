<?php

namespace App\Providers;

use App\Listeners\LogLogin;
use App\Listeners\LogLoginFailed;
use App\Listeners\LogLogout;
use App\Listeners\LogPasswordReset;
use App\Listeners\LogRegistered;
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
            LogRegistered::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            LogLogin::class,
        ],
        'Illuminate\Auth\Events\Logout' => [
            LogLogout::class,
        ],
        'Illuminate\Auth\Events\Failed' => [
            LogLoginFailed::class,
        ],
        'Illuminate\Auth\Events\PasswordReset' => [
            LogPasswordReset::class,
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
