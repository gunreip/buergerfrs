<?php

use App\Settings\AppGeneralSettings;
use Flux\Flux;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdateApplier;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdatePlanner;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchEntries;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineEvent;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    if (! in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        $this->markTestSkipped('The isolated Workbench workflow requires pdo_sqlite (Ubuntu: php8.5-sqlite3).');
    }
    // A dedicated connection is forced even if PHPUnit was launched with a real DB configured.
    config(['database.default' => 'workbench_workflow_test', 'database.connections.workbench_workflow_test' => [
        'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
    ]]);
    DB::purge('workbench_workflow_test');
    // Production uses PostgreSQL BTRIM; expose its single-argument semantics in SQLite.
    DB::connection()->getPdo()->sqliteCreateFunction('BTRIM', static fn (?string $value): ?string => $value === null ? null : trim($value, ' '), 1);
    Schema::create('users', fn (Blueprint $table) => $table->id());
    Schema::create('languages', fn (Blueprint $table) => $table->id());
    Schema::create('countries', fn (Blueprint $table) => $table->id());
    (require base_path('database/migrations/2026_05_13_082530_create_locales_table.php'))->up();
    foreach (glob(base_path('packages/gunreip/laravel-translation-workbench/database/migrations/*.php')) as $migration) {
        (require $migration)->up();
    }
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'de';
    $settings->availableLocales = ['en', 'de'];
    $settings->addedPrimaryLocales = [];
    app()->instance(AppGeneralSettings::class, $settings);
    Flux::shouldReceive('toast')->andReturnNull();

    $this->projectBase = base_path();
    $this->projectLang = lang_path();
    $this->fixtureRoot = sys_get_temp_dir().'/tw-workflow-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->fixtureRoot.'/resources/views', 0755, true);
    File::makeDirectory($this->fixtureRoot.'/lang', 0755, true);
    app()->setBasePath($this->fixtureRoot);
    app()->useLangPath($this->fixtureRoot.'/lang');
    config(['translation-workbench.source_locale' => 'en']);
});

afterEach(function () {
    if (isset($this->projectBase)) {
        app()->setBasePath($this->projectBase);
        app()->useLangPath($this->projectLang);
        File::deleteDirectory($this->fixtureRoot);
    }
    DB::purge('workbench_workflow_test');
});

it('preserves a reviewed translation and its history through export, code update and rescan', function () {
    $source = "<p>{{ __('Welcome aboard') }}</p>\n";
    $file = base_path('resources/views/welcome.blade.php');
    File::put($file, $source);
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $items = $scanner->scan(['resources/views']);
    expect($items)->toHaveCount(1);
    $syncer->sync($items);
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $planner = app(TranslationWorkbenchCodeUpdatePlanner::class);
    expect($planner->plan()['summary']['safe_updates'])->toBe(0);

    // Exercise the real user actions; only the notification transport is stubbed.
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey($finding->id);
    $key->refresh();
    expect($key->review_status)->toBe('reviewed');
    // Review alone does not authorize replacement before translations are available.
    expect($planner->plan()['summary']['safe_updates'])->toBe(0);
    $editor->editFindingId = $finding->id;
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    expect(TranslationWorkbenchReview::where('decision', 'suggested_key_accepted')->count())->toBe(1);
    expect(TranslationWorkbenchLangValue::where('locale', 'de')->value('value'))->toBe('Willkommen an Bord');

    $exporter = app(TranslationWorkbenchLangFileExporter::class);
    $dryExport = $exporter->export(['en', 'de']);
    expect($dryExport['files_written'])->toBe(0)->and(File::allFiles(lang_path()))->toHaveCount(0);
    $export = $exporter->export(['en', 'de'], write: true);
    expect($export['files_written'])->toBe(2);
    $target = TranslationWorkbenchLangValue::where('locale', 'de')->sole();
    $exported = require lang_path('de/'.$target->namespace.'.php');
    expect(Arr::get($exported, $target->lang_key))->toBe('Willkommen an Bord');

    $plan = $planner->plan();
    expect($plan['summary']['safe_updates'])->toBe(1);
    $applier = app(TranslationWorkbenchCodeUpdateApplier::class);
    $dryApply = $applier->apply();
    expect($dryApply['summary']['would_apply'])->toBe(1)->and(File::get($file))->toBe($source);
    $applied = $applier->apply(write: true);
    expect($applied['summary']['applied'])->toBe(1)
        ->and($applied['diff'])->toBe($dryApply['diff'])
        ->and(File::get($file))->toContain("__('".$key->translation_key."')")
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(1);
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->sole();
    expect($event->key_id)->toBe($key->id)
        ->and($event->finding_id)->toBe($finding->id)
        ->and($event->old_values['raw_expression'])->toBe("__('Welcome aboard')")
        ->and($event->new_values['new_expression'])->toBe("__('".$key->translation_key."')");

    $syncer->sync($scanner->scan(['resources/views']), markObsolete: true);
    expect(TranslationWorkbenchKey::where('translation_key', $key->translation_key)->count())->toBe(1)
        ->and($key->fresh()->review_status)->toBe('reviewed')
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->value('value'))->toBe('Willkommen an Bord')
        ->and($planner->plan()['summary']['safe_updates'])->toBe(0);
    expect($applier->apply(write: true)['summary']['applied'])->toBe(0);
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(1);
});

it('deduplicates dynamic source types without mixing unrelated or obsolete discoveries', function () {
    File::put(base_path('resources/views/welcome.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    foreach (['collection', 'collection', 'enum', 'unrelated', 'obsolete'] as $index => $type) {
        DB::table('translation_workbench_option_discoveries')->insert([
            'fingerprint' => hash('sha256', (string) $index),
            'scope' => 'test',
            'source_path' => 'resources/views/options.blade.php',
            'options_variable' => '$options',
            'suggested_key' => $type === 'unrelated' ? 'other.key' : $key->suggested_key,
            'source_type' => $type,
            'status' => $type === 'obsolete' ? 'obsolete' : 'open',
        ]);
    }

    $selected = (new ReflectionMethod(TranslationWorkbenchEntries::class, 'selectedFinding'))
        ->invoke(new TranslationWorkbenchEntries, $finding->id);

    expect(explode(', ', $selected->dynamic_source_types))->toEqualCanonicalizing(['collection', 'enum']);
});

it('keeps reviewed source unchanged while the target translation is missing', function () {
    $file = base_path('resources/views/welcome.blade.php');
    $source = "{{ __('Welcome aboard') }}";
    File::put($file, $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    $editor->editFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = null;
    $editor->saveTranslationValue();

    $report = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);

    expect($report['summary']['applied'])->toBe(0)
        ->and(File::get($file))->toBe($source)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(0);
});

it('does not overwrite source edited after the scan', function () {
    $file = base_path('resources/views/welcome.blade.php');
    File::put($file, "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    $editor->editFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    $changedSource = "{{ __('A different welcome') }}";
    File::put($file, $changedSource);

    $report = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);

    expect($report['summary']['applied'])->toBe(0)
        ->and(File::get($file))->toBe($changedSource)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(0);
});
