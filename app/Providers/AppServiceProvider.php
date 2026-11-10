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
use Livewire\Livewire;
use Spatie\Permission\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Blade touches compiled files with explicit timestamps, which requires ownership.
        // Resolve this at runtime (also with cached config), not while building config:cache.
        $this->app->beforeResolving('blade.compiler', function () {
            if ($this->app->environment('local') && ! $this->app->runningUnitTests() && function_exists('posix_geteuid')) {
                $base = storage_path('framework/views');
                if (rtrim((string) config('view.compiled'), '/') === $base) {
                    config(['view.compiled' => $base.'/user-'.posix_geteuid()]);
                }
            }
        });

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
        // Recheck the original route's role requirement on Livewire updates, too.
        Livewire::addPersistentMiddleware([RoleMiddleware::class]);

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
