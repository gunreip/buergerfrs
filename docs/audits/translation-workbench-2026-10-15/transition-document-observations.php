<?php
// Reviewed identity-only assignment. Default rolls back; --verify is read-only.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchDynamicOptionCollector;
use Illuminate\Support\Facades\DB;

umask(0077);
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$apply = in_array('--apply', $argv, true);
$verify = in_array('--verify', $argv, true);
$require = static function (bool $ok, string $why): void { if (! $ok) { throw new RuntimeException($why); } };
$targets = [4 => [9256, 3039], 9 => [9258, 3039], 10 => [9246, 3267], 12 => [23230, 3267], 13 => [9264, 3039]];
$tables = ['source_files', 'findings', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values',
    'dynamic_source_candidates', 'dynamic_key_values', 'key_values', 'lang_values', 'reviews', 'timeline_events'];
$allowed = [];
foreach ($targets as $id => [$finding, $key]) {
    $allowed['findings'][$finding] = ['meta', 'updated_at'];
    $allowed['option_discoveries'][$id] = ['fingerprint', 'matched_entry_id', 'suggested_key', 'suggested_dynamic_key', 'workbench_suggested_key',
        'suggested_state', 'source_line', 'label_usage', 'source_reference', 'status', 'meta', 'last_seen_at', 'updated_at'];
}
$ui = static function () use ($targets): array {
    $rows = [];
    foreach ($targets as $id => [$finding, $key]) { $rows[$id] = app(TranslationWorkbenchDynamicOptions::class)->rows($key, $finding, ['source' => 'en', 'active' => 'de']); }
    return $rows;
};
$validate = static function (string $backup) use ($tables, $allowed, $targets, $require): array {
    $changes = [];
    foreach ($tables as $table) {
        $before = json_decode(file_get_contents($backup.'/'.$table.'.json'), true, flags: JSON_THROW_ON_ERROR);
        $seen = 0;
        DB::table('translation_workbench_'.$table)->orderBy('id')->chunkById(200, function ($chunk) use ($table, $before, $allowed, $targets, $require, &$changes, &$seen) {
            foreach ($chunk as $record) {
                $row = (array) $record;
                $id = $row['id'];
                if (! isset($before[$id])) {
                    $require($table === 'timeline_events' && in_array([$row['finding_id'], $row['key_id']], array_values($targets), true)
                        && in_array($row['event_type'], ['dynamic_option_context_refreshed', 'dynamic_option_observation_prepared', 'dynamic_option_observation_recorded'], true), 'Unexpected new row '.$table.':'.$id);
                    continue;
                }
                $seen++;
                $fields = array_keys(array_filter($row, fn ($v, $k) => $v !== $before[$id][$k], ARRAY_FILTER_USE_BOTH));
                $require(array_diff($fields, $allowed[$table][$id] ?? []) === [], 'Unexpected change '.$table.':'.$id.' '.implode(',', $fields));
                if ($fields !== []) { $changes[$table][$id] = $fields; }
            }
        });
        $require($seen === count($before), 'Deleted row in '.$table);
        unset($before);
    }
    foreach ($targets as $id => [$finding, $key]) {
        $observation = TranslationWorkbenchOptionDiscovery::findOrFail($id);
        $a = $observation->meta['foundation_assignment'] ?? [];
        $require(($a['status'] ?? null) === 'matched' && ($a['finding_id'] ?? null) === $finding && ($a['key_id'] ?? null) === $key, 'Assignment differs: '.$id);
        $require($observation->options === [] && $observation->options_count === 0 && $observation->source_type === 'unresolved'
            && $observation->status === 'needs_review', 'Unresolved payload changed: '.$id);
    }
    return $changes;
};
try {
    $require(! ($apply && $verify), 'Choose apply or verify');
    $require(DB::connection()->getDriverName() === 'pgsql', 'PostgreSQL required');
    DB::beginTransaction();
    DB::statement($verify ? 'SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY' : 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    if ($verify) {
        $report = json_decode(file_get_contents(__DIR__.'/document-observations-applied.json'), true, flags: JSON_THROW_ON_ERROR);
        $changes = $validate($report['backup']);
        $require($changes === $report['changes'], 'Changed fields differ from applied report');
        $require($ui() === json_decode(file_get_contents($report['backup'].'/ui.json'), true, flags: JSON_THROW_ON_ERROR), 'UI values changed');
        foreach ($report['file_hashes'] as $path => $hash) { $require(hash_file('sha256', base_path($path)) === $hash, 'Source changed'); }
        DB::rollBack();
        $result = ['mode' => 'read_only_verified', 'targets' => $targets, 'ui_unchanged' => true, 'runtime_and_translation_tables_unchanged' => true];
        file_put_contents(__DIR__.'/document-observations-verified.json', json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
        echo json_encode($result).PHP_EOL;
        exit(0);
    }
    DB::select('SELECT pg_advisory_xact_lock(?)', [2026101513]);
    $requests = $hashes = [];
    foreach ($targets as $id => [$finding, $key]) {
        $observation = TranslationWorkbenchOptionDiscovery::findOrFail($id);
        $path = $observation->source_path;
        $hashes[$path] = hash_file('sha256', base_path($path));
        $discovery = app(TranslationWorkbenchDynamicOptionCollector::class)->collectStatic([$path])->filter(fn ($row) =>
            $row['options_variable'] === $observation->options_variable && $row['key_variable'] === $observation->key_variable
            && $row['label_variable'] === $observation->label_variable)->sole();
        $requests[] = ['observation_id' => $id, 'target_finding_id' => $finding, 'key_id' => $key,
            'file_hash' => $hashes[$path], 'discovery' => $discovery, 'assign_unresolved' => true];
    }
    $transition = app(TranslationWorkbenchOptionObservationTransition::class);
    $preview = $transition->process($requests);
    $require(count($preview) === count($targets), 'Incomplete preparation');
    $backup = storage_path('app/private/document-observations-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)));
    $require(mkdir($backup, 0700, true), 'Backup directory failed');
    foreach ($tables as $table) {
        $rows = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($r) => [$r->id => (array) $r])->all();
        $require(file_put_contents($backup.'/'.$table.'.json', json_encode($rows, JSON_THROW_ON_ERROR)) !== false, 'Backup failed');
        unset($rows);
    }
    $beforeUi = $ui();
    $require(file_put_contents($backup.'/ui.json', json_encode($beforeUi, JSON_THROW_ON_ERROR)) !== false, 'UI backup failed');
    $transition->process($requests, true);
    $events = DB::table('translation_workbench_timeline_events')->count();
    $transition->process($requests, true);
    $require(DB::table('translation_workbench_timeline_events')->count() === $events, 'Repeat created events');
    $changes = $validate($backup);
    $require($ui() === $beforeUi, 'UI values changed');
    foreach ($hashes as $path => $hash) { $require(hash_file('sha256', base_path($path)) === $hash, 'Source changed during transition'); }
    $report = ['mode' => $apply ? 'applied' : 'rollback_rehearsal', 'targets' => $targets, 'backup' => $backup,
        'file_hashes' => $hashes, 'changes' => $changes, 'ui_unchanged' => true, 'repeat_without_new_events' => true];
    if ($apply) { DB::commit(); } else { DB::rollBack(); }
    file_put_contents(__DIR__.'/document-observations-'.($apply ? 'applied' : 'rehearsal').'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['mode' => $report['mode'], 'targets' => $targets, 'ui_unchanged' => true]).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
