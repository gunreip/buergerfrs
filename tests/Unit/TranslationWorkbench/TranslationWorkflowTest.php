<?php

use App\Models\User;
use App\Settings\AppGeneralSettings;
use Flux\Flux;
use Gunreip\TranslationWorkbench\Console\ImportTranslationWorkbenchLangValues;
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
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangImportConflict;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangValueImporter;
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

function publishReviewedFixture(int $keyId, int $findingId, array $locales, bool $dynamic = false): array
{
    $kind = $dynamic ? 'option' : 'scalar';
    $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    foreach ($approval->inspect($kind, $keyId, $findingId, $locales) as $row) {
        $approval->approve($kind, $row['snapshot']['value_id'], $keyId, $findingId, $row['fingerprint']);
    }
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->preview($kind, $keyId, $findingId, $locales);
    $run = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class)->publish($kind, $keyId, $findingId, $locales, $report['fingerprint']);

    return ['files_written' => count(array_filter($run['files'], fn ($file) => $file['written']))];
}

/** Existing published file fixture; deliberately no approval or publication history. */
function installPublishedValueFixture(string $translationKey, array $locales): void
{
    foreach (TranslationWorkbenchLangValue::where('translation_key', $translationKey)->whereIn('locale', $locales)->get() as $row) {
        $values = [];
        Arr::set($values, $row->lang_key, $row->value);
        File::ensureDirectoryExists(lang_path($row->locale));
        File::put(lang_path($row->locale.'/'.$row->namespace.'.php'), '<?php return '.var_export($values, true).';');
    }
}

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
    // Publication fixtures use an explicitly nested leaf key.
    $editor->acceptSuggestedTranslationKey($finding->id);
    app(TranslationWorkbenchKeyReviewEditor::class)->saveDecision(TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id, ['translation_key' => 'ui.fixture.welcome'], 'translation_key', 'translation_key_updated');
    $key->refresh();
    expect($key->review_status)->toBe('reviewed');
    // Review alone does not authorize replacement before translations are available.
    expect($planner->plan()['summary']['safe_updates'])->toBe(0);
    $editor->openEditModal($finding->id);
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    expect(TranslationWorkbenchReview::where('decision', 'suggested_key_accepted')->count())->toBe(1);
    expect(TranslationWorkbenchLangValue::where('locale', 'de')->value('value'))->toBe('Willkommen an Bord');

    $exporter = app(TranslationWorkbenchLangFileExporter::class);
    $dryExport = $exporter->export(['en', 'de']);
    expect($dryExport['files_written'])->toBe(0)->and(File::allFiles(lang_path()))->toHaveCount(0);
    $export = publishReviewedFixture($key->id, $finding->id, ['en', 'de']);
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

    $scan = $scanner->scanWithManifest(['resources/views']);
    $syncer->sync($scan['items'], markObsolete: true, reconciliationPaths: $scan['scanned_paths']);
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

it('keeps reviewed source unchanged until its source translation is published', function () {
    $file = base_path('resources/views/welcome.blade.php');
    $source = "{{ __('Welcome aboard') }}";
    File::put($file, $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    app(TranslationWorkbenchKeyReviewEditor::class)->saveDecision(TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id, ['translation_key' => 'ui.fixture.source_only'], 'translation_key', 'translation_key_updated');
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = null;
    $editor->saveTranslationValue();

    $report = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);

    expect($report['summary']['applied'])->toBe(0)
        ->and(File::get($file))->toBe($source)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(0);

    $key = TranslationWorkbenchKey::sole();
    publishReviewedFixture($key->id, TranslationWorkbenchFinding::sole()->id, ['en']);
    $plan = app(TranslationWorkbenchCodeUpdatePlanner::class)->plan();
    expect($plan['summary']['safe_updates'])->toBe(1)
        ->and($plan['updates'][0]['lang_state']['target_main_exists'])->toBeFalse();
    $result = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);
    expect($result['summary']['applied'])->toBe(1)
        ->and(File::get($file))->toContain($key->translation_key)
        ->and(File::exists(lang_path('de/'.explode('.', $key->translation_key)[0].'.php')))->toBeFalse();
    $translator = new \Illuminate\Translation\Translator(
        new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, lang_path()), 'de');
    $translator->setFallback('en');
    expect($translator->get($key->translation_key))->toBe('Welcome aboard');
});

it('does not overwrite source edited after the scan', function () {
    $file = base_path('resources/views/welcome.blade.php');
    File::put($file, "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
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

    if ($write) {
        expect(fn () => app(TranslationWorkbenchLangFileExporter::class)->export(['en'], ['ui'], true))->toThrow(LogicException::class);
    }
    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], ['ui']);

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

it('prepares a scalar leaf without replacing its array parent or untouched sibling', function () {
    File::ensureDirectoryExists(lang_path('en'));
    $path = lang_path('en/ui.php');
    File::put($path, "<?php\nreturn ['button' => ['cancel' => ['cancel' => 'Old', 'hint' => 'Keep']]];\n");
    TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'leaf'), 'translation_key' => 'ui.button.cancel.cancel', 'status' => 'open']);
    TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'button.cancel.cancel', 'translation_key' => 'ui.button.cancel.cancel', 'value' => 'Cancel', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php']);

    $report = app(TranslationWorkbenchLangFileExporter::class)->prepare(['en'], ['ui.button.cancel.cancel']);
    $prepared = base_path('prepared-leaf.php');
    File::put($prepared, $report['plans'][0]['_contents']);

    expect($report['values_conflicted'])->toBe(0)
        ->and($report['files_written'])->toBe(0)
        ->and(require $prepared)->toBe(['button' => ['cancel' => ['cancel' => 'Cancel', 'hint' => 'Keep']]]);
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

    if ($write) {
        expect(fn () => app(TranslationWorkbenchLangFileExporter::class)->export(['en'], write: true, keys: ['ui.selected', 'ui.obsolete']))->toThrow(LogicException::class);
    }
    $report = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], keys: ['ui.selected', 'ui.obsolete']);

    expect($report['keys'])->toBe(['ui.selected', 'ui.obsolete'])
        ->and($report['files'])->toBe(1)
        ->and($report['values_exportable'])->toBe(1)
        ->and($report['values_pruned'])->toBe(1)
        ->and($report['files_written'])->toBe(0)
        ->and(File::get(lang_path('en/other.php')))->toBe($other);
    expect(File::get(lang_path('en/ui.php')))->toBe($original)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
    $empty = app(TranslationWorkbenchLangFileExporter::class)->export(['en'], keys: ['ui.does-not-exist']);
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

it('requires an explicit conflict decision before reusing an obsolete target leaf', function () {
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

    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $reviews = TranslationWorkbenchReview::count();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyReviewWriter::class)->saveDecision(
        $key, $finding, $finding,
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyAttributes::class)->forKey('ui.button.cancel.cancel', $key),
        'translation_key', 'translation_key_updated',
    ))->toThrow(ValidationException::class, 'target key already has a value');
    expect(TranslationWorkbenchLangValue::orderBy('id')->get()->toJson())->toBe($before)
        ->and($key->fresh()->translation_key)->toBe('ui.button.cancel')
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
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
    $report = publishReviewedFixture($key->id, $finding->id, ['en']);

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
    // Publication fixtures use an explicitly nested leaf key.
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    app(TranslationWorkbenchKeyReviewEditor::class)->saveDecision(TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id, ['translation_key' => 'ui.fixture.welcome'], 'translation_key', 'translation_key_updated');
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    publishReviewedFixture(TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id, ['de']);
    $target = TranslationWorkbenchLangValue::where('locale', 'de')->sole();
    $file = lang_path('de/'.$target->namespace.'.php');
    $before = File::get($file);

    createLegacyExportTables();
    DB::table('translation_keys')->insert(['id' => 1, 'key' => $target->namespace.'.legacy', 'group' => $target->namespace, 'status' => 'ok']);
    foreach (['en', 'de'] as $locale) {
        DB::table('translation_values')->insert(['translation_key_id' => 1, 'locale' => $locale, 'value' => 'Legacy text', 'status' => 'ok']);
    }

    $this->artisan('translations:export-lang-files', ['--locales' => 'en,de', '--dry-run' => $dryRun])
        ->expectsOutputToContain($dryRun ? 'Export blocked' : 'Legacy export writes are disabled')
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

    $command = $this->artisan('translations:export-lang-files', ['--locales' => 'de', '--dry-run' => true]);
    if ($blocked) {
        $command->expectsTable(['Locale', 'File', 'Translation key', 'Conflict'], [['de', 'lang/de/ui.php', 'ui.owned', 'would overwrite']])->assertFailed()->run();
        expect(File::get($file))->toBe($before);
    } else {
        $command->assertSuccessful()->run();
        expect(File::get($file))->toBe($before);
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
    $command = $this->artisan('translations:export-lang-files', ['--locales' => 'de', '--dry-run' => true]);
    if ($replaceContainer) {
        $command->expectsOutputToContain('ui.button.save')->assertFailed()->run();
        expect(File::get($file))->toBe($before);
    } else {
        $command->assertSuccessful()->run();
        expect(File::get($file))->toBe($before);
    }
})->with([false, true]);

function workbenchValueEditor(bool $nestedPublicationKey = true): TranslationWorkbenchEntries
{
    File::put(base_path('resources/views/value.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $editor = new TranslationWorkbenchEntries;
    // Publication fixtures use an explicitly nested leaf key.
    $editor->acceptSuggestedTranslationKey(TranslationWorkbenchFinding::sole()->id);
    if ($nestedPublicationKey) {
        app(TranslationWorkbenchKeyReviewEditor::class)->saveDecision(TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id, ['translation_key' => 'ui.fixture.welcome'], 'translation_key', 'translation_key_updated');
    }
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
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
    $editor->openEditModal($finding->id);
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
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
    $editor->continueDynamicEdit();
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
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
    $editor->continueDynamicEdit();
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
    if ($sameLiteral) {
        $editor->openBulkEqualizeTranslationKeyModal();
    }
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
    $editor->openBulkEqualizeTranslationKeyModal();
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

it('refuses the entire bulk selection when a selected source is no longer available', function (string $change) {
    $editor = workbenchBulkEditor();
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    $ids = $editor->bulkEqualizeSelectedFindingIds;
    expect($writer->context($ids)['can_confirm'])->toBeTrue();

    if ($change === 'missing') {
        // Two valid findings must not be processed while a third selected ID is missing.
        $ids[] = 999999;
    } elseif ($change === 'file') {
        $finding = TranslationWorkbenchFinding::findOrFail($ids[1]);
        DB::table('translation_workbench_source_files')->where('id', $finding->source_file_id)->update(['status' => 'obsolete']);
    } else {
        TranslationWorkbenchFinding::findOrFail($ids[1])->update(['status' => $change]);
    }

    $keys = TranslationWorkbenchKey::orderBy('id')->get()->toArray();
    $reviews = TranslationWorkbenchReview::count();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect($writer->context($ids)['can_confirm'])->toBeFalse()
        ->and($writer->equalize($ids, 'ui.shared.welcome'))->toBe(0)
        ->and(TranslationWorkbenchKey::orderBy('id')->get()->toArray())->toBe($keys)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['obsolete', 'stale', 'commented_out', 'file', 'missing']);

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
    $reviewsBefore = TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count();
    $editor->saveTranslationValue();
    $oldKey = TranslationWorkbenchKey::sole()->translation_key;
    $editor->translationKeyFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->translationKeyValue = 'messages.renamed.welcome';
    $editor->saveTranslationKeyModal();
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe('messages.renamed.welcome')
        ->and(TranslationWorkbenchLangValue::where('translation_key', $oldKey)->count())->toBe(0)
        ->and(TranslationWorkbenchLangValue::where('translation_key', 'messages.renamed.welcome')->count())->toBe(2)
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->sole()->value)->toBe('Willkommen')
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe($reviewsBefore + 1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_lang_values_rekeyed')->sole()->context['migrated_count'])->toBe(2)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('rejects occupied rename targets without retiring source values or leaving history', function () {
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
    $keyBefore = TranslationWorkbenchKey::sole()->translation_key;
    $valuesBefore = TranslationWorkbenchLangValue::orderBy('id')->get()->toJson();
    $eventsBefore = TranslationWorkbenchTimelineEvent::count();
    $reviewsBefore = TranslationWorkbenchReview::count();
    expect(fn () => $editor->saveTranslationKeyModal())->toThrow(ValidationException::class, 'target key already has a value');
    expect(TranslationWorkbenchKey::sole()->translation_key)->toBe($keyBefore)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toJson())->toBe($valuesBefore)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($eventsBefore)
        ->and(TranslationWorkbenchReview::count())->toBe($reviewsBefore);
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
    $editor = workbenchValueEditor(nestedPublicationKey: false);
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
    $reviewsBefore = TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count();
    $eventsBefore = TranslationWorkbenchTimelineEvent::where('event_type', 'translation_key_updated')->count();
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
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe($reviewsBefore)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_key_updated')->count())->toBe($eventsBefore);
});

it('refuses manual scalar keys that point at a language container', function () {
    $editor = workbenchValueEditor();
    $reviewsBefore = TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count();
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
        ->and(TranslationWorkbenchReview::where('decision', 'translation_key_updated')->count())->toBe($reviewsBefore);
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
        ->and($key->fresh()->translation_key)->toBe($finding->suggested_key)
        ->and($key->fresh()->suggested_key)->toBe('messages.current');
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
    expect(fn () => $writer->{$method}($selected, $runtimeSources, $related, []))->toThrow(AuthorizationException::class);
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
    $scan = new Command('translation-workbench:sync-foundation');
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
    $scan = new Command('translation-workbench:sync-foundation');
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
    $editor->openEditModal($finding->id);
    $editor->sourceTranslationValue = 'Welcome aboard';
    $editor->targetTranslationValue = 'Willkommen an Bord';
    $editor->saveTranslationValue();
    $key = $finding->keys()->firstOrFail()->translation_key;
    installPublishedValueFixture($key, ['en']);
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
    $result = publishReviewedFixture($key->id, TranslationWorkbenchFinding::sole()->id, ['de'], true);
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
    $result = $exporter->export(['de'], keys: [$key->translation_key], dynamicOptions: true);
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
    installPublishedValueFixture('ui.owner', ['en']);
    $planner = app(TranslationWorkbenchCodeUpdatePlanner::class);
    $plan = $planner->plan();
    expect($plan['summary']['safe_updates'])->toBe(1)
        ->and($plan['updates'][0]['key_id'])->toBe($reviewed->id);

    $duplicate = $reviewed->replicate();
    $duplicate->fingerprint = hash('sha256', 'same-reviewed-name');
    $duplicate->save();
    TranslationWorkbenchKeyFinding::create(['key_id' => $duplicate->id, 'finding_id' => $finding->id, 'relation_type' => 'candidate', 'status' => 'active']);
    expect($planner->plan()['summary']['safe_updates'])->toBe(0)
        ->and($planner->plan()['updates'][0]['reason'])->toBe('multiple_reviewed_key_records');

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
    $successor = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($result['findings_created'])->toBe(1)
        ->and($result['findings_obsoleted'])->toBe(1)
        ->and($successor->id)->not->toBe($finding->id)
        ->and($successor->source_line)->toBe(3)
        ->and($finding->fresh()->source_signature)->toBe($signature)
        ->and($finding->fresh()->meta['successor_finding_id'])->toBe($successor->id)
        ->and($key->fresh()->review_status)->toBe('reviewed')
        ->and(TranslationWorkbenchKeyFinding::findOrFail($relationId)->status)->toBe('obsolete')
        ->and(TranslationWorkbenchTimelineEvent::where('finding_id', $finding->id)->where('event_type', 'finding_superseded')->exists())->toBeTrue();
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
    expect(fn () => $syncer->sync($scanner->scan(['resources/views'])->take(1)))
        ->toThrow(RuntimeException::class, 'Ambiguous finding occurrence');
    expect(TranslationWorkbenchFinding::count())->toBe(1)
        ->and($old->fresh()->source_line)->toBe(1)
        ->and($old->fresh()->status)->toBe('active');
});

it('preserves and blocks indistinguishable same-line occurrences without retiring their active finding', function (bool $moved) {
    $file = base_path('resources/views/same-line.blade.php');
    File::put($file, "{{ __('ui.key.suggested-key') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile($file));
    $finding = TranslationWorkbenchFinding::sole();
    // Simulate the historical line-only identity; no column can be guessed for it.
    $finding->forceFill(['source_column' => null, 'source_signature' => 'historical-line-only-signature'])->save();
    $before = $finding->getAttributes();
    $events = TranslationWorkbenchTimelineEvent::count();
    File::put($file, ($moved ? "\n" : '')."{{ __('ui.key.suggested-key') => __('ui.key.suggested-key') }}\n");
    $items = $scanner->scanFile($file);
    expect($items)->toHaveCount(2)
        ->and($items->pluck('sourceSignature')->unique())->toHaveCount(2)
        ->and($items->pluck('meta.source_offset')->unique())->toHaveCount(2);
    $stored = (object) [...$before, 'source_path' => 'resources/views/same-line.blade.php'];
    $rows = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOccurrenceMatcher::class)
        ->compare($items, collect([$stored]));
    expect(array_column($rows, 'state'))->toBe(['ambiguous', 'ambiguous'])
        ->and($rows[0]['finding_ids'])->toBe([$finding->id]);
    expect(fn () => $syncer->sync($items->take(1)))
        ->toThrow(RuntimeException::class, 'Ambiguous finding occurrence');
    expect($finding->fresh()->getAttributes())->toBe($before)
        ->and(TranslationWorkbenchFinding::count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['unchanged line' => false, 'moved line' => true]);

it('stores separate same-line calls and retains their identities on repeated scans', function () {
    $file = base_path('resources/views/two-calls.blade.php');
    File::put($file, "{{ __('ui.key.same') => __('ui.key.same') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $items = $scanner->scanFile($file);
    expect($syncer->sync($items)['findings_created'])->toBe(2);
    $ids = TranslationWorkbenchFinding::orderBy('source_column')->pluck('id')->all();
    expect(TranslationWorkbenchFinding::where('status', 'active')->count())->toBe(2)
        ->and(TranslationWorkbenchFinding::pluck('source_column')->unique())->toHaveCount(2);
    expect($syncer->sync($scanner->scanFile($file))['findings_created'])->toBe(0)
        ->and(TranslationWorkbenchFinding::orderBy('source_column')->pluck('id')->all())->toBe($ids)
        ->and(TranslationWorkbenchFinding::where('status', 'active')->count())->toBe(2);
});

it('keeps the existing occurrence when another identical call is appended on the same line', function () {
    $file = base_path('resources/views/appended-call.blade.php');
    File::put($file, "{{ __('ui.key.same') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile($file));
    $original = TranslationWorkbenchFinding::sole();
    File::put($file, "{{ __('ui.key.same') }} {{ __('ui.key.same') }}\n");
    expect($syncer->sync($scanner->scanFile($file))['findings_created'])->toBe(1)
        ->and($original->fresh()->status)->toBe('active')
        ->and(TranslationWorkbenchFinding::where('status', 'active')->count())->toBe(2);
});

it('links a new same-line call to the reviewed owner without reviving the archived candidate', function () {
    $file = base_path('resources/views/copied-reviewed-call.blade.php');
    File::put($file, "{{ __('ui.key.same') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile($file));
    $finding = TranslationWorkbenchFinding::sole();
    $archived = TranslationWorkbenchKey::sole();
    $owner = $archived->replicate();
    $owner->forceFill(['fingerprint' => hash('sha256', 'reviewed-shared-owner'), 'review_status' => 'reviewed'])->save();
    $archived->forceFill(['status' => 'obsolete'])->save();
    TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->update(['key_id' => $owner->id]);
    $before = [$finding->fresh()->getAttributes(), $archived->fresh()->getAttributes(), $owner->fresh()->getAttributes()];
    File::put($file, "{{ __('ui.key.same') }} {{ __('ui.key.same') }}\n");
    $newCall = $scanner->scanFile($file)->slice(1)->values();
    $result = $syncer->sync($newCall);
    $created = TranslationWorkbenchFinding::where('id', '!=', $finding->id)->sole();
    expect($result['findings_created'])->toBe(1)
        ->and($result['keys_created'])->toBe(0)
        ->and(TranslationWorkbenchKeyFinding::where('finding_id', $created->id)->sole()->key_id)->toBe($owner->id)
        ->and([$finding->fresh()->getAttributes(), $archived->fresh()->getAttributes(), $owner->fresh()->getAttributes()])->toBe($before);
    expect($syncer->sync($newCall)['findings_created'])->toBe(0)
        ->and(TranslationWorkbenchFinding::count())->toBe(2)
        ->and(TranslationWorkbenchKey::count())->toBe(2)
        ->and(TranslationWorkbenchKeyFinding::where('finding_id', $created->id)->count())->toBe(1);
});

it('preserves a reviewed file relocation through subsequent scans and position changes', function (string $expression, bool $shift) {
    $old = 'resources/views/old-location.blade.php';
    $new = 'resources/views/new-location.blade.php';
    File::put(base_path($old), "{{ ".$expression." }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile(base_path($old)));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $originalSuggestion = $finding->suggested_key;
    $keyBefore = $key->getAttributes();
    $linkBefore = TranslationWorkbenchKeyFinding::sole()->getAttributes();
    File::put(base_path($old), '');
    File::put(base_path($new), "{{ ".$expression." }}\n");
    $item = $scanner->scanFile(base_path($new))->sole();
    expect($item->suggestedKey)->not->toBe($originalSuggestion);
    $source = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchSourceFile::create([
        'path' => $new, 'source_type' => 'blade', 'status' => 'active',
    ]);
    $finding->forceFill(['source_file_id' => $source->id, 'source_line' => $item->sourceLine,
        'source_column' => $item->sourceColumn, 'source_signature' => $item->sourceSignature,
        'source_fingerprint' => $item->sourceFingerprint, 'meta' => [...($finding->meta ?? []),
            'source_relocation' => ['from_path' => $old, 'to_path' => $new, 'expression_fingerprint' => $item->expressionFingerprint]]])->save();
    if ($shift) { File::put(base_path($new), "\n    {{ ".$expression." }}\n"); }
    $items = $scanner->scanFile(base_path($new));
    $result = $syncer->sync($items);
    $current = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($result['findings_created'])->toBe($shift ? 1 : 0)
        ->and($result['keys_created'])->toBe(0)
        ->and($current->suggested_key)->toBe($originalSuggestion)
        ->and($key->fresh()->getAttributes())->toBe($keyBefore)
        ->and(TranslationWorkbenchKeyFinding::where('status', 'active')->sole()->key_id)->toBe($key->id)
        ->and($current->source_line)->toBe($shift ? 2 : 1);
    expect($syncer->sync($items)['findings_created'])->toBe(0)
        ->and(TranslationWorkbenchFinding::count())->toBe($shift ? 2 : 1)
        ->and(TranslationWorkbenchKey::count())->toBe(1);
})->with(["__('Open in VSC')", "__('ui.source.source')"])->with([false, true]);

it('archives only explicitly selected missing findings and preserves raw history', function () {
    $path = 'resources/views/missing-selection.blade.php';
    File::put(base_path($path), "{{ __('First old text') }}\n{{ __('Second old text') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile(base_path($path)));
    [$first, $second] = TranslationWorkbenchFinding::orderBy('id')->get()->all();
    $raw = $first->raw_expression;
    File::put(base_path($path), "{{ __('New replacement text') }}");
    $result = $syncer->reconcileMissingFindings([$first->id], [$path]);
    expect($result['findings_obsoleted'])->toBe(1)
        ->and($first->fresh()->status)->toBe('obsolete')
        ->and($first->fresh()->raw_expression)->toBe($raw)
        ->and($second->fresh()->status)->toBe('active')
        ->and(TranslationWorkbenchFinding::count())->toBe(2);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect($syncer->reconcileMissingFindings([$first->id], [$path])['findings_obsoleted'])->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
});

it('refuses to archive present moved or ambiguous findings', function (string $source) {
    $path = 'resources/views/not-missing.blade.php';
    File::put(base_path($path), "{{ __('Keep this') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile(base_path($path)));
    $finding = TranslationWorkbenchFinding::sole();
    $before = $finding->getAttributes();
    $events = TranslationWorkbenchTimelineEvent::count();
    File::put(base_path($path), $source);
    expect(fn () => $syncer->reconcileMissingFindings([$finding->id], [$path]))
        ->toThrow(RuntimeException::class, 'not confirmed missing');
    expect($finding->fresh()->getAttributes())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(["{{ __('Keep this') }}\n", "\n{{ __('Keep this') }}\n", "\n{{ __('Keep this') }} {{ __('Keep this') }}\n"]);

it('upgrades a unique historical line position without creating another finding', function () {
    $file = base_path('resources/views/historical-column.blade.php');
    File::put($file, "{{ __('ui.key.same') }}\n");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scanFile($file));
    $finding = TranslationWorkbenchFinding::sole();
    $finding->forceFill(['source_column' => null, 'source_signature' => 'historical-line-only'])->save();
    $reconciliation = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation::class);
    $report = $reconciliation->plan(['resources/views/historical-column.blade.php']);
    expect($report['summary'])->toBe(['position_refined' => 1]);
    $repair = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class);
    $plan = $repair->plan(['resources/views/historical-column.blade.php']);
    expect($plan['actions'][0]['type'])->toBe('refine_position')
        ->and($repair->apply($plan)[0]['state'])->toBe('applied')
        ->and($reconciliation->plan(['resources/views/historical-column.blade.php'])['summary'])->toBe(['unchanged' => 1]);
    expect($syncer->sync($scanner->scanFile($file))['findings_created'])->toBe(0)
        ->and($finding->fresh()->source_column)->toBe(4)
        ->and(TranslationWorkbenchFinding::count())->toBe(1);
});

it('rejects conflicting lang imports atomically without changing saved values or provenance', function (string $origin, string $status, string $fileValue) {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), '<?php return '.var_export(['new' => 'New file value', 'saved' => $fileValue], true).';');
    $row = TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'saved', 'translation_key' => 'ui.saved',
        'value' => 'Saved draft', 'value_type' => 'string', 'status' => $status,
        'source_path' => 'lang/en/ui.php', 'meta' => ['source' => $origin, 'editor_note' => 'Keep'],
    ]);
    $before = $row->fresh()->getAttributes();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => app(TranslationWorkbenchLangValueImporter::class)->import('en'))
        ->toThrow(TranslationWorkbenchLangImportConflict::class);
    expect($row->fresh()->getAttributes())->toBe($before)
        ->and(TranslationWorkbenchLangValue::count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with([
    'edited text' => ['translation-workbench:modal-edit', 'active', 'Old published value'],
    'imported text without revision provenance' => ['translation-workbench:import-lang-values', 'active', 'Different file value'],
    'unknown provenance' => ['historical-recovery', 'active', 'Different file value'],
    'obsolete same text' => ['translation-workbench:import-lang-values', 'obsolete', 'Saved draft'],
    'missing same text' => ['translation-workbench:modal-edit', 'missing', 'Saved draft'],
]);

it('keeps editorial metadata on matching imports and creates no duplicate change events', function () {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['saved' => 'Saved draft'];");
    $row = TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'saved', 'translation_key' => 'ui.saved',
        'value' => 'Saved draft', 'value_type' => 'string', 'status' => 'active',
        'source_path' => 'lang/en/ui.php', 'source_hash' => sha1('Saved draft'),
        'meta' => ['source' => 'translation-workbench:modal-edit', 'editor_note' => 'Keep'],
    ]);
    $meta = $row->meta;
    $importer = app(TranslationWorkbenchLangValueImporter::class);
    $importer->import('en');
    $second = $importer->import('en');
    expect($row->fresh()->meta)->toBe($meta)
        ->and($row->fresh()->source_hash)->toBe(sha1('Saved draft'))
        ->and($second['values_unchanged'])->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
});

it('still imports new lang values and observes unchanged imports without extra events', function () {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['new' => 'New value'];");
    $importer = app(TranslationWorkbenchLangValueImporter::class);
    expect($importer->import('en')['values_created'])->toBe(1)
        ->and($importer->import('en')['values_unchanged'])->toBe(1)
        ->and(TranslationWorkbenchLangValue::sole()->value)->toBe('New value')
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(1);
});

it('reports import conflicts with locale and key and fails the command', function () {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['saved' => 'File value'];");
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'saved', 'translation_key' => 'ui.saved',
        'value' => 'Draft', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php',
        'meta' => ['source' => 'translation-workbench:modal-edit'],
    ]);
    expect(Artisan::call('translation-workbench:import-lang-values', ['--source-locale' => 'en']))->toBe(1);
    expect(Artisan::output())->toContain('ui.saved', 'en', 'No lang values were changed');
});

it('does not allow truncate import to bypass saved value protection', function () {
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'saved', 'translation_key' => 'ui.saved',
        'value' => 'Draft', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php',
    ]);
    expect(fn () => app(TranslationWorkbenchLangValueImporter::class)->import('en', truncate: true))
        ->toThrow(TranslationWorkbenchLangImportConflict::class);
    expect(TranslationWorkbenchLangValue::sole()->value)->toBe('Draft');
});

it('stops the pipeline after a real lang import conflict before invoking the next command', function () {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['saved' => 'File value'];");
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'saved', 'translation_key' => 'ui.saved',
        'value' => 'Draft', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php',
    ]);
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $scan = new Command('translation-workbench:sync-foundation');
    $scan->setCode(fn () => 0);
    $application->addCommand($scan);
    $import = new ImportTranslationWorkbenchLangValues;
    $import->setLaravel(app());
    $application->addCommand($import);
    $nextCalled = false;
    $next = new Command('translation-workbench:discover-dynamic-options');
    $next->addOption('sync', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE);
    $next->setCode(function () use (&$nextCalled) {
        $nextCalled = true;

        return 0;
    });
    $application->addCommand($next);
    $output = new BufferedOutput;
    expect($runner->run(new ArrayInput(['--skip-foundation' => true]), $output))->toBe(1)
        ->and($nextCalled)->toBeFalse()
        ->and($output->fetch())->toContain('ui.saved', 'pipeline stopped')
        ->and(TranslationWorkbenchLangValue::sole()->value)->toBe('Draft');
});

it('preflights all export files before writing or pruning any file', function (bool $write) {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/aaa.php'), "<?php return ['saved' => 'Old', 'retired' => 'Retired'];");
    File::put(lang_path('en/zzz.php'), "<?php return ['parent' => 'Scalar'];");
    $before = [File::get(lang_path('en/aaa.php')), File::get(lang_path('en/zzz.php'))];
    foreach (['aaa.saved', 'bbb.new', 'zzz.parent.child'] as $key) {
        [$namespace, $langKey] = explode('.', $key, 2);
        TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', $key), 'translation_key' => $key, 'status' => 'open']);
        TranslationWorkbenchLangValue::create([
            'locale' => 'en', 'namespace' => $namespace, 'lang_key' => $langKey, 'translation_key' => $key,
            'value' => 'New value', 'value_type' => 'string', 'status' => 'active', 'source_path' => "lang/en/$namespace.php",
        ]);
    }
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'aaa', 'lang_key' => 'retired', 'translation_key' => 'aaa.retired',
        'value' => 'Retired', 'value_type' => 'string', 'status' => 'obsolete', 'source_path' => 'lang/en/aaa.php',
    ]);
    if ($write) {
        expect(fn () => app(TranslationWorkbenchLangFileExporter::class)->export(locales: ['en'], write: true))->toThrow(LogicException::class);
    }
    $report = app(TranslationWorkbenchLangFileExporter::class)->export(locales: ['en']);
    expect($report['values_conflicted'])->toBe(1)
        ->and($report['values_pruned'])->toBe(1)
        ->and($report['files_written'])->toBe(0)
        ->and($report['timeline_events_created'])->toBe(0)
        ->and(File::get(lang_path('en/aaa.php')))->toBe($before[0])
        ->and(File::get(lang_path('en/zzz.php')))->toBe($before[1])
        ->and(File::exists(lang_path('en/bbb.php')))->toBeFalse()
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
})->with([true, false]);

it('returns failure and reports the key and path for export conflicts', function (bool $write) {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['parent' => 'Scalar'];");
    TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'ui.parent.child'), 'translation_key' => 'ui.parent.child', 'status' => 'open']);
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'parent.child', 'translation_key' => 'ui.parent.child',
        'value' => 'New', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php',
    ]);
    expect(Artisan::call('translation-workbench:export-lang-files', ['--write' => $write]))->toBe(1);
    if ($write) {
        expect(Artisan::output())->toContain('--actor', 'No lang files were written');
        expect(File::get(lang_path('en/ui.php')))->toBe("<?php return ['parent' => 'Scalar'];")
            ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0);

        return;
    }
    expect(Artisan::output())->toContain('ui.parent.child', 'lang/en/ui.php', 'No lang files were written');
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-export-lang-files.json')), true, flags: JSON_THROW_ON_ERROR);
    expect($report['summary']['values_conflicted'])->toBe(1)->and($report['summary']['files_written'])->toBe(0);
})->with([true, false]);

it('stops the pipeline on export conflicts before planning source replacements', function (bool $write) {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['parent' => 'Scalar'];");
    TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'ui.parent.child'), 'translation_key' => 'ui.parent.child', 'status' => 'open']);
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'parent.child', 'translation_key' => 'ui.parent.child',
        'value' => 'New', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php',
    ]);
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $scan = new Command('translation-workbench:sync-foundation');
    $scan->setCode(fn () => 0);
    $application->addCommand($scan);
    $export = new \Gunreip\TranslationWorkbench\Console\ExportTranslationWorkbenchLangFiles;
    $export->setLaravel(app());
    $application->addCommand($export);
    $nextCalled = false;
    $next = new Command('translation-workbench:plan-code-updates');
    $next->setCode(function () use (&$nextCalled) {
        $nextCalled = true;

        return 0;
    });
    $application->addCommand($next);
    $options = [];
    foreach (array_keys(RunTranslationWorkbench::executionDefaults()) as $option) {
        if (str_starts_with($option, '--skip-')) {
            $options[$option] = true;
        }
    }
    $options['--skip-lang-file-export'] = false;
    $options['--skip-code-update-plan'] = false;
    $options['--write-lang-files'] = $write;
    $output = new BufferedOutput;
    expect($runner->run(new ArrayInput($options), $output))->toBe(1)
        ->and($nextCalled)->toBeFalse()
        ->and($output->fetch())->toContain(...($write ? ['separate guarded preview'] : ['ui.parent.child', 'pipeline stopped']))
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->count())->toBe(0);
})->with([true, false]);

it('rejects direct exporter writes before any filesystem operation', function () {
    TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'ui.new'), 'translation_key' => 'ui.new', 'status' => 'open']);
    TranslationWorkbenchLangValue::create([
        'locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'new', 'translation_key' => 'ui.new',
        'value' => 'New', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php',
    ]);
    File::partialMock()->shouldNotReceive('put');
    expect(fn () => app(TranslationWorkbenchLangFileExporter::class)->export(write: true))
        ->toThrow(LogicException::class, 'Direct lang-file writes are disabled');
    expect(TranslationWorkbenchTimelineEvent::count())->toBe(0);
});

it('reconciles only successfully scanned files including files with zero remaining calls', function (string $command) {
    File::put(base_path('resources/views/a.blade.php'), "{{ __('Alpha') }}");
    File::put(base_path('resources/views/b.blade.php'), "{{ __('Beta') }}");
    expect(Artisan::call($command, ['--paths' => 'resources/views']))->toBe(0);
    File::put(base_path('resources/views/a.blade.php'), '<p>No translation calls left</p>');
    expect(Artisan::call($command, ['--paths' => 'resources/views/a.blade.php', '--mark-obsolete' => true]))->toBe(0);
    expect(TranslationWorkbenchFinding::where('status', 'obsolete')->count())->toBe(1)
        ->and(TranslationWorkbenchFinding::where('status', 'active')->count())->toBe(1)
        ->and(TranslationWorkbenchKeyFinding::where('status', 'obsolete')->count())->toBe(1);
})->with(['translation-workbench:sync-foundation']);

it('leaves records unchanged when the requested scan path is absent or excluded', function (string $command, bool $excluded) {
    File::put(base_path('resources/views/a.blade.php'), "{{ __('Alpha') }}");
    Artisan::call($command, ['--paths' => 'resources/views']);
    $tables = ['translation_workbench_entries', 'translation_workbench_occurrences', 'translation_workbench_findings', 'translation_workbench_keys', 'translation_workbench_key_findings'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    if ($excluded) {
        config(['translation-workbench.exclude_paths' => ['resources/views']]);
    }
    Artisan::call($command, ['--paths' => $excluded ? 'resources/views' : 'resources/missing', '--mark-obsolete' => true]);
    expect($snapshot())->toBe($before);
})->with(['translation-workbench:sync-foundation'])->with([true, false]);

it('aborts before database changes when a later source file cannot be read', function (string $command) {
    File::put(base_path('resources/views/a.blade.php'), "{{ __('Alpha') }}");
    File::put(base_path('resources/views/b.blade.php'), "{{ __('Beta') }}");
    Artisan::call($command, ['--paths' => 'resources/views']);
    $tables = ['translation_workbench_entries', 'translation_workbench_occurrences', 'translation_workbench_findings', 'translation_workbench_keys', 'translation_workbench_key_findings'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    File::put(base_path('resources/views/a.blade.php'), '<p>Removed</p>');
    File::partialMock()->shouldReceive('get')->with(base_path('resources/views/b.blade.php'))->once()
        ->andThrow(new RuntimeException('Source cannot be read'));
    expect(fn () => Artisan::call($command, ['--paths' => 'resources/views', '--mark-obsolete' => true]))
        ->toThrow(RuntimeException::class, 'Source cannot be read');
    expect($snapshot())->toBe($before);
})->with(['translation-workbench:sync-foundation']);

it('requires an explicit successful file manifest for foundation removal reconciliation', function () {
    File::put(base_path('resources/views/a.blade.php'), "{{ __('Alpha') }}");
    $items = app(TranslationWorkbenchScanner::class)->scan(['resources/views']);
    expect(fn () => app(TranslationWorkbenchFoundationSyncer::class)->sync($items, markObsolete: true))
        ->toThrow(InvalidArgumentException::class, 'successfully scanned file manifest');
    expect(TranslationWorkbenchFinding::count())->toBe(0);
});

it('reconciles a deleted file only through a successfully scanned containing directory', function (string $command, string $scope) {
    File::put(base_path('resources/views/deleted.blade.php'), "{{ __('Deleted text') }}");
    Artisan::call($command, ['--paths' => 'resources/views']);
    File::delete(base_path('resources/views/deleted.blade.php'));
    Artisan::call($command, ['--paths' => $scope, '--mark-obsolete' => true]);
    $removed = $scope === 'resources/views';
    expect(TranslationWorkbenchFinding::sole()->status)->toBe($removed ? 'obsolete' : 'active');
})->with(['translation-workbench:sync-foundation'])
    ->with(['resources/views', 'resources/views/deleted.blade.php', 'resources/absent']);

it('preserves deleted findings outside the effective eligible directory scope', function (string $command, string $restriction) {
    File::ensureDirectoryExists(base_path('resources/views-neighbor'));
    File::put(base_path('resources/views/gone.blade.php'), "{{ __('Gone') }}");
    File::put(base_path('resources/views-neighbor/gone.blade.php'), "{{ __('Neighbor') }}");
    Artisan::call($command, ['--paths' => 'resources/views,resources/views-neighbor']);
    File::delete(base_path('resources/views/gone.blade.php'));
    File::delete(base_path('resources/views-neighbor/gone.blade.php'));
    if ($restriction === 'excluded') {
        config(['translation-workbench.exclude_paths' => ['resources/views']]);
    } elseif ($restriction === 'pattern') {
        config(['translation-workbench.file_patterns' => ['*.js']]);
    } else {
        config(['translation-workbench.ignored_filename_contains' => ['gone']]);
    }
    Artisan::call($command, ['--paths' => 'resources/views', '--mark-obsolete' => true]);
    expect(TranslationWorkbenchFinding::where('status', 'obsolete')->count())->toBe(0)
        ->and(\Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOccurrence::where('status', 'stale')->count())->toBe(0);
})->with(['translation-workbench:sync-foundation'])->with(['excluded', 'pattern', 'parked']);

it('keeps directory boundaries when reconciling deleted sources', function (string $command) {
    File::ensureDirectoryExists(base_path('resources/views-neighbor'));
    File::put(base_path('resources/views/gone.blade.php'), "{{ __('Gone') }}");
    File::put(base_path('resources/views-neighbor/neighbor.blade.php'), "{{ __('Neighbor') }}");
    Artisan::call($command, ['--paths' => 'resources/views,resources/views-neighbor']);
    File::delete(base_path('resources/views/gone.blade.php'));
    File::delete(base_path('resources/views-neighbor/neighbor.blade.php'));
    Artisan::call($command, ['--paths' => 'resources/views', '--mark-obsolete' => true]);
    $rows = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')->pluck('f.status', 's.path');
    expect($rows['resources/views/gone.blade.php'])->toBe('obsolete')
        ->and($rows['resources/views-neighbor/neighbor.blade.php'])->toBe('active');
})->with(['translation-workbench:sync-foundation']);

it('does not reconcile deleted sources after a directory scan read failure', function (string $command) {
    File::put(base_path('resources/views/gone.blade.php'), "{{ __('Gone') }}");
    File::put(base_path('resources/views/blocked.blade.php'), "{{ __('Blocked') }}");
    Artisan::call($command, ['--paths' => 'resources/views']);
    File::delete(base_path('resources/views/gone.blade.php'));
    File::partialMock()->shouldReceive('get')->with(base_path('resources/views/blocked.blade.php'))->once()
        ->andThrow(new RuntimeException('Read failed'));
    expect(fn () => Artisan::call($command, ['--paths' => 'resources/views', '--mark-obsolete' => true]))->toThrow(RuntimeException::class, 'Read failed');
    expect(TranslationWorkbenchFinding::where('status', 'obsolete')->count())->toBe(0)
        ->and(\Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOccurrence::where('status', 'stale')->count())->toBe(0);
})->with(['translation-workbench:sync-foundation']);

it('shares one persisted scan across consumers and creates a fresh state after source writes', function () {
    $file = base_path('resources/views/observed.blade.php');
    $source = "{{ __('Observed literal') }}";
    File::put($file, $source);
    File::put(base_path('resources/views/empty.blade.php'), '<p>Empty</p>');
    $session = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession::class);
    $session->begin(['resources/views'], ['resources/views/deleted.blade.php']);
    try {
        $first = app(TranslationWorkbenchScanner::class)->scanWithManifest();
        File::put($file, "{{ __('Changed literal') }}");
        $again = app(TranslationWorkbenchScanner::class)->scanWithManifest(['resources/views']);
        expect($again['scan_run']['id'])->toBe($first['scan_run']['id'])
            ->and($again['items']->first()->literalText)->toBe('Observed literal')
            ->and($again['scanned_paths'])->toContain('resources/views/empty.blade.php')
            ->and($again['missing_paths'])->toBe(['resources/views/deleted.blade.php'])
            ->and($again['file_hashes']['resources/views/observed.blade.php'])->toBe(hash('sha256', $source));
        $stored = File::get($first['scan_run']['path']);
        expect(json_decode($stored, true)['result']['items'][0]['literalText'])->toBe('Observed literal');
        $again['items']->pop();
        expect(app(TranslationWorkbenchScanner::class)->scanFile($file))->toHaveCount(1);
        expect(fn () => app(TranslationWorkbenchScanner::class)->scan(['app']))
            ->toThrow(RuntimeException::class, 'Scan scope differs');
        $session->nextPhase('after-source-write');
        $fresh = app(TranslationWorkbenchScanner::class)->scanWithManifest();
        expect($fresh['scan_run']['id'])->not->toBe($first['scan_run']['id'])
            ->and($fresh['items']->first()->literalText)->toBe('Changed literal')
            ->and($session->references())->toHaveCount(2)
            ->and(File::get($first['scan_run']['path']))->toBe($stored);
    } finally {
        $session->end();
    }
    expect($session->manifest())->toBeNull()->and($session->active())->toBeFalse();
});

it('uses the same scan id for pipeline scan foundation and reports without rescanning files', function () {
    $file = base_path('resources/views/selected.blade.php');
    $source = "{{ __('Shared observation') }}";
    File::put($file, $source);
    File::put(base_path('resources/views/outside.blade.php'), "{{ __('Outside scope') }}");
    File::partialMock()->shouldReceive('get')->with($file)->once()->andReturn($source);
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    foreach ([\Gunreip\TranslationWorkbench\Console\SyncTranslationWorkbenchFoundation::class] as $class) {
        $command = new $class;
        $command->setLaravel(app());
        $application->addCommand($command);
    }
    $options = ['--paths' => 'resources/views/selected.blade.php'];
    foreach (array_keys(RunTranslationWorkbench::executionDefaults()) as $option) {
        if (str_starts_with($option, '--skip-')) {
            $options[$option] = true;
        }
    }
    $options['--skip-foundation'] = false;
    $output = new BufferedOutput;
    expect($runner->run(new ArrayInput($options), $output))->toBe(0)
        ->and(TranslationWorkbenchFinding::sole()->literal_text)->toBe('Shared observation');
    $ids = [];
    foreach (['translation-workbench-sync-foundation', 'translation-workbench'] as $name) {
        $report = json_decode(File::get(storage_path('translation-workbench/'.$name.'.json')), true);
        expect($report['raw_data']['files'])->toBe(1)
            ->and($report['raw_data']['found'])->toBe(1)
            ->and($report['scan_runs'])->toHaveCount(1);
        $ids[] = $report['scan_runs'][0]['id'];
    }
    expect(array_unique($ids))->toHaveCount(1)
        ->and(File::files(storage_path('translation-workbench/scan-runs')))->toHaveCount(1)
        ->and(app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession::class)->active())->toBeFalse();
});

it('records a failed scan once and does not retry it while producing a pipeline failure report', function () {
    $file = base_path('resources/views/broken.blade.php');
    File::put($file, "{{ __('Unreadable') }}");
    File::partialMock()->shouldReceive('get')->with($file)->once()->andThrow(new RuntimeException('Source cannot be read'));
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $command = new \Gunreip\TranslationWorkbench\Console\SyncTranslationWorkbenchFoundation;
    $command->setLaravel(app());
    $application->addCommand($command);
    $output = new BufferedOutput;
    expect($runner->run(new ArrayInput(['--paths' => 'resources/views']), $output))->toBe(1)
        ->and($output->fetch())->toContain('Source cannot be read')
        ->and(TranslationWorkbenchFinding::count())->toBe(0);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench.json')), true);
    expect($report['scan_runs'])->toHaveCount(1)
        ->and($report['scan_runs'][0]['status'])->toBe('failed')
        ->and($report['raw_data']['files'])->toBe(0)
        ->and(app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession::class)->active())->toBeFalse();
});

it('invalidates the pipeline snapshot after a source writer before the complete refresh', function () {
    $file = base_path('resources/views/change.blade.php');
    File::put($file, "{{ __('Before writing') }}");
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $scan = new \Gunreip\TranslationWorkbench\Console\SyncTranslationWorkbenchFoundation;
    $scan->setLaravel(app());
    $application->addCommand($scan);
    $plan = new Command('translation-workbench:plan-code-updates');
    $plan->addOption('paths', null, \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED);
    $plan->setCode(fn () => 0);
    $application->addCommand($plan);
    $writer = new Command('translation-workbench:apply-code-updates');
    $writer->addOption('paths', null, \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED);
    $writer->addOption('write');
    $writer->addOption('suppress-dry-run-warning');
    $writer->setCode(function (\Symfony\Component\Console\Input\InputInterface $input) use ($file) {
        if ($input->getOption('write')) {
            File::put($file, "{{ __('After writing') }}");
        }
        return 0;
    });
    $application->addCommand($writer);
    $options = ['--complete' => true, '--paths' => 'resources/views'];
    foreach (array_keys(RunTranslationWorkbench::executionDefaults()) as $option) {
        if (str_starts_with($option, '--skip-')) {
            $options[$option] = true;
        }
    }
    $options['--skip-foundation'] = false;
    $options['--skip-code-update-plan'] = false;
    $options['--skip-code-update-apply'] = false;
    $output = new BufferedOutput;
    $exit = $runner->run(new ArrayInput($options), $output);
    expect($exit)->toBe(0, $output->fetch());
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench.json')), true);
    expect($report['scan_runs'])->toHaveCount(2)
        ->and($report['scan_runs'][1]['phase'])->toStartWith('after-source-write-')
        ->and($report['raw_data']['files_found'][0]['findings'][0]['literal_text'])->toBe('After writing');
    $first = json_decode(File::get($report['scan_runs'][0]['path']), true);
    expect($first['result']['items'][0]['literalText'])->toBe('Before writing');
});

it('blocks database consumers when the common scan snapshot cannot be persisted', function () {
    File::put(base_path('resources/views/source.blade.php'), "{{ __('Must not be synchronized') }}");
    File::partialMock()->shouldReceive('put')->withArgs(fn ($path, $contents) => str_ends_with($path, '.json.tmp'))->twice()->andReturnFalse();
    $session = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession::class);
    $session->begin(['resources/views'], []);
    try {
        expect(fn () => Artisan::call('translation-workbench:sync-foundation', ['--paths' => 'resources/views']))
            ->toThrow(RuntimeException::class, 'Cannot persist translation scan snapshot:');
        expect(TranslationWorkbenchFinding::count())->toBe(0)
            ->and($session->failed())->toBeTrue()
            ->and($session->references()[0]['storage_error'])->toContain('Cannot persist');
        expect(fn () => app(TranslationWorkbenchScanner::class)->scan())
            ->toThrow(RuntimeException::class, 'Cannot persist translation scan snapshot:');
    } finally {
        $session->end();
    }
});

it('captures edits and exports through foundation without legacy workbench tables', function (bool $dynamic) {
    // This is the isolated SQLite fixture, never the configured project database.
    Schema::withoutForeignKeyConstraints(function () {
        foreach (['option_discoveries', 'duplicate_candidates', 'dynamic_values', 'values', 'events', 'occurrences', 'entries'] as $suffix) {
            Schema::drop('translation_workbench_'.$suffix);
        }
    });
    $editor = $dynamic ? workbenchDynamicEditor() : workbenchValueEditor();
    $key = TranslationWorkbenchKey::sole();
    if ($dynamic) {
        $key->update(['is_dynamic_key' => true, 'is_dynamic_multi' => true]);
        $editor->saveDynamicMultiTranslationValues();
        expect(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->pluck('value', 'value_key')->all())
            ->toBe(['draft' => 'Entwurf', 'published' => 'Veröffentlicht']);
    } else {
        $editor->saveTranslationValue();
        expect(TranslationWorkbenchLangValue::where('locale', 'de')->sole()->value)->toBe('Willkommen');
    }
    $result = publishReviewedFixture($key->id, TranslationWorkbenchFinding::sole()->id, ['de'], $dynamic);
    expect($result['files_written'])->toBe(1)
        ->and(TranslationWorkbenchFinding::count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', $dynamic ? 'dynamic_multi_values_saved' : 'translation_values_saved')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->count())->toBe($dynamic ? 2 : 1);
})->with([false, true]);

it('stores unresolved option observations without creating legacy records', function (bool $sync) {
    $oldAppPath = app_path();
    app()->useAppPath(base_path('app'));
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/Options.php'), <<<'CODE'
<?php
class Options {
    public array $statusOptions = ['draft' => 'Draft', 'published' => 'Published'];
}
CODE);
    File::put(base_path('resources/views/options.blade.php'), <<<'BLADE'
@foreach ($statusOptions as $value => $label)
    <option>{{ $label }}</option>
@endforeach
@foreach ($unknownOptions as $value => $label)
    <option>{{ __($label) }}</option>
@endforeach
BLADE);
    try {
        $arguments = ['--paths' => 'resources/views/options.blade.php', '--sync' => true, '--dry-run' => ! $sync];
        expect(Artisan::call('translation-workbench:discover-dynamic-options', $arguments))->toBe(0)
            ->and(Artisan::output())->toContain('Discoveries: 2');
        $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-options.json')), true);
        expect($report['plan_summary']['foundation_assignment']['mode'])->toBe($sync ? 'persisted_observations' : 'read_only_preflight')
            ->and($report['plan_summary']['foundation_assignment']['summary']['missing_finding'])->toBe(2);
        expect(DB::table('translation_workbench_entries')->count())->toBe(0)
            ->and(DB::table('translation_workbench_option_discoveries')->count())->toBe($sync ? 2 : 0)
            ->and(DB::table('translation_workbench_values')->count())->toBe(0)
            ->and(DB::table('translation_workbench_dynamic_values')->count())->toBe(0);
        if ($sync) {
            $rows = DB::table('translation_workbench_option_discoveries')->orderBy('source_line')->get();
            expect(json_decode($rows[0]->options, true))->toBe(['draft' => 'Draft', 'published' => 'Published'])
                ->and($rows[0]->label_usage)->toBe('plain_label')
                ->and($rows[0]->matched_entry_id)->toBeNull()
                ->and($rows[0]->status)->toBe('needs_review')
                ->and($rows[1]->source_type)->toBe('unresolved')
                ->and($rows[1]->label_usage)->toBe('translated_label');
            expect(Artisan::call('translation-workbench:discover-dynamic-options', $arguments))->toBe(0)
                ->and(DB::table('translation_workbench_option_discoveries')->count())->toBe(2)
                ->and(DB::table('translation_workbench_entries')->count())->toBe(0)
                ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_option_observation_recorded')->count())->toBe(2);
        }
    } finally {
        app()->useAppPath($oldAppPath);
    }
})->with([false, true]);

class WorkbenchDiscoveryProviderFixture
{
    public function options(): \Illuminate\Support\Collection
    {
        return collect(['ready' => ['label' => 'Ready'], 'waiting' => 'Waiting', 'ignored' => ['other' => 'Ignored']]);
    }
}

it('collects provider options without workbench database access and respects the requested view file', function () {
    $oldAppPath = app_path();
    app()->useAppPath(base_path('app'));
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/ProviderView.php'), <<<'CODE'
<?php
use WorkbenchDiscoveryProviderFixture;
class ProviderView {
    public function render(WorkbenchDiscoveryProviderFixture $provider) {
        return view('options', ['providerOptions' => $provider->options()]);
    }
}
CODE);
    $view = <<<'BLADE'
@foreach ($providerOptions as $key => $label)
    <option>{{ dynamic_label('states', $key, $label) }}</option>
@endforeach
BLADE;
    File::put(base_path('resources/views/provider.blade.php'), $view);
    File::put(base_path('resources/views/outside.blade.php'), $view);
    try {
        DB::enableQueryLog();
        DB::flushQueryLog();
        $rows = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)
            ->collect(['resources/views/provider.blade.php']);
        expect(DB::getQueryLog())->toBe([])
            ->and($rows)->toHaveCount(1)
            ->and($rows[0]['options'])->toBe(['ready' => 'Ready', 'waiting' => 'Waiting'])
            ->and($rows[0]['source_reference'])->toBe('app/Livewire/ProviderView.php:options')
            ->and($rows[0]['source_path'])->toBe('resources/views/provider.blade.php')
            ->and($rows[0]['label_usage'])->toBe('dynamic_label');
    } finally {
        DB::disableQueryLog();
        app()->useAppPath($oldAppPath);
    }
});


it('preflights dynamic option assignments by source context without changing foundation data', function (string $scenario, string $expected) {
    $source = <<<'BLADE'
@foreach ($statusOptions as $key => $label)
{{ __($label) }}
@endforeach
BLADE;
    if ($scenario === 'two_findings') {
        $source = str_replace('@endforeach', "{{ __(\$label) }}\n@endforeach", $source);
    }
    File::put(base_path('resources/views/assignment.blade.php'), $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::firstOrFail();
    $key = TranslationWorkbenchKey::firstOrFail();
    $discovery = [
        'source_path' => 'resources/views/assignment.blade.php', 'source_line' => 1, 'source_end_line' => 4,
        'options_variable' => 'statusOptions', 'key_variable' => 'key', 'label_variable' => 'label',
        'suggested_key' => 'unrelated.proposed.key',
    ];
    if ($scenario === 'other_file') { $discovery['source_path'] = 'resources/views/other.blade.php'; }
    if ($scenario === 'other_loop') { $discovery['source_line'] = 8; $discovery['source_end_line'] = 10; }
    if ($scenario === 'other_variable') { $discovery['options_variable'] = 'unrelatedOptions'; }
    if ($scenario === 'obsolete') { $finding->update(['status' => 'obsolete']); }
    if ($scenario === 'inactive_link') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    if ($scenario === 'obsolete_key') { $key->update(['status' => 'obsolete']); }
    if ($scenario === 'two_keys') {
        $other = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'other-assignment'), 'translation_key' => 'ui.other', 'status' => 'open']);
        $link = (array) DB::table('translation_workbench_key_findings')->first();
        unset($link['id']);
        $link['key_id'] = $other->id;
        DB::table('translation_workbench_key_findings')->insert($link);
    }
    $historyCount = TranslationWorkbenchTimelineEvent::count();
    DB::enableQueryLog();
    DB::flushQueryLog();
    try {
        $plan = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionAssignment::class)->plan(collect([$discovery]));
        expect($plan['summary'][$expected])->toBe(1)
            ->and($plan['assignments'][0]['status'])->toBe($expected)
            ->and($plan['assignments'][0]['finding_id'])->toBe($expected === 'matched' ? $finding->id : null)
            ->and($plan['assignments'][0]['key_id'])->toBe($expected === 'matched' ? $key->id : null);
        foreach (DB::getQueryLog() as $query) {
            expect(strtolower(ltrim($query['query'])))->toStartWith('select');
        }
        expect(TranslationWorkbenchTimelineEvent::count())->toBe($historyCount);
    } finally {
        DB::disableQueryLog();
    }
})->with([
    ['matched', 'matched'], ['other_file', 'missing_finding'], ['other_loop', 'missing_finding'],
    ['other_variable', 'missing_finding'], ['obsolete', 'missing_finding'],
    ['inactive_link', 'missing_key'], ['obsolete_key', 'missing_key'],
    ['two_keys', 'ambiguous_key'], ['two_findings', 'ambiguous_finding'],
]);

function workbenchOptionObservation(): array
{
    return [
        'scope' => 'status', 'suggested_key' => 'options.status', 'suggested_dynamic_key' => 'options.status',
        'source_path' => 'resources/views/options.blade.php', 'source_line' => 1, 'source_end_line' => 3,
        'options_variable' => 'statusOptions', 'key_variable' => 'key', 'label_variable' => 'label',
        'label_usage' => 'translated_label', 'source_type' => 'hardcoded_public_array',
        'options' => ['draft' => 'Draft', 'published' => 'Published'],
    ];
}

it('feeds Foundation classification from an exact observation without touching edited translations', function () {
    File::put(base_path('resources/views/options.blade.php'), <<<'BLADE'
@foreach ($statusOptions as $key => $label)
{{ __($label) }}
@endforeach
BLADE);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $key = TranslationWorkbenchKey::sole();
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => $key->id, 'value_key' => 'draft', 'locale' => 'de', 'value' => 'Geprüfter Entwurf',
        'native_label' => 'Original', 'status' => 'active', 'source' => 'editor',
    ]);
    $before = DB::table('translation_workbench_dynamic_key_values')->get()->map(fn ($row) => (array) $row)->all();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $result = $service->process(collect([workbenchOptionObservation()]), true);
    expect($result['plan']['summary']['matched'])->toBe(1)
        ->and(DB::table('translation_workbench_entries')->count())->toBe(0)
        ->and(DB::table('translation_workbench_values')->count())->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_values')->count())->toBe(0);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    expect(DB::table('translation_workbench_dynamic_sources')->where('finding_id', TranslationWorkbenchFinding::sole()->id)->where('classification', 'multi_hardcoded')->count())->toBe(1)
        ->and(DB::table('translation_workbench_dynamic_source_values')->count())->toBe(2)
        ->and(DB::table('translation_workbench_dynamic_key_values')->get()->map(fn ($row) => (array) $row)->all())->toBe($before);
    $again = $service->process(collect([workbenchOptionObservation()]), true);
    expect($again['summary']['observations_unchanged'])->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_option_observation_recorded')->count())->toBe(1);
    $row = DB::table('translation_workbench_option_discoveries')->sole();
    expect(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::appliesTo($row, TranslationWorkbenchFinding::sole()->id, $key->id + 1))->toBeFalse();
});

it('prevents ambiguous observations from becoming structured sources through broad key matches', function () {
    File::put(base_path('resources/views/options.blade.php'), <<<'BLADE'
@foreach ($statusOptions as $key => $label)
{{ __($label) }}
{{ __($label) }}
@endforeach
BLADE);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $observation = workbenchOptionObservation();
    $observation['source_end_line'] = 4;
    $observation['suggested_key'] = TranslationWorkbenchKey::firstOrFail()->suggested_key;
    $result = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)->process(collect([$observation]), true);
    expect($result['plan']['summary']['ambiguous_finding'])->toBe(1)
        ->and(DB::table('translation_workbench_option_discoveries')->sole()->status)->toBe('needs_review');
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(Artisan::call('translation-workbench:resolve-unknown-dynamic-sources'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_sources')->where('classification', '<>', 'unknown')->count())->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_source_values')->count())->toBe(0);
});

it('rolls back option observations and their history when a later observation cannot be stored', function () {
    DB::statement("CREATE TRIGGER reject_second_observation BEFORE INSERT ON translation_workbench_option_discoveries
        WHEN NEW.source_path = 'resources/views/second.blade.php' BEGIN SELECT RAISE(ABORT, 'Observation storage failure'); END");
    $first = workbenchOptionObservation();
    $second = [...$first, 'source_path' => 'resources/views/second.blade.php'];
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)->process(collect([$first, $second]), true))
        ->toThrow(\Illuminate\Database\QueryException::class, 'Observation storage failure');
    expect(DB::table('translation_workbench_option_discoveries')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
});

it('runs foundation capture without legacy tables and excludes legacy refresh writers', function () {
    Schema::withoutForeignKeyConstraints(function () {
        foreach (['duplicate_candidates', 'dynamic_values', 'values', 'events', 'occurrences', 'entries'] as $suffix) {
            Schema::drop('translation_workbench_'.$suffix);
        }
    });
    File::ensureDirectoryExists(base_path('resources/views'));
    File::put(base_path('resources/views/capture.blade.php'), "{{ __('Capture this literal') }}");
    $options = ['--paths' => 'resources/views'];
    foreach (array_keys(RunTranslationWorkbench::executionDefaults()) as $option) {
        if (str_starts_with($option, '--skip-')) {
            $options[$option] = true;
        }
    }
    $options['--skip-foundation'] = false;
    expect(Artisan::call('translation:workbench', $options))->toBe(0)
        ->and(TranslationWorkbenchFinding::count())->toBe(1);
    // Refresh planning uses the same Foundation-only boundary after source writes.
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $input = new ArrayInput([]);
    $input->bind($runner->getDefinition());
    $runner->setInput($input);
    $steps = (new ReflectionMethod($runner, 'completeRefreshSteps'))->invoke($runner, null, 'en', true);
    $commands = array_column($steps, 'command');
    expect($commands)->toContain('translation-workbench:sync-foundation')
        ->not->toContain('translation-workbench:scan', 'translation-workbench:import-existing', 'translation-workbench:detect-duplicates');
});

it('compares historical package values without treating suggested keys as assignments or writing rows', function (string $case, string $expected) {
    $entry = DB::table('translation_workbench_entries')->insertGetId([
        'fingerprint' => hash('sha256', 'history'), 'source_signature' => hash('sha256', 'history-source'),
        'kind' => 'literal', 'source_type' => 'blade', 'source_path' => 'resources/views/history.blade.php',
        'translation_key' => $case === 'suggested' ? null : 'ui.history',
        'suggested_key' => 'ui.history', 'existing_key' => $case === 'conflicting' ? 'ui.other' : null,
    ]);
    $dynamic = $case === 'dynamic';
    if ($dynamic) {
        DB::table('translation_workbench_dynamic_values')->insert([
            'entry_id' => $entry, 'value_key' => 'draft', 'locale' => 'de_AT', 'value' => 'Entwurf',
        ]);
    } else {
        DB::table('translation_workbench_values')->insert([
            'entry_id' => $entry, 'value_key' => $case === 'observation' ? 'draft' : 'source:de_AT',
            'native_label' => $case === 'null' ? null : 'Entwurf',
        ]);
    }
    $key = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'history-key'), 'translation_key' => 'ui.history', 'status' => 'open']);
    if ($case !== 'missing') {
        if ($dynamic) {
            DB::table('translation_workbench_dynamic_key_values')->insert([
                'key_id' => $key->id, 'value_key' => 'draft', 'locale' => 'de-at', 'value' => 'Entwurf',
            ]);
        } else {
            TranslationWorkbenchLangValue::create([
                'translation_key' => 'ui.history', 'locale' => 'de-at', 'namespace' => 'ui', 'lang_key' => 'history',
                'source_path' => 'lang/de-at/ui.php', 'value' => $case === 'different' ? 'Bearbeitet' : ($case === 'null' ? '' : 'Entwurf'),
            ]);
            if ($case === 'multiple') {
                TranslationWorkbenchLangValue::create([
                    'translation_key' => 'ui.history', 'locale' => 'de_AT', 'namespace' => 'ui', 'lang_key' => 'history',
                    'source_path' => 'lang/de_AT/ui.php', 'value' => 'Entwurf',
                ]);
            }
        }
    }
    DB::enableQueryLog();
    DB::flushQueryLog();
    expect(Artisan::call('translation-workbench:compare-values', ['--package-history' => true, '--json' => true]))->toBe(0);
    $report = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);
    expect($report['counts'])->toBe([$expected => 1])
        ->and($report['comparisons'][0]['row']['entry_id'])->toBe($entry);
    foreach (DB::getQueryLog() as $query) {
        expect(strtolower(ltrim($query['query'])))->not->toMatch('/^(insert|update|delete|replace|truncate)\b/');
    }
    DB::disableQueryLog();
})->with([
    ['identical', 'identical'], ['dynamic', 'identical'], ['different', 'different'],
    ['null', 'different'], ['missing', 'legacy_only'], ['suggested', 'unassigned_key'],
    ['conflicting', 'conflicting_keys'], ['observation', 'observation_without_locale'], ['multiple', 'ambiguous'],
]);

it('does not register or execute retired legacy writer commands', function (string $name) {
    expect(Artisan::all())->not->toHaveKey($name)
        ->toHaveKeys(['translation-workbench:sync-foundation', 'translation-workbench:import-lang-values', 'translation-workbench:discover-dynamic-options']);
    DB::enableQueryLog();
    DB::flushQueryLog();
    expect(fn () => Artisan::call($name))->toThrow(\Symfony\Component\Console\Exception\CommandNotFoundException::class);
    expect(DB::getQueryLog())->toBe([]);
    DB::disableQueryLog();
})->with(['translation-workbench:scan', 'translation-workbench:import-existing', 'translation-workbench:detect-duplicates']);

it('cleans only literal excluded paths in foundation while retaining historical entries and edited values', function (bool $apply, string $segment) {
    $excluded = 'resources/views/'.$segment;
    $neighbor = 'resources/views/'.str_replace(['_', '%', '!'], ['x', 'x', 'x'], $segment);
    foreach ([$excluded, $neighbor] as $dir) {
        File::ensureDirectoryExists(base_path($dir));
        File::put(base_path($dir.'/sample.blade.php'), "{{ __('Keep the translation') }}");
    }
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(TranslationWorkbenchFinding::count())->toBe(2);
    $legacyId = DB::table('translation_workbench_entries')->insertGetId([
        'fingerprint' => hash('sha256', 'excluded-history'), 'source_signature' => hash('sha256', 'excluded-history'),
        'kind' => 'literal', 'source_type' => 'blade', 'source_path' => $excluded.'/sample.blade.php',
        'status' => 'open',
    ]);
    TranslationWorkbenchLangValue::create([
        'translation_key' => 'ui.preserved', 'locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'preserved',
        'source_path' => 'lang/de/ui.php', 'value' => 'Bearbeitet', 'status' => 'active',
    ]);
    foreach ([$excluded, $neighbor] as $dir) {
        DB::table('translation_workbench_option_discoveries')->insert([
            'fingerprint' => hash('sha256', $dir), 'scope' => 'test', 'source_path' => $dir.'/sample.blade.php',
            'options_variable' => '$options', 'status' => 'open',
        ]);
    }
    $before = DB::table('translation_workbench_entries')->where('id', $legacyId)->first();
    $valueBefore = TranslationWorkbenchLangValue::sole()->toArray();
    config(['translation-workbench.exclude_paths' => [$excluded]]);
    expect(Artisan::call('translation-workbench:cleanup-excluded-paths', ['--apply' => $apply, '--force' => $apply]))->toBe(0);
    $rows = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')->pluck('f.status', 's.path');
    expect($rows[$excluded.'/sample.blade.php'])->toBe($apply ? 'obsolete' : 'active')
        ->and($rows[$neighbor.'/sample.blade.php'])->toBe('active')
        ->and(TranslationWorkbenchKeyFinding::where('status', 'obsolete')->count())->toBe($apply ? 1 : 0)
        ->and(DB::table('translation_workbench_option_discoveries')->where('source_path', $excluded.'/sample.blade.php')->value('status'))->toBe($apply ? 'obsolete' : 'open')
        ->and(DB::table('translation_workbench_option_discoveries')->where('source_path', $neighbor.'/sample.blade.php')->value('status'))->toBe('open')
        ->and(DB::table('translation_workbench_entries')->where('id', $legacyId)->first())->toEqual($before)
        ->and(TranslationWorkbenchLangValue::sole()->toArray())->toBe($valueBefore);
})->with([false, true])->with(['_retired', '%retired', '!retired']);

it('assigns explicit option calls only to their loop variables and linked owner', function (string $variant, string $status) {
    $call = '\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label';
    $argument = $variant === 'wrong_key_variable' ? '$other' : '(string) $value';
    $fallback = $variant === 'transformed' ? 'strtoupper($label)' : '$label';
    $source = "@foreach (\$statusOptions as \$value => \$label)\n{{ $call('ui.status', $argument, $fallback) }}\n";
    if ($variant === 'multiple') {
        $source .= "{{ $call('ui.status', (string) \$value, \$label) }}\n";
    }
    $source .= '@endforeach';
    File::put(base_path('resources/views/explicit-options.blade.php'), $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    if ($variant === 'wrong_owner') {
        TranslationWorkbenchKey::query()->update(['translation_key' => 'ui.other']);
    }
    $observation = workbenchOptionObservation();
    $observation['source_path'] = 'resources/views/explicit-options.blade.php';
    $observation['source_line'] = 1;
    $observation['source_end_line'] = 5;
    $observation['key_variable'] = 'value';
    $plan = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionAssignment::class)->plan(collect([$observation]));
    expect($plan['assignments'][0]['status'])->toBe($status);
    if ($variant === 'valid') {
        File::ensureDirectoryExists(app_path('Livewire'));
        $discovered = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collect(['resources/views/explicit-options.blade.php']);
        expect($discovered)->toHaveCount(1)
            ->and($discovered->first()['label_usage'])->toBe('workbench_option_label');
        $actualPlan = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionAssignment::class)->plan($discovered);
        expect($actualPlan['assignments'][0]['status'])->toBe('matched');
    }
})->with([
    ['valid', 'matched'], ['wrong_key_variable', 'missing_finding'], ['transformed', 'missing_finding'],
    ['multiple', 'ambiguous_finding'], ['wrong_owner', 'owner_key_mismatch'],
]);

it('captures indexed option calls outside loops without guessing complex expressions', function (string $fallback, bool $supported) {
    File::ensureDirectoryExists(app_path('Livewire'));
    $call = '\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label';
    File::put(base_path('resources/views/indexed.blade.php'), "{{ $call('ui.status', (string) \$selected, $fallback) }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $observations = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collect(['resources/views/indexed.blade.php']);
    expect($observations)->toHaveCount($supported ? 1 : 0);
    if (! $supported) {
        expect(TranslationWorkbenchFinding::sole()->meta['dynamic_option_context'])->toBeNull();
        return;
    }
    $plan = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionAssignment::class)->plan($observations);
    expect($plan['assignments'][0]['status'])->toBe('matched')
        ->and($observations->first()['options_variable'])->toBe('statusOptions')
        ->and($observations->first()['key_variable'])->toBe('selected');
    $result = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)->process($observations, true);
    expect($result['plan']['assignments'][0]['status'])->toBe('matched');
})->with([
    ['$statusOptions[$selected] ?? $emptyValue', true], ['$statusOptions[$selected]', true],
    ['$statusOptions[$other] ?? $emptyValue', false], ['strtoupper($statusOptions[$selected])', false],
]);

it('keeps same-named option sources separate and blocks ambiguous consumption', function (string $secondSource) {
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/First.php'), <<<'CODE'
<?php
class First {
    public array $statusOptions = [
        'draft' => 'Draft',
    ];
}
CODE);
    File::put(base_path('resources/views/source-ambiguity.blade.php'), <<<'BLADE'
@foreach ($statusOptions as $key => $label)
{{ \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label('ui.status', (string) $key, $label) }}
@endforeach
BLADE);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $collector = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class);
    $syncer = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $first = $collector->collect(['resources/views/source-ambiguity.blade.php']);
    expect($syncer->process($first, true)['plan']['assignments'][0]['status'])->toBe('matched');
    File::put(app_path('Livewire/Second.php'), $secondSource);
    $ambiguous = $collector->collect(['resources/views/source-ambiguity.blade.php']);
    expect($ambiguous->sole()['options'])->toBe([])
        ->and($ambiguous->sole()['source_candidates'])->toHaveCount(2);
    $result = $syncer->process($ambiguous, true);
    $row = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::sole();
    expect($result['plan']['assignments'][0]['status'])->toBe('ambiguous_source')
        ->and($row->status)->toBe('needs_review')
        ->and($row->meta['options_before_source_ambiguity']['options'])->toBe(['draft' => 'Draft'])
        ->and(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::appliesTo(
            $row, TranslationWorkbenchFinding::sole()->id, TranslationWorkbenchKey::sole()->id,
        ))->toBeFalse();
})->with([
    "<?php class Second {\n public array \$statusOptions = [\n 'draft' => 'Other',\n ];\n}",
    "<?php class Second {\n public array \$statusOptions = [\n 'draft' => 'Draft',\n ];\n}",
    "<?php\nuse WorkbenchDiscoveryProviderFixture;\nclass Second {\n public function render(WorkbenchDiscoveryProviderFixture \$provider) {\n return view('other', ['statusOptions' => \$provider->options()]);\n }\n}",
]);

it('does not use archived option providers as active source candidates', function (string $archive) {
    File::ensureDirectoryExists(app_path('Livewire/'.$archive));
    $source = "<?php class Options {\n public array \$statusOptions = [\n 'draft' => 'Draft',\n ];\n}";
    File::put(app_path('Livewire/Current.php'), $source);
    File::put(app_path('Livewire/'.$archive.'/Historical.php'), $source);
    File::put(base_path('resources/views/archive-options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    $rows = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collect(['resources/views/archive-options.blade.php']);
    expect($rows->sole()['source_candidates'])->toHaveCount(1)
        ->and($rows->sole()['source_resolution'])->toBe('single_candidate');
})->with(['_old', '-old']);

it('reports static option view paths without inferring dynamic or overridden includes', function (string $include, string $expected) {
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/Binding.php'), "<?php class Binding { public function render() { return view('root'); } }");
    File::put(base_path('resources/views/root.blade.php'), $include);
    File::put(base_path('resources/views/child.blade.php'), "@include('root')\n<p>Child</p>");
    $report = (new \Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchOptionViewBinding)
        ->inspect('app/Livewire/Binding.php:options', 'resources/views/child.blade.php');
    expect($report['status'])->toBe($expected);
    if ($expected === 'static_view_path') {
        expect($report['paths'])->toBe([['app/Livewire/Binding.php', 'resources/views/root.blade.php', 'resources/views/child.blade.php']]);
    }
    $cycle = (new \Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchOptionViewBinding)
        ->inspect('app/Livewire/Binding.php', 'resources/views/absent.blade.php');
    expect($cycle['status'])->toBe('unverified_view_binding');
})->with([
    ["@include('child')", 'static_view_path'], ["@includeIf('child')", 'static_view_path'],
    ["@include('child', ['options' => []])", 'unverified_view_binding'],
    ['@include($view)', 'unverified_view_binding'],
    ["{{-- @include('child') --}}", 'unverified_view_binding'],
]);

it('reports possible option variable overrides without silently selecting a source', function (string $body, string $expected, ?string $reason) {
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/Flow.php'), "<?php class Flow { public array \$statusOptions = []; public function render() { return view('flow-root'); } }");
    File::put(base_path('resources/views/flow-root.blade.php'), "@include('flow-child')");
    File::put(base_path('resources/views/flow-child.blade.php'), $body);
    $report = (new \Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchOptionViewBinding)
        ->inspect('app/Livewire/Flow.php', 'resources/views/flow-child.blade.php', 'statusOptions');
    expect($report['variable_flow']['status'])->toBe($expected);
    if ($reason !== null) {
        expect(array_column($report['variable_flow']['findings'], 'reason'))->toContain($reason)
            ->and($report['variable_flow']['findings'][0]['line'])->toBe(1);
    }
})->with([
    ['{{ $statusOptions[$selected] ?? $emptyValue }}', 'no_detected_override', null],
    ['@php($statusOptions = [])', 'potential_override', 'variable_write'],
    ["@php(\$statusOptions['draft'] = 'Changed')", 'potential_override', 'variable_write'],
    ['@php($statusOptions += $other)', 'potential_override', 'variable_write'],
    ['@foreach ($others as $statusOptions)', 'potential_override', 'loop_binding'],
    ['@php(unset($statusOptions))', 'potential_override', 'variable_unset'],
    ['{{-- @php($statusOptions = []) --}}', 'no_detected_override', null],
    ['@php($this->statusOptions = [])', 'potential_override', 'property_write'],
    ["@props(['statusOptions' => []])", 'potential_override', 'component_scope'],
]);

it('preserves runtime candidate reviews while refreshing observed option values', function (string $reviewStatus) {
    config(['translation-workbench.runtime_collector.enabled' => true]);
    $collector = app(\Gunreip\TranslationWorkbench\Foundation\RuntimeDynamicTranslationCollector::class);
    $observe = fn (array $values) => $collector->options('ui.status', 'status_options', $values, 'Fixture::statusOptions');
    $original = ['draft' => 'Draft', 'published' => 'Published'];
    $observe($original);
    $candidate = DB::table('translation_workbench_dynamic_source_candidates')->sole();
    expect($candidate->review_status)->toBe('pending')->and($original)->toBe(['draft' => 'Draft', 'published' => 'Published']);
    DB::table('translation_workbench_dynamic_source_candidates')->where('id', $candidate->id)->update(['review_status' => $reviewStatus]);

    $observe($original);
    expect(DB::table('translation_workbench_dynamic_source_candidates')->sole()->review_status)->toBe($reviewStatus);

    $observe(['draft' => 'Updated draft', 'archived' => 'Archived']);
    $refreshed = DB::table('translation_workbench_dynamic_source_candidates')->sole();
    expect($refreshed->id)->toBe($candidate->id)
        ->and($refreshed->review_status)->toBe($reviewStatus)
        ->and(json_decode($refreshed->candidate_values, true))->toBe(['draft' => 'Updated draft', 'archived' => 'Archived'])
        ->and(DB::table('translation_workbench_dynamic_source_values')->where('value_key', 'published')->value('status'))->toBe('stale')
        ->and(DB::table('translation_workbench_dynamic_key_values')->count())->toBe(0)
        ->and(DB::table('translation_workbench_lang_values')->count())->toBe(0);
})->with(['pending', 'confirmed', 'unlinked']);

it('retains option observation identity and linked data when its source line moves', function () {
    $source = "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach";
    $path = base_path('resources/views/options.blade.php');
    File::put($path, $source);
    $scan = fn () => app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $scan();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $observation = workbenchOptionObservation();
    $service->process(collect([$observation]), true);
    Artisan::call('translation-workbench:classify-dynamic-values');
    $before = DB::table('translation_workbench_option_discoveries')->sole();
    DB::table('translation_workbench_dynamic_source_candidates')->insert([
        'dynamic_source_id' => DB::table('translation_workbench_dynamic_sources')->sole()->id,
        'candidate_source_type' => 'runtime_options', 'candidate_reference' => 'reviewed-fixture',
        'review_status' => 'confirmed', 'status' => 'active',
    ]);
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => TranslationWorkbenchKey::sole()->id, 'value_key' => 'draft', 'locale' => 'de',
        'value' => 'Geprüfter Entwurf', 'native_label' => 'Draft', 'status' => 'active', 'source' => 'editor',
    ]);
    $reviewedCandidates = DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson();
    $translations = DB::table('translation_workbench_dynamic_key_values')->get()->toJson();
    $sources = DB::table('translation_workbench_dynamic_sources')->orderBy('id')->get()->toJson();
    $values = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    $reviews = TranslationWorkbenchReview::orderBy('id')->get()->toJson();
    File::put($path, "\n\n\n".$source);
    $scan();
    $moved = [...$observation, 'source_line' => 4, 'source_end_line' => 6];
    $result = $service->process(collect([$moved]), true);
    $after = DB::table('translation_workbench_option_discoveries')->sole();
    expect($result['summary']['observations_created'])->toBe(0)
        ->and($result['summary']['observations_updated'])->toBe(1)
        ->and($after->id)->toBe($before->id)
        ->and($after->first_seen_at)->toBe($before->first_seen_at)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson())->toBe($reviewedCandidates)
        ->and(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations)
        ->and($after->source_line)->toBe(4)
        ->and($after->fingerprint)->not->toBe($before->fingerprint)
        ->and(DB::table('translation_workbench_dynamic_sources')->orderBy('id')->get()->toJson())->toBe($sources)
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($values)
        ->and(TranslationWorkbenchReview::orderBy('id')->get()->toJson())->toBe($reviews);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $sourceAfter = DB::table('translation_workbench_dynamic_sources')->sole();
    $originalSource = json_decode($sources, true)[0];
    expect($sourceAfter->id)->toBe($originalSource['id'])
        ->and($sourceAfter->first_seen_at)->toBe($originalSource['first_seen_at'])
        ->and($sourceAfter->fingerprint)->not->toBe($originalSource['fingerprint'])
        ->and($sourceAfter->status)->toBe('active')
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($values)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson())->toBe($reviewedCandidates)
        ->and(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_sources')->sole()->id)->toBe($originalSource['id']);
    $again = $service->process(collect([$moved]), true);
    expect($again['summary']['observations_unchanged'])->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_option_observation_recorded')->count())->toBe(2);
});

it('blocks unresolved historical option identity before creating a duplicate', function (bool $sync) {
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $observation = workbenchOptionObservation();
    $service->process(collect([$observation]), true);
    DB::table('translation_workbench_option_discoveries')->update(['meta' => json_encode(['source' => 'historical'])]);
    $before = DB::table('translation_workbench_option_discoveries')->get()->toJson();
    $history = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $service->process(collect([[...$observation, 'source_line' => 10, 'source_end_line' => 12]]), $sync))
        ->toThrow(LogicException::class, 'Option observation identity requires review');
    expect(DB::table('translation_workbench_option_discoveries')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($history);
})->with([false, true]);

it('rejects duplicate option input before storing observations or history', function () {
    $observation = workbenchOptionObservation();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([$observation, $observation]), true))
        ->toThrow(LogicException::class, 'Duplicate option discovery');
    expect(DB::table('translation_workbench_option_discoveries')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe(0);
});

it('refuses multiple stored observations claiming the same moved Foundation finding', function () {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $observation = workbenchOptionObservation();
    $service->process(collect([$observation]), true);
    $copy = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::sole()->replicate();
    $copy->fingerprint = hash('sha256', 'duplicate historical identity');
    $copy->source_line = 9;
    $copy->save();
    TranslationWorkbenchFinding::query()->update(['source_line' => 21]);
    $before = DB::table('translation_workbench_option_discoveries')->orderBy('id')->get()->toJson();
    $history = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $service->process(collect([[...$observation, 'source_line' => 20, 'source_end_line' => 22]]), true))
        ->toThrow(LogicException::class, 'Option observation identity requires review');
    expect(DB::table('translation_workbench_option_discoveries')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($history);
});

it('rolls back classification when source identity is ambiguous or a value write fails', function (string $failure) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $observation = workbenchOptionObservation();
    $service->process(collect([$observation]), true);
    Artisan::call('translation-workbench:classify-dynamic-values');
    if ($failure === 'ambiguous') {
        $duplicate = (array) DB::table('translation_workbench_dynamic_sources')->sole();
        unset($duplicate['id']);
        $duplicate['fingerprint'] = hash('sha256', 'conflicting source');
        DB::table('translation_workbench_dynamic_sources')->insert($duplicate);
    } else {
        $observation['options']['draft'] = 'Changed draft';
        $observation['source_reference'] = 'updated-provider-reference';
        $service->process(collect([$observation]), true);
        DB::statement("CREATE TRIGGER reject_option_value BEFORE UPDATE ON translation_workbench_dynamic_source_values
            BEGIN SELECT RAISE(ABORT, 'Deliberate value failure'); END");
    }
    $tables = ['dynamic_sources', 'dynamic_source_values', 'dynamic_source_candidates', 'dynamic_key_values', 'timeline_events', 'findings', 'keys'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(fn () => Artisan::call('translation-workbench:classify-dynamic-values'))
        ->toThrow($failure === 'ambiguous' ? LogicException::class : \Illuminate\Database\QueryException::class);
    expect($snapshot())->toBe($before);
})->with(['ambiguous', 'write_failure']);

it('explicitly transitions reviewed option sources without losing links on reclassification', function (string $scenario) {
    $path = base_path('resources/views/options.blade.php');
    $source = "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach";
    File::put($path, $source);
    $scan = function () {
        $manifest = app(TranslationWorkbenchScanner::class)->scanWithManifest(['resources/views']);
        return app(TranslationWorkbenchFoundationSyncer::class)->sync($manifest['items'], markObsolete: true, reconciliationPaths: $manifest['reconciliation_paths']);
    };
    $scan();
    $old = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['translation_key' => 'ui.status']);
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)->process(collect([workbenchOptionObservation()]), true);
    Artisan::call('translation-workbench:classify-dynamic-values');
    $originalSource = DB::table('translation_workbench_dynamic_sources')->sole();
    DB::table('translation_workbench_dynamic_source_candidates')->insert([
        'dynamic_source_id' => $originalSource->id, 'key_id' => $key->id, 'finding_id' => $old->id,
        'candidate_source_type' => 'related_dynamic_source', 'candidate_reference' => 'dynamic_source:'.$originalSource->id,
        'candidate_values' => json_encode(workbenchOptionObservation()['options']), 'review_status' => 'confirmed', 'status' => 'active',
    ]);
    $link = DB::table('translation_workbench_dynamic_source_candidates')->sole();
    $values = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    File::put($path, str_replace('__($label)', "\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label('ui.status', (string) \$key, \$label)", $source));
    $scan();
    $target = TranslationWorkbenchFinding::where('status', 'active')->sole();
    // Reviewed historical and current findings share the same existing translation key.
    $key->refresh()->update(['status' => 'open']);
    $target->keys()->sync([$key->id => ['status' => 'active', 'relation_type' => 'primary']]);
    $request = ['source_id' => $originalSource->id, 'old_finding_id' => $old->id, 'target_finding_id' => $target->id,
        'key_id' => $key->id, 'options' => workbenchOptionObservation()['options'], 'file_hash' => hash_file('sha256', $path)];
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionSourceTransition::class);
    if ($scenario !== 'valid') {
        $invalid = match ($scenario) {
            'file_changed' => [...$request, 'file_hash' => str_repeat('0', 64)],
            'options_changed' => [...$request, 'options' => ['draft' => 'Unexpected']],
            'wrong_key' => [...$request, 'key_id' => $key->id + 100],
            default => $request,
        };
        if ($scenario === 'existing_target') {
            $duplicate = (array) $originalSource;
            unset($duplicate['id']);
            $duplicate['finding_id'] = $target->id;
            $duplicate['fingerprint'] = hash('sha256', 'already occupied target');
            DB::table('translation_workbench_dynamic_sources')->insert($duplicate);
        }
        $tables = ['dynamic_sources', 'option_discoveries', 'dynamic_source_candidates', 'dynamic_source_values', 'timeline_events'];
        $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
        $before = $snapshot();
        $batch = $scenario === 'batch_rollback' ? [$request, $request] : [$invalid];
        expect(fn () => $service->process($batch, true))->toThrow(LogicException::class);
        expect($snapshot())->toBe($before);
        return;
    }
    expect($service->process([$request])[0]['status'])->toBe('ready')
        ->and(DB::table('translation_workbench_dynamic_sources')->sole()->finding_id)->toBe($old->id);
    expect($service->process([$request], true)[0]['status'])->toBe('transitioned');
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $after = DB::table('translation_workbench_dynamic_sources')->sole();
    expect($after->id)->toBe($originalSource->id)->and($after->finding_id)->toBe($target->id)
        ->and($after->status)->toBe('active')
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->sole()->id)->toBe($link->id)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->sole()->review_status)->toBe('confirmed')
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($values)
        ->and($service->process([$request], true)[0]['status'])->toBe('already_transitioned')
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_option_source_transitioned')->count())->toBe(1);
    $invalid = [...$request, 'file_hash' => str_repeat('0', 64)];
    expect(fn () => $service->process([$invalid], true))->toThrow(LogicException::class, 'source file changed');
})->with(['valid', 'file_changed', 'options_changed', 'wrong_key', 'existing_target', 'batch_rollback']);

it('resolves same-named arrays only through an unambiguous literal view chain', function (string $scenario, string $expected) {
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/First.php'), "<?php class First {\n public array \$statusOptions = [\n 'draft' => 'First',\n ];\n public function render() { return view('first-root'); }\n}");
    File::put(app_path('Livewire/Second.php'), "<?php class Second {\n public array \$statusOptions = [\n 'draft' => 'Second',\n ];\n public function render() { return view('second-root'); }\n}");
    File::put(base_path('resources/views/first-root.blade.php'), "@include('bound-options')");
    File::put(base_path('resources/views/second-root.blade.php'), match ($scenario) {
        'both_reachable' => "@include('bound-options')",
        'dynamic_include' => '@include($selectedView)',
        'missing_view' => "@include('missing-view')",
        default => '<div>Independent component</div>',
    });
    File::put(base_path('resources/views/bound-options.blade.php'), ($scenario === 'override' ? '@php($statusOptions = [])' : '')."\n@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    $observation = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collect(['resources/views/bound-options.blade.php'])->sole();
    expect($observation['source_resolution'])->toBe($expected)
        ->and($observation['source_candidates'])->toHaveCount(2)
        ->and($observation['options'])->toBe($expected === 'view_bound_candidate' ? ['draft' => 'First'] : []);
    if ($scenario === 'unique') {
        app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views/bound-options.blade.php']));
        $synchronizer = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
        expect($synchronizer->process(collect([$observation]), true)['plan']['summary']['matched'])->toBe(1);
        Artisan::call('translation-workbench:classify-dynamic-values');
        $id = DB::table('translation_workbench_dynamic_sources')->sole()->id;
        $repeated = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collect(['resources/views/bound-options.blade.php']);
        expect($synchronizer->process($repeated, true)['summary']['observations_unchanged'])->toBe(1);
        Artisan::call('translation-workbench:classify-dynamic-values');
        expect(DB::table('translation_workbench_dynamic_sources')->sole()->id)->toBe($id)
            ->and(DB::table('translation_workbench_dynamic_source_values')->sole()->native_label)->toBe('First');
    }

})->with([
    ['unique', 'view_bound_candidate'], ['both_reachable', 'ambiguous_source'],
    ['dynamic_include', 'ambiguous_source'], ['missing_view', 'ambiguous_source'], ['override', 'ambiguous_source'],
]);

it('inspects overrides along every include path to a shared option view', function () {
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/Owner.php'), "<?php class Owner { public array \$options = []; public function render() { return view('root'); } }");
    File::put(base_path('resources/views/root.blade.php'), "@include('first')\n@include('second')");
    File::put(base_path('resources/views/first.blade.php'), "@include('shared')");
    File::put(base_path('resources/views/second.blade.php'), "@php(\$options = [])\n@include('shared')");
    File::put(base_path('resources/views/shared.blade.php'), "@include('leaf')");
    File::put(base_path('resources/views/leaf.blade.php'), '{{ $options }}');
    $result = (new \Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchOptionViewBinding)->inspect('app/Livewire/Owner.php', 'resources/views/leaf.blade.php', 'options');
    expect($result['paths'])->toHaveCount(2)
        ->and($result['variable_flow']['status'])->toBe('potential_override');
});

it('collects complete constant-key option arrays repeatedly without executing their classes', function () {
    File::ensureDirectoryExists(app_path('Models'));
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Models/StaticKinds.php'), "<?php\nnamespace App\\Models;\nthrow new \\RuntimeException('Never execute');\nclass StaticKinds { public const FIRST = 'first'; }\n");
    File::put(app_path('Livewire/StaticOwner.php'), <<<'CODE'
<?php
use App\Models\StaticKinds;
class StaticOwner {
    public array $kindOptions = [StaticKinds::FIRST => 'First',
        'second' => 'Second',
    ];
    public function render() { return view('static-options'); }
}
CODE);
    File::put(base_path('resources/views/static-options.blade.php'), '@foreach ($kindOptions as $key => $label) {{ __($label) }} @endforeach');
    $collector = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class);
    $paths = ['resources/views/static-options.blade.php'];
    $first = $collector->collect($paths);
    expect($first->sole()['options'])->toBe(['first' => 'First', 'second' => 'Second'])
        ->and($collector->collect($paths)->all())->toBe($first->all());
});

it('reads literal class-constant option keys without executing application code', function (string $body, array $expected) {
    File::ensureDirectoryExists(app_path('Models'));
    File::put(app_path('Models/OptionKinds.php'), <<<'CODE'
<?php
namespace App\Models;
throw new \RuntimeException('Must never execute this file');
class OptionKinds {
    public const PARENT = 'parent';
    public const DYNAMIC = unknown_function();
    private const PRIVATE_KEY = 'secret';
}
CODE);
    $owner = '<?php namespace App\\Livewire; use App\\Models\\OptionKinds as Kinds;';
    // Imports in the supported declaration form start on their own line.
    $owner = str_replace('; use', ";\nuse", $owner);
    expect((new \Gunreip\TranslationWorkbench\Scanner\StaticOptionArray)->read($body, $owner))->toBe($expected);
})->with([
    ["Kinds::PARENT => 'Parent',", ['parent' => 'Parent']],
    ["\\App\\Models\\OptionKinds::PARENT => 'Parent',", ['parent' => 'Parent']],
    ["'plain' => 'Text', Kinds::PARENT => 'Parent',", ['plain' => 'Text', 'parent' => 'Parent']],
    ["'plain' => 'Text', Kinds::MISSING => 'Other',", []],
    ["'plain' => 'Text', Kinds::DYNAMIC => 'Other',", []],
    ["'plain' => 'Text', Kinds::PRIVATE_KEY => 'Other',", []],
    ["'plain' => 'Text', ...\$runtime,", []],
    ["/* 'fake' => 'Fake' */ 'real' => 'Real',", ['real' => 'Real']],
    ["'path' => 'A\\nB',", ['path' => 'A\\nB']],
    ['\'path\' => "A\\qB",', ['path' => 'A\\qB']],
    ['\'path\' => "A\\nB",', ['path' => "A\nB"]],
    ['\'price\' => "\\$5",', ['price' => '$5']],
]);

it('preserves observed values through unresolved discovery and resumes an explicit value reconciliation', function () {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $known = workbenchOptionObservation();
    $service->process(collect([$known]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $sourceId = DB::table('translation_workbench_dynamic_sources')->sole()->id;
    $values = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => TranslationWorkbenchKey::sole()->id, 'value_key' => 'draft', 'locale' => 'de',
        'value' => 'Geprüfter Entwurf', 'native_label' => 'Draft', 'status' => 'active', 'source' => 'editor',
    ]);
    DB::table('translation_workbench_dynamic_source_candidates')->insert([
        'dynamic_source_id' => $sourceId, 'candidate_source_type' => 'runtime_options',
        'candidate_reference' => 'reviewed-fixture', 'review_status' => 'confirmed', 'status' => 'active',
    ]);
    $translations = DB::table('translation_workbench_dynamic_key_values')->get()->toJson();
    $candidates = DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson();
    $unknown = [...$known, 'options' => [], 'source_type' => 'unresolved', 'source_resolution' => 'unresolved'];
    for ($i = 0; $i < 2; $i++) {
        $result = $service->process(collect([$unknown]), true);
        expect($result['summary']['needs_review'])->toBe(1)
            ->and($result['plan']['summary']['matched'])->toBe(1);
        expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
        expect(Artisan::output())->toContain('Source values preserved (unresolved options): 2');
        $source = DB::table('translation_workbench_dynamic_sources')->sole();
        $observation = DB::table('translation_workbench_option_discoveries')->sole();
        expect($source->id)->toBe($sourceId)->and($source->status)->toBe('needs_review')
            ->and($observation->status)->toBe('needs_review')
            ->and(json_decode($observation->meta, true)['options_before_unresolved']['options'])->toBe($known['options'])
            ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($values);
    }
    $recovered = [...$known, 'options' => ['draft' => 'Updated draft', 'archived' => 'Archived']];
    $service->process(collect([$recovered]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    expect(DB::table('translation_workbench_dynamic_sources')->sole()->id)->toBe($sourceId)
        ->and(DB::table('translation_workbench_dynamic_sources')->sole()->status)->toBe('active')
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('value_key')->pluck('native_label', 'value_key')->all())
        ->toBe(['archived' => 'Archived', 'draft' => 'Updated draft'])
        ->and(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson())->toBe($candidates);
});

it('rebinds reviewed historical sources to their own observation without changing shared observations', function (string $scenario) {
    $indexed = $scenario === 'indexed';
    $path = base_path('resources/views/options.blade.php');
    $oldCode = $indexed ? '{{ __($statusOptions[$selected] ?? $emptyValue) }}'
        : "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach";
    File::put($path, $oldCode);
    $scan = function () {
        $manifest = app(TranslationWorkbenchScanner::class)->scanWithManifest(['resources/views']);
        app(TranslationWorkbenchFoundationSyncer::class)->sync($manifest['items'], markObsolete: true, reconciliationPaths: $manifest['reconciliation_paths']);
    };
    $scan();
    $old = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['translation_key' => 'ui.status']);
    $observation = workbenchOptionObservation();
    $previous = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect($observation)->except('source_end_line')->all(), 'fingerprint' => hash('sha256', 'historical-observation'), 'workbench_suggested_key' => 'ui.status',
        'status' => 'open', 'options_count' => 2, 'first_seen_at' => now(), 'last_seen_at' => now(),
    ]);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $source = DB::table('translation_workbench_dynamic_sources')->sole();
    // Historically the source consumed another view's observation (or a foreach observation for an indexed call).
    $previous->update(['source_path' => $indexed ? 'resources/views/options.blade.php' : 'resources/views/other.blade.php']);
    $previousSnapshot = $previous->fresh()->toArray();
    DB::table('translation_workbench_dynamic_source_candidates')->insert([
        'dynamic_source_id' => $source->id, 'key_id' => $key->id, 'finding_id' => $old->id,
        'candidate_source_type' => 'related_dynamic_source', 'candidate_reference' => 'dynamic_source:'.$source->id,
        'candidate_values' => json_encode($observation['options']), 'review_status' => 'confirmed', 'status' => 'active',
    ]);
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => $key->id, 'value_key' => 'draft', 'locale' => 'de', 'value' => 'Entwurf',
        'native_label' => 'Draft', 'status' => 'active', 'source' => 'editor',
    ]);
    $translations = DB::table('translation_workbench_dynamic_key_values')->get()->toJson();
    $values = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    $call = $indexed
        ? "\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label('ui.status', (string) \$selected, \$statusOptions[\$selected] ?? \$emptyValue)"
        : "\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label('ui.status', (string) \$key, \$label)";
    File::put($path, str_replace($indexed ? '__($statusOptions[$selected] ?? $emptyValue)' : '__($label)', $call, $oldCode));
    $scan();
    $target = TranslationWorkbenchFinding::where('status', 'active')->sole();
    $key->refresh()->update(['status' => 'open']);
    $target->keys()->sync([$key->id => ['status' => 'active', 'relation_type' => 'primary']]);
    File::ensureDirectoryExists(app_path('Livewire'));
    File::put(app_path('Livewire/Options.php'), "<?php class Options {\n public array \$statusOptions = ['draft' => 'Draft', 'published' => 'Published'];\n public function render() { return view('options'); }\n}");
    $collector = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class);
    $sync = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $current = $collector->collect(['resources/views/options.blade.php']);
    $existingTarget = $indexed ? null : \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect($current->sole())->only(['scope', 'suggested_key', 'suggested_dynamic_key', 'source_path', 'source_line',
            'options_variable', 'key_variable', 'label_variable', 'label_usage', 'source_type', 'source_reference', 'options'])->all(),
        'fingerprint' => hash('sha256', 'old-target-observation'), 'source_line' => 99,
        'status' => 'open', 'options_count' => 2, 'first_seen_at' => now(), 'last_seen_at' => now(),
    ]);
    $target->update(['meta' => collect($target->meta)->except('dynamic_option_context')->all()]);
    $preparation = ['observation_id' => $existingTarget?->id, 'target_finding_id' => $target->id, 'key_id' => $key->id,
        'file_hash' => hash_file('sha256', $path), 'discovery' => $current->sole()];
    $prepare = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition::class);
    if (str_starts_with($scenario, 'prepare_')) {
        $invalid = match ($scenario) {
            'prepare_file_changed' => [...$preparation, 'file_hash' => str_repeat('0', 64)],
            'prepare_wrong_owner' => [...$preparation, 'key_id' => $key->id + 100],
            'prepare_options_changed' => [...$preparation, 'discovery' => [...$preparation['discovery'], 'options' => ['changed' => 'Changed']]],
            'prepare_context_changed' => [...$preparation, 'discovery' => [...$preparation['discovery'], 'key_variable' => 'other']],
            default => $preparation,
        };
        $tables = ['findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values', 'timeline_events'];
        $snapshot = fn () => collect($tables)->mapWithKeys(fn ($t) => [$t => DB::table('translation_workbench_'.$t)->orderBy('id')->get()->toJson()])->all();
        $before = $snapshot();
        expect(fn () => $prepare->process($scenario === 'prepare_batch_rollback' ? [$preparation, $invalid] : [$invalid], true))->toThrow(LogicException::class);
        expect($snapshot())->toBe($before);
        return;
    }
    $beforePreparation = DB::table('translation_workbench_option_discoveries')->orderBy('id')->get()->toJson();
    expect($prepare->process([$preparation])[0]['status'])->toBe($indexed ? 'ready_to_create' : 'ready_to_refresh')
        ->and(DB::table('translation_workbench_option_discoveries')->orderBy('id')->get()->toJson())->toBe($beforePreparation);
    $prepared = $prepare->process([$preparation], true)[0];
    $eventCount = TranslationWorkbenchTimelineEvent::count();
    expect($prepare->process([$preparation], true)[0]['observation_id'])->toBe($prepared['observation_id'])
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($eventCount);
    $newObservation = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::where('id', '<>', $previous->id)->sole();
    if ($existingTarget !== null) { expect($newObservation->id)->toBe($existingTarget->id); }
    $request = ['source_id' => $source->id, 'old_finding_id' => $old->id, 'target_finding_id' => $target->id,
        'key_id' => $key->id, 'options' => $observation['options'], 'file_hash' => hash_file('sha256', $path),
        'old_observation_id' => $previous->id, 'target_observation_id' => $newObservation->id];
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionSourceTransition::class);
    if (! in_array($scenario, ['other_view', 'indexed'], true) && ! str_starts_with($scenario, 'consolidate_')) {
        if ($scenario === 'unassigned_target') { $newObservation->update(['meta' => []]); }
        if ($scenario === 'wrong_context') { $newObservation->update(['key_variable' => 'other']); }
        if ($scenario === 'changed_payload') { $newObservation->update(['options' => ['draft' => 'Changed']]); }
        if ($scenario === 'partial_transition') { DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->update(['finding_id' => $target->id]); }
        $tables = ['dynamic_sources', 'option_discoveries', 'dynamic_source_candidates', 'dynamic_source_values', 'dynamic_key_values', 'timeline_events'];
        $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
        $before = $snapshot();
        $requests = $scenario === 'batch_rollback' ? [$request, $request] : [$request];
        expect(fn () => $service->process($requests, true))->toThrow(LogicException::class, match ($scenario) {
            'unassigned_target' => 'exact Foundation assignment',
            'changed_payload' => 'reviewed options changed',
            'partial_transition' => 'partial observation transition',
            'wrong_context' => 'observation context differs',
            'batch_rollback' => 'duplicate transition request',
        });
        expect($snapshot())->toBe($before);
        return;
    }
    expect($service->process([$request])[0]['status'])->toBe('ready')
        ->and(DB::table('translation_workbench_dynamic_sources')->sole()->finding_id)->toBe($old->id);
    expect($service->process([$request], true)[0]['status'])->toBe('transitioned');
    for ($i = 0; $i < 2; $i++) {
        $sync->process($collector->collect(['resources/views/options.blade.php']), true);
        expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
        $after = DB::table('translation_workbench_dynamic_sources')->sole();
        expect($after->id)->toBe($source->id)->and($after->finding_id)->toBe($target->id)
            ->and($after->option_discovery_id)->toBe($newObservation->id)->and($after->status)->toBe('active');
    }
    expect($previous->fresh()->toArray())->toBe($previousSnapshot)
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($values)
        ->and(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->sole()->review_status)->toBe('confirmed')
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->sole()->finding_id)->toBe($target->id)
        ->and($service->process([$request], true)[0]['status'])->toBe('already_transitioned');

    if (str_starts_with($scenario, 'consolidate_')) {
        $canonical = DB::table('translation_workbench_dynamic_sources')->find($source->id);
        $duplicate = (array) $canonical;
        unset($duplicate['id']);
        $duplicate['finding_id'] = $old->id;
        $duplicate['fingerprint'] = hash('sha256', 'duplicate-history');
        $duplicateId = DB::table('translation_workbench_dynamic_sources')->insertGetId($duplicate);
        foreach (DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->get() as $value) {
            $copy = (array) $value; unset($copy['id']); $copy['dynamic_source_id'] = $duplicateId;
            DB::table('translation_workbench_dynamic_source_values')->insert($copy);
        }
        $canonicalCandidate = DB::table('translation_workbench_dynamic_source_candidates')->sole();
        $candidate = (array) $canonicalCandidate; unset($candidate['id']);
        $candidate['finding_id'] = $old->id;
        $candidate['candidate_reference'] = 'dynamic_source:'.$duplicateId;
        $candidateId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId($candidate);
        $retire = ['source_id' => $duplicateId, 'canonical_source_id' => $source->id, 'key_id' => $key->id,
            'options' => $observation['options'], 'file_hash' => hash_file('sha256', $path),
            'candidate_replacements' => [$candidateId => $canonicalCandidate->id]];
        $consolidate = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionSourceConsolidation::class);
        if ($scenario === 'consolidate_different_values') { $retire['options'] = ['wrong' => 'Wrong']; }
        if ($scenario === 'consolidate_unconfirmed') { DB::table('translation_workbench_dynamic_source_candidates')->where('id', $canonicalCandidate->id)->update(['review_status' => 'pending']); }
        if ($scenario === 'consolidate_missing_link') { $retire['candidate_replacements'] = []; }
        $tables = ['dynamic_sources', 'dynamic_source_values', 'dynamic_source_candidates', 'dynamic_key_values', 'timeline_events'];
        $snapshot = fn () => collect($tables)->mapWithKeys(fn ($t) => [$t => DB::table('translation_workbench_'.$t)->orderBy('id')->get()->toJson()])->all();
        $baseline = $snapshot();
        if ($scenario !== 'consolidate_valid') {
            expect(fn () => $consolidate->process($scenario === 'consolidate_batch_rollback' ? [$retire, $retire] : [$retire], true))->toThrow(LogicException::class);
            expect($snapshot())->toBe($baseline);
            return;
        }
        expect($consolidate->process([$retire])[0]['status'])->toBe('ready')->and($snapshot())->toBe($baseline);
        expect($consolidate->process([$retire], true)[0]['status'])->toBe('consolidated');
        $events = TranslationWorkbenchTimelineEvent::count();
        expect($consolidate->process([$retire], true)[0]['status'])->toBe('already_consolidated')
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
            ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($baseline['dynamic_source_values'])
            ->and(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations)
            ->and(DB::table('translation_workbench_dynamic_source_candidates')->where('id', $candidateId)->value('review_status'))->toBe('confirmed')
            ->and(DB::table('translation_workbench_dynamic_source_candidates')->where('id', $candidateId)->value('candidate_values'))->toBe($canonicalCandidate->candidate_values)
            ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $duplicateId)->value('status'))->toBe('obsolete');
        expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
            ->and(DB::table('translation_workbench_dynamic_sources')->where('finding_id', $target->id)->where('status', 'active')->count())->toBe(1)
            ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $duplicateId)->value('status'))->toBe('obsolete');
    }
})->with(['other_view', 'indexed', 'unassigned_target', 'changed_payload', 'partial_transition', 'wrong_context', 'batch_rollback', 'prepare_file_changed', 'prepare_wrong_owner', 'prepare_options_changed', 'prepare_context_changed', 'prepare_batch_rollback', 'consolidate_valid', 'consolidate_different_values', 'consolidate_unconfirmed', 'consolidate_missing_link', 'consolidate_batch_rollback']);

it('does not automatically resolve unknown sources of inactive findings or source files', function (string $inactive) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    Artisan::call('translation-workbench:classify-dynamic-values');
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $method = new ReflectionMethod($resolver, 'unknownSources');
    expect($method->invoke($resolver))->toHaveCount(1);
    if ($inactive === 'finding') { TranslationWorkbenchFinding::sole()->update(['status' => 'obsolete']); }
    if ($inactive === 'file') { TranslationWorkbenchFinding::sole()->sourceFile->update(['status' => 'obsolete']); }
    if ($inactive === 'missing') { DB::table('translation_workbench_dynamic_sources')->update(['finding_id' => null]); }
    $before = DB::table('translation_workbench_dynamic_sources')->get()->toJson();
    expect($method->invoke($resolver))->toHaveCount(0);
    expect(Artisan::call('translation-workbench:resolve-unknown-dynamic-sources'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_sources')->get()->toJson())->toBe($before);
})->with(['finding', 'file', 'missing']);

it('prefers exact Foundation assignments over historical scoring in unknown source resolution', function () {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $sync = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $sync->process(collect([workbenchOptionObservation()]), true);
    $assigned = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::sole();
    $copy = $assigned->replicate();
    $copy->forceFill(['fingerprint' => hash('sha256', 'legacy-score'), 'meta' => [], 'options' => ['wrong' => 'Wrong'],
        'workbench_suggested_key' => TranslationWorkbenchKey::sole()->suggested_key])->save();
    // A proven observation with no current payload must not be replaced by a guessed legacy list.
    $assigned->update(['options' => [], 'options_count' => 0, 'source_type' => 'unresolved']);
    Artisan::call('translation-workbench:classify-dynamic-values');
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $source = (new ReflectionMethod($resolver, 'unknownSources'))->invoke($resolver)->sole();
    $selected = (new ReflectionMethod($resolver, 'bestDiscoveryForSource'))->invoke($resolver, $source);
    expect($selected->id)->toBe($assigned->id)
        ->and(DB::table('translation_workbench_dynamic_source_values')->count())->toBe(0);
});

it('blocks automatic dynamic processing before writes when a finding has competing active keys', function (string $command, bool $dryRun) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $other = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'competing-key'), 'translation_key' => 'ui.competing', 'status' => 'open']);
    $link = (array) DB::table('translation_workbench_key_findings')->first();
    unset($link['id']);
    $link['key_id'] = $other->id;
    DB::table('translation_workbench_key_findings')->insert($link);
    DB::enableQueryLog();
    DB::flushQueryLog();
    try {
        expect(Artisan::call($command, ['--dry-run' => $dryRun]))->toBe(1);
        expect(Artisan::output())->toContain('Ambiguous active key assignments', 'Finding '.$finding->id, 'Key IDs: '.$key->id.', '.$other->id);
        $report = json_decode(File::get(storage_path('translation-workbench/'.str_replace(':', '-', $command).'.json')), true);
        expect($report['summary']['exit_code'])->toBe(1)->and($report['summary']['dry_run'])->toBe($dryRun)
            ->and($report['summary']['error'])->toBe('ambiguous_active_key_assignments')
            ->and($report['summary']['conflicting_findings'])->toBe(1)
            ->and($report['plan_summary']['key_assignment_conflicts'])->toBe([['finding_id' => $finding->id, 'key_ids' => [$key->id, $other->id]]]);
        foreach (DB::getQueryLog() as $query) {
            expect(strtolower(ltrim($query['query'])))->toStartWith('select');
        }
    } finally {
        DB::disableQueryLog();
    }
})->with(['translation-workbench:classify-dynamic-values', 'translation-workbench:resolve-unknown-dynamic-sources'])->with([false, true]);

it('ignores obsolete keys and links when selecting the single current dynamic owner', function (string $inactive) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $old = TranslationWorkbenchKey::sole();
    $current = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'current-key'), 'translation_key' => 'ui.current', 'status' => 'open']);
    $link = (array) DB::table('translation_workbench_key_findings')->first();
    unset($link['id']);
    $link['key_id'] = $current->id;
    DB::table('translation_workbench_key_findings')->insert($link);
    if ($inactive === 'key') { $old->update(['status' => 'obsolete']); }
    else { DB::table('translation_workbench_key_findings')->where('key_id', $old->id)->update(['status' => 'obsolete']); }
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_sources')->sole()->key_id)->toBe($current->id);
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $source = (new ReflectionMethod($resolver, 'unknownSources'))->invoke($resolver)->sole();
    expect($source->translation_key)->toBe('ui.current')
        ->and(Artisan::call('translation-workbench:resolve-unknown-dynamic-sources', ['--dry-run' => true]))->toBe(0);
})->with(['key', 'link']);

it('does not classify a dynamic finding from an obsolete source file', function () {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $finding->sourceFile->update(['status' => 'obsolete']);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_sources')->count())->toBe(0)
        ->and($finding->fresh()->status)->toBe('active');
});

it('retires ambiguous legacy option findings through an explicit owner call without losing reviewed data', function () {
    $path = 'resources/views/options.blade.php';
    $legacy = "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach";
    File::put(base_path($path), $legacy);
    $scanner = app(TranslationWorkbenchScanner::class);
    $sync = app(TranslationWorkbenchFoundationSyncer::class);
    $sync->sync($scanner->scan([$path]));
    $oldFinding = TranslationWorkbenchFinding::sole();
    $oldKey = TranslationWorkbenchKey::sole();
    $current = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'reviewed-option-owner'),
        'translation_key' => 'ui.current', 'status' => 'open', 'review_status' => 'reviewed']);
    $link = (array) DB::table('translation_workbench_key_findings')->first();
    unset($link['id']); $link['key_id'] = $current->id;
    DB::table('translation_workbench_key_findings')->insert($link);
    foreach ([$oldKey, $current] as $key) {
        DB::table('translation_workbench_dynamic_key_values')->insert(['key_id' => $key->id, 'value_key' => 'draft',
            'locale' => 'de', 'value' => 'Entwurf', 'native_label' => 'Draft', 'status' => 'active', 'source' => 'editor']);
    }
    $values = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    File::put(base_path($path), str_replace('__($label)', "\\Gunreip\\TranslationWorkbench\\Foundation\\TranslationWorkbenchOption::label('ui.current', (string) \$key, \$label)", $legacy));
    $manifest = $scanner->scanWithManifest([$path]);
    $sync->sync($manifest['items'], markObsolete: true, reconciliationPaths: $manifest['reconciliation_paths']);
    $newFinding = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($oldFinding->fresh()->status)->toBe('obsolete')
        ->and($newFinding->id)->not->toBe($oldFinding->id)
        ->and($newFinding->keys()->wherePivot('status', 'active')->sole()->id)->toBe($current->id)
        ->and($oldKey->fresh()->status)->toBe('obsolete')
        ->and($current->fresh()->status)->toBe('open')
        ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($values);
    $sync->sync($manifest['items'], markObsolete: true, reconciliationPaths: $manifest['reconciliation_paths']);
    expect(TranslationWorkbenchFinding::where('status', 'active')->sole()->id)->toBe($newFinding->id)
        ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($values)
        ->and(app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicKeyAssignment::class)->conflicts([$newFinding->id]))->toBeEmpty();
});

it('populates only explicitly reviewed empty option observations from their proven static declaration', function (string $scenario) {
    File::ensureDirectoryExists(app_path('Livewire'));
    $declaration = 'app/Livewire/ResolvedOptions.php';
    File::put(base_path($declaration), <<<'CODE'
<?php
throw new \RuntimeException('Static inspection must never execute this file');
class ResolvedOptions {
    public array $statusOptions = ['draft' => 'Draft', 'published' => 'Published'];
    public function render() { return view('options'); }
}
CODE);
    $path = 'resources/views/options.blade.php';
    File::put(base_path($path), <<<'BLADE'
@foreach ($statusOptions as $key => $label)
{{ \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label('ui.status', (string) $key, $label) }}
@endforeach
BLADE);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan([$path]));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $discovery = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collectStatic([$path])->sole();
    expect($discovery['source_resolution'])->toBe('view_bound_candidate');
    $observation = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect($discovery)->except(['source_end_line', 'source_candidates', 'source_resolution'])->all(),
        'fingerprint' => hash('sha256', 'historical-empty-options'), 'options' => [], 'options_count' => 0,
        'source_type' => 'unresolved', 'status' => 'needs_review', 'meta' => [], 'first_seen_at' => now(),
    ]);
    DB::table('translation_workbench_dynamic_key_values')->insert(['key_id' => $key->id, 'value_key' => 'draft',
        'locale' => 'de', 'value' => 'Geprüfter Entwurf', 'native_label' => 'Draft', 'status' => 'active', 'source' => 'editor']);
    $translations = DB::table('translation_workbench_dynamic_key_values')->get()->toJson();
    $request = ['observation_id' => $observation->id, 'target_finding_id' => $finding->id, 'key_id' => $key->id,
        'file_hash' => hash_file('sha256', base_path($path)), 'discovery' => $discovery,
        'resolve_empty_options' => ['observation_fingerprint' => $observation->fingerprint, 'declaration_path' => $declaration,
            'declaration_hash' => hash_file('sha256', base_path($declaration))]];
    if ($scenario === 'no_proof') { unset($request['resolve_empty_options']); }
    if ($scenario === 'changed_declaration') { $request['resolve_empty_options']['declaration_hash'] = str_repeat('0', 64); }
    if ($scenario === 'changed_observation') { $request['resolve_empty_options']['observation_fingerprint'] = 'stale'; }
    if ($scenario === 'forged_payload') { $request['discovery']['options']['draft'] = 'Forged'; }
    if ($scenario === 'nonempty') { $observation->update(['options' => ['other' => 'Existing']]); }
    if ($scenario === 'stale_count') { $observation->update(['options_count' => 1]); }
    if ($scenario === 'previous_values') { $observation->update(['meta' => ['options_before_unresolved' => ['options' => ['old' => 'Old']]]]); }
    if ($scenario === 'ambiguous_source') {
        File::put(app_path('Livewire/OtherOptions.php'), "<?php class OtherOptions { public array \$statusOptions = ['draft' => 'Other']; public function render() { return view('options'); } }");
    }
    $before = $observation->fresh()->toArray();
    $events = TranslationWorkbenchTimelineEvent::count();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition::class);
    if ($scenario === 'repeat_changed_declaration') {
        $service->process([$request], true);
        File::append(base_path($declaration), "\n// Changed after review\n");
        expect(fn () => $service->process([$request], true))->toThrow(LogicException::class, 'reviewed declaration changed');
        expect(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_option_empty_payload_resolved')->count())->toBe(1);
    } elseif ($scenario !== 'valid') {
        $reason = match ($scenario) {
            'no_proof' => 'stored options differ',
            'changed_declaration' => 'reviewed declaration changed',
            'changed_observation' => 'reviewed observation changed',
            'nonempty', 'stale_count' => 'only empty unresolved',
            'previous_values' => 'historical resolved options',
            'batch_rollback' => 'duplicate preparation request',
            default => 'reviewed payload does not match static source',
        };
        expect(fn () => $service->process($scenario === 'batch_rollback' ? [$request, $request] : [$request], true))->toThrow(LogicException::class, $reason);
        expect($observation->fresh()->toArray())->toBe($before)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    } else {
        expect($service->process([$request])[0]['status'])->toBe('ready_to_refresh')
            ->and($observation->fresh()->toArray())->toBe($before);
        $service->process([$request], true);
        $service->process([$request], true);
        $fresh = $observation->fresh();
        expect($fresh->options)->toBe($discovery['options'])
            ->and($fresh->first_seen_at)->toEqual($observation->first_seen_at)
            ->and($fresh->meta['foundation_assignment']['finding_id'])->toBe($finding->id)
            ->and($fresh->meta['reviewed_empty_options_resolution']['previous']['options'])->toBe([])
            ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_option_empty_payload_resolved')->count())->toBe(1)
            ->and(\Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::count())->toBe(1);
    }
    expect(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations);
})->with(['valid', 'no_proof', 'changed_declaration', 'changed_observation', 'forged_payload', 'nonempty', 'stale_count', 'previous_values', 'ambiguous_source', 'batch_rollback', 'repeat_changed_declaration']);

it('assigns unresolved observation identity without pretending runtime values are static options', function (string $scenario) {
    File::ensureDirectoryExists(app_path());
    $path = 'resources/views/options.blade.php';
    File::put(base_path($path), <<<'BLADE'
@foreach ($statusOptions as $key => $label)
{{ \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOption::label('ui.status', (string) $key, $label) }}
@endforeach
BLADE);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan([$path]));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $discovery = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector::class)->collectStatic([$path])->sole();
    $observation = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect($discovery)->except(['source_end_line', 'source_candidates', 'source_resolution'])->all(),
        'fingerprint' => hash('sha256', 'unresolved-identity'), 'options' => [], 'options_count' => 0,
        'status' => 'needs_review', 'meta' => [], 'first_seen_at' => now(),
    ]);
    DB::table('translation_workbench_dynamic_key_values')->insert(['key_id' => $key->id, 'value_key' => 'custom',
        'locale' => 'de', 'value' => 'Individuell', 'native_label' => 'Custom', 'status' => 'active', 'source' => 'editor']);
    $translations = DB::table('translation_workbench_dynamic_key_values')->get()->toJson();
    $request = ['observation_id' => $observation->id, 'target_finding_id' => $finding->id, 'key_id' => $key->id,
        'file_hash' => hash_file('sha256', base_path($path)), 'discovery' => $discovery, 'assign_unresolved' => true];
    if ($scenario === 'not_explicit') { unset($request['assign_unresolved']); }
    if ($scenario === 'nonempty') { $observation->update(['options' => ['existing' => 'Existing']]); }
    if ($scenario === 'archived') { $observation->update(['meta' => ['options_before_unresolved' => ['options' => ['old' => 'Old']]]]); }
    if ($scenario === 'forged_values') { $request['discovery']['options'] = ['default' => 'Default']; }
    if ($scenario === 'resolved_source') {
        File::ensureDirectoryExists(app_path('Livewire'));
        File::put(app_path('Livewire/StaticOptions.php'), "<?php class StaticOptions { public array \$statusOptions = ['ready' => 'Ready']; public function render() { return view('options'); } }");
    }
    $before = $observation->fresh()->toArray();
    $events = TranslationWorkbenchTimelineEvent::count();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition::class);
    if ($scenario === 'valid') {
        expect($service->process([$request])[0]['status'])->toBe('ready_to_refresh')
            ->and($observation->fresh()->toArray())->toBe($before);
        $service->process([$request], true);
        $eventsAfter = TranslationWorkbenchTimelineEvent::count();
        $service->process([$request], true);
        $after = $observation->fresh();
        expect($after->options)->toBe([])->and($after->options_count)->toBe(0)
            ->and($after->source_type)->toBe('unresolved')->and($after->status)->toBe('needs_review')
            ->and($after->meta['foundation_assignment']['status'])->toBe('matched')
            ->and($after->meta['foundation_assignment']['finding_id'])->toBe($finding->id)
            ->and($after->first_seen_at)->toEqual($observation->first_seen_at)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($eventsAfter);
    } else {
        $reason = match ($scenario) {
            'not_explicit' => 'reviewed resolved payload required',
            'nonempty', 'archived' => 'existing observation has resolved or archived values',
            'forged_values' => 'identity-only assignment requires',
            'resolved_source' => 'current source is not the reviewed unresolved observation',
            'batch_rollback' => 'duplicate preparation request',
        };
        expect(fn () => $service->process($scenario === 'batch_rollback' ? [$request, $request] : [$request], true))->toThrow(LogicException::class, $reason);
        expect($observation->fresh()->toArray())->toBe($before)->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    }
    expect(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($translations)
        ->and(DB::table('translation_workbench_dynamic_sources')->count())->toBe(0);
})->with(['valid', 'not_explicit', 'nonempty', 'archived', 'forged_values', 'resolved_source', 'batch_rollback']);

it('requires more than file proximity when resolving historical option observations', function (string $identity) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    Artisan::call('translation-workbench:classify-dynamic-values');
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $source = (new ReflectionMethod($resolver, 'unknownSources'))->invoke($resolver)->sole();
    $source->dynamic_scope = 'reviewed-scope';
    $observation = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect(workbenchOptionObservation())->except(['source_end_line'])->all(),
        'fingerprint' => hash('sha256', 'historical-proximity'),
        'meta' => [], 'scope' => $identity === 'scope' ? 'reviewed-scope' : 'unrelated-scope',
        'source_path' => $identity === 'file_only' ? $source->finding_source_path : 'resources/views/elsewhere.blade.php',
        'source_line' => $source->source_line,
        'suggested_key' => $identity === 'key' ? $source->key_suggested_key : 'ui.unrelated',
        'workbench_suggested_key' => null, 'suggested_dynamic_key' => null,
        'options' => ['wrong' => 'Unrelated display value'], 'options_count' => 1, 'status' => 'open',
    ]);
    $before = $observation->fresh()->toArray();
    $selected = (new ReflectionMethod($resolver, 'bestDiscoveryForSource'))->invoke($resolver, $source);
    expect($selected?->id)->toBe($identity === 'file_only' ? null : $observation->id)
        ->and($observation->fresh()->toArray())->toBe($before)
        ->and(DB::table('translation_workbench_dynamic_source_values')->count())->toBe(0);
})->with(['file_only', 'key', 'scope']);

it('does not guess between runtime sources or revive obsolete sources', function (string $scenario) {
    [, $ids] = workbenchSourceLinkEditor();
    DB::table('translation_workbench_dynamic_sources')->whereIn('id', $ids)->update(['dynamic_scope' => 'shared']);
    if ($scenario !== 'ambiguous') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[1])->update(['status' => 'obsolete']);
    }
    if ($scenario === 'obsolete') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[0])->update(['status' => 'obsolete']);
    }
    $source = DB::table('translation_workbench_dynamic_sources')->find($ids[2]);
    $before = DB::table('translation_workbench_dynamic_sources')->orderBy('id')->get()->toJson();
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $selected = (new ReflectionMethod($resolver, 'bestRuntimeSourceForSource'))->invoke($resolver, $source);
    expect($selected?->id)->toBe($scenario === 'unique' ? $ids[0] : null)
        ->and(DB::table('translation_workbench_dynamic_sources')->orderBy('id')->get()->toJson())->toBe($before);
})->with(['ambiguous', 'obsolete', 'unique']);

it('preserves reviewed runtime links and their snapshots during resolver suggestions', function (string $decision) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    DB::table('translation_workbench_dynamic_source_candidates')->update([
        'review_status' => $decision, 'status' => $decision === 'confirmed' ? 'active' : 'inactive',
    ]);
    DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[0])->update(['native_label' => 'Changed runtime value']);
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $runtime = DB::table('translation_workbench_dynamic_sources')->find($ids[0]);
    $consumer = DB::table('translation_workbench_dynamic_sources')->find($ids[2]);
    $consumer->key_suggested_key = TranslationWorkbenchKey::sole()->suggested_key;
    $consumer->finding_suggested_key = TranslationWorkbenchFinding::sole()->suggested_key;
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $result = (new ReflectionMethod($resolver, 'syncRuntimeSourceLink'))->invoke($resolver, $runtime, $consumer);
    expect($result['runtime_source_links_unchanged'])->toBe(1)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['confirmed', 'unlinked', 'rejected', 'pending']);

it('refreshes only pending active runtime suggestions and keeps repeats idempotent', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    DB::table('translation_workbench_dynamic_source_candidates')->update(['review_status' => 'pending']);
    DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[0])->update(['native_label' => 'Updated']);
    $runtime = DB::table('translation_workbench_dynamic_sources')->find($ids[0]);
    $consumer = DB::table('translation_workbench_dynamic_sources')->find($ids[2]);
    $consumer->key_suggested_key = TranslationWorkbenchKey::sole()->suggested_key;
    $consumer->finding_suggested_key = TranslationWorkbenchFinding::sole()->suggested_key;
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $method = new ReflectionMethod($resolver, 'syncRuntimeSourceLink');
    expect($method->invoke($resolver, $runtime, $consumer)['runtime_source_links_updated'])->toBe(1);
    $row = DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $ids[0])->sole();
    expect(json_decode($row->candidate_values, true))->toBe(['draft' => 'Updated', 'published' => 'Updated'])
        ->and($row->review_status)->toBe('pending');
    $events = TranslationWorkbenchTimelineEvent::count();
    expect($method->invoke($resolver, $runtime, $consumer)['runtime_source_links_unchanged'])->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
});

it('retires only reviewed stale runtime suggestions without altering values or decisions', function (string $scenario) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    $candidate = DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $ids[0])->sole();
    DB::table('translation_workbench_dynamic_source_candidates')->where('id', $candidate->id)->update(['review_status' => $scenario === 'confirmed' ? 'confirmed' : 'pending']);
    $finding = TranslationWorkbenchFinding::sole();
    if ($scenario !== 'active') { $finding->update(['status' => 'obsolete']); }
    if ($scenario !== 'active_links') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    $request = ['candidate_id' => $candidate->id, 'finding_id' => $finding->id, 'consumer_source_id' => $ids[2],
        'snapshot_hash' => $scenario === 'changed_snapshot' ? 'wrong' : hash('sha256', $candidate->candidate_values)];
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $values = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    $sources = DB::table('translation_workbench_dynamic_sources')->orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchRuntimeSuggestionRetirement::class);
    if ($scenario === 'valid') {
        expect($service->process([$request])[0]['status'])->toBe('ready_to_retire')
            ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before);
        $service->process([$request], true);
        expect($service->process([$request], true)[0]['status'])->toBe('already_retired');
        $after = DB::table('translation_workbench_dynamic_source_candidates')->find($candidate->id);
        expect($after->status)->toBe('obsolete')->and($after->review_status)->toBe('pending')
            ->and($after->candidate_values)->toBe($candidate->candidate_values)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events + 1);
    } else {
        expect(fn () => $service->process($scenario === 'rollback' ? [$request, $request] : [$request], true))->toThrow(LogicException::class);
        expect(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    }
    expect(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($values)
        ->and(DB::table('translation_workbench_dynamic_sources')->orderBy('id')->get()->toJson())->toBe($sources);
})->with(['valid', 'active', 'active_links', 'confirmed', 'changed_snapshot', 'rollback']);

it('uses confirmed runtime links only while their owner remains active', function (string $state) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $selected = (object) ['id' => $finding->id, 'key_id' => $key->id];
    if ($state === 'finding') { $finding->update(['status' => 'obsolete']); }
    if ($state === 'file') { $finding->sourceFile->update(['status' => 'obsolete']); }
    if ($state === 'key') { $key->update(['status' => 'obsolete']); }
    if ($state === 'link') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    if ($state === 'other_finding_same_key') { $selected->id = $finding->id + 100; }
    $candidates = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $reviews = TranslationWorkbenchReview::orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $result = app(TranslationWorkbenchDynamicOptions::class)->linkedRuntimeSourceIdsForFinding($selected);
    expect($result)->toBe(in_array($state, ['active', 'other_finding_same_key'], true) ? [$ids[0], $ids[1]] : [])
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($candidates)
        ->and(TranslationWorkbenchReview::orderBy('id')->get()->toJson())->toBe($reviews)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['active', 'finding', 'file', 'key', 'link', 'other_finding_same_key']);

it('separates valid direct option ownership from historical sources and keeps stored translations', function (string $scenario) {
    workbenchDynamicEditor();
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $runtime = $scenario === 'runtime';
    $id = DB::table('translation_workbench_dynamic_sources')->insertGetId([
        'fingerprint' => hash('sha256', 'direct-owner'), 'key_id' => $key->id,
        'finding_id' => in_array($scenario, ['runtime', 'orphan'], true) ? null : $finding->id,
        'source_type' => $runtime ? 'runtime_options' : 'hardcoded_public_array', 'status' => 'active', 'values_count' => 1,
    ]);
    DB::table('translation_workbench_dynamic_source_values')->insert(['dynamic_source_id' => $id, 'value_key' => 'source-only', 'native_label' => 'Source value', 'status' => 'active']);
    if ($scenario === 'finding') { $finding->update(['status' => 'obsolete']); }
    if ($scenario === 'file') { $finding->sourceFile->update(['status' => 'obsolete']); }
    if ($scenario === 'link') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    if ($scenario === 'key') { $key->update(['status' => 'obsolete']); }
    if ($scenario === 'wrong_key') {
        $other = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'unrelated-owner'), 'translation_key' => 'ui.unrelated', 'status' => 'open']);
        DB::table('translation_workbench_dynamic_sources')->where('id', $id)->update(['key_id' => $other->id]);
    }
    $sources = DB::table('translation_workbench_dynamic_sources')->get()->toJson();
    $stored = DB::table('translation_workbench_dynamic_key_values')->get()->toJson();
    $selectedFinding = $scenario === 'same_key' ? $finding->id + 100 : $finding->id;
    $rows = app(TranslationWorkbenchDynamicOptions::class)->rows($key->id, $selectedFinding, ['source' => 'en', 'active' => 'de']);
    expect(in_array('source-only', array_column($rows, 'value_key'), true))->toBe(in_array($scenario, ['active', 'runtime', 'same_key'], true))
        ->and(array_column($rows, 'value_key'))->toContain('draft', 'published')
        ->and(DB::table('translation_workbench_dynamic_sources')->get()->toJson())->toBe($sources)
        ->and(DB::table('translation_workbench_dynamic_key_values')->get()->toJson())->toBe($stored);
})->with(['active', 'runtime', 'orphan', 'finding', 'file', 'link', 'key', 'wrong_key', 'same_key']);

it('keeps identical duplicate option sources independent of insertion order and preserves edited values', function (bool $reverse, bool $stored) {
    workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $types = ['runtime_options', 'runtime_db_options'];
    foreach ($reverse ? array_reverse($types) : $types as $type) {
        $id = DB::table('translation_workbench_dynamic_sources')->insertGetId([
            'fingerprint' => hash('sha256', 'duplicates-'.$type), 'source_type' => $type,
            'key_id' => $key->id, 'finding_id' => null, 'status' => 'active', 'values_count' => 1,
        ]);
        DB::table('translation_workbench_dynamic_source_values')->insert([
            'dynamic_source_id' => $id, 'value_key' => 'duplicated', 'native_label' => 'Original', 'status' => 'active',
        ]);
    }
    if ($stored) {
        foreach (['en' => 'Edited source', 'de' => 'Bearbeitete Übersetzung'] as $locale => $value) {
            DB::table('translation_workbench_dynamic_key_values')->insert([
                'key_id' => $key->id, 'value_key' => 'duplicated', 'locale' => $locale,
                'value' => $value, 'native_label' => 'Original', 'status' => 'active', 'source' => 'translation_workbench_modal',
            ]);
        }
    }
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    $sourceBefore = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    $rows = collect(app(TranslationWorkbenchDynamicOptions::class)->rows($key->id, $finding->id, ['source' => 'en', 'active' => 'de']))->where('value_key', 'duplicated');
    expect($rows)->toHaveCount(1)
        ->and($rows->sole()['source'])->toBe($stored ? 'Edited source' : 'Original')
        ->and($rows->sole()['target'])->toBe($stored ? 'Bearbeitete Übersetzung' : null)
        ->and($rows->sole()['native_label'])->toBe('Original')
        ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($sourceBefore);
})->with([false, true])->with([false, true]);

it('reports conflicting option sources and rechecks them before dynamic writes', function (string $action, bool $stored) {
    workbenchDynamicEditor();
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $field = 'v_'.sha1('conflicting');
    if ($stored) {
        foreach (['en' => 'Reviewed source', 'de' => 'Geprüfte Übersetzung'] as $locale => $value) {
            DB::table('translation_workbench_dynamic_key_values')->insert(['key_id' => $key->id, 'value_key' => 'conflicting', 'locale' => $locale,
                'value' => $value, 'native_label' => 'Reviewed original', 'status' => 'active', 'source' => 'translation_workbench_modal']);
        }
    }
    $ids = [];
    foreach (['First value', 'Second value'] as $value) {
        $ids[] = $id = DB::table('translation_workbench_dynamic_sources')->insertGetId(['fingerprint' => hash('sha256', $value),
            'source_type' => 'runtime_options', 'source_reference' => 'Provider '.$value, 'key_id' => $key->id, 'status' => 'active', 'values_count' => 1]);
        DB::table('translation_workbench_dynamic_source_values')->insert(['dynamic_source_id' => $id, 'value_key' => 'conflicting', 'native_label' => $value, 'status' => 'active']);
        if (count($ids) === 1) {
            $opened = collect(app(TranslationWorkbenchDynamicOptions::class)->rows($key->id, $finding->id, ['source' => 'en', 'active' => 'de']))->firstWhere('value_key', 'conflicting');
            expect($opened)->not->toHaveKey('source_conflicts');
        }
    }
    $rows = app(TranslationWorkbenchDynamicOptions::class)->rows($key->id, $finding->id, ['source' => 'en', 'active' => 'de']);
    $row = collect($rows)->firstWhere('value_key', 'conflicting');
    expect($row['source'])->toBe($stored ? 'Reviewed source' : null)
        ->and($row['target'])->toBe($stored ? 'Geprüfte Übersetzung' : null)
        ->and(array_column($row['source_conflicts'], 'source_id'))->toBe($ids)
        ->and(array_column($row['source_conflicts'], 'value'))->toBe(['First value', 'Second value']);
    $before = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $editor = app(TranslationWorkbenchDynamicEditor::class);
    if ($action === 'unrelated_source') {
        // Saving an independent source row remains possible; batch save includes all rows and is blocked.
        $editor->saveSource($key->id, $finding->id, 'v_'.sha1('draft'), 'Independent correction');
        expect(DB::table('translation_workbench_dynamic_key_values')->where('value_key', 'conflicting')->where('locale', 'de')->value('value'))->toBe($stored ? 'Geprüfte Übersetzung' : null);
    } else {
        $save = fn () => $action === 'source'
            ? $editor->saveSource($key->id, $finding->id, $field, 'First value')
            : $editor->save($key->id, $finding->id, [$field => 'First value'], [$field => 'Submitted target']);
        expect($save)->toThrow(ValidationException::class, 'Conflicting option sources')
            ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[1])->update(['status' => 'obsolete']);
        expect(collect(app(TranslationWorkbenchDynamicOptions::class)->rows($key->id, $finding->id, ['source' => 'en', 'active' => 'de']))->firstWhere('value_key', 'conflicting'))->not->toHaveKey('source_conflicts');
        $save();
    }
})->with(['source', 'batch', 'unrelated_source'])->with([false, true]);

it('confirms runtime links under the current consumer key instead of the provider key', function () {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $consumerKey = TranslationWorkbenchKey::sole()->id;
    $provider = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'provider-key'), 'translation_key' => 'ui.provider', 'status' => 'open']);
    DB::table('translation_workbench_dynamic_sources')->whereIn('id', [$ids[0], $ids[1]])->update(['key_id' => $provider->id]);
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->confirmDynamicSourceLink();
    $links = DB::table('translation_workbench_dynamic_source_candidates')->get();
    expect($links)->toHaveCount(2)->and($links->pluck('key_id')->unique()->all())->toBe([$consumerKey])
        ->and($links->pluck('finding_id')->unique()->all())->toBe([TranslationWorkbenchFinding::sole()->id]);
    $selected = (object) ['id' => TranslationWorkbenchFinding::sole()->id, 'key_id' => $consumerKey];
    expect(app(TranslationWorkbenchDynamicOptions::class)->linkedRuntimeSourceIdsForFinding($selected))->toBe([$ids[0], $ids[1]])
        ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $ids[0])->value('key_id'))->toBe($provider->id);
});

it('rechecks consumer ownership before a source review can write confirmations', function (string $scenario) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $finding = TranslationWorkbenchFinding::sole();
    $selected = (new ReflectionMethod($editor, 'selectedFinding'))->invoke($editor, $finding->id);
    $sources = collect((new ReflectionMethod($editor, 'dynamicReviewSources'))->invoke($editor, $finding->id));
    $runtime = $sources->whereIn('id', [$ids[0], $ids[1]])->all();
    $related = $sources->firstWhere('id', $ids[2]);
    if ($scenario === 'finding') { $finding->update(['status' => 'obsolete']); }
    if ($scenario === 'file') { $finding->sourceFile->update(['status' => 'obsolete']); }
    if ($scenario === 'source') { DB::table('translation_workbench_dynamic_sources')->where('id', $ids[2])->update(['status' => 'obsolete']); }
    if ($scenario === 'link') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    if (in_array($scenario, ['changed_key', 'ambiguous'], true)) {
        $other = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'changed-consumer'), 'translation_key' => 'ui.other', 'status' => 'open']);
        if ($scenario === 'changed_key') { DB::table('translation_workbench_dynamic_sources')->where('id', $ids[2])->update(['key_id' => $other->id]); }
        else { $link = (array) DB::table('translation_workbench_key_findings')->first(); unset($link['id']); $link['key_id'] = $other->id; DB::table('translation_workbench_key_findings')->insert($link); }
    }
    $events = TranslationWorkbenchTimelineEvent::count();
    $reviews = TranslationWorkbenchReview::count();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicSourceLinkWriter::class)->confirm($selected, $runtime, $related, []))->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews);
})->with(['finding', 'file', 'source', 'link', 'changed_key', 'ambiguous']);

it('reads authoritative source snapshots and rejects stale or forged source review payloads', function (string $scenario) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    if ($scenario === 'raw_values') {
        DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[2])->where('value_key', 'draft')->update(['native_label' => '  Raw draft  ']);
    }
    $finding = TranslationWorkbenchFinding::sole();
    $selected = (new ReflectionMethod($editor, 'selectedFinding'))->invoke($editor, $finding->id);
    $sources = collect((new ReflectionMethod($editor, 'dynamicReviewSources'))->invoke($editor, $finding->id));
    $runtime = $sources->whereIn('id', [$ids[0], $ids[1]])->values()->all();
    $related = $sources->firstWhere('id', $ids[2]);
    if ($scenario === 'provider_values') { DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[1])->update(['native_label' => 'Changed']); }
    if ($scenario === 'consumer_values') { DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[2])->update(['native_label' => 'Changed']); }
    if ($scenario === 'provider_obsolete') { DB::table('translation_workbench_dynamic_sources')->where('id', $ids[1])->update(['status' => 'obsolete']); }
    if ($scenario === 'provider_type') { DB::table('translation_workbench_dynamic_sources')->where('id', $ids[1])->update(['source_type' => 'collection']); }
    if ($scenario === 'provider_reference') { DB::table('translation_workbench_dynamic_sources')->where('id', $ids[1])->update(['source_reference' => 'Changed provider']); }
    if ($scenario === 'forged_values') { $related['values'][0]['native_label'] = 'Forged'; }
    if ($scenario === 'forged_provider') { $runtime[1]['id'] = $ids[3]; }
    if ($scenario === 'count') { $related['values_count'] = 999; }
    $before = DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson();
    $translations = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $reviews = TranslationWorkbenchReview::count();
    $writer = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicSourceLinkWriter::class);
    if (in_array($scenario, ['count', 'raw_values'], true)) {
        $writer->confirm($selected, $runtime, $related, []);
        $candidate = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->first();
        expect($candidate->candidate_values_count)->toBe(2)
            ->and(json_decode($candidate->candidate_values, true)['draft'])->toBe($scenario === 'raw_values' ? '  Raw draft  ' : 'Draft');
    } else {
        expect(fn () => $writer->confirm($selected, $runtime, $related, []))->toThrow(ValidationException::class)
            ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
            ->and(TranslationWorkbenchReview::count())->toBe($reviews);
    }
    expect(DB::table('translation_workbench_dynamic_source_values')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($translations);
})->with(['provider_values', 'consumer_values', 'provider_obsolete', 'provider_type', 'provider_reference', 'forged_values', 'forged_provider', 'count', 'raw_values']);


it('requires the source preview reviewed when opening the confirmation', function (string $change) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $originalUser = auth()->user();
    $reviews = TranslationWorkbenchReview::count();
    if ($change === 'consumer_value' || $change === 'provider_value') {
        DB::table('translation_workbench_dynamic_source_values')
            ->where('dynamic_source_id', $change === 'consumer_value' ? $ids[2] : $ids[0])
            ->where('value_key', 'draft')->update(['native_label' => 'Changed after opening']);
    }
    if ($change === 'provider_removed') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[0])->update(['status' => 'obsolete']);
    }
    if ($change === 'provider_added') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[3])->update(['source_type' => 'runtime_options']);
    }
    if ($change === 'provenance') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[2])->update(['source_reference' => 'changed-reference']);
    }
    if ($change === 'actor') {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 987654;
        $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(true);
        $this->actingAs($user);
    }
    if ($change === 'consumer') { $editor->dynamicSourceLinkRelatedSourceId = $ids[3]; }
    if ($change === 'tampered') { $editor->dynamicSourceLinkPreviewProof = 'forged-proof'; }
    if ($change === 'missing') { $editor->dynamicSourceLinkPreviewProof = null; }
    if ($change === 'closed') { $editor->dynamicSourceLinkConfirmModalOpen = false; }

    try {
        $editor->confirmDynamicSourceLink();
        test()->fail('An unreviewed source preview must not be confirmed.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('dynamicSourceLinkRelatedSourceId');
    }
    expect(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews);

    $this->actingAs($originalUser);
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->confirmDynamicSourceLink();
    expect(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBeGreaterThan(0)
        ->and($editor->dynamicSourceLinkPreviewProof)->toBeNull()
        ->and($editor->dynamicSourceLinkConfirmModalOpen)->toBeFalse();
})->with(['consumer_value', 'provider_value', 'provider_removed', 'provider_added', 'provenance', 'consumer', 'actor', 'tampered', 'missing', 'closed']);

it('rejects changed source link previews before unlinking', function (string $change) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    if ($change === 'values') {
        DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[2])->update(['native_label' => 'Changed']);
    }
    if ($change === 'second_link') {
        DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $ids[1])->update(['review_status' => 'rejected']);
    }
    if ($change === 'target') { $editor->dynamicSourceLinkRelatedSourceId = $ids[3]; }
    if ($change === 'proof') { $editor->dynamicSourceLinkPreviewProof = 'forged'; }
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $reviews = TranslationWorkbenchReview::count();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $editor->unlinkDynamicSourceLink())->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $editor->unlinkDynamicSourceLink();
    expect(DB::table('translation_workbench_dynamic_source_candidates')->where('status', 'active')->count())->toBe(0);
})->with(['values', 'second_link', 'target', 'proof']);

it('rechecks source ownership and values transactionally before unlinking', function (string $change) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $editor->confirmDynamicSourceLink();
    $editor->openDynamicSourceLinkConfirm($ids[2]);
    $preview = (new ReflectionMethod($editor, 'dynamicSourceLinkPreview'))->invoke($editor);
    if ($change === 'provider') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $ids[1])->update(['status' => 'obsolete']);
    }
    if ($change === 'consumer') {
        DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $ids[2])->update(['native_label' => 'Changed']);
    }
    if ($change === 'owner') {
        DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $ids[1])->update(['finding_id' => null]);
    }
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $reviews = TranslationWorkbenchReview::count();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicSourceLinkWriter::class)
        ->unlink($preview['finding'], $preview['runtime_sources'], $preview['related_source'], $preview['links']))->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['provider', 'consumer', 'owner']);

it('rejects link changes between preview and the transactional writer', function (string $action, string $change) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    if ($change !== 'added') {
        $editor->confirmDynamicSourceLink();
        $editor->openDynamicSourceLinkConfirm($ids[2]);
    }
    $preview = (new ReflectionMethod($editor, 'dynamicSourceLinkPreview'))->invoke($editor);
    $links = DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $ids[1]);
    if ($change === 'status') { $links->update(['review_status' => 'rejected']); }
    if ($change === 'values') { $links->update(['candidate_values' => json_encode(['draft' => 'Concurrent value'])]); }
    if ($change === 'removed') { $links->delete(); }
    if ($change === 'owner') { $links->update(['finding_id' => null]); }
    if ($change === 'added') { $editor->confirmDynamicSourceLink(); }
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $reviews = TranslationWorkbenchReview::count();
    $events = TranslationWorkbenchTimelineEvent::count();
    $writer = app(TranslationWorkbenchDynamicSourceLinkWriter::class);
    expect(fn () => $writer->{$action}($preview['finding'], $preview['runtime_sources'], $preview['related_source'], $preview['links']))
        ->toThrow(ValidationException::class, 'The links have changed since the source review.')
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['confirm', 'unlink'])->with(['status', 'values', 'removed', 'owner', 'added']);

it('rolls back runtime suggestions if their history cannot be written', function (bool $existing) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    if ($existing) {
        $editor->confirmDynamicSourceLink();
        DB::table('translation_workbench_dynamic_source_candidates')->update(['review_status' => 'pending']);
    }
    $runtime = DB::table('translation_workbench_dynamic_sources')->find($ids[0]);
    $consumer = DB::table('translation_workbench_dynamic_sources')->find($ids[2]);
    $consumer->key_suggested_key = TranslationWorkbenchKey::sole()->suggested_key;
    $consumer->finding_suggested_key = TranslationWorkbenchFinding::sole()->suggested_key;
    $before = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('record')->once()->andThrow(new RuntimeException('Resolver history unavailable'));
    $resolver = new \Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources($recorder);
    expect(fn () => (new ReflectionMethod($resolver, 'syncRuntimeSourceLink'))->invoke($resolver, $runtime, $consumer))
        ->toThrow(RuntimeException::class, 'Resolver history unavailable')
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with([false, true]);

it('rejects a source changed after runtime suggestion selection', function (string $change) {
    [, $ids] = workbenchSourceLinkEditor();
    $runtime = DB::table('translation_workbench_dynamic_sources')->find($ids[0]);
    $consumer = DB::table('translation_workbench_dynamic_sources')->find($ids[2]);
    $id = $change === 'provider' ? $ids[0] : $ids[2];
    DB::table('translation_workbench_dynamic_sources')->where('id', $id)->update(['status' => 'obsolete']);
    $events = TranslationWorkbenchTimelineEvent::count();
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    expect(fn () => (new ReflectionMethod($resolver, 'syncRuntimeSourceLink'))->invoke($resolver, $runtime, $consumer))
        ->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['consumer', 'provider']);

it('resolves discovery sources atomically and rejects stale selected inputs', function (string $scenario) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    Artisan::call('translation-workbench:classify-dynamic-values');
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $source = (new ReflectionMethod($resolver, 'unknownSources'))->invoke($resolver)->sole();
    $observation = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect(workbenchOptionObservation())->except(['source_end_line'])->all(),
        'fingerprint' => hash('sha256', 'atomic-discovery'), 'meta' => [],
        'suggested_key' => $source->key_suggested_key, 'status' => 'open',
        'options' => ['draft' => 'Draft', 'published' => 'Published'], 'options_count' => 2,
        'source_type' => 'hardcoded_array',
    ]);
    $discovery = DB::table('translation_workbench_option_discoveries')->find($observation->id);
    DB::table('translation_workbench_findings')->update(['dynamic_data_state' => 'unknown']);
    if ($scenario === 'source_changed') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->update(['status' => 'obsolete']);
    }
    if ($scenario === 'discovery_changed') {
        DB::table('translation_workbench_option_discoveries')->where('id', $discovery->id)->update(['options' => json_encode(['different' => 'Changed'])]);
    }
    if ($scenario === 'history_failure') {
        $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
        $recorder->shouldReceive('record')->once()->andThrow(new RuntimeException('Discovery history unavailable'));
        $resolver = new \Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources($recorder);
    }
    if ($scenario === 'value_failure_existing') {
        DB::table('translation_workbench_dynamic_source_values')->insert(['dynamic_source_id' => $source->id, 'value_key' => 'draft', 'native_label' => 'Original draft', 'status' => 'active']);
    }
    if (in_array($scenario, ['value_failure', 'value_failure_existing'], true)) {
        DB::statement("CREATE TRIGGER reject_discovery_value BEFORE INSERT ON translation_workbench_dynamic_source_values WHEN NEW.value_key = 'published' BEGIN SELECT RAISE(ABORT, 'Discovery value unavailable'); END");
    }
    if ($scenario === 'state_failure') {
        DB::statement("CREATE TRIGGER reject_structured_state BEFORE UPDATE ON translation_workbench_findings WHEN NEW.dynamic_data_state = 'structured' BEGIN SELECT RAISE(ABORT, 'Structured state unavailable'); END");
    }
    $tables = ['translation_workbench_dynamic_sources', 'translation_workbench_dynamic_source_values', 'translation_workbench_findings', 'translation_workbench_keys', 'translation_workbench_timeline_events', 'translation_workbench_dynamic_key_values'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $method = new ReflectionMethod($resolver, 'resolveDiscoverySource');
    if ($scenario === 'dry_run') {
        expect(Artisan::call('translation-workbench:resolve-unknown-dynamic-sources', ['--dry-run' => true]))->toBe(0)
            ->and($snapshot())->toBe($before);
    } elseif ($scenario === 'success') {
        expect(Artisan::call('translation-workbench:resolve-unknown-dynamic-sources'))->toBe(0)
            ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->value('classification'))->toBe('multi_hardcoded')
            ->and(DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->count())->toBe(2)
            ->and(DB::table('translation_workbench_findings')->where('id', $source->finding_id)->value('dynamic_data_state'))->toBe('structured')
            ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_unknown_resolved')->count())->toBe(1)
            ->and(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson())->toBe($before['translation_workbench_dynamic_key_values']);
        $after = $snapshot();
        expect(Artisan::call('translation-workbench:resolve-unknown-dynamic-sources'))->toBe(0)->and($snapshot())->toBe($after);
    } else {
        $expected = match ($scenario) {
            'history_failure' => RuntimeException::class,
            'value_failure', 'value_failure_existing', 'state_failure' => \Illuminate\Database\QueryException::class,
            default => ValidationException::class,
        };
        expect(fn () => $method->invoke($resolver, $source, $discovery))->toThrow($expected)
            ->and($snapshot())->toBe($before);
    }
})->with(['success', 'dry_run', 'history_failure', 'value_failure', 'value_failure_existing', 'state_failure', 'source_changed', 'discovery_changed']);


it('rechecks the active discovery owner after selection without changing data', function (string $change) {
    [$editor, $ids] = workbenchSourceLinkEditor();
    $resolver = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
    $source = (new ReflectionMethod($resolver, 'unknownSources'))->invoke($resolver)->firstWhere('id', $ids[2]);
    expect($source)->not->toBeNull();
    $observation = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery::create([
        ...collect(workbenchOptionObservation())->except(['source_end_line'])->all(),
        'fingerprint' => hash('sha256', 'owner-check'), 'meta' => [], 'status' => 'open',
        'options' => ['draft' => 'Draft'], 'options_count' => 1, 'source_type' => 'hardcoded_array',
    ]);
    $discovery = DB::table('translation_workbench_option_discoveries')->find($observation->id);
    $finding = TranslationWorkbenchFinding::sole();
    if ($change === 'finding') { $finding->update(['status' => 'obsolete']); }
    if ($change === 'file') { $finding->sourceFile->update(['status' => 'obsolete']); }
    if ($change === 'file_path') { $finding->sourceFile->update(['path' => 'resources/views/moved.blade.php']); }
    if ($change === 'key') { TranslationWorkbenchKey::sole()->update(['status' => 'obsolete']); }
    if ($change === 'link') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    if (in_array($change, ['different_key', 'ambiguous'], true)) {
        $key = TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'new-owner'), 'translation_key' => 'ui.different', 'status' => 'open']);
        if ($change === 'different_key') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
        DB::table('translation_workbench_key_findings')->insert(['finding_id' => $finding->id, 'key_id' => $key->id, 'status' => 'active']);
    }
    $tables = ['translation_workbench_dynamic_sources', 'translation_workbench_dynamic_source_values', 'translation_workbench_findings', 'translation_workbench_keys', 'translation_workbench_timeline_events', 'translation_workbench_dynamic_key_values', 'translation_workbench_dynamic_source_candidates'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table($table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(fn () => (new ReflectionMethod($resolver, 'resolveDiscoverySource'))->invoke($resolver, $source, $discovery))->toThrow(ValidationException::class)
        ->and($snapshot())->toBe($before);
    $runtime = DB::table('translation_workbench_dynamic_sources')->find($ids[0]);
    expect(fn () => (new ReflectionMethod($resolver, 'syncRuntimeSourceLink'))->invoke($resolver, $runtime, $source))->toThrow(ValidationException::class)
        ->and($snapshot())->toBe($before);
})->with(['finding', 'file', 'file_path', 'key', 'link', 'different_key', 'ambiguous']);

it('records concrete option value changes without changing translations or review snapshots', function (bool $failHistory) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class);
    $observation = workbenchOptionObservation();
    $service->process(collect([$observation]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $source = DB::table('translation_workbench_dynamic_sources')->sole();
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_options_reconciled')->sole();
    expect($event->old_values['values'])->toBe(['draft' => null, 'published' => null])
        ->and($event->new_values['values']['draft']['native_label'])->toBe('Draft')
        ->and($event->context['dynamic_source_id'])->toBe($source->id)
        ->and($event->context['option_discovery_id'])->toBe($source->option_discovery_id);
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => $source->key_id, 'value_key' => 'draft', 'locale' => 'de', 'value' => 'Geprüfter Entwurf', 'status' => 'active',
    ]);
    DB::table('translation_workbench_dynamic_source_candidates')->insert([
        'dynamic_source_id' => $source->id, 'candidate_source_type' => 'runtime_options', 'candidate_reference' => 'reviewed',
        'review_status' => 'confirmed', 'status' => 'active', 'candidate_values' => json_encode($observation['options']),
    ]);
    $tables = ['dynamic_sources', 'dynamic_source_values', 'dynamic_source_candidates', 'dynamic_key_values', 'timeline_events', 'findings', 'keys'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $observation['options'] = ['draft' => 'Updated draft', 'archived' => 'Archived'];
    $service->process(collect([$observation]), true);
    $before = $snapshot();
    expect(Artisan::call('translation-workbench:classify-dynamic-values', ['--dry-run' => true]))->toBe(0)->and($snapshot())->toBe($before);
    if ($failHistory) {
        DB::statement("CREATE TRIGGER reject_options_history BEFORE INSERT ON translation_workbench_timeline_events WHEN NEW.event_type = 'dynamic_source_options_reconciled' BEGIN SELECT RAISE(ABORT, 'Options history unavailable'); END");
        expect(fn () => Artisan::call('translation-workbench:classify-dynamic-values'))->toThrow(\Illuminate\Database\QueryException::class)
            ->and($snapshot())->toBe($before);
        return;
    }
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $events = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_options_reconciled')->orderBy('id')->get();
    expect($events)->toHaveCount(2);
    $change = $events->last();
    expect($change->old_values['values']['draft']['native_label'])->toBe('Draft')
        ->and($change->new_values['values']['draft']['native_label'])->toBe('Updated draft')
        ->and($change->old_values['values']['archived'])->toBeNull()
        ->and($change->new_values['values']['archived']['native_label'])->toBe('Archived')
        ->and($change->old_values['values']['published']['native_label'])->toBe('Published')
        ->and($change->new_values['values']['published'])->toBeNull()
        ->and($snapshot()['dynamic_key_values'])->toBe($before['dynamic_key_values'])
        ->and($snapshot()['dynamic_source_candidates'])->toBe($before['dynamic_source_candidates']);
    Artisan::call('translation-workbench:classify-dynamic-values');
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_options_reconciled')->count())->toBe(2);
    $service->process(collect([[...$observation, 'options' => [], 'source_type' => 'unresolved']]), true);
    Artisan::call('translation-workbench:classify-dynamic-values');
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_options_reconciled')->count())->toBe(2);
})->with([false, true]);

it('retires only unchanged unseen sources from the classification starting inventory', function (string $scenario) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
    $source = DB::table('translation_workbench_dynamic_sources')->sole();
    $runtime = (array) $source;
    unset($runtime['id']);
    $runtime['fingerprint'] = hash('sha256', 'retirement-runtime');
    $runtime['source_type'] = 'runtime_options';
    $runtimeId = DB::table('translation_workbench_dynamic_sources')->insertGetId($runtime);
    $initial = (new ReflectionMethod($command, 'retirementSourceSnapshot'))->invoke($command);
    expect($initial->keys()->all())->toBe([$source->id]);
    $seen = [];
    $newId = null;
    if ($scenario === 'new_source') {
        $new = (array) $source;
        unset($new['id']);
        $new['fingerprint'] = hash('sha256', 'created-after-classifier-start');
        $newId = DB::table('translation_workbench_dynamic_sources')->insertGetId($new);
    } elseif ($scenario === 'changed') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->update(['source_reference' => 'newer-provider']);
    } elseif ($scenario === 'deleted') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->delete();
    } elseif ($scenario === 'seen_with_new_fingerprint') {
        DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->update(['fingerprint' => hash('sha256', 'reclassified-identity')]);
        $seen = [(int) $source->id];
    }
    $tables = ['dynamic_sources', 'dynamic_source_values', 'timeline_events', 'keys'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $retire = fn () => DB::transaction(function () use ($command, $seen, $initial, $scenario) {
        // Earlier writes in the same classifier transaction must also roll back on stale input.
        if (in_array($scenario, ['changed', 'deleted'], true)) {
            DB::table('translation_workbench_keys')->update(['dynamic_data_state' => 'unstructured']);
        }
        return (new ReflectionMethod($command, 'obsoleteUnseenSources'))->invoke($command, $seen, $initial);
    });
    if (in_array($scenario, ['changed', 'deleted'], true)) {
        expect($retire)->toThrow(ValidationException::class, 'changed during classification')
            ->and($snapshot())->toBe($before);
    } else {
        expect($retire())->toBe($seen === [] ? 1 : 0)
            ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->value('status'))
            ->toBe($seen === [] ? 'obsolete' : $source->status);
    }
    expect(DB::table('translation_workbench_dynamic_sources')->where('id', $runtimeId)->value('status'))->toBe($source->status);
    if ($newId !== null) {
        expect(DB::table('translation_workbench_dynamic_sources')->where('id', $newId)->value('status'))->toBe($source->status);
    }
})->with(['unchanged', 'new_source', 'changed', 'deleted', 'seen_with_new_fingerprint']);

it('uses the starting inventory in the actual classifier retirement pass', function (bool $changeExisting) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    TranslationWorkbenchFinding::query()->update(['status' => 'obsolete']);
    $source = DB::table('translation_workbench_dynamic_sources')->sole();
    $injected = false;
    $newId = null;
    // Deterministically alter the inventory after its snapshot, during finding selection.
    // This tests command wiring and rollback, not simultaneous PostgreSQL connections.
    DB::listen(function ($query) use (&$injected, &$newId, $source, $changeExisting) {
        if ($injected || ! str_contains($query->sql, 'as "key_links"')) {
            return;
        }
        $injected = true;
        if ($changeExisting) {
            DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->update(['source_reference' => 'changed-after-snapshot']);
        } else {
            $new = (array) $source;
            unset($new['id']);
            $new['fingerprint'] = hash('sha256', 'late-source-in-command');
            $newId = DB::table('translation_workbench_dynamic_sources')->insertGetId($new);
        }
    });
    if ($changeExisting) {
        expect(fn () => Artisan::call('translation-workbench:classify-dynamic-values'))
            ->toThrow(ValidationException::class, 'changed during classification');
        expect((array) DB::table('translation_workbench_dynamic_sources')->sole())->toBe((array) $source);
    } else {
        expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
            ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->value('status'))->toBe('obsolete')
            ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $newId)->value('status'))->toBe($source->status);
    }
    expect($injected)->toBeTrue();
})->with([false, true]);

it('rejects stale classifier inputs before source values or history can change', function (string $change) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
    if ($change === 'unchanged_without_key') {
        DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']);
    }
    $row = (new ReflectionMethod($command, 'dynamicFindingRows'))->invoke($command)->sole();
    $classifications = (new ReflectionMethod($command, 'classificationsForRow'))->invoke($command, $row);
    match ($change) {
        'finding' => DB::table('translation_workbench_findings')->update(['status' => 'obsolete']),
        'expression' => DB::table('translation_workbench_findings')->update(['raw_expression' => '$changedLabel']),
        'file' => DB::table('translation_workbench_source_files')->update(['status' => 'obsolete']),
        'path' => DB::table('translation_workbench_source_files')->update(['path' => 'resources/views/moved.blade.php']),
        'key' => DB::table('translation_workbench_keys')->update(['status' => 'obsolete']),
        'link' => DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']),
        'options' => DB::table('translation_workbench_option_discoveries')->update(['options' => json_encode(['draft' => 'Changed'])]),
        'discovery' => DB::table('translation_workbench_option_discoveries')->update(['status' => 'obsolete']),
        default => null,
    };
    if ($change === 'ambiguous_key') {
        $key = TranslationWorkbenchKey::sole()->replicate();
        $key->fingerprint = hash('sha256', 'ambiguous-classifier-key');
        $key->translation_key = 'ui.another';
        $key->suggested_key = 'ui.another';
        $key->save();
        DB::table('translation_workbench_key_findings')->insert([
            'key_id' => $key->id, 'finding_id' => $row->finding_id, 'relation_type' => 'primary', 'status' => 'active',
        ]);
    }
    $tables = ['findings', 'source_files', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values', 'timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $validate = fn () => DB::transaction(fn () => (new ReflectionMethod($command, 'lockClassificationInput'))->invoke($command, $row, $classifications));
    if (str_starts_with($change, 'unchanged')) {
        $validate();
    } else {
        expect($validate)->toThrow(ValidationException::class, 'Classification input for finding '.$row->finding_id.' changed');
    }
    expect($snapshot())->toBe($before);
})->with(['finding', 'expression', 'file', 'path', 'key', 'link', 'options', 'discovery', 'ambiguous_key', 'unchanged', 'unchanged_without_key']);

it('rolls back the classifier command when selected input changes before its lock', function () {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $tables = ['findings', 'keys', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values', 'timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $injected = false;
    DB::listen(function ($query) use (&$injected) {
        if ($injected || ! str_starts_with($query->sql, 'select * from "translation_workbench_dynamic_sources" where "finding_id"')) {
            return;
        }
        $injected = true;
        DB::table('translation_workbench_option_discoveries')->update(['options' => json_encode(['draft' => 'Changed after selection'])]);
        DB::table('translation_workbench_dynamic_source_values')->update(['native_label' => 'Earlier transactional write']);
    });
    expect(fn () => Artisan::call('translation-workbench:classify-dynamic-values'))
        ->toThrow(ValidationException::class, 'changed during classification')
        ->and($injected)->toBeTrue()->and($snapshot())->toBe($before);
});

it('preserves unseen sources when their retirement context changed', function (string $change) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $source = DB::table('translation_workbench_dynamic_sources')->sole();
    // Already inactive at the start: unchanged inactivity must still permit retirement.
    DB::table('translation_workbench_findings')->update(['status' => 'obsolete']);
    $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
    $initial = (new ReflectionMethod($command, 'retirementSourceSnapshot'))->invoke($command);
    match ($change) {
        'reactivated' => DB::table('translation_workbench_findings')->update(['status' => 'active']),
        'expression' => DB::table('translation_workbench_findings')->update(['raw_expression' => '$newLabel']),
        'file' => DB::table('translation_workbench_source_files')->update(['path' => 'moved.blade.php']),
        'key' => DB::table('translation_workbench_keys')->update(['translation_key' => 'ui.new_owner']),
        'link' => DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']),
        'discovery' => DB::table('translation_workbench_option_discoveries')->update(['options' => json_encode(['new' => 'New'])]),
        'discovery_removed' => DB::table('translation_workbench_option_discoveries')->delete(),
        'external_state' => DB::table('translation_workbench_keys')->update(['dynamic_data_state' => 'unknown']),
        default => null,
    };
    $tables = ['findings', 'source_files', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values', 'timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $retire = fn () => DB::transaction(fn () => (new ReflectionMethod($command, 'obsoleteUnseenSources'))->invoke($command, [], $initial));
    if ($change === 'unchanged') {
        expect($retire())->toBe(1)->and(DB::table('translation_workbench_dynamic_sources')->where('id', $source->id)->value('status'))->toBe('obsolete');
    } else {
        expect($retire)->toThrow(ValidationException::class, 'Dynamic source '.$source->id.' changed during classification')
            ->and($snapshot())->toBe($before);
    }
})->with(['reactivated', 'expression', 'file', 'key', 'link', 'discovery', 'discovery_removed', 'external_state', 'unchanged']);

it('accounts for its own classification state without accepting other retirement context edits', function (bool $otherEdit) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
    $initial = (new ReflectionMethod($command, 'retirementSourceSnapshot'))->invoke($command);
    $row = (new ReflectionMethod($command, 'dynamicFindingRows'))->invoke($command)->sole();
    $before = DB::table('translation_workbench_dynamic_sources')->sole();
    $run = fn () => DB::transaction(function () use ($command, $initial, $row, $otherEdit) {
        (new ReflectionMethod($command, 'syncDynamicDataState'))->invoke($command, $row, 'unstructured');
        (new ReflectionMethod($command, 'retainOwnClassificationState'))->invoke($command, $initial, $row, 'unstructured');
        if ($otherEdit) {
            DB::table('translation_workbench_findings')->update(['raw_expression' => '$unexpected']);
        }
        return (new ReflectionMethod($command, 'obsoleteUnseenSources'))->invoke($command, [], $initial);
    });
    if ($otherEdit) {
        expect($run)->toThrow(ValidationException::class, 'changed during classification')
            ->and((array) DB::table('translation_workbench_dynamic_sources')->sole())->toBe((array) $before)
            ->and(DB::table('translation_workbench_keys')->sole()->dynamic_data_state)->toBe('structured');
    } else {
        expect($run())->toBe(1)->and(DB::table('translation_workbench_dynamic_sources')->sole()->status)->toBe('obsolete');
    }
})->with([false, true]);

it('retires unknown sources with atomic history while preserving translations and runtime sources', function (string $scenario, bool $failHistory) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $unknown = DB::table('translation_workbench_dynamic_sources')->sole();
    expect($unknown->source_type)->toBeNull()->and($unknown->classification)->toBe('unknown');
    foreach (['runtime_options', 'runtime_db_options'] as $type) {
        $runtime = (array) $unknown;
        unset($runtime['id']);
        $runtime['source_type'] = $type;
        $runtime['fingerprint'] = hash('sha256', 'preserved-'.$type);
        DB::table('translation_workbench_dynamic_sources')->insert($runtime);
    }
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => $unknown->key_id, 'value_key' => 'draft', 'locale' => 'de', 'value' => 'Geprüfter Entwurf', 'status' => 'active',
    ]);
    DB::table('translation_workbench_dynamic_source_candidates')->insert([
        'dynamic_source_id' => $unknown->id, 'candidate_source_type' => 'runtime_options', 'candidate_reference' => 'reviewed',
        'review_status' => 'confirmed', 'status' => 'active', 'candidate_values' => json_encode(['draft' => 'Draft']),
    ]);
    // Unknown but still selected must not be retired merely because its type is NULL.
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_obsoleted')->count())->toBe(0);
    if ($scenario === 'removed') {
        DB::table('translation_workbench_findings')->update(['status' => 'obsolete']);
    } else {
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
            ->process(collect([workbenchOptionObservation()]), true);
    }
    $tables = ['findings', 'keys', 'dynamic_sources', 'dynamic_source_values', 'dynamic_key_values', 'dynamic_source_candidates', 'timeline_events', 'event_types'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(Artisan::call('translation-workbench:classify-dynamic-values', ['--dry-run' => true]))->toBe(0)->and($snapshot())->toBe($before);
    if ($failHistory) {
        DB::statement("CREATE TRIGGER reject_retirement_history BEFORE INSERT ON translation_workbench_timeline_events WHEN NEW.event_type = 'dynamic_source_obsoleted' BEGIN SELECT RAISE(ABORT, 'Retirement history unavailable'); END");
        expect(fn () => Artisan::call('translation-workbench:classify-dynamic-values'))->toThrow(\Illuminate\Database\QueryException::class)
            ->and($snapshot())->toBe($before);
        return;
    }
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_obsoleted')->sole();
    expect($event->key_id)->toBe($unknown->key_id)->and($event->finding_id)->toBe($unknown->finding_id)
        ->and($event->old_values)->toBe(['status' => $unknown->status])->and($event->new_values)->toBe(['status' => 'obsolete'])
        ->and($event->context['dynamic_source_id'])->toBe($unknown->id)
        ->and($event->context['reason'])->toBe('not_classified_in_current_pass')
        ->and(DB::table('translation_workbench_dynamic_sources')->where('id', $unknown->id)->value('status'))->toBe('obsolete');
    if ($scenario === 'discovered') {
        expect(DB::table('translation_workbench_dynamic_sources')->where('classification', 'multi_hardcoded')->where('status', '<>', 'obsolete')->count())->toBe(1);
    }
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_obsoleted')->count())->toBe(1);
    $after = $snapshot();
    expect($after['dynamic_key_values'])->toBe($before['dynamic_key_values'])
        ->and($after['dynamic_source_candidates'])->toBe($before['dynamic_source_candidates'])
        ->and(DB::table('translation_workbench_dynamic_sources')->whereIn('source_type', ['runtime_options', 'runtime_db_options'])->where('status', $unknown->status)->count())->toBe(2);
})->with(['removed', 'discovered'])->with([false, true]);

it('stops the pipeline on a real classifier rollback and reports the current exception rather than an old success', function (bool $tracked) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $oldReport = storage_path('translation-workbench/translation-workbench-classify-dynamic-values.json');
    $oldContents = File::get($oldReport);
    $tables = ['findings', 'keys', 'dynamic_sources', 'dynamic_source_values', 'option_discoveries', 'timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $options = [];
    foreach (array_keys(RunTranslationWorkbench::executionDefaults()) as $option) {
        if (str_starts_with($option, '--skip-')) {
            $options[$option] = true;
        }
    }
    $options['--skip-dynamic-classification'] = false;
    $options['--skip-dynamic-resolution'] = false;
    $options['--skip-lang-file-export'] = false;
    $run = $tracked ? app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', [
        ...RunTranslationWorkbench::executionDefaults(), ...$options, 'started_from' => 'ui', 'user_id' => 1,
    ]) : null;
    if ($run) {
        $access = Mockery::mock(TranslationWorkbenchPipelineAccess::class)->makePartial();
        $access->shouldReceive('authorize')->twice()->with((string) $run->id);
        app()->instance(TranslationWorkbenchPipelineAccess::class, $access);
        $options['--pipeline-run-id'] = (string) $run->id;
    }
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $classifier = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
    $classifier->setLaravel(app());
    $application->addCommand($classifier);
    $laterCalls = [];
    foreach (['translation-workbench:resolve-unknown-dynamic-sources', 'translation-workbench:export-lang-files'] as $name) {
        $later = new Command($name);
        $later->setCode(function () use (&$laterCalls, $name) { $laterCalls[] = $name; return 0; });
        $application->addCommand($later);
    }
    $injected = false;
    DB::listen(function ($query) use (&$injected) {
        if ($injected || ! str_starts_with($query->sql, 'select * from "translation_workbench_dynamic_sources" where "finding_id"')) {
            return;
        }
        $injected = true;
        DB::table('translation_workbench_option_discoveries')->update(['options' => json_encode(['changed' => 'Changed after selection'])]);
    });
    $output = new BufferedOutput;
    expect($runner->run(new ArrayInput($options), $output))->toBe(1)
        ->and($injected)->toBeTrue()->and($laterCalls)->toBe([])->and($snapshot())->toBe($before)
        ->and(File::get($oldReport))->toBe($oldContents)
        ->and(app(TranslationWorkbenchPipelineContext::class)->current())->toBeNull()
        ->and(app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession::class)->active())->toBeFalse();
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench.json')), true);
    expect($report['summary']['steps_failed'])->toBe(1)->and($report['summary']['steps_succeeded'])->toBe(0)
        ->and($report['steps'])->toHaveCount(1)
        ->and($report['steps'][0]['error']['class'])->toBe(ValidationException::class)
        ->and($report['steps'][0]['error']['message'])->toContain('changed during classification')
        ->and($report['steps'][0]['report'])->toBe(['path' => $oldReport, 'exists' => true, 'used' => false, 'reason' => 'step_threw_exception']);
    if ($run) {
        expect($run->refresh()->status)->toBe('failed')->and($run->error_message)->toContain('changed during classification')
            ->and($run->steps()->where('status', 'failed')->count())->toBe(1)
            ->and($run->steps()->where('status', 'pending')->count())->toBe(2);
    }
})->with([false, true]);

it('records nonzero pipeline failures without attributing an unchanged report to the current step', function (string $reportMode) {
    $name = 'translation-workbench:classify-dynamic-values';
    $path = storage_path('translation-workbench/translation-workbench-classify-dynamic-values.json');
    File::ensureDirectoryExists(dirname($path));
    $old = json_encode(['command' => $name, 'generated_at' => '2020-01-01T00:00:00Z', 'summary' => ['old_success' => true]]);
    if ($reportMode !== 'missing') {
        File::put($path, $old);
    }
    $options = [];
    foreach (array_keys(RunTranslationWorkbench::executionDefaults()) as $option) {
        if (str_starts_with($option, '--skip-')) { $options[$option] = true; }
    }
    $options['--skip-dynamic-classification'] = false;
    $options['--skip-dynamic-resolution'] = false;
    $options['--skip-lang-file-export'] = false;
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', [
        ...RunTranslationWorkbench::executionDefaults(), ...$options, 'started_from' => 'ui', 'user_id' => 1,
    ]);
    $access = Mockery::mock(TranslationWorkbenchPipelineAccess::class)->makePartial();
    $access->shouldReceive('authorize')->twice()->with((string) $run->id);
    app()->instance(TranslationWorkbenchPipelineAccess::class, $access);
    $options['--pipeline-run-id'] = (string) $run->id;
    $runner = new RunTranslationWorkbench;
    $runner->setLaravel(app());
    $application = new Application;
    $application->addCommand($runner);
    $command = new Command($name);
    $command->setCode(function () use ($reportMode, $path, $name) {
        if ($reportMode === 'fresh') {
            File::put($path, json_encode(['command' => $name, 'generated_at' => now()->toISOString(), 'summary' => ['conflicting_findings' => [42]]]));
        } elseif ($reportMode === 'invalid') {
            File::put($path, '{invalid report');
        }
        return 7;
    });
    $application->addCommand($command);
    $output = new BufferedOutput;
    expect($runner->run(new ArrayInput($options), $output))->toBe(7);
    $message = 'Translation Workbench pipeline stopped at "'.$name.'" (exit code 7).';
    expect($run->refresh()->status)->toBe('failed')->and($run->exit_code)->toBe(7)->and($run->error_message)->toBe($message)
        ->and($run->steps()->where('status', 'pending')->count())->toBe(2);
    $step = $run->steps()->where('status', 'failed')->sole();
    expect($step->exit_code)->toBe(7)->and($step->error_message)->toBe($message);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench.json')), true);
    $stepReport = $report['steps'][0]['report'];
    expect($report['steps'])->toHaveCount(1)->and($report['steps'][0]['error'])->toBe(['exit_code' => 7, 'message' => $message])
        ->and($step->meta['report'])->toBe($stepReport);
    if ($reportMode === 'fresh') {
        expect($stepReport['used'])->toBeTrue()->and($stepReport['summary'])->toBe(['conflicting_findings' => [42]]);
    } else {
        expect($stepReport['used'])->toBeFalse()->and($stepReport)->not->toHaveKey('summary');
        if ($reportMode === 'stale') {
            expect($stepReport['reason'])->toBe('report_not_changed')->and(File::get($path))->toBe($old);
        } elseif ($reportMode === 'missing') {
            expect($stepReport['exists'])->toBeFalse()->and($stepReport['reason'])->toBe('report_unavailable');
        } else {
            expect($stepReport['readable'])->toBeFalse()->and($stepReport['error'])->not->toBeEmpty();
        }
    }
    expect(app(TranslationWorkbenchPipelineContext::class)->current())->toBeNull()
        ->and(app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanSession::class)->active())->toBeFalse();
})->with(['stale', 'missing', 'fresh', 'invalid']);

it('reports dynamic command counts and distinguishes dry runs from persisted changes', function (string $command) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $resolver = str_contains($command, 'resolve-unknown');
    if ($resolver) { expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0); }
    app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptionSynchronizer::class)
        ->process(collect([workbenchOptionObservation()]), true);
    $tables = ['findings', 'keys', 'dynamic_sources', 'dynamic_source_values', 'timeline_events'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $report = fn () => json_decode(File::get(storage_path('translation-workbench/'.str_replace(':', '-', $command).'.json')), true);
    expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)->and($snapshot())->toBe($before);
    $dry = $report();
    expect($dry['summary']['dry_run'])->toBeTrue()->and($dry['summary']['exit_code'])->toBe(0)
        ->and($dry['summary'][$resolver ? 'unknown_sources' : 'dynamic_findings'])->toBe(1)
        ->and($dry['summary']['source_values_created'])->toBe(0)->and($dry['summary']['timeline_events_created'])->toBe(0);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(Artisan::call($command))->toBe(0);
    $written = $report();
    expect($written['summary']['dry_run'])->toBeFalse()->and($written['summary']['exit_code'])->toBe(0)
        ->and($written['summary'][$resolver ? 'sources_resolved' : 'sources_created'])->toBe(1)
        ->and($written['summary']['source_values_created'])->toBe(2)
        ->and($written['summary']['timeline_events_created'])->toBe(TranslationWorkbenchTimelineEvent::count() - $events)
        ->and($written['summary']['timeline_events_created'])->toBeGreaterThan(0)
        ->and($written['raw_data'])->toHaveKeys(['files', 'found', 'files_found']);
})->with(['translation-workbench:classify-dynamic-values', 'translation-workbench:resolve-unknown-dynamic-sources']);

it('writes an explicit missing schema report for dynamic commands', function (string $command) {
    Schema::drop('translation_workbench_dynamic_source_values');
    expect(Artisan::call($command))->toBe(1);
    $report = json_decode(File::get(storage_path('translation-workbench/'.str_replace(':', '-', $command).'.json')), true);
    expect($report['summary'])->toBe(['dry_run' => false, 'exit_code' => 1, 'error' => 'missing_required_tables']);
})->with(['translation-workbench:classify-dynamic-values', 'translation-workbench:resolve-unknown-dynamic-sources']);

it('preserves reviewed and inactive dynamic candidates during repeated discovery', function (string $review, string $status) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0)
        ->and(Artisan::call('translation-workbench:discover-dynamic-source-candidates'))->toBe(0);
    DB::table('translation_workbench_dynamic_source_candidates')->update([
        'review_status' => $review, 'status' => $status, 'candidate_values' => json_encode(['reviewed' => 'Reviewed value']),
    ]);
    $before = DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(Artisan::call('translation-workbench:discover-dynamic-source-candidates'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
    expect($report['summary']['candidates_preserved'])->toBe(1)->and($report['summary']['candidates_updated'])->toBe(0);
})->with([['confirmed', 'active'], ['rejected', 'active'], ['unlinked', 'inactive'], ['pending', 'inactive']]);

it('records dynamic candidate changes atomically and avoids duplicate unchanged history', function (string $failure) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = 'translation-workbench:discover-dynamic-source-candidates';
    $tables = ['dynamic_source_candidates', 'timeline_events', 'event_types', 'dynamic_sources'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    if ($failure !== 'create') {
        expect(Artisan::call($command))->toBe(0);
        $candidate = DB::table('translation_workbench_dynamic_source_candidates')->sole();
        $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_candidate_discovered')->sole();
        expect($event->context['candidate_id'])->toBe($candidate->id)->and($event->old_values)->toBeNull();
        $before = $snapshot();
        expect(Artisan::call($command))->toBe(0)->and($snapshot())->toBe($before);
        DB::table('translation_workbench_dynamic_source_candidates')->update(['confidence' => 'high']);
    }
    $before = $snapshot();
    expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)->and($snapshot())->toBe($before);
    if ($failure !== 'none') {
        DB::statement("CREATE TRIGGER reject_candidate_history BEFORE INSERT ON translation_workbench_timeline_events WHEN NEW.event_type IN ('dynamic_source_candidate_discovered', 'dynamic_source_candidate_updated') BEGIN SELECT RAISE(ABORT, 'Candidate history unavailable'); END");
        expect(fn () => Artisan::call($command))->toThrow(\Illuminate\Database\QueryException::class)->and($snapshot())->toBe($before);
    } else {
        expect(Artisan::call($command))->toBe(0);
        $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_candidate_updated')->sole();
        expect($event->old_values['confidence'])->toBe('high')->and($event->new_values['confidence'])->toBe('low');
        $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
        expect($report['summary']['candidates_updated'])->toBe(1)->and($report['summary']['timeline_events_created'])->toBe(1);
    }
})->with(['create', 'update', 'none']);

it('selects only current candidate source owners without discarding keyless sources', function (string $change) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    match ($change) {
        'finding' => DB::table('translation_workbench_findings')->update(['status' => 'obsolete']),
        'file' => DB::table('translation_workbench_source_files')->update(['status' => 'obsolete']),
        'key' => DB::table('translation_workbench_keys')->update(['status' => 'obsolete']),
        'link' => DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']),
        'keyless' => DB::table('translation_workbench_dynamic_sources')->update(['key_id' => null]),
        'standalone' => DB::table('translation_workbench_dynamic_sources')->update(['key_id' => null, 'finding_id' => null]),
        default => null,
    };
    if ($change === 'keyless') { DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']); }
    $before = DB::table('translation_workbench_dynamic_sources')->get()->toJson();
    expect(Artisan::call('translation-workbench:discover-dynamic-source-candidates'))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(in_array($change, ['keyless', 'standalone', 'unchanged']) ? 1 : 0)
        ->and(DB::table('translation_workbench_dynamic_sources')->get()->toJson())->toBe($before);
})->with(['finding', 'file', 'key', 'link', 'keyless', 'standalone', 'unchanged']);

it('rejects competing candidate keys before any writes and reports their identities', function (bool $dryRun) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $key = TranslationWorkbenchKey::sole()->replicate();
    $key->fingerprint = hash('sha256', 'candidate-competing-key');
    $key->translation_key = $key->suggested_key = 'ui.another';
    $key->save();
    $finding = TranslationWorkbenchFinding::sole();
    DB::table('translation_workbench_key_findings')->insert(['key_id' => $key->id, 'finding_id' => $finding->id, 'relation_type' => 'primary', 'status' => 'active']);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(Artisan::call('translation-workbench:discover-dynamic-source-candidates', ['--dry-run' => $dryRun]))->toBe(1)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
    expect($report['summary']['error'])->toBe('ambiguous_active_key_assignments')
        ->and($report['plan_summary']['key_assignment_conflicts'][0]['finding_id'])->toBe($finding->id)
        ->and($report['plan_summary']['key_assignment_conflicts'][0]['key_ids'])->toContain($key->id);
})->with([false, true]);

it('rechecks candidate ownership and suggestion context after selection', function (string $change) {
    File::put(base_path('resources/views/options.blade.php'), "@foreach (\$statusOptions as \$key => \$label)\n{{ __(\$label) }}\n@endforeach");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = app(\Gunreip\TranslationWorkbench\Console\DiscoverDynamicSourceCandidates::class);
    $source = (new ReflectionMethod($command, 'unresolvedSources'))->invoke($command)->sole();
    $candidate = (new ReflectionMethod($command, 'candidatesForSource'))->invoke($command, $source)[0];
    match ($change) {
        'finding' => DB::table('translation_workbench_findings')->update(['status' => 'obsolete']),
        'file' => DB::table('translation_workbench_source_files')->update(['status' => 'obsolete']),
        'key' => DB::table('translation_workbench_keys')->update(['status' => 'obsolete']),
        'link' => DB::table('translation_workbench_key_findings')->update(['status' => 'obsolete']),
        'suggestion' => DB::table('translation_workbench_keys')->update(['suggested_key' => 'ui.changed']),
        'path' => DB::table('translation_workbench_source_files')->update(['path' => 'resources/views/moved.blade.php']),
    };
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => (new ReflectionMethod($command, 'syncCandidate'))->invoke($command, $source, $candidate))->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['finding', 'file', 'key', 'link', 'suggestion', 'path']);

it('keeps raw model candidate values and explicitly reports bounded unverified samples', function (int $count) {
    Schema::create('person_documents', function ($table) { $table->id(); $table->text('type')->nullable(); });
    $values = ['  raw_label  ', 'iPhone XML', '0'];
    for ($i = count($values); $i < $count; $i++) { $values[] = 'value_'.str_pad((string) $i, 3, '0', STR_PAD_LEFT); }
    DB::table('person_documents')->insert(array_map(fn ($value) => ['type' => $value], [...$values, null, $values[0]]));
    File::put(base_path('resources/views/document.blade.php'), '{{ __($document->type) }}');
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = 'translation-workbench:discover-dynamic-source-candidates';
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    expect(Artisan::call($command))->toBe(0);
    $candidate = DB::table('translation_workbench_dynamic_source_candidates')->sole();
    $stored = json_decode($candidate->candidate_values, true);
    $meta = json_decode($candidate->meta, true)['value_observation'];
    expect($stored['  raw_label  '])->toBe('  raw_label  ')->and($stored['iPhone XML'])->toBe('iPhone XML')
        ->and($stored[0])->toBe('0')->and(count($stored))->toBe(min(200, $count))
        ->and($candidate->review_status)->toBe('pending')
        ->and($meta)->toMatchArray(['table' => 'person_documents', 'column' => 'type', 'limit' => 200,
            'truncated' => $count > 200, 'source_verified' => false, 'label_origin' => 'raw_database_value', 'status' => 'observed']);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
    expect($report['summary']['truncated_value_samples'])->toBe($count > 200 ? 1 : 0);
    if ($count > 200) { expect(Artisan::output())->toContain('unverified sample'); }
    expect(Artisan::call($command))->toBe(0)->and(TranslationWorkbenchTimelineEvent::count())->toBe($events + 1);
})->with([3, 200, 201]);

it('updates the same pending model proposal when observed values appear and disappear', function () {
    Schema::create('person_documents', function ($table) { $table->id(); $table->text('type')->nullable(); });
    File::put(base_path('resources/views/document.blade.php'), '{{ __($document->type) }}');
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = 'translation-workbench:discover-dynamic-source-candidates';
    expect(Artisan::call($command))->toBe(0);
    $original = DB::table('translation_workbench_dynamic_source_candidates')->sole();
    expect($original->candidate_source_type)->toBe('model_column_unresolved');
    foreach ([true, false] as $hasValues) {
        if ($hasValues) { DB::table('person_documents')->insert(['type' => 'Raw value']); }
        else { DB::table('person_documents')->delete(); }
        $before = DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson();
        $events = TranslationWorkbenchTimelineEvent::count();
        expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)
            ->and(DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson())->toBe($before)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
        expect(Artisan::call($command))->toBe(0);
        $current = DB::table('translation_workbench_dynamic_source_candidates')->sole();
        expect($current->id)->toBe($original->id)
            ->and($current->candidate_source_type)->toBe($hasValues ? 'model_column' : 'model_column_unresolved')
            ->and($current->candidate_values_count)->toBe($hasValues ? 1 : 0)
            ->and(json_decode($current->candidate_values ?? 'null', true))->toBe($hasValues ? ['Raw value' => 'Raw value'] : null);
        $event = TranslationWorkbenchTimelineEvent::where('event_type', 'dynamic_source_candidate_updated')->orderByDesc('id')->first();
        expect($event->context['candidate_id'])->toBe($original->id)
            ->and($event->old_values['candidate_source_type'])->toBe($hasValues ? 'model_column_unresolved' : 'model_column')
            ->and($event->new_values['candidate_source_type'])->toBe($current->candidate_source_type)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events + 1);
        expect(Artisan::call($command))->toBe(0)->and(TranslationWorkbenchTimelineEvent::count())->toBe($events + 1);
    }
});

it('preserves model candidate decisions across changes in value availability', function (string $review, string $status, bool $initialValues) {
    Schema::create('person_documents', function ($table) { $table->id(); $table->text('type')->nullable(); });
    if ($initialValues) { DB::table('person_documents')->insert(['type' => 'Reviewed value']); }
    File::put(base_path('resources/views/document.blade.php'), '{{ __($document->type) }}');
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = 'translation-workbench:discover-dynamic-source-candidates';
    expect(Artisan::call($command))->toBe(0);
    DB::table('translation_workbench_dynamic_source_candidates')->update(['review_status' => $review, 'status' => $status]);
    $before = DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    if ($initialValues) { DB::table('person_documents')->delete(); }
    else { DB::table('person_documents')->insert(['type' => 'New value']); }
    expect(Artisan::call($command))->toBe(0)
        ->and(DB::table('translation_workbench_dynamic_source_candidates')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
    expect($report['summary']['candidates_preserved'])->toBe(1)->and($report['summary']['candidates_created'])->toBe(0);
})->with([['confirmed', 'active'], ['rejected', 'active'], ['pending', 'inactive']])->with([false, true]);

it('reports duplicate model proposal states before any candidate writes including dry runs', function (bool $dryRun) {
    Schema::create('person_documents', function ($table) { $table->id(); $table->text('type')->nullable(); });
    File::put(base_path('resources/views/document.blade.php'), '{{ __($document->type) }}');
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    expect(Artisan::call('translation-workbench:classify-dynamic-values'))->toBe(0);
    $command = 'translation-workbench:discover-dynamic-source-candidates';
    expect(Artisan::call($command))->toBe(0);
    $original = DB::table('translation_workbench_dynamic_source_candidates')->sole();
    // The first source would be updated, but a later source has conflicting candidates.
    DB::table('translation_workbench_dynamic_source_candidates')->update(['confidence' => 'high']);
    $source = (array) DB::table('translation_workbench_dynamic_sources')->sole();
    unset($source['id']);
    $source['fingerprint'] = hash('sha256', 'later-conflicting-source');
    $sourceId = DB::table('translation_workbench_dynamic_sources')->insertGetId($source);
    $duplicate = (array) $original;
    unset($duplicate['id']);
    $duplicate['dynamic_source_id'] = $sourceId;
    $firstId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId($duplicate);
    $duplicate['candidate_source_type'] = 'model_column';
    $duplicateId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId($duplicate);
    $tables = ['dynamic_sources', 'dynamic_source_candidates', 'timeline_events', 'event_types'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(Artisan::call($command, ['--dry-run' => $dryRun]))->toBe(1)->and($snapshot())->toBe($before)
        ->and(Artisan::output())->toContain('Candidate IDs: '.$firstId.', '.$duplicateId);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
    expect($report['summary'])->toMatchArray(['dry_run' => $dryRun, 'exit_code' => 1,
        'error' => 'ambiguous_candidate_identities', 'candidate_conflicts' => 1,
        'candidates_created' => 0, 'candidates_updated' => 0, 'timeline_events_created' => 0])
        ->and($report['plan_summary']['candidate_identity_conflicts'])->toBe([[
            'dynamic_source_id' => $sourceId, 'candidate_reference' => 'document.type', 'candidate_ids' => [$firstId, $duplicateId],
        ]]);
    // Even after planning, the locked write must independently reject the conflict.
    $instance = app(\Gunreip\TranslationWorkbench\Console\DiscoverDynamicSourceCandidates::class);
    $selected = (new ReflectionMethod($instance, 'unresolvedSources'))->invoke($instance, $sourceId)->sole();
    $candidate = (new ReflectionMethod($instance, 'candidatesForSource'))->invoke($instance, $selected)[0];
    expect(fn () => (new ReflectionMethod($instance, 'syncCandidate'))->invoke($instance, $selected, $candidate))
        ->toThrow(LogicException::class, 'candidate IDs: '.$firstId.', '.$duplicateId)->and($snapshot())->toBe($before);
})->with([false, true]);

it('reports missing candidate schema without a stale success summary', function (bool $dryRun) {
    Schema::drop('translation_workbench_dynamic_source_candidates');
    expect(Artisan::call('translation-workbench:discover-dynamic-source-candidates', ['--dry-run' => $dryRun]))->toBe(1);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-discover-dynamic-source-candidates.json')), true);
    expect($report['summary'])->toBe(['dry_run' => $dryRun, 'exit_code' => 1, 'error' => 'missing_required_tables']);
})->with([false, true]);

it('keeps historical shared key suggestions across partial scans until their finding or file is retired', function (string $retirement) {
    DB::connection()->getPdo()->sqliteCreateAggregate('array_agg',
        static fn ($context, $row, $value) => ($context ?? '').','.$value,
        static fn ($context, $row) => '{'.trim($context ?? '', ',').'}', 1);
    $editor = workbenchBulkEditor();
    $editor->confirmBulkEqualizeTranslationKey();
    DB::table('translation_workbench_reviews')->update(['reviewed_at' => now()->subDays(3)]);
    File::put(base_path('resources/views/third.blade.php'), "{{ __('Welcome aboard') }}");
    File::put(base_path('resources/views/unrelated.blade.php'), "{{ __('Unrelated recent scan') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $fileId = DB::table('translation_workbench_source_files')->where('path', 'resources/views/third.blade.php')->value('id');
    $finding = TranslationWorkbenchFinding::where('source_file_id', $fileId)->sole();
    $command = 'translation-workbench:detect-shared-key-candidates';
    expect(Artisan::call($command))->toBe(0);
    $candidate = DB::table('translation_workbench_shared_key_candidates')->where('finding_id', $finding->id)->sole();
    expect($candidate->status)->toBe('pending');
    // A recent unrelated observation must not hide this still-active older finding.
    DB::table('translation_workbench_findings')->where('id', $finding->id)->update([
        'first_seen_at' => now()->subDays(2), 'last_seen_at' => now()->subDay(),
    ]);
    if ($retirement === 'finding') { DB::table('translation_workbench_findings')->where('id', $finding->id)->update(['status' => 'obsolete']); }
    if ($retirement === 'file') { DB::table('translation_workbench_source_files')->where('id', $fileId)->update(['status' => 'obsolete']); }
    $tables = ['findings', 'keys', 'key_findings', 'reviews', 'shared_key_candidates'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)->and($snapshot())->toBe($before);
    expect(Artisan::call($command))->toBe(0);
    $current = DB::table('translation_workbench_shared_key_candidates')->where('finding_id', $finding->id)->sole();
    expect($current->id)->toBe($candidate->id)->and($current->status)->toBe($retirement === 'none' ? 'pending' : 'obsolete');
    $after = $snapshot();
    foreach (['findings', 'keys', 'key_findings', 'reviews'] as $table) {
        expect($after[$table])->toBe($before[$table]);
    }
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-detect-shared-key-candidates.json')), true);
    expect($report['summary']['candidate_rows'])->toBe($retirement === 'none' ? 1 : 0)
        ->and($report['summary']['obsolete_marked'])->toBe($retirement === 'none' ? 0 : 1);
})->with(['none', 'finding', 'file']);

it('uses historical shared decisions only while their reviewed context remains current', function (string $change) {
    DB::connection()->getPdo()->sqliteCreateAggregate('array_agg',
        static fn ($context, $row, $value) => ($context ?? '').','.$value,
        static fn ($context, $row) => '{'.trim($context ?? '', ',').'}', 1);
    $editor = workbenchBulkEditor();
    $editor->confirmBulkEqualizeTranslationKey();
    DB::table('translation_workbench_reviews')->update(['reviewed_at' => now()->subDays(3)]);
    $reviewed = TranslationWorkbenchFinding::orderBy('id')->first();
    $keyId = DB::table('translation_workbench_key_findings')->where('finding_id', $reviewed->id)->value('key_id');
    File::put(base_path('resources/views/third.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $command = 'translation-workbench:detect-shared-key-candidates';
    expect(Artisan::call($command))->toBe(0)->and(DB::table('translation_workbench_shared_key_candidates')->sole()->status)->toBe('pending');
    match ($change) {
        'finding' => DB::table('translation_workbench_findings')->where('id', $reviewed->id)->update(['status' => 'obsolete']),
        'file' => DB::table('translation_workbench_source_files')->where('id', $reviewed->source_file_id)->update(['status' => 'obsolete']),
        'key' => DB::table('translation_workbench_keys')->where('id', $keyId)->update(['status' => 'obsolete']),
        'link' => DB::table('translation_workbench_key_findings')->where('finding_id', $reviewed->id)->update(['status' => 'obsolete']),
        'rename' => DB::table('translation_workbench_keys')->where('translation_key', 'ui.shared.welcome')->update(['translation_key' => 'ui.shared.changed']),
        'literal' => DB::table('translation_workbench_findings')->update(['literal_text' => 'Changed text']),
        'missing_snapshot' => DB::table('translation_workbench_reviews')->where('finding_id', $reviewed->id)->update(['new_values' => null]),
        default => null,
    };
    if (in_array($change, ['competing_key', 'obsolete_competing_key'], true)) {
        $key = TranslationWorkbenchKey::findOrFail($keyId)->replicate();
        $key->fingerprint = hash('sha256', 'shared-competing-key');
        $key->translation_key = 'ui.competing';
        $key->status = $change === 'obsolete_competing_key' ? 'obsolete' : 'active';
        $key->save();
        DB::table('translation_workbench_key_findings')->insert(['key_id' => $key->id, 'finding_id' => $reviewed->id, 'relation_type' => 'primary', 'status' => 'active']);
    }
    $tables = ['findings', 'source_files', 'keys', 'key_findings', 'reviews'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $candidateBefore = DB::table('translation_workbench_shared_key_candidates')->get()->toJson();
    expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)
        ->and(DB::table('translation_workbench_shared_key_candidates')->get()->toJson())->toBe($candidateBefore);
    expect(Artisan::call($command))->toBe(0)->and($snapshot())->toBe($before);
    $valid = in_array($change, ['unchanged', 'obsolete_competing_key'], true);
    expect(DB::table('translation_workbench_shared_key_candidates')->sole()->status)->toBe($valid ? 'pending' : 'obsolete');
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-detect-shared-key-candidates.json')), true);
    expect($report['summary']['shared_decision_groups'])->toBe($valid ? 1 : 0);
})->with(['unchanged', 'finding', 'file', 'key', 'link', 'rename', 'literal', 'missing_snapshot', 'competing_key', 'obsolete_competing_key']);

function sharedSuggestionFixture(): TranslationWorkbenchFinding
{
    DB::connection()->getPdo()->sqliteCreateAggregate('array_agg',
        static fn ($context, $row, $value) => ($context ?? '').','.$value,
        static fn ($context, $row) => '{'.trim($context ?? '', ',').'}', 1);
    $editor = workbenchBulkEditor();
    $editor->confirmBulkEqualizeTranslationKey();
    DB::table('translation_workbench_reviews')->update(['reviewed_at' => now()->subDays(3)]);
    File::put(base_path('resources/views/third.blade.php'), "{{ __('Welcome aboard') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $fileId = DB::table('translation_workbench_source_files')->where('path', 'resources/views/third.blade.php')->value('id');
    return TranslationWorkbenchFinding::where('source_file_id', $fileId)->sole();
}

it('preserves reviewed shared suggestion snapshots on repeated detection', function (string $status) {
    sharedSuggestionFixture();
    $command = 'translation-workbench:detect-shared-key-candidates';
    expect(Artisan::call($command))->toBe(0);
    DB::table('translation_workbench_shared_key_candidates')->update([
        'status' => $status, 'confidence' => 'manual', 'meta' => json_encode(['reviewed' => true]),
    ]);
    $before = DB::table('translation_workbench_shared_key_candidates')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(Artisan::call($command))->toBe(0)
        ->and(DB::table('translation_workbench_shared_key_candidates')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-detect-shared-key-candidates.json')), true);
    expect($report['summary']['reviewed_candidates_preserved'])->toBe(1)
        ->and($report['summary']['timeline_events_created'])->toBe(0);
})->with(['confirmed', 'rejected', 'applied']);

it('records shared suggestion lifecycle without duplicate heartbeat history', function () {
    $finding = sharedSuggestionFixture();
    $command = 'translation-workbench:detect-shared-key-candidates';
    expect(Artisan::call($command))->toBe(0);
    $candidate = DB::table('translation_workbench_shared_key_candidates')->sole();
    $created = TranslationWorkbenchTimelineEvent::where('event_type', 'shared_key_candidate_discovered')->sole();
    expect($created->context['candidate_id'])->toBe($candidate->id)->and($created->old_values)->toBeNull();
    $events = TranslationWorkbenchTimelineEvent::count();
    $this->travel(1)->hours();
    expect(Artisan::call($command))->toBe(0)->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $refreshed = DB::table('translation_workbench_shared_key_candidates')->sole();
    expect($refreshed->first_seen_at)->toBe($candidate->first_seen_at)
        ->and($refreshed->created_at)->toBe($candidate->created_at)->and($refreshed->last_seen_at)->not->toBe($candidate->last_seen_at);
    DB::table('translation_workbench_shared_key_candidates')->update(['confidence' => 'low']);
    expect(Artisan::call($command))->toBe(0);
    $updated = TranslationWorkbenchTimelineEvent::where('event_type', 'shared_key_candidate_updated')->sole();
    expect($updated->old_values['confidence'])->toBe('low')->and($updated->new_values['confidence'])->toBe('medium');
    $finding->update(['status' => 'obsolete']);
    expect(Artisan::call($command))->toBe(0);
    $retired = TranslationWorkbenchTimelineEvent::where('event_type', 'shared_key_candidate_obsoleted')->sole();
    expect($retired->old_values['status'])->toBe('pending')->and($retired->new_values['status'])->toBe('obsolete');
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(Artisan::call($command))->toBe(0)->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $finding->update(['status' => 'active']);
    expect(Artisan::call($command))->toBe(0)
        ->and(DB::table('translation_workbench_shared_key_candidates')->sole()->id)->toBe($candidate->id)
        ->and(DB::table('translation_workbench_shared_key_candidates')->sole()->status)->toBe('pending');
    $reactivated = TranslationWorkbenchTimelineEvent::where('event_type', 'shared_key_candidate_updated')->orderByDesc('id')->first();
    expect($reactivated->old_values['status'])->toBe('obsolete')->and($reactivated->new_values['status'])->toBe('pending');
    $this->travelBack();
});

it('rolls back shared suggestion changes when their history cannot be recorded', function (string $operation) {
    $finding = sharedSuggestionFixture();
    $command = 'translation-workbench:detect-shared-key-candidates';
    if ($operation !== 'discovered') { expect(Artisan::call($command))->toBe(0); }
    if ($operation === 'updated') { DB::table('translation_workbench_shared_key_candidates')->update(['confidence' => 'low']); }
    if ($operation === 'obsoleted') { $finding->update(['status' => 'obsolete']); }
    $tables = ['shared_key_candidates', 'timeline_events', 'event_types', 'reviews', 'keys', 'findings'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(Artisan::call($command, ['--dry-run' => true]))->toBe(0)->and($snapshot())->toBe($before);
    DB::statement("CREATE TRIGGER reject_shared_history BEFORE INSERT ON translation_workbench_timeline_events WHEN NEW.event_type = 'shared_key_candidate_".$operation."' BEGIN SELECT RAISE(ABORT, 'Shared history unavailable'); END");
    expect(fn () => Artisan::call($command))->toThrow(\Illuminate\Database\QueryException::class)
        ->and($snapshot())->toBe($before);
})->with(['discovered', 'updated', 'obsoleted']);

it('blocks ambiguous new shared candidate assignments before any writes', function (bool $dryRun) {
    $finding = sharedSuggestionFixture();
    $command = 'translation-workbench:detect-shared-key-candidates';
    expect(Artisan::call($command))->toBe(0);
    $keyId = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)->value('key_id');
    $extra = TranslationWorkbenchKey::findOrFail($keyId)->replicate();
    $extra->fingerprint = hash('sha256', 'shared-target-competing');
    $extra->translation_key = 'ui.target.competing';
    $extra->save();
    DB::table('translation_workbench_key_findings')->insert(['finding_id' => $finding->id, 'key_id' => $extra->id, 'status' => 'active']);
    $tables = ['shared_key_candidates', 'keys', 'key_findings', 'reviews', 'timeline_events', 'event_types'];
    $snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(Artisan::call($command, ['--dry-run' => $dryRun]))->toBe(1)->and($snapshot())->toBe($before)
        ->and(Artisan::output())->toContain('Finding '.$finding->id.' -> Key IDs: '.$keyId.', '.$extra->id);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-detect-shared-key-candidates.json')), true);
    expect($report['summary'])->toMatchArray(['exit_code' => 1, 'dry_run' => $dryRun,
        'error' => 'ambiguous_active_key_assignments', 'obsolete_marked' => 0, 'timeline_events_created' => 0])
        ->and($report['summary']['key_assignment_conflicts'])->toBe([['finding_id' => $finding->id, 'key_ids' => [$keyId, $extra->id]]]);
})->with([false, true]);

it('excludes obsolete keys from new shared candidate assignments', function (bool $replacement) {
    $finding = sharedSuggestionFixture();
    $keyId = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)->value('key_id');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    $key->update(['status' => 'obsolete']);
    $replacementId = null;
    if ($replacement) {
        $extra = $key->replicate();
        $extra->fingerprint = hash('sha256', 'shared-target-replacement');
        $extra->translation_key = 'ui.target.replacement';
        $extra->status = 'open';
        $extra->save();
        $replacementId = $extra->id;
        DB::table('translation_workbench_key_findings')->insert(['finding_id' => $finding->id, 'key_id' => $extra->id, 'status' => 'active']);
    }
    expect(Artisan::call('translation-workbench:detect-shared-key-candidates'))->toBe(0);
    $candidate = DB::table('translation_workbench_shared_key_candidates')->sole();
    expect($candidate->key_id)->toBe($replacementId)
        ->and($candidate->current_translation_key)->toBe($replacement ? 'ui.target.replacement' : null)
        ->and($candidate->suggested_shared_translation_key)->toBe('ui.shared.welcome')
        ->and($candidate->status)->toBe('pending');
    expect($key->refresh()->status)->toBe('obsolete');
})->with([false, true]);

it('derives shared literal eligibility from active distinct suggested keys independent of UI filters', function () {
    $editor = workbenchBulkEditor();
    $groupQuery = fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSharedLiteralCandidates::class)->groups();
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    $before = DB::table('translation_workbench_reviews')->count();
    expect($groupQuery()->sole()->bulk_literal_count)->toBe(2)->and($groupQuery()->sole()->suggested_key_count)->toBe(2);
    $editor->findingSearch = 'first.blade.php';
    $editor->findingIdSearch = '999999';
    $editor->findingNamespace = 'not-the-matching-namespace';
    $groups = (new ReflectionMethod($editor, 'bulkLiteralCountsQuery'))->invoke($editor)->get();
    expect($groups)->toHaveCount(1)->and($groups->sole()->bulk_literal_count)->toBe(2);
    $original = $findings[1]->suggested_key;
    $findings[1]->update(['suggested_key' => $findings[0]->suggested_key]);
    expect($groupQuery()->get())->toHaveCount(0);
    $findings[1]->update(['suggested_key' => $original]);
    expect($groupQuery()->get())->toHaveCount(1);
    foreach (['obsolete', 'commented_out', 'stale'] as $status) {
        $findings[1]->update(['status' => $status]);
        expect($groupQuery()->get())->toHaveCount(0);
    }
    $findings[1]->update(['status' => 'active']);
    DB::table('translation_workbench_source_files')->where('id', $findings[1]->source_file_id)->update(['status' => 'obsolete']);
    expect($groupQuery()->get())->toHaveCount(0);
    DB::table('translation_workbench_source_files')->where('id', $findings[1]->source_file_id)->update(['status' => 'active']);
    expect($groupQuery()->get())->toHaveCount(1);
    foreach ([null, '', '  '] as $missing) {
        $findings[1]->update(['suggested_key' => $missing]);
        expect($groupQuery()->get())->toHaveCount(0);
    }
    expect(DB::table('translation_workbench_reviews')->count())->toBe($before)
        ->and(DB::table('translation_workbench_shared_key_candidates')->count())->toBe(0);
});

it('counts shared literal occurrences once without counting key links or requiring separate files', function () {
    DB::connection()->getPdo()->sqliteCreateFunction('REGEXP_REPLACE',
        static fn ($value, $pattern, $replacement, $flags) => preg_replace('/'.$pattern.'/u', $replacement, $value), 4);
    File::put(base_path('resources/views/same-file.blade.php'), "{{ __('Same text') }}\n{{ __('Same text') }}\n{{ __('Same text') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    expect($findings)->toHaveCount(3);
    foreach ($findings as $index => $finding) { $finding->update(['suggested_key' => 'ui.shared.'.($index === 0 ? 'a' : 'b')]); }
    $query = fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSharedLiteralCandidates::class)->groups();
    expect($query()->sole()->bulk_literal_count)->toBe(3)->and($query()->sole()->suggested_key_count)->toBe(2);
    $findings[0]->update(['status' => 'obsolete']);
    expect($query()->get())->toHaveCount(0);
    $findings[2]->update(['suggested_key' => 'ui.shared.c']);
    expect($query()->sole()->bulk_literal_count)->toBe(2);
    $findings[2]->update(['literal_text' => 'Different text']);
    expect($query()->get())->toHaveCount(0);
});

it('keeps shared UI results filterable without changing their global eligibility', function () {
    $editor = workbenchBulkEditor();
    $editor->bulkEqualizeSelectedFindingIds = [];
    $editor->findingKeyRelation = 'shared_candidates';
    $ids = fn () => (new ReflectionMethod($editor, 'filteredFindingIds'))->invoke($editor);
    expect($ids())->toHaveCount(2);
    $editor->findingSearch = 'first.blade.php';
    expect($ids())->toHaveCount(1);
    $editor->findingSearch = '';
    $second = TranslationWorkbenchFinding::orderByDesc('id')->first();
    $second->update(['status' => 'obsolete']);
    $editor->showObsoleteFindings = true;
    expect($ids())->toBe([]);
    $second->update(['status' => 'active']);
    expect($ids())->toHaveCount(2);
});

it('blocks ambiguous bulk key assignments with concrete finding and key IDs', function () {
    $editor = workbenchBulkEditor();
    $ids = $editor->bulkEqualizeSelectedFindingIds;
    $keyId = TranslationWorkbenchKeyFinding::where('finding_id', $ids[0])->value('key_id');
    $extra = TranslationWorkbenchKey::findOrFail($keyId)->replicate();
    $extra->fingerprint = hash('sha256', 'bulk-competing-key');
    $extra->save();
    DB::table('translation_workbench_key_findings')->insert(['finding_id' => $ids[0], 'key_id' => $extra->id, 'status' => 'active']);
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    $context = $writer->context($ids);
    expect($context['can_confirm'])->toBeFalse()
        ->and($context['key_assignment_conflicts'])->toBe([['finding_id' => $ids[0], 'key_ids' => [$keyId, $extra->id]]])
        ->and($context['selection_error'])->toContain($ids[0].' → '.$keyId.', '.$extra->id);
    $snapshot = fn () => collect(['keys', 'key_findings', 'reviews', 'timeline_events'])
        ->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    expect(fn () => $writer->equalize($ids, 'ui.shared.welcome'))->toThrow(ValidationException::class)
        ->and($snapshot())->toBe($before);
    $editor->confirmBulkEqualizeTranslationKey();
    expect($snapshot())->toBe($before)
        ->and($editor->bulkEqualizeSelectedFindingIds)->toBe($ids);
});

it('excludes obsolete keys from the bulk selection without discarding a valid replacement', function (bool $replacement) {
    $editor = workbenchBulkEditor();
    $ids = $editor->bulkEqualizeSelectedFindingIds;
    $keyId = TranslationWorkbenchKeyFinding::where('finding_id', $ids[0])->value('key_id');
    $old = TranslationWorkbenchKey::findOrFail($keyId);
    $old->update(['status' => 'obsolete']);
    $oldSnapshot = $old->fresh()->toArray();
    if ($replacement) {
        $extra = $old->replicate();
        $extra->fingerprint = hash('sha256', 'bulk-replacement-key');
        $extra->status = 'open';
        $extra->save();
        DB::table('translation_workbench_key_findings')->insert(['finding_id' => $ids[0], 'key_id' => $extra->id, 'status' => 'active']);
    }
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    $context = $writer->context($ids);
    expect($context['key_assignment_conflicts'])->toBe([])
        ->and($context['can_confirm'])->toBe($replacement)
        ->and($context['rows'][0]['key_id'])->toBe($replacement ? $extra->id : null)
        ->and($writer->equalize($ids, 'ui.shared.welcome'))->toBe($replacement ? 2 : 0)
        ->and($old->fresh()->toArray())->toBe($oldSnapshot);
})->with([false, true]);

it('requires a current exact reviewed assignment for an existing bulk shared target', function (string $change) {
    $finding = sharedSuggestionFixture();
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    expect($writer->context([$finding->id])['existing_shared_key'])->toBe('ui.shared.welcome');
    $reviews = TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->get();
    foreach ($reviews as $review) {
        if ($change === 'file') {
            $sourceId = TranslationWorkbenchFinding::findOrFail($review->finding_id)->source_file_id;
            DB::table('translation_workbench_source_files')->where('id', $sourceId)->update(['status' => 'obsolete']);
        } elseif ($change === 'key') {
            TranslationWorkbenchKey::findOrFail($review->key_id)->update(['status' => 'obsolete']);
        } elseif ($change === 'renamed') {
            TranslationWorkbenchKey::findOrFail($review->key_id)->update(['translation_key' => 'ui.changed']);
        } elseif ($change === 'text') {
            TranslationWorkbenchFinding::findOrFail($review->finding_id)->update(['literal_text' => 'Changed text']);
        } elseif ($change === 'wrong_finding') {
            $review->update(['finding_id' => $finding->id]);
        } elseif ($change === 'link') {
            TranslationWorkbenchKeyFinding::where('finding_id', $review->finding_id)->update(['status' => 'obsolete']);
        } else {
            $key = TranslationWorkbenchKey::findOrFail($review->key_id)->replicate();
            $key->fingerprint = hash('sha256', 'shared-competing-'.$review->id);
            $key->save();
            DB::table('translation_workbench_key_findings')->insert(['finding_id' => $review->finding_id, 'key_id' => $key->id, 'status' => 'active']);
        }
    }
    $snapshot = fn () => collect(['keys', 'key_findings', 'reviews', 'timeline_events'])
        ->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $context = $writer->context([$finding->id]);
    expect($context['existing_shared_key'])->toBeNull()
        ->and($context['can_confirm'])->toBeFalse()
        ->and($writer->equalize([$finding->id], 'ui.shared.welcome'))->toBe(0)
        ->and($snapshot())->toBe($before);
})->with(['file', 'key', 'renamed', 'text', 'wrong_finding', 'link', 'competing']);

it('does not choose between different currently confirmed shared target keys', function () {
    $finding = sharedSuggestionFixture();
    $review = TranslationWorkbenchReview::where('decision', 'translation_key_bulk_equalized')->firstOrFail();
    TranslationWorkbenchKey::findOrFail($review->key_id)->update(['translation_key' => 'ui.other.shared']);
    $review->update(['new_values' => [...$review->new_values, 'translation_key' => 'ui.other.shared']]);
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    expect($writer->context([$finding->id])['existing_shared_key'])->toBeNull()
        ->and($writer->context([$finding->id])['can_confirm'])->toBeFalse()
        ->and($writer->equalize([$finding->id], 'ui.shared.welcome'))->toBe(0);
});

it('rejects a changed but still valid bulk preview and accepts an explicitly reopened dialog', function () {
    $editor = workbenchBulkEditor();
    $oldFingerprint = $editor->bulkEqualizeContextFingerprint;
    TranslationWorkbenchFinding::query()->update(['literal_text' => 'A different shared literal']);
    $before = TranslationWorkbenchKey::orderBy('id')->get()->toArray();
    $reviews = TranslationWorkbenchReview::count();
    expect(fn () => $editor->confirmBulkEqualizeTranslationKey())->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchKey::orderBy('id')->get()->toArray())->toBe($before)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and($editor->bulkEqualizeTranslationKeyModalOpen)->toBeTrue();
    $editor->openBulkEqualizeTranslationKeyModal();
    expect($editor->bulkEqualizeContextFingerprint)->not->toBe($oldFingerprint);
    $editor->bulkEqualizeTranslationKey = 'ui.shared.changed';
    $editor->confirmBulkEqualizeTranslationKey();
    expect(TranslationWorkbenchKey::where('translation_key', 'ui.shared.changed')->count())->toBe(2)
        ->and($editor->bulkEqualizeContextFingerprint)->toBeNull();
});

it('requires a preview before the UI confirms bulk changes', function () {
    $editor = workbenchBulkEditor();
    $editor->closeBulkEqualizeTranslationKeyModal();
    $editor->bulkEqualizeTranslationKey = 'ui.shared.welcome';
    expect(fn () => $editor->confirmBulkEqualizeTranslationKey())->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchKey::where('translation_key', 'ui.shared.welcome')->count())->toBe(0);
});

it('does not reclaim unsafe detached keys during bulk shared attachment', function (string $change) {
    $finding = sharedSuggestionFixture();
    $link = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->sole();
    $key = TranslationWorkbenchKey::findOrFail($link->key_id);
    $link->update(['status' => 'obsolete']);
    if ($change === 'obsolete') {
        $key->update(['status' => 'obsolete']);
    } elseif ($change === 'reviewed') {
        $key->update(['review_status' => 'reviewed']);
    } elseif ($change === 'assigned_value') {
        $key->update(['translation_key' => 'ui.other.owner']);
    } elseif ($change === 'other_finding') {
        $other = TranslationWorkbenchFinding::where('id', '<>', $finding->id)->firstOrFail();
        DB::table('translation_workbench_key_findings')->insert(['finding_id' => $other->id, 'key_id' => $key->id, 'status' => 'active']);
    } else {
        $copy = $key->replicate();
        $copy->fingerprint = hash('sha256', 'duplicate-detached-key');
        $copy->save();
    }
    $snapshot = fn () => collect(['keys', 'key_findings', 'reviews', 'timeline_events'])
        ->mapWithKeys(fn ($table) => [$table => DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson()])->all();
    $before = $snapshot();
    $writer = app(TranslationWorkbenchBulkKeyWriter::class);
    expect($writer->context([$finding->id])['can_confirm'])->toBeFalse()
        ->and($writer->context([$finding->id])['selection_error'])->toContain('Finding '.$finding->id);
    $editor = new TranslationWorkbenchEntries;
    $editor->bulkEqualizeSelectedFindingIds = [$finding->id];
    $editor->openBulkEqualizeTranslationKeyModal();
    expect($editor->bulkEqualizeTranslationKeyModalOpen)->toBeFalse()
        ->and($editor->bulkEqualizeContextFingerprint)->toBeNull();
    try {
        $writer->equalize([$finding->id], 'ui.shared.welcome');
        test()->fail('Unsafe detached key was reused.');
    } catch (ValidationException $exception) {
        expect($exception->getMessage())->toContain('Finding '.$finding->id)->toContain((string) $key->id);
    }
    expect($snapshot())->toBe($before);
})->with(['obsolete', 'reviewed', 'assigned_value', 'other_finding', 'ambiguous']);

it('preserves newer scalar translations when a second editor saves an old preview', function () {
    $first = workbenchValueEditor();
    $second = new TranslationWorkbenchEntries;
    $second->openEditModal($first->editFindingId);
    $second->targetTranslationValue = 'Old editor input';
    $first->targetTranslationValue = 'Newer stored translation';
    $first->saveTranslationValue();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $second->saveTranslationValue())->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchLangValue::where('locale', 'de')->value('value'))->toBe('Newer stored translation')
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and($second->targetTranslationValue)->toBe('Old editor input')
        ->and($second->editModalOpen)->toBeTrue();
    $second->openEditModal($first->editFindingId);
    $second->targetTranslationValue = 'Reviewed current state';
    $second->saveTranslationValue();
    $second->targetTranslationValue = 'Next intentional edit';
    $second->saveTranslationValue();
    expect(TranslationWorkbenchLangValue::where('locale', 'de')->value('value'))->toBe('Next intentional edit');
});

it('rejects scalar preview changes to key identity or value status', function (string $change) {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    if ($change === 'key') {
        TranslationWorkbenchKey::sole()->update(['translation_key' => 'ui.renamed']);
    } else {
        TranslationWorkbenchLangValue::where('locale', 'de')->update(['status' => 'obsolete']);
    }
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toArray();
    expect(fn () => $editor->saveTranslationValue())->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toArray())->toBe($before);
})->with(['key', 'status']);

it('rejects stale option translations without losing editor input or newer values', function () {
    $first = workbenchDynamicEditor();
    $second = new TranslationWorkbenchEntries;
    $second->openEditModal($first->editFindingId);
    $second->continueDynamicEdit();
    $field = 'v_'.sha1('draft');
    $second->dynamicMultiTargetValues[$field] = 'Stale user input';
    $first->saveDynamicMultiTranslationValues();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $second->saveDynamicMultiTranslationValues())->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->where('value_key', 'draft')->value('value'))->toBe('Entwurf')
        ->and($second->dynamicMultiTargetValues[$field])->toBe('Stale user input')
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $second->openEditModal($first->editFindingId);
    $second->continueDynamicEdit();
    $second->dynamicMultiTargetValues[$field] = 'Newly reviewed input';
    $second->saveDynamicMultiTranslationValues();
    $second->saveDynamicMultiTranslationValues();
    expect(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'de')->where('value_key', 'draft')->value('value'))->toBe('Newly reviewed input');
});

it('rejects stale single source option edits after a source value changes', function () {
    $editor = workbenchDynamicEditor();
    DB::table('translation_workbench_dynamic_key_values')->where('locale', 'en')->where('value_key', 'draft')->update(['value' => 'New native text']);
    $field = 'v_'.sha1('draft');
    $editor->dynamicMultiSourceValues[$field] = 'Old editor correction';
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $editor->saveDynamicMultiSourceValue($field))->toThrow(ValidationException::class)
        ->and(DB::table('translation_workbench_dynamic_key_values')->where('locale', 'en')->where('value_key', 'draft')->value('value'))->toBe('New native text')
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
});

function workbenchApprovalValue(string $kind): array
{
    $editor = $kind === 'option' ? workbenchDynamicEditor() : workbenchValueEditor();
    $kind === 'option' ? $editor->saveDynamicMultiTranslationValues() : $editor->saveTranslationValue();
    $table = $kind === 'option' ? 'translation_workbench_dynamic_key_values' : 'translation_workbench_lang_values';
    $valueId = (int) DB::table($table)->where('locale', 'de')->orderBy('id')->value('id');

    return [$table, $valueId, TranslationWorkbenchKey::sole()->id, TranslationWorkbenchFinding::sole()->id];
}

it('approves an explicit stored language value snapshot without publishing or duplicating history', function (string $kind) {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $before = DB::table($table)->orderBy('id')->get()->toJson();
    expect($service->isApproved($kind, $id, $keyId, $findingId))->toBeFalse();
    $preview = $service->preview($kind, $id, $keyId, $findingId);
    $review = $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']);
    expect($service->isApproved($kind, $id, $keyId, $findingId))->toBeTrue()
        ->and($service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint'])->id)->toBe($review->id)
        ->and(DB::table($table)->orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_value_approved')->count())->toBe(1)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
    DB::table($table)->where('id', $id)->update(['value' => 'A changed value']);
    expect($service->isApproved($kind, $id, $keyId, $findingId))->toBeFalse()
        ->and(fn () => $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']))->toThrow(ValidationException::class)
        ->and($review->fresh()->new_values)->toBe($preview['snapshot']);
})->with(['scalar', 'option']);

it('rolls back explicit value approval when its history cannot be recorded', function (string $kind) {
    [, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $preview = $service->preview($kind, $id, $keyId, $findingId);
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('recordReviewEvent')->once()->andThrow(new RuntimeException('Approval history unavailable'));
    app()->instance(TranslationWorkbenchTimelineRecorder::class, $recorder);
    expect(fn () => $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']))->toThrow(RuntimeException::class, 'Approval history unavailable')
        ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(0);
})->with(['scalar', 'option']);

it('keeps explicit value approval separate for each stored language', function (string $kind) {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $preview = $service->preview($kind, $id, $keyId, $findingId);
    $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']);
    $sourceId = (int) DB::table($table)->where('locale', 'en')->orderBy('id')->value('id');
    expect($service->isApproved($kind, $sourceId, $keyId, $findingId))->toBeFalse()
        ->and($service->isApproved($kind, $id, $keyId, $findingId))->toBeTrue();
})->with(['scalar', 'option']);

it('rejects explicit value approval after its owner becomes unreviewed', function (string $kind) {
    [, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $preview = $service->preview($kind, $id, $keyId, $findingId);
    TranslationWorkbenchKey::findOrFail($keyId)->update(['review_status' => 'pending']);
    expect(fn () => $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']))->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(0);
})->with(['scalar', 'option']);

it('requires a new approval after a value is changed and restored', function (string $kind) {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $preview = $service->preview($kind, $id, $keyId, $findingId);
    $review = $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']);
    $row = DB::table($table)->where('id', $id);
    $revision = (int) $row->value('value_revision');
    (clone $row)->update(['value' => 'Temporary changed text']);
    (clone $row)->update(['value' => $preview['snapshot']['value']]);
    expect((int) $row->value('value_revision'))->toBe($revision + 2)
        ->and($service->isApproved($kind, $id, $keyId, $findingId))->toBeFalse()
        ->and(fn () => $service->approve($kind, $id, $keyId, $findingId, $preview['fingerprint']))->toThrow(ValidationException::class);
    $current = $service->preview($kind, $id, $keyId, $findingId);
    $replacement = $service->approve($kind, $id, $keyId, $findingId, $current['fingerprint']);
    expect($replacement->id)->not->toBe($review->id)
        ->and($replacement->old_values)->toBe($review->new_values)
        ->and($service->isApproved($kind, $id, $keyId, $findingId))->toBeTrue();
})->with(['scalar', 'option']);

it('preserves value revisions on observation updates and transaction rollback', function (string $kind) {
    [$table, $id] = workbenchApprovalValue($kind);
    $row = DB::table($table)->where('id', $id);
    $before = $row->first();
    (clone $row)->update(['value' => $before->value, 'updated_at' => now()->addMinute(), 'meta' => json_encode(['observation' => 1])]);
    expect((int) $row->value('value_revision'))->toBe((int) $before->value_revision);
    DB::beginTransaction();
    (clone $row)->update(['value' => null]);
    expect((int) $row->value('value_revision'))->toBe((int) $before->value_revision + 1);
    DB::rollBack();
    expect((int) $row->value('value_revision'))->toBe((int) $before->value_revision)
        ->and($row->value('value'))->toBe($before->value);
    (clone $row)->update(['status' => 'obsolete']);
    (clone $row)->update(['status' => $before->status]);
    expect((int) $row->value('value_revision'))->toBe((int) $before->value_revision + 2);
})->with(['scalar', 'option']);

it('rejects an editor preview after a stored value was changed and restored', function (string $kind) {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $editor = $kind === 'scalar'
        ? app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchScalarEditor::class)
        : app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicEditor::class);
    $fingerprint = $editor->fingerprint($keyId, $findingId);
    $row = DB::table($table)->where('id', $id);
    $original = $row->value('value');
    (clone $row)->update(['value' => 'Intervening edit']);
    (clone $row)->update(['value' => $original]);
    expect($editor->fingerprint($keyId, $findingId))->not->toBe($fingerprint);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $kind === 'scalar'
        ? $editor->save($keyId, $findingId, [], $fingerprint)
        : $editor->save($keyId, $findingId, [], [], $fingerprint))->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
})->with(['scalar', 'option']);

it('preserves stored values during revision migration and requires it for approval', function (string $kind) {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    $migration = require dirname(__DIR__, 3).'/packages/gunreip/laravel-translation-workbench/database/migrations/2026_10_16_000001_add_value_revisions.php';
    $migration->down();
    $before = DB::table($table)->where('id', $id)->first();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    expect(fn () => $service->preview($kind, $id, $keyId, $findingId))->toThrow(ValidationException::class, 'Value approval requires the value revision migration.');
    $editor = $kind === 'scalar'
        ? app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchScalarEditor::class)
        : app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicEditor::class);
    expect($editor->fingerprint($keyId, $findingId))->toHaveLength(64);
    $migration->up();
    $after = (array) DB::table($table)->where('id', $id)->first();
    expect($after['value_revision'])->toBe(1);
    unset($after['value_revision']);
    expect($after)->toBe((array) $before)
        ->and($service->isApproved($kind, $id, $keyId, $findingId))->toBeFalse();
})->with(['scalar', 'option']);

it('previews and approves stored revisions from the editor without saving draft input', function (string $kind) {
    $editor = $kind === 'option' ? workbenchDynamicEditor() : workbenchValueEditor();
    $kind === 'option' ? $editor->saveDynamicMultiTranslationValues() : $editor->saveTranslationValue();
    $editor->targetTranslationValue = 'Unsaved scalar draft';
    $editor->dynamicMultiTargetValues['v_'.sha1('draft')] = 'Unsaved option draft';
    $editor->openValueApproval($kind);
    $index = collect($editor->valueApprovalRows)->search(fn ($row) => $row['snapshot']['locale'] === 'de');
    expect($index)->not->toBeFalse();
    $before = $editor->valueApprovalRows[$index];
    expect($before['state'])->toBe('unapproved');
    if ($kind === 'option') {
        expect($before['legacy_reviewed_at'])->not->toBeNull();
    }
    $editor->approveStoredValue($index);
    expect($editor->valueApprovalRows[$index]['state'])->toBe('approved')
        ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->sole()->new_values)->toBe($before['snapshot'])
        ->and($editor->targetTranslationValue)->toBe('Unsaved scalar draft')
        ->and($editor->dynamicMultiTargetValues['v_'.sha1('draft')])->toBe('Unsaved option draft')
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
    $editor->closeValueApproval();
    expect($editor->valueApprovalRows)->toBe([])
        ->and(fn () => $editor->approveStoredValue($index))->toThrow(ValidationException::class);
})->with(['scalar', 'option']);

it('rejects a stale approval dialog and distinguishes a changed previously approved value', function (string $kind) {
    $editor = $kind === 'option' ? workbenchDynamicEditor() : workbenchValueEditor();
    $kind === 'option' ? $editor->saveDynamicMultiTranslationValues() : $editor->saveTranslationValue();
    $editor->openValueApproval($kind);
    $index = collect($editor->valueApprovalRows)->search(fn ($row) => $row['snapshot']['locale'] === 'de');
    $snapshot = $editor->valueApprovalRows[$index]['snapshot'];
    $editor->approveStoredValue($index);
    $table = $kind === 'option' ? 'translation_workbench_dynamic_key_values' : 'translation_workbench_lang_values';
    DB::table($table)->where('id', $snapshot['value_id'])->update(['value' => 'New stored revision']);
    expect(fn () => $editor->approveStoredValue($index))->toThrow(ValidationException::class)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(1);
    $editor->openValueApproval($kind);
    expect($editor->valueApprovalRows[$index]['state'])->toBe('changed')
        ->and($editor->valueApprovalRows[$index]['snapshot']['value'])->toBe('New stored revision');
    $editor->approveStoredValue($index);
    expect(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(2);
})->with(['scalar', 'option']);

it('clears the previous approval preview when its owner is no longer eligible', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    expect($editor->valueApprovalRows)->not->toBeEmpty();
    TranslationWorkbenchKey::sole()->update(['review_status' => 'pending']);
    expect(fn () => $editor->openValueApproval('scalar'))->toThrow(ValidationException::class)
        ->and($editor->valueApprovalRows)->toBe([]);
});

it('requires permission again when confirming a stored value approval', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    auth()->forgetUser();
    expect(fn () => $editor->approveStoredValue(0))->toThrow(AuthorizationException::class)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(0);
});

it('keeps the approval preview limited to available editor languages and nonempty stored values', function () {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue('scalar');
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $rows = $service->inspect('scalar', $keyId, $findingId, ['de']);
    expect(array_column(array_column($rows, 'snapshot'), 'locale'))->toBe(['de']);
    DB::table($table)->where('id', $id)->update(['value' => null]);
    expect($service->inspect('scalar', $keyId, $findingId, ['de']))->toBe([]);
});

it('clears the approval proof when the Flux dialog closes through its model', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    $editor->updatedValueApprovalModalOpen(false);
    expect($editor->valueApprovalRows)->toBe([])
        ->and($editor->valueApprovalModalOpen)->toBeFalse();
});

it('previews publication using current approvals without writing files or history', function (string $kind) {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue($kind);
    if ($kind === 'option') {
        TranslationWorkbenchKey::findOrFail($keyId)->update(['is_dynamic_key' => true, 'is_dynamic_multi' => true]);
    }
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $first = $service->preview($kind, $keyId, $findingId, ['de']);
    expect($first['dry_run'])->toBeTrue()->and($first['advisory_only'])->toBeTrue()
        ->and($first['changes_without_current_approval'])->toBe($first['values_new']);
    foreach ($approval->inspect($kind, $keyId, $findingId, ['de']) as $row) {
        $approval->approve($kind, $row['snapshot']['value_id'], $keyId, $findingId, $row['fingerprint']);
    }
    $events = TranslationWorkbenchTimelineEvent::count();
    $report = $service->preview($kind, $keyId, $findingId, ['de']);
    expect($report['changes_without_current_approval'])->toBe(0)
        ->and($report['files_written'])->toBe(0)
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    DB::table($table)->where('id', $id)->update(['value' => 'Changed after approval']);
    expect($service->preview($kind, $keyId, $findingId, ['de'])['changes_without_current_approval'])->toBe(1);
})->with(['scalar', 'option']);

it('retains existing published values without granting an implicit approval', function () {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue('scalar');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    installPublishedValueFixture($key->translation_key, ['de']);
    $file = File::allFiles(lang_path())[0]->getPathname();
    $contents = File::get($file);
    $events = TranslationWorkbenchTimelineEvent::count();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $report = $service->preview('scalar', $keyId, $findingId, ['de']);
    expect($report['unchanged_values_retained'])->toBe(1)
        ->and($report['values_changed'])->toBe(0)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(0);
    DB::table($table)->where('id', $id)->update(['value' => 'Unapproved new translation']);
    $report = $service->preview('scalar', $keyId, $findingId, ['de']);
    expect($report['changes_without_current_approval'])->toBe(1)
        ->and(File::get($file))->toBe($contents)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
});

it('reports planned removals separately and never deletes during publication preview', function () {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue('scalar');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    installPublishedValueFixture($key->translation_key, ['de']);
    $file = File::allFiles(lang_path())[0]->getPathname();
    $contents = File::get($file);
    DB::table($table)->where('id', $id)->update(['status' => 'obsolete']);
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->preview('scalar', $keyId, $findingId, ['de']);
    expect($report['pruning_requires_separate_review'])->toBe(1)
        ->and($report['files_written'])->toBe(0)->and(File::get($file))->toBe($contents);
});

it('invalidates the publication report after approving a stored value and closing its dialog', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    $editor->previewValuePublication();
    expect($editor->valuePublicationPreview)->not->toBeNull();
    $editor->approveStoredValue(0);
    expect($editor->valuePublicationPreview)->toBeNull();
    $editor->previewValuePublication();
    $editor->closeValueApproval();
    expect($editor->valuePublicationPreview)->toBeNull()->and($editor->valueApprovalContext)->toBeNull()
        ->and(fn () => $editor->previewValuePublication())->toThrow(ValidationException::class);
});

it('reports approved options still excluded by the legacy reviewed timestamp filter', function () {
    [, $id, $keyId, $findingId] = workbenchApprovalValue('option');
    TranslationWorkbenchKey::findOrFail($keyId)->update(['is_dynamic_key' => true, 'is_dynamic_multi' => true]);
    DB::table('translation_workbench_dynamic_key_values')->where('id', $id)->update(['reviewed_at' => null]);
    $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $preview = $approval->preview('option', $id, $keyId, $findingId);
    $approval->approve('option', $id, $keyId, $findingId, $preview['fingerprint']);
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->preview('option', $keyId, $findingId, ['de']);
    expect($report['values_excluded_by_legacy_review_filter'])->toBe(1)
        ->and($report['values_exportable'])->toBe(1)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('invalidates publication proof for restored values and newly approved revisions', function () {
    [$table, $id, $keyId, $findingId] = workbenchApprovalValue('scalar');
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $report = $service->preview('scalar', $keyId, $findingId, ['de']);
    expect($service->verify('scalar', $keyId, $findingId, ['de'], $report['fingerprint'])['fingerprint'])->toBe($report['fingerprint']);
    $original = DB::table($table)->where('id', $id)->value('value');
    DB::table($table)->where('id', $id)->update(['value' => 'Intermediate revision']);
    DB::table($table)->where('id', $id)->update(['value' => $original]);
    expect(fn () => $service->verify('scalar', $keyId, $findingId, ['de'], $report['fingerprint']))->toThrow(ValidationException::class);
    $report = $service->preview('scalar', $keyId, $findingId, ['de']);
    $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $snapshot = $approval->preview('scalar', $id, $keyId, $findingId);
    $approval->approve('scalar', $id, $keyId, $findingId, $snapshot['fingerprint']);
    expect(fn () => $service->verify('scalar', $keyId, $findingId, ['de'], $report['fingerprint']))->toThrow(ValidationException::class);
});

it('invalidates publication proof when file bytes change even if translations do not', function (string $change) {
    [, , $keyId, $findingId] = workbenchApprovalValue('scalar');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    installPublishedValueFixture($key->translation_key, ['de']);
    $file = File::allFiles(lang_path())[0]->getPathname();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    if ($change === 'created') {
        File::delete($file);
    }
    $report = $service->preview('scalar', $keyId, $findingId, ['de']);
    match ($change) {
        'created' => File::put($file, "<?php return [];\n"),
        'deleted' => File::delete($file),
        'comment' => File::append($file, "\n// Changed after preview\n"),
    };
    expect(fn () => $service->verify('scalar', $keyId, $findingId, ['de'], $report['fingerprint']))->toThrow(ValidationException::class);
})->with(['created', 'deleted', 'comment']);

it('keeps the old publication report after a failed freshness check', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    $editor->previewValuePublication();
    $editor->verifyValuePublication();
    expect($editor->valuePublicationVerified)->toBeTrue();
    $old = $editor->valuePublicationPreview;
    TranslationWorkbenchLangValue::where('locale', 'de')->update(['value' => 'A new value']);
    expect(fn () => $editor->verifyValuePublication())->toThrow(ValidationException::class)
        ->and($editor->valuePublicationVerified)->toBeFalse()
        ->and($editor->valuePublicationPreview)->toBe($old);
    $editor->previewValuePublication();
    $editor->verifyValuePublication();
    expect($editor->valuePublicationVerified)->toBeTrue();
});

it('rejects a file changed after export planning but before publication preview completes', function () {
    [, , $keyId, $findingId] = workbenchApprovalValue('scalar');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    $exporter = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class);
    installPublishedValueFixture($key->translation_key, ['de']);
    $file = File::allFiles(lang_path())[0]->getPathname();
    $plan = $exporter->export(['de'], keys: [$key->translation_key]);
    $mock = Mockery::mock(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class)->makePartial();
    $mock->shouldReceive('export')->once()->andReturnUsing(function () use ($file, $plan) {
        File::append($file, "\n// Concurrent file edit\n");

        return $plan;
    });
    $service = new \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview(
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class), $mock,
    );
    expect(fn () => $service->preview('scalar', $keyId, $findingId, ['de']))->toThrow(ValidationException::class);
});

function approvedPublicationFixture(): array
{
    [, , $keyId, $findingId] = workbenchApprovalValue('scalar');
    $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    foreach ($approval->inspect('scalar', $keyId, $findingId, ['de', 'en']) as $value) {
        $approval->approve('scalar', $value['snapshot']['value_id'], $keyId, $findingId, $value['fingerprint']);
    }
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->preview('scalar', $keyId, $findingId, ['de', 'en']);

    return [$keyId, $findingId, $report];
}

it('publishes a verified approved plan and resumes without duplicate history', function () {
    [$keyId, $findingId, $report] = approvedPublicationFixture();
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $result = $runner->publish('scalar', $keyId, $findingId, ['de', 'en'], $report['fingerprint']);
    expect($result['state'])->toBe('complete')->and(File::allFiles(lang_path()))->toHaveCount(2)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(1);
    $events = TranslationWorkbenchTimelineEvent::count();
    $runner->resume($result['id']);
    expect(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(1);
});

it('refuses stale and unapproved publication without writing files', function () {
    [, , $keyId, $findingId] = workbenchApprovalValue('scalar');
    $preview = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $report = $preview->preview('scalar', $keyId, $findingId, ['de']);
    expect(fn () => $runner->publish('scalar', $keyId, $findingId, ['de'], $report['fingerprint']))->toThrow(ValidationException::class);
    TranslationWorkbenchLangValue::where('locale', 'de')->update(['value' => 'Changed']);
    expect(fn () => $runner->publish('scalar', $keyId, $findingId, ['de'], $report['fingerprint']))->toThrow(ValidationException::class)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('resumes a partially written publication and refuses external file changes', function (bool $externalChange) {
    [$keyId, $findingId, $report] = approvedPublicationFixture();
    $preview = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $exporter = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class);
    $runner = new class($preview, $exporter) extends \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner {
        private int $writes = 0;
        protected function replaceFile(string $path, string $contents, ?string $expectedHash): void
        {
            if (++$this->writes === 2) { throw new RuntimeException('Simulated second file failure'); }
            parent::replaceFile($path, $contents, $expectedHash);
        }
    };
    expect(fn () => $runner->publish('scalar', $keyId, $findingId, ['de', 'en'], $report['fingerprint']))->toThrow(RuntimeException::class, 'Publication interrupted');
    $journals = glob(storage_path('app/private/translation-workbench/publications/*.json'));
    $run = json_decode(File::get($journals[0]), true);
    expect($run['state'])->toBe('interrupted')->and(File::allFiles(lang_path()))->toHaveCount(1)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0);
    if ($externalChange) {
        $path = File::allFiles(lang_path())[0]->getPathname();
        File::append($path, "\n// External change\n");
        expect(fn () => $runner->resume($run['id']))->toThrow(ValidationException::class)
            ->and(File::get($path))->toContain('// External change')
            ->and(File::allFiles(lang_path()))->toHaveCount(1);
    } else {
        $result = $runner->resume($run['id']);
        expect($result['state'])->toBe('complete')->and(File::allFiles(lang_path()))->toHaveCount(2)
            ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->count())->toBe(2);
    }
})->with([false, true]);

it('recovers written files after history failure without duplicating publication history', function (bool $changedData) {
    [$keyId, $findingId, $report] = approvedPublicationFixture();
    $preview = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $exporter = Mockery::mock(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class, [app(TranslationWorkbenchTimelineRecorder::class)])->makePartial();
    $exporter->shouldReceive('recordPreparedPublicationHistory')->once()->andThrow(new RuntimeException('Simulated history failure'));
    $runner = new \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner($preview, $exporter);
    expect(fn () => $runner->publish('scalar', $keyId, $findingId, ['de', 'en'], $report['fingerprint']))->toThrow(RuntimeException::class);
    $path = glob(storage_path('app/private/translation-workbench/publications/*.json'))[0];
    $run = json_decode(File::get($path), true);
    expect(File::allFiles(lang_path()))->toHaveCount(2)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->count())->toBe(0);
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    if ($changedData) {
        TranslationWorkbenchLangValue::where('locale', 'de')->update(['value' => 'Changed before recovery']);
        expect(fn () => $runner->resume($run['id']))->toThrow(ValidationException::class)
            ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0);

        return;
    }
    $current = $preview->preview('scalar', $keyId, $findingId, ['de', 'en']);
    expect(fn () => $runner->publish('scalar', $keyId, $findingId, ['de', 'en'], $current['fingerprint']))->toThrow(ValidationException::class);
    // Simulate losing the last journal progress update after atomic file replacement.
    foreach ($run['plans'] as &$plan) { $plan['written'] = false; }
    unset($plan);
    File::put($path, json_encode($run));
    $result = $runner->resume($run['id']);
    expect($result['state'])->toBe('complete')
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->count())->toBe(2);
    // Simulate completion journal failure after the DB receipt committed.
    $run['state'] = 'writing';
    File::put($path, json_encode($run));
    $result = $runner->resume($run['id']);
    expect(array_column($result['files'], 'written'))->toBe([true, true])
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'lang_file_value_exported')->count())->toBe(2)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(1);
})->with([false, true]);

it('publishes from the value review dialog and lists only safe journal metadata', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    foreach (array_keys($editor->valueApprovalRows) as $index) {
        $editor->approveStoredValue($index);
    }
    $editor->previewValuePublication();
    $editor->publishValuePublication();
    expect($editor->valuePublicationPreview)->toBeNull()
        ->and($editor->valuePublicationVerified)->toBeFalse()
        ->and($editor->valuePublicationRuns)->toHaveCount(1)
        ->and($editor->valuePublicationRuns[0]['state'])->toBe('complete')
        ->and($editor->valuePublicationRuns[0]['recorded'])->toBeTrue()
        ->and(array_column($editor->valuePublicationRuns[0]['files'], 'current_state'))->each->toBe('result');
    $json = json_encode($editor->valuePublicationRuns);
    expect($json)->not->toContain('_contents', 'data_fingerprint');
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $context = $editor->valueApprovalContext;
    expect($runner->runs('option', $context['key_id'], $context['finding_id']))->toBe([]);
    expect(fn () => $runner->resume($editor->valuePublicationRuns[0]['id'], [
        'kind' => 'option', 'keyId' => $context['key_id'], 'findingId' => $context['finding_id'],
    ]))->toThrow(ValidationException::class);
    $editor->closeValueApproval();
    expect($editor->valuePublicationRuns)->toBe([]);
    expect(fn () => $editor->publishValuePublication())->toThrow(ValidationException::class);
});

it('shows stale publication failure in the dialog without writing or retaining its preview', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    foreach (array_keys($editor->valueApprovalRows) as $index) { $editor->approveStoredValue($index); }
    $editor->previewValuePublication();
    TranslationWorkbenchLangValue::where('locale', 'de')->update(['value' => 'Changed after the UI preview']);
    $editor->publishValuePublication();
    expect($editor->getErrorBag()->has('valueApproval'))->toBeTrue()
        ->and($editor->valuePublicationPreview)->toBeNull()
        ->and($editor->valuePublicationRuns)->toBe([])
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('shows interrupted files in the dialog and resumes without duplicate history', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    foreach (array_keys($editor->valueApprovalRows) as $index) { $editor->approveStoredValue($index); }
    $editor->previewValuePublication();
    $runner = new class(
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class),
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class),
    ) extends \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner {
        private int $writes = 0;
        protected function replaceFile(string $path, string $contents, ?string $expectedHash): void
        {
            if (++$this->writes === 2) { throw new RuntimeException('Simulated UI file failure'); }
            parent::replaceFile($path, $contents, $expectedHash);
        }
    };
    app()->instance(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class, $runner);
    $editor->publishValuePublication();
    expect($editor->getErrorBag()->has('valueApproval'))->toBeTrue()
        ->and($editor->valuePublicationRuns[0]['error'])->toContain('Simulated UI file failure')
        ->and($editor->valuePublicationRuns[0]['recorded'])->toBeFalse()
        ->and(array_column($editor->valuePublicationRuns[0]['files'], 'current_state'))->toContain('source', 'result');
    $editor->resumeValuePublication(0);
    expect($editor->getErrorBag()->has('valueApproval'))->toBeFalse()
        ->and($editor->valuePublicationRuns[0]['state'])->toBe('complete')
        ->and($editor->valuePublicationRuns[0]['recorded'])->toBeTrue()
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(1);
});

it('rechecks editor access before publishing or reading publication journals', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $editor->openValueApproval('scalar');
    $editor->previewValuePublication();
    auth()->forgetUser();
    expect(fn () => $editor->publishValuePublication())->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
    expect(fn () => $editor->refreshValuePublicationRuns())->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
    expect(File::allFiles(lang_path()))->toHaveCount(0);
});

function interruptedPublicationFixture(): array
{
    [$keyId, $findingId, $report] = approvedPublicationFixture();
    $runner = new class(
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class),
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class),
    ) extends \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner {
        private int $writes = 0;
        protected function replaceFile(string $path, string $contents, ?string $expectedHash): void
        {
            if (++$this->writes === 2) { throw new RuntimeException('Closure fixture: second file failed'); }
            parent::replaceFile($path, $contents, $expectedHash);
        }
    };
    expect(fn () => $runner->publish('scalar', $keyId, $findingId, ['de', 'en'], $report['fingerprint']))->toThrow(RuntimeException::class);
    $path = glob(storage_path('app/private/translation-workbench/publications/*.json'))[0];
    $run = json_decode(File::get($path), true);

    return [$run, ['kind' => 'scalar', 'keyId' => $keyId, 'findingId' => $findingId], $path];
}

it('closes an interrupted publication with an explicit observed state without modifying files', function () {
    [$run, $context, $journal] = interruptedPublicationFixture();
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $file = File::allFiles(lang_path())[0]->getPathname();
    File::append($file, "\n// External edit retained\n");
    $contents = File::get($file);
    $proof = $runner->previewClosure($run['id'], $context);
    expect(array_column($proof['files'], 'current_state'))->toContain('changed', 'source');
    $events = TranslationWorkbenchTimelineEvent::count();
    $runner->closeWithoutWriting($run['id'], $context, $proof['fingerprint'], 'Keep the external edit and prepare current values.');
    $receipt = TranslationWorkbenchReview::where('decision', 'publication_closed_without_writing')->sole();
    expect(File::get($file))->toBe($contents)
        ->and(File::allFiles(lang_path()))->toHaveCount(1)
        ->and($receipt->new_values['files'])->toBe($proof['files'])
        ->and($receipt->new_values['reason'])->toBe('Keep the external edit and prepare current values.')
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0);
    // Simulate a lost journal update after the closure receipt committed.
    File::put($journal, json_encode($run));
    expect($runner->runs(...array_values($context))[0]['state'])->toBe('closed');
    expect(fn () => $runner->resume($run['id'], $context))->toThrow(ValidationException::class);
    expect(fn () => $runner->closeWithoutWriting($run['id'], $context, $proof['fingerprint'], 'Duplicate'))->toThrow(ValidationException::class);
    expect(TranslationWorkbenchReview::where('decision', 'publication_closed_without_writing')->count())->toBe(1);
    $fresh = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)
        ->preview('scalar', $context['keyId'], $context['findingId'], ['de', 'en']);
    expect($runner->publish('scalar', $context['keyId'], $context['findingId'], ['de', 'en'], $fresh['fingerprint'])['state'])->toBe('complete');
    expect(File::get($file))->toBe($contents);
});

it('rejects closure with a stale file proof or missing reason', function (string $change) {
    [$run, $context] = interruptedPublicationFixture();
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $proof = $runner->previewClosure($run['id'], $context);
    if ($change === 'file') {
        File::append(File::allFiles(lang_path())[0]->getPathname(), "\n// Changed after closure preview\n");
    }
    expect(fn () => $runner->closeWithoutWriting($run['id'], $context, $proof['fingerprint'], $change === 'reason' ? '  ' : 'Reviewed'))
        ->toThrow(ValidationException::class);
    expect(TranslationWorkbenchReview::where('decision', 'publication_closed_without_writing')->count())->toBe(0)
        ->and($runner->runs(...array_values($context))[0]['state'])->toBe('interrupted');
})->with(['file', 'reason']);

it('does not release an unfinished publication when recording its closure fails', function () {
    [$run, $context] = interruptedPublicationFixture();
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $proof = $runner->previewClosure($run['id'], $context);
    TranslationWorkbenchReview::creating(function ($review) {
        if ($review->decision === 'publication_closed_without_writing') { throw new RuntimeException('Closure receipt failed'); }
    });
    try {
        expect(fn () => $runner->closeWithoutWriting($run['id'], $context, $proof['fingerprint'], 'Reviewed'))
            ->toThrow(RuntimeException::class, 'Closure receipt failed');
    } finally {
        TranslationWorkbenchReview::flushEventListeners();
    }
    expect($runner->runs(...array_values($context))[0]['state'])->toBe('interrupted')
        ->and(TranslationWorkbenchReview::where('decision', 'publication_closed_without_writing')->count())->toBe(0);
    $fresh = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)
        ->preview('scalar', $context['keyId'], $context['findingId'], ['de', 'en']);
    expect(fn () => $runner->publish('scalar', $context['keyId'], $context['findingId'], ['de', 'en'], $fresh['fingerprint']))
        ->toThrow(ValidationException::class);
});

it('rejects closure for completed publications and other contexts', function () {
    [$keyId, $findingId, $report] = approvedPublicationFixture();
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $run = $runner->publish('scalar', $keyId, $findingId, ['de', 'en'], $report['fingerprint']);
    expect(fn () => $runner->previewClosure($run['id'], ['kind' => 'option', 'keyId' => $keyId, 'findingId' => $findingId]))->toThrow(ValidationException::class);
    expect(fn () => $runner->previewClosure($run['id'], ['kind' => 'scalar', 'keyId' => $keyId, 'findingId' => $findingId]))->toThrow(ValidationException::class);
});

it('closes an interrupted publication through its locked UI preview and clears that preview', function () {
    [$run, $context] = interruptedPublicationFixture();
    $editor = new TranslationWorkbenchEntries;
    $editor->openEditModal($context['findingId']);
    $editor->openValueApproval('scalar');
    $editor->previewValuePublicationClosure(0);
    expect($editor->valuePublicationClosure['id'])->toBe($run['id']);
    $editor->valuePublicationClosureReason = 'Retain partial output; prepare new publication.';
    $editor->closeValuePublicationWithoutWriting();
    expect($editor->valuePublicationClosure)->toBeNull()
        ->and($editor->valuePublicationClosureReason)->toBe('')
        ->and($editor->valuePublicationRuns[0]['state'])->toBe('closed')
        ->and($editor->valuePublicationRuns[0]['closure_reason'])->toBe('Retain partial output; prepare new publication.');
    expect(fn () => $editor->closeValuePublicationWithoutWriting())->toThrow(ValidationException::class);
});

it('rechecks access before previewing or recording publication closure', function () {
    [$run, $context] = interruptedPublicationFixture();
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $proof = $runner->previewClosure($run['id'], $context);
    auth()->forgetUser();
    expect(fn () => $runner->previewClosure($run['id'], $context))->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
    expect(fn () => $runner->closeWithoutWriting($run['id'], $context, $proof['fingerprint'], 'Reviewed'))
        ->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
    expect(TranslationWorkbenchReview::where('decision', 'publication_closed_without_writing')->count())->toBe(0);
});

function publicationCommandActor(): void
{
    $actor = auth()->user();
    $provider = Mockery::mock(\Illuminate\Contracts\Auth\UserProvider::class);
    $provider->shouldReceive('retrieveById')->with(1)->andReturn($actor);
    auth()->guard()->setProvider($provider);
}

it('publishes an explicit CLI preview through the shared runner and restores its caller', function () {
    [$keyId, $findingId] = approvedPublicationFixture();
    publicationCommandActor();
    $actor = auth()->user();
    $options = ['--actor' => '1', '--finding' => (string) $findingId,
        '--key' => [TranslationWorkbenchKey::findOrFail($keyId)->translation_key], '--locale' => ['de', 'en']];
    expect(Artisan::call('translation-workbench:export-lang-files', $options))->toBe(0);
    $report = json_decode(File::get(storage_path('translation-workbench/translation-workbench-export-lang-files.json')), true);
    expect($report['dry_run'])->toBeTrue()->and(File::allFiles(lang_path()))->toHaveCount(0);
    expect(Artisan::call('translation-workbench:export-lang-files', $options + ['--write' => true, '--fingerprint' => $report['summary']['fingerprint']]))->toBe(0);
    expect(Artisan::output())->toContain('Publication run:', 'complete')
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(1)
        ->and(auth()->user())->toBe($actor);
    $id = TranslationWorkbenchReview::where('decision', 'lang_files_published')->sole()->meta['run_id'];
    expect(Artisan::call('translation-workbench:export-lang-files', ['--actor' => '1', '--resume' => $id]))->toBe(0)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(1);
});

it('rejects CLI publication without proof or with a stale proof before writes', function (string $mode) {
    [$keyId, $findingId, $report] = approvedPublicationFixture();
    publicationCommandActor();
    $options = ['--actor' => '1', '--finding' => (string) $findingId,
        '--key' => [TranslationWorkbenchKey::findOrFail($keyId)->translation_key], '--locale' => ['de', 'en'], '--write' => true];
    if ($mode === 'stale') {
        $options['--fingerprint'] = $report['fingerprint'];
        TranslationWorkbenchLangValue::where('locale', 'de')->update(['value' => 'Changed after CLI preview']);
    }
    expect(Artisan::call('translation-workbench:export-lang-files', $options))->toBe(1)
        ->and(File::allFiles(lang_path()))->toHaveCount(0)
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0);
})->with(['missing', 'stale']);

it('rejects broad CLI writes and rejects automatic pipeline publication before scanning', function () {
    expect(Artisan::call('translation-workbench:export-lang-files', ['--write' => true]))->toBe(1);
    expect(Artisan::output())->toContain('--actor');
    File::put(base_path('resources/views/not-scanned.blade.php'), "{{ __('Must not be scanned') }}");
    $before = TranslationWorkbenchFinding::count();
    expect(Artisan::call('translation:workbench', ['--write-lang-files' => true]))->toBe(1);
    expect(Artisan::output())->toContain('separate guarded preview')
        ->and(TranslationWorkbenchFinding::count())->toBe($before)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('rejects an invalid CLI actor and restores the original authenticated user', function (bool $missing) {
    $original = auth()->user();
    $actor = $missing ? null : Mockery::mock(User::class)->makePartial();
    if ($actor !== null) {
        $actor->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(false);
    }
    $provider = Mockery::mock(\Illuminate\Contracts\Auth\UserProvider::class);
    $provider->shouldReceive('retrieveById')->with(12)->andReturn($actor);
    auth()->guard()->setProvider($provider);
    expect(Artisan::call('translation-workbench:export-lang-files', ['--actor' => '12', '--write' => true]))->toBe(1)
        ->and(auth()->user())->toBe($original)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
})->with([false, true]);

it('runs a complete tracked pipeline with publication explicitly excluded', function () {
    $options = RunTranslationWorkbench::executionDefaults();
    foreach (array_keys($options) as $option) {
        if (str_starts_with($option, '--skip-')) { $options[$option] = true; }
    }
    $options['--complete'] = true;
    $run = app(TranslationWorkbenchPipelineRunTracker::class)->create('translation:workbench', [
        ...$options, 'started_from' => 'ui', 'user_id' => 1,
    ]);
    $access = Mockery::mock(TranslationWorkbenchPipelineAccess::class)->makePartial();
    $access->shouldReceive('authorize')->with((string) $run->id)->andReturnNull();
    app()->instance(TranslationWorkbenchPipelineAccess::class, $access);
    expect(Artisan::call('translation:workbench', $options + ['--pipeline-run-id' => (string) $run->id]))->toBe(0);
    expect($run->fresh()->status)->toBe('finished')
        ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(0)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('rejects legacy language writes before reading any legacy inventory', function () {
    expect(Schema::hasTable('translation_keys'))->toBeFalse();
    expect(Artisan::call('translations:export-lang-files', ['--locales' => 'de']))->toBe(1);
    expect(Artisan::output())->toContain('Legacy export writes are disabled', '--dry-run')
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('keeps legacy export comparison read only even without workbench conflicts', function () {
    createLegacyExportTables();
    DB::table('translation_keys')->insert(['id' => 1, 'key' => 'ui.old', 'group' => 'ui', 'status' => 'ok']);
    DB::table('translation_values')->insert(['translation_key_id' => 1, 'locale' => 'de', 'value' => 'Legacy value', 'status' => 'ok']);
    expect(Artisan::call('translations:export-lang-files', ['--locales' => 'de', '--dry-run' => true]))->toBe(0);
    expect(Artisan::output())->toContain('would_create', 'comparison finished')
        ->and(File::exists(lang_path('de')))->toBeFalse()
        ->and(TranslationWorkbenchReview::count())->toBe(0);
});

it('shows root scalar structure errors without changing keys and blocks publication', function () {
    [, , $keyId, $findingId] = workbenchApprovalValue('scalar');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    $key->update(['translation_key' => 'ui.type']);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)
        ->preview('scalar', $keyId, $findingId, ['de']))->toThrow(ValidationException::class, 'Root scalar translation key');
    expect($key->fresh()->translation_key)->toBe('ui.type')
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
});

it('distinguishes root scalar keys from nested leaves including package namespaces', function () {
    $classifier = \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangNodeClassifier::class;
    expect($classifier::isRootScalarKey('ui.type'))->toBeTrue()
        ->and($classifier::isRootScalarKey('package::ui.type'))->toBeTrue()
        ->and($classifier::isRootScalarKey('ui.type.type'))->toBeFalse()
        ->and($classifier::isRootScalarKey('ui.button.type'))->toBeFalse()
        ->and($classifier::isRootScalarKey('package::ui.type.type'))->toBeFalse()
        ->and($classifier::isRootScalarKey(null))->toBeFalse();
});

it('reports duplicate active owners separately and blocks publication without choosing one', function () {
    [, , $keyId, $findingId] = workbenchApprovalValue('scalar');
    $key = TranslationWorkbenchKey::findOrFail($keyId);
    $duplicate = $key->replicate();
    $duplicate->fingerprint = hash('sha256', 'second-owner');
    $duplicate->save();
    $ownership = \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyOwnership::class;
    expect($ownership::ids($key->translation_key))->toBe([$keyId, $duplicate->id]);
    $review = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangNodeClassifier::class)->reviewCandidate($key->translation_key, $keyId);
    expect($review['active_owner_ids'])->toBe([$keyId, $duplicate->id]);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->preview('scalar', $keyId, $findingId, ['de']))
        ->toThrow(ValidationException::class, 'Multiple active records');
    expect(TranslationWorkbenchTimelineEvent::count())->toBe($events)->and(File::allFiles(lang_path()))->toHaveCount(0);
    $duplicate->update(['status' => 'obsolete']);
    expect($ownership::ids($key->translation_key))->toBe([$keyId]);
    expect(app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->preview('scalar', $keyId, $findingId, ['de'])['dry_run'])->toBeTrue();
});

it('filters only displayed key errors before pagination and respects other conditions', function () {
    $rows = [
        ['ui.type', false, 'leaf'], ['ui.type.type', false, 'leaf'],
        ['ui.status', true, 'container'], ['ui.group', false, 'container'],
        ['ui.same.leaf', false, 'leaf'], ['ui.same.leaf', false, 'leaf'],
        ['ui.history.leaf', false, 'leaf'], ['ui.history.leaf', false, 'leaf'],
        ['package::ui.root', false, 'leaf'],
    ];
    foreach ($rows as $i => [$key, $multi, $node]) {
        TranslationWorkbenchKey::create(['fingerprint' => hash('sha256', 'error-filter-'.$i), 'translation_key' => $key,
            'status' => $i === 7 ? 'obsolete' : 'open', 'is_dynamic_multi' => $multi, 'lang_node_type' => $node]);
    }
    $query = DB::table('translation_workbench_keys as keys')->where('keys.status', '<>', 'obsolete');
    \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingErrors::apply($query);
    expect((clone $query)->orderBy('keys.id')->pluck('keys.translation_key')->all())
        ->toBe(['ui.type', 'ui.same.leaf', 'ui.same.leaf', 'package::ui.root']);
    expect((clone $query)->where('keys.translation_key', 'ui.type')->count())->toBe(1);
    expect((clone $query)->paginate(2)->total())->toBe(4);
});

it('compares stored and published values in the editor without approving or saving unsaved input', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $key = TranslationWorkbenchKey::sole();
    installPublishedValueFixture($key->translation_key, ['de']);
    $file = File::allFiles(lang_path())[0]->getPathname();
    File::put($file, str_replace('Willkommen', 'Legacy text', File::get($file)));
    $editor->openEditModal(TranslationWorkbenchFinding::sole()->id);
    $editor->targetTranslationValue = 'Unsaved text';
    $before = File::get($file);
    $events = TranslationWorkbenchTimelineEvent::count();
    $reviews = TranslationWorkbenchReview::count();
    $editor->compareStoredPublication();
    $changes = collect($editor->storedPublicationComparison['plans'])->firstWhere('locale', 'de')['changes'];
    expect($changes[0]['old_value'])->toBe('Legacy text')->and($changes[0]['new_value'])->toBe('Willkommen')
        ->and($editor->targetTranslationValue)->toBe('Unsaved text')
        ->and(File::get($file))->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews);
    $editor->closeEditModal();
    expect($editor->storedPublicationComparison)->toBeNull();
    expect(fn () => $editor->compareStoredPublication())->toThrow(ValidationException::class);
});


it('blocks a rename with shared active source ownership without moving shared values', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $key = TranslationWorkbenchKey::sole();
    $other = $key->replicate();
    $other->fingerprint = hash('sha256', 'competing-rename-owner');
    $other->save();
    $values = TranslationWorkbenchLangValue::orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $editor->translationKeyFindingId = TranslationWorkbenchFinding::sole()->id;
    $editor->translationKeyValue = 'ui.corrected.leaf';
    expect(fn () => $editor->saveTranslationKeyModal())->toThrow(ValidationException::class, 'Multiple active records share');
    expect($key->fresh()->translation_key)->toBe($other->translation_key)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toJson())->toBe($values)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
});

it('inspects shared key assignments and occupied targets without modifying values or history', function () {
    $editor = workbenchValueEditor();
    $editor->saveTranslationValue();
    $key = TranslationWorkbenchKey::sole();
    $other = $key->replicate();
    $other->fingerprint = hash('sha256', 'inspection-owner');
    $other->review_status = 'pending';
    $other->reviewed_at = null;
    $other->save();
    $key->update(['review_status' => 'reviewed', 'reviewed_at' => '2026-07-30 10:01:03']);
    $ownersBefore = TranslationWorkbenchKey::orderBy('id')->get()->toJson();
    $target = TranslationWorkbenchLangValue::first()->replicate();
    $target->translation_key = 'ui.target.leaf';
    $target->lang_key = 'target.leaf';
    $target->status = 'obsolete';
    $target->save();
    $before = TranslationWorkbenchLangValue::orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $reviews = TranslationWorkbenchReview::count();
    $editor->openTranslationKeyModal(TranslationWorkbenchFinding::sole()->id);
    $editor->translationKeyValue = 'ui.target.leaf';
    $editor->inspectTranslationKeyConflict();
    $groups = $editor->keyConflictInspection['groups'];
    expect($groups)->toHaveCount(2)
        ->and(array_column($groups[0]['owners'], 'id'))->toBe([$key->id, $other->id])
        ->and(array_column($groups[0]['owners'], 'review_status'))->toBe(['reviewed', 'pending'])
        ->and(array_column($groups[0]['owners'], 'reviewed_at'))->toBe(['2026-07-30 10:01:03', null])
        ->and($groups[0]['findings'][0]['id'])->toBe(TranslationWorkbenchFinding::sole()->id)
        ->and($groups[1]['values'][0]['status'])->toBe('obsolete')
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews)
        ->and(TranslationWorkbenchKey::orderBy('id')->get()->toJson())->toBe($ownersBefore);
    $editor->closeTranslationKeyModal();
    expect($editor->keyConflictInspection)->toBeNull();
    expect(fn () => $editor->inspectTranslationKeyConflict())->toThrow(ValidationException::class);
});

it('denies key inspection without editor authorization and locks its snapshot', function () {
    $property = new ReflectionProperty(TranslationWorkbenchEntries::class, 'keyConflictInspection');
    expect($property->getAttributes(\Livewire\Attributes\Locked::class))->toHaveCount(1);
    $editor = workbenchValueEditor();
    $editor->openTranslationKeyModal(TranslationWorkbenchFinding::sole()->id);
    Auth::forgetUser();
    expect(fn () => $editor->inspectTranslationKeyConflict())->toThrow(AuthorizationException::class);
    expect($editor->keyConflictInspection)->toBeNull();
});

it('lists shared key sources by owner and finding while excluding inactive assignments', function () {
    $editor = workbenchValueEditor();
    $key = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $other = $key->replicate();
    $other->fingerprint = hash('sha256', 'source-list-owner');
    $other->save();
    $link = TranslationWorkbenchKeyFinding::first()->replicate();
    $link->key_id = $other->id;
    $link->status = 'obsolete';
    $link->save();
    $editor->openTranslationKeyModal($finding->id);
    expect($editor->translationKeySources)->toHaveCount(2)
        ->and($editor->translationKeySources[0]['key_id'])->toBe($key->id)
        ->and($editor->translationKeySources[0]['findings'][0]['id'])->toBe($finding->id)
        ->and($editor->translationKeySources[1]['findings'])->toBe([]);
    $link->update(['status' => 'active']);
    $editor->openTranslationKeyModal($finding->id);
    expect($editor->translationKeySources[1]['findings'][0]['id'])->toBe($finding->id);
    $other->update(['status' => 'obsolete']);
    $editor->openTranslationKeyModal($finding->id);
    expect($editor->translationKeySources)->toHaveCount(1);
    $editor->closeTranslationKeyModal();
    expect($editor->translationKeySources)->toBe([]);
});

it('refuses to recreate consolidated assignments on repeated foundation syncs atomically', function (string $case) {
    File::put(base_path('resources/views/consolidated.blade.php'), "{{ __('ui.shared.title') }}");
    $items = app(TranslationWorkbenchScanner::class)->scan(['resources/views']);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($items);
    $key = TranslationWorkbenchKey::sole();
    $relation = TranslationWorkbenchKeyFinding::sole();
    $canonical = $key->replicate();
    $canonical->fingerprint = hash('sha256', 'canonical-owner');
    $canonical->save();
    $meta = ['source' => 'duplicate_key_consolidation', 'canonical_key_id' => $canonical->id, 'duplicate_key_id' => $key->id];
    if ($case === 'moved') {
        $relation->update(['key_id' => $canonical->id, 'meta' => $meta]);
    } else {
        $relation->update(['status' => $case, 'meta' => $case === 'merged' ? [] : $meta]);
    }
    $key->update(['status' => 'obsolete']);
    $before = [];
    foreach (['keys', 'findings', 'key_findings', 'timeline_events'] as $table) {
        $before[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson();
    }
    foreach ([1, 2] as $attempt) {
        expect(fn () => $syncer->sync($items))->toThrow(InvalidArgumentException::class, 'recorded consolidation');
        foreach ($before as $table => $rows) {
            expect(DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson())->toBe($rows);
        }
    }
})->with(['moved', 'obsolete', 'merged']);

it('refuses to choose between multiple reviewed owners during foundation sync', function () {
    File::put(base_path('resources/views/ambiguous.blade.php'), "{{ __('ui.shared.title') }}");
    $items = app(TranslationWorkbenchScanner::class)->scan(['resources/views']);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($items);
    $key = TranslationWorkbenchKey::sole();
    $key->update(['review_status' => 'reviewed']);
    $other = $key->replicate();
    $other->fingerprint = hash('sha256', 'second-reviewed-owner');
    $other->save();
    $links = TranslationWorkbenchKeyFinding::get()->toJson();
    expect(fn () => $syncer->sync($items))->toThrow(InvalidArgumentException::class, 'multiple reviewed owners');
    expect(TranslationWorkbenchKeyFinding::get()->toJson())->toBe($links);
});

it('preserves canonical consolidation evidence and ordinary reappearance during foundation sync', function () {
    File::put(base_path('resources/views/canonical.blade.php'), "{{ __('ui.shared.title') }}");
    $items = app(TranslationWorkbenchScanner::class)->scan(['resources/views']);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($items);
    $key = TranslationWorkbenchKey::sole();
    $relation = TranslationWorkbenchKeyFinding::sole();
    $meta = ['source' => 'duplicate_key_consolidation', 'canonical_key_id' => $key->id, 'duplicate_key_id' => 999999];
    $relation->update(['meta' => $meta]);
    foreach ([1, 2] as $attempt) {
        expect($syncer->sync($items)['relations_created'])->toBe(0);
        expect($relation->fresh()->meta)->toBe($meta);
    }
    $relation->update(['status' => 'obsolete', 'meta' => ['source' => 'foundation_sync']]);
    $key->update(['status' => 'obsolete']);
    $syncer->sync($items);
    expect($relation->fresh()->status)->toBe('active')->and($key->fresh()->status)->toBe('open');
});

it('plans source moves new and missing occurrences without changing findings or reviewed keys', function () {
    File::put(base_path('resources/views/move.blade.php'), "{{ __('ui.saved.title') }}");
    File::put(base_path('resources/views/remove.blade.php'), "{{ __('Removed literal') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    app(TranslationWorkbenchFoundationSyncer::class)->sync($scanner->scan(['resources/views']));
    $key = TranslationWorkbenchKey::where('translation_key', 'ui.saved.title')->sole();
    $key->update(['translation_key' => 'ui.edited.title', 'review_status' => 'reviewed']);
    File::put(base_path('resources/views/move.blade.php'), "\n\n{{ __('ui.saved.title') }}");
    File::put(base_path('resources/views/remove.blade.php'), '');
    File::put(base_path('resources/views/new.blade.php'), "{{ __('New literal') }}");
    $before = TranslationWorkbenchFinding::orderBy('id')->get()->toJson();
    $events = TranslationWorkbenchTimelineEvent::count();
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation::class)->plan(['resources/views']);
    $move = collect($report['rows'])->firstWhere('state', 'moved');
    expect($move['old_line'])->toBe(1)->and($move['line'])->toBe(3)
        ->and($move['reviewed_key_differs_from_code'])->toBeTrue()
        ->and($report['summary']['new'])->toBe(1)->and($report['summary']['missing'])->toBe(1)
        ->and(TranslationWorkbenchFinding::orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and($key->fresh()->translation_key)->toBe('ui.edited.title');
});

it('does not propose removing ambiguous or out of scope source occurrences', function () {
    File::put(base_path('resources/views/repeat.blade.php'), "{{ __('Repeated') }}");
    File::put(base_path('resources/views/outside.blade.php'), "{{ __('Outside') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    File::put(base_path('resources/views/repeat.blade.php'), "\n{{ __('Repeated') }}\n{{ __('Repeated') }}");
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation::class)->plan(['resources/views/repeat.blade.php']);
    expect($report['summary'])->toBe(['ambiguous' => 2])
        ->and(array_column($report['rows'], 'path'))->not->toContain('resources/views/outside.blade.php');
});

it('reports competing source assignments using the reconciliation command without writing', function () {
    File::put(base_path('resources/views/shared.blade.php'), "{{ __('Shared') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $other = TranslationWorkbenchKey::sole()->replicate();
    $other->fingerprint = hash('sha256', 'reconcile-other');
    $other->save();
    $link = TranslationWorkbenchKeyFinding::sole()->replicate();
    $link->key_id = $other->id;
    $link->save();
    $before = TranslationWorkbenchKeyFinding::get()->toJson();
    $this->artisan('translation-workbench:reconcile-sources', ['--paths' => 'resources/views', '--json' => true])
        ->expectsOutputToContain('multiple_active_assignments')->assertSuccessful();
    expect(TranslationWorkbenchKeyFinding::get()->toJson())->toBe($before);
});

it('applies saved position repairs with a successor and preserves reviewed keys through rescan', function () {
    $file = base_path('resources/views/repair-position.blade.php');
    File::put($file, "{{ __('ui.example.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['review_status' => 'reviewed']);
    $original = collect($finding->getAttributes())->only(['id', 'first_seen_at', 'fingerprint', 'raw_expression'])->all();
    File::put($file, "\n\n{{ __('ui.example.title') }}");
    $repair = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class);
    $plan = json_decode(json_encode($repair->plan(['resources/views'])), true);
    expect($plan['actions'])->toHaveCount(1);
    expect($repair->apply($plan)[0]['state'])->toBe('applied');
    expect($finding->fresh()->source_line)->toBe(1)->and($finding->fresh()->status)->toBe('obsolete')->and(collect($finding->fresh()->getAttributes())->only(array_keys($original))->all())->toBe($original);
    $successor = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($successor->source_line)->toBe(3)->and($successor->meta['predecessor_finding_id'])->toBe($finding->id);
    $events = TranslationWorkbenchTimelineEvent::count();
    expect($repair->apply($plan)[0]['state'])->toBe('skipped')->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    $syncer->sync($scanner->scan(['resources/views']));
    expect(TranslationWorkbenchFinding::where('status', 'active')->sole()->id)->toBe($successor->id)
        ->and($key->fresh()->review_status)->toBe('reviewed')
        ->and($repair->plan(['resources/views'])['actions'])->toBe([]);
});

it('skips stale source repair plans and rolls back position changes if history fails', function () {
    $file = base_path('resources/views/stale-position.blade.php');
    File::put($file, "{{ __('ui.example.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    File::put($file, "\n{{ __('ui.example.title') }}");
    $repair = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class);
    $plan = $repair->plan(['resources/views']);
    File::put($file, "\n\n{{ __('ui.example.title') }}");
    expect($repair->apply($plan)[0]['state'])->toBe('skipped');
    $plan = $repair->plan(['resources/views']);
    $recorder = Mockery::mock(TranslationWorkbenchTimelineRecorder::class);
    $recorder->shouldReceive('record')->andReturn(new TranslationWorkbenchTimelineEvent);
    $recorder->shouldReceive('recordFindingEvent')->once()->andThrow(new RuntimeException('History unavailable'));
    $repair = new \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair(app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation::class), $recorder);
    expect(fn () => $repair->apply($plan))->toThrow(RuntimeException::class, 'History unavailable');
    expect(TranslationWorkbenchFinding::sole()->source_line)->toBe(1);
});

it('keeps repeated literals in separate files independent after retiring historical foreign links', function () {
    $paths = ['resources/views/literal-owner-a.blade.php', 'resources/views/literal-owner-b.blade.php', 'resources/views/literal-owner-c.blade.php'];
    foreach ($paths as $index => $path) {
        // Equal literals with differently formatted calls remain separate source occurrences.
        $call = $index === 2 ? "{{ __( 'Ready' ) }}" : "{{ __('Ready') }}";
        File::put(base_path($path), $call."\n".$call);
    }
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan($paths));
    expect(TranslationWorkbenchFinding::count())->toBe(6)->and(TranslationWorkbenchKey::count())->toBe(3);
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    $identity = $findings->map->only(['id', 'source_file_id', 'source_signature', 'suggested_key', 'raw_expression'])->all();
    $links = TranslationWorkbenchKeyFinding::orderBy('id')->get();
    $foreign = $links->first()->replicate();
    $foreign->finding_id = $findings->last()->id;
    $foreign->status = 'obsolete';
    $foreign->meta = ['source' => 'foundation_sync', 'obsolete_reason' => 'historical_cross_file_finding_reuse'];
    $foreign->save();
    $foreignBefore = $foreign->fresh()->getAttributes();
    $assignments = $links->map->only(['id', 'key_id', 'finding_id', 'status'])->all();

    $syncer->sync($scanner->scan($paths));
    $syncer->sync($scanner->scan(array_reverse($paths)));

    expect(TranslationWorkbenchFinding::orderBy('id')->get()->map->only(['id', 'source_file_id', 'source_signature', 'suggested_key', 'raw_expression'])->all())->toBe($identity)
        ->and(TranslationWorkbenchKey::count())->toBe(3)
        ->and(TranslationWorkbenchKeyFinding::where('status', 'active')->orderBy('id')->get()->map->only(['id', 'key_id', 'finding_id', 'status'])->all())->toBe($assignments)
        ->and($foreign->fresh()->getAttributes())->toBe($foreignBefore);
});

it('repairs only evidenced duplicate assignments and keeps consolidation stable on rescan', function () {
    File::put(base_path('resources/views/consolidation-repair.blade.php'), "{{ __('ui.example.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $items = $scanner->scan(['resources/views']);
    $syncer->sync($items);
    $source = TranslationWorkbenchKey::sole();
    $target = $source->replicate();
    $target->fingerprint = hash('sha256', 'repair-canonical');
    $target->save();
    $link = TranslationWorkbenchKeyFinding::sole();
    $canonicalLink = $link->replicate();
    $canonicalLink->key_id = $target->id;
    $canonicalLink->meta = ['source' => 'duplicate_key_consolidation', 'duplicate_key_id' => $source->id, 'canonical_key_id' => $target->id];
    $canonicalLink->save();
    $repair = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class);
    $plan = json_decode(json_encode($repair->plan(['resources/views'])), true);
    expect($plan['actions'])->toHaveCount(1)->and($plan['actions'][0]['type'])->toBe('consolidate');
    expect($repair->apply($plan)[0]['state'])->toBe('applied');
    expect($source->fresh()->status)->toBe('obsolete')->and($link->fresh()->status)->toBe('merged');
    $syncer->sync($items);
    $syncer->sync($items);
    expect(TranslationWorkbenchKeyFinding::where('status', 'active')->count())->toBe(1)
        ->and($source->fresh()->status)->toBe('obsolete')->and($repair->plan(['resources/views'])['actions'])->toBe([]);
});

it('saves signed source repair plans and refuses tampered CLI plans', function () {
    File::put(base_path('resources/views/cli-repair.blade.php'), "{{ __('ui.example.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    File::put(base_path('resources/views/cli-repair.blade.php'), "\n{{ __('ui.example.title') }}");
    $this->artisan('translation-workbench:reconcile-sources', ['--paths' => 'resources/views', '--save-plan' => true])->assertSuccessful();
    $files = glob(storage_path('app/private/translation-workbench/source-repairs/*.json'));
    expect($files)->toHaveCount(1)->and(TranslationWorkbenchFinding::sole()->source_line)->toBe(1);
    $id = basename($files[0], '.json');
    $original = File::get($files[0]);
    $envelope = json_decode($original, true);
    $envelope['payload'] .= ' ';
    File::put($files[0], json_encode($envelope));
    $this->artisan('translation-workbench:reconcile-sources', ['--apply' => $id])->expectsOutputToContain('signature is invalid')->assertFailed();
    expect(TranslationWorkbenchFinding::sole()->source_line)->toBe(1);
    File::put($files[0], $original);
    $this->artisan('translation-workbench:reconcile-sources', ['--apply' => $id])->assertSuccessful();
    expect(TranslationWorkbenchFinding::where('status', 'active')->sole()->source_line)->toBe(2)
        ->and(TranslationWorkbenchFinding::where('status', 'obsolete')->sole()->source_line)->toBe(1);
});

it('does not plan consolidation without per-finding evidence or with owned translations', function (string $case) {
    File::put(base_path('resources/views/no-consolidate.blade.php'), "{{ __('ui.example.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $source = TranslationWorkbenchKey::sole();
    $target = $source->replicate();
    $target->fingerprint = hash('sha256', 'noneligible-canonical');
    $target->save();
    $canonicalLink = TranslationWorkbenchKeyFinding::sole()->replicate();
    $canonicalLink->key_id = $target->id;
    $canonicalLink->meta = $case === 'no_evidence' ? [] : ['source' => 'duplicate_key_consolidation', 'duplicate_key_id' => $source->id, 'canonical_key_id' => $target->id];
    $canonicalLink->save();
    if ($case === 'values') {
        DB::table('translation_workbench_key_values')->insert(['key_id' => $source->id, 'locale' => 'en', 'value' => 'Keep']);
    }
    expect(app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class)->plan(['resources/views'])['actions'])->toBe([]);
})->with(['no_evidence', 'values']);

it('uses historical sync reactivation evidence only without later contrary decisions', function (string $case) {
    File::put(base_path('resources/views/timeline-repair.blade.php'), "{{ __('ui.example.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $items = $scanner->scan(['resources/views']);
    $syncer->sync($items);
    $source = TranslationWorkbenchKey::sole();
    $target = $source->replicate();
    $target->fingerprint = hash('sha256', 'timeline-canonical');
    $target->save();
    $link = TranslationWorkbenchKeyFinding::sole()->replicate();
    $link->key_id = $target->id;
    $link->save();
    $event = app(TranslationWorkbenchTimelineRecorder::class)->record('key_finding_relation_changed', key: $source,
        finding: TranslationWorkbenchFinding::sole(),
        oldValues: ['status' => 'obsolete', 'meta' => ['source' => 'duplicate_key_consolidation', 'canonical_key_id' => $target->id, 'duplicate_key_id' => $source->id]],
        newValues: ['status' => 'active', 'meta' => ['source' => 'foundation_sync']],
        context: ['source' => $case === 'wrong_source' ? 'manual' : 'translation-workbench:sync-foundation']);
    if ($case === 'classification') {
        $classification = app(TranslationWorkbenchTimelineRecorder::class)->record('lang_node_type_classified', key: $source,
            oldValues: ['lang_node_type' => 'unknown'], newValues: ['lang_node_type' => 'leaf', 'translation_key' => $source->translation_key],
            context: ['source' => 'translation-workbench:classify-lang-node-types']);
        $classification->update(['created_by_user_id' => null]);
    }
    if ($case === 'later_decision') {
        app(TranslationWorkbenchTimelineRecorder::class)->record('manual_key_decision', key: $source, context: ['source' => 'review-modal']);
    }
    $repair = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceRepair::class);
    $plan = json_decode(json_encode($repair->plan(['resources/views'])), true);
    if (! in_array($case, ['valid', 'stale', 'classification'], true)) {
        expect($plan['actions'])->toBe([]);
        return;
    }
    expect($plan['actions'])->toHaveCount(1)
        ->and($plan['actions'][0]['before']['decisions'][0]['evidence_event_id'])->toBe($event->id);
    if ($case === 'stale') {
        app(TranslationWorkbenchTimelineRecorder::class)->record('manual_key_decision', key: $source, context: ['source' => 'review-modal']);
        expect($repair->apply($plan)[0]['state'])->toBe('skipped')->and($source->fresh()->status)->toBe('open');
        return;
    }
    $historical = $event->fresh()->getAttributes();
    expect($repair->apply($plan)[0]['state'])->toBe('applied');
    $syncer->sync($items);
    expect($source->fresh()->status)->toBe('obsolete')
        ->and(TranslationWorkbenchKeyFinding::where('status', 'active')->count())->toBe(1)
        ->and($event->fresh()->getAttributes())->toBe($historical);
})->with(['valid', 'classification', 'stale', 'later_decision', 'wrong_source']);

it('sorts source findings numerically or by path and line within their own key group', function () {
    $editor = new TranslationWorkbenchEntries;
    $editor->translationKeyModalOpen = true;
    $editor->translationKeySources = [
        ['key_id' => 7, 'findings' => [
            ['id' => 12, 'path' => 'views/b.blade.php', 'source_line' => 1],
            ['id' => 3, 'path' => 'views/a.blade.php', 'source_line' => 20],
            ['id' => 21, 'path' => 'views/a.blade.php', 'source_line' => 2],
        ]],
        ['key_id' => 8, 'findings' => [['id' => 99]]],
    ];
    $editor->sortTranslationKeySources(7, 'id');
    expect(array_column($editor->translationKeySources[0]['findings'], 'id'))->toBe([3, 12, 21]);
    $editor->sortTranslationKeySources(7, 'id');
    expect(array_column($editor->translationKeySources[0]['findings'], 'id'))->toBe([21, 12, 3]);
    $editor->sortTranslationKeySources(7, 'path');
    expect(array_column($editor->translationKeySources[0]['findings'], 'id'))->toBe([21, 3, 12])
        ->and($editor->translationKeySources[1]['findings'])->toBe([['id' => 99]]);
    $before = $editor->translationKeySources;
    $editor->sortTranslationKeySources(7, 'unknown');
    $editor->sortTranslationKeySources(999, 'id');
    expect($editor->translationKeySources)->toBe($before)
        ->and($editor->translationKeySourceSorts)->toBe([7 => ['field' => 'path', 'direction' => 'asc']]);
    $editor->closeTranslationKeyModal();
    expect($editor->translationKeySourceSorts)->toBe([]);
});

it('loads a fresh key inspection when its disclosure opens and clears it on close', function () {
    $editor = workbenchValueEditor();
    $editor->openTranslationKeyModal(TranslationWorkbenchFinding::sole()->id);
    expect($editor->keyConflictInspectionOpen)->toBeFalse()
        ->and($editor->keyConflictInspection)->toBeNull();
    $editor->keyConflictInspectionOpen = true;
    $editor->updatedKeyConflictInspectionOpen(true);
    expect($editor->keyConflictInspection['target'])->toBe($editor->translationKeyValue);
    $editor->keyConflictInspectionOpen = false;
    $editor->updatedKeyConflictInspectionOpen(false);
    expect($editor->keyConflictInspection)->toBeNull();
    $editor->translationKeyValue = 'ui.changed.example';
    $editor->keyConflictInspectionOpen = true;
    $editor->updatedKeyConflictInspectionOpen(true);
    expect($editor->keyConflictInspection['target'])->toBe('ui.changed.example');
    $editor->closeTranslationKeyModal();
    expect($editor->keyConflictInspectionOpen)->toBeFalse()
        ->and($editor->keyConflictInspection)->toBeNull();
});

it('filters an exact finding ID while preserving lifecycle toggles and other filters', function () {
    $editor = workbenchBulkEditor();
    $editor->findingKeyRelation = 'all';
    $editor->bulkEqualizeSelectedFindingIds = [];
    $finding = TranslationWorkbenchFinding::orderBy('id')->first();
    $ids = fn () => (new ReflectionMethod($editor, 'filteredFindingIds'))->invoke($editor);
    $editor->findingIdSearch = (string) $finding->id;
    expect(array_map('intval', $ids()))->toBe([$finding->id]);
    $editor->findingSearch = 'no-such-source-file';
    expect($ids())->toBe([]);
    $editor->findingSearch = '';
    $finding->update(['status' => 'obsolete']);
    expect($ids())->toBe([]);
    $editor->showObsoleteFindings = true;
    expect(array_map('intval', $ids()))->toBe([$finding->id]);
    $finding->update(['status' => 'commented_out']);
    expect($ids())->toBe([]);
    $editor->findingCommentedOutScope = 'commentedOutPresentYes';
    expect(array_map('intval', $ids()))->toBe([$finding->id]);
    foreach (['abc', '1 OR 1=1', '-1', '99999999999999999999999999'] as $invalid) {
        $editor->findingIdSearch = $invalid;
        expect($ids())->toBe([]);
    }
});

it('ignores archived positions when reconciling active source occurrences', function (string $case) {
    $path = base_path('resources/views/archive-position.blade.php');
    File::put($path, "{{ __('ui.saved.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $archived = TranslationWorkbenchFinding::sole();
    $archived->update(['status' => 'obsolete']);
    if ($case !== 'new') {
        foreach ($case === 'ambiguous' ? [3, 5] : [3] as $line) {
            $copy = $archived->replicate();
            $copy->status = 'active';
            $copy->source_line = $line;
            $copy->source_signature = hash('sha256', 'active-position-'.$line);
            $copy->save();
        }
    }
    $before = TranslationWorkbenchFinding::orderBy('id')->get()->toJson();
    $plan = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation::class)->plan(['resources/views']);
    $row = collect($plan['rows'])->firstWhere('line', 1);
    expect($row['state'])->toBe($case)
        ->and($row['finding_ids'])->not->toContain($archived->id)
        ->and(TranslationWorkbenchFinding::orderBy('id')->get()->toJson())->toBe($before);
    $events = TranslationWorkbenchTimelineEvent::count();
    $archiveBefore = $archived->fresh()->getAttributes();
    if ($case === 'ambiguous') {
        expect(fn () => $syncer->sync($scanner->scan(['resources/views'])))->toThrow(RuntimeException::class, 'Ambiguous finding occurrence')
            ->and(TranslationWorkbenchFinding::orderBy('id')->get()->toJson())->toBe($before)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    } else {
        $result = $syncer->sync($scanner->scan(['resources/views']));
        expect($result['findings_created'])->toBe(1)
            ->and(TranslationWorkbenchFinding::where('status', 'active')->sole()->source_line)->toBe(1);
        $activeId = TranslationWorkbenchFinding::where('status', 'active')->sole()->id;
        expect($activeId)->not->toBe($archived->id)
            ->and($syncer->sync($scanner->scan(['resources/views']))['findings_created'])->toBe(0)
            ->and(TranslationWorkbenchFinding::where('status', 'active')->sole()->id)->toBe($activeId);
    }
    expect($archived->fresh()->getAttributes())->toBe($archiveBefore);
})->with(['new', 'moved', 'ambiguous']);

it('recognizes copied key occurrences after matching existing active positions', function () {
    $path = base_path('resources/views/copy-key.blade.php');
    File::put($path, "{{ __('ui.saved.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    app(TranslationWorkbenchFoundationSyncer::class)->sync($scanner->scan(['resources/views']));
    File::put($path, "{{ __('ui.saved.title') }}\n{{ __('ui.saved.title') }}");
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation::class)->plan(['resources/views']);
    expect($report['summary'])->toBe(['unchanged' => 1, 'new' => 1]);
});

it('retains archived signatures while enforcing one current occurrence and safe migration rollback', function () {
    $editor = workbenchValueEditor();
    $finding = TranslationWorkbenchFinding::sole();
    $finding->update(['status' => 'obsolete']);
    $active = $finding->replicate();
    $active->status = 'active';
    $active->save();
    $duplicate = $active->replicate();
    expect(fn () => $duplicate->save())->toThrow(\Illuminate\Database\QueryException::class);
    $migration = require dirname(__DIR__, 3).'/packages/gunreip/laravel-translation-workbench/database/migrations/2026_10_17_000001_scope_finding_signature_uniqueness_to_current.php';
    expect(fn () => $migration->down())->toThrow(RuntimeException::class, 'History must not be deleted')
        ->and(TranslationWorkbenchFinding::count())->toBe(2)
        ->and($finding->fresh()->status)->toBe('obsolete');
});

it('keeps an explicit finding assignment across scans without merging key records', function () {
    File::put(base_path('resources/views/choice.blade.php'), "{{ __('ui.shared.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $first->update(['review_status' => 'reviewed']);
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'explicit-choice-second');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class);
    $preview = $service->preview($finding->id);
    $keysBefore = TranslationWorkbenchKey::orderBy('id')->get()->toJson();
    $actor = Auth::user();
    Auth::guard()->forgetUser();
    expect(fn () => $service->choose($finding->id, $other->id, $preview['proof']))->toThrow(AuthorizationException::class);
    $this->actingAs($actor);
    expect(fn () => $service->choose($finding->id, 999999, $preview['proof']))->toThrow(ValidationException::class);
    expect(fn () => $service->choose($finding->id, $other->id, 'invalid-proof'))->toThrow(ValidationException::class);
    File::put(base_path('resources/views/choice.blade.php'), "\n{{ __('ui.shared.title') }}");
    expect(fn () => $service->choose($finding->id, $other->id, $preview['proof']))->toThrow(ValidationException::class);
    File::put(base_path('resources/views/choice.blade.php'), "{{ __('ui.shared.title') }}");
    expect(TranslationWorkbenchReview::count())->toBe(0);
    $service->choose($finding->id, $other->id, $preview['proof']);
    expect(TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$other->id]);
    expect(TranslationWorkbenchReview::where('decision', 'finding_assignment_selected')->count())->toBe(1);
    foreach ([1, 2] as $attempt) {
        $syncer->sync($scanner->scan(['resources/views']));
        expect(TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$other->id]);
    }
    expect(TranslationWorkbenchKey::orderBy('id')->get()->toJson())->toBe($keysBefore);
    expect(fn () => $service->choose($finding->id, $first->id, $preview['proof']))->toThrow(ValidationException::class);
    File::put(base_path('resources/views/choice.blade.php'), "\n\n{{ __('ui.shared.title') }}");
    $syncer->sync($scanner->scan(['resources/views']));
    expect($finding->fresh()->source_line)->toBe(1)->and($finding->fresh()->status)->toBe('obsolete');
    $successor = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($successor->source_line)->toBe(3)->and($service->savedDecision($successor->id)['active'])->toBeTrue();
    expect(TranslationWorkbenchKeyFinding::where('finding_id', $successor->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$other->id]);
    $other->update(['status' => 'obsolete']);
    expect(fn () => $syncer->sync($scanner->scan(['resources/views'])))->toThrow(InvalidArgumentException::class, 'explicit finding assignment');
});

it('scopes assignment evidence to the finding and rejects a choice after its review history changes', function () {
    File::put(base_path('resources/views/evidence.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'evidence-other-key');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    $review = static fn (int $keyId, ?int $findingId, array $meta, string $decision = 'translation_key_bulk_equalized') => TranslationWorkbenchReview::create([
        'key_id' => $keyId, 'finding_id' => $findingId, 'review_type' => 'translation_key', 'decision' => $decision,
        'new_values' => ['translation_key' => 'ui.shared.title'], 'meta' => $meta, 'reviewed_at' => now(), 'reviewed_by_user_id' => 1,
    ]);
    $direct = $review($first->id, $finding->id, [], 'translation_key_updated');
    $bulkA = $review($first->id, null, ['operation_id' => 'same-bulk', 'selected_finding_ids' => [$finding->id]]);
    $bulkB = $review($first->id, null, ['operation_id' => 'same-bulk', 'selected_finding_ids' => [$finding->id]]);
    $unrelated = $review($other->id, null, ['operation_id' => 'unrelated', 'selected_finding_ids' => [999999]]);
    $before = [];
    foreach (['keys', 'findings', 'key_findings', 'reviews', 'timeline_events'] as $table) {
        $before[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson();
    }
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class);
    $preview = $service->preview($finding->id);
    $evidence = $preview['keys'][0]['review_evidence'];
    expect($evidence)->toHaveCount(2)
        ->and($evidence[0]['review_ids'])->toBe([$direct->id])->and($evidence[0]['direct'])->toBeTrue()->and($evidence[0]['bulk'])->toBeFalse()
        ->and($evidence[1]['review_ids'])->toBe([$bulkA->id, $bulkB->id])->and($evidence[1]['direct'])->toBeFalse()->and($evidence[1]['bulk'])->toBeTrue()
        ->and($preview['keys'][1]['review_evidence'])->toBe([]);
    foreach ($before as $table => $rows) {
        expect(DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson())->toBe($rows);
    }
    $newReview = $review($other->id, $finding->id, [], 'translation_key_updated');
    expect(fn () => $service->choose($finding->id, $first->id, $preview['proof']))->toThrow(ValidationException::class);
    expect(TranslationWorkbenchKeyFinding::where('status', 'active')->count())->toBe(2);
    expect(TranslationWorkbenchReview::where('decision', 'finding_assignment_selected')->count())->toBe(0);
    expect($service->preview($finding->id)['keys'][1]['review_evidence'][0]['review_ids'])->toBe([$newReview->id]);
});

it('refreshes key sources and keeps the saved finding decision visible through the editor lifecycle', function () {
    File::put(base_path('resources/views/modal-choice.blade.php'), "{{ __('ui.shared.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $first->update(['review_status' => 'reviewed']);
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'modal-choice-other');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($finding->id);
    $editor->keyConflictInspectionOpen = true;
    $editor->updatedKeyConflictInspectionOpen(true);
    expect($editor->keyConflictInspection['assignment']['keys'])->toHaveCount(2)
        ->and($editor->keyConflictInspection['assignment_decision'])->toBeNull()
        ->and($editor->keyConflictInspection['groups'][0]['ownership_conflict'])->toBeTrue()
        ->and($editor->keyConflictInspection['groups'][0]['ambiguous_finding_ids'])->toBe([$finding->id]);
    expect(fn () => $editor->chooseFindingKeyAssignment())->toThrow(ValidationException::class);
    $editor->selectedAssignmentKeyId = (string) $other->id;
    $linksBeforeConfirmation = TranslationWorkbenchKeyFinding::orderBy('id')->get()->toJson();
    $editor->openKeyAssignmentConfirmation();
    expect($editor->keyAssignmentConfirmOpen)->toBeTrue()
        ->and(TranslationWorkbenchKeyFinding::orderBy('id')->get()->toJson())->toBe($linksBeforeConfirmation);
    $editor->keyAssignmentConfirmOpen = false;
    expect(fn () => $editor->confirmFindingKeyAssignment())->toThrow(ValidationException::class);
    $editor->openKeyAssignmentConfirmation();
    $editor->confirmFindingKeyAssignment();
    expect($editor->keyAssignmentConfirmOpen)->toBeFalse()->and($editor->pendingKeyAssignment)->toBeNull();
    expect($editor->selectedAssignmentKeyId)->toBe('');
    expect($editor->translationKeyModalOpen)->toBeTrue()
        ->and($editor->keyConflictInspectionOpen)->toBeTrue()
        ->and($editor->keyConflictInspection['assignment'])->toBeNull()
        ->and($editor->keyConflictInspection['assignment_decision']['active'])->toBeTrue()
        ->and($editor->keyConflictInspection['assignment_decision']['key_id'])->toBe($other->id);
    expect($editor->keyConflictInspection['groups'][0]['ownership_conflict'])->toBeTrue()
        ->and($editor->keyConflictInspection['groups'][0]['ambiguous_finding_ids'])->toBe([]);
    $reviewsBeforeRename = TranslationWorkbenchReview::count();
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyReviewWriter::class)->saveDecision(
        $other->fresh(), $finding->fresh(), (object) [], ['translation_key' => 'ui.separate.title'], 'manual', 'manual',
    ))->toThrow(ValidationException::class, 'Multiple active records share the source key');
    expect($other->fresh()->translation_key)->toBe('ui.shared.title')
        ->and(TranslationWorkbenchReview::count())->toBe($reviewsBeforeRename);
    $sourceOwners = collect($editor->translationKeySources)->keyBy('key_id');
    expect($sourceOwners[$first->id]['findings'])->toBe([])
        ->and($sourceOwners[$other->id]['findings'][0]['id'])->toBe($finding->id);
    $saved = $editor->keyConflictInspection['assignment_decision'];
    $editor->closeTranslationKeyModal();
    expect(fn () => $editor->chooseFindingKeyAssignment($first->id))->toThrow(ValidationException::class);
    $editor->openTranslationKeyModal($finding->id);
    $editor->updatedKeyConflictInspectionOpen(true);
    expect($editor->keyConflictInspection['assignment_decision'])->toBe($saved);
    $syncer->sync($scanner->scan(['resources/views']));
    $editor->inspectTranslationKeyConflict();
    expect($editor->keyConflictInspection['assignment_decision'])->toBe($saved);
    $other->update(['status' => 'obsolete']);
    $inspection = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConflictInspector::class)->inspect('ui.shared.title', 'ui.shared.title');
    expect($inspection['groups'][0]['ownership_conflict'])->toBeFalse()
        ->and($inspection['groups'][0]['ambiguous_finding_ids'])->toBe([]);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class);
    expect($service->savedDecision($finding->id)['active'])->toBeFalse();
    $actor = Auth::user();
    Auth::guard()->forgetUser();
    expect(fn () => $service->savedDecision($finding->id))->toThrow(AuthorizationException::class);
    $this->actingAs($actor);
});

it('archives only explicitly reassigned unused scalar keys and retains the decision across scans', function () {
    File::put(base_path('resources/views/retirement.blade.php'), "{{ __('ui.shared.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'retirement-other');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchUnusedKeyRetirement::class);
    expect($service->preview('ui.shared.title'))->toBe([]);
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($finding->id);
    $editor->inspectTranslationKeyConflict();
    $editor->chooseFindingKeyAssignment($other->id);
    $preview = $editor->keyConflictInspection['retirements'];
    expect($preview)->toHaveCount(1)->and($preview[0]['key_id'])->toBe($first->id)->and($preview[0]['target_id'])->toBe($other->id);
    $actor = Auth::user();
    Auth::guard()->forgetUser();
    expect(fn () => $service->retire($first->id, $preview[0]['proof']))->toThrow(AuthorizationException::class);
    $this->actingAs($actor);
    expect(fn () => $service->retire($first->id, 'tampered'))->toThrow(ValidationException::class);
    expect(fn () => $service->retire($other->id, $preview[0]['proof']))->toThrow(ValidationException::class);
    $other->update(['review_status' => 'reviewed']);
    expect(fn () => $service->retire($first->id, $preview[0]['proof']))->toThrow(ValidationException::class);
    $editor->inspectTranslationKeyConflict();
    $before = [];
    foreach (['findings', 'key_findings', 'lang_values', 'key_values', 'dynamic_key_values'] as $table) {
        $before[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson();
    }
    $reviews = TranslationWorkbenchReview::orderBy('id')->get()->map->getAttributes()->all();
    $editor->retireUnassignedKey($first->id);
    expect($first->fresh()->status)->toBe('obsolete')
        ->and($first->fresh()->meta['reconciled_canonical_key_id'])->toBe($other->id)
        ->and($editor->keyConflictInspection['retirements'])->toBe([])
        ->and($editor->keyConflictInspection['groups'][0]['ownership_conflict'])->toBeFalse();
    foreach ($before as $table => $rows) {
        expect(DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson())->toBe($rows);
    }
    expect(TranslationWorkbenchReview::whereIn('id', array_column($reviews, 'id'))->orderBy('id')->get()->map->getAttributes()->all())->toBe($reviews)
        ->and(TranslationWorkbenchReview::where('decision', 'unassigned_key_retired')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'unassigned_key_retired')->count())->toBe(1);
    expect(fn () => $service->retire($first->id, $preview[0]['proof']))->toThrow(ValidationException::class);
    foreach ([1, 2] as $attempt) {
        $syncer->sync($scanner->scan(['resources/views']));
        expect($first->fresh()->status)->toBe('obsolete')
            ->and(TranslationWorkbenchKeyFinding::where('status', 'active')->pluck('key_id')->all())->toBe([$other->id]);
    }
});

it('refuses unused key retirement when its evidence or ownership changes', function (string $change) {
    File::put(base_path('resources/views/retirement-guard.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'retirement-guard-other');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    $assignment = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class);
    $assignment->choose($finding->id, $other->id, $assignment->preview($finding->id)['proof']);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchUnusedKeyRetirement::class);
    $proof = $service->preview('ui.shared.title')[0]['proof'];
    match ($change) {
        'own value' => DB::table('translation_workbench_key_values')->insert(['key_id' => $first->id, 'locale' => 'de', 'value' => 'Eigener Wert']),
        'dynamic' => $first->update(['is_dynamic_key' => true]),
        'active link' => TranslationWorkbenchKeyFinding::where('key_id', $first->id)->update(['status' => 'active']),
        'missing decision' => $finding->update(['meta' => []]),
        'target obsolete' => $other->update(['status' => 'obsolete']),
    };
    $before = TranslationWorkbenchKey::orderBy('id')->get()->toJson();
    expect($service->preview('ui.shared.title'))->toBe([]);
    expect(fn () => $service->retire($first->id, $proof))->toThrow(ValidationException::class);
    expect(TranslationWorkbenchKey::orderBy('id')->get()->toJson())->toBe($before)
        ->and(TranslationWorkbenchReview::where('decision', 'unassigned_key_retired')->count())->toBe(0);
})->with(['own value', 'dynamic', 'active link', 'missing decision', 'target obsolete']);

it('previews separation of one task without renaming shared values or changing assignments', function () {
    File::put(base_path('resources/views/separation.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'separation-other');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    $inspector = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConflictInspector::class);
    $before = [];
    foreach (['keys', 'findings', 'key_findings', 'reviews', 'lang_values', 'timeline_events'] as $table) {
        $before[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson();
    }
    expect($inspector->separation($first->id, $finding->id, 'ui.shared.title'))->toBeNull();
    $preview = $inspector->separation($first->id, $finding->id, 'ui.separate.title');
    expect($preview['key_id'])->toBe($first->id)
        ->and($preview['remaining_owner_ids'])->toBe([$other->id])
        ->and(array_column($preview['findings'], 'id'))->toBe([$finding->id])
        ->and($preview['issues'])->toContain('Resolve multiple assignments of the affected findings first.');
    expect($inspector->separation($first->id, $finding->id, 'ui.type')['issues'])
        ->toContain('Enter a nested scalar translation key, for example ui.buttons.save.');
    $actor = Auth::user();
    Auth::guard()->forgetUser();
    expect(fn () => $inspector->separation($first->id, $finding->id, 'ui.separate.title'))->toThrow(AuthorizationException::class);
    $this->actingAs($actor);
    foreach ($before as $table => $rows) {
        expect(DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson())->toBe($rows);
    }
    $assignment = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class);
    $assignment->choose($finding->id, $first->id, $assignment->preview($finding->id)['proof']);
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($finding->id);
    $editor->translationKeyValue = 'ui.separate.title';
    $editor->inspectTranslationKeyConflict();
    expect($editor->keyConflictInspection['separation']['issues'])->toBe([])
        ->and($editor->keyConflictInspection['separation']['remaining_owner_ids'])->toBe([$other->id]);
    $occupied = $first->replicate();
    $occupied->fingerprint = hash('sha256', 'separation-occupied');
    $occupied->translation_key = 'ui.separate.title';
    $occupied->save();
    expect($inspector->separation($first->id, $finding->id, 'ui.separate.title')['issues'])
        ->toContain('The target already has key records or stored values. Their conflicts need a separate decision.');
    $other->update(['status' => 'obsolete']);
    expect($inspector->separation($first->id, $finding->id, 'ui.separate.title'))->toBeNull();
});

it('does not let an old replace-all review overwrite a different task hidden by code-update filters', function (string $scope, string $layout) {
    $path = 'resources/views/separate-calls.blade.php';
    $source = $layout === 'same line' ? "ä {{ __('Same source text') }} · {{ __('Same source text') }}\n" : "{{ __('Same source text') }}\n{{ __('Same source text') }}\n";
    File::put(base_path($path), $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    expect($findings)->toHaveCount(2);
    $first = TranslationWorkbenchKey::firstOrFail();
    $first->update(['translation_key' => 'ui.tasks.first', 'review_status' => 'reviewed']);
    $second = $first->replicate();
    $second->fingerprint = hash('sha256', 'separate-source-second');
    $second->translation_key = 'ui.tasks.second';
    $second->save();
    TranslationWorkbenchKeyFinding::where('finding_id', $findings[1]->id)->update(['key_id' => $second->id]);
    foreach ([$first, $second] as $key) {
        foreach (['en', 'de'] as $locale) {
            TranslationWorkbenchLangValue::create(['locale' => $locale, 'namespace' => 'ui', 'lang_key' => substr($key->translation_key, 3),
                'translation_key' => $key->translation_key, 'value' => 'Example', 'status' => 'active', 'source_path' => 'lang/'.$locale.'/ui.php']);
        }
    }
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/ui.php'), "<?php return ['tasks' => ['first' => 'Example', 'second' => 'Example']];");
    app(TranslationWorkbenchConflictReviewWriter::class)->save($findings[0], $first, 'duplicate_confirmed_same_key', 'Historical replace-all decision');
    $keys = $scope === 'key' ? [$first->translation_key] : [];
    $limit = $scope === 'limit' ? 1 : null;
    if ($layout === 'shifted') {
        $source = "\n".$source;
        File::put(base_path($path), $source);
    } elseif ($layout === 'missing column') {
        $findings[0]->update(['source_column' => null]);
    } elseif ($layout === 'extra call') {
        $source .= "{{ __('Same source text') }}\n";
        File::put(base_path($path), $source);
    }
    if ($layout === 'manual review') {
        app(TranslationWorkbenchConflictReviewWriter::class)->save($findings[0], $first, 'ignore_for_now', 'Needs review');
    }
    $valid = in_array($layout, ['lines', 'same line'], true);
    $count = $scope === 'all' ? 2 : 1;
    $expected = $source;
    if ($valid) {
        $expected = substr_replace($source, "__('ui.tasks.first')", strpos($source, "__('Same source text')"), strlen("__('Same source text')"));
        if ($scope === 'all') {
            $expected = str_replace("__('Same source text')", "__('ui.tasks.second')", $expected);
        }
    }
    $diff = null;
    foreach ([false, true] as $write) {
        $result = app(TranslationWorkbenchCodeUpdateApplier::class)->apply([$path], $limit, $write, $keys);
        expect($result['summary']['applied'])->toBe($write && $valid ? $count : 0)
            ->and($result['summary']['would_apply'])->toBe(! $write && $valid ? $count : 0)
            ->and($result['summary']['duplicate_expression'])->toBe($valid ? 0 : $count)
            ->and(File::get(base_path($path)))->toBe($write ? $expected : $source);
        if (! $write) { $diff = $result['diff']; }
        else { expect($result['diff'])->toBe($diff); }
    }
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe($valid ? $count : 0);
    if ($valid) {
        $ids = $findings->pluck('id')->all();
        $suggestions = $findings->pluck('suggested_key', 'id')->all();
        $beforeTransition = TranslationWorkbenchFinding::orderBy('id')->get()->toJson();
        File::append(base_path($path), "\n{{-- changed after code update --}}");
        expect(fn () => app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views'])))
            ->toThrow(RuntimeException::class, 'recorded code update');
        expect(TranslationWorkbenchFinding::orderBy('id')->get()->toJson())->toBe($beforeTransition);
        File::put(base_path($path), $expected);
        $first->update(['translation_key' => 'ui.changed.assignment']);
        expect(fn () => app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views'])))
            ->toThrow(RuntimeException::class, 'recorded code update');
        expect(TranslationWorkbenchFinding::orderBy('id')->get()->toJson())->toBe($beforeTransition);
        $first->update(['translation_key' => 'ui.tasks.first']);

        foreach ([1, 2] as $attempt) {
            $summary = app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
            $shiftedSibling = $layout === 'same line' && $scope !== 'all';
            $currentSecond = $shiftedSibling ? TranslationWorkbenchFinding::findOrFail($findings[1]->fresh()->meta['successor_finding_id']) : $findings[1];
            expect($summary['findings_created'])->toBe($shiftedSibling && $attempt === 1 ? 1 : 0)
                ->and(TranslationWorkbenchFinding::where('status', 'active')->orderBy('id')->pluck('id')->all())->toBe([$findings[0]->id, $currentSecond->id])
                ->and($currentSecond->suggested_key)->toBe($suggestions[$findings[1]->id])
                ->and(TranslationWorkbenchKeyFinding::where('finding_id', $findings[0]->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$first->id])
                ->and(TranslationWorkbenchKeyFinding::where('finding_id', $currentSecond->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$second->id]);
        }
    }

})->with(['all', 'key', 'limit'])->with(['lines', 'same line', 'shifted', 'missing column', 'extra call', 'manual review']);

it('separates a task without copying shared values and preserves its findings through pending and applied code updates', function () {
    $path = 'resources/views/task-separation.blade.php';
    $source = "{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}\n";
    File::put(base_path($path), $source);
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    $chosen = TranslationWorkbenchKey::sole();
    $chosen->update(['review_status' => 'reviewed']);
    $remaining = $chosen->replicate();
    $remaining->fingerprint = hash('sha256', 'remaining-separation-task');
    $remaining->save();
    TranslationWorkbenchKeyFinding::where('finding_id', $findings[2]->id)->update(['key_id' => $remaining->id]);
    foreach (['en', 'de'] as $locale) {
        TranslationWorkbenchLangValue::create(['locale' => $locale, 'namespace' => 'ui', 'lang_key' => 'shared.title',
            'translation_key' => 'ui.shared.title', 'value' => 'Shared title', 'status' => 'active', 'source_path' => 'lang/'.$locale.'/ui.php']);
    }
    installPublishedValueFixture('ui.shared.title', ['en', 'de']);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeySeparation::class);
    $plan = $service->preview($chosen->id, $findings[0]->id, 'ui.independent.title');
    expect($plan['issues'])->toBe([])->and($plan['proof'])->toBeString();
    $actor = Auth::user();
    Auth::guard()->forgetUser();
    expect(fn () => $service->separate($chosen->id, $findings[0]->id, $plan['target'], $plan['proof']))->toThrow(AuthorizationException::class);
    $this->actingAs($actor);
    expect(fn () => $service->separate($chosen->id, $findings[0]->id, $plan['target'], 'invalid'))->toThrow(ValidationException::class);
    File::append(base_path($path), "\n");
    expect(fn () => $service->separate($chosen->id, $findings[0]->id, $plan['target'], $plan['proof']))->toThrow(ValidationException::class);
    File::put(base_path($path), $source);
    $beforeValues = TranslationWorkbenchLangValue::orderBy('id')->get()->toJson();
    $beforeLinks = TranslationWorkbenchKeyFinding::orderBy('id')->get()->toJson();
    $remainingBefore = $remaining->fresh()->getAttributes();
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($findings[0]->id);
    $editor->translationKeyValue = 'ui.independent.title';
    $editor->inspectTranslationKeyConflict();
    $editor->separateTranslationTask();
    expect($editor->keyConflictInspectionOpen)->toBeTrue()
        ->and($editor->keyConflictInspection['pending_separation']['target_key'])->toBe('ui.independent.title');
    expect($chosen->fresh()->translation_key)->toBe('ui.independent.title')
        ->and($remaining->fresh()->getAttributes())->toBe($remainingBefore)
        ->and(TranslationWorkbenchLangValue::orderBy('id')->get()->toJson())->toBe($beforeValues)
        ->and(TranslationWorkbenchKeyFinding::orderBy('id')->get()->toJson())->toBe($beforeLinks)
        ->and(File::get(base_path($path)))->toBe($source)
        ->and(TranslationWorkbenchReview::where('decision', 'translation_task_separated')->count())->toBe(1)
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'translation_task_separated')->count())->toBe(1);
    expect(fn () => $service->separate($chosen->id, $findings[0]->id, $plan['target'], $plan['proof']))->toThrow(ValidationException::class);
    foreach ([1, 2] as $attempt) {
        $summary = $syncer->sync($scanner->scan(['resources/views']));
        expect($summary['findings_created'])->toBe(0)
            ->and(TranslationWorkbenchKeyFinding::where('finding_id', $findings[0]->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$chosen->id]);
    }
    $scalarEditor = app(TranslationWorkbenchScalarEditor::class);
    $scalarEditor->save($chosen->id, $findings[0]->id, [
        ['locale' => 'en', 'value' => 'Independent title'],
        ['locale' => 'de', 'value' => 'Eigenständiger Titel'],
    ], $scalarEditor->fingerprint($chosen->id, $findings[0]->id));
    $blockedUpdate = app(TranslationWorkbenchCodeUpdateApplier::class)->apply([$path], write: true, keys: ['ui.independent.title']);
    expect($blockedUpdate['summary']['applied'])->toBe(0)
        ->and(File::get(base_path($path)))->toBe($source);
    $publication = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $unapproved = $publication->preview('scalar', $chosen->id, $findings[0]->id, ['en', 'de']);
    expect($unapproved['changes_without_current_approval'])->toBe(2);
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class)
        ->publish('scalar', $chosen->id, $findings[0]->id, ['en', 'de'], $unapproved['fingerprint']))
        ->toThrow(ValidationException::class);
    expect(File::get(base_path($path)))->toBe($source);
    foreach (['en', 'de'] as $locale) {
        expect(require lang_path($locale.'/ui.php'))->toBe(['shared' => ['title' => 'Shared title']]);
    }
    $published = publishReviewedFixture($chosen->id, $findings[0]->id, ['en', 'de']);
    expect($published['files_written'])->toBe(2)
        ->and(TranslationWorkbenchLangValue::where('translation_key', 'ui.shared.title')->orderBy('id')->get()->toJson())->toBe($beforeValues)
        ->and(File::get(base_path($path)))->toBe($source);
    foreach (['en' => 'Independent title', 'de' => 'Eigenständiger Titel'] as $locale => $value) {
        $output = require lang_path($locale.'/ui.php');
        expect($output['shared']['title'])->toBe('Shared title')
            ->and($output['independent']['title'])->toBe($value);
    }
    $applied = app(TranslationWorkbenchCodeUpdateApplier::class)->apply([$path], write: true, keys: ['ui.independent.title']);
    expect($applied['summary']['applied'])->toBe(2)
        ->and(File::get(base_path($path)))->toBe("{{ __('ui.independent.title') }}\n{{ __('ui.independent.title') }}\n{{ __('ui.shared.title') }}\n");
    foreach ([1, 2] as $attempt) {
        $summary = $syncer->sync($scanner->scan(['resources/views']));
        expect($summary['findings_created'])->toBe(0)
            ->and(TranslationWorkbenchFinding::orderBy('id')->pluck('id')->all())->toBe($findings->pluck('id')->all())
            ->and($findings[0]->fresh()->meta)->not->toHaveKey('pending_key_separation')
            ->and(TranslationWorkbenchKeyFinding::where('finding_id', $findings[2]->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$remaining->id]);
    }
});

it('rejects changed or occupied task separations without partial writes', function (string $change) {
    File::ensureDirectoryExists(lang_path('en'));
    $path = 'resources/views/separation-guards.blade.php';
    File::put(base_path($path), "{{ __('ui.shared.title') }}\n");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $other = $key->replicate();
    $other->fingerprint = hash('sha256', 'separation-guard-other');
    $other->save();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeySeparation::class);
    $plan = $service->preview($key->id, $finding->id, 'ui.separate.title');
    expect($plan['proof'])->toBeString();
    match ($change) {
        'own value' => DB::table('translation_workbench_key_values')->insert(['key_id' => $key->id, 'locale' => 'en', 'value' => 'Own value']),
        'occupied target' => $other->update(['translation_key' => 'ui.separate.title']),
        'inactive finding' => $finding->update(['status' => 'obsolete']),
        'extra assignment' => TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']),
        'source changed' => File::put(base_path($path), "\n{{ __('ui.shared.title') }}\n"),
        'published target' => File::put(lang_path('en/ui.php'), "<?php return ['separate' => ['title' => 'Published']];"),
        'new review' => TranslationWorkbenchReview::create(['key_id' => $key->id, 'finding_id' => $finding->id, 'review_type' => 'translation_key', 'decision' => 'translation_key_updated', 'reviewed_at' => now()]),
    };
    $before = [];
    foreach (['keys', 'findings', 'key_findings', 'reviews', 'lang_values', 'timeline_events'] as $table) {
        $before[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson();
    }
    expect(fn () => $service->separate($key->id, $finding->id, 'ui.separate.title', $plan['proof']))->toThrow(ValidationException::class);
    foreach ($before as $table => $rows) {
        expect(DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson())->toBe($rows);
    }
})->with(['own value', 'occupied target', 'inactive finding', 'extra assignment', 'source changed', 'published target', 'new review']);


it('requires a published source-language string before replacing a literal', function (string $published) {
    $path = 'resources/views/publication-guard.blade.php';
    $source = "{{ __('Keep this literal') }}";
    File::put(base_path($path), $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $key = TranslationWorkbenchKey::sole();
    $key->update(['translation_key' => 'ui.guard.title', 'review_status' => 'reviewed']);
    foreach (['en', 'de'] as $locale) {
        TranslationWorkbenchLangValue::create(['locale' => $locale, 'namespace' => 'ui', 'lang_key' => 'guard.title',
            'translation_key' => 'ui.guard.title', 'value' => 'New database draft', 'status' => 'active', 'source_path' => 'lang/'.$locale.'/ui.php']);
    }
    File::ensureDirectoryExists(lang_path('en'));
    $contents = match ($published) {
        'missing' => null,
        'empty' => "<?php return ['guard' => ['title' => ' ']];",
        'container' => "<?php return ['guard' => ['title' => ['child' => 'Text']]];",
        'number' => "<?php return ['guard' => ['title' => 123]];",
        'broken' => "<?php return [;",
        'other key' => "<?php return ['guard' => ['other' => 'Text']];",
        'source only' => "<?php return ['guard' => ['title' => 'Previously published text']];",
    };
    if ($contents !== null) {
        File::put(lang_path('en/ui.php'), $contents);
    }
    $allowed = $published === 'source only';
    $plan = app(TranslationWorkbenchCodeUpdatePlanner::class)->plan();
    expect($plan['updates'][0]['state'])->toBe($allowed ? 'safe_update' : 'missing_lang_values');
    if (! $allowed) {
        expect($plan['updates'][0]['reason'])->toBe('source_lang_value_not_published');
    }
    $result = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);
    expect($result['summary']['applied'])->toBe($allowed ? 1 : 0)
        ->and(File::get(base_path($path)))->toBe($allowed ? "{{ __('ui.guard.title') }}" : $source)
        ->and(File::exists(lang_path('de/ui.php')))->toBeFalse();
})->with(['missing', 'empty', 'container', 'number', 'broken', 'other key', 'source only']);


it('keeps published key calls current while later source-language drafts are edited', function (string $change) {
    $path = 'resources/views/published-draft.blade.php';
    $source = "{{ __('ui.fixture.published') }}";
    File::put(base_path($path), $source);
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['review_status' => 'reviewed']);
    $editor = app(TranslationWorkbenchScalarEditor::class);
    $editor->save($key->id, $finding->id, [['locale' => 'en', 'value' => 'Published source text']]);
    publishReviewedFixture($key->id, $finding->id, ['en']);
    $publication = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $oldPreview = $publication->preview('scalar', $key->id, $finding->id, ['en']);
    $oldApprovals = TranslationWorkbenchReview::where('decision', 'translation_value_approved')->orderBy('id')->get();
    $oldApprovalSnapshot = $oldApprovals->toJson();
    $published = File::get(lang_path('en/ui.php'));
    $editor->save($key->id, $finding->id, [['locale' => 'en', 'value' => $change === 'cleared' ? null : 'Unpublished revision']]);
    foreach ([1, 2] as $attempt) {
        $syncer->sync($scanner->scan(['resources/views']));
        $plan = app(TranslationWorkbenchCodeUpdatePlanner::class)->plan();
        expect($plan['summary']['already_current'])->toBe(1)
            ->and($plan['summary']['safe_updates'])->toBe(0)
            ->and($plan['summary']['missing_lang_values'])->toBe(0)
            ->and(TranslationWorkbenchFinding::where('status', 'active')->pluck('id')->all())->toBe([$finding->id]);
        $applied = app(TranslationWorkbenchCodeUpdateApplier::class)->apply(write: true);
        expect($applied['summary']['applied'])->toBe(0)
            ->and(File::get(base_path($path)))->toBe($source)
            ->and(File::get(lang_path('en/ui.php')))->toBe($published);
    }
    $translator = new \Illuminate\Translation\Translator(
        new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, lang_path()), 'de');
    $translator->setFallback('en');
    expect($translator->get($key->translation_key))->toBe('Published source text')
        ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(0);

    if ($change === 'changed') {
        $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
        $rows = $approval->inspect('scalar', $key->id, $finding->id, ['en']);
        expect($rows)->toHaveCount(1)
            ->and($rows[0]['state'])->toBe('changed');
        expect(fn () => $runner->publish('scalar', $key->id, $finding->id, ['en'], $oldPreview['fingerprint']))
            ->toThrow(ValidationException::class);
        $newPreview = $publication->preview('scalar', $key->id, $finding->id, ['en']);
        expect($newPreview['changes_without_current_approval'])->toBe(1);
        expect(fn () => $runner->publish('scalar', $key->id, $finding->id, ['en'], $newPreview['fingerprint']))
            ->toThrow(ValidationException::class);
        expect(File::get(lang_path('en/ui.php')))->toBe($published);
        $result = publishReviewedFixture($key->id, $finding->id, ['en']);
        expect($result['files_written'])->toBe(1)
            ->and(TranslationWorkbenchReview::whereIn('id', $oldApprovals->modelKeys())->orderBy('id')->get()->toJson())->toBe($oldApprovalSnapshot)
            ->and(TranslationWorkbenchReview::where('decision', 'translation_value_approved')->count())->toBe(2)
            ->and(TranslationWorkbenchReview::where('decision', 'lang_files_published')->count())->toBe(2);
        $translator = new \Illuminate\Translation\Translator(
            new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, lang_path()), 'de');
        $translator->setFallback('en');
        expect($translator->get($key->translation_key))->toBe('Unpublished revision');
        $syncer->sync($scanner->scan(['resources/views']));
        expect(TranslationWorkbenchFinding::where('status', 'active')->pluck('id')->all())->toBe([$finding->id])
            ->and(TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$key->id])
            ->and(File::get(base_path($path)))->toBe($source)
            ->and(app(TranslationWorkbenchCodeUpdatePlanner::class)->plan()['summary']['already_current'])->toBe(1)
            ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'code_update_applied')->count())->toBe(0);
    }
})->with(['changed', 'cleared']);


it('does not remove a published translation when its stored value is cleared', function (string $locale) {
    $path = 'resources/views/cleared-published.blade.php';
    $source = "{{ __('ui.fixture.retained') }}";
    File::put(base_path($path), $source);
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['review_status' => 'reviewed']);
    $editor = app(TranslationWorkbenchScalarEditor::class);
    $editor->save($key->id, $finding->id, [
        ['locale' => 'en', 'value' => 'Published English'],
        ['locale' => 'de', 'value' => 'Veröffentlichtes Deutsch'],
    ]);
    publishReviewedFixture($key->id, $finding->id, ['en', 'de']);
    $files = ['en' => File::get(lang_path('en/ui.php')), 'de' => File::get(lang_path('de/ui.php'))];
    $editor->save($key->id, $finding->id, [['locale' => $locale, 'value' => null]]);
    $events = TranslationWorkbenchTimelineEvent::count();
    $reviews = TranslationWorkbenchReview::count();
    $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)
        ->preview('scalar', $key->id, $finding->id, ['en', 'de']);
    expect($report['pruning_requires_separate_review'])->toBe(1);
    expect(fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class)
        ->publish('scalar', $key->id, $finding->id, ['en', 'de'], $report['fingerprint']))
        ->toThrow(ValidationException::class, 'Publication blocked: this preview would remove published translations.');
    expect(File::get(lang_path('en/ui.php')))->toBe($files['en'])
        ->and(File::get(lang_path('de/ui.php')))->toBe($files['de'])
        ->and(File::get(base_path($path)))->toBe($source)
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
        ->and(TranslationWorkbenchReview::count())->toBe($reviews);
    $translator = new \Illuminate\Translation\Translator(
        new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, lang_path()), 'de');
    $translator->setFallback('en');
    expect($translator->get($key->translation_key))->toBe('Veröffentlichtes Deutsch');
})->with(['en', 'de']);


it('keeps repeated scalar saves and approvals stable and prevents empty publication runs', function () {
    $path = 'resources/views/repeated-actions.blade.php';
    File::put(base_path($path), "{{ __('ui.fixture.repeated') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['review_status' => 'reviewed']);
    $editor = app(TranslationWorkbenchScalarEditor::class);
    $values = [['locale' => 'en', 'value' => 'Stable text']];
    $editor->save($key->id, $finding->id, $values);
    publishReviewedFixture($key->id, $finding->id, ['en']);
    $stored = TranslationWorkbenchLangValue::sole();
    $storedBefore = $stored->getAttributes();
    $reviews = TranslationWorkbenchReview::count();
    $events = TranslationWorkbenchTimelineEvent::count();
    $contents = File::get(lang_path('en/ui.php'));
    $runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
    $runs = $runner->runs('scalar', $key->id, $finding->id);
    foreach ([1, 2] as $attempt) {
        expect($editor->save($key->id, $finding->id, $values, $editor->fingerprint($key->id, $finding->id)))->toBe([]);
        $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
        $preview = $approval->preview('scalar', $stored->id, $key->id, $finding->id);
        $approval->approve('scalar', $stored->id, $key->id, $finding->id, $preview['fingerprint']);
        $report = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)
            ->preview('scalar', $key->id, $finding->id, ['en']);
        expect(fn () => $runner->publish('scalar', $key->id, $finding->id, ['en'], $report['fingerprint']))
            ->toThrow(ValidationException::class, 'No new or changed translations to publish.');
        expect($stored->fresh()->getAttributes())->toBe($storedBefore)
            ->and(TranslationWorkbenchReview::count())->toBe($reviews)
            ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events)
            ->and(File::get(lang_path('en/ui.php')))->toBe($contents)
            ->and($runner->runs('scalar', $key->id, $finding->id))->toBe($runs);
    }
});


it('invalidates a verified publication preview when a second dialog rejects a stale approval', function () {
    $first = workbenchValueEditor();
    $first->saveTranslationValue();
    $second = new TranslationWorkbenchEntries;
    $second->openEditModal($first->editFindingId);
    $second->targetTranslationValue = 'Unsaved text in the second dialog';
    $second->openValueApproval('scalar');
    foreach (array_keys($second->valueApprovalRows) as $index) {
        $second->approveStoredValue($index);
    }
    $second->previewValuePublication();
    $second->verifyValuePublication();
    expect($second->valuePublicationVerified)->toBeTrue();
    $target = collect($second->valueApprovalRows)->search(fn ($row) => $row['snapshot']['locale'] === 'de');
    $first->targetTranslationValue = 'Newer translation in the database';
    $first->saveTranslationValue();
    $events = TranslationWorkbenchTimelineEvent::count();
    expect(fn () => $second->approveStoredValue($target))->toThrow(ValidationException::class)
        ->and($second->valuePublicationVerified)->toBeFalse()
        ->and($second->valuePublicationPreview)->toBeNull()
        ->and($second->targetTranslationValue)->toBe('Unsaved text in the second dialog')
        ->and($second->editModalOpen)->toBeTrue()
        ->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    expect(fn () => $second->publishValuePublication())->toThrow(ValidationException::class)
        ->and(File::allFiles(lang_path()))->toHaveCount(0);
    $second->openValueApproval('scalar');
    expect($second->valueApprovalRows[$target]['snapshot']['value'])->toBe('Newer translation in the database')
        ->and($second->valueApprovalRows[$target]['state'])->toBe('changed')
        ->and($second->targetTranslationValue)->toBe('Unsaved text in the second dialog');
    $second->approveStoredValue($target);
    $second->previewValuePublication();
    $second->publishValuePublication();
    expect($second->getErrorBag()->has('valueApproval'))->toBeFalse()
        ->and(Arr::get(require lang_path('de/ui.php'), 'fixture.welcome'))->toBe('Newer translation in the database')
        ->and($second->targetTranslationValue)->toBe('Unsaved text in the second dialog');
});

it('keeps repeated key occurrences in separate files stable without artificial finding changes', function () {
    foreach (range(1, 5) as $index) {
        File::put(base_path('resources/views/repeated-key-'.$index.'.blade.php'), "{{ __('ui.fixture.shared') }}\n");
    }
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $identities = TranslationWorkbenchFinding::orderBy('id')->get(['id', 'source_file_id', 'source_signature', 'suggested_key'])->toJson();
    expect(TranslationWorkbenchFinding::count())->toBe(5);
    $events = TranslationWorkbenchTimelineEvent::where('event_type', 'finding_changed')->count();
    foreach ([1, 2] as $attempt) {
        $summary = $syncer->sync($scanner->scan(['resources/views']));
        expect($summary['findings_created'])->toBe(0)
            ->and(TranslationWorkbenchFinding::orderBy('id')->get(['id', 'source_file_id', 'source_signature', 'suggested_key'])->toJson())->toBe($identities)
            ->and(TranslationWorkbenchTimelineEvent::where('event_type', 'finding_changed')->count())->toBe($events);
    }
});

it('distinguishes original finding literals from stored source translation context', function (string $scenario) {
    config(['translation-workbench.source_locale' => 'en']);
    File::put(base_path('resources/views/context-choice.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $finding->update(['literal_text' => $scenario === 'literal' ? 'Original text' : null]);
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'context-choice-other');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['finding_id' => $finding->id, 'key_id' => $other->id, 'status' => 'active', 'relation_type' => 'candidate']);
    TranslationWorkbenchLangValue::create(['locale' => 'de', 'namespace' => 'ui', 'lang_key' => 'shared.title', 'translation_key' => 'ui.shared.title', 'value' => 'German target', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/de/ui.php']);
    if ($scenario !== 'missing') {
        TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'shared.title', 'translation_key' => 'ui.shared.title', 'value' => 'Source text', 'value_type' => 'string', 'status' => $scenario === 'obsolete' ? 'obsolete' : 'active', 'source_path' => 'lang/en/ui.php']);
    }
    $before = $finding->fresh()->getAttributes();
    $preview = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class)->preview($finding->id);
    expect($preview['display_text'])->toBe(match ($scenario) { 'literal' => 'Original text', 'source' => 'Source text', default => '' })
        ->and($preview['text_source'])->toBe(match ($scenario) { 'literal' => 'literal', 'source' => 'source_translation', default => 'missing' })
        ->and($preview['source_locale'])->toBe('en')
        ->and($finding->fresh()->getAttributes())->toBe($before);
})->with(['literal', 'source', 'missing', 'obsolete']);

it('opens a related finding from the assignment inspection without losing an unsaved key', function () {
    File::put(base_path('resources/views/related-one.blade.php'), "{{ __('ui.related.title') }}");
    File::put(base_path('resources/views/related-two.blade.php'), "{{ __('ui.related.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($findings[0]->id);
    $editor->updatedKeyConflictInspectionOpen(true);
    $before = TranslationWorkbenchKeyFinding::orderBy('id')->get()->toJson();
    $editor->translationKeyValue = 'ui.unsaved.title';
    expect(fn () => $editor->openRelatedKeyFinding($findings[1]->id))->toThrow(ValidationException::class);
    expect($editor->translationKeyFindingId)->toBe($findings[0]->id)->and($editor->translationKeyValue)->toBe('ui.unsaved.title');
    $editor->translationKeyValue = $editor->translationKeySegmentBaseValue;
    expect(fn () => $editor->openRelatedKeyFinding(999999))->toThrow(ValidationException::class);
    $editor->openRelatedKeyFinding($findings[1]->id);
    expect($editor->translationKeyFindingId)->toBe($findings[1]->id)
        ->and($editor->keyConflictInspectionOpen)->toBeTrue()
        ->and($editor->keyConflictInspection)->not->toBeNull()
        ->and(TranslationWorkbenchKeyFinding::orderBy('id')->get()->toJson())->toBe($before);
});

it('consolidates reviewed scalar ownership explicitly and preserves the decision across rescans', function () {
    File::put(base_path('resources/views/ownership.blade.php'), "{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $first = TranslationWorkbenchKey::first();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'consolidation-other');
    $other->save();
    $findings = TranslationWorkbenchFinding::orderBy('id')->get();
    TranslationWorkbenchKeyFinding::where('finding_id', $findings[1]->id)->update(['key_id' => $other->id]);
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConsolidation::class);
    $plan = $service->preview('ui.shared.title');
    expect($plan['issues'])->toBe([])->and($plan['same_literals'])->toBeFalse();
    $before = TranslationWorkbenchFinding::orderBy('id')->get()->map->only(['id', 'literal_text', 'suggested_key', 'status'])->all();
    $actor = Auth::user();
    Auth::guard()->forgetUser();
    expect(fn () => $service->merge($first->id, $plan['proof']))->toThrow(AuthorizationException::class);
    $this->actingAs($actor);
    expect(fn () => $service->merge(999999, $plan['proof']))->toThrow(ValidationException::class);
    File::append(base_path('resources/views/ownership.blade.php'), "\n");
    expect(fn () => $service->merge($first->id, $plan['proof']))->toThrow(ValidationException::class);
    File::put(base_path('resources/views/ownership.blade.php'), "{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}");
    expect(TranslationWorkbenchReview::count())->toBe(0);
    $service->merge($first->id, $plan['proof']);
    expect($other->fresh()->status)->toBe('obsolete');
    expect($other->fresh()->meta['reconciled_canonical_key_id'])->toBe($first->id);
    foreach ($findings as $finding) {
        expect(TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$first->id]);
        expect(app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingAssignment::class)->savedDecision($finding->id)['active'])->toBeTrue();
    }
    expect(TranslationWorkbenchFinding::orderBy('id')->get()->map->only(['id', 'literal_text', 'suggested_key', 'status'])->all())->toBe($before);
    expect(fn () => $service->merge($first->id, $plan['proof']))->toThrow(ValidationException::class);
    $reviewCount = TranslationWorkbenchReview::count();
    $syncer->sync($scanner->scan(['resources/views']));
    $syncer->sync($scanner->scan(['resources/views']));
    expect(TranslationWorkbenchReview::count())->toBe($reviewCount);
    expect(TranslationWorkbenchKey::where('status', '!=', 'obsolete')->pluck('id')->all())->toBe([$first->id]);
});

it('blocks consolidation after a review changes and distinguishes missing from identical original literals', function () {
    File::put(base_path('resources/views/ownership-review.blade.php'), "{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $first = TranslationWorkbenchKey::first();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'consolidation-review');
    $other->save();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConsolidation::class);
    TranslationWorkbenchKeyFinding::where('finding_id', TranslationWorkbenchFinding::orderByDesc('id')->first()->id)->update(['key_id' => $other->id]);
    TranslationWorkbenchFinding::query()->update(['literal_text' => 'Original']);
    expect($service->preview('ui.shared.title')['same_literals'])->toBeTrue();
    TranslationWorkbenchFinding::first()->update(['literal_text' => null]);
    $plan = $service->preview('ui.shared.title');
    expect($plan['same_literals'])->toBeFalse();
    TranslationWorkbenchReview::create(['key_id' => $first->id, 'review_type' => 'translation_key', 'decision' => 'translation_key_updated', 'reviewed_at' => now()]);
    expect(fn () => $service->merge($first->id, $plan['proof']))->toThrow(ValidationException::class);
    TranslationWorkbenchFinding::first()->update(['status' => 'obsolete']);
    expect($service->preview('ui.shared.title')['proof'])->toBeNull();
    expect($other->fresh()->status)->not->toBe('obsolete');
});

it('requires explicit confirmation in the ownership modal and refreshes the editor after consolidation', function () {
    File::put(base_path('resources/views/ownership-modal.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'ownership-modal');
    $other->save();
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($finding->id);
    $editor->inspectTranslationKeyConflict();
    $editor->openKeyOwnershipConflict($first->id);
    expect($editor->keyOwnershipModalOpen)->toBeTrue()->and(TranslationWorkbenchReview::count())->toBe(0);
    $editor->ownershipTargetKeyId = (string) $other->id;
    expect(fn () => $editor->confirmKeyOwnershipMerge())->toThrow(ValidationException::class);
    $editor->ownershipMergeConfirmed = true;
    $editor->confirmKeyOwnershipMerge();
    expect($editor->keyOwnershipModalOpen)->toBeFalse()
        ->and($editor->keyConflictInspection['groups'][0]['ownership_conflict'])->toBeFalse()
        ->and($editor->keyConflictInspection['assignment_decision']['active'])->toBeTrue()
        ->and($first->fresh()->status)->toBe('obsolete');
});

it('previews and confirms independent task separation from the ownership modal', function () {
    File::put(base_path('resources/views/ownership-separate.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'ownership-separate');
    $other->save();
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($finding->id);
    $editor->inspectTranslationKeyConflict();
    $editor->openKeyOwnershipConflict($first->id);
    $editor->ownershipSeparateKeyId = (string) $first->id;
    $editor->ownershipSeparateTarget = 'ui.independent.title';
    $editor->previewOwnershipSeparation();
    expect($editor->ownershipSeparationPlan['issues'])->toBe([])
        ->and($first->fresh()->translation_key)->toBe('ui.shared.title');
    $editor->ownershipSeparateTarget = 'ui.changed.title';
    expect(fn () => $editor->confirmOwnershipSeparation())->toThrow(ValidationException::class);
    $editor->ownershipSeparateTarget = 'ui.independent.title';
    $editor->confirmOwnershipSeparation();
    expect($first->fresh()->translation_key)->toBe('ui.independent.title')
        ->and($other->fresh()->translation_key)->toBe('ui.shared.title')
        ->and($finding->fresh()->meta['pending_key_separation']['key_id'])->toBe($first->id);
});

it('blocks unsafe scalar ownership consolidation without changing stored data', function (string $case) {
    File::put(base_path('resources/views/ownership-blocked.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $first = TranslationWorkbenchKey::sole();
    $finding = TranslationWorkbenchFinding::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'ownership-blocked');
    $other->save();
    match ($case) {
        'own values' => DB::table('translation_workbench_key_values')->insert(['key_id' => $other->id, 'locale' => 'en', 'value' => 'Keep']),
        'dynamic' => $other->update(['is_dynamic_key' => true]),
        'pending' => $finding->update(['meta' => ['pending_key_separation' => ['key_id' => $first->id]]]),
        'outside' => (function () use ($other, $finding) {
            $outside = $other->replicate();
            $outside->fingerprint = hash('sha256', 'outside');
            $outside->translation_key = 'ui.other.title';
            $outside->save();
            TranslationWorkbenchKeyFinding::create(['key_id' => $outside->id, 'finding_id' => $finding->id, 'relation_type' => 'candidate', 'status' => 'active']);
        })(),
    };
    $before = TranslationWorkbenchKey::orderBy('id')->get()->toJson();
    $plan = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConsolidation::class)->preview('ui.shared.title');
    expect($plan['proof'])->toBeNull()->and($plan['issues'])->not->toBeEmpty()
        ->and(TranslationWorkbenchReview::count())->toBe(0)
        ->and(TranslationWorkbenchKey::orderBy('id')->get()->toJson())->toBe($before);
})->with(['own values', 'dynamic', 'pending', 'outside']);

it('retains shared values and archived findings while recording an explicit link reassignment', function () {
    File::put(base_path('resources/views/ownership-history.blade.php'), "{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $source = TranslationWorkbenchKey::sole();
    $target = $source->replicate();
    $target->fingerprint = hash('sha256', 'ownership-history-target');
    $target->save();
    $alias = $source->replicate();
    $alias->fingerprint = hash('sha256', 'ownership-history-alias');
    $alias->status = 'obsolete';
    $alias->meta = ['reconciled_canonical_key_id' => $source->id];
    $alias->save();
    $archived = $finding->replicate();
    $archived->status = 'obsolete';
    $archived->source_signature = hash('sha256', 'archived-finding');
    $archived->save();
    $archivedLink = TranslationWorkbenchKeyFinding::create(['key_id' => $source->id, 'finding_id' => $archived->id, 'relation_type' => 'candidate', 'status' => 'obsolete']);
    $priorLink = TranslationWorkbenchKeyFinding::create(['key_id' => $target->id, 'finding_id' => $finding->id, 'relation_type' => 'candidate', 'status' => 'obsolete']);
    TranslationWorkbenchLangValue::create(['locale' => 'en', 'namespace' => 'ui', 'lang_key' => 'shared.title', 'translation_key' => 'ui.shared.title', 'value' => 'Keep me', 'value_type' => 'string', 'status' => 'active', 'source_path' => 'lang/en/ui.php']);
    $values = TranslationWorkbenchLangValue::get()->toJson();
    $history = $archived->fresh()->getAttributes();
    $linkHistory = $archivedLink->fresh()->getAttributes();
    $service = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConsolidation::class);
    $service->merge($target->id, $service->preview('ui.shared.title')['proof']);
    expect(TranslationWorkbenchLangValue::get()->toJson())->toBe($values)
        ->and($archived->fresh()->getAttributes())->toBe($history)
        ->and($archivedLink->fresh()->getAttributes())->toBe($linkHistory)
        ->and($priorLink->fresh()->status)->toBe('active')
        ->and($alias->fresh()->meta['reconciled_canonical_key_id'])->toBe($target->id);
    expect(TranslationWorkbenchTimelineEvent::where('event_type', 'key_assignment_reconciled')->get()
        ->contains(fn ($event) => ($event->old_values['id'] ?? null) === $priorLink->id && ($event->old_values['status'] ?? null) === 'obsolete' && ($event->new_values['status'] ?? null) === 'active'))->toBeTrue();
});

it('versions an unambiguous source move while keeping distinct occurrences and their translation task', function () {
    $path = base_path('resources/views/versioned-finding.blade.php');
    File::put($path, "{{ __('Ready') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $original = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $keyBefore = $key->getAttributes();
    $raw = $original->only(['source_line', 'source_column', 'raw_expression', 'literal_text', 'suggested_key']);
    File::put($path, "\n\n{{ __('Ready') }}");
    $summary = $syncer->sync($scanner->scan(['resources/views']));
    $next = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($summary['findings_created'])->toBe(1)->and($summary['findings_obsoleted'])->toBe(1)
        ->and($original->fresh()->status)->toBe('obsolete')->and($original->fresh()->only(array_keys($raw)))->toBe($raw)
        ->and($original->fresh()->meta['successor_finding_id'])->toBe($next->id)
        ->and($next->meta['predecessor_finding_id'])->toBe($original->id)->and($next->source_line)->toBe(3)
        ->and($key->fresh()->getAttributes())->toBe($keyBefore);
    expect(TranslationWorkbenchKeyFinding::where('finding_id', $original->id)->where('status', 'active')->count())->toBe(0);
    expect(TranslationWorkbenchKeyFinding::where('finding_id', $next->id)->where('status', 'active')->pluck('key_id')->all())->toBe([$key->id]);
    $events = TranslationWorkbenchTimelineEvent::count();
    $syncer->sync($scanner->scan(['resources/views']));
    expect(TranslationWorkbenchFinding::count())->toBe(2)->and(TranslationWorkbenchTimelineEvent::count())->toBe($events);
    // An additional occurrence is independent, not a successor of the existing one.
    File::append($path, "\n{{ __('Ready') }}");
    $syncer->sync($scanner->scan(['resources/views']));
    expect(TranslationWorkbenchFinding::where('status', 'active')->count())->toBe(2)
        ->and($next->fresh()->status)->toBe('active');
    $additional = TranslationWorkbenchFinding::where('status', 'active')->where('id', '!=', $next->id)->sole();
    expect($additional->meta)->not->toHaveKey('predecessor_finding_id');
});

it('versions a column move and preserves unresolved assignments instead of merging findings or owners', function () {
    $path = base_path('resources/views/versioned-column.blade.php');
    File::put($path, "{{ __('ui.shared.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $original = TranslationWorkbenchFinding::sole();
    $first = TranslationWorkbenchKey::sole();
    $other = $first->replicate();
    $other->fingerprint = hash('sha256', 'versioned-other-owner');
    $other->save();
    TranslationWorkbenchKeyFinding::create(['key_id' => $other->id, 'finding_id' => $original->id, 'relation_type' => 'candidate', 'status' => 'active']);
    File::put($path, "    {{ __('ui.shared.title') }}");
    $syncer->sync($scanner->scan(['resources/views']));
    $next = TranslationWorkbenchFinding::where('status', 'active')->sole();
    expect($next->id)->not->toBe($original->id)->and($next->source_column)->toBe($original->source_column + 4)
        ->and(TranslationWorkbenchKeyFinding::where('finding_id', $next->id)->where('status', 'active')->orderBy('key_id')->pluck('key_id')->all())->toBe([$first->id, $other->id]);
    $syncer->sync($scanner->scan(['resources/views']));
    expect(TranslationWorkbenchFinding::count())->toBe(2)->and(TranslationWorkbenchKey::count())->toBe(2);
});

it('navigates finding succession read only without reopening archived findings for editing', function () {
    $path = base_path('resources/views/lineage-navigation.blade.php');
    File::put($path, "{{ __('ui.shared.title') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $syncer->sync($scanner->scan(['resources/views']));
    $first = TranslationWorkbenchFinding::sole();
    File::put($path, "\n{{ __('ui.shared.title') }}");
    $syncer->sync($scanner->scan(['resources/views']));
    $second = TranslationWorkbenchFinding::where('status', 'active')->sole();
    File::put($path, "\n\n{{ __('ui.shared.title') }}");
    $syncer->sync($scanner->scan(['resources/views']));
    $third = TranslationWorkbenchFinding::where('status', 'active')->sole();
    $lineage = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFindingLineage::class);
    $rows = $lineage->rows($second->id);
    expect(array_column($rows, 'id'))->toBe([$first->id, $second->id, $third->id])
        ->and(array_column($rows, 'role'))->toBe(['predecessor', 'selected', 'successor'])
        ->and(array_column($rows, 'line'))->toBe([1, 2, 3])
        ->and($rows[0]['keys'][0]['status'])->toBe('obsolete')
        ->and($rows[2]['keys'][0]['status'])->toBe('active');
    $before = [];
    foreach (['findings', 'keys', 'key_findings', 'reviews', 'timeline_events'] as $table) {
        $before[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson();
    }
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($third->id);
    $enteredKey = $editor->translationKeyValue;
    $editor->openFindingVersion($second->id);
    expect($editor->timelineFindingId)->toBe($second->id)->and($editor->timelineModalOpen)->toBeTrue();
    $editor->openFindingVersion($first->id);
    expect($editor->timelineFindingId)->toBe($first->id)
        ->and($editor->translationKeyFindingId)->toBe($third->id)->and($editor->translationKeyValue)->toBe($enteredKey);
    expect(fn () => $editor->openFindingVersion(999999))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    foreach ($before as $table => $json) {
        expect(DB::table('translation_workbench_'.$table)->orderBy('id')->get()->toJson())->toBe($json);
    }
    // Broken one-way metadata must not turn an unrelated finding into a navigation target.
    $first->update(['meta' => []]);
    expect(array_column($lineage->rows($second->id), 'id'))->toBe([$second->id, $third->id]);
});

it('counts duplicate key records independently from the number of linked findings', function () {
    File::put(base_path('resources/views/key-record-count.blade.php'), "{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}\n{{ __('ui.shared.title') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $key = TranslationWorkbenchKey::sole();
    $inspector = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchKeyConflictInspector::class);
    $group = $inspector->inspect('ui.shared.title', 'ui.shared.title')['groups'][0];
    expect($group['owners'])->toHaveCount(1)->and($group['findings'])->toHaveCount(3)
        ->and($group['ownership_conflict'])->toBeFalse()->and($group['ambiguous_finding_ids'])->toBe([]);
    $duplicate = $key->replicate();
    $duplicate->fingerprint = hash('sha256', 'duplicate-key-count');
    $duplicate->save();
    // Even without additional findings, another active key record creates the conflict.
    $group = $inspector->inspect('ui.shared.title', 'ui.shared.title')['groups'][0];
    expect($group['ownership_conflict'])->toBeTrue()->and($group['findings'])->toHaveCount(3)
        ->and($group['ambiguous_finding_ids'])->toBe([]);
    $duplicate->update(['status' => 'obsolete']);
    expect($inspector->inspect('ui.shared.title', 'ui.shared.title')['groups'][0]['ownership_conflict'])->toBeFalse();
});

it('retains original finding and key suggestions when later scanner suggestions differ', function () {
    File::put(base_path('resources/views/immutable-suggestion.blade.php'), "{{ __('Original literal') }}");
    $scanner = app(TranslationWorkbenchScanner::class);
    $syncer = app(TranslationWorkbenchFoundationSyncer::class);
    $item = $scanner->scan(['resources/views'])->sole();
    $syncer->sync(collect([$item]));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $original = $finding->only(['suggested_key', 'namespace', 'group', 'path_key', 'scope']);
    $capture = collect($finding->meta)->only(['suggested_key_path', 'suggested_key_name', 'suggested_key_source'])->all();
    // Simulate a later scanner-rule change without changing the source occurrence.
    $newItem = new \Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation(...[...get_object_vars($item),
        'suggestedKey' => 'ui.recalculated.suggestion', 'meta' => [...$item->meta,
            'suggested_key_source' => 'new_rule', 'suggested_key_name' => 'suggestion', 'suggested_key_path' => ['ui', 'recalculated']]]);
    foreach ([1, 2] as $run) {
        $syncer->sync(collect([$newItem]));
        expect($finding->fresh()->only(array_keys($original)))->toBe($original)
            ->and(collect($finding->fresh()->meta)->only(array_keys($capture))->all())->toBe($capture)
            ->and($key->fresh()->suggested_key)->toBe($key->suggested_key)
            ->and(TranslationWorkbenchKey::count())->toBe(1)
            ->and(TranslationWorkbenchFinding::count())->toBe(1);
    }
    // A stored key-record suggestion can differ from the finding: neither is overwritten.
    $key->update(['suggested_key' => 'ui.historical.record']);
    $syncer->sync(collect([$item]));
    expect($key->fresh()->suggested_key)->toBe('ui.historical.record')
        ->and($finding->fresh()->suggested_key)->toBe($original['suggested_key']);
});

it('uses the finding suggestion for editor actions regardless of the linked key suggestion', function () {
    File::put(base_path('resources/views/finding-suggestion.blade.php'), "{{ __('Finding suggestion') }}");
    app(TranslationWorkbenchFoundationSyncer::class)->sync(app(TranslationWorkbenchScanner::class)->scan(['resources/views']));
    $finding = TranslationWorkbenchFinding::sole();
    $key = TranslationWorkbenchKey::sole();
    $key->update(['suggested_key' => 'ui.different.owner']);
    $editor = new TranslationWorkbenchEntries;
    $editor->openTranslationKeyModal($finding->id);
    $editor->copySuggestedKeyToTranslationKeyModal();
    expect($editor->translationKeyValue)->toBe($finding->suggested_key);
    $editor->transformSuggestedKeyToUiTranslationKeyModal();
    expect($editor->translationKeySegmentBaseValue)->toBe($finding->suggested_key)
        ->and($editor->translationKeyValue)->not->toBe('ui.owner');
    $editor->acceptSuggestedTranslationKey($finding->id);
    expect($key->fresh()->translation_key)->toBe($finding->suggested_key)
        ->and($key->fresh()->suggested_key)->toBe('ui.different.owner')
        ->and($finding->fresh()->suggested_key)->toBe($finding->suggested_key);
});
