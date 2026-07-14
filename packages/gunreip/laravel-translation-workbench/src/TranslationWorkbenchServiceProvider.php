<?php

namespace Gunreip\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Console\ImportExistingTranslations;
use Gunreip\TranslationWorkbench\Console\ImportTranslationWorkbenchLangValues;
use Gunreip\TranslationWorkbench\Console\DiscoverDynamicOptions;
use Gunreip\TranslationWorkbench\Console\DetectDuplicateCandidates;
use Gunreip\TranslationWorkbench\Console\RunTranslationWorkbench;
use Gunreip\TranslationWorkbench\Console\ScanTranslationWorkbench;
use Gunreip\TranslationWorkbench\Console\SyncTranslationWorkbenchFoundation;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchEntries;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchOldEntries;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawData;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataNew;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TranslationWorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/translation-workbench.php',
            'translation-workbench',
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'translation-workbench');

        Livewire::component('translation-workbench.entries', TranslationWorkbenchEntries::class);
        Livewire::component('translation-workbench.old-entries', TranslationWorkbenchOldEntries::class);
        Livewire::component('translation-workbench.raw-data', TranslationWorkbenchRawData::class);
        Livewire::component('translation-workbench.raw-data-new', TranslationWorkbenchRawDataNew::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                DetectDuplicateCandidates::class,
                DiscoverDynamicOptions::class,
                ImportExistingTranslations::class,
                ImportTranslationWorkbenchLangValues::class,
                RunTranslationWorkbench::class,
                ScanTranslationWorkbench::class,
                SyncTranslationWorkbenchFoundation::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/translation-workbench.php' => config_path('translation-workbench.php'),
            ], 'translation-workbench-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'translation-workbench-migrations');
        }
    }
}
