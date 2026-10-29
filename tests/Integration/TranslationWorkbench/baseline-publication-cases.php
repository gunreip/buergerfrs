<?php

// Included only by the isolated baseline runner; reuses its assertions and fixture DB.
use Symfony\Component\Process\Process;
use Tests\Support\TranslationWorkbench\PostgresPublisher;

if (! isset($db, $assert, $directory, $uuid, $key)) {
    throw new RuntimeException('Run baseline-persistence.php');
}
$publisher = new PostgresPublisher($directory);
$publicationKey = $key('publication.button.cancel');
$publicationFinding = $db->capture($uuid(), 'publication.blade.php', 1, 'Cancel')['finding'];
$db->assign($uuid(), $publicationFinding, $publicationKey, null, 7, 'Publication usage');
$publicationRevisions = [];
foreach (['de' => 'Abbrechen', 'en' => 'Cancel'] as $locale => $value) {
    $r = $db->save($uuid(), $publicationKey, $locale, $value, null, 7, 'Publication draft')['revision'];
    $db->decide($uuid(), $r, $db->reviewSnapshot($r)['fingerprint'], true, 7, 'Checked');
    $publicationRevisions[] = $r;
}
$run = $uuid();
$plan = $publisher->prepare($run, $publicationRevisions, 7, 'Publish');
$assert($publisher->prepare($run, $publicationRevisions, 7, 'Publish') === $plan, 'Prepare retry changed plan');
$reject(fn () => $publisher->prepare($uuid(), $publicationRevisions, 7, 'Overlap'), 'File reserved by publication ');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_publication_runs') === 1, 'Failed reservation left plan');
$reject(fn () => $publisher->publish($run, str_repeat('0', 64)), 'plan mismatch');
$crash = new Process([PHP_BINARY, __DIR__.'/baseline-persistence.php', 'worker', $directory, 'publication-crash',
    json_encode(['method' => 'publish-crash', 'args' => [$run, $plan['fingerprint']]])]);
$crash->run();
$assert($crash->getExitCode() === 73, 'Expected abrupt worker exit');
$assert($scalar('SELECT state FROM translation_workbench_publication_runs WHERE id=?', [$run]) === 'writing', 'Crash ran normal interruption handling');
$deFile = $publisher->directory.'/de/publication.php';
$enFile = $publisher->directory.'/en/publication.php';
$assert(is_file($deFile) && ! is_file($enFile), 'Expected partial file state');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_publication_outcomes') === 0, 'Partial run claimed success');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_file_reservations') === 2, 'Partial run lost reservations');
touch($deFile, 1000000000);
// Resume in a new process after a real server restart (handled by the launcher).
file_put_contents($directory.'/publication-restart.json', json_encode($plan, JSON_THROW_ON_ERROR));

// A separate file group exercises stale file checks and closure without undoing writes.
$secondKey = $key('second.button.cancel');
$secondFinding = $db->capture($uuid(), 'second.blade.php', 1, 'Cancel')['finding'];
$db->assign($uuid(), $secondFinding, $secondKey, null, 7, 'Second usage');
$secondRevision = $db->save($uuid(), $secondKey, 'en', 'Cancel', null, 7, 'Draft')['revision'];
$db->decide($uuid(), $secondRevision, $db->reviewSnapshot($secondRevision)['fingerprint'], true, 7, 'Checked');
$secondRun = $uuid();
$secondPlan = $publisher->prepare($secondRun, [$secondRevision], 7, 'Second publish');
mkdir($publisher->directory.'/en', 0700);
$secondFile = $publisher->directory.'/en/second.php';
file_put_contents($secondFile, '<?php return ["external" => "preserve"];');
$reject(fn () => $publisher->publish($secondRun, $secondPlan['fingerprint']), 'External file change');
$preview = $publisher->closurePreview($secondRun);
file_put_contents($secondFile, '<?php return ["external" => "new"];');
$reject(fn () => $publisher->close($secondRun, $preview['fingerprint'], 7, 'Close'), 'Closure preview changed');
$preview = $publisher->closurePreview($secondRun);
$closed = $publisher->close($secondRun, $preview['fingerprint'], 7, 'Keep external edit');
$assert($closed['kind'] === 'closed_without_writing', 'Wrong closure outcome');
$assert(file_get_contents($secondFile) === '<?php return ["external" => "new"];', 'Closure overwrote external edit');
$assert($publisher->close($secondRun, $preview['fingerprint'], 7, 'Keep external edit') === $closed, 'Closure retry changed outcome');
$reject(fn () => $publisher->publish($secondRun, $secondPlan['fingerprint']), 'Publication closed');
$replacement = $publisher->prepare($uuid(), [$secondRevision], 7, 'New plan');
// Failure of the recorder after files are written keeps reservations and allows recovery.
$db->db->exec("CREATE FUNCTION fail_publish_event() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''Publication recorder failed''; END'; CREATE TRIGGER fail_publish_event BEFORE INSERT ON translation_workbench_events FOR EACH ROW EXECUTE FUNCTION fail_publish_event()");
$reject(fn () => $publisher->publish($replacement['run'], $replacement['fingerprint']), 'Publication recorder failed');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_publication_outcomes WHERE run_id=?', [$replacement['run']]) === 0, 'Failed recorder left outcome');
$db->db->exec('DROP TRIGGER fail_publish_event ON translation_workbench_events; DROP FUNCTION fail_publish_event()');
$written = file_get_contents($secondFile);
$reject(fn () => $publisher->publish($replacement['run'], $replacement['fingerprint'], function ($stage) {
    if ($stage === 'after_write') {
        throw new RuntimeException('Unexpected rewrite');
    }
    throw new RuntimeException('After receipt');
}), 'After receipt');
$success = $publisher->publish($replacement['run'], $replacement['fingerprint']);
$assert($success['kind'] === 'completed', 'Receipt recovery failed');
$assert(file_get_contents($secondFile) === $written, 'Receipt recovery rewrote file');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_events WHERE operation_id=?', [$replacement['run']]) === 1, 'Duplicate success');
$assert(str_contains($written, 'external'), 'Unselected content lost');
file_put_contents($secondFile, '<?php return ["later" => "edit"];');
$assert($publisher->publish($replacement['run'], $replacement['fingerprint']) === $success, 'Historical receipt changed');
$assert(file_get_contents($secondFile) === '<?php return ["later" => "edit"];', 'Historical retry overwrote later edit');
$reject(fn () => $publisher->close($replacement['run'], 'invalid', 7, 'Close success'), 'Different terminal decision');

// Revocation between preparation and publication blocks all writes, but permits closure.
$third = $publisher->prepare($uuid(), [$secondRevision], 7, 'Revocable plan');
$db->decide($uuid(), $secondRevision, $db->reviewSnapshot($secondRevision)['fingerprint'], false, 8, 'Recheck');
$reject(fn () => $publisher->publish($third['run'], $third['fingerprint']), 'not currently approved');
$assert(file_get_contents($secondFile) === '<?php return ["later" => "edit"];', 'Revoked plan wrote file');
$preview = $publisher->closurePreview($third['run']);
$publisher->close($third['run'], $preview['fingerprint'], 8, 'Close revoked plan');
$assert((int) $scalar('SELECT count(*) FROM translation_workbench_file_reservations WHERE run_id=?', [$third['run']]) === 0, 'Closed run kept reservation');

// Writer holds key-context locks through the last file and receipt.
$db->decide($uuid(), $secondRevision, $db->reviewSnapshot($secondRevision)['fingerprint'], true, 7, 'Reapprove');
$concurrent = $publisher->prepare($uuid(), [$secondRevision], 7, 'Publish with waiting review');
$reviewSnapshot = $db->reviewSnapshot($secondRevision);
$reviewWorker = null;
try {
    $published = $publisher->publish($concurrent['run'], $concurrent['fingerprint'], function ($stage) use (&$reviewWorker, $directory, $secondRevision, $reviewSnapshot, $uuid, $db, $assert) {
        if ($stage !== 'after_write') {
            return;
        }
        $reviewWorker = new Process([PHP_BINARY, __DIR__.'/baseline-persistence.php', 'worker', $directory, 'publication-waiting-revoke',
            json_encode(['method' => 'decide', 'args' => [$uuid(), $secondRevision, $reviewSnapshot['fingerprint'], false, 8, 'Withdraw after publication']])]);
        $reviewWorker->start();
        $deadline = microtime(true) + 5;
        do {
            $db->query('SELECT pg_stat_clear_snapshot()');
            $waiting = (int) $db->query("SELECT count(*) FROM pg_stat_activity WHERE application_name='publication-waiting-revoke' AND wait_event_type='Lock'")->fetchColumn();
            if ($waiting === 1) {
                break;
            }
            usleep(20000);
        } while (microtime(true) < $deadline);
        $assert($waiting === 1, 'Revocation did not wait for publisher key lock');
    });
    $reviewWorker->wait();
    $assert(isset(json_decode($reviewWorker->getOutput(), true)['result']), 'Waiting revocation failed');
    $assert($published['kind'] === 'completed' && ! $db->approvalIsCurrent($secondRevision), 'Publication/revocation ordering wrong');
    $assert($publisher->publish($concurrent['run'], $concurrent['fingerprint']) === $published, 'Later revocation changed historical receipt');
} finally {
    if ($reviewWorker?->isRunning()) {
        $reviewWorker->stop();
    }
}
