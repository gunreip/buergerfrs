<?php

namespace Gunreip\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Console\ApplySuspiciousKeyRestores;
use Gunreip\TranslationWorkbench\Console\ApplyTranslationWorkbenchCodeUpdates;
use Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues;
use Gunreip\TranslationWorkbench\Console\ClassifyLangNodeTypes;
use Gunreip\TranslationWorkbench\Console\ClassifyTimelineEventNoise;
use Gunreip\TranslationWorkbench\Console\CleanupExcludedTranslationWorkbenchPaths;
use Gunreip\TranslationWorkbench\Console\CollectTimelineChains;
use Gunreip\TranslationWorkbench\Console\DetectDuplicateCandidates;
use Gunreip\TranslationWorkbench\Console\DetectSharedKeyCandidates;
use Gunreip\TranslationWorkbench\Console\DetectSuspiciousKeyedAdditions;
use Gunreip\TranslationWorkbench\Console\DiscoverDynamicOptions;
use Gunreip\TranslationWorkbench\Console\DiscoverDynamicSourceCandidates;
use Gunreip\TranslationWorkbench\Console\ExportTranslationWorkbenchLangFiles;
use Gunreip\TranslationWorkbench\Console\ImportExistingTranslations;
use Gunreip\TranslationWorkbench\Console\ImportTranslationWorkbenchLangValues;
use Gunreip\TranslationWorkbench\Console\InventoryTranslationWorkbenchKeys;
use Gunreip\TranslationWorkbench\Console\PlanTranslationWorkbenchCodeUpdates;
use Gunreip\TranslationWorkbench\Console\ProfileTranslationWorkbenchFindings;
use Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources;
use Gunreip\TranslationWorkbench\Console\RunTranslationWorkbench;
use Gunreip\TranslationWorkbench\Console\ScanTranslationWorkbench;
use Gunreip\TranslationWorkbench\Console\SyncTranslationWorkbenchFoundation;
use Gunreip\TranslationWorkbench\Foundation\RuntimeDynamicTranslationCollector;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchEntries;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchOldEntries;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawData;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataNew;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchSettings;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Illuminate\Support\Facades\Blade;
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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tw-graph-defaults.php',
            'tw-graph-defaults',
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../config/defaults/tw-graph-data-driven.php',
            'tw-graph-data-driven-defaults',
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../config/layout-corrections/tw-graph-data-driven.php',
            'tw-graph-data-driven-layout-corrections',
        );

        $this->app->singleton(RuntimeDynamicTranslationCollector::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'translation-workbench');
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'translation-workbench');

        Livewire::component('translation-workbench.entries', TranslationWorkbenchEntries::class);
        Livewire::component('translation-workbench.old-entries', TranslationWorkbenchOldEntries::class);
        Livewire::component('translation-workbench.raw-data', TranslationWorkbenchRawData::class);
        Livewire::component('translation-workbench.raw-data-new', TranslationWorkbenchRawDataNew::class);
        Livewire::component('translation-workbench.settings', TranslationWorkbenchSettings::class);
        Livewire::component('translation-workbench.tw-graph.data-driven.datasets', TwGraphDataDrivenDatasets::class);

        $this->commands([
            ApplySuspiciousKeyRestores::class,
            ApplyTranslationWorkbenchCodeUpdates::class,
            ClassifyDynamicValues::class,
            ClassifyLangNodeTypes::class,
            ClassifyTimelineEventNoise::class,
            CleanupExcludedTranslationWorkbenchPaths::class,
            CollectTimelineChains::class,
            DetectDuplicateCandidates::class,
            DetectSharedKeyCandidates::class,
            DetectSuspiciousKeyedAdditions::class,
            DiscoverDynamicOptions::class,
            DiscoverDynamicSourceCandidates::class,
            ImportExistingTranslations::class,
            ImportTranslationWorkbenchLangValues::class,
            InventoryTranslationWorkbenchKeys::class,
            ExportTranslationWorkbenchLangFiles::class,
            PlanTranslationWorkbenchCodeUpdates::class,
            ProfileTranslationWorkbenchFindings::class,
            ResolveUnknownDynamicSources::class,
            RunTranslationWorkbench::class,
            ScanTranslationWorkbench::class,
            SyncTranslationWorkbenchFoundation::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/translation-workbench.php' => config_path('translation-workbench.php'),
                __DIR__ . '/../config/tw-graph-defaults.php' => config_path('tw-graph-defaults.php'),
                __DIR__ . '/../config/defaults/tw-graph-data-driven.php' => config_path('defaults/tw-graph-data-driven.php'),
                __DIR__ . '/../config/layout-corrections/tw-graph-data-driven.php' => config_path('layout-corrections/tw-graph-data-driven.php'),
            ], 'translation-workbench-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'translation-workbench-migrations');

            $this->publishes([
                __DIR__ . '/../resources/img' => public_path('vendor/translation-workbench/img'),
            ], 'translation-workbench-assets');
        }
    }
}
