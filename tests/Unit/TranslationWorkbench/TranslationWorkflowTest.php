<?php

use App\Models\User;
use App\Settings\AppGeneralSettings;
use Flux\Flux;
use Gunreip\TranslationWorkbench\Console\RunTranslationWorkbench;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchBulkKeyWriter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCleanupReviewWriter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdateApplier;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdatePlanner;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchConflictReviewWriter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicEditor;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicSourceLinkWriter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyReviewEditor;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPipelineAccess;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPipelineContext;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPipelineProcess;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPipelineRunTracker;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchProvenanceReviewWriter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchScalarEditor;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchScalarTransformWriter;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTranslationWriter;
use Gunreip\TranslationWorkbench\Livewire\TranslationWorkbenchEntries;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyInventory;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineEvent;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

uses(TestCase::class);

function createLegacyExportTables(): void
{
    Schema::create('translation_keys', function (Blueprint $table) {
        $table->id();
        $table->string('key');
        $table->string('group')->nullable();
        $table->string('status');
        $table->string('workflow_status')->nullable();
    });
    Schema::create('translation_values', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('translation_key_id');
        $table->string('locale');
        $table->text('value');
        $table->string('status');
        $table->boolean('is_base_duplicate')->default(false);
    });
}

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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->json('settings')->nullable();
        $table->timestamps();
    });
    DB::table('users')->insert(['id' => 1]);
    $actor = Mockery::mock(User::class)->makePartial();
    $actor->setTable('users');
    $actor->exists = true;
    $actor->id = 1;
    $actor->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(true);
    $this->actingAs($actor);

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
    $this->projectStorage = storage_path();
    $this->fixtureRoot = sys_get_temp_dir().'/tw-workflow-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->fixtureRoot.'/resources/views', 0755, true);
    File::makeDirectory($this->fixtureRoot.'/lang', 0755, true);
    app()->setBasePath($this->fixtureRoot);
    app()->useLangPath($this->fixtureRoot.'/lang');
    app()->useStoragePath($this->fixtureRoot.'/storage');
    config(['translation-workbench.source_locale' => 'en']);
});

afterEach(function () {
    if (isset($this->projectBase)) {
        app()->setBasePath($this->projectBase);
        app()->useLangPath($this->projectLang);
        app()->useStoragePath($this->projectStorage);
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

it('blocks array to scalar export before or after pruning and preserves the entire file', function (bool $write, bool $pruneChild) {
    File::ensureDirectoryExists(lang_path('en'));
    $path = lang_path('en/ui.php');
    $original = "<?php\nreturn ['button' => ['cancel' => ['cancel' => 'Cancel']], 'sibling' => 'Keep'];\n";
    File::put($path, $original);
    foreach (['button.cancel', 'sibling'] as $langKey) {
        TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', $langKey), 'translation_key' => 'ui.'.$langKey, 'status' => 'open']);
        TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => $langKey, 'translation_key' => 'ui.'.$langKey, 'value' => $langKey === 'sibling' ? 'Updated sibling' : 'Cancel', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php']);
    }
    if ($pruneChild) {
        TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'button.cancel.cancel', 'translation_key' => 'ui.button.cancel.cancel', 'value' => 'Cancel', 'value_type' => 'string', 'status' => 'obsolete', 'source_path' => 'lang/en/ui.php']);
    }
    $before = TranslationWorkbenchLangValue::all()->toArray();

    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], ['ui'], $write);

    expect($report['values_conflicted'])->toBe(1)
        ->and($report['files_written'])->toBe(0)
        ->and($report['timeline_events_created'])->toBe(0)
        ->and($report['values_pruned'])->toBe($pruneChild ? 1 : 0)
        ->and($report['plans'][0]['conflicts'][0]['reason'])->toBe('array_to_scalar_conflict')
        ->and($report['plans'][0]['conflicts'][0]['blocking_child_keys'])->toBe(['button.cancel.cancel'])
        ->and($report['plans'][0]['conflicts'][0]['blocking_value_type'])->toBe('array')
        ->and(File::get($path))->toBe($original)
        ->and(TranslationWorkbenchLangValue::all()->toArray())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
})->with([false, true])->with([false, true]);

it('exports a scalar leaf without replacing its array parent or untouched sibling', function () {
    File::ensureDirectoryExists(lang_path('en'));
    $path = lang_path('en/ui.php');
    File::put($path, "<?php\nreturn ['button' => ['cancel' => ['cancel' => 'Old', 'hint' => 'Keep']]];\n");
    TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'leaf'), 'translation_key' => 'ui.button.cancel.cancel', 'status' => 'open']);
    TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'button.cancel.cancel', 'translation_key' => 'ui.button.cancel.cancel', 'value' => 'Cancel', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php']);

    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], ['ui'], true);

    expect($report['values_conflicted'])->toBe(0)
        ->and($report['files_written'])->toBe(1)
        ->and(require $path)->toBe(['button' => ['cancel' => ['cancel' => 'Cancel', 'hint' => 'Keep']]]);
});

it('limits export changes and pruning to exact selected keys', function (bool $write) {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['selected' => 'Old', 'selected_more' => 'Keep prefix', 'obsolete' => 'Remove selected', 'other_obsolete' => 'Keep obsolete'];");
    File::put(lang_path('en/other.php'), "<?php return ['obsolete' => 'Keep other file'];");
    $original = File::get(lang_path('en/ui.php'));
    $other = File::get(lang_path('en/other.php'));
    foreach ([
        ['ui.selected', 'active', 'New'],
        ['ui.selected_more', 'active', 'Unselected replacement'],
        ['ui.obsolete', 'obsolete', 'Remove selected'],
        ['ui.other_obsolete', 'obsolete', 'Keep obsolete'],
        ['other.obsolete', 'obsolete', 'Keep other file'],
    ] as [$name, $status, $value]) {
        [$namespace, $langKey] = explode('.', $name, 2);
        TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', $name), 'translation_key' => $name, 'status' => 'open']);
        TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => $namespace, 'lang_key' => $langKey, 'translation_key' => $name, 'value' => $value, 'value_type' => 'string', 'status' => $status, 'source_path' => 'lang/en/'.$namespace.'.php']);
    }

    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], write: $write, keys: ['ui.selected', 'ui.obsolete']);

    expect($report['keys'])->toBe(['ui.selected', 'ui.obsolete'])
        ->and($report['files'])->toBe(1)
        ->and($report['values_exportable'])->toBe(1)
        ->and($report['values_pruned'])->toBe(1)
        ->and($report['files_written'])->toBe($write ? 1 : 0)
        ->and(File::get(lang_path('en/other.php')))->toBe($other);
    if ($write) {
        expect(require lang_path('en/ui.php'))->toBe(['other_obsolete' => 'Keep obsolete', 'selected' => 'New', 'selected_more' => 'Keep prefix']);
        $events = TranslationWorkbenchTimelineEvent::all();
        expect($events)->toHaveCount(2);
        foreach ($events as $event) {
            expect($event->context['translation_key'])->toBeIn(['ui.selected', 'ui.obsolete']);
        }
    } else {
        expect(File::get(lang_path('en/ui.php')))->toBe($original)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
    }
    $empty = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], write: true, keys: ['ui.does-not-exist']);
    expect($empty['files'])->toBe(0)->and($empty['files_written'])->toBe(0);
})->with([false, true]);

it('does not broaden an explicitly blank key filter into a full export', function () {
    expect(fn () => app(TranslationWorkbenchLangFileExporter::class)->export(keys: [' ']))
        ->toThrow(InvalidArgumentException::class);
});

it('forwards repeated exact key filters from the export command', function () {
    $this->artisan('translation-workbench:export-lang-files', ['--key' => ['ui.one', 'ui.two']])
        ->expectsOutputToContain('Keys: ui.one, ui.two')
        ->assertSuccessful();
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-export-lang-files.json')), true);
    expect($report['summary']['keys'])->toBe(['ui.one', 'ui.two'])
        ->and($report['summary']['files_written'])->toBe(0);
    $reader = new ReflectionMethod(TranslationWorkbenchEntries::class, 'langFileExportReport');
    expect($reader->invoke(new TranslationWorkbenchEntries)['keys'])->toBe(['ui.one', 'ui.two']);
});

it('recovers an existing reviewed button key into its scalar leaf with history and scoped export', function () {
    $file = base_path('resources/views/button.blade.php');
    File::put($file, "{{ __('ui.button.cancel') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views/button.blade.php']));
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $key->update(['review_status' => 'reviewed']);
    $parent = TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'button.cancel', 'translation_key' => 'ui.button.cancel', 'value' => 'Cancel', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php']);
    $leaf = TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'button.cancel.cancel', 'translation_key' => 'ui.button.cancel.cancel', 'value' => 'Cancel', 'value_type' => 'string', 'status' => 'obsolete', 'source_path' => 'lang/en/ui.php']);
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['button' => ['cancel' => ['cancel' => 'Cancel']], 'untouched' => 'Keep'];");
    File::put($file, "{{ __('ui.button.cancel.cancel') }}");

    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyReviewWriter::class)->saveDecision(
        $key, $finding, $finding,
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyAttributes::class)->forKey('ui.button.cancel.cancel', $key),
        'translation_key', 'translation_key_updated',
    );
    app(TranslationWorkbenchTranslationWriter::class)->save($key->fresh(), $finding, 'ui.button.cancel.cancel', 'en', [['locale' => 'en', 'value' => 'Cancel']], 'translation-workbench:reviewed-recovery');
    $syncer->sync($scanner->scan(['resources/views/button.blade.php']));
    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], keys: ['ui.button.cancel', 'ui.button.cancel.cancel'], write: true);

    expect($parent->fresh()->status)->toBe('obsolete')
        ->and($leaf->fresh()->status)->toBe('active')
        ->and($key->fresh()->translation_key)->toBe('ui.button.cancel.cancel')
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_lang_values_rekeyed')->count())->toBe(1)
        ->and($report['values_conflicted'])->toBe(0)
        ->and($report['values_pruned'])->toBe(0)
        ->and(require lang_path('en/ui.php'))->toBe(['button' => ['cancel' => ['cancel' => 'Cancel']], 'untouched' => 'Keep']);
});

it('restores a specific modal title without renaming the shared filter key', function () {
    $file = base_path('resources/views/modal.blade.php');
    File::put($file, "{{ __('ui.title.filter') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views/modal.blade.php']));
    $filter = TranslationWorkbenchKey::sole();
    $filter->update(['review_status' => 'reviewed']);
    $title = 'admin.permissions.modals.edit.title';
    File::put($file, "{{ __('admin.permissions.modals.edit.title') }}");
    $syncer->sync($scanner->scan(['resources/views/modal.blade.php']));
    $key = TranslationWorkbenchKey::where('translation_key', $title)->sole();
    $finding = TranslationWorkbenchFinding::where('existing_key', $title)->where('status', 'active')->sole();
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyReviewWriter::class)->saveDecision($key, $finding, $finding, [], 'translation_key', 'translation_key_updated');
    TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'admin', 'lang_key' => 'permissions.modals.edit.title', 'translation_key' => $title, 'value' => 'Edit Permission', 'value_type' => 'string', 'status' => 'obsolete', 'source_path' => 'lang/en/admin.php']);
    app(TranslationWorkbenchTranslationWriter::class)->save($key->fresh(), $finding, $title, 'en', [['locale' => 'en', 'value' => 'Edit Permission']], 'translation-workbench:reviewed-recovery');
    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], keys: [$title], write: true);

    expect($filter->fresh()->translation_key)->toBe('ui.title.filter')
        ->and($key->fresh()->review_status)->toBe('reviewed')
        ->and($report['files_written'])->toBe(1)
        ->and(Arr::get(require lang_path('en/admin.php'), 'permissions.modals.edit.title'))->toBe('Edit Permission');
});

it('requires an explicit review to restore a historically renamed modal key after a focused rescan', function () {
    $title = 'admin.permissions.modals.edit.title';
    File::put(base_path('resources/views/modal.blade.php'), "{{ __('admin.permissions.modals.edit.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $items = $scanner->scan(['resources/views/modal.blade.php']);
    $syncer->sync($items);
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $key->update(['translation_key' => 'ui.title.title', 'review_status' => 'reviewed', 'status' => 'obsolete']);
    $syncer->sync($items);
    expect($key->fresh()->translation_key)->toBe('ui.title.title');

    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyReviewWriter::class)->saveDecision(
        $key->fresh(), $finding, $finding,
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyAttributes::class)->forKey($title, $key),
        'translation_key', 'translation_key_updated',
    );
    $syncer->sync($items);
    expect($key->fresh()->translation_key)->toBe($title)
        ->and($key->fresh()->status)->toBe('open')
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe(1);
});

it('blocks a legacy export from removing an exported workbench value before any file is written', function (bool $dryRun) {
    File::put(base_path('resources/views/welcome.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    $editor->editFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    app(TranslationWorkbenchLangFileExporter::class)->export(['de'], write: true);
    $target = TranslationWorkbenchLangValue::where('locale', 'de')->sole();
    $file = lang_path('de/'.$target->namespace.'.php');
    $before = File::get($file);

    createLegacyExportTables();
    DB::table('translation_keys')->insert(['id' => 1, 'key' => $target->namespace.'.legacy', 'group' => $target->namespace, 'status' => 'ok']);
    foreach (['en', 'de'] as $locale) {
        DB::table('translation_values')->insert(['translation_key_id' => 1, 'locale' => $locale, 'value' => 'Legacy text', 'status' => 'ok']);
    }

    $this->artisan('translations:export-lang-files', ['--locales' => 'en,de', '--dry-run' => $dryRun])
        ->expectsOutputToContain('Export blocked')
        ->expectsOutputToContain($target->translation_key)
        ->assertFailed();

    expect(File::get($file))->toBe($before)
        ->and(File::exists(lang_path('en/'.$target->namespace.'.php')))->toBeFalse()
        ->and($target->fresh()->value)->toBe('Willkommen an Bord');
})->with([false, true]);

it('distinguishes conflicting workbench writes from unchanged and legacy-owned values', function (string $status, string $proposed, bool $blocked) {
    createLegacyExportTables();
    File::makeDirectory(lang_path('de'));
    $file = lang_path('de/ui.php');
    File::put($file, "<?php return ['owned' => 'Workbench text'];");
    $before = File::get($file);
    DB::table('translation_workbench_lang_values')->insert([
        'locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'owned',
        'translation_key' => 'ui.owned', 'value' => 'Workbench text',
        'source_path' => 'lang/de/ui.php', 'status' => $status,
    ]);
    DB::table('translation_keys')->insert([
        ['id' => 1, 'key' => 'ui.owned', 'group' => 'ui', 'status' => 'ok'],
        ['id' => 2, 'key' => 'ui.legacy', 'group' => 'ui', 'status' => 'ok'],
    ]);
    DB::table('translation_values')->insert([
        ['translation_key_id' => 1, 'locale' => 'de', 'value' => $proposed, 'status' => 'ok'],
        ['translation_key_id' => 2, 'locale' => 'de', 'value' => 'Legacy addition', 'status' => 'ok'],
    ]);

    $command = $this->artisan('translations:export-lang-files', ['--locales' => 'de']);
    if ($blocked) {
        $command->expectsTable(['Locale', 'File', 'Translation key', 'Conflict'], [['de', 'lang/de/ui.php', 'ui.owned', 'would overwrite']])->assertFailed()->run();
        expect(File::get($file))->toBe($before);
    } else {
        $command->assertSuccessful()->run();
        expect(require $file)->toBe(['legacy' => 'Legacy addition', 'owned' => $proposed]);
    }
})->with([
    'active Workbench value is protected' => ['active', 'Different legacy text', true],
    'identical Workbench value allows legacy additions' => ['active', 'Workbench text', false],
    'obsolete Workbench row does not own the value' => ['obsolete', 'Legacy text', false],
]);

it('protects a workbench container from scalar replacement without blocking added children', function (bool $replaceContainer) {
    createLegacyExportTables();
    File::makeDirectory(lang_path('de'));
    $file = lang_path('de/ui.php');
    File::put($file, "<?php return ['button' => ['save' => 'Save']];");
    $before = File::get($file);
    DB::table('translation_workbench_lang_values')->insert([
        'locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'button',
        'translation_key' => 'ui.button', 'value' => '{"save":"Save"}',
        'value_type' => 'array', 'source_path' => 'lang/de/ui.php', 'status' => 'active',
    ]);
    foreach (($replaceContainer ? ['ui.button' => 'Other'] : ['ui.button.save' => 'Save', 'ui.button.new' => 'New']) as $key => $value) {
        $id = DB::table('translation_keys')->insertGetId(['key' => $key, 'group' => 'ui', 'status' => 'ok']);
        DB::table('translation_values')->insert(['translation_key_id' => $id, 'locale' => 'de', 'value' => $value, 'status' => 'ok']);
    }
    $command = $this->artisan('translations:export-lang-files', ['--locales' => 'de']);
    if ($replaceContainer) {
        $command->expectsOutputToContain('ui.button.save')->assertFailed()->run();
        expect(File::get($file))->toBe($before);
    } else {
        $command->assertSuccessful()->run();
        expect(require $file)->toBe(['button' => ['new' => 'New', 'save' => 'Save']]);
    }
})->with([false, true]);

function workbenchValueEditor(): TranslationWorkbenchEntries
{
    File::put(base_path('resources/views/value.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    $editor->editFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen';

    return $editor;
}

it('saves trimmed source and target values with one history event and leaves files untouched', function () {
    $editor = workbenchValueEditor();
    $editor->targetTranslationValue = '  Willkommen  ';
    $before = File::get(base_path('resources/views/value.blade.php'));
    $editor->saveTranslationValue();

    expect(TranslationWorkbenchLangValue::where('locale', 'de')->sole()->value)->toBe('Willkommen')
        ->and(TranslationWorkbenchLangValue::where('locale', 'en')->sole()->value)->toBe('Welcome aboard')
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe($before);
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->sole();
    expect($event->new_values['values'])->toBe(['en' => 'Welcome aboard', 'de' => 'Willkommen']);
    $editor->saveTranslationValue();
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->count())->toBe(1);
});

it('preserves the existing blank-value semantics without creating absent empty translations', function () {
    $editor = workbenchValueEditor();
    $editor->targetTranslationValue = '  ';
    $editor->saveTranslationValue();
    expect(TranslationWorkbenchLangValue::where('locale', 'de')->exists())->toBeFalse();
    $editor->targetTranslationValue = 'Willkommen';
    $editor->saveTranslationValue();
    $editor->targetTranslationValue = null;
    $editor->saveTranslationValue();
    $value = TranslationWorkbenchLangValue::where('locale', 'de')->sole();
    expect($value->value)->toBeNull()->and($value->status)->toBe('missing');
});

it('edits only selected available sub-locales and preserves untouched locale values', function () {
    $editor = workbenchValueEditor();
    foreach (['de-AT', 'de-CH'] as $locale) {
        DB::table('locales')->insert(['code' => $locale, 'normalized_code' => $locale, 'is_active' => true]);
    }
    $editor->selectedTargetSubLocales = ['de-AT', 'de-CH'];
    $editor->targetSubTranslationValues = ['de-AT' => 'Servus', 'de-CH' => 'Grüezi'];
    $editor->saveTranslationValue();
    $editor->selectedTargetSubLocales = ['de_AT'];
    $editor->targetSubTranslationValues = ['de-AT' => 'Grüß Gott', 'de-CH' => 'Ignored', 'fr-FR' => 'Ignored'];
    $editor->saveTranslationValue();
    expect(TranslationWorkbenchLangValue::where('locale', 'de-AT')->sole()->value)->toBe('Grüß Gott')
        ->and(TranslationWorkbenchLangValue::where('locale', 'de-CH')->sole()->value)->toBe('Grüezi')
        ->and(TranslationWorkbenchLangValue::where('locale', 'fr-FR')->exists())->toBeFalse();
});

it('rolls back all edited locales when writing translation history fails', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $editor->sourceTranslationValue = 'Changed source';
    $editor->targetTranslationValue = 'Changed target';
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordKeyFindingEvent')->once()->andThrow(new RuntimeException('History unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);

    expect(fn () => $editor->saveTranslationValue())->toThrow(RuntimeException::class, 'History unavailable');
    expect(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->count())->toBe(1);
});

it('uses the same scalar persistence and history semantics through the service and editor', function () {
    $editor = workbenchValueEditor();
    $writer = app(TranslationWorkbenchTranslationWriter::class);
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $changes = $writer->save($key, $finding, $key->translation_key, 'en', [
        ['locale' => 'en', 'value' => 'Welcome aboard'],
        ['locale' => 'de', 'value' => '  Willkommen  '],
    ]);
    expect($changes)->toHaveCount(2);
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $editor->saveTranslationValue();
    expect(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->count())->toBe(1);
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->sole();
    expect($event->key_id)->toBe($key->id)->and($event->finding_id)->toBe($finding->id)
        ->and($event->context['source'])->toBe('translation-workbench:modal-edit');
});

function workbenchDynamicEditor(): TranslationWorkbenchEntries
{
    $editor = workbenchValueEditor();
    $key = TranslationWorkbenchKey::sole();
    foreach (['draft' => 'Draft', 'published' => 'Published'] as $valueKey => $value) {
        DB::table('translation_workbench_dynamic_key_values')->insert([
            'key_id' => $key->id, 'value_key' => $valueKey, 'locale' => 'en',
            'value' => $value, 'native_label' => $value, 'status' => 'active', 'source' => 'runtime_source',
        ]);
    }
    $editor->dynamicMultiSourceValues = ['v_'.sha1('draft') => 'Draft', 'v_'.sha1('published') => 'Published'];
    $editor->dynamicMultiTargetValues = ['v_'.sha1('draft') => ' Entwurf ', 'v_'.sha1('published') => 'Veröffentlicht'];

    return $editor;
}

it('saves dynamic options separately from scalar values and does not duplicate unchanged history', function () {
    $editor = workbenchDynamicEditor();
    $editor->dynamicMultiTargetValues['unrecognized'] = 'Rejected';
    expect(fn () => $editor->saveDynamicMultiTranslationValues())->toThrow(ValidationException::class);
    unset($editor->dynamicMultiTargetValues['unrecognized']);
    $editor->saveDynamicMultiTranslationValues();
    expect(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->pluck('value', 'value_key')->all())
        ->toBe(['draft' => 'Entwurf', 'published' => 'Veröffentlicht'])
        ->and(TranslationWorkbenchLangValue::count())->toBe(0)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_multi_values_saved')->sole();
    expect($event->new_values['values'])->toBe(['draft:de' => 'Entwurf', 'published:de' => 'Veröffentlicht']);
    $editor->saveDynamicMultiTranslationValues();
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_multi_values_saved')->count())->toBe(1);
});

it('keeps blank dynamic targets absent until created and marks cleared values missing', function () {
    $editor = workbenchDynamicEditor();
    $field = 'v_'.sha1('draft');
    $editor->dynamicMultiTargetValues[$field] = ' ';
    $editor->saveDynamicMultiTranslationValues();
    expect(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->where('value_key', 'draft')->exists())->toBeFalse();
    $editor->dynamicMultiTargetValues[$field] = 'Entwurf';
    $editor->saveDynamicMultiTranslationValues();
    $editor->dynamicMultiTargetValues[$field] = null;
    $editor->saveDynamicMultiTranslationValues();
    $row = DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->where('value_key', 'draft')->sole();
    expect($row->value)->toBeNull()->and($row->status)->toBe('missing');
});

it('corrects one dynamic source option without changing targets or other options', function () {
    $editor = workbenchDynamicEditor();
    $editor->saveDynamicMultiTranslationValues();
    $field = 'v_'.sha1('draft');
    $editor->dynamicMultiSourceValues[$field] = ' Draft corrected ';
    $editor->saveDynamicMultiSourceValue($field);
    $row = DB::table('translation_workbench_dynamic_key_values')->where('locale', 'en')->where('value_key', 'draft')->sole();
    expect($row->value)->toBe('Draft corrected')->and($row->native_label)->toBe('Draft corrected')
        ->and($row->source)->toBe('translation_workbench_source_correction')
        ->and(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->where('value_key', 'draft')->value('value'))->toBe('Entwurf')
        ->and(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'en')->where('value_key', 'published')->value('value'))->toBe('Published');
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_multi_source_value_saved')->sole()->new_values['values'])
        ->toBe(['draft:en' => 'Draft corrected']);
    $editor->saveDynamicMultiSourceValue($field);
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_multi_source_value_saved')->count())->toBe(1);
});

it('rolls back dynamic option inserts and edits when their history fails', function (bool $sourceOnly) {
    $editor = workbenchDynamicEditor();
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
    $field = 'v_'.sha1('draft');
    $editor->dynamicMultiSourceValues[$field] = 'Changed draft';
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordKeyFindingEvent')->once()->andThrow(new RuntimeException('Dynamic history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $sourceOnly ? $editor->saveDynamicMultiSourceValue($field) : $editor->saveDynamicMultiTranslationValues())
        ->toThrow(RuntimeException::class, 'Dynamic history unavailable');
    expect(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all())
        ->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::whereIn('event_type', ['dynamic_multi_values_saved', 'dynamic_multi_source_value_saved'])->count())->toBe(0);
})->with([false, true]);

it('retains dynamic history context and leaves other locales untouched through the shared writer', function () {
    $editor = workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => $key->id, 'value_key' => 'draft', 'locale' => 'fr',
        'value' => 'Brouillon', 'native_label' => 'Draft', 'status' => 'active', 'source' => 'test',
    ]);
    $writer = app(TranslationWorkbenchTranslationWriter::class);
    $writer->saveDynamicValues($key, TranslationWorkbenchFinding::sole(), [
        ['value_key' => 'draft', 'locale' => 'de', 'value' => 'Entwurf', 'native_label' => 'Draft', 'source' => 'translation_workbench_modal'],
        ['value_key' => 'published', 'locale' => 'de', 'value' => 'Veröffentlicht', 'native_label' => 'Published', 'source' => 'translation_workbench_modal'],
    ]);
    $editor->saveDynamicMultiTranslationValues();
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_multi_values_saved')->sole();
    expect($event->context['locales'])->toBe(['de'])
        ->and($event->context['value_keys'])->toBe(['draft', 'published'])
        ->and($event->context['source'])->toBe('translation-workbench:modal-edit-dynamic-multi')
        ->and(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'fr')->value('value'))->toBe('Brouillon');
});

function workbenchBulkEditor(bool $sameLiteral = true): TranslationWorkbenchEntries
{
    DB::connection()->getPdo()->sqliteCreateFunction('REGEXP_REPLACE',
        static fn ($value, $pattern, $replacement, $flags) => preg_replace('/'.$pattern.'/u', $replacement, $value), 4);
    File::put(base_path('resources/views/first.blade.php'), "{{ __('Welcome aboard') }}");
    File::put(base_path('resources/views/second.blade.php'), $sameLiteral ? "{{ __('Welcome aboard') }}" : "{{ __('Different text') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->bulkEqualizeSelectedFindingIds = TranslationWorkbenchFinding::orderBy('id')->pluck('id')->all();
    $editor->bulkEqualizeTranslationKey = 'ui.shared.welcome';

    return $editor;
}

it('equalizes selected keys and records linked reviews under one operation without publishing', function () {
    $editor = workbenchBulkEditor();
    $before = File::get(base_path('resources/views/first.blade.php'));
    $editor->confirmBulkEqualizeTranslationKey();
    expect(TranslationWorkbenchKey::where('translation_key', 'ui.shared.welcome')->count())->toBe(2)
        ->and(TranslationWorkbenchKey::where('review_status', 'reviewed')->count())->toBe(2)
        ->and(TranslationWorkbenchKey::where('is_ui_key', true)->count())->toBe(2)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->count())->toBe(2)
        ->and(TranslationWorkbenchLangValue::count())->toBe(0)
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and(File::get(base_path('resources/views/first.blade.php')))->toBe($before)
        ->and($editor->bulkEqualizeSelectedFindingIds)->toBe([]);
    $events = TranslationWorkbenchTimelineEvent::where('event_type', 'translation_key_bulk_equalized')->get();
    expect($events)->toHaveCount(2)
        ->and($events->pluck('context.operation_id')->unique())->toHaveCount(1)
        ->and($events->pluck('context.target_translation_key')->unique()->all())->toBe(['ui.shared.welcome']);
});

it('refuses mixed literals and invalid target keys without creating bulk reviews', function () {
    $editor = workbenchBulkEditor(false);
    $editor->confirmBulkEqualizeTranslationKey();
    expect(TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->count())->toBe(0);
    TranslationWorkbenchFinding::query()->update(['literal_text' => 'Welcome aboard']);
    $editor->bulkEqualizeTranslationKey = 'Invalid Key';
    expect(fn () => $editor->confirmBulkEqualizeTranslationKey())->toThrow(ValidationException::class);
    expect(TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->count())->toBe(0)
        ->and(TranslationWorkbenchKey::whereNotNull('translation_key')->count())->toBe(0);
});

it('rolls back bulk key changes and reviews if recording history fails', function () {
    $editor = workbenchBulkEditor();
    $before = TranslationWorkbenchKey::orderBy('id')->get()->toArray();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Bulk history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $editor->confirmBulkEqualizeTranslationKey())->toThrow(RuntimeException::class, 'Bulk history unavailable');
    expect(TranslationWorkbenchKey::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->count())->toBe(0);
});

it('links a finding without an active key to an established shared key workflow', function (bool $removeKey) {
    $editor = workbenchBulkEditor();
    $editor->confirmBulkEqualizeTranslationKey();
    File::put(base_path('resources/views/third.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::orderByDesc('id')->first();
    $links = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->get();
    foreach ($links as $link) {
        if ($removeKey) {
            TranslationWorkbenchKey::findOrFail($link->key_id)->delete();
        } else {
            $link->update(['status' => 'obsolete']);
        }
    }
    $editor->bulkEqualizeSelectedFindingIds = [$finding->id];
    $editor->bulkEqualizeTranslationKey = 'ui.shared.welcome';
    $editor->confirmBulkEqualizeTranslationKey();
    $link = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->sole();
    expect(TranslationWorkbenchKey::findOrFail($link->key_id)->translation_key)->toBe('ui.shared.welcome')
        ->and(TranslationWorkbenchReview::where('finding_id', $finding->id)->where('decision', 'translation_key_bulk_equalized')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('finding_id', $finding->id)->whereIn('event_type', ['key_finding_relation_created', 'key_finding_relation_updated'])->count())->toBeGreaterThan(0);
})->with([false, true]);

it('uses fresh selection rules in the bulk service instead of trusting an earlier preview', function () {
    $editor = workbenchBulkEditor();
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    $ids = $editor->bulkEqualizeSelectedFindingIds;
    expect($writer->context($ids)['can_confirm'])->toBeTrue();
    TranslationWorkbenchFinding::findOrFail($ids[1])->update(['literal_text' => 'Changed after preview']);
    expect($writer->equalize($ids, 'ui.shared.welcome'))->toBe(0)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->count())->toBe(0);
    expect(fn () => $writer->equalize($ids, 'Invalid Key'))->toThrow(ValidationException::class);
});

it('preserves non-ui classification and existing translation values during bulk key equalization', function () {
    $editor = workbenchBulkEditor();
    TranslationWorkbenchKey::query()->update(['key_type' => 'dynamic', 'is_dynamic_key' => true, 'is_dynamic_multi' => true]);
    TranslationWorkbenchLangValue::create([
        'locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'old', 'translation_key' => 'ui.old',
        'value' => 'Existing translation', 'status' => 'active', 'source_path' => 'lang/de/ui.php',
    ]);
    $before = TranslationWorkbenchLangValue::sole()->toArray();
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    expect($writer->equalize($editor->bulkEqualizeSelectedFindingIds, 'messages.shared.welcome'))->toBe(2)
        ->and(TranslationWorkbenchKey::where('key_type', 'dynamic')->where('is_dynamic_multi', true)->count())->toBe(2)
        ->and(TranslationWorkbenchLangValue::sole()->toArray())->toBe($before);
});

it('renames a reviewed key together with its existing language values and records both changes', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $oldKey = TranslationWorkbenchKey::sole()->translation_key;
    $editor->translationKeyFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->translationKeyValue = 'messages.renamed.welcome';
    $editor->saveTranslationKeyModal();
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('messages.renamed.welcome')
        ->and(TranslationWorkbenchLangValue::where('translation_key', $oldKey)->count())->toBe(0)
        ->and(TranslationWorkbenchLangValue::where('translation_key', 'messages.renamed.welcome')->count())->toBe(2)
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->sole()->value)->toBe('Willkommen')
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_lang_values_rekeyed')->sole()->context['migrated_count'])->toBe(2)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('preserves the current rename policy when a target locale value already exists', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $old = TranslationWorkbenchLangValue::where('locale', 'de')->sole();
    TranslationWorkbenchLangValue::create([
        'locale' => 'de', 'namespace' => 'messages', 'lang_key' => 'existing',
        'translation_key' => 'messages.existing', 'value' => 'Existing target',
        'source_path' => 'lang/de/messages.php', 'status' => 'active',
    ]);
    $editor->translationKeyFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->translationKeyValue = 'messages.existing';
    $editor->saveTranslationKeyModal();
    expect($old->fresh()->status)->toBe('obsolete')->and($old->fresh()->value)->toBe('Willkommen')
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->where('translation_key', 'messages.existing')->sole()->value)->toBe('Existing target')
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_lang_values_rekeyed')->sole()->context['obsoleted_count'])->toBe(1);
});

it('preserves classification transitions and records the corresponding review types', function () {
    $editor = workbenchValueEditor();
    $id = TranslationWorkbenchFinding::sole()->id;
    $editor->setDynamicKeyReview($id, true);
    $key = TranslationWorkbenchKey::sole();
    expect($key->is_dynamic_key)->toBeTrue()->and($key->is_ui_key)->toBeFalse()
        ->and($key->translation_key)->toStartWith('dynamic.');
    $editor->setUiKeyReview($id, true);
    $key->refresh();
    expect($key->is_ui_key)->toBeTrue()->and($key->is_dynamic_key)->toBeFalse()
        ->and($key->translation_key)->toStartWith('ui.');
    $editor->setUiKeyRejected($id, true);
    expect($key->fresh()->is_ui_candidate_rejected)->toBeTrue()
        ->and(TranslationWorkbenchReview::where('decision', 'ui_candidate_rejected')->sole()->review_type)->toBe('classification');
    $editor->setDynamicCandidateClassification($id, true);
    $editor->setDynamicMultiKeyReview($id, true);
    expect($key->fresh()->reviewed_is_dynamic_multi)->toBeTrue()
        ->and(TranslationWorkbenchReview::where('decision', 'dynamic_multi_confirmed')->sole()->review_type)->toBe('candidate_classification');
});

it('does not repeat suggested-key acceptance or accept an invalid manual key', function () {
    $editor = workbenchValueEditor();
    $id = TranslationWorkbenchFinding::sole()->id;
    $editor->acceptSuggestedTranslationKey($id);
    expect(TranslationWorkbenchReview::where('decision', 'suggested_key_accepted')->count())->toBe(1);
    $editor->translationKeyFindingId = $id;
    $editor->translationKeyValue = 'Invalid key';
    expect(fn () => $editor->saveTranslationKeyModal())->toThrow(ValidationException::class);
    expect(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe(0);
});

it('rolls back a key rename and its locale moves if the rekey history fails', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $beforeKey = TranslationWorkbenchKey::sole()->toArray();
    $beforeValues = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class)->makePartial();
    $recorder->shouldReceive('recordKeyFindingEvent')->once()->andThrow(new RuntimeException('Rekey history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    $editor->translationKeyFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->translationKeyValue = 'messages.renamed';
    expect(fn () => $editor->saveTranslationKeyModal())->toThrow(RuntimeException::class, 'Rekey history unavailable');
    expect(TranslationWorkbenchKey::sole()->toArray())->toBe($beforeKey)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($beforeValues)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_key_updated')->count())->toBe(0);
});

it('refuses manual scalar keys that point at a language container', function () {
    $editor = workbenchValueEditor();
    $keyBefore = TranslationWorkbenchKey::sole()->toArray();
    TranslationWorkbenchLangValue::create([
        'locale' => 'de', 'namespace' => 'messages', 'lang_key' => 'container.child',
        'translation_key' => 'messages.container.child', 'value' => 'Child',
        'status' => 'active', 'source_path' => 'lang/de/messages.php',
    ]);
    $editor->translationKeyFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->translationKeyValue = 'messages.container';
    $editor->saveTranslationKeyModal();
    expect(TranslationWorkbenchKey::sole()->toArray())->toBe($keyBefore)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe(0);
});

it('preserves dynamic mode changes and rejection removal through the shared review writer', function () {
    $editor = workbenchValueEditor();
    $id = TranslationWorkbenchFinding::sole()->id;
    $editor->setDynamicReviewMode($id, 'multi');
    expect(TranslationWorkbenchKey::sole()->reviewed_is_dynamic_multi)->toBeTrue()
        ->and(TranslationWorkbenchReview::where('decision', 'dynamic_multi_confirmed')->sole()->review_type)->toBe('dynamic_classification');
    $editor->setDynamicReviewMode($id, 'single');
    expect(TranslationWorkbenchKey::sole()->is_dynamic_multi)->toBeFalse();
    $editor->setDynamicKeyRejected($id, true);
    expect(TranslationWorkbenchKey::sole()->is_dynamic_candidate_rejected)->toBeTrue();
    $editor->setDynamicKeyRejected($id, false);
    expect(TranslationWorkbenchKey::sole()->is_dynamic_candidate_rejected)->toBeFalse()
        ->and(TranslationWorkbenchReview::where('decision', 'dynamic_candidate_rejection_removed')->count())->toBe(1);
});

it('rolls back suggested key acceptance when its review event fails', function () {
    File::put(base_path('resources/views/value.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $before = TranslationWorkbenchKey::sole()->toArray();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Acceptance history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => (new TranslationWorkbenchEntries)->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id))
        ->toThrow(RuntimeException::class, 'Acceptance history unavailable');
    expect(TranslationWorkbenchKey::sole()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::count())->toBe(0);
});

it('stores conflict decisions without changing translations or files and ignores repeated decisions', function (string $decision) {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $source = File::get(base_path('resources/views/value.blade.php'));
    $editor->codeUpdateConflictFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->codeUpdateConflictKeyId = TranslationWorkbenchKey::sole()->id;
    $editor->codeUpdateConflictDecision = $decision;
    $editor->codeUpdateConflictNote = '  Reviewed conflict  ';
    $editor->saveCodeUpdateConflictReview();
    $review = TranslationWorkbenchReview::where('review_type', 'code_update_conflict')->sole();
    expect($review->decision)->toBe($decision === 'unsupported' ? 'ignore_for_now' : $decision)
        ->and($review->meta['note'])->toBe('Reviewed conflict');
    app(TranslationWorkbenchConflictReviewWriter::class)->save(
        TranslationWorkbenchFinding::sole(), TranslationWorkbenchKey::sole(), $decision, 'Reviewed conflict',
    );
    expect(TranslationWorkbenchReview::where('review_type', 'code_update_conflict')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_conflict_reviewed')->count())->toBe(1)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before)
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe($source);
})->with(['duplicate_confirmed_same_key', 'existing_key_should_be_replaced', 'duplicate_dynamic_manual_workflow', 'ignore_for_now', 'unsupported']);

function workbenchCleanupEditor(): TranslationWorkbenchEntries
{
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $key = TranslationWorkbenchKey::sole()->translation_key;
    $inventory = TranslationWorkbenchKeyInventory::create([
        'translation_key' => $key, 'normalized_translation_key' => $key,
        'is_orphaned_lang_value' => true, 'candidate_for_lang_delete' => true,
        'source_value_active_count' => 1, 'target_value_active_count' => 1,
    ]);
    $editor->langCleanupInventoryId = $inventory->id;

    return $editor;
}

it('preserves cleanup decisions and updates only the selected key without publishing files', function (string $decision, string $event) {
    $editor = workbenchCleanupEditor();
    $inventory = TranslationWorkbenchKeyInventory::findOrFail($editor->langCleanupInventoryId);
    $other = TranslationWorkbenchLangValue::create([
        'locale' => 'de', 'namespace' => 'messages', 'lang_key' => 'other',
        'translation_key' => 'messages.other', 'value' => 'Other', 'status' => 'active', 'source_path' => 'lang/de/messages.php',
    ]);
    File::ensureDirectoryExists(lang_path('de'));
    File::put(lang_path('de/messages.php'), "<?php return ['untouched' => 'Text'];");
    $editor->saveLangCleanupReview($decision);
    $review = TranslationWorkbenchReview::where('review_type', 'lang_cleanup')->sole();
    expect($review->decision)->toBe($decision)
        ->and($review->new_values['obsolete_lang_values_count'])->toBe($decision === 'obsolete' ? 2 : 0)
        ->and(TranslationWorkbenchLangValue::where('translation_key', $inventory->translation_key)->pluck('status')->unique()->all())->toBe([$decision === 'obsolete' ? 'obsolete' : 'active'])
        ->and($other->fresh()->status)->toBe('active')
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', $event)->count())->toBe(1)
        ->and(File::get(lang_path('de/messages.php')))->toBe("<?php return ['untouched' => 'Text'];");
    if ($decision === 'obsolete') {
        expect($inventory->fresh()->source_value_active_count)->toBe(0)
            ->and($inventory->fresh()->target_value_obsolete_count)->toBe(1);
    }
})->with([
    ['keep', 'lang_value_kept'], ['needs_review', 'lang_cleanup_needs_review'], ['obsolete', 'lang_value_marked_obsolete'],
]);

it('rolls back cleanup statuses counts and review when history fails', function () {
    $editor = workbenchCleanupEditor();
    $inventory = TranslationWorkbenchKeyInventory::findOrFail($editor->langCleanupInventoryId);
    $before = $inventory->toArray();
    $values = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Cleanup history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $editor->saveLangCleanupReview('obsolete'))->toThrow(RuntimeException::class, 'Cleanup history unavailable');
    expect($inventory->fresh()->toArray())->toBe($before)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($values)
        ->and(TranslationWorkbenchReview::where('review_type', 'lang_cleanup')->count())->toBe(0);
});

it('rolls back conflict review when history fails', function () {
    $editor = workbenchValueEditor();
    $editor->codeUpdateConflictFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->codeUpdateConflictKeyId = TranslationWorkbenchKey::sole()->id;
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Conflict history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $editor->saveCodeUpdateConflictReview())->toThrow(RuntimeException::class, 'Conflict history unavailable');
    expect(TranslationWorkbenchReview::where('review_type', 'code_update_conflict')->count())->toBe(0);
});

it('does not reactivate obsolete values when keeping a cleanup candidate and rejects unsupported decisions', function () {
    $editor = workbenchCleanupEditor();
    $inventory = TranslationWorkbenchKeyInventory::findOrFail($editor->langCleanupInventoryId);
    $writer = app(TranslationWorkbenchCleanupReviewWriter::class);
    $writer->save($inventory, 'en', 'obsolete');
    $writer->save($inventory->fresh(), 'en', 'keep');
    expect(TranslationWorkbenchLangValue::where('status', 'active')->count())->toBe(0);
    $count = TranslationWorkbenchReview::count();
    expect(fn () => $writer->save($inventory, 'en', 'delete'))->toThrow(InvalidArgumentException::class);
    expect(TranslationWorkbenchReview::count())->toBe($count);
});

function workbenchProvenanceReport(TranslationWorkbenchEntries $editor, bool $linked = true): array
{
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $row = [
        'source_signature' => $linked ? $finding->source_signature : 'unlinked-source',
        'finding_id' => $linked ? $finding->id : null,
        'key_id' => $linked ? $key->id : null,
        'translation_key' => $linked ? $key->translation_key : 'messages.unlinked',
        'source_path' => 'resources/views/value.blade.php', 'source_line' => 1,
        'raw_expression' => "__('messages.welcome')", 'suggested_key' => 'messages.welcome',
        'literal_text_suggested' => 'Welcome aboard', 'source_lang_value' => 'Welcome aboard',
        'state' => 'suspicious', 'source_lang_value_exists' => true,
        'active_usage_count' => 2, 'reviewed_usage_count' => 1,
    ];
    File::ensureDirectoryExists(storage_path('translation-workbench'));
    File::put(storage_path('translation-workbench/translation-workbench-detect-suspicious-keyed-additions.json'), json_encode(['results' => [$row]]));
    $editor->openSuspiciousKeyReviewModal($row['source_signature']);

    return $row;
}

it('records provenance decisions with source context without changing keys values or files', function (string $decision) {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $row = workbenchProvenanceReport($editor);
    $beforeKey = TranslationWorkbenchKey::sole()->toArray();
    $beforeValues = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $source = File::get(base_path('resources/views/value.blade.php'));
    // Report regeneration is orchestration; avoid running the detector in this write-contract test.
    Artisan::shouldReceive('call')->once()
        ->with('translation-workbench:detect-suspicious-keyed-additions', ['--source-locale' => 'en', '--skip-raw-data' => true])->andReturn(0);
    $editor->reviewSuspiciousKeyProvenance($decision);
    $review = TranslationWorkbenchReview::where('review_type', 'suspicious_key_provenance')->sole();
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'suspicious_key_reviewed')->sole();
    expect($review->decision)->toBe($decision)
        ->and($review->finding_id)->toBe(TranslationWorkbenchFinding::sole()->id)
        ->and($review->meta['source_signature'])->toBe($row['source_signature'])
        ->and($review->meta['raw_expression'])->toBe($row['raw_expression'])
        ->and($review->old_values)->toBe([
            'state' => 'suspicious', 'translation_key' => $row['translation_key'],
            'source_lang_value_exists' => true, 'active_usage_count' => 2, 'reviewed_usage_count' => 1,
        ])
        ->and($event->new_values)->toBe($review->new_values)
        ->and(TranslationWorkbenchKey::sole()->toArray())->toBe($beforeKey)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($beforeValues)
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe($source)
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and($editor->suspiciousKeyReviewModalOpen)->toBeFalse();
})->with(['mark_as_valid_existing_key', 'needs_literal_restore', 'needs_key_review', 'ignore_for_now']);

it('retains provenance reviews even without a linked finding or key', function () {
    $editor = workbenchValueEditor();
    $row = workbenchProvenanceReport($editor, false);
    Artisan::shouldReceive('call')->once()->andReturn(0);
    $editor->reviewSuspiciousKeyProvenance('needs_key_review');
    $review = TranslationWorkbenchReview::where('review_type', 'suspicious_key_provenance')->sole();
    expect($review->key_id)->toBeNull()->and($review->finding_id)->toBeNull()
        ->and($review->meta['translation_key'])->toBe($row['translation_key'])
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'suspicious_key_reviewed')->count())->toBe(1);
});

it('ignores unsupported provenance decisions and missing report context', function () {
    $editor = workbenchValueEditor();
    workbenchProvenanceReport($editor);
    $editor->reviewSuspiciousKeyProvenance('unsupported');
    $editor->suspiciousKeyReviewSourceSignature = 'missing';
    $editor->reviewSuspiciousKeyProvenance('needs_key_review');
    expect(TranslationWorkbenchReview::where('review_type', 'suspicious_key_provenance')->count())->toBe(0);
});

it('rolls back provenance reviews when recording history fails', function () {
    $editor = workbenchValueEditor();
    workbenchProvenanceReport($editor);
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Provenance history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $editor->reviewSuspiciousKeyProvenance('needs_literal_restore'))->toThrow(RuntimeException::class, 'Provenance history unavailable');
    expect(TranslationWorkbenchReview::where('review_type', 'suspicious_key_provenance')->count())->toBe(0)
        ->and($editor->suspiciousKeyReviewModalOpen)->toBeTrue();
});

it('marks only the source locale obsolete and records one review on repeated confirmation', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $key = TranslationWorkbenchKey::sole()->translation_key;
    $source = TranslationWorkbenchLangValue::where('locale', 'en')->sole();
    $targetBefore = TranslationWorkbenchLangValue::where('locale', 'de')->sole()->toArray();
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/messages.php'), "<?php return ['keep' => 'Keep'];");
    $editor->openObsoleteSourceValueReview($key);
    $editor->confirmObsoleteSourceValue();
    $review = TranslationWorkbenchReview::where('review_type', 'lang_value_obsolete')->sole();
    expect($source->fresh()->status)->toBe('obsolete')
        ->and($source->fresh()->value)->toBe($source->value)
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->sole()->toArray())->toBe($targetBefore)
        ->and($review->old_values['status'])->toBe('active')
        ->and($review->new_values['status'])->toBe('obsolete')
        ->and($review->meta['locale'])->toBe('en')
        ->and(File::get(lang_path('en/messages.php')))->toBe("<?php return ['keep' => 'Keep'];")
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe("{{ __('Welcome aboard') }}")
        ->and($editor->obsoleteSourceValueReviewModalOpen)->toBeFalse();
    $editor->openObsoleteSourceValueReview($key);
    $editor->confirmObsoleteSourceValue();
    expect(TranslationWorkbenchReview::where('review_type', 'lang_value_obsolete')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'source_lang_value_marked_obsolete')->count())->toBe(1);
});

it('leaves missing and empty obsolete source selections unchanged', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    foreach (['messages.missing', ''] as $key) {
        $editor->obsoleteSourceValueTranslationKey = $key;
        $editor->obsoleteSourceValueReviewModalOpen = true;
        $editor->confirmObsoleteSourceValue();
        expect($editor->obsoleteSourceValueReviewModalOpen)->toBeFalse();
    }
    expect(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::where('review_type', 'lang_value_obsolete')->count())->toBe(0);
});

it('rolls back source status and review when history fails', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $editor->openObsoleteSourceValueReview(TranslationWorkbenchKey::sole()->translation_key);
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Source history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $editor->confirmObsoleteSourceValue())->toThrow(RuntimeException::class, 'Source history unavailable');
    expect(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::where('review_type', 'lang_value_obsolete')->count())->toBe(0)
        ->and($editor->obsoleteSourceValueReviewModalOpen)->toBeTrue();
});

it('preserves repeated provenance reviews and rejects unsupported direct writer decisions', function () {
    $editor = workbenchValueEditor();
    $row = workbenchProvenanceReport($editor);
    $writer = app(TranslationWorkbenchProvenanceReviewWriter::class);
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $writer->save($row, $finding, $key, 'ignore_for_now');
    $writer->save($row, $finding, $key, 'ignore_for_now');
    expect(TranslationWorkbenchReview::where('review_type', 'suspicious_key_provenance')->count())->toBe(2)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'suspicious_key_reviewed')->count())->toBe(2);
    expect(fn () => $writer->save($row, $finding, $key, 'unsupported'))->toThrow(InvalidArgumentException::class);
    expect(TranslationWorkbenchReview::where('review_type', 'suspicious_key_provenance')->count())->toBe(2);
});

it('can mark an unlinked source value obsolete without changing other keys or locales', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $orphan = TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'messages', 'lang_key' => 'orphan',
        'translation_key' => 'messages.orphan', 'value' => 'Orphan text', 'status' => 'active',
        'source_path' => 'lang/en/messages.php',
    ]);
    $writer = app(TranslationWorkbenchCleanupReviewWriter::class);
    expect($writer->markSourceObsolete(' messages.orphan ', 'en'))->toBeTrue()
        ->and($orphan->fresh()->status)->toBe('obsolete')
        ->and(TranslationWorkbenchReview::where('review_type', 'lang_value_obsolete')->sole()->key_id)->toBeNull()
        ->and(TranslationWorkbenchLangValue::where('id', '<>', $orphan->id)->orderBy('id')->get()->toArray())->toBe($before)
        ->and($writer->markSourceObsolete('messages.orphan', 'en'))->toBeFalse()
        ->and($writer->markSourceObsolete('messages.orphan', 'de'))->toBeNull()
        ->and($writer->markSourceObsolete(' ', 'en'))->toBeNull();
});

function workbenchScalarEditor(): TranslationWorkbenchEntries
{
    DB::connection()->getPdo()->sqliteCreateFunction('string_to_array', static fn (string $value, string $separator): string => json_encode(explode($separator, $value)), 2);
    DB::connection()->getPdo()->sqliteCreateFunction('array_length', static fn (string $value, int $dimension): int => count(json_decode($value, true)), 2);
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    TranslationWorkbenchKey::sole()->update(['translation_key' => 'ui.save']);
    TranslationWorkbenchLangValue::query()->update(['translation_key' => 'ui.save', 'namespace' => 'ui', 'lang_key' => 'save']);
    $editor->scalarReviewSelectedLangValueIds = [TranslationWorkbenchLangValue::where('locale', 'en')->sole()->id];
    $editor->openScalarTransformModal();
    $editor->scalarTransformTargetPath = 'ui.actions';

    return $editor;
}

it('transforms scalar keys and all locale values without publishing files', function () {
    $editor = workbenchScalarEditor();
    $before = TranslationWorkbenchLangValue::orderBy('id')->pluck('value', 'locale')->all();
    $editor->confirmScalarTransformToArray();
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('ui.actions.save')
        ->and(TranslationWorkbenchLangValue::pluck('translation_key')->unique()->all())->toBe(['ui.actions.save'])
        ->and(TranslationWorkbenchLangValue::orderBy('id')->pluck('value', 'locale')->all())->toBe($before)
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->sole()->source_path)->toBe('lang/de/ui.php')
        ->and(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'scalar_lang_values_rekeyed')->sole()->new_values['migrated_count'])->toBe(2)
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe("{{ __('Welcome aboard') }}")
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and($editor->scalarReviewSelectedLangValueIds)->toBe([]);
});

it('blocks scalar transform when a target exists even if obsolete', function (string $status) {
    $editor = workbenchScalarEditor();
    TranslationWorkbenchLangValue::create(['locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'actions.save', 'translation_key' => 'ui.actions.save', 'value' => 'Existing', 'source_path' => 'lang/de/ui.php', 'status' => $status]);
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $editor->confirmScalarTransformToArray();
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('ui.save')
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(0);
})->with(['active', 'obsolete']);

it('rejects invalid scalar targets and empty included selections', function () {
    $editor = workbenchScalarEditor();
    $editor->scalarTransformTargetPath = 'invalid!';
    $editor->confirmScalarTransformToArray();
    $editor->scalarTransformTargetPath = 'ui.actions';
    $editor->scalarTransformIncludedLangValueIds = [];
    $editor->confirmScalarTransformToArray();
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('ui.save')
        ->and(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(0);
});

it('rolls back scalar key values and review when the final history event fails', function () {
    $editor = workbenchScalarEditor();
    $beforeKey = TranslationWorkbenchKey::sole()->toArray();
    $beforeValues = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class)->makePartial();
    $recorder->shouldReceive('record')->withArgs(fn (...$args) => ($args[0] ?? null) === 'scalar_lang_values_rekeyed')->once()->andThrow(new RuntimeException('Scalar history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $editor->confirmScalarTransformToArray())->toThrow(RuntimeException::class, 'Scalar history unavailable');
    expect(TranslationWorkbenchKey::sole()->toArray())->toBe($beforeKey)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($beforeValues)
        ->and(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(0);
});

it('blocks duplicate scalar targets and permits excluding the conflicting row', function () {
    $editor = workbenchScalarEditor();
    $second = TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'messages', 'lang_key' => 'save', 'translation_key' => 'messages.save', 'value' => 'Save again', 'source_path' => 'lang/en/messages.php', 'status' => 'active', 'value_type' => 'string']);
    $editor->scalarReviewSelectedLangValueIds[] = $second->id;
    $editor->openScalarTransformModal();
    $editor->scalarTransformTargetPath = 'ui.actions';
    $editor->confirmScalarTransformToArray();
    expect(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(0);
    $editor->scalarTransformIncludedLangValueIds = array_values(array_diff($editor->scalarTransformIncludedLangValueIds, [$second->id]));
    $editor->confirmScalarTransformToArray();
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('ui.actions.save')
        ->and($second->fresh()->translation_key)->toBe('messages.save');
});

it('rechecks scalar target conflicts in the writer after preview', function () {
    workbenchScalarEditor();
    $writer = app(TranslationWorkbenchScalarTransformWriter::class);
    $id = TranslationWorkbenchLangValue::where('locale', 'en')->sole()->id;
    $context = $writer->context([['id' => $id, 'translation_key' => 'ui.save', 'leaf' => 'save']], [$id], ' UI . Actions ');
    expect($context['invalid_count'])->toBe(0)->and($context['conflict_count'])->toBe(0);
    TranslationWorkbenchLangValue::create(['locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'actions.save', 'translation_key' => 'ui.actions.save', 'value' => 'New conflict', 'source_path' => 'lang/de/ui.php', 'status' => 'active']);
    expect(fn () => $writer->transform($context['rows']))->toThrow(ValidationException::class);
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('ui.save')
        ->and(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(0);
});

it('migrates unlinked scalar values while preserving obsolete source history', function () {
    workbenchScalarEditor();
    $active = TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'messages', 'lang_key' => 'orphan', 'translation_key' => 'messages.orphan', 'value' => 'Orphan', 'source_path' => 'lang/en/messages.php', 'status' => 'active']);
    $obsolete = TranslationWorkbenchLangValue::create(['locale' => 'de', 'namespace' => 'messages', 'lang_key' => 'orphan', 'translation_key' => 'messages.orphan', 'value' => 'Alt', 'source_path' => 'lang/de/messages.php', 'status' => 'obsolete']);
    $writer = app(TranslationWorkbenchScalarTransformWriter::class);
    expect($writer->transform([['id' => $active->id, 'translation_key' => 'messages.orphan', 'target_translation_key' => 'messages.labels.orphan']]))->toBe(1)
        ->and($active->fresh()->translation_key)->toBe('messages.labels.orphan')
        ->and($obsolete->fresh()->translation_key)->toBe('messages.orphan')
        ->and(TranslationWorkbenchReview::where('review_type', 'scalar_key_transform')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'scalar_lang_values_rekeyed')->count())->toBe(1);
});

function workbenchSourceLinkEditor(): array
{
    $editor = workbenchDynamicEditor();
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $ids = [];
    foreach (['runtime_options', 'runtime_db_options', 'collection', 'enum'] as $type) {
        $ids[] = DB::table('translation_workbench_dynamic_sources')->insertGetId([
            'fingerprint' => hash('sha256', $type), 'key_id' => $key->id, 'finding_id' => $finding->id,
            'source_type' => $type, 'values_count' => 2, 'status' => 'active',
        ]);
    }
    foreach ($ids as $id) {
        foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label) {
            DB::table('translation_workbench_dynamic_source_values')->insert([
                'dynamic_source_id' => $id, 'value_key' => $value, 'native_label' => $label, 'status' => 'active',
            ]);
        }
    }
    $editor->dynamicReviewFindingId = $finding->id;
    $editor->openDynamicSourceLinkConfirm($ids[2]);

    return [$editor, $ids];
}

it('links every runtime source with a value snapshot without altering translations or sources', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    $sources = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    $editor->confirmDynamicSourceLink();
    $candidates = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get();
    expect($candidates)->toHaveCount(2)
        ->and($candidates->pluck('dynamic_source_id')->all())->toBe([$ids[0], $ids[1]])
        ->and($candidates->pluck('review_status')->unique()->all())->toBe(['confirmed'])
        ->and(json_decode($candidates->first()->candidate_values, true))->toBe(['draft' => 'Draft', 'published' => 'Published'])
        ->and($candidates->first()->candidate_reference)->toBe('dynamic_source:'.$ids[2])
        ->and(TranslationWorkbenchReview::where('review_type', 'dynamic_source_link')->sole()->meta['runtime_source_count'])->toBe(2)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_link_confirmed')->count())->toBe(1)
        ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($sources)
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe("{{ __('Welcome aboard') }}")
        ->and($editor->dynamicSourceLinkConfirmModalOpen)->toBeFalse();
});

it('updates existing links and can unlink then reactivate them without duplicates', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    $originalIds = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->pluck('id')->all();
    DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[2])->where('value_key', 'draft')->update(['native_label' => 'Revised']);
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->confirmDynamicSourceLink();
    expect(TranslationWorkbenchReview::where('decision', 'runtime_options_link_confirmed')->count())->toBe(2)
        ->and(json_decode(DB::table('translation_workbench_dynamic_source_candidates')->first()->candidate_values, true)['draft'])->toBe('Revised');
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->unlinkDynamicSourceLink();
    expect(DB::table('translation_workbench_dynamic_source_candidates')->where('status', 'inactive')->count())->toBe(2);
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->unlinkDynamicSourceLink();
    expect(TranslationWorkbenchReview::where('decision', 'runtime_options_link_unlinked')->count())->toBe(1);
    $editor->confirmDynamicSourceLink();
    expect(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->pluck('id')->all())->toBe($originalIds)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->where('status', 'active')->count())->toBe(2);
});

it('unlinks only the selected related source', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    $editor->openDynamicSourceLinkConfirm($ids[3]);
    $editor->confirmDynamicSourceLink();
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->unlinkDynamicSourceLink();
    expect(DB::table('translation_workbench_dynamic_source_candidates')->where('candidate_reference', 'dynamic_source:'.$ids[3])->where('status', 'active')->count())->toBe(2)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->where('candidate_reference', 'dynamic_source:'.$ids[2])->where('review_status', 'unlinked')->count())->toBe(2);
});

it('does not write links with missing or obsolete source context', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    DB::table('translation_workbench_dynamic_sources')->where('id', $ids[2])->update(['status' => 'obsolete']);
    $editor->confirmDynamicSourceLink();
    $editor->unlinkDynamicSourceLink();
    $editor->dynamicReviewFindingId = null;
    $editor->confirmDynamicSourceLink();
    expect(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchReview::where('review_type', 'dynamic_source_link')->count())->toBe(0);
});

it('rolls back all dynamic link changes when history fails', function (bool $unlink) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    if ($unlink) {
        $editor->confirmDynamicSourceLink();
        $editor->openDynamicSourceLinkConfirm($ids[2]);
    }
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $reviews = TranslationWorkbenchReview::where('review_type', 'dynamic_source_link')->count();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Link history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $unlink ? $editor->unlinkDynamicSourceLink() : $editor->confirmDynamicSourceLink())->toThrow(RuntimeException::class, 'Link history unavailable');
    expect(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchReview::where('review_type', 'dynamic_source_link')->count())->toBe($reviews)
        ->and($editor->dynamicSourceLinkConfirmModalOpen)->toBeTrue();
})->with([false, true]);

it('denies scalar edits by guests and users without admin roles before writing', function (bool $guest) {
    $editor = workbenchValueEditor();
    if ($guest) {
        auth()->forgetUser();
    } else {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
        $this->actingAs($user);
    }
    expect(fn () => $editor->saveTranslationValue())->toThrow(AuthorizationException::class);
    expect(fn () => app(TranslationWorkbenchScalarEditor::class)->save(
        TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id, [['locale' => 'de', 'value' => 'Denied']],
    ))->toThrow(AuthorizationException::class);
    expect(TranslationWorkbenchLangValue::count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->count())->toBe(0);
})->with([true, false]);

it('rejects unavailable variants atomically instead of silently skipping them', function () {
    $editor = workbenchValueEditor();
    $editor->selectedTargetSubLocales = ['fr-FR'];
    $editor->targetSubTranslationValues = ['fr-FR' => 'Bonjour'];
    expect(fn () => $editor->saveTranslationValue())->toThrow(ValidationException::class);
    expect(TranslationWorkbenchLangValue::count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_values_saved')->count())->toBe(0);
});

it('rejects missing or inactive scalar assignments and malformed values at the application boundary', function () {
    workbenchValueEditor();
    $service = app(TranslationWorkbenchScalarEditor::class);
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    expect(fn () => $service->save($key->id, $finding->id, [['locale' => 'de', 'value' => ['bad']]]))->toThrow(ValidationException::class);
    expect(fn () => $service->save($key->id, 999999, [['locale' => 'de', 'value' => 'Bad']]))->toThrow(ValidationException::class);
    TranslationWorkbenchKeyFinding::query()->update(['status' => 'inactive']);
    expect(fn () => $service->save($key->id, $finding->id, [['locale' => 'de', 'value' => 'Bad']]))->toThrow(ValidationException::class);
    expect(TranslationWorkbenchLangValue::count())->toBe(0);
});

it('rechecks roles on consecutive scalar service calls', function () {
    workbenchValueEditor();
    $allowed = true;
    // A mutable reference models a fresh role lookup after revocation.
    $user = Mockery::mock(User::class)->makePartial();
    $user->id = 1;
    $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturnUsing(function () use (&$allowed) {
        return $allowed;
    });
    $this->actingAs($user);
    $service = app(TranslationWorkbenchScalarEditor::class);
    $keyId = TranslationWorkbenchKey::sole()->id;
    $findingId = TranslationWorkbenchFinding::sole()->id;
    $service->save($keyId, $findingId, [['locale' => 'de', 'value' => 'Allowed']]);
    $allowed = false;
    expect(fn () => $service->save($keyId, $findingId, [['locale' => 'de', 'value' => 'Denied']]))->toThrow(AuthorizationException::class);
    expect(TranslationWorkbenchLangValue::sole()->value)->toBe('Allowed');
});

it('rechecks variant activation and rejects an unrelated existing key', function () {
    workbenchValueEditor();
    $key = TranslationWorkbenchKey::sole();
    $other = $key->replicate();
    $other->fingerprint = hash('sha256', 'unrelated-key');
    $other->translation_key = 'messages.unrelated';
    $other->save();
    $finding = TranslationWorkbenchFinding::sole();
    $service = app(TranslationWorkbenchScalarEditor::class);
    expect(fn () => $service->save($other->id, $finding->id, [['locale' => 'de', 'value' => 'Wrong key']]))->toThrow(ValidationException::class);
    DB::table('locales')->insert(['code' => 'de-AT', 'normalized_code' => 'de-AT', 'is_active' => true]);
    $service->save($key->id, $finding->id, [['locale' => 'de_AT', 'value' => 'Servus']]);
    DB::table('locales')->where('code', 'de-AT')->update(['is_active' => false]);
    expect(fn () => $service->save($key->id, $finding->id, [['locale' => 'de-AT', 'value' => 'Unavailable']]))->toThrow(ValidationException::class);
    expect(TranslationWorkbenchLangValue::sole()->value)->toBe('Servus');
});

it('denies both dynamic editor actions without an authorized user', function (bool $guest) {
    $editor = workbenchDynamicEditor();
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    if ($guest) {
        auth()->forgetUser();
    } else {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
        $this->actingAs($user);
    }
    $service = app(TranslationWorkbenchDynamicEditor::class);
    $keyId = TranslationWorkbenchKey::sole()->id;
    $findingId = TranslationWorkbenchFinding::sole()->id;
    expect(fn () => $editor->saveDynamicMultiTranslationValues())->toThrow(AuthorizationException::class);
    expect(fn () => $editor->saveDynamicMultiSourceValue('v_'.sha1('draft')))->toThrow(AuthorizationException::class);
    expect(fn () => $service->save($keyId, $findingId, [], []))->toThrow(AuthorizationException::class);
    expect(fn () => $service->saveSource($keyId, $findingId, 'v_'.sha1('draft'), 'Denied'))->toThrow(AuthorizationException::class);
    expect(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before);
})->with([true, false]);

it('rejects injected option fields and malformed dynamic values without partial writes', function () {
    $editor = workbenchDynamicEditor();
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    $field = 'v_'.sha1('foreign-option');
    $editor->dynamicMultiSourceValues[$field] = 'Injected';
    expect(fn () => $editor->saveDynamicMultiTranslationValues())->toThrow(ValidationException::class);
    expect(fn () => $editor->saveDynamicMultiSourceValue($field))->toThrow(ValidationException::class);
    $service = app(TranslationWorkbenchDynamicEditor::class);
    $keyId = TranslationWorkbenchKey::sole()->id;
    $findingId = TranslationWorkbenchFinding::sole()->id;
    expect(fn () => $service->save($keyId, $findingId, [], ['v_'.sha1('draft') => ['bad']]))->toThrow(ValidationException::class);
    expect(fn () => $service->saveSource($keyId, $findingId, 'v_'.sha1('draft'), ['bad']))->toThrow(ValidationException::class);
    expect(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::whereIn('event_type', ['dynamic_multi_values_saved', 'dynamic_multi_source_value_saved'])->count())->toBe(0);
});

it('rechecks dynamic assignments and ignores client supplied option mappings', function () {
    $editor = workbenchDynamicEditor();
    $field = 'v_'.sha1('draft');
    $editor->dynamicMultiValueKeyMap[$field] = 'published';
    $editor->dynamicMultiSourceValues[$field] = 'Corrected draft';
    $editor->saveDynamicMultiSourceValue($field);
    expect(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'en')->where('value_key', 'draft')->value('value'))->toBe('Corrected draft')
        ->and(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'en')->where('value_key', 'published')->value('value'))->toBe('Published');
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    TranslationWorkbenchKeyFinding::query()->update(['status' => 'inactive']);
    $service = app(TranslationWorkbenchDynamicEditor::class);
    $keyId = TranslationWorkbenchKey::sole()->id;
    $findingId = TranslationWorkbenchFinding::sole()->id;
    expect(fn () => $service->save($keyId, $findingId, [], []))->toThrow(ValidationException::class);
    expect(fn () => $service->saveSource($keyId, $findingId, $field, 'Invalid'))->toThrow(ValidationException::class);
    expect(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before);
});

it('resolves confirmed runtime options again after unlinking while retaining stored options', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    // Only the confirmed candidate associates this runtime source with the current entry.
    DB::table('translation_workbench_dynamic_sources')->where('id', $ids[0])->update(['key_id' => null, 'finding_id' => null]);
    DB::table('translation_workbench_dynamic_source_values')->insert(['dynamic_source_id' => $ids[0], 'value_key' => 'runtime-only', 'native_label' => 'Runtime', 'status' => 'active']);
    $keyId = TranslationWorkbenchKey::sole()->id;
    $findingId = TranslationWorkbenchFinding::sole()->id;
    $resolver = app(TranslationWorkbenchDynamicOptions::class);
    $locales = app(TranslationWorkbenchEditorLocales::class)->resolve();
    expect(array_column($resolver->rows($keyId, $findingId, $locales), 'value_key'))->toContain('runtime-only');
    DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $ids[0])->update(['status' => 'inactive', 'review_status' => 'unlinked']);
    expect(array_column($resolver->rows($keyId, $findingId, $locales), 'value_key'))->not->toContain('runtime-only');
    $service = app(TranslationWorkbenchDynamicEditor::class);
    expect(fn () => $service->saveSource($keyId, $findingId, 'v_'.sha1('runtime-only'), 'Denied'))->toThrow(ValidationException::class);
    expect($service->saveSource($keyId, $findingId, 'v_'.sha1('draft'), 'Retained'))->not->toBeNull();
});

it('denies single key review actions without the current admin role', function (string $action) {
    $editor = workbenchValueEditor();
    $id = TranslationWorkbenchFinding::sole()->id;
    $before = TranslationWorkbenchKey::sole()->toArray();
    $reviews = TranslationWorkbenchReview::count();
    $user = Mockery::mock(User::class)->makePartial();
    $user->id = 1;
    $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
    $this->actingAs($user);
    $editor->translationKeyFindingId = $id;
    $editor->translationKeyValue = 'messages.denied';
    $call = fn () => match ($action) {
        'saveTranslationKeyModal' => $editor->saveTranslationKeyModal(),
        'acceptSuggestedTranslationKey' => $editor->acceptSuggestedTranslationKey($id),
        'setDynamicReviewMode' => $editor->setDynamicReviewMode($id, 'multi'),
        default => $editor->{$action}($id, true),
    };
    expect($call)->toThrow(AuthorizationException::class);
    expect(TranslationWorkbenchKey::sole()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews);
})->with(['saveTranslationKeyModal', 'acceptSuggestedTranslationKey', 'setDynamicReviewMode', 'setUiKeyReview', 'setUiKeyRejected', 'setUiCandidateClassification', 'setDynamicKeyReview', 'setDynamicKeyRejected', 'setDynamicCandidateClassification', 'setDynamicMultiKeyReview']);

it('protects direct key review calls and rechecks assignments', function () {
    workbenchValueEditor();
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $before = $key->toArray();
    $service = app(TranslationWorkbenchKeyReviewEditor::class);
    auth()->forgetUser();
    expect(fn () => $service->acceptSuggestedKey($key->id, $finding->id))->toThrow(AuthorizationException::class);
    expect(fn () => $service->saveDecision($key->id, $finding->id, [], 'classification', 'ui_key_confirmed'))->toThrow(AuthorizationException::class);
    $user = Mockery::mock(User::class)->makePartial();
    $user->id = 1;
    $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(true);
    $this->actingAs($user);
    TranslationWorkbenchKeyFinding::query()->update(['status' => 'inactive']);
    expect(fn () => $service->acceptSuggestedKey($key->id, $finding->id))->toThrow(ValidationException::class);
    expect(fn () => $service->saveDecision($key->id, $finding->id, [], 'classification', 'ui_key_confirmed'))->toThrow(ValidationException::class);
    expect($key->fresh()->toArray())->toBe($before);
});

it('validates direct manual key targets and resolves suggestions from current data', function () {
    workbenchValueEditor();
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $service = app(TranslationWorkbenchKeyReviewEditor::class);
    expect(fn () => $service->saveDecision($key->id, $finding->id, ['translation_key' => 'invalid!'], 'translation_key', 'translation_key_updated'))->toThrow(ValidationException::class);
    expect(fn () => $service->saveDecision($key->id, $finding->id, [], 'classification', 'unsupported'))->toThrow(ValidationException::class);
    TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'messages', 'lang_key' => 'container.child', 'translation_key' => 'messages.container.child', 'value' => 'Child', 'status' => 'active', 'source_path' => 'lang/en/messages.php']);
    expect(fn () => $service->saveDecision($key->id, $finding->id, ['translation_key' => 'messages.container'], 'translation_key', 'translation_key_updated'))->toThrow(ValidationException::class);
    $key->update(['suggested_key' => 'messages.current']);
    expect($service->acceptSuggestedKey($key->id, $finding->id))->toBeTrue()
        ->and($key->fresh()->translation_key)->toBe('messages.current');
});

it('denies bulk and scalar writes for guests and revoked roles without changing data', function (string $operation, bool $guest) {
    $editor = $operation === 'bulk' ? workbenchBulkEditor() : workbenchScalarEditor();
    $tables = ['translation_workbench_keys', 'translation_workbench_key_findings', 'translation_workbench_lang_values', 'translation_workbench_reviews', 'translation_workbench_timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    // Resolve the service while authorized; authorization must be checked on each write.
    $writer = app($operation === 'bulk' ? TranslationWorkbenchBulkKeyWriter::class : TranslationWorkbenchScalarTransformWriter::class);
    if ($guest) {
        auth()->forgetUser();
    } else {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
        $this->actingAs($user);
    }
    if ($operation === 'bulk') {
        expect(fn () => $editor->confirmBulkEqualizeTranslationKey())->toThrow(AuthorizationException::class);
        expect(fn () => $writer->equalize($editor->bulkEqualizeSelectedFindingIds, 'ui.shared.denied'))->toThrow(AuthorizationException::class);
    } else {
        expect(fn () => $editor->confirmScalarTransformToArray())->toThrow(AuthorizationException::class);
        expect(fn () => $writer->transform([[
            'id' => TranslationWorkbenchLangValue::first()->id,
            'translation_key' => 'ui.save',
            'target_translation_key' => 'ui.denied.save',
        ]]))->toThrow(AuthorizationException::class);
    }
    expect($snapshot())->toBe($before);
})->with(['bulk', 'scalar'])->with([true, false]);

it('protects review writes at the UI and service boundaries after access is lost', function (string $action, bool $guest) {
    $editor = workbenchCleanupEditor();
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $inventory = TranslationWorkbenchKeyInventory::findOrFail($editor->langCleanupInventoryId);
    $row = workbenchProvenanceReport($editor, false);
    $editor->codeUpdateConflictFindingId = $finding->id;
    $editor->codeUpdateConflictKeyId = $key->id;
    $editor->codeUpdateConflictDecision = 'existing_key_should_be_replaced';
    $editor->obsoleteSourceValueTranslationKey = $key->translation_key;

    $serviceCall = match ($action) {
        'conflict' => fn () => app(TranslationWorkbenchConflictReviewWriter::class)->save($finding, $key, 'existing_key_should_be_replaced', 'Denied'),
        'cleanup' => fn () => app(TranslationWorkbenchCleanupReviewWriter::class)->save($inventory, 'en', 'obsolete'),
        'source' => fn () => app(TranslationWorkbenchCleanupReviewWriter::class)->markSourceObsolete($key->translation_key, 'en'),
        'provenance' => fn () => app(TranslationWorkbenchProvenanceReviewWriter::class)->save($row, null, null, 'needs_literal_restore'),
    };
    $uiCall = match ($action) {
        'conflict' => fn () => $editor->saveCodeUpdateConflictReview(),
        'cleanup' => fn () => $editor->saveLangCleanupReview('obsolete'),
        'source' => fn () => $editor->confirmObsoleteSourceValue(),
        'provenance' => fn () => $editor->reviewSuspiciousKeyProvenance('needs_literal_restore'),
    };
    $tables = ['translation_workbench_keys', 'translation_workbench_key_findings', 'translation_workbench_lang_values', $inventory->getTable(), 'translation_workbench_reviews', 'translation_workbench_timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $source = File::get(base_path('resources/views/value.blade.php'));
    $reportPath = storage_path('translation-workbench/translation-workbench-detect-suspicious-keyed-additions.json');
    $report = File::get($reportPath);
    Artisan::shouldReceive('call')->never();
    if ($guest) {
        auth()->forgetUser();
    } else {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
        $this->actingAs($user);
    }
    expect($uiCall)->toThrow(AuthorizationException::class);
    expect($serviceCall)->toThrow(AuthorizationException::class);
    expect($snapshot())->toBe($before)
        ->and(File::get(base_path('resources/views/value.blade.php')))->toBe($source)
        ->and(File::get($reportPath))->toBe($report)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
})->with(['conflict', 'cleanup', 'source', 'provenance'])->with([true, false]);

it('denies dynamic source link writes after access is lost without changing links or history', function (bool $unlink, bool $guest) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    if ($unlink) {
        $editor->confirmDynamicSourceLink();
        $editor->openDynamicSourceLinkConfirm($ids[2]);
    }
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $selected = (object) ['id' => $finding->id, 'key_id' => $key->id, 'suggested_key' => $finding->suggested_key, 'dynamic_scope' => $finding->dynamic_scope];
    $runtimeSources = DB::table('translation_workbench_dynamic_sources')->whereIn('id', [$ids[0], $ids[1]])->get()->map(fn ($row) => (array) $row)->all();
    $related = (array) DB::table('translation_workbench_dynamic_sources')->find($ids[2]);
    $related['values'] = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[2])->get()->map(fn ($row) => (array) $row)->all();
    $writer = app(TranslationWorkbenchDynamicSourceLinkWriter::class);
    $tables = ['translation_workbench_dynamic_sources', 'translation_workbench_dynamic_source_values', 'translation_workbench_dynamic_source_candidates', 'translation_workbench_dynamic_key_values', 'translation_workbench_keys', 'translation_workbench_reviews', 'translation_workbench_timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    if ($guest) {
        auth()->forgetUser();
    } else {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
        $this->actingAs($user);
    }
    $action = $unlink ? 'unlinkDynamicSourceLink' : 'confirmDynamicSourceLink';
    $method = $unlink ? 'unlink' : 'confirm';
    expect(fn () => $editor->{$action}())->toThrow(AuthorizationException::class);
    expect(fn () => $writer->{$method}($selected, $runtimeSources, $related))->toThrow(AuthorizationException::class);
    expect($snapshot())->toBe($before);
})->with([false, true])->with([true, false]);

it('blocks report refreshes and pipeline startup before invoking commands or services', function (string $action, bool $guest) {
    $editor = new TranslationWorkbenchEntries;
    $editor->findingsActiveTab = 'shared-key-candidates';
    Artisan::shouldReceive('call')->never();
    $exporter = Mockery::mock(TranslationWorkbenchLangFileExporter::class);
    $exporter->shouldNotReceive('export');
    app()->instance(TranslationWorkbenchLangFileExporter::class, $exporter);
    $tracker = Mockery::mock(TranslationWorkbenchPipelineRunTracker::class);
    $tracker->shouldNotReceive('latest');
    $tracker->shouldNotReceive('create');
    app()->instance(TranslationWorkbenchPipelineRunTracker::class, $tracker);
    if ($guest) {
        auth()->forgetUser();
    } else {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
        $this->actingAs($user);
    }
    expect(fn () => $editor->{$action}())->toThrow(AuthorizationException::class);
})->with(['refreshFindingsCurrentTab', 'refreshFindingsAllTabs', 'refreshCodeUpdateApplyReport', 'startCompletePipelineRun'])->with([true, false]);

it('retains the existing report command sequence for authorized refreshes', function () {
    $editor = new TranslationWorkbenchEntries;
    $editor->findingsActiveTab = 'shared-key-candidates';
    Artisan::shouldReceive('call')->once()->with('translation-workbench:detect-shared-key-candidates')->andReturn(0);
    Artisan::shouldReceive('call')->once()->with('translation-workbench:apply-code-updates')->andReturn(0);
    $editor->refreshFindingsCurrentTab();
    $editor->refreshCodeUpdateApplyReport();
});

it('rechecks the persisted pipeline actor without relying on the current session', function () {
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', ['started_from' => 'ui', 'user_id' => 1]);
    $user = Mockery::mock(User::class)->makePartial();
    $allowed = true;
    $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturnUsing(function () use (&$allowed) {
        return $allowed;
    });
    $provider = Mockery::mock('Illuminate\Contracts\Auth\UserProvider');
    $provider->shouldReceive('retrieveById')->with(1)->andReturn($user, $user, null);
    Auth::shouldReceive('createUserProvider')->andReturn($provider);
    $access = app(TranslationWorkbenchPipelineAccess::class);
    $access->authorize((string) $run->id);
    $allowed = false;
    expect(fn () => $access->authorize((string) $run->id))->toThrow(AuthorizationException::class);
    expect(fn () => $access->authorize((string) $run->id))->toThrow(AuthorizationException::class);
    $access->authorize(null);
    expect(fn () => $access->authorize('999999'))->toThrow(AuthorizationException::class);
    expect(fn () => $access->authorize('0'))->toThrow(AuthorizationException::class);
});

it('rejects tracked runs with missing identity or completed status', function (array $options, string $status) {
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', $options);
    $run->update(['status' => $status]);
    expect(fn () => app(TranslationWorkbenchPipelineAccess::class)->authorize((string) $run->id))->toThrow(AuthorizationException::class);
})->with([
    [['started_from' => 'ui'], 'pending'],
    [[], 'pending'],
    [['started_from' => 'ui', 'user_id' => 1], 'finished'],
    [['started_from' => 'ui', 'user_id' => 1], 'failed'],
]);

it('fails an unauthorized background run before any pipeline steps or reports', function () {
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', ['started_from' => 'ui']);
    $exit = Artisan::call('translation:workbench', ['--complete' => true, '--pipeline-run-id' => (string) $run->id]);
    expect($exit)->toBe(1)
        ->and($run->fresh()->status)->toBe('failed')
        ->and($run->fresh()->error_message)->toContain('required Admin')
        ->and($run->steps()->count())->toBe(0)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('stops before the next pipeline command when actor authorization is revoked', function () {
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', ['started_from' => 'ui', 'user_id' => 1]);
    $access = Mockery::mock(TranslationWorkbenchPipelineAccess::class);
    $checks = 0;
    $access->shouldReceive('validateOptions')->with((string) $run->id, Mockery::type('array'))->twice();
    $access->shouldReceive('authorize')->with((string) $run->id)->times(3)->andReturnUsing(function () use (&$checks) {
        if (++$checks === 3) {
            throw new AuthorizationException('Pipeline actor role revoked.');
        }
    });
    app()->instance(TranslationWorkbenchPipelineAccess::class, $access);
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());

    $runner->setName('translation:workbench');
    $application = new Application;
    $application->addCommand($runner);
    $calls = 0;
    $scan = new Command('translation-workbench:scan');
    $scan->setCode(function () use (&$calls) {
        $calls++;

        return 0;
    });
    $application->addCommand($scan);
    $exit = $runner->run(new ArrayInput(['--pipeline-run-id' => (string) $run->id]), new BufferedOutput);
    expect($exit)->toBe(1)
        ->and($run->fresh()->status)->toBe('failed')
        ->and($run->fresh()->error_message)->toBe('Pipeline actor role revoked.')
        ->and($calls)->toBe(1)
        ->and($run->steps()->where('step_number', 1)->value('status'))->toBe('finished')
        ->and($run->steps()->where('step_number', 2)->value('status'))->toBe('failed');
});

it('claims a pending pipeline only once and leaves an active run untouched on duplicate command startup', function () {
    $tracker = app(TranslationWorkbenchPipelineRunTracker::class);
    $run = $tracker->create('translation:workbench', ['started_from' => 'ui', 'user_id' => 1]);
    $tracker->claim((string) $run->id);
    $before = $run->fresh()->toArray();
    expect(fn () => $tracker->claim((string) $run->id))->toThrow(AuthorizationException::class);
    expect(Artisan::call('translation:workbench', ['--pipeline-run-id' => (string) $run->id]))->toBe(1)
        ->and($run->fresh()->toArray())->toBe($before)
        ->and($run->steps()->count())->toBe(0);
});

it('binds all pipeline execution options to the stored UI request', function (string $option, mixed $value) {
    $expected = array_replace(RunTranslationWorkbench::executionDefaults(), ['--complete' => true]);
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', [...$expected, 'started_from' => 'ui', 'user_id' => 1]);
    $access = app(TranslationWorkbenchPipelineAccess::class);
    $access->validateOptions((string) $run->id, $expected);
    $actual = array_replace($expected, [$option => $value]);
    expect(fn () => $access->validateOptions((string) $run->id, $actual))->toThrow(AuthorizationException::class);
    $run->update(['options' => ['started_from' => 'ui', 'user_id' => 1, '--complete' => true]]);
    expect(fn () => $access->validateOptions((string) $run->id, $expected))->toThrow(AuthorizationException::class);
})->with([
    ['--complete', false], ['--paths', 'app'], ['--source-locale', 'de'],
    ['--truncate', true], ['--force-truncate', true], ['--truncate-foundation', true],
    ['--dry-run', true], ['--skip-foundation', true], ['--write-lang-files', true],
]);

it('fails a claimed pipeline with changed options before invoking any subcommand', function () {
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', [
        ...RunTranslationWorkbench::executionDefaults(), '--complete' => true, 'started_from' => 'ui', 'user_id' => 1,
    ]);
    $access = Mockery::mock(TranslationWorkbenchPipelineAccess::class)->makePartial();
    $access->shouldReceive('authorize')->once()->with((string) $run->id);
    app()->instance(TranslationWorkbenchPipelineAccess::class, $access);
    // Deliberately omit --complete; actual command handling must reject before any scan.
    expect(Artisan::call('translation:workbench', ['--pipeline-run-id' => (string) $run->id]))->toBe(1)
        ->and($run->fresh()->status)->toBe('failed')
        ->and($run->fresh()->error_message)->toContain('options differ')
        ->and($run->steps()->count())->toBe(0);
});

it('keeps command execution defaults independent of presentation and tracking options', function () {
    $defaults = RunTranslationWorkbench::executionDefaults();
    expect($defaults)->toHaveKey('--complete', false)->toHaveKey('--source-locale', 'en')
        ->not->toHaveKey('--pipeline-run-id')->not->toHaveKey('--verbose')->not->toHaveKey('--no-interaction');
});

it('detects released pipeline locks and only closes interrupted runs after explicit review', function () {
    $tracker = app(TranslationWorkbenchPipelineRunTracker::class);
    $process = app(TranslationWorkbenchPipelineProcess::class);
    $run = $tracker->create('translation:workbench', ['started_from' => 'ui', 'user_id' => 1]);
    $lock = $process->acquire($run->id);
    $run = $tracker->claim((string) $run->id);
    $steps = [['label' => 'Done', 'command' => 'one', 'arguments' => []], ['label' => 'In progress', 'command' => 'two', 'arguments' => []]];
    $tracker->start($run, $steps);
    $tracker->finishStep($run, 1, 0, 10);
    $tracker->startStep($run, 2, 2, $steps[1]);
    try {
        expect($process->state($run->fresh()))->toBe('active');
        expect(fn () => $process->markInterrupted($run->id))->toThrow(ValidationException::class);
        expect(Artisan::call('translation:workbench', ['--pipeline-run-id' => (string) $run->id]))->toBe(1)
            ->and($run->fresh()->status)->toBe('running');
    } finally {
        fclose($lock);
    }
    expect($process->state($run->fresh()))->toBe('interrupted')
        ->and($run->fresh()->status)->toBe('running');
    $editor = new TranslationWorkbenchEntries;
    $editor->markPipelineInterrupted($run->id);
    expect($run->fresh()->status)->toBe('failed')
        ->and($run->fresh()->meta['interruption_confirmed_by'])->toBe(1)
        ->and($run->steps()->where('step_number', 1)->value('status'))->toBe('finished')
        ->and($run->steps()->where('step_number', 2)->value('status'))->toBe('failed');
    expect(fn () => $process->markInterrupted($run->id))->toThrow(ValidationException::class);
});

it('refuses interruption correction without reliable local process evidence', function (bool $foreignHost) {
    $tracker = app(TranslationWorkbenchPipelineRunTracker::class);
    $process = app(TranslationWorkbenchPipelineProcess::class);
    $run = $tracker->create('translation:workbench', ['started_from' => 'ui']);
    $run = $tracker->claim((string) $run->id);
    if ($foreignHost) {
        fclose($process->acquire($run->id));
        $run->update(['meta' => ['process_host' => 'another-host']]);
    }
    expect($process->state($run))->toBe('unknown');
    expect(fn () => $process->markInterrupted($run->id))->toThrow(ValidationException::class);
    expect($run->fresh()->status)->toBe('running');
})->with([true, false]);

it('requires current authorization to mark an interrupted run', function () {
    auth()->forgetUser();
    expect(fn () => (new TranslationWorkbenchEntries)->markPipelineInterrupted(1))->toThrow(AuthorizationException::class);
});

it('records pipeline initiation separately from review authorship and clears context after a failed step', function (bool $tracked) {
    $context = app(TranslationWorkbenchPipelineContext::class);
    $run = $tracked ? app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', [
        ...RunTranslationWorkbench::executionDefaults(), 'started_from' => 'ui', 'user_id' => 1,
    ]) : null;
    if ($tracked) {
        $access = Mockery::mock(TranslationWorkbenchPipelineAccess::class)->makePartial();
        $access->shouldReceive('authorize')->twice()->with((string) $run->id);
        app()->instance(TranslationWorkbenchPipelineAccess::class, $access);
    }
    auth()->forgetUser();
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $scan = new Command('translation-workbench:scan');
    $scan->setCode(function () {
        app(TranslationWorkbenchTimelineRecorder::class)->record('pipeline_test_observation', context: ['source' => 'test-scan']);
        throw new RuntimeException('Simulated scan interruption');
    });
    $application->addCommand($scan);
    $exit = $runner->run(new ArrayInput($tracked ? ['--pipeline-run-id' => (string) $run->id] : []), new BufferedOutput);
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'pipeline_test_observation')->sole();
    expect($exit)->toBe(1)
        ->and($event->context['pipeline'])->toBe(['run_id' => $run?->id, 'origin' => $tracked ? 'ui' : 'cli', 'initiated_by_user_id' => $tracked ? 1 : null])
        ->and($event->context['source'])->toBe('test-scan')
        ->and($event->created_by_user_id)->toBeNull()
        ->and(TranslationWorkbenchReview::count())->toBe(0)
        ->and($context->current())->toBeNull();
    $outside = app(TranslationWorkbenchTimelineRecorder::class)->record('outside_pipeline', context: ['source' => 'outside']);
    expect($outside->context)->toBe(['source' => 'outside']);
})->with([true, false]);

it('runs the database value comparison without modifying inventories or publishing files', function () {
    createLegacyExportTables();
    $keyId = DB::table('translation_keys')->insertGetId(['key' => 'ui.sample', 'group' => 'ui', 'status' => 'active', 'workflow_status' => 'reviewed']);
    DB::table('translation_values')->insert(['translation_key_id' => $keyId, 'locale' => 'de_AT', 'value' => 'Legacy', 'status' => 'ok', 'is_base_duplicate' => true]);
    TranslationWorkbenchLangValue::create(['translation_key' => 'ui.sample', 'locale' => 'de-at', 'value' => 'Workbench', 'status' => 'active', 'namespace' => 'ui', 'lang_key' => 'sample', 'source_path' => 'lang/de-at/ui.php']);
    $snapshot = fn () => [DB::table('translation_values')->get()->toJson(), TranslationWorkbenchLangValue::all()->toJson(), TranslationWorkbenchTimelineEvent::count(), TranslationWorkbenchReview::count()];
    $before = $snapshot();
    expect(Artisan::call('translation-workbench:compare-values', ['--json' => true]))->toBe(0);
    $report = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);
    expect($report['counts']['different'])->toBe(1)
        ->and($report['status_differences'])->toBe(0)
        ->and((bool) $report['comparisons'][0]['legacy'][0]['is_base_duplicate'])->toBeTrue()
        ->and($snapshot())->toBe($before)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('updates a multiline call without touching its commented twin or replacement arguments', function () {
    $file = base_path('resources/views/welcome.blade.php');
    $call = "__(\n    'Welcome aboard',\n    ['name' => \$name],\n)";
    $comment = "{{-- ".$call." --}}";
    File::put($file, $comment."\n{{ ".$call." }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::where('status', 'active')->sole();
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey($finding->id);
    $editor->editFindingId = $finding->id;
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    $key = $finding->keys()->firstOrFail()->translation_key;
    expect(app(TranslationWorkbenchCodeUpdateApplier::class)->apply(keys: ['unrelated.key'])['summary']['planned_safe_updates'])->toBe(0);
    $expected = $comment."\n{{ ".str_replace("'Welcome aboard'", "'".$key."'", $call)." }}";
    $dry = app(TranslationWorkbenchCodeUpdateApplier::class)->apply();
    expect($dry['summary']['would_apply'])->toBe(1)
        ->and($dry['summary']['duplicate_expression'])->toBe(0)
        ->and(File::get($file))->not->toBe($expected);
    $result = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);
    expect($result['summary']['applied'])->toBe(1)
        ->and(File::get($file))->toBe($expected);
});

it('does not normalize changes inside a translation argument when matching source calls', function () {
    $scanner = app(TranslationWorkbenchScanner::class);
    expect($scanner->activeSourceCalls("{{ __('Two  spaces') }}", "__('Two spaces')"))->toBe([])
        ->and($scanner->activeSourceCalls("{{-- __('Same') --}}", "__('Same')"))->toBe([])
        ->and($scanner->activeSourceCalls("{{ __(\n'Same'\n) }}", "__('Same')"))->toHaveCount(1);
});

it('publishes explicit dynamic options and resolves them without database reads or global literal aliases', function () {
    workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['is_dynamic_key' => true, 'is_dynamic_multi' => true]);
    $writer = app(TranslationWorkbenchTranslationWriter::class);
    $writer->saveDynamicValues($key, TranslationWorkbenchFinding::sole(), [
        ['value_key' => 'draft', 'locale' => 'de', 'value' => 'Entwurf', 'native_label' => 'Draft', 'source' => 'translation_workbench_modal'],
        ['value_key' => 'a.b', 'locale' => 'de', 'value' => 'Punkt', 'native_label' => 'Dot', 'source' => 'translation_workbench_modal'],
        ['value_key' => 'a%2Eb', 'locale' => 'de', 'value' => 'Prozent', 'native_label' => 'Percent', 'source' => 'translation_workbench_modal'],
    ]);
    $exporter = app(TranslationWorkbenchLangFileExporter::class);
    $plan = $exporter->export(['de'], keys: [$key->translation_key], dynamicOptions: true);
    expect($plan['values_new'])->toBe(3)->and($plan['files_written'])->toBe(0)->and($plan['values_pruned'])->toBe(0);
    $result = $exporter->export(['de'], write: true, keys: [$key->translation_key], dynamicOptions: true);
    expect($result['files_written'])->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->where('key_id', $key->id)->count())->toBe(3);
    $translator = new Illuminate\Translation\Translator(new Illuminate\Translation\FileLoader(new Illuminate\Filesystem\Filesystem, lang_path()), 'de');
    $previous = app('translator');
    app()->instance('translator', $translator);
    try {
        DB::enableQueryLog(); DB::flushQueryLog();
        expect(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label($key->translation_key, 'draft', 'Draft'))->toBe('Entwurf')
            ->and(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label($key->translation_key, 'a.b', 'Dot'))->toBe('Punkt')
            ->and(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label($key->translation_key, 'a%2Eb', 'Percent'))->toBe('Prozent')
            ->and(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label($key->translation_key, 'unknown', 'Unknown'))->toBe('Unknown')
            ->and($translator->get('Draft'))->toBe('Draft')->and(DB::getQueryLog())->toBe([]);
    } finally { app()->instance('translator', $previous); DB::disableQueryLog(); }
    expect($exporter->export(['de'], keys: [$key->translation_key], dynamicOptions: true)['values_new'])->toBe(0);
});

it('blocks dynamic option publication over a scalar parent and requires explicit reviewed owners', function () {
    workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['is_dynamic_key' => true]);
    app(TranslationWorkbenchTranslationWriter::class)->saveDynamicValues($key, TranslationWorkbenchFinding::sole(), [
        ['value_key' => 'draft', 'locale' => 'de', 'value' => 'Entwurf', 'native_label' => 'Draft', 'source' => 'translation_workbench_modal'],
    ]);
    [$namespace, $path] = explode('.', $key->translation_key, 2);
    $content = []; Arr::set($content, $path, 'Keep scalar');
    File::ensureDirectoryExists(lang_path('de'));
    $file = lang_path('de/'.$namespace.'.php'); File::put($file, '<?php return '.var_export($content, true).';');
    $before = File::get($file);
    $exporter = app(TranslationWorkbenchLangFileExporter::class);
    $result = $exporter->export(['de'], write: true, keys: [$key->translation_key], dynamicOptions: true);
    expect($result['values_conflicted'])->toBe(1)->and($result['files_written'])->toBe(0)->and(File::get($file))->toBe($before);
    expect(fn () => $exporter->export(dynamicOptions: true))->toThrow(InvalidArgumentException::class);
    $key->update(['review_status' => 'pending']);
    expect(fn () => $exporter->export(keys: [$key->translation_key], dynamicOptions: true))->toThrow(InvalidArgumentException::class);
});

it('discovers explicit option owners and preserves their reviewed assignment on rescan', function () {
    workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['is_dynamic_key' => true, 'is_dynamic_multi' => true]);
    $call = '\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label('.var_export($key->translation_key, true).', $value, $label)';
    File::put(base_path('resources/views/options.blade.php'), '{{ '.$call.' }}'."\n".'{{-- {{ '.$call.' }} --}}');
    $items = app(TranslationWorkbenchScanner::class)->scan(['resources/views/options.blade.php']);
    expect($items)->toHaveCount(2)->and($items[0]->kind)->toBe('dynamic_multi')
        ->and($items[0]->translationKey)->toBe($key->translation_key)->and($items[1]->meta['source_context'])->toBe('commented_out');
    app(TranslationWorkbenchFoundationSyncer::class)->sync($items, markObsolete: false);
    expect(TranslationWorkbenchKey::count())->toBe(1)
        ->and(TranslationWorkbenchKeyFinding::where('key_id', $key->id)->where('status', 'active')->count())->toBe(2);
});

it('keeps unreviewed and missing dynamic values out of the explicit publication command', function () {
    workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['key_type' => 'dynamic_candidate']);
    $exporter = app(TranslationWorkbenchLangFileExporter::class);
    expect($exporter->export(['en'], keys: [$key->translation_key], dynamicOptions: true)['values_exportable'])->toBe(0);
    DB::table('translation_workbench_dynamic_key_values')->where('value_key', 'draft')->update(['reviewed_at' => now()]);
    DB::table('translation_workbench_dynamic_key_values')->where('value_key', 'published')->update(['reviewed_at' => now(), 'status' => 'missing', 'value' => null]);
    $this->artisan('translation-workbench:export-lang-files', ['--locale' => ['en'], '--key' => [$key->translation_key], '--dynamic-options' => true])
        ->expectsOutputToContain('Mode: dynamic options')->expectsOutputToContain('Exportable values: 1')->assertSuccessful()->run();
    expect(File::exists(lang_path('en/'.explode('.', $key->translation_key)[0].'.php')))->toBeFalse();
    $key->update(['is_dynamic_candidate_rejected' => true]);
    expect(fn () => $exporter->export(['en'], keys: [$key->translation_key], dynamicOptions: true))->toThrow(InvalidArgumentException::class);
});

it('plans the reviewed owner instead of an older unnamed candidate and blocks competing names', function () {
    $file = base_path('resources/views/owners.blade.php');
    File::put($file, "<p>{{ __('Owner label') }}</p>\n");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $pending = TranslationWorkbenchKey::sole();
    $pending->update(['translation_key' => null, 'review_status' => 'pending']);
    $reviewed = $pending->replicate();
    $reviewed->forceFill(['fingerprint' => hash('sha256', 'reviewed-owner'), 'translation_key' => 'ui.owner', 'review_status' => 'reviewed'])->save();
    TranslationWorkbenchKeyFinding::create(['key_id' => $reviewed->id, 'finding_id' => $finding->id, 'relation_type' => 'candidate', 'status' => 'active']);
    foreach (['en' => 'Owner label', 'de' => 'Besitzer'] as $locale => $value) {
        TranslationWorkbenchLangValue::create(['locale' => $locale, 'namespace' => 'ui', 'lang_key' => 'owner', 'translation_key' => 'ui.owner', 'value' => $value, 'status' => 'active', 'source_path' => 'lang/'.$locale.'/ui.php']);
    }
    $planner = app(TranslationWorkbenchCodeUpdatePlanner::class);
    $plan = $planner->plan();
    expect($plan['summary']['safe_updates'])->toBe(1)
        ->and($plan['updates'][0]['key_id'])->toBe($reviewed->id);

    $duplicate = $reviewed->replicate();
    $duplicate->fingerprint = hash('sha256', 'same-reviewed-name');
    $duplicate->save();
    TranslationWorkbenchKeyFinding::create(['key_id' => $duplicate->id, 'finding_id' => $finding->id, 'relation_type' => 'candidate', 'status' => 'active']);
    expect($planner->plan()['summary']['safe_updates'])->toBe(1);

    $duplicate->update(['translation_key' => 'ui.competing']);
    foreach ([[], ['ui.owner'], ['ui.competing']] as $filter) {
        $plan = $planner->plan(keys: $filter);
        expect($plan['summary']['safe_updates'])->toBe(0)
            ->and($plan['summary']['manual_review'])->toBe(1)
            ->and($plan['updates'][0]['reason'])->toBe('multiple_reviewed_translation_keys');
    }
    expect(app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true)['summary']['applied'])->toBe(0)
        ->and(File::get($file))->toBe("<p>{{ __('Owner label') }}</p>\n");
    $duplicate->update(['status' => 'obsolete']);
    expect($planner->plan()['summary']['safe_updates'])->toBe(1);
});

it('preserves a uniquely moved finding and its reviewed relationship across source rescans', function () {
    $file = base_path('resources/views/moved.blade.php');
    File::put($file, "{{ __('Moved label') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey($finding->id);
    $key = TranslationWorkbenchKey::sole();
    $relationId = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->value('id');
    $signature = $finding->source_signature;
    File::put($file, "\n\n{{ __('Moved label') }}\n");
    $result = $syncer->sync($scanner->scan(['resources/views']));
    expect($result['findings_created'])->toBe(0)
        ->and($result['findings_updated'])->toBe(1)
        ->and(TranslationWorkbenchFinding::sole()->id)->toBe($finding->id)
        ->and($finding->fresh()->source_line)->toBe(3)
        ->and($finding->fresh()->source_signature)->not->toBe($signature)
        ->and($key->fresh()->review_status)->toBe('reviewed')
        ->and(TranslationWorkbenchKeyFinding::findOrFail($relationId)->status)->toBe('active')
        ->and(TranslationWorkbenchTimelineEvent::where('finding_id', $finding->id)->where('event_type', 'finding_changed')->exists())->toBeTrue();
    expect($syncer->sync($scanner->scan(['resources/views']))['findings_created'])->toBe(0);
});

it('does not relocate a repeated expression when syncing only one selected occurrence', function () {
    $file = base_path('resources/views/repeated.blade.php');
    File::put($file, "{{ __('Repeated label') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $old = TranslationWorkbenchFinding::sole();
    File::put($file, "\n{{ __('Repeated label') }}\n{{ __('Repeated label') }}\n");
    $result = $syncer->sync($scanner->scan(['resources/views'])->take(1));
    expect($result['findings_created'])->toBe(1)
        ->and($old->fresh()->source_line)->toBe(1)
        ->and($old->fresh()->status)->toBe('active');
});
