<?php

use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Generation\RevisionReviewContext;
use Gunreip\TranslationWorkbench\Generation\WorkInProgress;
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = new Application($root);
$app->instance('config', new Repository(['translation-workbench' => [
    'allowed_first_translation_key_segments' => ['ui'],
]]));
$bin = '/usr/lib/postgresql/18/bin';
$dir = sys_get_temp_dir().'/tw-work-list-'.bin2hex(random_bytes(8));
mkdir($dir, 0700);
mkdir($dir.'/socket', 0700);
$run = static fn ($cmd) => (new Process($cmd))->mustRun();
$checks = 0;
$started = false;
$check = static function ($ok, string $message) use (&$checks) {
    if (! $ok) {
        throw new RuntimeException($message);
    } $checks++;
};
try {
    $run([$bin.'/initdb', '-D', $dir.'/data', '-U', 'tester', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']);
    file_put_contents($dir.'/data/postgresql.conf', "\nlisten_addresses=''\nunix_socket_directories='".$dir."/socket'\n");
    $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-l', $dir.'/log', '-w', 'start']);
    $started = true;
    $run([$bin.'/createdb', '-h', $dir.'/socket', '-U', 'tester', 'editor']);
    $db = new PDO('pgsql:host='.$dir.'/socket;dbname=editor;user=tester', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    (new WorkingGenerationSchema)->install($db, 'editor', 'current_generation_v2', (string) Str::uuid());
    $db->exec('CREATE TABLE public.translation_workbench_findings(id int PRIMARY KEY); INSERT INTO public.translation_workbench_findings VALUES(777)');
    $db->exec('SET search_path TO current_generation_v2,pg_catalog');
    $q = static function ($sql, $params = []) use ($db) {
        $s = $db->prepare($sql);
        $s->execute($params);

        return $s;
    };

    $scan = (string) Str::uuid();
    $q("INSERT INTO translation_workbench_scan_runs VALUES(?,'completed',?::jsonb,now())", [$scan, json_encode(['mode' => 'generation_rescan'])]);
    $q("INSERT INTO translation_workbench_operations VALUES(?, 'complete_rescan', ?, '{}',now())", [$scan, str_repeat('b', 64)]);
    $file = (int) $q("INSERT INTO translation_workbench_source_files(path) VALUES('fixture.php') RETURNING id")->fetchColumn();
    $create = static fn ($line, $key = null, $predecessor = null) => (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_source_code_literal,raw_literal,suggested_key,provenance,predecessor_id) VALUES(?,?,?,1,?,?,?,'fixture.source.example','{}',?) RETURNING id",
        [$file, $scan, $line, "__('".($key ?? 'Example')."')", $key ?? 'Example', $key === null ? 'Example' : null, $predecessor])->fetchColumn();
    $one = $create(1);
    $two = $create(2);
    $three = $create(3);
    $editor = new GenerationEditor($db);
    $work = new WorkInProgress($db);
    $first = $editor->assign($one, 0, null, 7, 'Assign first', 'ui.button.example');
    $key = $first['key'];
    $editor->assign($two, $key, null, 7, 'Share the key');
    $other = $editor->assign($three, 0, null, 8, 'Other user', 'ui.button.other');
    $check($work->page('de', 7, '', 1)->total() === 1, 'Shared findings must form one task');
    $check($work->page('de', null, '', 1)->total() === 2, 'All users must include both tasks');
    $row = $work->recent('de', 7)[0];
    $check((int) $row['finding_count'] === 2 && $row['step'] === 'translations', 'Initial saved assignment must be actionable');
    $check($work->page('de', 7, (string) $two, 1)->total() === 1, 'Find every shared member by finding ID');
    $check($work->page('de', 7, 'fixture.php', 1)->total() === 1, 'Find source path');
    $lastEvent = $row['event_id'];
    $event = static function ($type, $details, $actor = 7) use ($q) {
        $operation = (string) Str::uuid();
        $q("INSERT INTO translation_workbench_operations VALUES(?, 'fixture', ?, '{}',now())", [$operation, str_repeat('a', 64)]);
        $q("INSERT INTO translation_workbench_events(operation_id,event_type,actor_id,reason,details) VALUES(?,?,?,'Fixture',?::jsonb)", [$operation, $type, $actor, json_encode($details)]);
    };
    $event('finding_confirmed', ['key' => $key, 'finding' => $one]);
    $check($work->recent('de', 7)[0]['event_id'] === $lastEvent, 'Scanner confirmation must not reorder manual work');
    $q("UPDATE translation_workbench_findings SET status='obsolete', retired_at=now(),retired_reason='Moved' WHERE id=?", [$one]);
    $q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$one]);
    $successor = $create(4, 'ui.button.example', $one);
    $q('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES(?,?)', [$successor, $key]);
    $row = $work->find($key, 'de');
    $check((int) $row['finding_count'] === 2 && (int) $row['finding_id'] === $two, 'Choose unfinished active member before replaced successor');
    $check($work->page('de', 7, (string) $one, 1)->total() === 1, 'Old finding ID must still locate shared work after move');
    $check($row['event_id'] === $lastEvent, 'A move must preserve the last manual edit');
    $draft = $editor->save($two, $key, (int) $q('SELECT id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$two])->fetchColumn(), 'de', 'Beispiel', null, 7, 'Save draft');
    $check($work->recent('de', 7)[0]['event_id'] > $lastEvent, 'Draft event must advance manual order via revision-to-key mapping');
    $check($work->find($key, 'de')['step'] === 'translations', 'Missing English remains a task');
    $q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason='Removed' WHERE id IN (?,?)", [$two, $successor]);
    $check($work->page('de', 7, '', 1)->total() === 0, 'Keys without active members leave the active queue');
    $check($work->recent('de', 7)[0]['step'] === 'archived', 'Archived work remains accessible from recent work');

    $doneFinding = $create(5, 'ui.button.done');
    $done = $editor->assign($doneFinding, 0, null, 7, 'Start complete workflow', 'ui.button.done');
    $q('INSERT INTO translation_workbench_scan_findings VALUES(?,?)', [$scan, $doneFinding]);
    $drafts = $editor->saveDrafts($doneFinding, $done['key'], $done['link'], [
        'en' => ['value' => 'Done', 'revision' => null],
        'de' => ['value' => 'Fertig', 'revision' => null],
    ], 7, 'Save both languages');
    $expected = [];
    foreach ($drafts as $draft) {
        $revision = $draft['revision'];
        $snapshot = RevisionReviewContext::snapshot($db, $revision);
        $expected[$revision] = $snapshot['fingerprint'];
    }
    $editor->reviewWorklist()->submit($expected, 7);
    $check($work->find($done['key'], 'de')['step'] === 'review', 'Submitted drafts should continue in Review');
    $editor->reviewMany($expected, true, 7, 'Approve both languages');
    $check($work->find($done['key'], 'de')['step'] === 'publication', 'Approved drafts should continue in Publication');
    mkdir($dir.'/lang', 0700);
    $publisher = new GenerationPublisher($db, $dir.'/lang');
    $publication = (string) Str::uuid();
    $plan = $publisher->prepare($publication, array_keys($expected), 7, 'Publish both languages');
    $check($work->find($done['key'], 'de')['open_publication'] === $publication, 'Resume the prepared run instead of preparing twice');
    // Scanner evidence must include completed publications without requiring imported baseline values.
    $readEvidence = static function () use ($app, $dir, $db, $root) {
        $app->setBasePath($dir);
        $app->useLangPath($dir.'/lang');
        try {
            return (new \Gunreip\TranslationWorkbench\Generation\GenerationDatabase)->languageEvidence($db);
        } finally {
            $app->setBasePath($root);
            $app->useLangPath($root.'/lang');
        }
    };
    $check(!isset($readEvidence()['ui.button.done']), 'Prepared publications are not scanner evidence');
    $publisher->publish($publication, $plan['fingerprint']);
    $check(($readEvidence()['ui.button.done']['value'] ?? null) === 'Done', 'Completed publication supplies verified English scanner evidence');
    $englishFile = $dir.'/lang/en/ui.php';
    $englishBytes = file_get_contents($englishFile);
    file_put_contents($englishFile, "<?php return ['button' => ['done' => 'Changed externally']];");
    $check(!isset($readEvidence()['ui.button.done']), 'Changed language values cannot confirm a source key');
    file_put_contents($englishFile, $englishBytes);

    $check($work->find($done['key'], 'de')['step'] === 'complete', 'Published languages and scan-confirmed source complete the task');
    $check($work->page('de', 7, '', 1)->total() === 0, 'Completed task must leave In progress');
    $check($work->recent('de', 7)[0]['step'] === 'complete', 'Completed task stays in recent work');
    $check($work->find($done['key'], 'fr')['step'] === 'translations', 'An untranslated target language must remain visible');

    $sourceRun = (string) Str::uuid();
    $sourceFinish = (string) Str::uuid();
    $q("INSERT INTO translation_workbench_operations VALUES(?, 'source_replacement_prepared', ?, ?::jsonb,now())", [
        $sourceRun, str_repeat('c', 64), json_encode(['actor' => 7, 'finish' => $sourceFinish, 'preview' => ['key_id' => $done['key']]]),
    ]);
    $check($work->find($done['key'], 'de')['open_source'] === $sourceRun, 'Open source plan must be resumable');
    $check($work->page('de', 7, '', 1)->total() === 1, 'An open source plan stays visible even if saved records look complete');
    $q("INSERT INTO translation_workbench_operations VALUES(?, 'source_replacement_closed', ?, '{}',now())", [$sourceFinish, str_repeat('d', 64)]);
    $check($work->find($done['key'], 'de')['open_source'] === null, 'Finished source plan must stop being offered');
    $editor->save($doneFinding, $done['key'], $done['link'], 'de', 'Erledigt', $drafts['de']['revision'], 7, 'Change the translation');
    $check($work->find($done['key'], 'de')['step'] === 'translations', 'A changed published translation must reopen work');

    for ($index = 0; $index < 12; $index++) {
        $member = $create(10 + $index);
        $editor->assign($member, 0, null, 7, 'Start another task', 'ui.task.item'.$index);
    }
    $check(count($work->recent('de', 7)) === 5, 'Recent work is limited to five keys');
    $pageOne = $work->page('de', 7, '', 1, 10);
    $pageTwo = $work->page('de', 7, '', 2, 10);
    $check($pageOne->total() === 13 && count($pageTwo->items()) === 3, 'Pagination must cover all unfinished keys');
    $check(array_intersect(array_column($pageOne->items(), 'key_id'), array_column($pageTwo->items(), 'key_id')) === [], 'Pages must not repeat work items');
    $check($work->page('de', 7, '%', 1)->total() === 0, 'Search wildcards must be literal');
    $check($q('SELECT count(*) FROM public.translation_workbench_findings')->fetchColumn() == 1, 'Legacy table untouched');
    echo json_encode(['checks' => $checks, 'state' => 'passed', 'production_connected' => false]).PHP_EOL;
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-m', 'immediate', '-w', 'stop']);
    }
}
