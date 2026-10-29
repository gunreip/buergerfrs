<?php

// Explicit runner. Own temporary PostgreSQL cluster; no application bootstrap or DB credentials.
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\PostgresBaseline;
use Tests\Support\TranslationWorkbench\PostgresPublisher;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$mode = $argv[1] ?? 'launch';
if ($mode === 'launch') {
    $bin = getenv('TW_TEST_POSTGRES_BIN') ?: '/usr/lib/postgresql/18/bin';
    $directory = sys_get_temp_dir().'/tw-baseline-pg-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    mkdir($directory.'/socket', 0700);
    $started = false;
    try {
        (new Process([$bin.'/initdb', '-D', $directory.'/data', '-U', 'workbench_test', '--auth-local=trust', '--auth-host=reject', '--no-locale', '--encoding=UTF8']))->mustRun();
        file_put_contents($directory.'/data/postgresql.conf', "\nlisten_addresses = ''\nunix_socket_directories = '".$directory."/socket'\nunix_socket_permissions = 0700\n", FILE_APPEND);
        try {
            (new Process([$bin.'/pg_ctl', '-D', $directory.'/data', '-l', $directory.'/server.log', '-w', 'start']))->mustRun();
        } catch (Throwable $error) {
            if (is_file($directory.'/server.log')) {
                fwrite(STDERR, file_get_contents($directory.'/server.log'));
            }
            throw $error;
        }
        $started = true;
        (new Process([$bin.'/createdb', '-h', $directory.'/socket', '-U', 'workbench_test', 'baseline_test']))->mustRun();
        $test = new Process([PHP_BINARY, __FILE__, 'run', $directory]);
        $test->setTimeout(90);
        $test->mustRun(static fn ($type, $buffer) => print ($buffer));
        // Restart the actual server, then retry from another fresh PHP process.
        $checkpoint = json_decode(file_get_contents($directory.'/restart.json'), true, flags: JSON_THROW_ON_ERROR);
        (new Process([$bin.'/pg_ctl', '-D', $directory.'/data', '-m', 'fast', '-w', 'stop']))->mustRun();
        $started = false;
        (new Process([$bin.'/pg_ctl', '-D', $directory.'/data', '-l', $directory.'/server.log', '-w', 'start']))->mustRun();
        $started = true;
        $restart = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'baseline-server-restart', json_encode($checkpoint['payload'])]);
        $restart->mustRun();
        if (json_decode($restart->getOutput(), true, flags: JSON_THROW_ON_ERROR) !== $checkpoint['result']) {
            throw new RuntimeException('Receipt changed across PostgreSQL restart');
        }
        $verification = new PostgresBaseline($directory);
        $count = (int) $verification->query('SELECT count(*) FROM translation_workbench_events WHERE operation_id=?', [$checkpoint['payload']['args'][0]])->fetchColumn();
        if ($count !== 1) {
            throw new RuntimeException('PostgreSQL restart retry duplicated the event');
        }
        $reviewCheckpoint = json_decode(file_get_contents($directory.'/review-restart.json'), true, flags: JSON_THROW_ON_ERROR);
        $reviewRestart = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'review-server-restart', json_encode($reviewCheckpoint['payload'])]);
        $reviewRestart->mustRun();
        if (json_decode($reviewRestart->getOutput(), true, flags: JSON_THROW_ON_ERROR) !== $reviewCheckpoint['result']) {
            throw new RuntimeException('Review receipt changed across PostgreSQL restart');
        }
        if ((int) $verification->query('SELECT count(*) FROM translation_workbench_events WHERE operation_id=?', [$reviewCheckpoint['payload']['args'][0]])->fetchColumn() !== 1) {
            throw new RuntimeException('Review restart duplicated history');
        }
        $publication = json_decode(file_get_contents($directory.'/publication-restart.json'), true, flags: JSON_THROW_ON_ERROR);
        $resume = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'publication-server-restart',
            json_encode(['method' => 'publish', 'args' => [$publication['run'], $publication['fingerprint']]])]);
        $resume->mustRun();
        $result = json_decode($resume->getOutput(), true, flags: JSON_THROW_ON_ERROR);
        if (($result['result']['kind'] ?? null) !== 'completed') {
            throw new RuntimeException('Publication restart failed: '.$resume->getOutput());
        }
        if (filemtime($directory.'/publication-files/de/publication.php') !== 1000000000
            || ! is_file($directory.'/publication-files/en/publication.php')) {
            throw new RuntimeException('Resume rewrote completed file or missed pending file');
        }
        if ((int) $verification->query('SELECT count(*) FROM translation_workbench_file_reservations WHERE run_id=?', [$publication['run']])->fetchColumn() !== 0
            || (int) $verification->query('SELECT count(*) FROM translation_workbench_events WHERE operation_id=?', [$publication['run']])->fetchColumn() !== 1) {
            throw new RuntimeException('Publication restart receipt/reservations invalid');
        }
        $optionCheckpoint = json_decode(file_get_contents($directory.'/option-restart.json'), true, flags: JSON_THROW_ON_ERROR);
        $optionRestart = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'option-server-restart', json_encode($optionCheckpoint['payload'])]);
        $optionRestart->mustRun();
        if (json_decode($optionRestart->getOutput(), true, flags: JSON_THROW_ON_ERROR) !== $optionCheckpoint['result']) {
            throw new RuntimeException('Option retry changed across server restart');
        }
        if ((int) $verification->query('SELECT count(*) FROM translation_workbench_events WHERE operation_id=?', [$optionCheckpoint['payload']['args'][0]])->fetchColumn() !== 1) {
            throw new RuntimeException('Option restart duplicated event');
        }
        unset($verification);
        echo json_encode(['case' => 'postgresql-server-restart', 'result' => 'passed', 'assertions' => 9]).PHP_EOL;
    } finally {
        if ($started) {
            (new Process([$bin.'/pg_ctl', '-D', $directory.'/data', '-m', 'immediate', '-w', 'stop']))->mustRun();
        }
        (new Filesystem)->deleteDirectory($directory);
    }
    exit;
}
if (! in_array($mode, ['run', 'worker'], true)) {
    throw new RuntimeException('Unknown test mode');
}
$directory = $argv[2] ?? '';
$db = new PostgresBaseline($directory, $mode === 'worker' ? $argv[3] : 'baseline-main');
if ($mode === 'worker') {
    $payload = json_decode($argv[4], true, flags: JSON_THROW_ON_ERROR);
    if (! in_array($payload['method'], ['save', 'assign', 'decide', 'capture', 'publish', 'publish-crash'], true)) {
        throw new RuntimeException('Unsupported worker action');
    }
    try {
        if ($payload['method'] === 'publish-crash') {
            (new PostgresPublisher($directory))->publish(...[...$payload['args'], static function ($stage) {
                if ($stage === 'after_write') {
                    exit(73); // Abrupt process exit: no publisher catch/rollback handling.
                }
            }]);
            throw new RuntimeException('Crash injection did not run');
        }
        $target = $payload['method'] === 'publish' ? new PostgresPublisher($directory) : $db;
        echo json_encode(['result' => $target->{$payload['method']}(...$payload['args'])], JSON_THROW_ON_ERROR);
    } catch (Throwable $error) {
        echo json_encode(['error' => $error->getMessage()], JSON_THROW_ON_ERROR);
    }
    exit;
}
$db->db->exec(file_get_contents(dirname(__DIR__, 3).'/packages/gunreip/laravel-translation-workbench/database/generations/v1/baseline.sql'));
$count = 0;
$assert = function (bool $condition, string $message) use (&$count) {
    if (! $condition) {
        throw new RuntimeException($message);
    }
    $count++;
};
$reject = function (callable $action, string $message) use ($assert) {
    try {
        $action();
    } catch (Throwable $error) {
        $assert(str_contains($error->getMessage(), $message), 'Unexpected rejection: '.$error->getMessage());

        return;
    }
    throw new RuntimeException('Expected rejection: '.$message);
};
$uuid = fn () => (string) Str::uuid();
$scalar = fn ($sql, $params = []) => $db->query($sql, $params)->fetchColumn();
$key = function (string $name) use ($db): int {
    $id = (int) $db->query('INSERT INTO translation_workbench_keys DEFAULT VALUES RETURNING id')->fetchColumn();
    $db->query('INSERT INTO translation_workbench_key_names(key_id,full_name) VALUES (?,?)', [$id, $name]);

    return $id;
};
$first = $db->capture($uuid(), 'example.blade.php', 1, 'Cancel')['finding'];
$events = (int) $scalar('SELECT count(*) FROM translation_workbench_events');
$assert($db->capture($uuid(), 'example.blade.php', 1, 'Cancel')['finding'] === $first, 'Rescan must keep identity');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events') === $events, 'Rescan created event');
$keyA = $key('ui.button.cancel');
$keyB = $key('ui.action.cancel');
$link = $db->assign($uuid(), $first, $keyA, null, 7, 'Select translation')['link'];
$moved = $db->capture($uuid(), 'example.blade.php', 5, 'Cancel', $first)['finding'];
$assert($moved !== $first && $scalar('SELECT status FROM translation_workbench_findings WHERE id=?', [$first]) === 'obsolete', 'Movement must archive predecessor');
$assert((int) $scalar('SELECT key_id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$moved]) === $keyA, 'Movement lost active assignment');
$reject(fn () => $db->capture($uuid(), 'example.blade.php', 8, 'Cancel', $first), 'No valid movement');
$reject(fn () => $db->query("UPDATE translation_workbench_findings SET suggested_key='changed' WHERE id=?", [$moved]), 'Immutable capture');
$reject(fn () => $db->query("UPDATE translation_workbench_findings SET status='active',retired_at=NULL,retired_reason=NULL WHERE id=?", [$first]), 'Archived finding');
$copy = $db->capture($uuid(), 'example.blade.php', 10, 'Cancel')['finding'];
$assert($copy !== $moved && $scalar('SELECT predecessor_id FROM translation_workbench_findings WHERE id=?', [$copy]) === null, 'Copy must be independent');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_key_findings WHERE finding_id=?', [$copy]) === 0, 'Copy must not inherit link');
$reject(fn () => $db->query('INSERT INTO translation_workbench_key_findings(finding_id,key_id) VALUES (?,?)', [$moved, $keyB]), 'current_assignment');
$op = $uuid();
$args = [$op, $keyA, 'en', 'Cancel', null, 7, 'Initial text'];
$revision = $db->save(...$args)['revision'];
$reject(fn () => $db->query("UPDATE translation_workbench_value_revisions SET value='Overwrite' WHERE id=?", [$revision]), 'Immutable');
$reject(fn () => $db->query('DELETE FROM translation_workbench_value_revisions WHERE id=?', [$revision]), 'Immutable');
$events = (int) $scalar('SELECT count(*) FROM translation_workbench_events');
$assert($db->save($uuid(), $keyA, 'en', 'Cancel', $revision, 7, 'No change')['revision'] === $revision, 'Unchanged save created revision');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events') === $events, 'No-op created event');
$other = $db->save($uuid(), $keyB, 'en', 'Other', null, 7, 'Other key')['revision'];
$reject(fn () => $db->query('UPDATE translation_workbench_value_slots SET current_revision_id=? WHERE key_id=?', [$revision, $keyB]), 'revision_same_slot');

// Fresh process, not just a second PDO object: durable idempotency result.
$restart = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'baseline-restart', json_encode(['method' => 'save', 'args' => $args])]);
$restart->mustRun();
$assert(json_decode($restart->getOutput(), true)['result']['revision'] === $revision, 'Retry across process lost receipt');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events') === $events + 1, 'Retry created extra event');
$changed = $args;
$changed[3] = 'Different';
$reject(fn () => $db->save(...$changed), 'Operation request changed');

// Recorder failure must roll back revision, pointer, operation and event together.
$db->db->exec("CREATE FUNCTION fail_event() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''Recorder failed''; END'; CREATE TRIGGER fail_event BEFORE INSERT ON translation_workbench_events FOR EACH ROW EXECUTE FUNCTION fail_event()");
$failed = $uuid();
$before = (int) $scalar('SELECT count(*) FROM translation_workbench_value_revisions');
$reject(fn () => $db->save($failed, $keyA, 'en', 'Changed', $revision, 7, 'Change'), 'Recorder failed');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_value_revisions') === $before, 'Failed recorder left revision');
$assert((int) $scalar('SELECT current_revision_id FROM translation_workbench_value_slots WHERE key_id=?', [$keyA]) === $revision, 'Failed recorder moved pointer');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_operations WHERE id=?', [$failed]) === 0, 'Failed operation left receipt');
$db->db->exec('DROP TRIGGER fail_event ON translation_workbench_events; DROP FUNCTION fail_event()');

// Hold a parent row until both processes demonstrably wait on PostgreSQL locks.
$race = function (string $lockSql, array $params, array $payloads) use ($db, $directory, $assert): array {
    $workers = [];
    $db->db->beginTransaction();
    try {
        $db->query($lockSql, $params);
        foreach ($payloads as $i => $payload) {
            $worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'baseline-race-'.$i, json_encode($payload)]);
            $worker->setTimeout(20);
            $worker->start();
            $workers[] = $worker;
        }
        $deadline = microtime(true) + 5;
        do {
            $db->query('SELECT pg_stat_clear_snapshot()');
            $waiting = (int) $db->query("SELECT count(*) FROM pg_stat_activity WHERE application_name LIKE 'baseline-race-%' AND wait_event_type='Lock'")->fetchColumn();
            if ($waiting === 2) {
                break;
            }
            usleep(20000);
        } while (microtime(true) < $deadline);
        $assert($waiting === 2, 'Both workers must demonstrably wait on locks');
        $db->db->commit();
        $results = [];
        foreach ($workers as $worker) {
            $worker->wait();
            $assert($worker->isSuccessful(), $worker->getErrorOutput());
            $results[] = json_decode($worker->getOutput(), true, flags: JSON_THROW_ON_ERROR);
        }

        return $results;
    } finally {
        if ($db->db->inTransaction()) {
            $db->db->rollBack();
        }
        foreach ($workers as $worker) {
            if ($worker->isRunning()) {
                $worker->stop();
            }
        }
    }
};
$results = $race('SELECT id FROM translation_workbench_findings WHERE id=? FOR UPDATE', [$copy], [
    ['method' => 'assign', 'args' => [$uuid(), $copy, $keyA, null, 7, 'Choice A']],
    ['method' => 'assign', 'args' => [$uuid(), $copy, $keyB, null, 8, 'Choice B']],
]);
$assert(count(array_filter($results, fn ($r) => isset($r['result']))) === 1, 'Two assignments succeeded');
$assert(count(array_filter($results, fn ($r) => ($r['error'] ?? '') === 'Assignment context changed')) === 1, 'Stale assignment not detected');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$copy]) === 1, 'Multiple current assignments');
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$keyA], [
    ['method' => 'save', 'args' => [$uuid(), $keyA, 'en', 'Text A', $revision, 7, 'Edit A']],
    ['method' => 'save', 'args' => [$uuid(), $keyA, 'en', 'Text B', $revision, 8, 'Edit B']],
]);
$assert(count(array_filter($results, fn ($r) => isset($r['result']))) === 1, 'Two stale saves succeeded');
$assert(count(array_filter($results, fn ($r) => ($r['error'] ?? '') === 'Revision context changed')) === 1, 'Stale save not detected');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_value_revisions r JOIN translation_workbench_value_slots s ON s.id=r.slot_id WHERE s.key_id=?', [$keyA]) === 2, 'Unexpected revision count');
$retry = ['method' => 'save', 'args' => [$uuid(), $keyB, 'de', 'Abbrechen', null, 7, 'Same logical operation']];
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$keyB], [$retry, $retry]);
$assert(isset($results[0]['result']) && $results[0] === $results[1], 'Concurrent retry was not idempotent');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events WHERE operation_id=?', [$retry['args'][0]]) === 1, 'Concurrent retry duplicated event');
file_put_contents($directory.'/restart.json', json_encode(['payload' => $retry, 'result' => $results[0]], JSON_THROW_ON_ERROR));
$conflictingId = $uuid();
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$keyB], [
    ['method' => 'save', 'args' => [$conflictingId, $keyB, 'fr', 'Annuler', null, 7, 'Version A']],
    ['method' => 'save', 'args' => [$conflictingId, $keyB, 'fr', 'Autre', null, 7, 'Version B']],
]);
$assert(count(array_filter($results, fn ($r) => isset($r['result']))) === 1, 'Conflicting operation repeated');
$assert(count(array_filter($results, fn ($r) => ($r['error'] ?? '') === 'Operation request changed')) === 1, 'Conflicting retry not rejected');
// Persistent revision decisions and all participating context writers share key locks.
$reviewKey = $key('ui.review.example');
$reviewFinding = $db->capture($uuid(), 'review.blade.php', 1, 'Review')['finding'];
$reviewLink = $db->assign($uuid(), $reviewFinding, $reviewKey, null, 7, 'Review usage')['link'];
$reviewRevision = $db->save($uuid(), $reviewKey, 'en', 'Review', null, 7, 'Draft')['revision'];
$eventsBefore = (int) $scalar('SELECT count(*) FROM translation_workbench_events');
$snapshot = $db->reviewSnapshot($reviewRevision);
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events') === $eventsBefore, 'Preview wrote history');
$assert(! $db->approvalIsCurrent($reviewRevision), 'Draft approved itself');
$approveArgs = [$uuid(), $reviewRevision, $snapshot['fingerprint'], true, 7, 'Checked revision'];
$approved = $db->decide(...$approveArgs);
$assert($db->approvalIsCurrent($reviewRevision), 'Approval not current');
$assert($db->decide(...$approveArgs) === $approved, 'Review retry changed result');
$assert($db->decide($uuid(), $reviewRevision, $db->reviewSnapshot($reviewRevision)['fingerprint'], true, 7, 'Still checked') === $approved, 'Repeat approval created decision');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events') === $eventsBefore + 1, 'Repeated approval duplicated event');
$reject(fn () => $db->query("UPDATE translation_workbench_reviews SET reason='Different' WHERE id=?", [$approved['review']]), 'Immutable');
$reject(fn () => $db->query('DELETE FROM translation_workbench_reviews WHERE id=?', [$approved['review']]), 'Immutable');
$oldDialog = $db->reviewSnapshot($reviewRevision);
$db->decide($uuid(), $reviewRevision, $oldDialog['fingerprint'], false, 8, 'Withdraw');
$assert(! $db->approvalIsCurrent($reviewRevision), 'Revocation ignored');
$reject(fn () => $db->decide($uuid(), $reviewRevision, $oldDialog['fingerprint'], true, 7, 'Old dialog'), 'Review context changed');
$db->decide($uuid(), $reviewRevision, $db->reviewSnapshot($reviewRevision)['fingerprint'], true, 7, 'Reviewed again');

// A fresh process must recover the historical result, including after revocation.
$worker = new Process([PHP_BINARY, __FILE__, 'worker', $directory, 'review-restart',
    json_encode(['method' => 'decide', 'args' => $approveArgs])]);
$worker->mustRun();
$assert(json_decode($worker->getOutput(), true)['result'] === $approved, 'Review receipt not durable');

// A valid historical decision is never authorization for a newer text.
$snapshot = $db->reviewSnapshot($reviewRevision);
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$reviewKey], [
    ['method' => 'decide', 'args' => [$uuid(), $reviewRevision, $snapshot['fingerprint'], true, 7, 'Concurrent approval']],
    ['method' => 'save', 'args' => [$uuid(), $reviewKey, 'en', 'New draft', $reviewRevision, 8, 'Concurrent edit']],
]);
$assert(isset($results[1]['result']), 'Concurrent text edit failed');
$assert(isset($results[0]['result']) || ($results[0]['error'] ?? '') === 'Review context changed', 'Unexpected approval race result');
$assert(! $db->approvalIsCurrent($reviewRevision), 'Old revision stayed approved after edit');
$current = $results[1]['result']['revision'];
$reject(fn () => $db->decide($uuid(), $reviewRevision, $db->reviewSnapshot($reviewRevision)['fingerprint'], true, 7, 'Old revision'), 'Approval needs current revision');
$assert(! $db->approvalIsCurrent($current), 'New revision inherited approval');

// Adding a usage, including a previously unassigned finding, changes review context.
$newFinding = $db->capture($uuid(), 'review.blade.php', 10, 'Review')['finding'];
$snapshot = $db->reviewSnapshot($current);
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$reviewKey], [
    ['method' => 'decide', 'args' => [$uuid(), $current, $snapshot['fingerprint'], true, 7, 'Before usage']],
    ['method' => 'assign', 'args' => [$uuid(), $newFinding, $reviewKey, null, 8, 'Additional usage']],
]);
$assert(isset($results[1]['result']), 'Concurrent new usage failed');
$assert(isset($results[0]['result']) || ($results[0]['error'] ?? '') === 'Review context changed', 'Unexpected usage race result');
$assert(! $db->approvalIsCurrent($current), 'New usage did not invalidate approval');
$db->decide($uuid(), $current, $db->reviewSnapshot($current)['fingerprint'], true, 7, 'Both usages checked');

// Moving an existing usage is also a context change, not a silent carried approval.
$snapshot = $db->reviewSnapshot($current);
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$reviewKey], [
    ['method' => 'decide', 'args' => [$uuid(), $current, $snapshot['fingerprint'], true, 7, 'Before move']],
    ['method' => 'capture', 'args' => [$uuid(), 'review.blade.php', 4, 'Review', $reviewFinding]],
]);
$assert(isset($results[1]['result']), 'Concurrent move failed');
$assert(isset($results[0]['result']) || ($results[0]['error'] ?? '') === 'Review context changed', 'Unexpected move race result');
$assert(! $db->approvalIsCurrent($current), 'Move silently carried approval');

// Competing fresh approval/revocation: one decision, the other dialog becomes stale.
$snapshot = $db->reviewSnapshot($current);
$beforeReviews = (int) $scalar('SELECT count(*) FROM translation_workbench_reviews');
$results = $race('SELECT id FROM translation_workbench_keys WHERE id=? FOR UPDATE', [$reviewKey], [
    ['method' => 'decide', 'args' => [$uuid(), $current, $snapshot['fingerprint'], true, 7, 'Approve moved usage']],
    ['method' => 'decide', 'args' => [$uuid(), $current, $snapshot['fingerprint'], false, 8, 'Withdraw moved usage']],
]);
$assert(count(array_filter($results, fn ($r) => isset($r['result']))) === 1, 'Competing decisions both succeeded');
$assert(count(array_filter($results, fn ($r) => ($r['error'] ?? '') === 'Review context changed')) === 1, 'Stale competing decision not rejected');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_reviews') === $beforeReviews + 1, 'Competing decisions wrote twice');

// Recorder failure must roll back decision and operation receipt.
$snapshot = $db->reviewSnapshot($current);
$lastDecision = $scalar('SELECT decision FROM translation_workbench_reviews WHERE id=?', [$snapshot['latest_decision']]);
$beforeReviews = (int) $scalar('SELECT count(*) FROM translation_workbench_reviews');
$db->db->exec("CREATE FUNCTION fail_review_event() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''Review recorder failed''; END'; CREATE TRIGGER fail_review_event BEFORE INSERT ON translation_workbench_events FOR EACH ROW EXECUTE FUNCTION fail_review_event()");
$reviewOp = $uuid();
$reject(fn () => $db->decide($reviewOp, $current, $snapshot['fingerprint'], $lastDecision !== 'approved', 7, 'Change decision'), 'Review recorder failed');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_reviews') === $beforeReviews, 'Recorder left decision behind');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_operations WHERE id=?', [$reviewOp]) === 0, 'Recorder left review receipt');
$db->db->exec('DROP TRIGGER fail_review_event ON translation_workbench_events; DROP FUNCTION fail_review_event()');
$reject(fn () => $db->decide($uuid(), $current, $snapshot['fingerprint'], true, 0, 'No actor'), 'Actor and reason required');
$reject(fn () => $db->decide($uuid(), $current, $snapshot['fingerprint'], true, 7, ''), 'Actor and reason required');
$unusedKey = $key('ui.review.unused');
$unusedRevision = $db->save($uuid(), $unusedKey, 'en', 'Unused', null, 7, 'Draft')['revision'];
$reject(fn () => $db->decide($uuid(), $unusedRevision, $db->reviewSnapshot($unusedRevision)['fingerprint'], true, 7, 'No usage'), 'Approval needs current revision');
// Moving a finding to another key changes both usage contexts.
$db->decide($uuid(), $current, $db->reviewSnapshot($current)['fingerprint'], true, 7, 'Current usages checked');
$destinationFinding = $db->capture($uuid(), 'destination.blade.php', 1, 'Destination')['finding'];
$db->assign($uuid(), $destinationFinding, $unusedKey, null, 7, 'Destination usage');
$db->decide($uuid(), $unusedRevision, $db->reviewSnapshot($unusedRevision)['fingerprint'], true, 7, 'Destination checked');
$oldLink = (int) $scalar('SELECT id FROM translation_workbench_key_findings WHERE finding_id=? AND valid_to IS NULL', [$newFinding]);
$db->assign($uuid(), $newFinding, $unusedKey, $oldLink, 8, 'Use destination key');
$assert(! $db->approvalIsCurrent($current), 'Removing usage kept source approval current');
$assert(! $db->approvalIsCurrent($unusedRevision), 'Adding usage kept destination approval current');
$localeRevision = $db->save($uuid(), $unusedKey, 'de', 'Prüfung', null, 7, 'German draft')['revision'];
$db->decide($uuid(), $unusedRevision, $db->reviewSnapshot($unusedRevision)['fingerprint'], true, 7, 'English checked');
$assert(! $db->approvalIsCurrent($localeRevision), 'Another locale inherited approval');
file_put_contents($directory.'/review-restart.json', json_encode([
    'payload' => ['method' => 'decide', 'args' => $approveArgs],
    'result' => ['result' => $approved],
], JSON_THROW_ON_ERROR));
require __DIR__.'/baseline-publication-cases.php';
require __DIR__.'/baseline-option-cases.php';
require __DIR__.'/baseline-destination-cases.php';
echo json_encode(['suite' => 'baseline-postgresql', 'result' => 'passed', 'assertions' => $count, 'races' => 10, 'fresh_process_retry' => true]).PHP_EOL;
