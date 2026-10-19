<?php
// Bounded reviewed source transition through existing Foundation methods. Default: rollback rehearsal.
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$apply = in_array('--apply', $argv, true);
umask(0077);
$path = 'resources/views/components/management/people/edit-person/sections/⚡documents.blade.php';
$owner = 'management.people.edit_person.sections.documents.document_category_options';
$require = function (bool $ok, string $message): void { if (! $ok) { throw new RuntimeException($message); } };
$tables = ['source_files', 'findings', 'keys', 'key_findings', 'option_discoveries', 'dynamic_sources', 'dynamic_source_values',
    'dynamic_source_candidates', 'dynamic_key_values', 'key_values', 'lang_values', 'reviews', 'timeline_events'];
$snapshot = function () use ($tables) {
    $data = [];
    foreach ($tables as $table) { $data[$table] = DB::table('translation_workbench_'.$table)->orderBy('id')->get()->mapWithKeys(fn ($r) => [$r->id => (array) $r])->all(); }
    return $data;
};
try {
    $require(DB::connection()->getDriverName() === 'pgsql', 'PostgreSQL required');
    DB::beginTransaction();
    DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    DB::select('SELECT pg_advisory_xact_lock(?)', [2026101510]);
    $before = $snapshot();
    $old = TranslationWorkbenchFinding::with('sourceFile')->findOrFail(2145);
    $require($old->raw_expression === '__($label)' && $old->sourceFile->path === $path, 'Historical finding changed');
    $require($before['keys'][3267]['translation_key'] === $owner, 'Current owner changed');
    $normalizeValues = fn ($id) => collect($before['dynamic_key_values'])->where('key_id', $id)
        ->map(fn ($v) => array_intersect_key($v, array_flip(['locale', 'value_key', 'value', 'native_label', 'status'])))
        ->sortBy(fn ($v) => $v['locale'].'/'.$v['value_key'])->values()->all();
    $require(count($normalizeValues(3188)) === 16 && $normalizeValues(3188) === $normalizeValues(3267), 'Reviewed translations differ');
    $manifest = app(TranslationWorkbenchScanner::class)->scanWithManifest([$path]);
    $require($manifest['scanned_paths'] === [$path], 'Source not scanned');
    $calls = $manifest['items']->filter(fn ($i) => ($i->meta['option_owner_key'] ?? null) === $owner
        && ($i->meta['dynamic_option_context']['options_variable'] ?? null) === 'documentCategoryOptions'
        && ($i->meta['dynamic_option_context']['label_variable'] ?? null) === 'label');
    $require($calls->count() === 1, 'Reviewed explicit call is not unique');
    $call = $calls->sole();
    $require(! $manifest['items']->contains(fn ($i) => $i->rawExpression === '__($label)'), 'Legacy call still present');
    $backup = storage_path('app/private/category-owner-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json');
    $require(file_put_contents($backup, json_encode($before, JSON_THROW_ON_ERROR)) !== false, 'Backup failed');
    $sync = app(TranslationWorkbenchFoundationSyncer::class);
    // Synchronize only the reviewed new call; reconcile disappearance against the complete file manifest.
    // No classification, option provider or broad scanner command is invoked.
    $sync->sync(collect([$call]));
    $reconciliation = (new ReflectionMethod($sync, 'markMissingFoundationRowsObsolete'))
        ->invoke($sync, $manifest['items'], $manifest['reconciliation_paths'], now());
    $new = TranslationWorkbenchFinding::where('source_file_id', $old->source_file_id)
        ->where('raw_expression', $call->rawExpression)->where('status', 'active')->sole();
    $require($new->keys()->wherePivot('status', 'active')->sole()->id === 3267, 'Explicit owner was not retained');
    $require($old->fresh()->status === 'obsolete', 'Legacy finding not retired');
    $require(DB::table('translation_workbench_key_findings')->where('finding_id', 2145)->where('status', 'active')->count() === 0, 'Ambiguous links remain');
    $require(DB::table('translation_workbench_keys')->where('id', 3188)->value('status') === 'obsolete', 'Historical key remains active');
    $sync->sync(collect([$call]));
    $repeat = (new ReflectionMethod($sync, 'markMissingFoundationRowsObsolete'))
        ->invoke($sync, $manifest['items'], $manifest['reconciliation_paths'], now());
    $require($repeat['findings_obsoleted'] === 0 && $repeat['relations_obsoleted'] === 0, 'Reconciliation not repeatable');
    $after = $snapshot();
    $allowed = [
        'source_files' => [$old->source_file_id => ['last_seen_at', 'scan_count', 'updated_at']],
        'findings' => [2145 => ['status', 'meta', 'last_seen_at', 'updated_at']],
        'keys' => [3188 => ['status', 'meta', 'updated_at']],
        'key_findings' => [3190 => ['status', 'meta', 'updated_at'], 3285 => ['status', 'meta', 'updated_at']],
    ];
    $changes = [];
    foreach ($before as $table => $rows) {
        foreach ($rows as $id => $row) {
            $require(isset($after[$table][$id]), 'Deleted row '.$table.':'.$id);
            $fields = array_keys(array_filter($after[$table][$id], fn ($v, $k) => $v !== $row[$k], ARRAY_FILTER_USE_BOTH));
            $require(array_diff($fields, $allowed[$table][$id] ?? []) === [], 'Unreviewed change '.$table.':'.$id.' '.implode(',', $fields));
            if ($fields !== []) { $changes[$table][$id] = $fields; }
        }
        foreach (array_diff_key($after[$table], $rows) as $id => $row) {
            $valid = match ($table) {
                'findings' => $id === $new->id,
                'key_findings' => $row['finding_id'] === $new->id && $row['key_id'] === 3267,
                'timeline_events' => in_array($row['finding_id'], [null, 2145, $new->id], true) && in_array($row['key_id'], [null, 3188, 3267], true),
                default => false,
            };
            $require($valid, 'Unreviewed new row '.$table.':'.$id);
        }
    }
    $require(hash_file('sha256', base_path($path)) === $manifest['file_hashes'][$path], 'Source changed during transition');
    $report = ['mode' => $apply ? 'applied' : 'rollback_rehearsal', 'backup' => $backup, 'source_path' => $path,
        'file_hash' => $manifest['file_hashes'][$path], 'old_finding_id' => 2145, 'new_finding_id' => $new->id,
        'owner_key_id' => 3267, 'changes' => $changes, 'reconciliation' => $reconciliation,
        'retained_tables' => ['dynamic_sources', 'dynamic_source_values', 'dynamic_source_candidates', 'dynamic_key_values', 'key_values', 'lang_values', 'reviews', 'option_discoveries']];
    if ($apply) { DB::commit(); } else { DB::rollBack(); }
    file_put_contents(__DIR__.'/category-owner-'.($apply ? 'applied' : 'rehearsal').'.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
    echo json_encode(['mode' => $report['mode'], 'new_finding_id' => $new->id, 'reconciliation' => $reconciliation]).PHP_EOL;
} catch (Throwable $e) { if (DB::transactionLevel()) { DB::rollBack(); } fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
