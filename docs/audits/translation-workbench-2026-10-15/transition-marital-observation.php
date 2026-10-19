<?php
// Explicit observation-only transition. Default rolls back; --verify is read-only.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchDynamicOptions;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchOptionObservationTransition;
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
$tables = ['source_files', 'findings', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values',
    'dynamic_source_candidates', 'dynamic_key_values', 'key_values', 'lang_values', 'reviews', 'timeline_events'];
$snapshot = static function () use ($tables): array {
    $data = [];
    foreach ($tables as $table) { $data[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($r) => [$r->id => (array) $r])->all(); }
    return $data;
};
$validate = static function (array $before, array $after) use ($require): array {
    $allowed = [
        'findings' => [9254 => ['meta', 'updated_at']],
        'option_discoveries' => [17 => ['fingerprint', 'matched_entry_id', 'suggested_key', 'suggested_dynamic_key', 'workbench_suggested_key', 'suggested_state',
            'source_line', 'label_usage', 'source_type', 'source_reference', 'options', 'status', 'meta', 'last_seen_at', 'updated_at']],
    ];
    $changes = [];
    foreach ($before as $table => $rows) {
        foreach ($rows as $id => $row) {
            $require(isset($after[$table][$id]), 'Deleted row '.$table.':'.$id);
            $fields = array_keys(array_filter($after[$table][$id], fn ($v, $k) => $v !== $row[$k], ARRAY_FILTER_USE_BOTH));
            $require(array_diff($fields, $allowed[$table][$id] ?? []) === [], 'Unexpected change '.$table.':'.$id.' '.implode(',', $fields));
            if ($fields !== []) { $changes[$table][$id] = $fields; }
        }
        foreach (array_diff_key($after[$table], $rows) as $id => $row) {
            $require($table === 'timeline_events' && $row['finding_id'] === 9254 && $row['key_id'] === 3452, 'Unexpected new row '.$table.':'.$id);
        }
    }
    $normalize = static function ($json): array { $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR); ksort($data); return $data; };
    $require($normalize($before['option_discoveries'][17]['options']) === $normalize($after['option_discoveries'][17]['options']), 'Options changed');
    $a = json_decode($after['option_discoveries'][17]['meta'], true)['foundation_assignment'] ?? [];
    $require(($a['status'] ?? null) === 'matched' && ($a['finding_id'] ?? null) === 9254 && ($a['key_id'] ?? null) === 3452, 'Assignment not exact');
    $require($before['option_discoveries'][27] === $after['option_discoveries'][27], 'Indexed observation changed');
    return $changes;
};
try {
    $require(! ($apply && $verify), 'Choose either apply or verify');
    $require(DB::connection()->getDriverName() === 'pgsql', 'PostgreSQL required');
    DB::beginTransaction();
    DB::statement($verify ? 'SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY' : 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    if ($verify) {
        $report = json_decode(file_get_contents(__DIR__.'/marital-observation-applied.json'), true, flags: JSON_THROW_ON_ERROR);
        $saved = json_decode(file_get_contents($report['backup']), true, flags: JSON_THROW_ON_ERROR);
        // Verify in chunks: materializing the complete history twice exceeds the audit memory budget.
        foreach ($saved['tables'] as $table => $rows) {
            $seen = 0;
            DB::table('translation_workbench_'.$table)->orderBy('id')->chunkById(200, function ($chunk) use ($table, $rows, $report, $require, &$seen) {
                foreach ($chunk as $record) {
                    $row = (array) $record;
                    $id = $row['id'];
                    if (! isset($rows[$id])) {
                        $require($table === 'timeline_events' && $row['finding_id'] === 9254 && $row['key_id'] === 3452, 'Unexpected new row '.$table.':'.$id);
                        continue;
                    }
                    $seen++;
                    $fields = array_keys(array_filter($row, fn ($v, $k) => $v !== $rows[$id][$k], ARRAY_FILTER_USE_BOTH));
                    $require(array_diff($fields, $report['changes'][$table][$id] ?? []) === [], 'Unexpected verified change '.$table.':'.$id);
                }
            });
            $require($seen === count($rows), 'Deleted row in '.$table);
        }
        $beforeSubset = ['findings' => [9254 => $saved['tables']['findings'][9254]],
            'option_discoveries' => [17 => $saved['tables']['option_discoveries'][17], 27 => $saved['tables']['option_discoveries'][27]]];
        $afterSubset = ['findings' => [9254 => (array) DB::table('translation_workbench_findings')->find(9254)],
            'option_discoveries' => DB::table('translation_workbench_option_discoveries')->whereIn('id', [17, 27])->get()->mapWithKeys(fn ($r) => [$r->id => (array) $r])->all()];
        $changes = $validate($beforeSubset, $afterSubset);
        foreach ($report['file_hashes'] as $path => $hash) { $require(hash_file('sha256', base_path($path)) === $hash, 'Source changed: '.$path); }
        $ui = app(TranslationWorkbenchDynamicOptions::class)->rows(3452, 9254, ['source' => 'en', 'active' => 'de']);
        $require($ui === $saved['ui'], 'UI resolver result changed');
        DB::rollBack();
        $result = ['mode' => 'read_only_verified', 'observation_id' => 17, 'finding_id' => 9254, 'key_id' => 3452, 'changes' => $changes, 'ui_unchanged' => true];
        file_put_contents(__DIR__.'/marital-observation-verified.json', json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);
        echo json_encode($result).PHP_EOL;
        exit(0);
    }
    DB::select('SELECT pg_advisory_xact_lock(?)', [2026101511]);
    $path = 'resources/views/components/management/people/edit-person/sections/⚡person-core.blade.php';
    $declaring = 'app/Livewire/Management/People/EditPerson.php';
    $hashes = [$path => hash_file('sha256', base_path($path)), $declaring => hash_file('sha256', base_path($declaring))];
    $collector = app(TranslationWorkbenchDynamicOptionCollector::class);
    $invoke = fn ($method, ...$args) => (new ReflectionMethod($collector, $method))->invoke($collector, ...$args);
    $groups = $invoke('hardcodedPublicArrayOptions')->groupBy('name')->map(fn ($items) => [
        'source_file' => $items->pluck('source_file')->implode(', '), 'options' => $items->count() === 1 ? $items->sole()['options'] : [],
        'source_candidates' => $items->values()->all(), 'source_resolution' => $items->count() === 1 ? 'single_candidate' : 'ambiguous_source',
    ]);
    $discovery = $invoke('discoveries', [$path], $groups)->filter(fn ($r) => $r['options_variable'] === 'maritalStatusOptions' && $r['label_variable'] === 'label')->sole();
    $require($discovery['source_reference'] === $declaring && count($discovery['options']) === 7, 'Reviewed declaration differs');
    $request = ['observation_id' => 17, 'target_finding_id' => 9254, 'key_id' => 3452, 'file_hash' => $hashes[$path], 'discovery' => $discovery];
    $transition = app(TranslationWorkbenchOptionObservationTransition::class);
    $preview = $transition->process([$request]);
    $require($preview[0]['status'] === 'ready_to_refresh', 'Preparation changed');
    $before = $snapshot();
    $ui = app(TranslationWorkbenchDynamicOptions::class)->rows(3452, 9254, ['source' => 'en', 'active' => 'de']);
    $backup = storage_path('app/private/marital-observation-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json');
    $require(file_put_contents($backup, json_encode(['tables' => $before, 'ui' => $ui], JSON_THROW_ON_ERROR)) !== false, 'Backup failed');
    $transition->process([$request], true);
    $events = DB::table('translation_workbench_timeline_events')->count();
    $transition->process([$request], true);
    $require(DB::table('translation_workbench_timeline_events')->count() === $events, 'Repeat produced extra timeline entries');
    $changes = $validate($before, $snapshot());
    $require(app(TranslationWorkbenchDynamicOptions::class)->rows(3452, 9254, ['source' => 'en', 'active' => 'de']) === $ui, 'UI resolver result changed');
    foreach ($hashes as $file => $hash) { $require(hash_file('sha256', base_path($file)) === $hash, 'Source changed during transition'); }
    $report = ['mode' => $apply ? 'applied' : 'rollback_rehearsal', 'backup' => $backup, 'file_hashes' => $hashes,
        'observation_id' => 17, 'finding_id' => 9254, 'key_id' => 3452, 'changes' => $changes, 'ui_unchanged' => true, 'repeat_without_new_events' => true];
    if ($apply) { DB::commit(); } else { DB::rollBack(); }
    file_put_contents(__DIR__.'/marital-observation-'.($apply ? 'applied' : 'rehearsal').'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['mode' => $report['mode'], 'observation_id' => 17, 'finding_id' => 9254, 'key_id' => 3452, 'ui_unchanged' => true]).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
