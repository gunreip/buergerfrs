<?php

namespace Gunreip\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Console\ApplySuspiciousKeyRestores;
use Gunreip\TranslationWorkbench\Console\ApplyTranslationWorkbenchCodeUpdates;
use Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues;
use Gunreip\TranslationWorkbench\Console\ClassifyLangNodeTypes;
use Gunreip\TranslationWorkbench\Console\ClassifyTimelineEventNoise;
use Gunreip\TranslationWorkbench\Console\CleanupExcludedTranslationWorkbenchPaths;
use Gunreip\TranslationWorkbench\Console\CollectTimelineChains;
use Gunreip\TranslationWorkbench\Console\CompareTranslationWorkbenchValues;
use Gunreip\TranslationWorkbench\Console\DetectSharedKeyCandidates;
use Gunreip\TranslationWorkbench\Console\DetectSuspiciousKeyedAdditions;
use Gunreip\TranslationWorkbench\Console\DiscoverDynamicOptions;
use Gunreip\TranslationWorkbench\Console\DiscoverDynamicSourceCandidates;
use Gunreip\TranslationWorkbench\Console\ExportTranslationWorkbenchLangFiles;
use Gunreip\TranslationWorkbench\Console\ImportTranslationWorkbenchLangValues;
use Gunreip\TranslationWorkbench\Console\InventoryTranslationWorkbenchKeys;
use Gunreip\TranslationWorkbench\Console\PlanTranslationWorkbenchCodeUpdates;
use Gunreip\TranslationWorkbench\Console\ProfileTranslationWorkbenchFindings;
use Gunreip\TranslationWorkbench\Console\ReconcileTranslationWorkbenchSources;
use Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources;
use Gunreip\TranslationWorkbench\Console\RunTranslationWorkbench;
// use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawData;
use Gunreip\TranslationWorkbench\Console\ScanWorkingGeneration;
use Gunreip\TranslationWorkbench\Console\SyncTranslationWorkbenchFoundation;
use Gunreip\TranslationWorkbench\Foundation\RuntimeDynamicTranslationCollector;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPipelineContext;
use Gunreip\TranslationWorkbench\Generation\GenerationDatabase;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementWriter;
use Gunreip\TranslationWorkbench\Livewire\CurrentSourceReview;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbench;
use Gunreip\TranslationWorkbench\Livewire\SourceCaseReview;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataChainPreview;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchRawDataNew;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchSettings;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchWorkflow;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDatasetDetails;
use Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation;
use Gunreip\TranslationWorkbench\Livewire\TwGraphOverview;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession;
use Gunreip\TranslationWorkbench\View\Components\HeadingCounterGroup;
use Gunreip\TranslationWorkbench\View\Components\TwGraph;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TranslationWorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(\Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment::class);
        $this->app->scoped(GenerationEditor::class, function ($app) {
            $database = $app->make(GenerationDatabase::class);
            $connection = $database->connection();
            $database->verify($connection);

            return new GenerationEditor($connection);
        });
        $this->app->scoped(GenerationPublisher::class, function ($app) {
            $database = $app->make(GenerationDatabase::class);
            $connection = $database->connection();
            $database->verify($connection);

            return new GenerationPublisher($connection, lang_path());
        });
        $this->app->scoped(SourceReplacementWriter::class, function ($app) {
            $database = $app->make(GenerationDatabase::class);
            $connection = $database->connection();
            $database->verify($connection);

            return new SourceReplacementWriter($connection, base_path(), lang_path(), $app->make(TranslationWorkbenchScanner::class));
        });
        $this->mergeConfigFrom(
            __DIR__.'/../config/translation-workbench.php',
            'translation-workbench',
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/tw-graph-defaults.php',
            'tw-graph-defaults',
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/defaults/tw-graph-data-driven.php',
            'tw-graph-data-driven-defaults',
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/layout-corrections/tw-graph-data-driven.php',
            'tw-graph-data-driven-layout-corrections',
        );

        $this->app->singleton(RuntimeDynamicTranslationCollector::class);
        $this->app->scoped(TranslationWorkbenchScanSession::class);
        $this->app->scoped(TranslationWorkbenchPipelineContext::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'translation-workbench');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'translation-workbench');
        Blade::component(TwGraph::class, 'translation-workbench::ui.tw-graph');
        Blade::component(HeadingCounterGroup::class, 'translation-workbench::ui.common.heading-counter-group');

        Livewire::component('translation-workbench.tw-graph.documentation', TwGraphDocumentation::class);
        Livewire::component('translation-workbench.tw-graph.overview', TwGraphOverview::class);
        Livewire::component('translation-workbench.entries', TranslationWorkbench::class);
        Livewire::component('translation-workbench.current-source-review', CurrentSourceReview::class);
        Livewire::component('translation-workbench.source-case-review', SourceCaseReview::class);
        Livewire::component('translation-workbench.workflow', TranslationWorkbenchWorkflow::class);
        // Retired page aliases; classes remain available for reference and shared inheritance.
        // Livewire::component('translation-workbench.raw-data', TranslationWorkbenchRawData::class);
        Livewire::component('translation-workbench.raw-data-new', TranslationWorkbenchRawDataNew::class);
        Livewire::component('translation-workbench.raw-data-chain-preview', TranslationWorkbenchRawDataChainPreview::class);
        Livewire::component('translation-workbench.settings', TranslationWorkbenchSettings::class);
        Livewire::component('translation-workbench.tw-graph.data-driven.datasets', TwGraphDataDrivenDatasets::class);
        Livewire::component('translation-workbench.tw-graph.data-driven.dataset-details', TwGraphDatasetDetails::class);

        $this->commands([
            ScanWorkingGeneration::class,
            ApplySuspiciousKeyRestores::class,
            ApplyTranslationWorkbenchCodeUpdates::class,
            ClassifyDynamicValues::class,
            ClassifyLangNodeTypes::class,
            ClassifyTimelineEventNoise::class,
            CleanupExcludedTranslationWorkbenchPaths::class,
            CollectTimelineChains::class,
            DetectSharedKeyCandidates::class,
            DetectSuspiciousKeyedAdditions::class,
            DiscoverDynamicOptions::class,
            DiscoverDynamicSourceCandidates::class,
            ImportTranslationWorkbenchLangValues::class,
            InventoryTranslationWorkbenchKeys::class,
            ExportTranslationWorkbenchLangFiles::class,
            CompareTranslationWorkbenchValues::class,
            PlanTranslationWorkbenchCodeUpdates::class,
            ProfileTranslationWorkbenchFindings::class,
            ResolveUnknownDynamicSources::class,
            RunTranslationWorkbench::class,
            SyncTranslationWorkbenchFoundation::class,
            ReconcileTranslationWorkbenchSources::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/translation-workbench.php' => config_path('translation-workbench.php'),
                __DIR__.'/../config/tw-graph-defaults.php' => config_path('tw-graph-defaults.php'),
                __DIR__.'/../config/defaults/tw-graph-data-driven.php' => config_path('defaults/tw-graph-data-driven.php'),
                __DIR__.'/../config/layout-corrections/tw-graph-data-driven.php' => config_path('layout-corrections/tw-graph-data-driven.php'),
            ], 'translation-workbench-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'translation-workbench-migrations');

            $this->publishes([
                __DIR__.'/../resources/img' => public_path('vendor/translation-workbench/img'),
            ], 'translation-workbench-assets');
        }
    }
}
