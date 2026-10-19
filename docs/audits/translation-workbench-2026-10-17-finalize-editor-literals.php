<?php
// Reviewed two-file capture/archive batch. Default is read-only; --apply requires the exact plan.
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Illuminate\Support\Facades\DB;

if ($argc !== 1 && ($argc !== 3 || $argv[1] !== '--apply' || ! is_file($argv[2]))) {
    fwrite(STDERR, 'Usage: php '.$argv[0]." [--apply plan.json]\n"); exit(1);
}
$paths = array_map(fn ($file) => 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/'.$file,
    ['modal-edit-translation-key.blade.php', 'key-sources.blade.php']);
$oldIds = [7121, 7125, 7129, 7133, 7142];
$build = static function () use ($paths, $oldIds): array {
    $report = app(TranslationWorkbenchSourceReconciliation::class)->plan($paths);
    $new = collect($report['rows'])->where('state', 'new')->pluck('source_signature');
    $missing = collect($report['rows'])->where('state', 'missing')->pluck('finding_ids')->flatten()->sort()->values()->all();
    if (($report['summary']['ambiguous'] ?? 0) > 0 || ($new->count() !== 0 && $new->count() !== 21)
        || ($missing !== [] && $missing !== $oldIds)) {
        throw new RuntimeException('Reviewed scope changed; no writes allowed.');
    }
    $items = collect(); foreach ($paths as $path) { $items = $items->concat(app(TranslationWorkbenchScanner::class)->scanFile(base_path($path))); }
    $newItems = $items->whereIn('sourceSignature', $new);
    foreach ($newItems as $item) {
        $fp = app(TranslationFingerprintFactory::class)->signature(['foundation-key', $item->translationKey ?: '', $item->suggestedKey ?: '']);
        if ($item->kind !== 'literal' || DB::table('translation_workbench_keys')->where('fingerprint', $fp)->exists()) {
            throw new RuntimeException('A new literal now has an existing key candidate; review first.');
        }
    }
    $links = DB::table('translation_workbench_key_findings')->whereIn('finding_id', $oldIds)->orderBy('id')->get();
    $keyIds = $links->pluck('key_id')->unique()->all();
    $keys = DB::table('translation_workbench_keys')->whereIn('id', $keyIds)->orderBy('id')->get();
    if ($keys->contains(fn ($key) => $key->review_status !== 'pending' || $key->translation_key !== null)
        || DB::table('translation_workbench_key_values')->whereIn('key_id', $keyIds)->exists()) {
        throw new RuntimeException('Old keys contain reviewed decisions or saved translations; review first.');
    }
    return ['files' => collect($paths)->mapWithKeys(fn ($p) => [$p => hash_file('sha256', base_path($p))])->all(),
        'summary' => $report['summary'], 'new_signatures' => $new->all(), 'missing_ids' => $missing,
        'new_items' => $newItems->map->toEntryAttributes()->values()->all(),
        'old_findings' => DB::table('translation_workbench_findings')->whereIn('id', $oldIds)->orderBy('id')->get()->map(fn ($r) => (array) $r)->all(),
        'old_links' => $links->map(fn ($r) => (array) $r)->all(), 'old_keys' => $keys->map(fn ($r) => (array) $r)->all()];
};
$hash = static function (string $name, array $excluded = []): string {
    $h = hash_init('sha256');
    foreach (DB::table('translation_workbench_'.$name)->whereNotIn('id', $excluded)->orderBy('id')->cursor() as $row) {
        hash_update($h, json_encode($row, JSON_THROW_ON_ERROR));
    }
    return hash_final($h);
};
if ($argc === 1) {
    $result = DB::transaction(function () use ($build) { DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY'); return $build(); });
} else {
    $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
    $result = DB::transaction(function () use ($build, $hash, $plan, $paths, $oldIds) {
        DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_lang_values, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
        $fresh = $build();
        if ($fresh['new_signatures'] === [] && $fresh['missing_ids'] === [] && $fresh['files'] === $plan['files']) { return ['state' => 'already_applied']; }
        if ($fresh !== $plan) { throw new RuntimeException('Stale plan; no changes applied.'); }
        $excluded = ['findings' => $oldIds, 'keys' => array_column($plan['old_keys'], 'id'),
            'key_findings' => array_column($plan['old_links'], 'id'), 'key_values' => [], 'lang_values' => []];
        $before = []; foreach ($excluded as $table => $ids) { $before[$table] = $hash($table, $ids); }
        $items = collect(); foreach ($paths as $p) { $items = $items->concat(app(TranslationWorkbenchScanner::class)->scanFile(base_path($p))); }
        $sync = app(TranslationWorkbenchFoundationSyncer::class);
        $captured = $sync->sync($items->whereIn('sourceSignature', $plan['new_signatures'])->values());
        if ($captured['findings_created'] !== 21 || $captured['keys_created'] !== 21 || $captured['relations_created'] !== 21) {
            throw new RuntimeException('Unexpected capture counts; batch rolled back.');
        }
        $archived = $sync->reconcileMissingFindings($oldIds, $paths);
        if ($archived['findings_obsoleted'] !== 5) { throw new RuntimeException('Unexpected archive count; batch rolled back.'); }
        $newFindings = DB::table('translation_workbench_findings')->whereIn('source_signature', $plan['new_signatures'])->get();
        $newLinks = DB::table('translation_workbench_key_findings')->whereIn('finding_id', $newFindings->pluck('id'))->get();
        $excluded['findings'] = [...$excluded['findings'], ...$newFindings->pluck('id')->all()];
        $excluded['keys'] = [...$excluded['keys'], ...$newLinks->pluck('key_id')->all()];
        $excluded['key_findings'] = [...$excluded['key_findings'], ...$newLinks->pluck('id')->all()];
        foreach ($before as $table => $value) { if ($hash($table, $excluded[$table]) !== $value) { throw new RuntimeException('Unexpected change in '.$table.'; batch rolled back.'); } }
        foreach ($plan['old_findings'] as $old) {
            $now = (array) DB::table('translation_workbench_findings')->find($old['id']);
            foreach ($old as $field => $value) {
                if (! in_array($field, ['status', 'meta', 'last_seen_at', 'updated_at'], true) && $now[$field] !== $value) {
                    throw new RuntimeException('Original finding data changed; batch rolled back.');
                }
            }
        }
        foreach ($plan['files'] as $path => $value) { if (hash_file('sha256', base_path($path)) !== $value) { throw new RuntimeException('Source changed; batch rolled back.'); } }
        return ['state' => 'applied', 'capture' => $captured, 'archive' => $archived,
            'new_finding_ids' => $newFindings->pluck('id')->all(), 'new_key_ids' => $newLinks->pluck('key_id')->all(),
            'existing_values_and_unrelated_records_unchanged' => true, 'original_archived_finding_data_preserved' => true];
    });
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
