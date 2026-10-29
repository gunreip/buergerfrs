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
    $db = new PDO('pgsql:host='.$dir.'/socket;dbname=editor;user=tester', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    (new WorkingGenerationSchema)->install($db, 'editor', 'current_generation_v2', (string) Str::uuid());
    $db->exec('CREATE TABLE public.translation_workbench_findings(id int PRIMARY KEY); INSERT INTO public.translation_workbench_findings VALUES(777)');
    $db->exec('SET search_path TO current_generation_v2,pg_catalog');
    $q = static function ($sql, $params = []) use ($db) {
        $s = $db->prepare($sql);
        $s->execute($params);

        return $s;
    };
    $app = new Application($dir);
    $app->instance('config', new \Illuminate\Config\Repository);
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
    $unchanged = $snapshot();
    $linksBefore = $q("SELECT md5(string_agg(row_to_json(l)::text, ',' ORDER BY id)) FROM translation_workbench_key_findings l")->fetchColumn();
    $eventsBefore = $q('SELECT count(*) FROM translation_workbench_events')->fetchColumn();
    for ($pass = 0; $pass < 2; $pass++) {
        $request = $rescan->prepare(['lines.php'], $languageEvidence);
        $scanResult = $rescan->apply((string) Str::uuid(), $request);
        $check($scanResult['confirmed'] === 3 && $scanResult['created'] === [] && $scanResult['moved'] === [] && $scanResult['removed'] === [] && $scanResult['review_files'] === [], 'Rescan did not simply confirm rewritten source');
        $check($snapshot() === $unchanged, 'Unchanged rescan mutated findings');
    }
    $check($q("SELECT md5(string_agg(row_to_json(l)::text, ',' ORDER BY id)) FROM translation_workbench_key_findings l")->fetchColumn() === $linksBefore, 'Unchanged rescan mutated key assignments');
    $check($q('SELECT count(*) FROM translation_workbench_events')->fetchColumn() === $eventsBefore, 'Unchanged rescan invented working events');
    $sourceBeforeMove = file_get_contents($dir.'/lines.php');
    file_put_contents($dir.'/lines.php', "\n".$sourceBeforeMove);
    $request = $rescan->prepare(['lines.php'], $languageEvidence);
    $scanResult = $rescan->apply((string) Str::uuid(), $request);
    $check(count($scanResult['moved']) === 3 && $scanResult['created'] === [] && $scanResult['removed'] === [], 'Source movement lost occurrence continuity');
    foreach ($scanResult['moved'] as $pair) {
        $old = $editor->inspect($pair['predecessor'], 'en')['finding'];
        $new = $editor->inspect($pair['successor'], 'en')['finding'];
        $check((int) $new['predecessor_id'] === $pair['predecessor'] && (int) $new['source_line'] === (int) $old['source_line'] + 1 && $new['suggested_key'] === $old['suggested_key'], 'Rescan movement lost provenance');
        $priorKey = $q('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? ORDER BY id DESC LIMIT 1', [$pair['predecessor']])->fetchColumn();
        $check(($new['key_id'] === null ? null : (int) $new['key_id']) === ($priorKey === false ? null : (int) $priorKey) && $new['raw_expression'] === $old['raw_expression'], 'Rescan movement changed key or source content');
    }
    $unchanged = $snapshot();
    $scanResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence));
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
    $createdResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence));
    $fresh = $createdResult['created'][0];
    $history = $editor->inspect($fresh, 'en')['history'];
    $check(count(array_filter($history, fn ($event) => $event['event_type'] === 'scan_finding_created')) === 1, 'Created finding has no scan history');
    file_put_contents($dir.'/lines.php', $scanBase);
    $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence));
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
    $reviewResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['review.php'], []));
    $reviewPage = $editor->scanPage($reviewResult['scan'], 'review_files', 1);
    $check($reviewPage->total() === 1 && $reviewPage->items()[0]['path'] === 'review.php' && $reviewPage->items()[0]['id'] === null, 'Review-file list fabricated a finding link');
    $reviewRow = $reviewPage->items()[0];
    $check($reviewRow['issues'][0]['reason'] === 'key_like_argument_without_lang_evidence' && $reviewRow['issues'][0]['line'] === 1 && $reviewRow['issues'][0]['column'] === 7 && $reviewRow['preserved_count'] === 0, 'Review reason or exact source position missing');
    $check(! array_key_exists('captures', $reviewRow), 'Review list leaked full source captures');

    $beforeReview = file_get_contents($dir.'/lines.php');
    file_put_contents($dir.'/lines.php', $beforeReview.'__($runtimeValue);');
    $preservedResult = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence));
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
    $reject(fn () => $rescan->apply((string) Str::uuid(), $stalePlan), 'Stale rescan database state');
    $reject(fn () => $editor->decideScanUsage($preservedResult['scan'], 'lines.php', 3, 'translate', 'Concurrent choice', 8, 0), 'Another decision');
    $beforeApply = $snapshot();
    $classified = $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence));
    $check($classified['confirmed'] === 3 && $classified['review_files'] === [] && $classified['created'] === [] && $classified['removed'] === [] && $snapshot() === $beforeApply, 'DatabaseValue changed findings instead of excluding the dynamic usage');
    $check($classified['usage_decisions'][0]['event_id'] === $decision['id'], 'Scan receipt lost the applied decision');
    $linkedContext = $editor->consideredScanUsage($classified['scan'], $decision['id']);
    $check($linkedContext['run'] === $preservedResult['scan'] && $linkedContext['index'] === 3 && $linkedContext['decision']['id'] === $decision['id'], 'considered decision opens its original scan capture');
    $reject(fn () => $editor->consideredScanUsage($preservedResult['scan'], $decision['id']), 'not considered');
    $summary = array_values(array_filter($editor->recentScans(), fn ($row) => $row['id'] === $classified['scan']))[0];
    $check((int) $summary['usage_decisions'] === 1, 'scan summary counts considered decisions');
    $decisionPage = $editor->scanPage($classified['scan'], 'usage_decisions', 1);
    $check($decisionPage->total() === 1 && $decisionPage->items()[0]['decision_event_id'] === $decision['id'] && $decisionPage->items()[0]['id'] === null, 'Scan UI lost usage decision evidence');
    $eventCount = $q('SELECT count(*) FROM translation_workbench_events')->fetchColumn();
    $rescan->apply((string) Str::uuid(), $rescan->prepare(['lines.php'], $languageEvidence));
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
    $suggestionResult = $rescan->apply((string) Str::uuid(), $suggestionRequest);
    $check(count($suggestionResult['created']) === 3, 'First scan must capture each independent occurrence');
    $suggestions = array_map(fn ($id) => $editor->inspect($id, 'en')['finding']['suggested_key'], $suggestionResult['created']);
    $check($suggestions[1] === $suggestions[0].'_2', 'Collision must append an ordinal');
    $check($suggestions[2] !== null && $suggestions[2] !== 'ui.suggestion.observed', 'Existing source key must receive a source-specific suggestion');
    $confirmedSuggestions = $rescan->apply((string) Str::uuid(), $rescan->prepare(['suggestions.php'], $suggestionEvidence));
    $check($confirmedSuggestions['created'] === [] && $confirmedSuggestions['confirmed'] === 3, 'Repeat scan must not recreate suggestions');
    file_put_contents($suggestionPath, "<?php\n");
    $rescan->apply((string) Str::uuid(), $rescan->prepare(['suggestions.php'], $suggestionEvidence));
    file_put_contents($suggestionPath, "<?php\n__('Ordinal fixture');\n");
    $reappeared = $rescan->apply((string) Str::uuid(), $rescan->prepare(['suggestions.php'], $suggestionEvidence));
    $check(count($reappeared['created']) === 1, 'Reappearing archived source is a new finding');
    $check($editor->inspect($reappeared['created'][0], 'en')['finding']['suggested_key'] === $suggestions[0].'_3', 'Archived suggestions must remain reserved');

    echo json_encode(['checks' => $checks, 'state' => 'passed', 'production_connected' => false]).PHP_EOL;
} finally {
    if ($started) {
        $runProcess([$bin.'/pg_ctl', '-D', $dir.'/data', '-m', 'immediate', '-w', 'stop']);
    }
}
