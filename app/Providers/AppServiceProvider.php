<?php

namespace App\Providers;

use App\Support\Debugbar\OverviewViewsCollectorProvider;
use Carbon\CarbonImmutable;
use Fruitcake\LaravelDebugbar\CollectorProviders\ViewsCollectorProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Debugbar is optional (require-dev); keep other requests on its normal view profile.
        if (class_exists(ViewsCollectorProvider::class)) {
            $this->app->bind(ViewsCollectorProvider::class, OverviewViewsCollectorProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureComponents();
    }

    protected function configureComponents(): void
    {
        Blade::anonymousComponentNamespace('livewire.admin.pages', 'pages');
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : null,
        );
    }
}
