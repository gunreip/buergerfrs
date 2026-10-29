<?php

use Gunreip\TranslationWorkbench\Generation\BaselineRevisionReviewer;
use Gunreip\TranslationWorkbench\Generation\BaselineValueReader;
use Gunreip\TranslationWorkbench\Generation\CorrectedSourceReconciler;
use Gunreip\TranslationWorkbench\Generation\RevisionReviewContext;
use Gunreip\TranslationWorkbench\Support\BaselineCorrectionPreflight;
use Gunreip\TranslationWorkbench\Support\BaselineKeyCorrectionPreview;
use Gunreip\TranslationWorkbench\Support\BaselineNameInventory;
use Gunreip\TranslationWorkbench\Support\BaselineProposalEvidence;
use Gunreip\TranslationWorkbench\Support\KeyCorrectionApplication;
use Gunreip\TranslationWorkbench\Support\KeyCorrectionDatabase;
use Gunreip\TranslationWorkbench\Support\KeyCorrectionProposal;
use Gunreip\TranslationWorkbench\Support\SourceKeyChangePreview;
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\BaselineTranslationReader;
use Tests\Support\TranslationWorkbench\CorrectedKeyValueReader;
use Tests\Support\TranslationWorkbench\CorrectedValueDraftImporter;
use Tests\Support\TranslationWorkbench\FreshGenerationArguments;
use Tests\Support\TranslationWorkbench\FreshGenerationCapture;
use Tests\Support\TranslationWorkbench\FreshGenerationPublishedValues;
use Tests\Support\TranslationWorkbench\PostgresPublisher;
use Tests\Support\TranslationWorkbench\PublishedRevisionReader;
use Tests\Support\TranslationWorkbench\SelectedGenerationPublication;
use Tests\Support\TranslationWorkbench\SelectedGenerationTransfer;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
umask(0077);
$read = static fn ($p) => json_decode(file_get_contents($p), true, flags: JSON_THROW_ON_ERROR);
$baseline = $read(__DIR__.'/published-values-2026-10-19.json');
$backupResult = $read(__DIR__.'/backup-restore-result-2026-10-19.json');
$manifestPath = $root.'/'.$backupResult['backup_manifest'];
if (hash_file('sha256', $manifestPath) !== $backupResult['backup_manifest_sha256'] || hash_file('sha256', $root.'/'.$baseline['dump']) !== $baseline['dump_sha256']) {
    throw new RuntimeException('Verified input changed');
}
$manifest = $read($manifestPath);
$backup = dirname($manifestPath);
// Runtime clusters are disposable. Recreate one per run instead of depending on
// a historical /tmp path from the immutable backup manifest.
$cluster = realpath(sys_get_temp_dir()).'/tw-restore-'.bin2hex(random_bytes(8));
if (! mkdir($cluster, 0700)) {
    throw new RuntimeException('Cannot create isolated rehearsal directory');
}
mkdir($cluster.'/socket', 0700);
$run = static function (array $args) use ($root): void {
    $p = new Process($args, $root, ['PGOPTIONS' => '', 'PGPASSWORD' => false, 'PGPASSFILE' => false]);
    $p->setTimeout(1800);
    $p->mustRun();
};
$bin = '/usr/lib/postgresql/18/bin';
$name = 'application_'.bin2hex(random_bytes(6));
$work = $backup.'/'.$name;
mkdir($work, 0700);
$started = false;
$checks = 0;
$check = static function (bool $ok, string $message) use (&$checks): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
    $checks++;
};
try {
    $run([$bin.'/initdb', '-D', $cluster.'/data', '-U', 'rehearsal', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($cluster.'/data/postgresql.conf', "\nlisten_addresses = ''\nunix_socket_directories = '".$cluster."/socket'\nunix_socket_permissions = 0700\n", FILE_APPEND);
    $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-l', $cluster.'/server.log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $cluster.'/socket', '-U', 'rehearsal', $name]);
    $run([$bin.'/pg_restore', '-h', $cluster.'/socket', '-U', 'rehearsal', '--exit-on-error', '--no-owner', '--no-privileges', '-d', $name, $root.'/'.$baseline['dump']]);
    $db = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $check($db->query('SHOW data_directory')->fetchColumn() === $cluster.'/data' && $db->query('SHOW listen_addresses')->fetchColumn() === '', 'Wrong cluster');
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    $fingerprint = static function () use ($db, $tables): array {
        $result = [];
        foreach ($tables as $table) {
            if (! preg_match('/^[a-z_]+$/D', $table)) {
                throw new RuntimeException('Unexpected table');
            }
            $result[$table] = $db->query('SELECT md5(string_agg(row_to_json(t)::text,chr(10) ORDER BY row_to_json(t)::text)) FROM '.$table.' t')->fetchColumn();
        }

        return $result;
    };
    $before = $fingerprint();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/key-correction-proposals.sql'));
    $hash = $db->query('SELECT hash FROM published_value_baselines')->fetchColumn();
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v2/name-inventory.sql'));
    $db->beginTransaction();
    $nameInventoryService = new BaselineNameInventory;
    $nameInventory = $nameInventoryService->capture($db, 'public', $hash);
    $nameInventoryService->store($db, 'public', $hash, $hash, $nameInventory);
    $db->commit();
    $database = new KeyCorrectionDatabase($db, $name, 'public', $hash, $backupResult['backup_manifest_sha256']);
    $service = new KeyCorrectionProposal($database);
    $source = 'ui.type';
    $target = 'rehearsal.correction.type'; // Synthetic proposal only; not an agreed replacement.
    $operation = '00000000-0000-4000-8000-000000000001';
    $preview = $service->preview($hash, $source, $target);
    $check(count($preview['evidence']['sources']) === 387, 'Wrong source impact');
    $check(count($preview['evidence']['published_values']) === 2, 'Wrong value impact');
    $reject = static function (callable $action, string $message) use ($check): void {
        $caught = false;
        try {
            $action();
        } catch (RuntimeException) {
            $caught = true;
        }
        $check($caught, $message);
    };
    // Install the package's full versioned schema separately from the restored baseline.
    $schemaService = new WorkingGenerationSchema;
    $generation = '00000000-0000-4000-8000-000000000101';
    $schema = 'working_generation_v2';
    $previousSearchPath = $db->query('SHOW search_path')->fetchColumn();
    $installed = $schemaService->install($db, $name, $schema, $generation);
    $check($schemaService->verify($db, $name, $schema, $generation) === $installed, 'Installed schema failed verification');
    $check($db->query('SHOW search_path')->fetchColumn() === $previousSearchPath, 'Installation changed caller search path');
    $reject(fn () => $schemaService->install($db, $name, $schema, $generation), 'Existing generation was adopted or overwritten');
    $reject(fn () => $schemaService->install($db, $name, 'public', $generation), 'Default application schema accepted');
    $reject(fn () => $schemaService->install($db, $name.'_wrong', 'wrong_target', $generation), 'Wrong schema installation database accepted');
    $reject(fn () => $schemaService->verify($db, $name, $schema, '00000000-0000-4000-8000-000000000102'), 'Wrong generation identity accepted');
    $reject(fn () => $db->exec("UPDATE $schema.workbench_generation SET version=2"), 'Generation ledger was mutable');
    $constraint = $db->query("SELECT pg_get_constraintdef(oid) FROM pg_constraint
        WHERE conrelid='$schema.translation_workbench_events'::regclass AND conname='translation_workbench_events_actor_id_check'")->fetchColumn();
    $index = $db->query("SELECT pg_get_indexdef('$schema.current_assignment'::regclass)")->fetchColumn();
    $function = $db->query("SELECT pg_get_functiondef('$schema.tw_immutable()'::regprocedure)")->fetchColumn();
    foreach ([
        ["ALTER TABLE $schema.translation_workbench_findings DISABLE TRIGGER immutable_capture", "ALTER TABLE $schema.translation_workbench_findings ENABLE TRIGGER immutable_capture"],
        ["ALTER TABLE $schema.translation_workbench_events DROP CONSTRAINT translation_workbench_events_actor_id_check", "ALTER TABLE $schema.translation_workbench_events ADD CONSTRAINT translation_workbench_events_actor_id_check $constraint"],
        ["DROP INDEX $schema.current_assignment", $index],
        ["ALTER TABLE $schema.translation_workbench_source_files ALTER COLUMN path TYPE varchar(1000)", "ALTER TABLE $schema.translation_workbench_source_files ALTER COLUMN path TYPE text"],
        ["CREATE OR REPLACE FUNCTION $schema.tw_immutable() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RETURN NEW; END'", $function],
    ] as [$changeSchema, $restoreSchema]) {
        try {
            $db->exec($changeSchema);
            $reject(fn () => $schemaService->verify($db, $name, $schema, $generation), 'Changed schema definition was accepted');
        } finally {
            $db->exec($restoreSchema);
        }
        $check($schemaService->verify($db, $name, $schema, $generation) === $installed, 'Restored fixture schema did not match');
    }
    $check($fingerprint() === $before, 'Versioned schema installation changed restored baseline data');
    $reject(fn () => $database->assertReady($hash), 'Target verification accepted no transaction');
    foreach ([
        [$name.'_wrong', 'public', $hash, $backupResult['backup_manifest_sha256']],
        [$name, 'missing_generation', $hash, $backupResult['backup_manifest_sha256']],
        [$name, 'public', str_repeat('0', 64), $backupResult['backup_manifest_sha256']],
        [$name, 'public', $hash, str_repeat('0', 64)],
    ] as [$expectedName, $schema, $expectedBaseline, $expectedBackup]) {
        $wrongDatabase = new KeyCorrectionDatabase($db, $expectedName, $schema, $expectedBaseline, $expectedBackup);
        $reject(fn () => (new KeyCorrectionProposal($wrongDatabase))->preview($hash, $source, $target), 'Wrong generation binding accepted');
        $check(! $db->inTransaction(), 'Rejected target left a transaction open');
    }
    foreach ([
        'ALTER TABLE public.translation_workbench_findings RENAME COLUMN raw_expression TO hidden_expression',
        'ALTER TABLE public.translation_workbench_findings RENAME TO hidden_findings',
        'CREATE TEMP TABLE translation_workbench_findings (id bigint) ON COMMIT DROP',
        'SET LOCAL search_path TO pg_catalog',
    ] as $change) {
        $db->beginTransaction();
        try {
            $db->exec($change);
            $reject(fn () => $service->context($hash, $source, $target), 'Changed or shadowed generation schema accepted');
        } finally {
            $db->rollBack();
        }
    }
    $check($fingerprint() === $before, 'Generation target checks changed baseline data');
    $snapshot = $read($root.'/storage/app/private/workbench-previews/reader-preview.json');
    $check($snapshot['input_dump_sha256'] === $baseline['dump_sha256'], 'Preview belongs to another database generation');
    $evidenceCheck = new BaselineProposalEvidence;
    $databaseMatch = $evidenceCheck->compare($snapshot, $source, $target, $preview);
    $check($databaseMatch['finding_count'] === 387 && $databaseMatch['published_count'] === 2, 'Snapshot and database impact differ');
    $check($fingerprint() === $before, 'Read-only database comparison changed data');
    $preflightReader = new BaselineCorrectionPreflight;
    $readDatabase = fn () => $service->preview($hash, $source, $target);
    $preflight = $preflightReader->inspect($snapshot, $source, $target, $backup.'/source', $readDatabase);
    $check($preflight['source_files'] === 44 && $preflight['source_occurrences'] === 387
        && $preflight['published_values'] === 2, 'Joint preflight impact differs');
    $check($preflight['database_fingerprint'] === $preview['fingerprint'], 'Joint preflight uses another database context');
    $check($preflightReader->inspect($snapshot, $source, $target, $backup.'/source', $readDatabase)['fingerprint'] === $preflight['fingerprint'], 'Identical joint preflight changed');
    $check($fingerprint() === $before, 'Joint preflight changed the database');
    // Prove that the package services work on the newly installed generation, not only the restored DB.
    $transferSchema = 'working_generation_v2';
    $transferService = new SelectedGenerationTransfer;
    $reject(fn () => $transferService->transfer($db, 'public', $transferSchema, $generation, $snapshot, $preview,
        static function (int $count): void {
            if ($count === 2) {
                throw new RuntimeException('Injected transfer failure');
            }
        }),
        'Interrupted transfer did not fail');
    $check((int) $db->query("SELECT count(*) FROM $transferSchema.translation_workbench_findings")->fetchColumn() === 0
        && (int) $db->query("SELECT count(*) FROM $transferSchema.capture_imports")->fetchColumn() === 0,
        'Interrupted transfer left partial captures');
    $transferred = $transferService->transfer($db, 'public', $transferSchema, $generation, $snapshot, $preview);
    $check(count($transferred['finding_map']) === 387 && $transferred['source_files'] === 44, 'Selected transfer lost occurrences or files');
    $check((int) $db->query("SELECT count(*) FROM $transferSchema.translation_workbench_findings n
        JOIN public.translation_workbench_findings o ON o.id=(n.provenance->'transfer'->>'finding_id')::bigint
        WHERE ROW(n.raw_expression,n.raw_source_code_literal,n.raw_literal,n.suggested_literal,n.suggested_key,n.source_line,n.source_column)
            IS NOT DISTINCT FROM ROW(o.raw_expression,o.raw_source_code_literal,o.raw_literal,o.suggested_literal,o.suggested_key,o.source_line,o.source_column)
        AND n.provenance->'original_provenance'=o.provenance")->fetchColumn() === 387,
        'Transfer changed immutable capture fields or lost original provenance');
    $reject(fn () => $transferService->transfer($db, 'public', $transferSchema, $generation, $snapshot, $preview), 'Transfer appended to a populated generation');
    foreach (['translation_workbench_keys', 'translation_workbench_key_findings', 'translation_workbench_events',
        'translation_workbench_reviews', 'translation_workbench_value_revisions', 'translation_workbench_operations'] as $emptyTable) {
        $check((int) $db->query("SELECT count(*) FROM $transferSchema.$emptyTable")->fetchColumn() === 0, 'Transfer carried active decisions or history: '.$emptyTable);
    }
    $check($schemaService->verify($db, $name, $transferSchema, $generation) === $installed, 'Transfer changed versioned schema');
    $check($fingerprint() === $before, 'Transfer changed restored source data');
    $generationDb = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $generationDb->exec("SET search_path TO $transferSchema, pg_catalog, pg_temp");
    $generationBinding = new KeyCorrectionDatabase($generationDb, $name, $transferSchema,
        $transferred['snapshot']['baseline_hash'], $backupResult['backup_manifest_sha256']);
    $generationProposal = new KeyCorrectionProposal($generationBinding);
    $reservedExample = null;
    foreach ($transferred['snapshot']['name_inventory']['names'] as $reservedName) {
        if ($reservedName['namespace'] === '' && substr_count($reservedName['full_name'], '.') >= 3
            && $reservedName['full_name'] !== $source) {
            $reservedExample = $reservedName['full_name'];
            break;
        }
    }
    $check($reservedExample !== null, 'No reserved name outside the selected key to test');
    $parentName = substr($reservedExample, 0, strrpos($reservedExample, '.'));
    $previewNames = new BaselineKeyCorrectionPreview;
    foreach ([$reservedExample, $reservedExample.'.extra', $parentName] as $conflictingName) {
        $reject(fn () => $generationProposal->preview($transferred['snapshot']['baseline_hash'], $source, $conflictingName),
            'Partial generation accepted an occupied name or scalar/array path');
        $check($previewNames->inspect($transferred['snapshot'], $source, $conflictingName)['status'] === 'conflict',
            'Snapshot and database name conflict checks disagree');
    }
    $withoutNames = $transferred['snapshot'];
    unset($withoutNames['name_inventory']);
    $check($previewNames->inspect($withoutNames, $source, $target)['status'] === 'name_inventory_unavailable',
        'Partial snapshot treated missing inventory as free namespace');
    $generationDb->beginTransaction();
    try {
        $generationDb->exec('ALTER TABLE baseline_name_inventories DISABLE TRIGGER immutable_name_inventory');
        $generationDb->exec('DELETE FROM baseline_name_inventories');
        $reject(fn () => $generationProposal->context($transferred['snapshot']['baseline_hash'], $source, $target),
            'Missing database name inventory accepted');
    } finally {
        $generationDb->rollBack();
    }
    $check(count($transferred['snapshot']['name_inventory']['names']) === count($nameInventory['names'])
        && $transferred['snapshot']['name_inventory']['sha256'] === $nameInventory['sha256'],
        'Transfer omitted names outside the selected data');
    $generationContext = $generationProposal->preview($transferred['snapshot']['baseline_hash'], $source, $target);
    $generationPreflight = $preflightReader->inspect($transferred['snapshot'], $source, $target, $backup.'/source',
        fn () => $generationProposal->preview($transferred['snapshot']['baseline_hash'], $source, $target));
    $generationOperation = '00000000-0000-4000-8000-000000000201';
    $generationProposal->record($generationOperation, $transferred['snapshot']['baseline_hash'], $source, $target,
        $generationContext['fingerprint'], 999999, 'Isolated selected generation proposal');
    $generationApplication = new KeyCorrectionApplication($generationBinding, $transferred['snapshot'], $backup.'/source');
    $generationReceipt = $generationApplication->apply('00000000-0000-4000-8000-000000000202', $generationOperation,
        $generationPreflight['fingerprint'], 999999, 'Isolated selected generation application');
    $check(count($generationReceipt['links']) === 387, 'Fresh generation application lost finding links');
    // JSONB object keys have no ordering contract; retain strict value/list comparisons.
    $canonicalReceipt = static function (array $value) use (&$canonicalReceipt): array {
        foreach ($value as &$item) {
            if (is_array($item)) {
                $item = $canonicalReceipt($item);
            }
        }
        unset($item);
        if (! array_is_list($value)) {
            ksort($value);
        }

        return $value;
    };
    $check($canonicalReceipt($generationApplication->apply('00000000-0000-4000-8000-000000000202', $generationOperation,
        $generationPreflight['fingerprint'], 999999, 'Isolated selected generation application')) === $canonicalReceipt($generationReceipt),
        'Fresh generation retry changed receipt');
    $check((int) $generationDb->query('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === 1,
        'Fresh generation history was duplicated');
    (new SelectedGenerationPublication)->verify(
        $generationDb, $cluster, $generationReceipt['key'], $check, $reject);
    $check($fingerprint() === $before, 'Fresh generation application changed source baseline');
    $check($schemaService->verify($db, $name, $transferSchema, $generation) === $installed, 'Application changed generation structure');
    // A separate fresh schema proves the other static capture categories without changing the selected correction case.
    $captureSchema = 'capture_generation_v2';
    $captureGeneration = (string) Str::uuid();
    $captureInstalled = $schemaService->install($db, $name, $captureSchema, $captureGeneration);
    $captureDb = new PDO('pgsql:host='.$cluster.'/socket;dbname='.$name.';user=rehearsal', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $captureDb->exec("SET search_path TO $captureSchema, pg_catalog, pg_temp");
    $captureAccounting = (new FreshGenerationCapture)->verify(
        $captureDb, $root, $backup, $backupResult['backup_manifest_sha256'], $check, $reject);
    $argumentAccounting = (new FreshGenerationArguments)->verify(
        $captureDb, $root, $backup, $check, $reject);
    $publishedAccounting = (new FreshGenerationPublishedValues)->verify(
        $captureDb, $backup, $manifest, $backupResult['backup_manifest_sha256'], $check, $reject);
    $check($schemaService->verify($db, $name, $captureSchema, $captureGeneration) === $captureInstalled,
        'Static capture changed versioned schema');
    $check($fingerprint() === $before, 'Full capture changed restored source baseline');
    $sourceId = (int) $preview['evidence']['sources'][0]['id'];
    $rejectChangedContext = static function (callable $change, string $message) use ($db, $service, $hash, $source, $target, $reject, $check, $fingerprint, $before): void {
        $db->beginTransaction();
        try {
            $change();
            $reject(fn () => $service->context($hash, $source, $target), $message);
        } finally {
            $db->rollBack();
        }
        $check($fingerprint() === $before, 'Database fixture escaped rollback');
    };
    $rejectChangedContext(fn () => $db->exec("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason='Fixture' WHERE id=".$sourceId), 'Inactive source accepted');
    $rejectChangedContext(static function () use ($db, $sourceId): void {
        $key = (int) $db->query('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
        $db->exec('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES ('.$sourceId.','.$key.')');
    }, 'Changed source assignment accepted');
    $rejectChangedContext(fn () => $db->exec('INSERT INTO translation_workbench_findings
        (source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance)
        SELECT source_file_id,captured_scan_id,source_line+100000,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_literal,suggested_key,provenance
        FROM translation_workbench_findings WHERE id='.$sourceId), 'Additional active source without baseline observation accepted');
    foreach (['rehearsal.correction', 'rehearsal.correction.type.child'] as $occupied) {
        $rejectChangedContext(static function () use ($db, $occupied): void {
            $key = (int) $db->query('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
            $db->prepare("INSERT INTO translation_workbench_key_names(key_id,namespace,full_name) VALUES (?,'',?)")->execute([$key, $occupied]);
        }, 'Late parent or child target collision accepted');
    }
    $db->beginTransaction();
    try {
        $db->exec("UPDATE translation_workbench_source_files SET path=path||'.moved' WHERE id=".(int) $preview['evidence']['sources'][0]['source_file_id']);
        $movedContext = $service->context($hash, $source, $target);
        $check($movedContext['fingerprint'] !== $preview['fingerprint'], 'Source path missing from database fingerprint');
        $reject(fn () => $evidenceCheck->compare($snapshot, $source, $target, $movedContext), 'Changed database path accepted against snapshot');
    } finally {
        $db->rollBack();
    }
    $check($fingerprint() === $before, 'Path fixture escaped rollback');
    $reject(fn () => $service->record($operation, $hash, $source, $target, str_repeat('0', 64), 999999, 'Fixture only'), 'Stale context accepted');
    $reject(fn () => $service->record($operation, $hash, $source, $target, $preview['fingerprint'], 999999, ''), 'Missing reason accepted');
    $reject(fn () => $service->record($operation, $hash, $source, $target, $preview['fingerprint'], 0, 'Fixture only'), 'Missing actor accepted');
    $reject(fn () => $service->preview($hash, $source, 'ui.type'), 'Invalid target accepted');
    $reject(fn () => $service->preview($hash, $source, 'ui.states.all'), 'Occupied target accepted');
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_proposals')->fetchColumn() === 0, 'Failed operations left records');
    $result = $service->record($operation, $hash, $source, $target, $preflight['database_fingerprint'], 999999, 'Synthetic database-only rehearsal proposal');
    $check(! $result['replayed'], 'Initial insert replayed');
    $check($service->record($operation, $hash, $source, $target, $preview['fingerprint'], 999999, 'Synthetic database-only rehearsal proposal')['replayed'], 'Retry not recognized');
    $reject(fn () => $service->record($operation, $hash, $source, 'rehearsal.other.type', $preview['fingerprint'], 999999, 'Changed request'), 'Changed retry accepted');
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_proposals')->fetchColumn() === 1, 'Duplicate proposal');
    $stored = $db->query('SELECT context FROM baseline_key_correction_proposals')->fetchColumn();
    $check(json_decode($stored, true, flags: JSON_THROW_ON_ERROR) == $preview['evidence'], 'Recorded evidence differs');
    $reject(fn () => $db->exec('DELETE FROM baseline_key_correction_proposals'), 'Proposal deletion accepted');
    $reject(fn () => $db->exec("UPDATE baseline_key_correction_proposals SET target_key='changed.other.name'"), 'Proposal modification accepted');
    $check($fingerprint() === $before, 'Baseline tables changed');
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/key-correction-applications.sql'));
    // Failure injection must touch a disposable working copy, never the saved backup.
    $confirmationRoot = $work.'/confirmation-source';
    mkdir($confirmationRoot, 0700);
    $referencePaths = array_column($snapshot['items'][$source]['blocked_sources'], 'path');
    foreach ($snapshot['items'][$source]['locales'] as $locale) {
        if (($locale['published'] ?? null) !== null) {
            $referencePaths[] = $locale['published']['path'];
        }
    }
    $referenceHashes = [];
    foreach (array_unique($referencePaths) as $path) {
        $original = realpath($backup.'/source/'.$path);
        if ($original === false || ! str_starts_with($original, realpath($backup.'/source').DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Invalid confirmation fixture path');
        }
        $destination = $confirmationRoot.'/'.$path;
        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0700, true);
        }
        if (! copy($original, $destination)) {
            throw new RuntimeException('Cannot create confirmation fixture');
        }
        $referenceHashes[$path] = hash_file('sha256', $original);
    }
    $application = new KeyCorrectionApplication($database, $snapshot, $confirmationRoot);
    $wrongApplication = new KeyCorrectionApplication(
        new KeyCorrectionDatabase($db, $name.'_wrong', 'public', $hash, $backupResult['backup_manifest_sha256']),
        $snapshot,
        $confirmationRoot,
    );
    $reject(fn () => $wrongApplication->apply('00000000-0000-4000-8000-999999999999', $operation, $preflight['fingerprint'], 999999, 'Reject wrong target'),
        'Application accepted another database target');
    $check(! $db->inTransaction(), 'Application target rejection left a transaction open');
    $applyOperation = '00000000-0000-4000-8000-000000000002';
    $reason = 'Synthetic confirmation for isolated DB test only';
    $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 0, $reason), 'Missing confirmation actor accepted');
    $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, ''), 'Missing confirmation reason accepted');
    $reject(fn () => $application->apply($applyOperation, $operation, str_repeat('0', 64), 999999, $reason), 'Wrong proposal fingerprint accepted');
    $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason,
        static function (int $count): void {
            if ($count === 1) {
                throw new RuntimeException('Injected failure after first link');
            }
        }), 'Injected failure ignored');
    $check($fingerprint() === $before, 'Partial correction survived rollback');
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_applications')->fetchColumn() === 0, 'Failed application left a ledger record');
    $reject(fn () => $application->apply($applyOperation, $operation, $preview['fingerprint'], 999999, $reason), 'Database-only fingerprint bypassed joint confirmation');
    $sourceFixture = $confirmationRoot.'/'.$snapshot['items'][$source]['blocked_sources'][0]['path'];
    $languageFixture = null;
    foreach ($snapshot['items'][$source]['locales'] as $locale) {
        if (($locale['published'] ?? null) !== null) {
            $languageFixture = $confirmationRoot.'/'.$locale['published']['path'];
            break;
        }
    }
    if ($languageFixture === null) {
        throw new RuntimeException('Missing published language fixture');
    }
    foreach ([$sourceFixture, $languageFixture] as $fixture) {
        $originalBytes = file_get_contents($fixture);
        $changedBytes = $originalBytes."\n/* changed confirmation fixture */\n";
        try {
            file_put_contents($fixture, $changedBytes);
            $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason), 'Changed file accepted before confirmation');
            $check($fingerprint() === $before, 'Stale file check left database writes');
            $check(file_get_contents($fixture) === $changedBytes, 'Stale file was silently repaired');
            file_put_contents($fixture, $originalBytes);
            $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason,
                static function (int $count) use ($fixture, $changedBytes): void {
                    if ($count === 1) {
                        file_put_contents($fixture, $changedBytes);
                    }
                }), 'File change during database writes was committed');
            $check($fingerprint() === $before, 'File change during writing left partial assignments');
            $check(file_get_contents($fixture) === $changedBytes, 'Rollback overwrote the changed source or language file');
        } finally {
            file_put_contents($fixture, $originalBytes);
        }
    }
    foreach ($referenceHashes as $path => $expectedHash) {
        $check(hash_file('sha256', $backup.'/source/'.$path) === $expectedHash, 'Original source backup changed');
    }
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_applications')->fetchColumn() === 0, 'Rejected file confirmation left a receipt');
    // A name claimed after the proposal was recorded must be detected again at application time.
    $competingKey = (int) $db->query('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
    $competingName = $db->prepare("INSERT INTO translation_workbench_key_names(key_id,namespace,full_name) VALUES (?,'',?)");
    $competingName->execute([$competingKey, $target]);
    $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason), 'Late target collision accepted');
    $db->exec('DELETE FROM translation_workbench_key_names WHERE key_id='.$competingKey);
    $db->exec('DELETE FROM translation_workbench_keys WHERE id='.$competingKey);
    $check($fingerprint() === $before, 'Late conflict partially applied correction');
    $countsBefore = [];
    foreach (['translation_workbench_keys', 'translation_workbench_key_names', 'translation_workbench_key_findings', 'translation_workbench_events', 'translation_workbench_operations'] as $table) {
        $countsBefore[$table] = (int) $db->query('SELECT count(*) FROM '.$table)->fetchColumn();
    }
    $applied = $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason);
    $check(count($applied['links']) === 387, 'Wrong applied finding count');
    $check($applied['preflight_fingerprint'] === $preflight['fingerprint'], 'Application receipt lost the confirmed preflight');
    $event = $db->prepare('SELECT details FROM translation_workbench_events WHERE operation_id=?');
    $event->execute([$applyOperation]);
    $eventDetails = json_decode($event->fetchColumn(), true, flags: JSON_THROW_ON_ERROR);
    $check($eventDetails['preflight_fingerprint'] === $preflight['fingerprint']
        && $eventDetails['source_fingerprint'] === $preflight['source_fingerprint']
        && $eventDetails['snapshot_sha256'] === $preflight['snapshot_sha256'], 'History lost the confirmed file and snapshot evidence');
    $check(array_column($applied['links'], 'finding') === array_map('intval', array_column($preview['evidence']['sources'], 'id')), 'Findings were merged or substituted');
    foreach ($countsBefore as $table => $count) {
        $expectedIncrease = $table === 'translation_workbench_key_findings' ? 387 : 1;
        $check((int) $db->query('SELECT count(*) FROM '.$table)->fetchColumn() === $count + $expectedIncrease, 'Unexpected correction writes: '.$table);
    }
    $afterApply = $fingerprint();
    $check($application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason) == $applied, 'Retry result differs');
    $check($fingerprint() === $afterApply, 'Retry duplicated writes');
    $retryBytes = file_get_contents($sourceFixture);
    try {
        file_put_contents($sourceFixture, $retryBytes."\n/* after completed operation */\n");
        $check($application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, $reason) == $applied,
            'Completed-operation retry did not return its original receipt');
        $check($fingerprint() === $afterApply, 'Completed-operation retry wrote new assignments');
    } finally {
        file_put_contents($sourceFixture, $retryBytes);
    }
    $reject(fn () => $application->apply($applyOperation, $operation, $preflight['fingerprint'], 999999, 'Changed confirmation'), 'Changed application retry accepted');
    $reject(fn () => $application->apply('00000000-0000-4000-8000-000000000003', $operation, $preflight['fingerprint'], 999999, $reason), 'Proposal applied twice');
    $check((int) $db->query('SELECT count(*) FROM baseline_key_correction_applications')->fetchColumn() === 1, 'Duplicate application');
    $after = $fingerprint();
    $unchanged = array_diff_key($before, $countsBefore);
    $check(array_intersect_key($after, $unchanged) === $unchanged, 'Raw findings, baseline values or other tables changed');
    $event = $db->query("SELECT actor_id,reason,details FROM translation_workbench_events WHERE operation_id='".$applyOperation."'")->fetch(PDO::FETCH_ASSOC);
    $check((int) $event['actor_id'] === 999999 && $event['reason'] === $reason
        && json_decode($event['details'], true, flags: JSON_THROW_ON_ERROR)['key'] === $applied['key'], 'Missing audit trail');
    $reject(fn () => $db->exec('DELETE FROM baseline_key_correction_applications'), 'Application provenance deletion accepted');
    $beforeRead = $fingerprint();
    $reader = new CorrectedKeyValueReader($db);
    $state = $reader->read($hash, $target, ['de-at', 'de', 'en']);
    $check($state['key_id'] === $applied['key'], 'Corrected key identity missing');
    $check($state['effective_published'] === null, 'Working basis appeared as publication');
    $check($state['locales']['de-at']['working_basis'] === null, 'Missing regional evidence was fabricated');
    foreach (['de', 'en'] as $locale) {
        $basis = $state['locales'][$locale]['working_basis'];
        $original = (new BaselineTranslationReader($db))->read($hash, $source, [$locale]);
        $check($basis['value'] === $original['locales'][$locale]['published']['value']
            && $basis['path'] === $original['locales'][$locale]['published']['path']
            && $basis['sha256'] === $original['locales'][$locale]['published']['sha256'], 'Original language evidence differs');
        $check($basis['source_key'] === $source && $basis['application_id'] === $applyOperation
            && $basis['proposal_id'] === $operation && $basis['published_under_requested_key'] === false, 'Correction provenance missing');
        $check($state['locales'][$locale]['published'] === null && $state['locales'][$locale]['editing'] === null, 'Invented publication or revision');
    }
    $unrelated = $reader->read($hash, 'ui.states.all', ['en']);
    $check($unrelated['correction'] === null && $unrelated['locales']['en']['working_basis'] === null
        && $unrelated['effective_published'] !== null, 'Unrelated key inherited corrected values');
    $unknown = $reader->read($hash, 'unknown.nonexistent.key', ['en']);
    $check($unknown['key_id'] === null && $unknown['locales']['en']['working_basis'] === null, 'Unknown key inherited corrected values');
    $reject(fn () => $reader->read($hash, $target, []), 'Empty locale chain accepted');
    $check($fingerprint() === $beforeRead, 'Reading working values wrote data');
    $db->exec(file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/corrected-value-drafts.sql'));
    $importer = new CorrectedValueDraftImporter($db);
    $draftPreview = $importer->preview($applyOperation, 'en');
    $draftOperation = '00000000-0000-4000-8000-000000000004';
    $draftReason = 'Explicit synthetic import of exact source-locale evidence';
    $beforeDraft = $fingerprint();
    $reject(fn () => $importer->preview($applyOperation, 'de-at'), 'Missing locale silently fell back');
    $reject(fn () => $importer->import($draftOperation, $applyOperation, 'en', str_repeat('0', 64), 999999, $draftReason), 'Stale import accepted');
    $reject(fn () => $importer->import($draftOperation, $applyOperation, 'en', $draftPreview['fingerprint'], 0, $draftReason), 'Missing import actor accepted');
    $reject(fn () => $importer->import($draftOperation, $applyOperation, 'en', $draftPreview['fingerprint'], 999999, ''), 'Missing import reason accepted');
    $reject(fn () => $importer->import($draftOperation, $applyOperation, 'en', $draftPreview['fingerprint'], 999999, $draftReason,
        static function (): void {
            throw new RuntimeException('Injected failure after revision');
        }), 'Injected import failure ignored');
    $check($fingerprint() === $beforeDraft && (int) $db->query('SELECT count(*) FROM baseline_value_draft_imports')->fetchColumn() === 0, 'Failed import left writes');
    $draft = $importer->import($draftOperation, $applyOperation, 'en', $draftPreview['fingerprint'], 999999, $draftReason);
    $afterDraft = $fingerprint();
    $check($importer->import($draftOperation, $applyOperation, 'en', $draftPreview['fingerprint'], 999999, $draftReason) == $draft
        && $fingerprint() === $afterDraft, 'Import retry duplicated writes');
    $updatedPreview = $importer->preview($applyOperation, 'en');
    $reject(fn () => $importer->import('00000000-0000-4000-8000-000000000005', $applyOperation, 'en', $updatedPreview['fingerprint'], 999999, $draftReason), 'Existing working revision overwritten');
    $draftState = $reader->read($hash, $target, ['de-at', 'de', 'en']);
    $check($draftState['locales']['en']['editing']['value'] === $draftPreview['evidence']['value']
        && $draftState['locales']['en']['editing']['approval'] === 'unreviewed', 'Imported draft not unreviewed or differs');
    $check($draftState['effective_published'] === null && $draftState['locales']['de']['editing'] === null, 'Import published or affected another locale');
    $origin = json_decode($db->query('SELECT evidence FROM baseline_value_draft_imports')->fetchColumn(), true, flags: JSON_THROW_ON_ERROR);
    $check($origin == $draftPreview['evidence'], 'Import origin lost');
    $unchanged = array_diff_key($unchanged, array_flip(['translation_workbench_value_slots', 'translation_workbench_value_revisions']));
    $check(array_intersect_key($fingerprint(), $unchanged) === $unchanged, 'Import changed baseline/raw/review tables');
    // Reuse the review contract also consulted by the existing publication writer.
    $reviewer = new BaselineRevisionReviewer($db);
    $revision = $draft['revision'];
    $reviewPreview = RevisionReviewContext::snapshot($db, $revision);
    $reviewOperation = '00000000-0000-4000-8000-000000000006';
    $reviewReason = 'Explicit review of imported baseline value on isolated copy';
    $beforeReview = $fingerprint();
    $check(! $reviewer->approvalIsCurrent($revision), 'Unreviewed import became publishable');
    $reject(fn () => $reviewer->decide($reviewOperation, $revision, str_repeat('0', 64), true, 999999, $reviewReason), 'Stale review accepted');
    $reject(fn () => $reviewer->decide($reviewOperation, $revision, $reviewPreview['fingerprint'], true, 0, $reviewReason), 'Missing reviewer accepted');
    $reject(fn () => $reviewer->decide($reviewOperation, $revision, $reviewPreview['fingerprint'], true, 999999, ''), 'Missing review reason accepted');
    $check($fingerprint() === $beforeReview, 'Rejected review wrote data');
    $decision = $reviewer->decide($reviewOperation, $revision, $reviewPreview['fingerprint'], true, 999999, $reviewReason);
    $check($reviewer->approvalIsCurrent($revision), 'Explicit approval not current');
    $afterReview = $fingerprint();
    $check($reviewer->decide($reviewOperation, $revision, $reviewPreview['fingerprint'], true, 999999, $reviewReason) === $decision
        && $fingerprint() === $afterReview, 'Review retry duplicated writes');
    $approved = $reader->read($hash, $target, ['en']);
    $check($approved['locales']['en']['editing']['approval'] === 'approved'
        && $approved['effective_published'] === null, 'Approval silently published value');
    // A changed usage invalidates the old approval; reverting the test transaction restores it.
    $db->beginTransaction();
    $db->exec('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE id='.(int) $applied['links'][0]['link']);
    $check(! $reviewer->approvalIsCurrent($revision), 'Changed usage retained approval');
    $db->rollBack();
    $check($reviewer->approvalIsCurrent($revision), 'Rollback did not restore approval context');
    $revokePreview = RevisionReviewContext::snapshot($db, $revision);
    $reviewer->decide('00000000-0000-4000-8000-000000000007', $revision, $revokePreview['fingerprint'], false, 999999, 'Revoke synthetic approval');
    $check(! $reviewer->approvalIsCurrent($revision), 'Revocation left value publishable');
    $revoked = $reader->read($hash, $target, ['en']);
    $check($revoked['locales']['en']['editing']['approval'] === 'revoked'
        && $revoked['effective_published'] === null, 'Revocation read state incorrect');
    $check(json_decode($db->query('SELECT evidence FROM baseline_value_draft_imports')->fetchColumn(), true, flags: JSON_THROW_ON_ERROR) == $origin, 'Review changed import origin');
    $unchanged = array_diff_key($unchanged, ['translation_workbench_reviews' => true]);
    $check(array_intersect_key($fingerprint(), $unchanged) === $unchanged, 'Review changed raw or baseline data');
    $publisher = new PostgresPublisher($cluster, $db);
    $exportPath = $publisher->directory.'/en/rehearsal.php';
    $exportRun = '00000000-0000-4000-8000-000000000008';
    $beforeExport = $fingerprint();
    $reject(fn () => new PostgresPublisher($root, $db), 'Publication accepted project directory');
    $reject(fn () => $publisher->prepare($exportRun, [$revision], 999999, 'Export revoked draft'), 'Revoked draft export prepared');
    $check(! file_exists($exportPath) && $fingerprint() === $beforeExport, 'Rejected export wrote data');
    $reviewer->decide('00000000-0000-4000-8000-000000000009', $revision, RevisionReviewContext::snapshot($db, $revision)['fingerprint'], true, 999999, 'Approve isolated export');
    $plan = $publisher->prepare($exportRun, [$revision], 999999, 'Export imported value to temporary language file');
    $check(! file_exists($exportPath), 'Preparation wrote language file');
    $reviewer->decide('00000000-0000-4000-8000-000000000010', $revision, RevisionReviewContext::snapshot($db, $revision)['fingerprint'], false, 999999, 'Withdraw before writing');
    $reject(fn () => $publisher->publish($exportRun, $plan['fingerprint']), 'Late revocation ignored');
    $check(! file_exists($exportPath), 'Late revocation still wrote file');
    $closure = $publisher->closurePreview($exportRun);
    $publisher->close($exportRun, $closure['fingerprint'], 999999, 'Close withdrawn export');
    $reviewer->decide('00000000-0000-4000-8000-000000000011', $revision, RevisionReviewContext::snapshot($db, $revision)['fingerprint'], true, 999999, 'Approve new export attempt');
    $exportRun = '00000000-0000-4000-8000-000000000012';
    $plan = $publisher->prepare($exportRun, [$revision], 999999, 'Confirmed isolated export');
    $reject(fn () => $publisher->publish($exportRun, str_repeat('0', 64)), 'Wrong publication fingerprint accepted');
    $check(! file_exists($exportPath), 'Wrong fingerprint wrote file');
    $reject(fn () => $publisher->publish($exportRun, $plan['fingerprint'], static function (): void {
        throw new RuntimeException('Injected interruption after file write');
    }), 'Injected publication interruption ignored');
    $check(is_file($exportPath) && (int) $db->query("SELECT count(*) FROM translation_workbench_publication_outcomes WHERE kind='completed'")->fetchColumn() === 0, 'Interrupted export claimed completion');
    $receipt = $publisher->publish($exportRun, $plan['fingerprint']);
    $tree = (new BaselineValueReader)->treeFromBytes(file_get_contents($exportPath));
    $check($tree === ['correction' => ['type' => $draftPreview['evidence']['value']]], 'Exported nested language value differs');
    $check($receipt['kind'] === 'completed', 'Missing publication receipt');
    $afterExport = $fingerprint();
    $fileHash = hash_file('sha256', $exportPath);
    $check($publisher->publish($exportRun, $plan['fingerprint']) === $receipt
        && $fingerprint() === $afterExport && hash_file('sha256', $exportPath) === $fileHash, 'Publication retry changed state');
    $check(json_decode($db->query('SELECT evidence FROM baseline_value_draft_imports')->fetchColumn(), true, flags: JSON_THROW_ON_ERROR) == $origin, 'Publication changed draft provenance');
    $unchanged = array_diff_key($unchanged, array_flip(['translation_workbench_publication_runs', 'translation_workbench_publication_files', 'translation_workbench_publication_outcomes', 'translation_workbench_file_reservations']));
    $check(array_intersect_key($fingerprint(), $unchanged) === $unchanged, 'Publication changed original evidence');
    $publishedReader = new PublishedRevisionReader($db, $publisher->directory);
    $beforeReadback = $fingerprint();
    $readback = $publishedReader->read($exportRun, $revision);
    $check($readback['value'] === $draftPreview['evidence']['value'] && $readback['key'] === $target
        && $readback['locale'] === 'en' && $readback['sha256'] === $fileHash, 'Readback differs from exported revision');
    $check($readback['origin'] == $origin, 'Readback lost import origin');
    $reject(fn () => $publishedReader->read('00000000-0000-4000-8000-000000000008', $revision), 'Closed export treated as completed');
    $reject(fn () => $publishedReader->read($exportRun, $revision + 1000000), 'Unpublished revision returned');
    $exportBytes = file_get_contents($exportPath);
    try {
        file_put_contents($exportPath, $exportBytes."\n// external change\n");
        $reject(fn () => $publishedReader->read($exportRun, $revision), 'File drift ignored');
        unlink($exportPath);
        $reject(fn () => $publishedReader->read($exportRun, $revision), 'Missing export ignored');
    } finally {
        file_put_contents($exportPath, $exportBytes);
    }
    $check($publishedReader->read($exportRun, $revision) === $readback, 'Restored file readback differs');
    $check($fingerprint() === $beforeReadback, 'Publication readback wrote database data');
    $check($reader->read($hash, $target, ['en'])['effective_published'] === null, 'Original baseline was replaced');
    $paths = $db->query('SELECT id,path FROM translation_workbench_source_files')->fetchAll(PDO::FETCH_KEY_PAIR);
    $byPath = [];
    foreach ($preview['evidence']['sources'] as $finding) {
        $byPath[$paths[$finding['source_file_id']]][] = $finding;
    }
    $sourceChanges = [];
    $plannedFindings = 0;
    $beforeSourcePreview = $fingerprint();
    foreach ($byPath as $path => $findings) {
        $copiedPath = realpath($backup.'/source/'.$path);
        $sourceRoot = realpath($backup.'/source');
        $check($copiedPath !== false && str_starts_with($copiedPath, $sourceRoot.'/'), 'Source outside backup');
        $sourceHash = $manifest['source_files'][$path]['sha256'];
        $change = (new SourceKeyChangePreview)->preview(file_get_contents($copiedPath), $sourceHash, $path, $findings, $source, $target);
        $check(hash_file('sha256', $copiedPath) === $sourceHash, 'Preview modified copied source');
        $plannedFindings += count($change['edits']);
        unset($change['proposed_bytes']);
        $sourceChanges[] = $change;
    }
    $check($plannedFindings === count($applied['links']), 'Source preview missed findings');
    $check($fingerprint() === $beforeSourcePreview, 'Source preview changed raw findings or database');
    file_put_contents($work.'/source-change-preview.json', json_encode($sourceChanges, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
    $sourceApplication = $read(__DIR__.'/source-key-application-2026-10-20.json');
    $sourceWork = realpath($root.'/'.$sourceApplication['work_directory']);
    $check($sourceWork !== false && str_starts_with($sourceWork, realpath($backup).'/source-apply-'), 'Unknown source application directory');
    $check(hash_file('sha256', $sourceWork.'/transitions.json') === $sourceApplication['transitions_sha256'], 'Rescan evidence changed');
    $transitions = $read($sourceWork.'/transitions.json');
    $actualIds = array_map('intval', array_column($transitions, 'predecessor'));
    $expectedIds = array_map('intval', array_column($preview['evidence']['sources'], 'id'));
    sort($actualIds);
    sort($expectedIds);
    $check($actualIds === $expectedIds, 'Rescan does not cover exact original findings');
    foreach ($sourceChanges as $change) {
        $check(hash_file('sha256', $sourceWork.'/'.$change['path']) === $change['after_sha256'], 'Rescan source differs from confirmed preview');
    }
    $reconciler = new CorrectedSourceReconciler($db);
    $reconcileOperation = '00000000-0000-4000-8000-000000000013';
    $beforeReconcile = $fingerprint();
    $reject(fn () => $reconciler->apply($reconcileOperation, $applied['key'], $transitions, 0, 'Invalid actor'), 'Missing reconciliation actor accepted');
    $duplicateTransitions = [...$transitions, $transitions[0]];
    $reject(fn () => $reconciler->apply($reconcileOperation, $applied['key'], $duplicateTransitions, 999999, 'Duplicate evidence'), 'Duplicate predecessor accepted');
    $changedTransitions = $transitions;
    $changedTransitions[0]['before_expression'] = 'changed expression';
    $reject(fn () => $reconciler->apply($reconcileOperation, $applied['key'], $changedTransitions, 999999, 'Stale evidence'), 'Stale predecessor accepted');
    $reject(fn () => $reconciler->apply($reconcileOperation, $applied['key'], $transitions, 999999, 'Confirmed working-copy rewrite', static function (): void {
        throw new RuntimeException('Injected failure after successor');
    }), 'Reconciliation ignored injected failure');
    $check($fingerprint() === $beforeReconcile, 'Partial reconciliation survived rollback');
    $reconciled = $reconciler->apply($reconcileOperation, $applied['key'], $transitions, 999999, 'Confirmed working-copy rewrite');
    $check(count($reconciled['successors']) === 387, 'Missing successor findings');
    foreach ($reconciled['successors'] as $pair) {
        $old = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$pair['predecessor'])->fetch(PDO::FETCH_ASSOC);
        $new = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$pair['successor'])->fetch(PDO::FETCH_ASSOC);
        $check($old['status'] === 'obsolete' && $new['status'] === 'active' && (int) $new['predecessor_id'] === $pair['predecessor']
            && $old['raw_source_code_literal'] === $source && $new['raw_source_code_literal'] === $target
            && $old['suggested_key'] === $new['suggested_key'], 'History or raw evidence lost');
        $check((int) $db->query('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id='.$pair['successor'].' AND valid_to IS NULL')->fetchColumn() === $applied['key']
            && $db->query('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id='.$pair['predecessor'].' AND valid_to IS NULL')->fetchColumn() === false, 'Wrong active assignment');
    }
    $afterReconcile = $fingerprint();
    $check($reconciler->apply($reconcileOperation, $applied['key'], $transitions, 999999, 'Confirmed working-copy rewrite') == $reconciled
        && $fingerprint() === $afterReconcile, 'Retry duplicated reconciliation');
    $check($reconciler->apply('00000000-0000-4000-8000-000000000014', $applied['key'], $transitions, 999999, 'Repeated identical rescan') == $reconciled, 'Identical rescan failed');
    $check(array_diff_key($fingerprint(), ['translation_workbench_operations' => true]) === array_diff_key($afterReconcile, ['translation_workbench_operations' => true]), 'Identical rescan created findings or events');
    $reject(fn () => $reconciler->apply('00000000-0000-4000-8000-000000000015', $applied['key'], $changedTransitions, 999999, 'Changed repeated evidence'), 'Changed repeated rescan accepted');
    $check(! $reviewer->approvalIsCurrent($revision), 'Changed finding context retained previous approval');
    $check($publishedReader->read($exportRun, $revision) === $readback, 'Historical publication evidence changed');
    $unchanged = array_diff_key($unchanged, array_flip(['translation_workbench_findings', 'translation_workbench_scan_runs', 'translation_workbench_scan_findings']));
    $check(array_intersect_key($fingerprint(), $unchanged) === $unchanged, 'Reconciliation changed unrelated baseline tables');
    $check(hash_file('sha256', $sourceWork.'/movements.json') === $sourceApplication['movements_sha256'], 'Movement evidence changed');
    $movements = $read($sourceWork.'/movements.json');
    $movementGroups = [];
    $movementOriginals = [];
    foreach ($movements as $movement) {
        $oldCapture = $movement['before'];
        $query = $db->prepare("SELECT f.*,l.key_id FROM translation_workbench_findings f JOIN translation_workbench_source_files s ON s.id=f.source_file_id LEFT JOIN translation_workbench_key_findings l ON l.finding_id=f.id AND l.valid_to IS NULL WHERE f.status='active' AND s.path=? AND f.source_line=? AND f.source_column=? AND f.raw_expression=?");
        $query->execute([$oldCapture['source_path'], $oldCapture['source_line'], $oldCapture['source_column'], $oldCapture['raw_expression']]);
        $matches = $query->fetchAll(PDO::FETCH_ASSOC);
        $check(count($matches) === 1, 'Moved call has no unique active finding');
        $old = $matches[0];
        $movementOriginals[$old['id']] = $old;
        $movementGroups[$old['key_id'] ?? 'unlinked'][] = ['predecessor' => (int) $old['id'], 'before_expression' => $old['raw_expression'], 'capture' => $movement['after']];
    }
    $movedCount = 0;
    $movementIndex = 20;
    foreach ($movementGroups as $keyId => $group) {
        $keyId = $keyId === 'unlinked' ? null : (int) $keyId;
        $movementOperation = sprintf('00000000-0000-4000-8000-%012d', $movementIndex++);
        $beforeMovement = $fingerprint();
        $reject(fn () => $reconciler->apply($movementOperation, $keyId, $group, 999999, 'Verified position shift', static function (): void {
            throw new RuntimeException('Injected movement failure');
        }, true), 'Movement failure ignored');
        $check($fingerprint() === $beforeMovement, 'Movement rollback left changes');
        $moved = $reconciler->apply($movementOperation, $keyId, $group, 999999, 'Verified position shift', null, true);
        foreach ($moved['successors'] as $pair) {
            $old = $movementOriginals[$pair['predecessor']];
            $new = $db->query('SELECT * FROM translation_workbench_findings WHERE id='.$pair['successor'])->fetch(PDO::FETCH_ASSOC);
            $check($new['raw_expression'] === $old['raw_expression'] && $new['raw_literal'] === $old['raw_literal']
                && $new['suggested_key'] === $old['suggested_key'] && $new['suggested_literal'] === $old['suggested_literal']
                && (int) $new['predecessor_id'] === $pair['predecessor'], 'Movement altered original content');
            $link = $db->query('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id='.$pair['successor'].' AND valid_to IS NULL')->fetchColumn();
            $check(($link === false ? null : (int) $link) === $keyId, 'Movement invented or lost key link');
            $movedCount++;
        }
        $afterMovement = $fingerprint();
        $check($reconciler->apply($movementOperation, $keyId, $group, 999999, 'Verified position shift', null, true) == $moved
            && $fingerprint() === $afterMovement, 'Movement retry changed data');
        $repeat = sprintf('00000000-0000-4000-8000-%012d', $movementIndex++);
        $check($reconciler->apply($repeat, $keyId, $group, 999999, 'Identical movement rescan', null, true) == $moved
            && array_diff_key($fingerprint(), ['translation_workbench_operations' => true]) === array_diff_key($afterMovement, ['translation_workbench_operations' => true]), 'Repeated movement rescan duplicated data');
    }
    $check($movedCount === count($movements), 'Not all positional shifts reconciled');
    $check(array_intersect_key($fingerprint(), $unchanged) === $unchanged, 'Movement changed unrelated tables');
    $report = ['state' => 'isolated_correction_application_verified', 'recorded_at' => gmdate('c'), 'rehearsal_cluster' => $cluster, 'production_connected' => false, 'assertions' => $checks,
        'unchanged_tables' => count($unchanged), 'corrected_values_read_only' => true, 'explicit_draft_import_verified' => true, 'explicit_review_cycle_verified' => true, 'imported_draft_file_export_verified' => true, 'publication_readback_verified' => true, 'source_change_preview_verified' => true, 'source_reconciliation_verified' => true, 'position_movements_verified' => true, 'position_movements' => $movedCount,
        'database_snapshot_comparison_verified' => true, 'database_snapshot_findings' => $databaseMatch['finding_count'],
        'explicit_database_binding_verified' => true,
        'versioned_schema_verified' => true, 'generation_schema_version' => WorkingGenerationSchema::VERSION,
        'selected_generation_transfer_verified' => true, 'selected_generation_findings' => count($transferred['finding_map']),
        'selected_generation_application_verified' => true,
        'selected_generation_publication_verified' => true,
        'fresh_generation_capture_verified' => true,
        'fresh_generation_capture' => $captureAccounting,
        'fresh_generation_arguments_verified' => true,
        'fresh_generation_arguments' => $argumentAccounting,
        'fresh_generation_published_values_verified' => true,
        'fresh_generation_published_values' => $publishedAccounting,
        'complete_name_inventory_verified' => true, 'reserved_name_count' => $nameInventory['count'],
        'combined_preflight_verified' => true, 'combined_preflight_fingerprint' => $preflight['fingerprint'],
        'confirmed_preflight_application_verified' => true,
        'source_change_files' => count($sourceChanges), 'source_change_findings' => $plannedFindings,
        'temporary_export_path' => $exportPath, 'temporary_export_sha256' => $fileHash, 'source_occurrences' => count($preview['evidence']['sources']),
        'published_values' => count($preview['evidence']['published_values']), 'synthetic_actor' => 999999,
        'synthetic_target' => $target, 'correction_applied' => 'isolated_database_only', 'input_dump_sha256' => $baseline['dump_sha256']];
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $cluster.'/data', '-m', 'fast', '-w', 'stop']);
    }
}
$report['cluster_stopped'] = true;
file_put_contents(__DIR__.'/key-correction-application-2026-10-19.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
// Publish a web-readable snapshot separately from the private audit evidence.
$previewDirectory = $root.'/storage/app/private/workbench-previews';
if (! is_dir($previewDirectory)) {
    mkdir($previewDirectory, 0755, true);
    chmod($previewDirectory, 0755);
}
$temporary = tempnam($previewDirectory, '.workflow-rehearsal-');
try {
    $bytes = json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
    if (file_put_contents($temporary, $bytes) !== strlen($bytes) || ! chmod($temporary, 0644)
        || ! rename($temporary, $previewDirectory.'/workflow-rehearsal.json')) {
        throw new RuntimeException('Cannot publish workflow rehearsal snapshot');
    }
} finally {
    if (is_file($temporary)) {
        unlink($temporary);
    }
}
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
