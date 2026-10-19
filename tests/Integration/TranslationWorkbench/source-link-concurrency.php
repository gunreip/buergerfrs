<?php

// Explicit integration runner: php tests/Integration/TranslationWorkbench/source-link-concurrency.php
// Creates its own PostgreSQL cluster; never reads application DB connection credentials.
use App\Models\User;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicSourceLinkWriter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$mode = $argv[1] ?? 'launch';
if ($mode === 'launch') {
    $bin = getenv('TW_TEST_POSTGRES_BIN') ?: '/usr/lib/postgresql/18/bin';
    $directory = sys_get_temp_dir().'/tw-source-links-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    mkdir($directory.'/socket', 0700);
    $started = false;
    try {
        (new Process([$bin.'/initdb', '-D', $directory.'/data', '-U', 'workbench_test', '--auth-local=trust', '--auth-host=reject', '--no-locale']))->mustRun();
        file_put_contents($directory.'/data/postgresql.conf', "\nlisten_addresses = ''\nunix_socket_directories = '".$directory."/socket'\nunix_socket_permissions = 0700\n", FILE_APPEND);
        try {
            (new Process([$bin.'/pg_ctl', '-D', $directory.'/data', '-l', $directory.'/server.log', '-w', 'start']))->mustRun();
        } catch (Throwable $exception) {
            if (is_file($directory.'/server.log')) {
                fwrite(STDERR, file_get_contents($directory.'/server.log'));
            }
            throw $exception;
        }
        $started = true;
        (new Process([$bin.'/createdb', '-h', $directory.'/socket', '-U', 'workbench_test', 'workbench_concurrency_test']))->mustRun();
        $test = new Process([PHP_BINARY, __FILE__, 'run', $directory]);
        $test->setTimeout(90);
        $test->mustRun(static fn ($type, $buffer) => print($buffer));
    } finally {
        if ($started) {
            (new Process([$bin.'/pg_ctl', '-D', $directory.'/data', '-m', 'immediate', '-w', 'stop']))->mustRun();
        }
        // Only the unique directory created by this invocation is removed.
        (new Illuminate\Filesystem\Filesystem)->deleteDirectory($directory);
    }
    exit;
}

$directory = realpath($argv[2] ?? '');
if (! in_array($mode, ['run', 'worker'], true) || ! $directory
    || dirname($directory) !== realpath(sys_get_temp_dir())
    || ! preg_match('/^tw-source-links-[a-f0-9]{16}$/', basename($directory))
    || ! is_file($directory.'/data/PG_VERSION')) {
    throw new RuntimeException('Only the runner-owned temporary cluster is allowed.');
}
foreach (['APP_ENV' => 'testing', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => '',
    'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'TELESCOPE_ENABLED' => 'false',
    'PULSE_ENABLED' => 'false', 'NIGHTWATCH_ENABLED' => 'false',
    'APP_CONFIG_CACHE' => $directory.'/unused-config.php'] as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $_SERVER[$name] = $value;
}
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'workbench_concurrency_test', 'database.connections.workbench_concurrency_test' => [
    'driver' => 'pgsql', 'host' => $directory.'/socket', 'port' => 5432,
    'database' => 'workbench_concurrency_test', 'username' => 'workbench_test', 'password' => '',
    'charset' => 'utf8', 'prefix' => '', 'search_path' => 'public', 'sslmode' => 'disable',
]]);
DB::statement("SET lock_timeout = '10s'");
DB::statement("SET statement_timeout = '15s'");
$actor = Mockery::mock(User::class)->makePartial();
$actor->id = 1;
$actor->exists = true;
$actor->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturn(true);
Auth::setUser($actor);
// Publication checks also use only runner-owned files and isolated settings.
app()->useLangPath($directory.'/lang');
app()->useStoragePath($directory.'/storage');
if (! is_dir($directory.'/lang')) { mkdir($directory.'/lang', 0700); }
$settings = (new ReflectionClass(\App\Settings\AppGeneralSettings::class))->newInstanceWithoutConstructor();
$settings->locale = 'de';
$settings->availableLocales = ['en', 'de'];
$settings->addedPrimaryLocales = [];
app()->instance(\App\Settings\AppGeneralSettings::class, $settings);
$writer = app(TranslationWorkbenchDynamicSourceLinkWriter::class);

if ($mode === 'worker') {
    $payload = json_decode(file_get_contents($directory.'/payload.json'), true, flags: JSON_THROW_ON_ERROR);
    echo json_encode(['pid' => DB::selectOne('SELECT pg_backend_pid() AS pid')->pid])."\n";
    flush();
    if (($payload['kind'] ?? null) === 'publication-verify') {
        try {
            app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class)->verify(
                'scalar', $payload['keyId'], $payload['findingId'], ['de'], $payload['fingerprint'],
            );
            echo "publication-current\n";
        } catch (ValidationException $exception) {
            if (! str_contains($exception->getMessage(), 'publication preview is out of date')) { throw $exception; }
            echo "publication-stale\n";
        }
        exit;
    }
    if (($payload['kind'] ?? null) === 'value-approval') {
        try {
            app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class)->approve(
                $payload['valueKind'], $payload['valueId'], $payload['keyId'], $payload['findingId'], $payload['fingerprint'],
            );
            echo "value-approved\n";
        } catch (ValidationException $exception) {
            if (! str_contains($exception->getMessage(), 'translation changed after the approval preview')) { throw $exception; }
            echo "approval-stale-preview\n";
        }
        exit;
    }
    if (in_array($payload['kind'] ?? null, ['scalar-preview-writer', 'dynamic-preview-writer'], true)) {
        $locales = Mockery::mock(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales::class);
        $locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => []]);
        app()->instance(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales::class, $locales);
        if ($payload['kind'] === 'dynamic-preview-writer') {
            try {
                app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicEditor::class)->save(
                    $payload['keyId'], $payload['findingId'], [], ['v_'.sha1('draft') => 'Intentional option edit'], $payload['fingerprint'],
                );
                echo "dynamic-saved\n";
            } catch (ValidationException $exception) {
                if (! str_contains($exception->getMessage(), 'options or translations changed')) { throw $exception; }
                echo "dynamic-stale-preview\n";
            }
            exit;
        }
        try {
            app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchScalarEditor::class)->savePreview(
                $payload['keyId'], $payload['findingId'], [['locale' => 'de', 'value' => 'Intentional editor value']], $payload['fingerprint'],
            );
            echo "scalar-saved\n";
        } catch (ValidationException $exception) {
            if (! str_contains($exception->getMessage(), 'translation changed after opening')) { throw $exception; }
            echo "scalar-stale-preview\n";
        }
        exit;
    }
    if (($payload['kind'] ?? null) === 'candidate-discovery') {
        $command = app(\Gunreip\TranslationWorkbench\Console\DiscoverDynamicSourceCandidates::class);
        echo (new ReflectionMethod($command, 'syncCandidate'))->invoke($command, (object) $payload['source'], $payload['candidate'])."\n";
        exit;
    }
    if (($payload['kind'] ?? null) === 'inventory-writer') {
        $query = DB::table('translation_workbench_option_discoveries');
        match ($payload['operation']) {
            'insert' => $query->insert($payload['attributes']),
            'update' => $query->where('id', $payload['id'])->update(['options' => json_encode(['changed' => 'Changed'])]),
            'delete' => $query->where('id', $payload['id'])->delete(),
        };
        echo "discovery-written\n";
        exit;
    }
    if (($payload['kind'] ?? null) === 'shared-input-writer') {
        DB::table($payload['table'])->where('id', $payload['id'])->update($payload['changes']);
        echo "shared-input-written\n";
        exit;
    }
    if (($payload['kind'] ?? null) === 'bulk-writer') {
        try {
            $result = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchBulkKeyWriter::class)
                ->equalize($payload['ids'], $payload['target'] ?? 'ui.shared.concurrent', $payload['fingerprint'] ?? null);
        } catch (ValidationException $exception) {
            if (! str_contains($exception->getMessage(), 'selection changed after the preview')) { throw $exception; }
            echo "bulk-stale-preview\n";
            exit;
        }
        echo 'bulk-updated:'.$result."\n";
        exit;
    }
    if (($payload['kind'] ?? null) === 'shared-detector') {
        $command = app(\Gunreip\TranslationWorkbench\Console\DetectSharedKeyCandidates::class);
        $result = DB::transaction(fn () => (new ReflectionMethod($command, 'detect'))->invoke($command, false));
        echo 'shared-groups:'.$result[1]['shared_decision_groups']."\n";
        exit;
    }
    if (($payload['kind'] ?? null) === 'inventory-reader') {
        DB::transaction(function () use ($payload): void {
            $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
            (new ReflectionMethod($command, 'lockDiscoveryInventory'))->invoke($command);
            echo DB::table('translation_workbench_option_discoveries')->where('fingerprint', $payload['fingerprint'])->exists()
                ? "present\n" : "absent\n";
        });
        exit;
    }
    if (($payload['kind'] ?? null) === 'retirement') {
        $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
        $initial = collect($payload['initial'])->map(fn (array $source): object => (object) $source);
        try {
            DB::transaction(fn () => (new ReflectionMethod($command, 'obsoleteUnseenSources'))->invoke($command, [], $initial));
            echo "retired\n";
        } catch (ValidationException $exception) {
            if (! str_contains($exception->getMessage(), 'changed during classification')) {
                throw $exception;
            }
            echo "stale-retirement-rejected\n";
        }
        exit;
    }
    if (($payload['kind'] ?? null) === 'classifier') {
        $command = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
        try {
            DB::transaction(fn () => (new ReflectionMethod($command, 'lockClassificationInput'))
                ->invoke($command, (object) $payload['row'], $payload['classifications']));
            echo "accepted\n";
        } catch (ValidationException $exception) {
            if (! str_contains($exception->getMessage(), 'changed during classification')) {
                throw $exception;
            }
            echo "stale-classification-rejected\n";
        }
        exit;
    }
    if (($payload['kind'] ?? null) === 'discovery') {
        $command = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
        try {
            (new ReflectionMethod($command, 'resolveDiscoverySource'))->invoke($command, (object) $payload['source'], (object) $payload['discovery']);
            echo "accepted\n";
        } catch (ValidationException $exception) {
            if (str_contains($exception->getMessage(), 'The finding, source file or key assignment changed during resolution.')) {
                echo "stale-owner-rejected\n";
            } elseif (str_contains($exception->getMessage(), 'The source or discovery changed during resolution.')) {
                echo "stale-discovery-rejected\n";
            } else {
                throw $exception;
            }
        }
        exit;
    }
    if ($payload['resolver'] ?? false) {
        $consumer = (object) $payload['related'];
        $consumer->key_suggested_key = 'ui.fixture';
        $consumer->finding_suggested_key = 'ui.fixture';
        $command = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
        $summary = (new ReflectionMethod($command, 'syncRuntimeSourceLink'))->invoke($command, (object) $payload['runtime'][0], $consumer);
        if ($summary['runtime_source_links_unchanged'] !== 1 || $summary['timeline_events_created'] !== 0) {
            throw new RuntimeException('Resolver did not preserve the concurrent review.');
        }
        echo "resolver-preserved-review\n";
        exit;
    }
    try {
        $writer->{$payload['action']}((object) $payload['finding'], $payload['runtime'], $payload['related'], $payload['links']);
        echo "accepted\n";
    } catch (ValidationException $exception) {
        if (! str_contains($exception->getMessage(), 'The links have changed since the source review.')) {
            throw $exception;
        }
        echo "stale-preview-rejected\n";
    }
    exit;
}

Schema::create('users', function (Blueprint $table) { $table->id(); $table->json('settings')->nullable(); $table->timestamps(); });
DB::table('users')->insert(['id' => 1]);
Schema::create('languages', fn (Blueprint $table) => $table->id());
Schema::create('countries', fn (Blueprint $table) => $table->id());
(require $root.'/database/migrations/2026_05_13_082530_create_locales_table.php')->up();
foreach (glob($root.'/packages/gunreip/laravel-translation-workbench/database/migrations/*.php') as $migration) {
    (require $migration)->up();
}
$fileId = DB::table('translation_workbench_source_files')->insertGetId(['path' => 'fixture.blade.php', 'source_type' => 'blade', 'status' => 'active']);
$findingId = DB::table('translation_workbench_findings')->insertGetId([
    'source_file_id' => $fileId, 'fingerprint' => hash('sha256', 'fixture'), 'source_signature' => hash('sha256', 'signature'), 'kind' => 'literal', 'status' => 'active',
]);
$keyId = DB::table('translation_workbench_keys')->insertGetId(['fingerprint' => hash('sha256', 'key'), 'translation_key' => 'ui.fixture', 'status' => 'open']);
DB::table('translation_workbench_key_findings')->insert(['key_id' => $keyId, 'finding_id' => $findingId, 'status' => 'active']);
$sources = [];
foreach (['runtime_options', 'runtime_db_options', 'collection'] as $type) {
    $id = DB::table('translation_workbench_dynamic_sources')->insertGetId([
        'fingerprint' => hash('sha256', $type), 'key_id' => $keyId, 'finding_id' => $findingId, 'source_type' => $type, 'values_count' => 1, 'status' => 'active',
    ]);
    DB::table('translation_workbench_dynamic_source_values')->insert(['dynamic_source_id' => $id, 'value_key' => 'draft', 'native_label' => 'Draft', 'status' => 'active']);
    $sources[] = [...(array) DB::table('translation_workbench_dynamic_sources')->find($id), 'values' => [['value_key' => 'draft', 'native_label' => 'Draft', 'status' => 'active']]];
}
$runtime = array_slice($sources, 0, 2);
$related = $sources[2];
$finding = (object) ['id' => $findingId, 'key_id' => $keyId, 'suggested_key' => 'ui.fixture', 'dynamic_scope' => null];
$assert = static function (bool $condition, string $message): void { if (! $condition) { throw new RuntimeException($message); } };
$counts = static fn () => [DB::table('translation_workbench_reviews')->count(), DB::table('translation_workbench_timeline_events')->count()];
$waitForPostgresLock = static function (Process $worker) use ($assert): void {
    $deadline = microtime(true) + 8;
    $waited = false;
    while (microtime(true) < $deadline && $worker->isRunning()) {
        $line = explode("\n", trim($worker->getOutput()))[0];
        $pid = json_decode($line, true)['pid'] ?? null;
        if ($pid) {
            // Avoid reusing a statistics snapshot inside this transaction.
            DB::select('SELECT pg_stat_clear_snapshot()');
            $state = DB::selectOne('SELECT wait_event_type FROM pg_stat_activity WHERE pid = ?', [$pid]);
            if ($state?->wait_event_type === 'Lock') { $waited = true; break; }
        }
        usleep(20000);
    }
    $assert($waited, 'Second writer did not demonstrably wait on a PostgreSQL lock. '.$worker->getErrorOutput());
};
foreach ([['confirm', true, false], ['unlink', true, false], ['confirm', false, false], ['unlink', false, false], ['confirm', true, true], ['unlink', true, true]] as [$action, $commit, $resolver]) {
    $links = $writer->linkSnapshot(array_column($runtime, 'id'), $related['id']);
    file_put_contents($directory.'/payload.json', json_encode(compact('action', 'finding', 'runtime', 'related', 'links', 'resolver'), JSON_THROW_ON_ERROR));
    $before = $counts();
    $worker = null;
    try {
        DB::beginTransaction();
        // The real writer runs inside an outer transaction so its locks remain held.
        $writer->{$action}($finding, $runtime, $related, $links);
        $writtenCandidates = DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson();
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $outcome = $resolver ? 'resolver-preserved-review' : ($commit ? 'stale-preview-rejected' : 'accepted');
        $assert(in_array($outcome, explode("\n", trim($worker->getOutput())), true), 'Unexpected second writer outcome: '.$worker->getOutput());
        $assert($counts() === [$before[0] + 1, $before[1] + 1], 'Unexpected duplicate reviews or timeline events.');
        if ($commit) {
            $assert(DB::table('translation_workbench_dynamic_source_candidates')->orderBy('id')->get()->toJson() === $writtenCandidates, 'Second process changed the committed candidate contents.');
        }
        $assert(DB::table('translation_workbench_dynamic_source_candidates')->count() === 2, 'Unexpected candidate count.');
        $expected = $action === 'confirm' ? 'active' : 'inactive';
        $assert(DB::table('translation_workbench_dynamic_source_candidates')->where('status', $expected)->count() === 2, 'Unexpected final link status.');
        echo json_encode(['action' => $action, 'result' => 'passed', 'lock_wait_observed' => true, 'first_transaction' => $commit ? 'committed' : 'rolled_back', 'second_writer' => $outcome, 'reviews_added' => 1, 'events_added' => 1])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
    }
}

$command = app(\Gunreip\TranslationWorkbench\Console\ResolveUnknownDynamicSources::class);
$resolve = new ReflectionMethod($command, 'resolveDiscoverySource');
foreach ([['resolve', true], ['resolve', false], ['change_discovery', true], ['change_discovery', false],
    ['retire_finding', true], ['retire_finding', false], ['retire_file', true], ['retire_file', false],
    ['retire_key', true], ['retire_key', false], ['retire_link', true], ['retire_link', false]] as [$operation, $commit]) {
    $identity = $operation.'-'.($commit ? 'commit' : 'rollback');
    $caseFileId = DB::table('translation_workbench_source_files')->insertGetId(['path' => $identity.'.blade.php', 'source_type' => 'blade', 'status' => 'active']);
    $findingId = DB::table('translation_workbench_findings')->insertGetId([
        'source_file_id' => $caseFileId, 'fingerprint' => hash('sha256', $identity), 'source_signature' => hash('sha256', 'signature-'.$identity),
        'kind' => 'dynamic', 'status' => 'active', 'dynamic_data_state' => 'unknown',
    ]);
    $keyId = DB::table('translation_workbench_keys')->insertGetId([
        'fingerprint' => hash('sha256', 'key-'.$identity), 'translation_key' => 'ui.'.$identity, 'status' => 'open', 'dynamic_data_state' => 'unknown',
    ]);
    DB::table('translation_workbench_key_findings')->insert(['key_id' => $keyId, 'finding_id' => $findingId, 'status' => 'active']);
    DB::table('translation_workbench_dynamic_key_values')->insert([
        'key_id' => $keyId, 'value_key' => 'draft', 'locale' => 'de', 'value' => 'Geprüfter Entwurf', 'status' => 'active',
    ]);
    $sourceId = DB::table('translation_workbench_dynamic_sources')->insertGetId([
        'fingerprint' => hash('sha256', 'source-'.$identity), 'key_id' => $keyId, 'finding_id' => $findingId, 'source_type' => 'unknown', 'status' => 'active',
    ]);
    DB::table('translation_workbench_dynamic_source_values')->insert([
        'dynamic_source_id' => $sourceId, 'value_key' => 'draft', 'native_label' => 'Previous draft', 'status' => 'active',
    ]);
    $discoveryId = DB::table('translation_workbench_option_discoveries')->insertGetId([
        'fingerprint' => hash('sha256', 'discovery-'.$identity), 'scope' => 'fixture', 'source_path' => 'fixture.blade.php',
        'options_variable' => 'statusOptions', 'source_type' => 'hardcoded_array', 'options_count' => 2, 'status' => 'open',
        'options' => json_encode(['draft' => 'Draft', 'published' => 'Published'], JSON_THROW_ON_ERROR),
    ]);
    $source = DB::table('translation_workbench_dynamic_sources')->find($sourceId);
    $discovery = DB::table('translation_workbench_option_discoveries')->find($discoveryId);
    $kind = 'discovery';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'source', 'discovery'), JSON_THROW_ON_ERROR));
    $snapshot = static fn () => [
        'file' => (array) DB::table('translation_workbench_source_files')->find($caseFileId),
        'links' => DB::table('translation_workbench_key_findings')->where('finding_id', $findingId)->orderBy('id')->get()->toJson(),
        'source' => (array) DB::table('translation_workbench_dynamic_sources')->find($sourceId),
        'values' => DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $sourceId)->orderBy('id')->get()->toJson(),
        'finding' => (array) DB::table('translation_workbench_findings')->find($findingId),
        'key' => (array) DB::table('translation_workbench_keys')->find($keyId),
    ];
    $translations = DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson();
    $before = $counts();
    $worker = null;
    try {
        DB::beginTransaction();
        if ($operation === 'resolve') {
            $resolve->invoke($command, $source, $discovery);
        } elseif ($operation === 'change_discovery') {
            DB::table('translation_workbench_option_discoveries')->where('id', $discoveryId)
                ->update(['options' => json_encode(['changed' => 'Changed during selection'], JSON_THROW_ON_ERROR)]);
        }
        if (str_starts_with($operation, 'retire_')) {
            [$table, $column, $id] = match ($operation) {
                'retire_finding' => ['translation_workbench_findings', 'id', $findingId],
                'retire_file' => ['translation_workbench_source_files', 'id', $caseFileId],
                'retire_key' => ['translation_workbench_keys', 'id', $keyId],
                'retire_link' => ['translation_workbench_key_findings', 'finding_id', $findingId],
            };
            DB::table($table)->where($column, $id)->update(['status' => 'obsolete']);
        }
        $firstState = $snapshot();
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $outcome = $commit ? (str_starts_with($operation, 'retire_') ? 'stale-owner-rejected' : 'stale-discovery-rejected') : 'accepted';
        $assert(in_array($outcome, explode("\n", trim($worker->getOutput())), true), 'Unexpected discovery outcome: '.$worker->getOutput());
        if ($commit) {
            $assert($snapshot() === $firstState, 'Waiting resolver overwrote committed data.');
        }
        $resolved = $operation === 'resolve' || ! $commit;
        $assert($counts() === [$before[0], $before[1] + ($resolved ? 1 : 0)], 'Incorrect discovery history count.');
        if ($resolved) {
            $assert(DB::table('translation_workbench_dynamic_sources')->where('id', $sourceId)->value('classification') === 'multi_hardcoded', 'Source not classified.');
            $assert(DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $sourceId)->orderBy('value_key')->pluck('native_label', 'value_key')->all()
                === ['draft' => 'Draft', 'published' => 'Published'], 'Incorrect option values.');
            $assert(DB::table('translation_workbench_findings')->where('id', $findingId)->value('dynamic_data_state') === 'structured', 'Finding not structured.');
            $assert(DB::table('translation_workbench_keys')->where('id', $keyId)->value('dynamic_data_state') === 'structured', 'Key not structured.');
        }
        $assert(DB::table('translation_workbench_dynamic_key_values')->orderBy('id')->get()->toJson() === $translations, 'Stored translations changed.');
        echo json_encode(['action' => $operation, 'result' => 'passed', 'lock_wait_observed' => true,
            'first_transaction' => $commit ? 'committed' : 'rolled_back', 'second_writer' => $outcome, 'events_added' => $resolved ? 1 : 0])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
    }
}

$classifier = app(\Gunreip\TranslationWorkbench\Console\ClassifyDynamicValues::class);
foreach (['finding', 'file', 'key', 'link', 'discovery'] as $target) {
    foreach ([true, false] as $commit) {
        $identity = 'classifier-'.$target.'-'.($commit ? 'commit' : 'rollback');
        $fileId = DB::table('translation_workbench_source_files')->insertGetId(['path' => $identity.'.blade.php', 'source_type' => 'blade', 'status' => 'active']);
        $findingId = DB::table('translation_workbench_findings')->insertGetId([
            'source_file_id' => $fileId, 'fingerprint' => hash('sha256', $identity), 'source_signature' => hash('sha256', 'signature-'.$identity),
            'kind' => 'dynamic', 'status' => 'active',
        ]);
        $keyId = DB::table('translation_workbench_keys')->insertGetId([
            'fingerprint' => hash('sha256', 'key-'.$identity), 'translation_key' => 'ui.'.$identity, 'status' => 'open',
        ]);
        $linkId = DB::table('translation_workbench_key_findings')->insertGetId(['finding_id' => $findingId, 'key_id' => $keyId, 'status' => 'active']);
        $discoveryId = DB::table('translation_workbench_option_discoveries')->insertGetId([
            'fingerprint' => hash('sha256', 'discovery-'.$identity), 'scope' => 'fixture', 'source_path' => $identity.'.blade.php',
            'options_variable' => 'statusOptions', 'source_type' => 'hardcoded_array', 'options_count' => 1, 'status' => 'open',
            'options' => json_encode(['draft' => 'Draft']),
        ]);
        $row = (new ReflectionMethod($classifier, 'dynamicFindingRows'))->invoke($classifier, $findingId)->sole();
        $classifications = (new ReflectionMethod($classifier, 'classificationsForRow'))->invoke($classifier, $row);
        $assert($classifications[0]['option_discovery_id'] === $discoveryId, 'Classifier fixture must select its discovery.');
        $kind = 'classifier';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'row', 'classifications'), JSON_THROW_ON_ERROR));
        [$table, $id] = match ($target) {
            'finding' => ['translation_workbench_findings', $findingId],
            'file' => ['translation_workbench_source_files', $fileId],
            'key' => ['translation_workbench_keys', $keyId],
            'link' => ['translation_workbench_key_findings', $linkId],
            'discovery' => ['translation_workbench_option_discoveries', $discoveryId],
        };
        $before = $counts();
        $worker = null;
        try {
            DB::beginTransaction();
            DB::table($table)->where('id', $id)->update($target === 'discovery'
                ? ['options' => json_encode(['draft' => 'Changed'])] : ['status' => 'obsolete']);
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $outcome = $commit ? 'stale-classification-rejected' : 'accepted';
            $assert(in_array($outcome, explode("\n", trim($worker->getOutput())), true), 'Unexpected classification outcome: '.$worker->getOutput());
            $assert($counts() === $before, 'Input validation unexpectedly wrote history.');
            echo json_encode(['action' => $identity, 'result' => 'passed', 'lock_wait_observed' => true, 'second_writer' => $outcome])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
        }

        $sourceId = DB::table('translation_workbench_dynamic_sources')->insertGetId([
            'fingerprint' => hash('sha256', 'retirement-'.$identity), 'finding_id' => $findingId,
            'key_id' => $keyId, 'option_discovery_id' => $discoveryId, 'source_type' => 'hardcoded_array', 'status' => 'active',
        ]);
        $initial = (new ReflectionMethod($classifier, 'retirementSourceSnapshot'))->invoke($classifier)->only([$sourceId])->all();
        $retirementEventsBefore = DB::table('translation_workbench_timeline_events')->where('event_type', 'dynamic_source_obsoleted')->count();
        $kind = 'retirement';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'initial'), JSON_THROW_ON_ERROR));
        $worker = null;
        try {
            DB::beginTransaction();
            $status = DB::table($table)->where('id', $id)->value('status');
            DB::table($table)->where('id', $id)->update($target === 'discovery'
                ? ['options' => json_encode(['retirement' => 'Changed before retirement'])]
                : ['status' => $status === 'obsolete' ? 'active' : 'obsolete']);
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $outcome = $commit ? 'stale-retirement-rejected' : 'retired';
            $assert(in_array($outcome, explode("\n", trim($worker->getOutput())), true), 'Unexpected retirement outcome: '.$worker->getOutput());
            $assert(DB::table('translation_workbench_dynamic_sources')->where('id', $sourceId)->value('status') === ($commit ? 'active' : 'obsolete'), 'Incorrect retirement status.');
            $assert(DB::table('translation_workbench_timeline_events')->where('event_type', 'dynamic_source_obsoleted')->count() === $retirementEventsBefore + ($commit ? 0 : 1), 'Incorrect retirement history count.');
            if (! $commit) {
                $event = DB::table('translation_workbench_timeline_events')->where('event_type', 'dynamic_source_obsoleted')->orderByDesc('id')->first();
                $assert((json_decode($event->context, true)['dynamic_source_id'] ?? null) === $sourceId, 'Retirement history refers to another source.');
            }
            echo json_encode(['action' => 'retirement-'.$identity, 'result' => 'passed', 'lock_wait_observed' => true, 'second_writer' => $outcome])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
        }
    }
}

// Protect absence as well as existing rows: new discoveries must wait for the whole pass.
foreach (['insert', 'update', 'delete'] as $operation) {
    foreach ([true, false] as $commit) {
        $attributes = [
            'fingerprint' => hash('sha256', 'inventory-'.$operation.'-'.(int) $commit),
            'scope' => 'fixture', 'source_path' => 'inventory.blade.php', 'source_type' => 'hardcoded_array',
            'options_variable' => 'statusOptions', 'options_count' => 1, 'status' => 'open',
            'options' => json_encode(['draft' => 'Draft']),
        ];
        $id = $operation === 'insert' ? null : DB::table('translation_workbench_option_discoveries')->insertGetId($attributes);
        $kind = 'inventory-writer';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'operation', 'attributes', 'id'), JSON_THROW_ON_ERROR));
        $worker = null;
        try {
            DB::beginTransaction();
            (new ReflectionMethod($classifier, 'lockDiscoveryInventory'))->invoke($classifier);
            $before = DB::table('translation_workbench_option_discoveries')->orderBy('id')->get()->toJson();
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $assert(DB::table('translation_workbench_option_discoveries')->orderBy('id')->get()->toJson() === $before, 'Discovery changed while inventory lock was held.');
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $assert(str_contains($worker->getOutput(), 'discovery-written'), 'Discovery writer did not resume.');
            $row = DB::table('translation_workbench_option_discoveries')->where('fingerprint', $attributes['fingerprint'])->first();
            $assert($operation === 'delete' ? $row === null : $row !== null, 'Incorrect final discovery existence.');
            if ($operation === 'update') {
                $assert(json_decode($row->options, true) === ['changed' => 'Changed'], 'Waiting discovery update was lost.');
            }
            echo json_encode(['action' => 'inventory-'.$operation, 'result' => 'passed', 'lock_wait_observed' => true, 'classifier_transaction' => $commit ? 'committed' : 'rolled_back'])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
        }
    }
}

// Reverse order: classification must wait for an already running discovery writer.
foreach ([true, false] as $commit) {
    $fingerprint = hash('sha256', 'inventory-preexisting-writer-'.(int) $commit);
    $kind = 'inventory-reader';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'fingerprint'), JSON_THROW_ON_ERROR));
    $worker = null;
    try {
        DB::beginTransaction();
        DB::table('translation_workbench_option_discoveries')->insert([
            'fingerprint' => $fingerprint, 'scope' => 'fixture', 'source_path' => 'inventory.blade.php',
            'source_type' => 'hardcoded_array', 'options_variable' => 'newOptions', 'options_count' => 1,
            'status' => 'open', 'options' => json_encode(['new' => 'New']),
        ]);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(in_array($commit ? 'present' : 'absent', explode("\n", trim($worker->getOutput())), true), 'Classifier did not see the completed discovery transaction.');
        echo json_encode(['action' => 'inventory-existing-writer', 'result' => 'passed', 'lock_wait_observed' => true, 'discovery_transaction' => $commit ? 'committed' : 'rolled_back'])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
    }
}

$candidateCommand = app(\Gunreip\TranslationWorkbench\Console\DiscoverDynamicSourceCandidates::class);
foreach (['confirmed', 'rejected'] as $review) {
    foreach ([true, false] as $commit) {
        $sourceId = DB::table('translation_workbench_dynamic_sources')->insertGetId([
            'fingerprint' => hash('sha256', 'candidate-review-'.$review.'-'.(int) $commit),
            'classification' => 'unknown', 'cardinality' => 'unknown', 'origin' => 'unknown',
            'dynamic_scope' => 'status_options', 'source_expression' => '__($label)', 'status' => 'active',
        ]);
        $source = DB::table('translation_workbench_dynamic_sources')->find($sourceId);
        $source->dynamic_source_id = $sourceId;
        foreach (['translation_key', 'key_suggested_key', 'finding_suggested_key', 'found_translation_key', 'existing_key'] as $alias) { $source->{$alias} = null; }
        $candidate = (new ReflectionMethod($candidateCommand, 'candidatesForSource'))->invoke($candidateCommand, $source)[0];
        (new ReflectionMethod($candidateCommand, 'syncCandidate'))->invoke($candidateCommand, $source, $candidate);
        $candidateId = DB::table('translation_workbench_dynamic_source_candidates')->where('dynamic_source_id', $sourceId)->value('id');
        $candidate['confidence'] = 'high';
        $kind = 'candidate-discovery';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'source', 'candidate'), JSON_THROW_ON_ERROR));
        $before = $counts();
        $worker = null;
        try {
            DB::beginTransaction();
            DB::table('translation_workbench_dynamic_source_candidates')->where('id', $candidateId)->update([
                'review_status' => $review, 'candidate_values' => json_encode(['reviewed' => 'Saved decision']),
            ]);
            $reviewed = (array) DB::table('translation_workbench_dynamic_source_candidates')->find($candidateId);
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $outcome = $commit ? 'candidates_preserved' : 'candidates_updated';
            $assert(in_array($outcome, explode("\n", trim($worker->getOutput())), true), 'Unexpected candidate outcome: '.$worker->getOutput());
            $current = (array) DB::table('translation_workbench_dynamic_source_candidates')->find($candidateId);
            $assert($commit ? $current === $reviewed : ($current['review_status'] === 'pending' && $current['confidence'] === 'high'), 'Candidate decision was not preserved.');
            $assert($counts() === [$before[0], $before[1] + ($commit ? 0 : 1)], 'Incorrect candidate history count.');
            echo json_encode(['action' => 'candidate-'.$review, 'result' => 'passed', 'lock_wait_observed' => true, 'review_transaction' => $commit ? 'committed' : 'rolled_back'])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
        }
    }
}

// Verify native PostgreSQL JSON extraction and grouping for historical shared decisions.
$sharedKeyIds = [];
for ($index = 1; $index <= 2; $index++) {
    $identity = 'shared-decision-snapshot-'.$index;
    $sharedFileId = DB::table('translation_workbench_source_files')->insertGetId(['path' => $identity.'.blade.php', 'source_type' => 'blade']);
    $sharedFindingId = DB::table('translation_workbench_findings')->insertGetId([
        'source_file_id' => $sharedFileId, 'fingerprint' => hash('sha256', $identity),
        'source_signature' => hash('sha256', $identity), 'kind' => 'literal', 'literal_text' => 'Native shared snapshot',
    ]);
    $sharedKeyId = DB::table('translation_workbench_keys')->insertGetId([
        'fingerprint' => hash('sha256', $identity), 'translation_key' => 'ui.shared.snapshot',
    ]);
    $sharedKeyIds[] = $sharedKeyId;
    DB::table('translation_workbench_key_findings')->insert(['finding_id' => $sharedFindingId, 'key_id' => $sharedKeyId, 'status' => 'active']);
    DB::table('translation_workbench_reviews')->insert([
        'finding_id' => $sharedFindingId, 'key_id' => $sharedKeyId, 'review_type' => 'translation_key',
        'decision' => 'translation_key_bulk_equalized', 'new_values' => json_encode(['translation_key' => 'ui.shared.snapshot']),
        'meta' => json_encode(['normalized_literal' => 'native shared snapshot']), 'reviewed_at' => now(),
    ]);
}
$sharedCommand = app(\Gunreip\TranslationWorkbench\Console\DetectSharedKeyCandidates::class);
$readShared = fn () => (new ReflectionMethod($sharedCommand, 'sharedDecisionGroups'))->invoke($sharedCommand)
    ->where('normalized_literal', 'native shared snapshot');
$assert($readShared()->count() === 1, 'PostgreSQL did not match the valid shared review snapshot.');
DB::table('translation_workbench_keys')->whereIn('id', $sharedKeyIds)->update(['translation_key' => 'ui.shared.renamed']);
$assert($readShared()->isEmpty(), 'PostgreSQL reused an old shared review for renamed keys.');
echo json_encode(['action' => 'shared-review-json-snapshot', 'result' => 'passed', 'parallel' => false])."\n";

// Shared detection protects every input table and the candidate inventory until transaction end.
$sharedCandidateId = DB::table('translation_workbench_shared_key_candidates')->insertGetId([
    'finding_id' => $sharedFindingId, 'suggested_shared_translation_key' => 'ui.shared.snapshot',
    'normalized_literal' => 'native shared snapshot', 'status' => 'confirmed',
]);
$sharedReviewId = DB::table('translation_workbench_reviews')->where('finding_id', $sharedFindingId)->value('id');
$sharedLinkId = DB::table('translation_workbench_key_findings')->where('finding_id', $sharedFindingId)->value('id');
foreach ([
    ['source_files', $sharedFileId, ['status' => 'obsolete']],
    ['findings', $sharedFindingId, ['status' => 'obsolete']],
    ['keys', $sharedKeyId, ['status' => 'obsolete']],
    ['key_findings', $sharedLinkId, ['status' => 'obsolete']],
    ['reviews', $sharedReviewId, ['decision' => 'changed']],
    ['shared_key_candidates', $sharedCandidateId, ['status' => 'rejected']],
] as [$suffix, $id, $changes]) {
    foreach ([true, false] as $commit) {
        $table = 'translation_workbench_'.$suffix;
        $original = (array) DB::table($table)->find($id);
        $kind = 'shared-input-writer';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'table', 'id', 'changes'), JSON_THROW_ON_ERROR));
        $worker = null;
        try {
            DB::beginTransaction();
            (new ReflectionMethod($sharedCommand, 'lockDetectionInputs'))->invoke($sharedCommand);
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $assert((array) DB::table($table)->find($id) === $original, 'Shared input changed while protected.');
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $assert(str_contains($worker->getOutput(), 'shared-input-written'), 'Shared input writer did not resume.');
            foreach ($changes as $field => $value) {
                $assert(DB::table($table)->where('id', $id)->value($field) === $value, 'Shared input update lost.');
            }
            echo json_encode(['action' => 'shared-input-'.$suffix, 'result' => 'passed', 'lock_wait_observed' => true, 'detector_transaction' => $commit ? 'committed' : 'rolled_back'])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
            DB::table($table)->where('id', $id)->update($original);
        }
    }
}
// Reverse order: selection must see the finished writer, not an earlier plan.
DB::table('translation_workbench_keys')->whereIn('id', $sharedKeyIds)->update(['translation_key' => 'ui.shared.snapshot']);
foreach ([true, false] as $commit) {
    $kind = 'shared-detector';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind'), JSON_THROW_ON_ERROR));
    $worker = null;
    try {
        DB::beginTransaction();
        DB::table('translation_workbench_keys')->where('id', $sharedKeyId)->update(['status' => 'obsolete']);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(str_contains($worker->getOutput(), 'shared-groups:'.($commit ? '0' : '1')), 'Shared detector used an outdated input plan: '.$worker->getOutput());
        echo json_encode(['action' => 'shared-existing-writer', 'result' => 'passed', 'lock_wait_observed' => true, 'writer_transaction' => $commit ? 'committed' : 'rolled_back'])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
        DB::table('translation_workbench_keys')->where('id', $sharedKeyId)->update(['status' => 'open']);
    }
}

// General shared eligibility is derived independently from historical candidates.
$literalFindings = DB::table('translation_workbench_findings')->where('literal_text', 'Native shared snapshot')->orderBy('id')->get();
foreach ($literalFindings as $index => $finding) {
    DB::table('translation_workbench_findings')->where('id', $finding->id)->update(['suggested_key' => 'ui.snapshot.'.($index + 1)]);
}
$readLiteralGroups = fn () => app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSharedLiteralCandidates::class)
    ->groups()->get()->where('bulk_literal', 'native shared snapshot');
$assert($readLiteralGroups()->count() === 1, 'Native shared literal group missing.');
$assert((int) $readLiteralGroups()->sole()->suggested_key_count === 2, 'Native distinct SuggestedKey count incorrect.');
DB::table('translation_workbench_findings')->whereIn('id', $literalFindings->pluck('id'))->update(['suggested_key' => 'ui.snapshot.same']);
$assert($readLiteralGroups()->isEmpty(), 'Repeated identical SuggestedKeys incorrectly qualify as shared.');
echo json_encode(['action' => 'shared-literal-eligibility', 'result' => 'passed', 'parallel' => false])."\n";

// Bulk UI target validation must compare the review JSON with the current assignment.
$bulkWriter = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchBulkKeyWriter::class);
$readBulkTarget = fn () => (new ReflectionMethod($bulkWriter, 'existingBulkSharedKeyForLiteral'))
    ->invoke($bulkWriter, 'native shared snapshot', []);
$assert(($readBulkTarget()['translation_key'] ?? null) === 'ui.shared.snapshot', 'Native bulk target missing despite matching review.');
DB::table('translation_workbench_keys')->whereIn('id', $sharedKeyIds)->update(['translation_key' => 'ui.shared.changed']);
$assert($readBulkTarget() === null, 'Native bulk target accepted stale review JSON.');
DB::table('translation_workbench_keys')->whereIn('id', $sharedKeyIds)->update(['translation_key' => 'ui.shared.snapshot']);
echo json_encode(['action' => 'bulk-shared-target-snapshot', 'result' => 'passed', 'parallel' => false])."\n";

foreach ([
    ['source_files', $sharedFileId, ['status' => 'obsolete']],
    ['findings', $sharedFindingId, ['status' => 'obsolete']],
    ['keys', $sharedKeyId, ['status' => 'obsolete']],
    ['key_findings', $sharedLinkId, ['status' => 'obsolete']],
    ['reviews', $sharedReviewId, ['decision' => 'changed']],
] as [$suffix, $id, $changes]) {
    foreach ([true, false] as $commit) {
        $table = 'translation_workbench_'.$suffix;
        $original = (array) DB::table($table)->find($id);
        $kind = 'shared-input-writer';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'table', 'id', 'changes'), JSON_THROW_ON_ERROR));
        $worker = null;
        try {
            DB::beginTransaction();
            (new ReflectionMethod($bulkWriter, 'lockWriteInputs'))->invoke($bulkWriter);
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $assert((array) DB::table($table)->find($id) === $original, 'Bulk input changed while protected.');
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            foreach ($changes as $field => $value) {
                $assert(DB::table($table)->where('id', $id)->value($field) === $value, 'Bulk input writer did not resume.');
            }
            echo json_encode(['action' => 'bulk-input-'.$suffix, 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
            DB::table($table)->where('id', $id)->update($original);
        }
    }
}
foreach ([true, false] as $commit) {
    $kind = 'bulk-writer';
    $ids = $literalFindings->pluck('id')->all();
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'ids'), JSON_THROW_ON_ERROR));
    $worker = null;
    try {
        DB::beginTransaction();
        DB::table('translation_workbench_findings')->where('id', $sharedFindingId)->update(['status' => 'obsolete']);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(str_contains($worker->getOutput(), 'bulk-updated:'.($commit ? '0' : '2')), 'Bulk writer used stale source state: '.$worker->getOutput());
        echo json_encode(['action' => 'bulk-existing-writer', 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
        DB::table('translation_workbench_findings')->where('id', $sharedFindingId)->update(['status' => 'active']);
    }
}

foreach ([true, false] as $commit) {
    $ids = $literalFindings->pluck('id')->all();
    $fingerprint = $bulkWriter->contextFingerprint($bulkWriter->context($ids));
    $kind = 'bulk-writer';
    $target = 'ui.shared.preview';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'ids', 'fingerprint', 'target'), JSON_THROW_ON_ERROR));
    $worker = null;
    try {
        DB::beginTransaction();
        DB::table('translation_workbench_findings')->whereIn('id', $ids)->update(['literal_text' => 'Changed but still shared']);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(str_contains($worker->getOutput(), $commit ? 'bulk-stale-preview' : 'bulk-updated:2'), 'Bulk preview was not checked after waiting: '.$worker->getOutput());
        echo json_encode(['action' => 'bulk-preview-existing-writer', 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
        DB::table('translation_workbench_findings')->whereIn('id', $ids)->update(['literal_text' => 'Native shared snapshot']);
    }
}

$scalarEditor = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchScalarEditor::class);
$keyId = $sharedKeyId;
$findingId = $sharedFindingId;
$translationKey = DB::table('translation_workbench_keys')->where('id', $keyId)->value('translation_key');
$parts = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTranslationWriter::class)->keyParts($translationKey);
$valueId = DB::table('translation_workbench_lang_values')->insertGetId([
    'locale' => 'de', 'namespace' => $parts['namespace'], 'lang_key' => $parts['lang_key'],
    'translation_key' => $translationKey, 'value' => 'Original preview', 'status' => 'active',
    'source_path' => 'lang/de/ui.php',
]);
foreach ([true, false] as $commit) {
    $fingerprint = $scalarEditor->fingerprint($keyId, $findingId);
    $kind = 'scalar-preview-writer';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'keyId', 'findingId', 'fingerprint'), JSON_THROW_ON_ERROR));
    $worker = null;
    try {
        DB::beginTransaction();
        DB::table('translation_workbench_lang_values')->where('id', $valueId)->update(['value' => 'Newer stored value']);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(str_contains($worker->getOutput(), $commit ? 'scalar-stale-preview' : 'scalar-saved'), 'Unexpected scalar preview outcome.');
        $assert(DB::table('translation_workbench_lang_values')->where('id', $valueId)->value('value') === ($commit ? 'Newer stored value' : 'Intentional editor value'), 'Scalar writer lost a value.');
        echo json_encode(['action' => 'scalar-preview-existing-writer', 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
        DB::table('translation_workbench_lang_values')->where('id', $valueId)->update(['value' => 'Original preview']);
    }
}

$locales = Mockery::mock(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales::class);
$locales->shouldReceive('resolve')->andReturn(['source' => 'en', 'active' => 'de', 'sub' => []]);
app()->instance(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales::class, $locales);
$dynamicEditor = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicEditor::class);
$optionId = DB::table('translation_workbench_dynamic_key_values')->insertGetId([
    'key_id' => $keyId, 'value_key' => 'draft', 'locale' => 'de', 'value' => 'Original option',
    'native_label' => 'Draft', 'status' => 'active', 'source' => 'test',
]);
foreach ([true, false] as $commit) {
    $fingerprint = $dynamicEditor->fingerprint($keyId, $findingId);
    $kind = 'dynamic-preview-writer';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'keyId', 'findingId', 'fingerprint'), JSON_THROW_ON_ERROR));
    $worker = null;
    try {
        DB::beginTransaction();
        DB::table('translation_workbench_dynamic_key_values')->where('id', $optionId)->update(['value' => 'Newer option value']);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(str_contains($worker->getOutput(), $commit ? 'dynamic-stale-preview' : 'dynamic-saved'), 'Unexpected dynamic preview outcome: '.$worker->getOutput());
        $assert(DB::table('translation_workbench_dynamic_key_values')->where('id', $optionId)->value('value') === ($commit ? 'Newer option value' : 'Intentional option edit'), 'Dynamic writer lost an option value.');
        echo json_encode(['action' => 'dynamic-preview-existing-writer', 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
        DB::table('translation_workbench_dynamic_key_values')->where('id', $optionId)->update(['value' => 'Original option']);
    }
}

// Revisions are maintained by PostgreSQL for every writer, including direct SQL updates.
foreach (['scalar', 'option'] as $valueKind) {
    $fileId = DB::table('translation_workbench_source_files')->insertGetId(['path' => 'approval-'.$valueKind.'.blade.php', 'source_type' => 'blade', 'status' => 'active']);
    $findingId = DB::table('translation_workbench_findings')->insertGetId([
        'source_file_id' => $fileId, 'fingerprint' => hash('sha256', 'approval-finding-'.$valueKind), 'source_signature' => hash('sha256', 'approval-signature-'.$valueKind), 'kind' => 'literal', 'status' => 'active',
    ]);
    $keyId = DB::table('translation_workbench_keys')->insertGetId([
        'fingerprint' => hash('sha256', 'approval-key-'.$valueKind), 'translation_key' => 'ui.approval.'.$valueKind, 'status' => 'open', 'review_status' => 'reviewed',
    ]);
    DB::table('translation_workbench_key_findings')->insert(['key_id' => $keyId, 'finding_id' => $findingId, 'status' => 'active']);
    $table = $valueKind === 'scalar' ? 'translation_workbench_lang_values' : 'translation_workbench_dynamic_key_values';
    $attributes = ['locale' => 'de', 'value' => 'Original approved text', 'status' => 'active'];
    $attributes += $valueKind === 'scalar'
        ? ['namespace' => 'ui', 'lang_key' => 'approval.'.$valueKind, 'translation_key' => 'ui.approval.'.$valueKind, 'source_path' => 'lang/de/ui.php']
        : ['key_id' => $keyId, 'value_key' => 'draft'];
    $valueId = DB::table($table)->insertGetId($attributes);
    $approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
    $preview = $approval->preview($valueKind, $valueId, $keyId, $findingId);
    $approval->approve($valueKind, $valueId, $keyId, $findingId, $preview['fingerprint']);
    DB::table($table)->where('id', $valueId)->update(['value' => 'Intermediate text']);
    DB::table($table)->where('id', $valueId)->update(['value' => 'Original approved text']);
    $assert((int) DB::table($table)->where('id', $valueId)->value('value_revision') === 3, 'Value revisions did not record both edits.');
    $assert(! $approval->isApproved($valueKind, $valueId, $keyId, $findingId), 'Old approval became valid after restoring old text.');
    DB::table($table)->where('id', $valueId)->update(['value' => 'Original approved text', 'updated_at' => now()]);
    $assert((int) DB::table($table)->where('id', $valueId)->value('value_revision') === 3, 'Observation update advanced the revision.');
    echo json_encode(['action' => 'value-revision-'.$valueKind, 'result' => 'passed'])."\n";
    foreach ([true, false] as $commit) {
        $fingerprint = $approval->preview($valueKind, $valueId, $keyId, $findingId)['fingerprint'];
        $kind = 'value-approval';
        file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'valueKind', 'valueId', 'keyId', 'findingId', 'fingerprint'), JSON_THROW_ON_ERROR));
        $worker = null;
        DB::beginTransaction();
        try {
            DB::table($table)->where('id', $valueId)->update(['value' => 'Concurrent edit']);
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
            $worker->setTimeout(20);
            $worker->start();
            $waitForPostgresLock($worker);
            $commit ? DB::commit() : DB::rollBack();
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $assert(str_contains($worker->getOutput(), $commit ? 'approval-stale-preview' : 'value-approved'), 'Unexpected approval outcome: '.$worker->getOutput());
            $assert($approval->isApproved($valueKind, $valueId, $keyId, $findingId) === ! $commit, 'Approval validity does not match committed revision.');
            echo json_encode(['action' => 'value-approval-'.$valueKind, 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
        } finally {
            if (DB::transactionLevel()) { DB::rollBack(); }
            if ($worker?->isRunning()) { $worker->stop(); }
            DB::table($table)->where('id', $valueId)->update(['value' => 'Original approved text']);
        }
    }
}

$keyId = (int) DB::table('translation_workbench_keys')->where('translation_key', 'ui.approval.scalar')->value('id');
$findingId = (int) DB::table('translation_workbench_key_findings')->where('key_id', $keyId)->value('finding_id');
$valueId = (int) DB::table('translation_workbench_lang_values')->where('translation_key', 'ui.approval.scalar')->value('id');
foreach ([true, false] as $commit) {
    $publication = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
    $fingerprint = $publication->preview('scalar', $keyId, $findingId, ['de'])['fingerprint'];
    $kind = 'publication-verify';
    file_put_contents($directory.'/payload.json', json_encode(compact('kind', 'keyId', 'findingId', 'fingerprint'), JSON_THROW_ON_ERROR));
    $worker = null;
    DB::beginTransaction();
    try {
        DB::table('translation_workbench_lang_values')->where('id', $valueId)->update(['value' => 'Concurrent publication edit']);
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory]);
        $worker->setTimeout(20);
        $worker->start();
        $waitForPostgresLock($worker);
        $commit ? DB::commit() : DB::rollBack();
        $worker->wait();
        $assert($worker->isSuccessful(), $worker->getErrorOutput());
        $assert(str_contains($worker->getOutput(), $commit ? 'publication-stale' : 'publication-current'), 'Unexpected publication verification: '.$worker->getOutput());
        echo json_encode(['action' => 'publication-verify', 'result' => 'passed', 'lock_wait_observed' => true, 'committed' => $commit])."\n";
    } finally {
        if (DB::transactionLevel()) { DB::rollBack(); }
        if ($worker?->isRunning()) { $worker->stop(); }
        DB::table('translation_workbench_lang_values')->where('id', $valueId)->update(['value' => 'Original approved text']);
    }
}

$approval = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueApproval::class);
$approved = $approval->preview('scalar', $valueId, $keyId, $findingId);
$approval->approve('scalar', $valueId, $keyId, $findingId, $approved['fingerprint']);
$publication = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationPreview::class);
$plan = $publication->preview('scalar', $keyId, $findingId, ['de']);
$runner = app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner::class);
$result = $runner->publish('scalar', $keyId, $findingId, ['de'], $plan['fingerprint']);
$assert($result['state'] === 'complete', 'Publication did not complete.');
$events = DB::table('translation_workbench_timeline_events')->count();
$runner->resume($result['id']);
$assert(DB::table('translation_workbench_timeline_events')->count() === $events, 'Publication recovery duplicated history.');
$assert(DB::table('translation_workbench_reviews')->where('review_type', 'publication')->where('meta->run_id', $result['id'])->count() === 1, 'Missing publication receipt.');
echo json_encode(['action' => 'publication-write-and-resume', 'result' => 'passed'])."\n";

DB::table('translation_workbench_lang_values')->where('id', $valueId)->update(['value' => 'New approved publication value']);
$approved = $approval->preview('scalar', $valueId, $keyId, $findingId);
$approval->approve('scalar', $valueId, $keyId, $findingId, $approved['fingerprint']);
$plan = $publication->preview('scalar', $keyId, $findingId, ['de']);
$failingRunner = new class($publication, app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter::class)) extends \Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchPublicationRunner {
    protected function replaceFile(string $path, string $contents, ?string $expectedHash): void
    {
        throw new RuntimeException('Native closure fixture write failure');
    }
};
try {
    $failingRunner->publish('scalar', $keyId, $findingId, ['de'], $plan['fingerprint']);
    throw new LogicException('Expected interrupted publication.');
} catch (RuntimeException $exception) {
    $assert(str_contains($exception->getMessage(), 'Native closure fixture'), 'Unexpected publication failure.');
}
$runs = $runner->runs('scalar', $keyId, $findingId);
$interrupted = array_values(array_filter($runs, fn ($run) => $run['state'] === 'interrupted'))[0];
$context = ['kind' => 'scalar', 'keyId' => $keyId, 'findingId' => $findingId];
$proof = $runner->previewClosure($interrupted['id'], $context);
$runner->closeWithoutWriting($interrupted['id'], $context, $proof['fingerprint'], 'Retain current files and prepare a new attempt.');
$assert(DB::table('translation_workbench_reviews')->where('decision', 'publication_closed_without_writing')
    ->where('meta->run_id', $interrupted['id'])->count() === 1, 'Missing closure receipt.');
try {
    $runner->resume($interrupted['id'], $context);
    throw new LogicException('Closed publication resumed.');
} catch (\Illuminate\Validation\ValidationException) {}
$fresh = $publication->preview('scalar', $keyId, $findingId, ['de']);
$assert($runner->publish('scalar', $keyId, $findingId, ['de'], $fresh['fingerprint'])['state'] === 'complete', 'Closure did not release overlapping scope.');
echo json_encode(['action' => 'publication-close-and-replan', 'result' => 'passed'])."\n";

// Archive/current signatures coexist; a second current owner is rejected by PostgreSQL itself.
DB::beginTransaction();
try {
    $prototype = \Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding::query()->firstOrFail();
    $archive = $prototype->replicate();
    $archive->source_signature = hash('sha256', 'archive-signature-integration-'.bin2hex(random_bytes(8)));
    $archive->status = 'obsolete';
    $archive->save();
    $current = $archive->replicate();
    $current->status = 'active';
    $current->save();
    $rejected = false;
    try {
        DB::transaction(function () use ($current): void {
            $duplicate = $current->replicate();
            $duplicate->save();
        });
    } catch (\Illuminate\Database\QueryException $exception) {
        $rejected = $exception->getCode() === '23505';
    }
    if (! $rejected || $archive->fresh()->status !== 'obsolete') {
        throw new RuntimeException('Current finding signature uniqueness or archive preservation failed.');
    }
    echo json_encode(['action' => 'archived-finding-signature-uniqueness', 'result' => 'passed'])."\n";
} finally {
    DB::rollBack();
}
