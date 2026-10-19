<?php
// Reviewed suggestions only. Default rollback; --verify performs no writes.
use Illuminate\Support\Facades\DB;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchRuntimeSuggestionRetirement;
umask(0077);
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$apply = in_array('--apply', $argv, true);
$verify = in_array('--verify', $argv, true);
$require = static function ($ok, $why) { if (! $ok) { throw new RuntimeException($why); } };
$ids = range(86, 95); // Explicitly reviewed batch, never a runtime special case.
$tables = ['source_files', 'findings', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values', 'dynamic_source_candidates', 'dynamic_key_values', 'key_values', 'lang_values', 'reviews', 'timeline_events'];
$digest = static function (int $timelineMax) use ($tables, $ids): array {
    $hashes = [];
    foreach ($tables as $table) {
        $ctx = hash_init('sha256');
        $query = DB::table('translation_workbench_'.$table)->orderBy('id');
        if ($table === 'dynamic_source_candidates') { $query->whereNotIn('id', $ids); }
        if ($table === 'timeline_events') { $query->where('id', '<=', $timelineMax); }
        $query->chunkById(200, function ($rows) use ($ctx) { foreach ($rows as $row) { hash_update($ctx, json_encode($row, JSON_THROW_ON_ERROR)."\n"); } });
        $hashes[$table] = hash_final($ctx);
    }
    return $hashes;
};
$validate = static function (array $saved) use ($require, $ids, $digest): void {
    $require($digest($saved['timeline_max']) === $saved['hashes'], 'Unrelated table rows changed');
    foreach ($saved['candidates'] as $before) {
        $after = (array) DB::table('translation_workbench_dynamic_source_candidates')->find($before['id']);
        $changed = array_keys(array_filter($after, fn ($v, $k) => $v !== $before[$k], ARRAY_FILTER_USE_BOTH));
        $require(array_diff($changed, ['status', 'meta', 'updated_at']) === [] && $after['status'] === 'obsolete', 'Unexpected candidate change');
        $meta = json_decode($after['meta'], true, flags: JSON_THROW_ON_ERROR);
        $proof = $meta['reviewed_retirement'] ?? [];
        unset($meta['reviewed_retirement']);
        $require($meta === json_decode($before['meta'], true), 'Old metadata changed');
        $require(($proof['finding_id'] ?? null) === $before['finding_id'] && ($proof['snapshot_hash'] ?? null) === hash('sha256', $before['candidate_values']), 'Missing retirement proof');
    }
    $events = DB::table('translation_workbench_timeline_events')->where('id', '>', $saved['timeline_max'])->get();
    $require($events->count() === count($ids), 'Unexpected new event count');
    $eventIds = [];
    foreach ($events as $event) {
        $new = json_decode($event->new_values, true, flags: JSON_THROW_ON_ERROR);
        $require($event->event_type === 'dynamic_runtime_suggestion_retired' && in_array($new['candidate_id'] ?? null, $ids, true), 'Unexpected event');
        $eventIds[] = $new['candidate_id'];
    }
    sort($eventIds); $require($eventIds === $ids, 'Missing retirement events');
};
try {
    $require(! ($apply && $verify), 'Choose apply or verify');
    DB::beginTransaction();
    DB::statement($verify ? 'SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY' : 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    if ($verify) {
        $report = json_decode(file_get_contents(__DIR__.'/pending-runtime-retirement-applied.json'), true, flags: JSON_THROW_ON_ERROR);
        $saved = json_decode(file_get_contents($report['backup']), true, flags: JSON_THROW_ON_ERROR);
        $validate($saved);
        DB::rollBack();
        $result = ['mode' => 'read_only_verified', 'candidate_ids' => $ids, 'snapshots_and_other_rows_unchanged' => true];
        file_put_contents(__DIR__.'/pending-runtime-retirement-verified.json', json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
        echo json_encode($result).PHP_EOL; exit(0);
    }
    DB::select('SELECT pg_advisory_xact_lock(?)', [2026101514]);
    $candidates = DB::table('translation_workbench_dynamic_source_candidates')->whereIn('id', $ids)->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
    $require(count($candidates) === count($ids), 'Missing reviewed candidates');
    $requests = [];
    foreach ($candidates as $candidate) {
        $require(preg_match('/^dynamic_source:(\d+)$/', $candidate['candidate_reference'], $m) === 1, 'Invalid consumer reference');
        $requests[] = ['candidate_id' => $candidate['id'], 'finding_id' => $candidate['finding_id'], 'consumer_source_id' => (int) $m[1], 'snapshot_hash' => hash('sha256', $candidate['candidate_values'])];
    }
    $service = app(TranslationWorkbenchRuntimeSuggestionRetirement::class);
    $service->process($requests);
    $max = (int) DB::table('translation_workbench_timeline_events')->max('id');
    $saved = ['candidates' => $candidates, 'timeline_max' => $max, 'hashes' => $digest($max)];
    $backup = storage_path('app/private/pending-runtime-retirement-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json');
    $require(file_put_contents($backup, json_encode($saved, JSON_THROW_ON_ERROR)) !== false, 'Backup failed');
    $service->process($requests, true);
    $service->process($requests, true);
    $validate($saved);
    if ($apply) { DB::commit(); } else { DB::rollBack(); }
    $report = ['mode' => $apply ? 'applied' : 'rollback_rehearsal', 'backup' => $backup, 'candidate_ids' => $ids, 'snapshots_and_other_rows_unchanged' => true, 'repeat_without_new_events' => true];
    file_put_contents(__DIR__.'/pending-runtime-retirement-'.($apply ? 'applied' : 'rehearsal').'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode($report).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
