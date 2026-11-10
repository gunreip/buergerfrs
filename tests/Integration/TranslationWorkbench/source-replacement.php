<?php

use Gunreip\TranslationWorkbench\Generation\CopiedSourceCapture;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementPreview;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementWriter;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
new Application($root);
$bin = '/usr/lib/postgresql/18/bin';
$dir = sys_get_temp_dir().'/tw-source-write-'.bin2hex(random_bytes(8));
mkdir($dir, 0700);
mkdir($dir.'/socket', 0700);
$runProcess = static fn ($cmd) => (new Process($cmd))->mustRun();
$checks = 0;
$started = false;
$check = static function ($ok, string $message) use (&$checks) {
    if (! $ok) {
        throw new RuntimeException($message);
    } $checks++;
};
$reject = static function (callable $action, string $message) use ($check) {
    try {
        $action();
    } catch (RuntimeException $e) {
        $check(str_contains($e->getMessage(), $message), 'Unexpected rejection: '.$e->getMessage());

        return;
    }
    throw new RuntimeException('Expected rejection: '.$message);
};
try {
    $runProcess([$bin.'/initdb', '-D', $dir.'/data', '-U', 'tester', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($dir.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$dir."/socket'\n");
    $runProcess([$bin.'/pg_ctl', '-D', $dir.'/data', '-l', $dir.'/log', '-w', 'start']);
    $started = true;
    $runProcess([$bin.'/createdb', '-h', $dir.'/socket', '-U', 'tester', 'editor']);
    $db = new class('pgsql:host='.$dir.'/socket;dbname=editor;user=tester', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]) extends PDO {
        public array $preparedStatements = [];
        public bool $measureStatements = false;

        public function prepare(string $query, array $options = []): PDOStatement|false
        {
            if ($this->measureStatements) {
                $this->preparedStatements[$query] = ($this->preparedStatements[$query] ?? 0) + 1;
            }
            return parent::prepare($query, $options);
        }
    };
    (new WorkingGenerationSchema)->install($db, 'editor', 'current_generation_v2', (string) Str::uuid());
    // Recreate a genuine v2 structure in an isolated schema and upgrade it without data writes.
    $schemaManager = new WorkingGenerationSchema;
    $legacyGeneration = (string) Str::uuid();
    $schemaManager->install($db, 'editor', 'upgrade_fixture', $legacyGeneration);
    $db->exec('SET search_path TO upgrade_fixture,pg_catalog');
    $baselineSql = file_get_contents($root.'/packages/gunreip/laravel-translation-workbench/database/generations/v1/baseline.sql');
    preg_match('/CREATE FUNCTION tw_capture_guard\(\).*?END \$\$;/s', $baselineSql, $guard);
    $check(isset($guard[0]), 'Original capture guard not found');
    $db->exec(str_replace('CREATE FUNCTION', 'CREATE OR REPLACE FUNCTION', $guard[0]));
    $db->exec('SET search_path TO pg_catalog');
    $hashMethod = new ReflectionMethod(WorkingGenerationSchema::class, 'catalogHash');
    $hash = $hashMethod->invoke($schemaManager, $db, 'upgrade_fixture');
    $db->exec('ALTER TABLE upgrade_fixture.workbench_generation DISABLE TRIGGER immutable_generation');
    $stmt = $db->prepare("UPDATE upgrade_fixture.workbench_generation SET version=2, manifest=manifest-'v3/suggested-key-initialization.sql',catalog_sha256=?");
    $stmt->execute([$hash]);
    $db->exec('ALTER TABLE upgrade_fixture.workbench_generation ENABLE TRIGGER immutable_generation');
    $check($schemaManager->verify($db, 'editor', 'upgrade_fixture', $legacyGeneration)['version'] === 2, 'Old schema is no longer readable');
    $check($schemaManager->upgrade($db, 'editor', 'upgrade_fixture', $legacyGeneration)['changed'], 'Schema upgrade did not run');
    $check($schemaManager->verify($db, 'editor', 'upgrade_fixture', $legacyGeneration)['version'] === 3, 'Upgraded catalog verification failed');
    $check(!$schemaManager->upgrade($db, 'editor', 'upgrade_fixture', $legacyGeneration)['changed'], 'Repeated upgrade changed schema');
    $db->exec('CREATE TABLE public.translation_workbench_findings(id int PRIMARY KEY); INSERT INTO public.translation_workbench_findings VALUES(777)');
    $db->exec('SET search_path TO current_generation_v2,pg_catalog');
    $q = static function ($sql, $params = []) use ($db) {
        $s = $db->prepare($sql);
        $s->execute($params);

        return $s;
    };
    $app = new Application($dir);
    $app->instance('config', new \Illuminate\Config\Repository(['translation-workbench' => ['allowed_first_translation_key_segments' => ['ui', 'admin']]]));
    $app->instance('translator', new \Illuminate\Translation\Translator(new \Illuminate\Translation\ArrayLoader, 'en'));
    $app->instance('files', new \Illuminate\Filesystem\Filesystem);
    \Illuminate\Support\Facades\Facade::setFacadeApplication($app);
    $scanner = $app->make(TranslationWorkbenchScanner::class);
    mkdir($dir.'/lang/en', 0700, true);
    file_put_contents($dir.'/lang/en/ui.php', "<?php return ['button' => ['save' => 'Save']];");
    $scan = (string) Str::uuid();
    $q("INSERT INTO translation_workbench_scan_runs VALUES(?,'completed','{}',now())", [$scan]);
    $editor = new GenerationEditor($db);
    $writer = new SourceReplacementWriter($db, $dir, $dir.'/lang', $scanner);
    $make = function ($name, $extra = '', $expression = "__('Save')") use ($dir, $q, $scan, $editor) {
        $bytes = "<?php\n".$expression.';'.$extra."\n";
        file_put_contents($dir.'/'.$name, $bytes);
        chmod($dir.'/'.$name, 0640);
        $file = $q('INSERT INTO translation_workbench_source_files(path) VALUES(?) RETURNING id', [$name])->fetchColumn();
        $id = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance) VALUES(?,?,2,1,?,?,?,'immutable.suggested.key','{}') RETURNING id", [$file, $scan, $expression, 'Save', 'Save'])->fetchColumn();
        $key = $q("SELECT key_id FROM translation_workbench_key_names WHERE full_name='ui.button.save'")->fetchColumn();
        $link = $editor->assign($id, $key ?: 0, null, 7, 'Assign explicit key', $key ? null : 'ui.button.save');
        $context = $editor->inspect($id, 'en');
        if (! $context['revision']) {
            $editor->save($id, $link['key'], $link['link'], 'en', 'Save', null, 7, 'Source draft');
        }
        $context = $editor->inspect($id, 'en');
        $editor->review($context['revision']['id'], $context['review']['fingerprint'], true, 7, 'Review source');

        return [$id, $file, $bytes];
    };
    $preview = fn ($id) => (new SourceReplacementPreview)->inspect($editor->inspect($id, 'en'), 'en', $dir, $dir.'/lang');
    [$id, $file, $original] = $make('one.php');
    $raw = $editor->inspect($id, 'en')['finding'];
    $run = $writer->prepare($id, 'en', $preview($id)['fingerprint'], 7, 'Replace this source call');
    $check(file_get_contents($dir.'/one.php') === $original, 'Preparation wrote source');
    $check($writer->inspect($run)['state'] === 'pending', 'Prepared plan not durable');
    $reject(fn () => $writer->prepare($id, 'en', $preview($id)['fingerprint'], 7, 'Duplicate plan'), 'open source replacement');
    $reject(fn () => $writer->apply($run, 8), 'preparing user');
    $reject(fn () => $writer->apply($run, 7, function () {
        throw new RuntimeException('Interrupted after rename');
    }), 'Interrupted after rename');
    $check($writer->inspect($run)['file_state'] === 'written', 'Missing written state after failed transaction');
    $check($editor->inspect($id, 'en')['finding']['status'] === 'active', 'Failed transaction archived finding');
    $reject(fn () => $writer->close($run, 7, 'Cancel'), 'not in its original state');
    $resumedDb = new PDO('pgsql:host='.$dir.'/socket;dbname=editor;user=tester', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $resumedDb->exec('SET search_path TO current_generation_v2,pg_catalog');
    $resumedWriter = new SourceReplacementWriter($resumedDb, $dir, $dir.'/lang', $scanner);
    $result = $resumedWriter->apply($run, 7, function () {
        throw new RuntimeException('Rewrote completed file');
    });
    $successor = $result['successors'][0]['successor'];
    $check($writer->inspect($run)['state'] === 'complete', 'No complete receipt');
    $check($editor->inspect($successor, 'en')['review']['approved'], 'Single replacement invalidated source approval');
    $old = $editor->inspect($id, 'en')['finding'];
    $new = $editor->inspect($successor, 'en')['finding'];
    $check($old['status'] === 'obsolete' && $old['raw_expression'] === $raw['raw_expression'], 'Original capture not preserved');
    $check($new['predecessor_id'] === $id && $new['key_id'] === $raw['key_id'], 'Wrong successor link');
    $check($new['suggested_key'] === $raw['suggested_key'] && $new['raw_literal'] === null, 'Immutable suggestion or raw literal corrupted');
    $check($new['raw_source_code_literal'] === 'ui.button.save' && $new['suggested_literal'] === 'Save', 'Successor capture not derived by scanner');
    $check(file_get_contents($dir.'/one.php') === str_replace("__('Save')", "__('ui.button.save')", $original), 'Source contents differ');
    clearstatcache(true, $dir.'/one.php');
    $check((fileperms($dir.'/one.php') & 0777) === 0640, 'Source mode changed');
    $count = $q('SELECT count(*) FROM translation_workbench_findings')->fetchColumn();
    $check($writer->apply($run, 7) == $result && $q('SELECT count(*) FROM translation_workbench_findings')->fetchColumn() === $count, 'Retry duplicated successor');
    $check($q("SELECT count(*) FROM translation_workbench_events WHERE event_type='source_rewrite_reconciled'")->fetchColumn() == 1, 'Duplicate history');
    [$next] = $make('two.php');
    $run2 = $writer->prepare($next, 'en', $preview($next)['fingerprint'], 7, 'Second source');
    $writer->close($run2, 7, 'Leave source unchanged');
    $check($writer->inspect($run2)['state'] === 'closed', 'Closure not recorded');
    $reject(fn () => $writer->apply($run2, 7), 'Operation request changed');
    $run3 = $writer->prepare($next, 'en', $preview($next)['fingerprint'], 7, 'New plan');
    file_put_contents($dir.'/two.php', "<?php // external change\n");
    $reject(fn () => $writer->apply($run3, 7), 'changed outside');
    $check($editor->inspect($next, 'en')['finding']['status'] === 'active', 'External conflict changed database');
    [$third, $thirdFile] = $make('three.php', " __('Another');");
    $other = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance) VALUES(?,?,2,13,?,'Another','Another','original.other.key','{}') RETURNING id", [$thirdFile, $scan, "__('Another')"])->fetchColumn();
    $context = $editor->inspect($third, 'en');
    $editor->review($context['revision']['id'], $context['review']['fingerprint'], true, 7, 'Review source set');
    $run = $writer->prepare($third, 'en', $preview($third)['fingerprint'], 7, 'Shift another finding');
    $check(count($writer->inspect($run)['impacts']) === 2, 'Movement missing from confirmation');
    $result = $writer->apply($run, 7);
    $check(count($result['successors']) === 2, 'Same-line successor missing');
    $moved = $editor->inspect($result['successors'][1]['successor'], 'en')['finding'];
    $check($moved['key_id'] === null && $moved['suggested_key'] === 'original.other.key' && $moved['raw_literal'] === 'Another', 'Movement changed assignment or immutable evidence');
    $check((int) $moved['source_column'] === 23, 'Wrong same-line position');
    $check($writer->apply($run, 7) == $result, 'Batch retry duplicated movements');
    [$multi] = $make('multiline.php', '', "__(\n    'Save'\n)");
    $run = $writer->prepare($multi, 'en', $preview($multi)['fingerprint'], 7, 'Multiline call');
    $result = $writer->apply($run, 7);
    $check(file_get_contents($dir.'/multiline.php') === "<?php\n__(\n    'ui.button.save'\n);\n", 'Multiline formatting changed');
    $check(count($result['successors']) === 1, 'Multiline call not reconciled');
    [$fourth] = $make('four.php');
    $run4 = $writer->prepare($fourth, 'en', $preview($fourth)['fingerprint'], 7, 'Test approval drift');
    $context = $editor->inspect($fourth, 'en');
    $editor->review($context['revision']['id'], $context['review']['fingerprint'], false, 7, 'Withdraw approval');
    $reject(fn () => $writer->apply($run4, 7), 'Approval or published');
    $check($writer->inspect($run4)['file_state'] === 'original', 'Revoked approval still wrote source');
    $writer->close($run4, 7, 'Discard stale approval plan');
    $context = $editor->inspect($fourth, 'en');
    $editor->review($context['revision']['id'], $context['review']['fingerprint'], true, 7, 'Approve again');
    $run5 = $writer->prepare($fourth, 'en', $preview($fourth)['fingerprint'], 7, 'Test published file drift');
    $language = file_get_contents($dir.'/lang/en/ui.php');
    file_put_contents($dir.'/lang/en/ui.php', $language.' // Changed');
    $reject(fn () => $writer->apply($run5, 7), 'Approval or published');
    $writer->close($run5, 7, 'Discard changed language plan');
    file_put_contents($dir.'/lang/en/ui.php', $language);
    $run6 = $writer->prepare($fourth, 'en', $preview($fourth)['fingerprint'], 7, 'Test assignment drift');
    $context = $editor->inspect($fourth, 'en');
    $editor->assign($fourth, 0, $context['finding']['link_id'], 7, 'Separate purpose', 'ui.other.save');
    $reject(fn () => $writer->apply($run6, 7), 'findings or assignments changed');
    $check($writer->inspect($run6)['file_state'] === 'original', 'Assignment drift wrote source');
    // Independent assignments, unchanged positions, and atomic multi-finding recovery.
    [$batch, $batchFile] = $make('batch.php', " __('Other');\n__('Stable');");
    $other = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance) VALUES(?,?,2,13,?,'Other','Other','own.suggested.key','{}') RETURNING id", [$batchFile, $scan, "__('Other')"])->fetchColumn();
    $stable = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,provenance) VALUES(?,?,3,1,?,'Stable','Stable','{}') RETURNING id", [$batchFile, $scan, "__('Stable')"])->fetchColumn();
    $own = $editor->assign($other, 0, null, 7, 'Independent purpose', 'ui.independent.other');
    $context = $editor->inspect($batch, 'en');
    $editor->review($context['revision']['id'], $context['review']['fingerprint'], true, 7, 'Approve batch');
    $run = $writer->prepare($batch, 'en', $preview($batch)['fingerprint'], 7, 'Batch movement');
    $reject(fn () => $writer->apply($run, 7, function () {
        throw new RuntimeException('Interrupted batch');
    }), 'Interrupted batch');
    $check($editor->inspect($other, 'en')['finding']['status'] === 'active', 'Interrupted batch partially retired findings');
    $result = $resumedWriter->apply($run, 7);
    $moved = $editor->inspect($result['successors'][1]['successor'], 'en')['finding'];
    $check((int) $moved['key_id'] === $own['key'] && $moved['suggested_key'] === 'own.suggested.key', 'Movement stole another key');
    $check($editor->inspect($stable, 'en')['finding']['status'] === 'active' && count($result['successors']) === 2, 'Unchanged occurrence lost identity');

    // A literal containing a real newline changes following line numbers as well as offsets.
    $literal = "Save\nnow";
    $expression = "__('Save\nnow')";
    $bytes = "<?php\n".$expression."; __('Other');\n__('Stable');\n";
    file_put_contents($dir.'/lines.php', $bytes);
    $file = $q('INSERT INTO translation_workbench_source_files(path) VALUES(?) RETURNING id', ['lines.php'])->fetchColumn();
    $ids = [];
    foreach ($scanner->scanSource($dir.'/lines.php', $bytes) as $item) {
        $capture = (new CopiedSourceCapture)->capture($item, $bytes, hash('sha256', $bytes), []);
        $ids[] = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,provenance) VALUES(?,?,?,?,?,?,?,'{}') RETURNING id", [$file, $scan, $capture['source_line'], $capture['source_column'], $capture['raw_expression'], $capture['raw_source_code_literal'], $capture['raw_literal']])->fetchColumn();
    }
    $assignment = $editor->assign($ids[0], 0, null, 7, 'Multiline literal', 'ui.multiline.save');
    $editor->save($ids[0], $assignment['key'], $assignment['link'], 'en', $literal, null, 7, 'Source revision');
    $context = $editor->inspect($ids[0], 'en');
    $editor->review($context['revision']['id'], $context['review']['fingerprint'], true, 7, 'Approve multiline literal');
    file_put_contents($dir.'/lang/en/ui.php', '<?php return '.var_export(['multiline' => ['save' => $literal]], true).';');
    $run = $writer->prepare($ids[0], 'en', $preview($ids[0])['fingerprint'], 7, 'Remove literal newline');
    $result = $writer->apply($run, 7);
    $check(count($result['successors']) === 3, 'Missing changed-line successors');
    foreach (array_slice($result['successors'], 1) as $pair) {
        $old = $editor->inspect($pair['predecessor'], 'en')['finding'];
        $new = $editor->inspect($pair['successor'], 'en')['finding'];
        $check((int) $new['source_line'] === (int) $old['source_line'] - 1 && $new['raw_literal'] === $old['raw_literal'], 'Line movement changed content or position');
    }
    // The batch receipt belongs to each predecessor and successor, even without a key.
    foreach ($result['successors'] as $pair) {
        foreach ([$pair['predecessor'], $pair['successor']] as $findingId) {
            $history = $editor->inspect($findingId, 'en')['history'];
            $receipts = array_values(array_filter($history, fn ($event) => $event['event_type'] === 'source_rewrite_reconciled'));
            $check(count($receipts) === 1 && $receipts[0]['successors'] == $result['successors'], 'Batch receipt missing from predecessor or successor history');
        }
    }
    $unrelatedHistory = $editor->inspect($stable, 'en')['history'];
    $check(count(array_filter($unrelatedHistory, fn ($event) => $event['event_type'] === 'source_rewrite_reconciled')) === 0, 'Unrelated finding received a batch receipt');

    // Actual complete scanner runs after publication/reconciliation must be idempotent.
    $rescan = new \Gunreip\TranslationWorkbench\Generation\CompleteRescanWriter($db, $scanner);
    $languageEvidence = ['ui.multiline.save' => ['value' => $literal, 'provenance' => ['path' => 'en/ui.php', 'sha256' => hash_file('sha256', $dir.'/lang/en/ui.php')]]];
    $snapshot = fn () => $q("SELECT md5(string_agg(row_to_json(f)::text, ',' ORDER BY id)) FROM translation_workbench_findings f")->fetchColumn();
    $initializationRequest = $rescan->prepare(['lines.php'], $languageEvidence);
    $missing = $initializationRequest['plan']['suggestion_initializations'];
    $check(count($missing) > 0, 'Fixture must exercise missing SuggestedKeys');
    $reject(fn () => $q('UPDATE translation_workbench_findings SET suggested_key=? WHERE id=?', ['manual.invalid.key', $missing[0]['finding']]), 'Immutable');
    $reject(fn () => $rescan->apply((string) Str::uuid(), $initializationRequest), 'user ID');
    $initializationRun = (string) Str::uuid();
    $initialized = $rescan->apply($initializationRun, $initializationRequest, actor: 7);
    $check(count($initialized['suggested_keys_initialized']) === count($missing), 'Missing SuggestedKeys were not initialized');
    $storedSnapshot = json_decode($q('SELECT scope_snapshot FROM translation_workbench_scan_runs WHERE id=?', [$initializationRun])->fetchColumn(), true);
    $check($storedSnapshot['format_version'] === 2 && ! array_key_exists('usage_decisions', $storedSnapshot), 'Scan snapshot duplicated usage decisions');
    $check($storedSnapshot['inventory'] == $initializationRequest['plan']['inventory'], 'Compact scan snapshot lost source evidence');
    $check((int) $q("SELECT count(*) FROM translation_workbench_events WHERE operation_id=? AND event_type='suggested_key_initialized' AND actor_id=7", [$initializationRun])->fetchColumn() === count($missing), 'SuggestedKey initialization lacks audit');
    foreach ($missing as $entry) {
        $check($q('SELECT suggested_key FROM translation_workbench_findings WHERE id=?', [$entry['finding']])->fetchColumn() === $entry['suggested_key'], 'Initialized SuggestedKey differs from preview');
        $reject(fn () => $q('UPDATE translation_workbench_findings SET suggested_key=? WHERE id=?', ['changed.invalid.key', $entry['finding']]), 'Immutable');
    }
    $unchanged = $snapshot();
    $linksBefore = $q("SELECT md5(string_agg(row_to_json(l)::text, ',' ORDER BY id)) FROM translation_workbench_key_findings l")->fetchColumn();
    $eventsBefore = $q('SELECT count(*) FROM translation_workbench_events')->fetchColumn();
    for ($pass = 0; $pass < 2; $pass++) {
        $request = $rescan->prepare(['lines.php'], $languageEvidence);
        $scanResult = $rescan->apply((string) Str::uuid(), $request, actor: 7);
        $check($scanResult['confirmed'] === 3 && $scanResult['created'] === [] && $scanResult['moved'] === [] && $scanResult['removed'] === [] && $scanResult['review_files'] === [], 'Rescan did not simply confirm rewritten source');
        $check($snapshot() === $unchanged, 'Unchanged rescan mutated findings');
    }
    $check($q("SELECT md5(string_agg(row_to_json(l)::text, ',' ORDER BY id)) FROM translation_workbench_key_findings l")->fetchColumn() === $linksBefore, 'Unchanged rescan mutated key assignments');
    $check($q('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === $eventsBefore, 'Unchanged rescan invented working events');
    $reviewedLinks = $q("SELECT l.id AS link,l.finding_id AS finding,l.key_id FROM translation_workbench_key_findings l JOIN translation_workbench_findings f ON f.id=l.finding_id JOIN translation_workbench_source_files s ON s.id=f.source_file_id WHERE l.valid_to IS NULL AND f.status='active' AND s.path='lines.php'")->fetchAll(PDO::FETCH_ASSOC);
    $sourceBeforeMove = file_get_contents($dir.'/lines.php');
    file_put_contents($dir.'/lines.php', "\n".$sourceBeforeMove);
    $request = $rescan->prepare(['lines.php'], $languageEvidence);
    $scanResult = $rescan->apply((string) Str::uuid(), $request, actor: 7);
    $check(count($scanResult['moved']) === 3 && $scanResult['created'] === [] && $scanResult['removed'] === [], 'Source movement lost occurrence continuity');
    foreach ($reviewedLinks as $link) {
        $successor = array_column($scanResult['moved'], 'successor', 'predecessor')[$link['finding']];
        $nextLink = $q('SELECT id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$successor])->fetchColumn();
        $expected = ['key_id' => $link['key_id'], 'usages' => [['link' => $link['link'], 'finding' => $link['finding']]]];
        $current = ['key_id' => $link['key_id'], 'usages' => [['link' => $nextLink, 'finding' => $successor]]];
        $check(\Gunreip\TranslationWorkbench\Generation\ReviewUsageContinuity::contextMatches($db, $expected, $current), 'Regular scan movement invalidated unchanged reviewed usage');
    }

    foreach ($scanResult['moved'] as $pair) {
        $old = $editor->inspect($pair['predecessor'], 'en')['finding'];
        $new = $editor->inspect($pair['successor'], 'en')['finding'];
        $check((int) $new['predecessor_id'] === $pair['predecessor'] && (int) $new['source_line'] === (int) $old['source_line'] + 1 && $new['suggested_key'] === $old['suggested_key'], 'Rescan movement lost provenance');
        $priorKey = $q('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? ORDER BY id DESC LIMIT 1', [$pair['predecessor']])->fetchColumn();
        $check(($new['key_id'] === null ? null : (int) $new['key_id']) === ($priorKey === false ? null : (int) $priorKey) && $new['raw_expression'] === $old['raw_expression'], 'Rescan movement changed key or source content');
    }
    $unchanged = $snapshot();
    $scanResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence), actor: 7);
    $check($scanResult['confirmed'] === 3 && $snapshot() === $unchanged, 'Follow-up rescan duplicated moved findings');

    // Scanner history comes from the existing operation receipt, not invented user events.
    foreach ($request['plan']['files']['lines.php']['moved'] as $movement) {
        $oldContext = $editor->inspect($movement['predecessor'], 'en');
        $newContext = $editor->inspect((int) $oldContext['successor'], 'en');
        foreach ([$oldContext, $newContext] as $context) {
            $scanHistory = array_values(array_filter($context['history'], fn ($event) => $event['event_type'] === 'scan_finding_moved'));
            $check(count($scanHistory) === 1 && $scanHistory[0]['actor_id'] === null && count($scanHistory[0]['successors']) === 1, 'Scanner movement receipt missing or duplicated');
        }
    }
    $scanBase = file_get_contents($dir.'/lines.php');
    file_put_contents($dir.'/lines.php', $scanBase."__('Fresh occurrence');\n");
    $createdResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence), actor: 7);
    $fresh = $createdResult['created'][0];
    $history = $editor->inspect($fresh, 'en')['history'];
    $check(count(array_filter($history, fn ($event) => $event['event_type'] === 'scan_finding_created')) === 1, 'Created finding has no scan history');
    file_put_contents($dir.'/lines.php', $scanBase);
    $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence), actor: 7);
    $history = $editor->inspect($fresh, 'en')['history'];
    $check(count(array_filter($history, fn ($event) => $event['event_type'] === 'scan_finding_removed')) === 1, 'Archived finding has no scan history');
    $check(count(array_filter($editor->inspect($stable, 'en')['history'], fn ($event) => str_starts_with($event['event_type'], 'scan_finding_'))) === 0, 'Unrelated finding acquired scan history');

    $scanList = $editor->recentScans();
    $check(count($scanList) >= 6 && count($scanList) <= 20 && (int) $scanList[0]['removed'] === 1, 'Scan summary missing latest archival');
    $createdPage = $editor->scanPage($createdResult['scan'], 'created', 999);
    $check($createdPage->total() === 1 && $createdPage->currentPage() === 1 && (int) $createdPage->items()[0]['id'] === $fresh, 'Created scan results do not navigate to the captured finding');
    $removedPage = $editor->scanPage($scanList[0]['id'], 'removed', 1);
    $check((int) $removedPage->items()[0]['id'] === $fresh && $removedPage->items()[0]['path'] === 'lines.php', 'Archived scan result lost its source');
    $reject(fn () => $editor->scanPage('not-a-uuid', 'created', 1), 'Invalid scan');
    $reject(fn () => $editor->scanPage($scanList[0]['id'], 'arbitrary', 1), 'Invalid scan');
    $reject(fn () => $editor->scanPage((string) Str::uuid(), 'created', 1), 'Scan not found');

    $movementScan = array_values(array_filter($scanList, fn ($entry) => (int) $entry['moved'] > 0))[0];
    $movedPage = $editor->scanPage($movementScan['id'], 'moved', 1);
    $check($movedPage->total() === 3 && $movedPage->items()[0]['predecessor'] !== null, 'Movement list lost predecessor navigation');
    file_put_contents($dir.'/review.php', "<?php __('ui.missing.label');");
    $reviewResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['review.php'], []), actor: 7);
    $reviewPage = $editor->scanPage($reviewResult['scan'], 'review_files', 1);
    $check($reviewPage->total() === 1 && $reviewPage->items()[0]['path'] === 'review.php' && $reviewPage->items()[0]['id'] === null, 'Review-file list fabricated a finding link');
    $check($editor->scanPage($reviewResult['scan'], 'review_files', 1, 'REVIEW.PHP')->total() === 1 && $editor->scanPage($reviewResult['scan'], 'review_files', 1, '%')->total() === 0, 'Review file search must match literal paths case insensitively');
    $reviewRow = $reviewPage->items()[0];
    $check($reviewRow['issues'][0]['reason'] === 'key_like_argument_without_lang_evidence' && $reviewRow['issues'][0]['line'] === 1 && $reviewRow['issues'][0]['column'] === 7 && $reviewRow['preserved_count'] === 0, 'Review reason or exact source position missing');
    $check(! array_key_exists('captures', $reviewRow), 'Review list leaked full source captures');

    $beforeReview = file_get_contents($dir.'/lines.php');
    file_put_contents($dir.'/lines.php', $beforeReview.'__($runtimeValue);');
    $preservedResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence), actor: 7);
    $preservedPage = $editor->preservedFindingsPage($preservedResult['scan'], 'lines.php', 999);
    $check($preservedPage->total() === 3 && $preservedPage->currentPage() === 1, 'Preserved findings page does not match the scan snapshot');
    $check(count(array_filter($preservedPage->items(), fn ($finding) => $finding['full_name'] === 'ui.multiline.save')) === 1, 'Preserved findings lost their current key assignment');
    $reject(fn () => $editor->preservedFindingsPage($preservedResult['scan'], 'other.php', 1), 'not part of the selected scan review');
    $reject(fn () => $editor->preservedFindingsPage('invalid', 'lines.php', 1), 'Invalid scan');
    $emptyPreserved = $editor->preservedFindingsPage($reviewResult['scan'], 'review.php', 1);
    $check($emptyPreserved->total() === 0 && $emptyPreserved->items() === [], 'New review file acquired existing findings');
    file_put_contents($dir.'/lines.php', $beforeReview);

    $capturePage = $editor->scanCapturesPage($reviewResult['scan'], 'review.php', 999);
    $check($capturePage->total() === 1 && $capturePage->currentPage() === 1 && $capturePage->items()[0]['found_translation_key'] === 'ui.missing.label', 'Captured source is not linked to the selected scan');
    $check($capturePage->items()[0]['raw_literal'] === null && $capturePage->items()[0]['raw_expression'] === "__('ui.missing.label')", 'Capture confused a key with a source literal');
    $savedCapture = $capturePage->items();
    file_put_contents($dir.'/review.php', "<?php __('Changed later');");
    $check($editor->scanCapturesPage($reviewResult['scan'], 'review.php', 1)->items() === $savedCapture, 'Historical capture was replaced with current source');
    $reject(fn () => $editor->scanCapturesPage($reviewResult['scan'], 'other.php', 1), 'not part of the selected scan review');
    $reject(fn () => $editor->scanCapturesPage('invalid', 'review.php', 1), 'Invalid scan');

    // A fresh scan capture can be classified without relying on historical capture manifests.
    file_put_contents($dir.'/lines.php', $beforeReview.'__($runtimeValue);');
    $usageContext = $editor->scanUsage($preservedResult['scan'], 'lines.php', 3);
    $check($usageContext['capture']['raw_expression'] === '__($runtimeValue)' && $usageContext['decision'] === null, 'Fresh dynamic capture not available for classification');
    $stalePlan = $rescan->prepare(['lines.php'], $languageEvidence);
    $captureBeforeDecision = $editor->scanCapturesPage($preservedResult['scan'], 'lines.php', 1)->items()[3];
    $check($captureBeforeDecision['can_classify'] && $captureBeforeDecision['usage_decision'] === null, 'unclassified confirmed capture remains open');
    $check($editor->scanUsageReasons('database_value') === [], 'no reasons suggested before a saved decision');
    $decision = $editor->decideScanUsage($preservedResult['scan'], 'lines.php', 3, 'database_value', 'Value comes from the database', 7, 0);
    $captureWithDecision = $editor->scanCapturesPage($preservedResult['scan'], 'lines.php', 1)->items();
    $check($captureWithDecision[3]['usage_decision']['id'] === $decision['id'] && $captureWithDecision[0]['usage_decision'] === null && ! $captureWithDecision[0]['can_classify'], 'saved decision belongs only to its exact dynamic capture');
    $check($decision['actor_id'] === 7 && $editor->scanUsage($preservedResult['scan'], 'lines.php', 3)['decision']['id'] === $decision['id'], 'Decision not stored with its actor');
    $reject(fn () => $rescan->apply((string) Str::uuid(), $stalePlan, actor: 7), 'Stale rescan database state');
    $reject(fn () => $editor->decideScanUsage($preservedResult['scan'], 'lines.php', 3, 'translate', 'Concurrent choice', 8, 0), 'Another decision');
    $beforeApply = $snapshot();
    $classified = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence), actor: 7);
    $check($classified['confirmed'] === 3 && $classified['review_files'] === [] && $classified['created'] === [] && $classified['removed'] === [] && $snapshot() === $beforeApply, 'DatabaseValue changed findings instead of excluding the dynamic usage');
    $check($classified['usage_decisions'][0]['event_id'] === $decision['id'], 'Scan receipt lost the applied decision');
    $linkedContext = $editor->consideredScanUsage($classified['scan'], $decision['id']);
    $check($linkedContext['run'] === $preservedResult['scan'] && $linkedContext['index'] === 3 && $linkedContext['decision']['id'] === $decision['id'], 'considered decision opens its original scan capture');
    $reject(fn () => $editor->consideredScanUsage($preservedResult['scan'], $decision['id']), 'not considered');
    $summary = array_values(array_filter($editor->recentScans(), fn ($row) => $row['id'] === $classified['scan']))[0];
    $check((int) $summary['usage_decisions'] === 1, 'scan summary counts considered decisions');
    $decisionPage = $editor->scanPage($classified['scan'], 'usage_decisions', 1);
    $check($editor->scanPage($classified['scan'], 'usage_decisions', 1, 'lines.php')->total() === 1 && $editor->scanPage($classified['scan'], 'usage_decisions', 1, 'not-present')->total() === 0, 'Usage decision search must filter source paths');
    $check($decisionPage->total() === 1 && $decisionPage->items()[0]['decision_event_id'] === $decision['id'] && $decisionPage->items()[0]['id'] === null, 'Scan UI lost usage decision evidence');
    $eventCount = $q('SELECT count(*) FROM translation_workbench_events')->fetchColumn();
    $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence), actor: 7);
    $check($q('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === $eventCount, 'Repeated scan duplicated the usage decision');
    $dynamic = $usageContext['capture'];
    $sourceFile = $q("SELECT id FROM translation_workbench_source_files WHERE path='lines.php'")->fetchColumn();
    $conflicting = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,provenance) VALUES(?,?,?,?,?,'{}') RETURNING id", [$sourceFile, $scan, $dynamic['source_line'], $dynamic['source_column'], $dynamic['raw_expression']])->fetchColumn();
    $conflictPlan = $rescan->prepare(['lines.php'], $languageEvidence);
    $check($conflictPlan['plan']['files']['lines.php']['state'] === 'review' && $conflictPlan['plan']['files']['lines.php']['issues'][0]['reason'] === 'database_value_finding_conflict', 'DatabaseValue silently retired an existing finding');
    $reject(fn () => $editor->decideScanUsage($preservedResult['scan'], 'lines.php', 3, 'database_value', 'Cannot exclude an existing finding', 7, $decision['id']), 'active finding occupies');
    $q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason='End isolated conflict fixture' WHERE id=?", [$conflicting]);
    $translated = $editor->decideScanUsage($preservedResult['scan'], 'lines.php', 3, 'translate', 'This runtime usage still needs translation work', 7, $decision['id']);
    $check($editor->scanCapturesPage($preservedResult['scan'], 'lines.php', 1)->items()[3]['usage_decision']['id'] === $translated['id'], 'capture list shows the latest saved decision');
    $request = $rescan->prepare(['lines.php'], $languageEvidence);
    $check($request['plan']['files']['lines.php']['state'] === 'review' && $request['plan']['files']['lines.php']['issues'][0]['reason'] === 'translation_work_required', 'Translate was incorrectly treated as finished');
    $check($translated['previous_event_id'] === $decision['id'], 'Changed decision lost history');
    $check($editor->consideredScanUsage($classified['scan'], $decision['id'])['decision']['id'] === $translated['id'] && $editor->scanPage($classified['scan'], 'usage_decisions', 1)->items()[0]['decision_event_id'] === $decision['id'], 'original scan keeps its historical decision while review opens the latest one');
    file_put_contents($dir.'/lines.php', $beforeReview.'__($differentRuntimeValue);');
    $reject(fn () => $editor->decideScanUsage($preservedResult['scan'], 'lines.php', 3, 'database_value', 'Outdated source', 7, $translated['id']), 'Source changed');
    $check($editor->scanUsageReasons('database_value') === ['Value comes from the database'], 'saved reasons are distinct and rejected reasons are excluded');
    $check($editor->scanUsageReasons('translate') === ['This runtime usage still needs translation work'], 'reason suggestions stay scoped to classification');
    $changedPlan = $rescan->prepare(['lines.php'], $languageEvidence);
    $check($changedPlan['plan']['usage_decisions'] === [] && $changedPlan['plan']['files']['lines.php']['state'] === 'review', 'Decision leaked to a changed source expression');
    $reject(fn () => $editor->scanUsage($reviewResult['scan'], 'review.php', 0), 'confirmed dynamic');
    $reject(fn () => $editor->scanUsage($preservedResult['scan'], 'lines.php', 99), 'not found');
    file_put_contents($dir.'/lines.php', $beforeReview);

    // Rewriting an inner call must not silently invalidate its stored enclosing call.
    file_put_contents($dir.'/lang/en/ui.php', "<?php return ['button' => ['save' => 'Save']];");
    [$outer, $nestedFile, $nestedBytes] = $make('nested.php', '', "__('Save', ['nested' => __('Save')])");
    $nestedItems = $scanner->scanSource($dir.'/nested.php', $nestedBytes)->values();
    $check($nestedItems->count() === 2, 'Nested fixture must contain two scanner findings');
    $innerCapture = (new CopiedSourceCapture)->capture($nestedItems[1], $nestedBytes, hash('sha256', $nestedBytes), []);
    $inner = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,provenance) VALUES(?,?,?,?,?,?,?,'{}') RETURNING id", [$nestedFile, $scan, $innerCapture['source_line'], $innerCapture['source_column'], $innerCapture['raw_expression'], 'Save', 'Save'])->fetchColumn();
    $context = $editor->inspect($outer, 'en');
    $editor->assign($inner, $context['finding']['key_id'], null, 7, 'Assign nested source');
    $context = $editor->inspect($inner, 'en');
    $editor->review($context['revision']['id'], $context['review']['fingerprint'], true, 7, 'Review nested source');
    $operations = $q('SELECT count(*) FROM translation_workbench_operations')->fetchColumn();
    $reject(fn () => $writer->prepare($inner, 'en', $preview($inner)['fingerprint'], 7, 'Overlapping rewrite'), 'overlaps the edited argument');
    $check(file_get_contents($dir.'/nested.php') === $nestedBytes && $q('SELECT count(*) FROM translation_workbench_operations')->fetchColumn() === $operations, 'Rejected overlap left file changes or a prepared plan');
    $check($editor->inspect($outer, 'en')['finding']['status'] === 'active' && $editor->inspect($inner, 'en')['finding']['status'] === 'active', 'Rejected overlap retired findings');
    $check($q('SELECT count(*) FROM public.translation_workbench_findings')->fetchColumn() == 1, 'Legacy data changed');

    // Suggested keys identify a lineage, not a shared translation key.
    $suggestionPath = $dir.'/suggestions.php';
    file_put_contents($suggestionPath, "<?php\n__('Ordinal fixture');\n__('Ordinal fixture');\n__('ui.suggestion.observed');\n");
    $suggestionEvidence = ['ui.suggestion.observed' => ['value' => 'Observed', 'provenance' => ['locale' => 'en', 'key' => 'ui.suggestion.observed', 'path' => 'missing-evidence.php', 'sha256' => str_repeat('a', 64)]]];
    $suggestionRequest = $rescan->prepare(['suggestions.php'], $suggestionEvidence);
    $suggestionResult = $rescan->apply((string) Str::uuid(), $suggestionRequest, actor: 7);
    $check(count($suggestionResult['created']) === 3, 'First scan must capture each independent occurrence');
    $suggestions = array_map(fn ($id) => $editor->inspect($id, 'en')['finding']['suggested_key'], $suggestionResult['created']);
    $check($suggestions[1] === $suggestions[0].'_2', 'Collision must append an ordinal');
    $check($suggestions[2] !== null && $suggestions[2] !== 'ui.suggestion.observed', 'Existing source key must receive a source-specific suggestion');
    $expectedGroups = array_column((new \Gunreip\TranslationWorkbench\Generation\RescanCandidateRules)->sharedCandidates($db), 'findings');
    $check($suggestionResult['format_version'] === 2 && ! array_key_exists('shared_candidates', $suggestionResult)
        && $suggestionResult['shared_candidate_groups'] === $expectedGroups, 'Compact receipt lost candidate group membership');
    $ordinalGroup = array_slice($suggestionResult['created'], 0, 2);
    $check(in_array($ordinalGroup, $suggestionResult['shared_candidate_groups'], true), 'Repeated literal must preserve both finding references');
    $check($rescan->apply($suggestionResult['scan'], $suggestionRequest, actor: 7) == $suggestionResult, 'Replayed compact receipt differs from original result');
    $confirmedSuggestions = $rescan->apply((string) Str::uuid(), $rescan->prepare(['suggestions.php'], $suggestionEvidence), actor: 7);
    $check($confirmedSuggestions['created'] === [] && $confirmedSuggestions['confirmed'] === 3, 'Repeat scan must not recreate suggestions');
    file_put_contents($suggestionPath, "<?php\n");
    $rescan->apply((string) Str::uuid(), $rescan->prepare(['suggestions.php'], $suggestionEvidence), actor: 7);
    $archivedLiterals = $q('SELECT raw_literal FROM translation_workbench_findings WHERE id IN (?,?) ORDER BY id', $ordinalGroup)->fetchAll(PDO::FETCH_COLUMN);
    $historicalResult = json_decode($q('SELECT result FROM translation_workbench_operations WHERE id=?', [$suggestionResult['scan']])->fetchColumn(), true);
    $check($archivedLiterals === ['Ordinal fixture', 'Ordinal fixture'] && in_array($ordinalGroup, $historicalResult['shared_candidate_groups'], true), 'Archival lost historical shared candidate evidence');
    file_put_contents($suggestionPath, "<?php\n__('Ordinal fixture');\n");
    $reappeared = $rescan->apply((string) Str::uuid(), $rescan->prepare(['suggestions.php'], $suggestionEvidence), actor: 7);
    $check(count($reappeared['created']) === 1, 'Reappearing archived source is a new finding');
    $check($editor->inspect($reappeared['created'][0], 'en')['finding']['suggested_key'] === $suggestions[0].'_3', 'Archived suggestions must remain reserved');

    // Explicit UI position decisions use only this temporary database and source tree.
    $decisions = new \Gunreip\TranslationWorkbench\Generation\SourcePositionDecision($db, $dir);
    [$moveId, , $moveBytes] = $make('decision-move.php');
    $moveKey = (int) $editor->inspect($moveId, 'en')['finding']['key_id'];
    file_put_contents($dir.'/decision-move.php', "\n".$moveBytes);
    $plan = $decisions->prepare($moveId, $moveKey);
    $check($plan['can_move'] && !$plan['can_remove'], 'Movement decision eligibility');
    $reject(fn () => $decisions->apply((string) Str::uuid(), $moveId, $moveKey, $plan['fingerprint'], 'removed', 7, 'Wrong decision'), 'not supported');
    file_put_contents($dir.'/decision-move.php', "\n\n".$moveBytes);
    $reject(fn () => $decisions->apply((string) Str::uuid(), $moveId, $moveKey, $plan['fingerprint'], 'moved', 7, 'Confirmed movement'), 'changed');
    $check($q('SELECT status FROM translation_workbench_findings WHERE id=?', [$moveId])->fetchColumn() === 'active', 'Stale decision changed finding');
    $plan = $decisions->prepare($moveId, $moveKey);
    $op = (string) Str::uuid();
    $result = $decisions->apply($op, $moveId, $moveKey, $plan['fingerprint'], 'moved', 7, 'Confirmed movement');
    $successor = $editor->inspect($result['successors'][0]['successor'], 'en')['finding'];
    $check((int) $successor['source_line'] === 4 && $successor['suggested_key'] === 'immutable.suggested.key' && (int) $successor['key_id'] === $moveKey, 'Movement lost position or immutable key/assignment');
    $check($q('SELECT status FROM translation_workbench_findings WHERE id=?', [$moveId])->fetchColumn() === 'obsolete', 'Movement did not archive predecessor');
    $check($decisions->apply($op, $moveId, $moveKey, $plan['fingerprint'], 'moved', 7, 'Confirmed movement') == $result, 'Retry result differs');
    $check((int) $q('SELECT count(*) FROM translation_workbench_findings WHERE predecessor_id=?', [$moveId])->fetchColumn() === 1, 'Retry created duplicate successor');
    $check(file_get_contents($dir.'/decision-move.php') === "\n\n".$moveBytes, 'Decision rewrote source');

    [$removedId] = $make('decision-removed.php');
    unlink($dir.'/decision-removed.php');
    $plan = $decisions->prepare($removedId, $moveKey);
    $check($plan['can_remove'] && !$plan['can_move'], 'Missing file decision eligibility');
    file_put_contents($dir.'/decision-removed.php', "<?php // returned");
    $reject(fn () => $decisions->apply((string) Str::uuid(), $removedId, $moveKey, $plan['fingerprint'], 'removed', 7, 'Confirmed removal'), 'changed');
    unlink($dir.'/decision-removed.php');
    $plan = $decisions->prepare($removedId, $moveKey);
    $decisions->apply((string) Str::uuid(), $removedId, $moveKey, $plan['fingerprint'], 'removed', 7, 'Confirmed removal');
    $check($q('SELECT status FROM translation_workbench_findings WHERE id=?', [$removedId])->fetchColumn() === 'obsolete', 'Removal did not archive finding');
    $check((int) $q('SELECT count(*) FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$removedId])->fetchColumn() === 0, 'Removal left current link');
    $check((int) $q('SELECT count(*) FROM translation_workbench_keys WHERE id=?', [$moveKey])->fetchColumn() === 1, 'Removal deleted shared key');
    $check((int) $q("SELECT count(*) FROM translation_workbench_events WHERE event_type='source_removal_confirmed' AND details->>'finding'=?", [(string) $removedId])->fetchColumn() === 1, 'Removal history missing');

    [$commentId, , $commentBytes] = $make('decision-comment.php');
    file_put_contents($dir.'/decision-comment.php', "<?php // __('Save');\n");
    $plan = $decisions->prepare($commentId, $moveKey);
    $reject(fn () => $decisions->apply((string) Str::uuid(), $commentId, $moveKey, $plan['fingerprint'], 'moved', 7, 'Do not accept a comment'), 'active translation call');
    $check($q('SELECT status FROM translation_workbench_findings WHERE id=?', [$commentId])->fetchColumn() === 'active', 'Comment decision changed finding');

    // Batch: compose same-line calls once, preserve other keys, and resume per-file receipts.
    file_put_contents($dir.'/lang/en/ui.php', "<?php return ['button'=>['save'=>'Save'], 'bulk'=>['save'=>'Save']];");
    $batchKey = null;
    $batchIds = [];
    $batchOriginals = [];
    foreach (['bulk-a.php' => "<?php\n__('Save'); __('Save'); __('Save');\n", 'bulk-b.php' => "<?php\n__('Save');\n", 'bulk-noop.php' => "<?php\n__('ui.bulk.save');\n"] as $name => $bytes) {
        file_put_contents($dir.'/'.$name, $bytes);
        $batchOriginals[$name] = $bytes;
        $source = $q('INSERT INTO translation_workbench_source_files(path) VALUES(?) RETURNING id', [$name])->fetchColumn();
        foreach ($scanner->scanSource($name, $bytes) as $index => $item) {
            $capture = (new CopiedSourceCapture)->capture($item, $bytes, hash('sha256', $bytes), []);
            $fid = (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance) VALUES(?,?,?,?,?,?,?,?,'{}') RETURNING id", [$source, $scan, $capture['source_line'], $capture['source_column'], $capture['raw_expression'], $capture['raw_source_code_literal'], $capture['raw_literal'], $name.'.'.$index])->fetchColumn();
            if ($name === 'bulk-a.php' && $index === 2) {
                $editor->assign($fid, $moveKey, null, 7, 'Independent key');
                $independent = $fid;
                continue;
            }
            $link = $editor->assign($fid, $batchKey ?? 0, null, 7, 'Batch fixture', $batchKey === null ? 'ui.bulk.save' : null);
            $batchKey = $link['key'];
            $batchIds[] = $fid;
        }
    }
    $ctx = $editor->inspect($batchIds[0], 'en');
    $editor->save($batchIds[0], $batchKey, $ctx['finding']['link_id'], 'en', 'Save', null, 7, 'Batch English draft');
    $ctx = $editor->inspect($batchIds[0], 'en');
    $editor->review($ctx['revision']['id'], $ctx['review']['fingerprint'], true, 7, 'Review batch');
    $batchPreview = $writer->previewBatch($batchKey, $batchIds);
    $check($batchPreview['ready'] && count($batchPreview['items']) === 4, 'Batch preview lost selected findings');
    $check(count(array_filter($batchPreview['items'], fn ($item) => $item['already_set'])) === 1, 'No-op not identified');
    $reject(fn () => $writer->previewBatch($batchKey, [$batchIds[0]]), 'key group changed');
    file_put_contents($dir.'/bulk-b.php', "\n".$batchOriginals['bulk-b.php']);
    $reject(fn () => $writer->prepareBatch($batchKey, $batchIds, $batchPreview['fingerprint'], 7, 'Batch reason'), 'changed or is blocked');
    file_put_contents($dir.'/bulk-b.php', $batchOriginals['bulk-b.php']);
    $batchRun = $writer->prepareBatch($batchKey, $batchIds, $batchPreview['fingerprint'], 7, 'Batch reason');
    $check(count($writer->inspect($batchRun)['files']) === 2, 'Must prepare one operation per changed file');
    $check(file_get_contents($dir.'/bulk-a.php') === $batchOriginals['bulk-a.php'], 'Batch preparation wrote source');
    $blockedBatch = $writer->previewBatch($batchKey, $batchIds);
    $check(!$blockedBatch['ready'] && $blockedBatch['blocked'] === 3, 'Open per-file plans not blocking whole group');
    $reject(fn () => $writer->apply($batchRun, 8), 'preparing user');
    $reject(fn () => $writer->apply($batchRun, 7, function ($stage) {
        if ($stage === 'after_file') throw new RuntimeException('Stop between files');
    }), 'Stop between files');
    $partial = $writer->inspect($batchRun);
    $check($partial['files'][0]['state'] === 'complete' && $partial['files'][1]['state'] === 'pending', 'Partial completion not visible');
    $check(file_get_contents($dir.'/bulk-a.php') === "<?php\n__('ui.bulk.save'); __('ui.bulk.save'); __('Save');\n", 'Combined same-line edits incorrect');
    $independentSuccessor = $q('SELECT id FROM translation_workbench_findings WHERE predecessor_id=?', [$independent])->fetchColumn();
    $check((int) $editor->inspect($independentSuccessor, 'en')['finding']['key_id'] === $moveKey, 'Shifted independent finding lost its own key');
    $resumed = $writer->apply($batchRun, 7);
    $check($writer->inspect($batchRun)['state'] === 'complete', 'Batch did not complete on resume');
    $check(file_get_contents($dir.'/bulk-b.php') === "<?php\n__('ui.bulk.save');\n", 'Second file not replaced');
    $check($writer->apply($batchRun, 7) == $resumed, 'Completed batch retry changed receipts');
    $check($q('SELECT status FROM translation_workbench_findings WHERE id=?', [$batchIds[3]])->fetchColumn() === 'active', 'Unchanged call must remain active');


    // A receipted successor must preserve approval in both PHP and SQL worklists.
    $revisionId = (int) $ctx['revision']['id'];
    $reviewer = new \Gunreip\TranslationWorkbench\Generation\BaselineRevisionReviewer($db);
    $approvedInList = static fn () => (bool) $q('SELECT 1 FROM ('.\Gunreip\TranslationWorkbench\Generation\ReviewWorklist::APPROVED.') a WHERE revision=?', [$revisionId])->fetchColumn();
    $check($reviewer->approvalIsCurrent($revisionId), 'Source replacement invalidated unchanged revision approval');
    $check($approvedInList(), 'SQL worklist disagrees about successor approval');
    $check($q('SELECT count(*) FROM translation_workbench_reviews WHERE revision_id=?', [$revisionId])->fetchColumn() == 1, 'Approval preservation must not write a new review');
    $successorId = $resumed['successors'][0]['successor'];
    $assertInvalid = static function (callable $change, string $message) use ($db, $check, $reviewer, $revisionId, $approvedInList) {
        $db->beginTransaction();
        try {
            $change();
            $check(!$reviewer->approvalIsCurrent($revisionId), $message.' (PHP)');
            $check(!$approvedInList(), $message.' (SQL)');
        } finally { $db->rollBack(); }
    };
    $assertInvalid(function () use ($db, $q, $successorId) {
        // Simulate missing receipt evidence in the isolated fixture only.
        $db->exec('ALTER TABLE translation_workbench_operations DISABLE TRIGGER USER');
        $q("UPDATE translation_workbench_operations SET kind='unverified' WHERE kind='source_replacement_applied' AND result->'successors' @> ?::jsonb", [json_encode([['successor' => $successorId]])]);
        $db->exec('ALTER TABLE translation_workbench_operations ENABLE TRIGGER USER');
    }, 'Unreceipted successor accepted');
    $assertInvalid(function () use ($q, $successorId, $batchKey) {
        $q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$successorId]);
        $q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$successorId, $batchKey]);
    }, 'A later reassignment was accepted as the original successor link');
    $assertInvalid(function () use ($q, $successorId, $moveKey) {
        $q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$successorId]);
        $q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$successorId, $moveKey]);
    }, 'Different key assignment accepted');
    $assertInvalid(fn () => $q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now() WHERE id=?", [$successorId]), 'Missing active usage accepted');
    $assertInvalid(function () use ($q, $independentSuccessor, $batchKey) {
        $q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$independentSuccessor]);
        $q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$independentSuccessor, $batchKey]);
    }, 'Additional usage accepted without review');
    $assertInvalid(function () use ($editor, $revisionId, $db) {
        $snapshot = \Gunreip\TranslationWorkbench\Generation\RevisionReviewContext::snapshot($db, $revisionId);
        $editor->review($revisionId, $snapshot['fingerprint'], false, 7, 'Withdraw approval');
    }, 'Revoked review accepted');
    $assertInvalid(function () use ($editor, $successorId, $batchKey, $revisionId, $q) {
        $link = (int) $q('SELECT id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$successorId])->fetchColumn();
        $editor->save($successorId, $batchKey, $link, 'en', 'Changed source text', $revisionId, 7, 'New revision');
    }, 'Changed translation revision accepted');
    $assertInvalid(function () use ($q, $batchKey) {
        $q('UPDATE translation_workbench_key_names SET valid_to=now() WHERE key_id=? AND valid_to IS NULL', [$batchKey]);
        $q("INSERT INTO translation_workbench_key_names(key_id,namespace,full_name) VALUES (?,'','ui.bulk.changed')", [$batchKey]);
    }, 'Changed key identity accepted');


    require __DIR__.'/scan-write-batching.php';
    require __DIR__.'/missing-immutable.php';
    echo json_encode(['checks' => $checks, 'state' => 'passed', 'production_connected' => false, 'scan_write_profile' => $scanWriteProfile]).PHP_EOL;
} finally {
    if ($started) {
        $runProcess([$bin.'/pg_ctl', '-D', $dir.'/data', '-m', 'immediate', '-w', 'stop']);
    }
}
