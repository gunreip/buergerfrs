<?php

use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
new Application($root);
$bin = '/usr/lib/postgresql/18/bin';
$dir = sys_get_temp_dir().'/tw-editor-'.bin2hex(random_bytes(8));
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
    $q("INSERT INTO translation_workbench_scan_runs VALUES(?,'completed','{}',now())", [$scan]);
    $file = $q("INSERT INTO translation_workbench_source_files(path) VALUES('fixture.php') RETURNING id")->fetchColumn();
    $create = static fn ($line, $expression = 'example') => (int) $q("INSERT INTO translation_workbench_findings(source_file_id,captured_scan_id,source_line,source_column,raw_expression,raw_literal,suggested_key,provenance) VALUES(?,?,?,1,?,'Example','fixture.source.example','{}') RETURNING id", [$file, $scan, $line, $expression])->fetchColumn();
    $one = $create(1);
    $two = $create(2);
    $raw = $q('SELECT raw_expression,raw_literal,suggested_key,provenance FROM translation_workbench_findings WHERE id=?', [$one])->fetch(PDO::FETCH_ASSOC);
    $editor = new GenerationEditor($db);
    $first = $editor->assign($one, 0, null, 7, 'Create first key', 'ui.button.example');
    $key = $first['key'];
    $link = $first['link'];
    $check($editor->inspect($one, 'en')['finding']['key_id'] == $key, 'Assignment not visible');
    $reject(fn () => $editor->sourcePreview($one, $key, 999999, 'en'), 'assignment changed');
    $editor->assign($two, $key, null, 7, 'Shared translation');
    $check($editor->assignmentReasons() === ['Shared translation', 'Create first key'], 'Assignment reasons include saved assignments in recent-first order');
    $check($editor->inspect($one, 'en')['usageCount'] === 2, 'Findings were not independently linked');
    // Sorting is performed before pagination and never changes stored records.
    $db->beginTransaction();
    $sortIds = [$one, $two];
    for ($line = 3; $line <= 30; $line++) {
        $sortIds[] = $create($line, match ($line) { 3 => 'Apple', 4 => 'Zebra', default => 'example' });
    }
    $ids = static fn ($page) => array_map('intval', array_column($page->items(), 'id'));
    $smallPage = $editor->page('', '', 'active', 2, 'id', 'asc', 10);
    $check($smallPage->perPage() === 10 && $ids($smallPage) === array_slice($sortIds, 10, 10), 'Selected page size must control limit and offset');
    $check(count($editor->page('', '', 'active', 1, 'id', 'asc', 50)->items()) === 30, 'Larger page must include all matching rows');
    $check($editor->page('', '', 'active', 1, 'id', 'asc', 0)->perPage() === 25, 'Invalid page size must use the default');
    $check($ids($editor->page('', '', 'active', 1, 'id', 'asc')) === array_slice($sortIds, 0, 25), 'ID sorting must precede pagination');
    $check($ids($editor->page('', '', 'active', 2, 'id', 'asc')) === array_slice($sortIds, 25), 'Second sorted page has gaps or duplicates');
    $check($ids($editor->page('', '', 'active', 1)) === array_slice(array_reverse($sortIds), 0, 25), 'Default newest-first order changed');
    $check($ids($editor->page('', '', 'active', 1, 'source', 'asc')) === array_slice($sortIds, 0, 25), 'Source line order must be numeric');
    $check($ids($editor->page('', '', 'active', 1, 'expression', 'asc'))[0] === $sortIds[2], 'Expression ascending sort failed');
    $check($ids($editor->page('', '', 'active', 1, 'expression', 'desc'))[0] === $sortIds[29], 'Expression descending sort failed');
    foreach (['asc', 'desc'] as $direction) {
        $keyPage = $editor->page('', '', 'active', 1, 'key', $direction)->items();
        $check($keyPage[0]['full_name'] === 'ui.button.example' && $keyPage[2]['full_name'] === null, 'Unassigned keys must sort last in both directions');
    }
    $check($ids($editor->page('Apple', '', 'active', 1, 'source', 'desc')) === [$sortIds[2]], 'Sorting lost the search filter');
    $check($ids($editor->page('', (string) $one, 'active', 1, 'key', 'asc')) === [$one], 'Sorting lost the ID filter');
    $reject(fn () => $editor->page('', '', 'all', 1, 'id; DROP TABLE x', 'asc'), 'Invalid findings sort');
    $reject(fn () => $editor->page('', '', 'all', 1, 'id', 'desc; SELECT 1'), 'Invalid findings sort');
    $db->rollBack();

    $before = $q('SELECT count(*) FROM translation_workbench_events')->fetchColumn();
    $editor->assign($one, $key, $link, 7, 'Same assignment');
    $check($q('SELECT count(*) FROM translation_workbench_events')->fetchColumn() == $before, 'Repeated assignment added an event');
    $reject(fn () => $editor->assign($one, $key, null, 7, 'Stale dialog'), 'assignment changed');
    $reject(fn () => $editor->assign($one, 99999, $link, 7, 'Missing key'), 'Unknown key');
    $reject(fn () => $editor->assign($one, 0, $link, 7, 'Duplicate key', 'ui.button.example'), 'already exists');
    $reject(fn () => $editor->assign($one, 0, $link, 7, 'Bad hierarchy', 'ui.button.example.extra'), 'conflicts');
    $reject(fn () => $editor->assign($one, 0, $link, 7, 'Too short', 'ui.type'), 'at least two');
    $check($q('SELECT count(*) FROM translation_workbench_keys')->fetchColumn() == 1, 'Rejected creation leaked a key');
    $rev = $editor->save($one, $key, $link, 'en', '0', null, 7, 'First draft')['revision'];
    $check($editor->inspect($one, 'en')['revision']['value'] === '0', 'Zero was lost');
    $check($editor->inspect($two, 'en')['revision']['value'] === '0', 'Shared key revision is not shared');
    $reject(fn () => $editor->save($one, $key, $link, 'en', 'wrong', null, 8, 'Stale dialog'), 'Revision context changed');
    $same = $editor->save($one, $key, $link, 'en', '0', $rev, 7, 'Same value');
    $check($same['revision'] === $rev, 'Identical save duplicated revision');
    $review = $editor->inspect($one, 'en')['review'];
    $check(! $review['approved'] && $review['latest'] === null, 'Unsaved approval was invented');
    $editor->review($rev, $review['fingerprint'], true, 7, 'Reviewed shared English value');
    $approved = $editor->inspect($two, 'en')['review'];
    $check($approved['approved'] && $approved['latest']['actor_id'] === 7, 'Shared approval not visible');
    $reject(fn () => $editor->review($rev, $review['fingerprint'], false, 8, 'Old confirmation'), 'Review context changed');
    $editor->review($rev, $approved['fingerprint'], false, 7, 'Revoke reviewed value');
    $revoked = $editor->inspect($one, 'en')['review'];
    $check(! $revoked['approved'] && $revoked['latest']['decision'] === 'revoked', 'Revocation not visible');
    $editor->review($rev, $revoked['fingerprint'], true, 7, 'Approve again');
    $third = $create(3);
    $editor->assign($third, $key, null, 7, 'Additional active source');
    $changed = $editor->inspect($one, 'en')['review'];
    $check(! $changed['approved'], 'Changed source links did not invalidate approval');
    $editor->review($rev, $changed['fingerprint'], true, 7, 'Review additional source');
    $beforeEdit = $editor->inspect($one, 'en')['review'];
    $empty = $editor->save($one, $key, $link, 'en', '', $rev, 7, 'Intentionally empty')['revision'];
    $check($editor->inspect($one, 'en')['revision']['value'] === '', 'Empty value was lost');
    $check(! $editor->inspect($one, 'en')['review']['approved'], 'New draft inherited approval');
    $reject(fn () => $editor->review($rev, $beforeEdit['fingerprint'], true, 7, 'Stale revision'), 'Review context changed');
    $emptyReview = $editor->inspect($one, 'en')['review'];
    $editor->review($empty, $emptyReview['fingerprint'], true, 7, 'Approve intentional empty value');
    $check($editor->inspect($one, 'en')['review']['approved'], 'Intentional empty value cannot be approved');
    $check($q('SELECT count(*) FROM translation_workbench_value_revisions')->fetchColumn() == 2, 'Wrong revision count');
    // The actual publication engine writes only inside this isolated test directory.
    mkdir($dir.'/lang');
    mkdir($dir.'/lang/en');
    $path = $dir.'/lang/en/ui.php';
    $original = "<?php\n// Keep translator notes\nreturn ['button' => ['example' => 'Before', 'other' => 'Untouched']];\n";
    file_put_contents($path, $original);
    chmod($path, 0640);
    $publisher = new GenerationPublisher($db, $dir.'/lang');
    $approvedEmpty = $editor->inspect($one, 'en')['review'];
    $reject(fn () => $publisher->prepare((string) Str::uuid(), [$empty], 7, 'Stale preview', [str_repeat('0', 64)]), 'Review changed');
    $plan = $publisher->prepare((string) Str::uuid(), [$empty], 7, 'Publish empty value', [$approvedEmpty['fingerprint']]);
    $preview = $publisher->inspect($plan['run']);
    $check($preview['changes'][0]['before'] === 'Before' && $preview['changes'][0]['after'] === '', 'Preview lost old or empty value');
    $check(file_get_contents($path) === $original, 'Prepare wrote file');
    $check(count($publisher->recent()) === 1, 'Prepared run not recoverable');
    $publisher->publish($plan['run'], $plan['fingerprint']);
    $check(file_get_contents($path) === str_replace("'Before'", "''", $original), 'Unrelated formatting or values changed');
    clearstatcache(true, $path);
    $check((fileperms($path) & 0777) === 0640, 'Original permissions changed');
    $check($publisher->inspect($plan['run'])['state'] === 'complete', 'Missing completed receipt');
    $next = $editor->save($one, $key, $link, 'en', 'Next', $empty, 7, 'Next draft')['revision'];
    $editor->review($next, $editor->inspect($one, 'en')['review']['fingerprint'], true, 7, 'Approve next');
    $pending = $publisher->prepare((string) Str::uuid(), [$next], 7, 'Publish next');
    file_put_contents($path, $original.'// External edit');
    $reject(fn () => $publisher->publish($pending['run'], $pending['fingerprint']), 'External file change');
    $check($publisher->inspect($pending['run'])['state'] === 'interrupted', 'Failure not persisted');
    $closure = $publisher->closurePreview($pending['run']);
    $publisher->close($pending['run'], $closure['fingerprint'], 7, 'Keep external change');
    $check(file_get_contents($path) === $original.'// External edit', 'Closure changed file');
    $recovery = $publisher->prepare((string) Str::uuid(), [$next], 7, 'Recoverable publication');
    $reject(fn () => $publisher->publish($recovery['run'], $recovery['fingerprint'], function () {
        throw new RuntimeException('Simulated interruption');
    }), 'Simulated interruption');
    $written = file_get_contents($path);
    $publisher->publish($recovery['run'], $recovery['fingerprint'], function ($stage) {
        if ($stage === 'after_write') {
            throw new RuntimeException('Must not rewrite');
        }
    });
    $check(file_get_contents($path) === $written && $publisher->inspect($recovery['run'])['state'] === 'complete', 'Resume did not retain written file');
    $check((int) $q('SELECT count(*) FROM translation_workbench_file_reservations')->fetchColumn() === 0, 'Reservations leaked');
    $empty = $next;
    $other = $editor->assign($one, 0, $link, 7, 'Separate key', 'ui.label.example');
    $reject(fn () => $editor->save($one, $key, $link, 'en', 'wrong', $empty, 7, 'Stale key'), 'assignment changed');
    $check($editor->inspect($two, 'en')['finding']['key_id'] == $key, 'Other finding reassigned');
    $check($editor->inspect($one, 'en')['revision'] === null, 'Draft copied between keys');
    $check($q('SELECT raw_expression,raw_literal,suggested_key,provenance FROM translation_workbench_findings WHERE id=?', [$one])->fetch(PDO::FETCH_ASSOC) === $raw, 'Immutable capture changed');
    $q("UPDATE translation_workbench_findings SET status='obsolete',retired_at=now(),retired_reason='Test removal' WHERE id=?", [$one]);
    $q('UPDATE translation_workbench_key_findings SET valid_to=now() WHERE finding_id=? AND valid_to IS NULL', [$one]);
    $reject(fn () => $editor->assign($one, $key, null, 7, 'Archived'), 'no longer active');
    $reject(fn () => $editor->save($one, $other['key'], $other['link'], 'en', 'x', null, 7, 'Archived'), 'no longer active');
    $check($editor->page('', '', 'active', 1)->total() === 2, 'Active filter');
    $check($editor->page('', '', 'obsolete', 1)->total() === 1, 'Archived filter');
    $check($editor->page('', (string) $two, 'active', 1)->total() === 1, 'ID filter');
    $check($editor->page('%', '', 'all', 1)->total() === 0, 'Search wildcards not escaped');
    $check($q('SELECT count(*) FROM public.translation_workbench_findings')->fetchColumn() == 1, 'Touched legacy table');
    $check(count($editor->inspect($two, 'en')['history']) >= 3, 'Working history not visible');
    echo json_encode(['checks' => $checks, 'state' => 'passed', 'production_connected' => false]).PHP_EOL;
} finally {
    if ($started) {
        $run([$bin.'/pg_ctl', '-D', $dir.'/data', '-m', 'immediate', '-w', 'stop']);
    }
}
