<?php

require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchFoundationSyncer;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
use Illuminate\Support\Facades\DB;

$ids = [4797, 8050];
$paths = ['resources/views/layouts/app/sidebar/⚡administration.blade.php', 'packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-filters.blade.php'];
$build = static function () use ($ids, $paths): array {
    $rows = app(TranslationWorkbenchSourceReconciliation::class)->plan($paths)['rows'];
    $findings = DB::table('translation_workbench_findings')->whereIn('id', $ids)->orderBy('id')->get();
    if ($findings->count() === 2 && $findings->every(fn ($f) => $f->status === 'obsolete')) { return ['state' => 'already_applied']; }
    foreach ($ids as $id) {
        if (! collect($rows)->contains(fn ($r) => $r['state'] === 'missing' && $r['finding_ids'] === [$id])) { throw new RuntimeException('Finding no longer unambiguously missing.'); }
    }
    $links = DB::table('translation_workbench_key_findings')->whereIn('finding_id', $ids)->where('status', 'active')->orderBy('id')->get();
    if ($links->pluck('id')->all() !== [6401, 10257]) { throw new RuntimeException('Unexpected assignments.'); }
    return ['findings' => $findings->map(fn ($r) => (array) $r)->all(), 'links' => $links->map(fn ($r) => (array) $r)->all(),
        'files' => collect($paths)->mapWithKeys(fn ($p) => [$p => hash_file('sha256', base_path($p))])->all()];
};
$hash = static function (string $table, array $except = []): string {
    $h = hash_init('sha256');
    foreach (DB::table('translation_workbench_'.$table)->whereNotIn('id', $except)->orderBy('id')->cursor() as $row) { hash_update($h, json_encode($row, JSON_THROW_ON_ERROR)); }
    return hash_final($h);
};
try {
    if ($argc === 1) {
        $result = DB::transaction(function () use ($build) { DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY'); return $build(); });
    } else {
        if ($argc !== 3 || $argv[1] !== '--apply') { throw new InvalidArgumentException('Use --apply PLAN.json.'); }
        $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
        $result = DB::transaction(function () use ($build, $hash, $plan, $ids, $paths) {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_lang_values, translation_workbench_reviews, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
            $fresh = $build();
            if (($fresh['state'] ?? null) === 'already_applied') { return $fresh; }
            if ($fresh !== $plan) { throw new RuntimeException('Stale plan.'); }
            $except = ['findings' => $ids, 'key_findings' => [6401, 10257], 'keys' => [], 'key_values' => [], 'dynamic_key_values' => [], 'lang_values' => [], 'reviews' => []];
            $before = collect($except)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            $result = app(TranslationWorkbenchFoundationSyncer::class)->reconcileMissingFindings($ids, $paths);
            if ($result['findings_obsoleted'] !== 2 || $result['keys_obsoleted'] !== 0) { throw new RuntimeException('Unexpected archive counts; rollback.'); }
            foreach ($plan['findings'] as $old) {
                $now = (array) DB::table('translation_workbench_findings')->find($old['id']);
                foreach ($old as $field => $value) {
                    if (! in_array($field, ['status', 'meta', 'last_seen_at', 'updated_at'], true) && $now[$field] !== $value) { throw new RuntimeException('Original finding content changed; rollback.'); }
                }
            }
            $after = collect($except)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            if ($before !== $after) { throw new RuntimeException('Unrelated data changed; rollback.'); }
            foreach ($plan['files'] as $p => $value) { if (hash_file('sha256', base_path($p)) !== $value) { throw new RuntimeException('Source changed; rollback.'); } }
            return ['state' => 'applied', 'summary' => $result, 'finding_ids' => $ids, 'unchanged_hashes' => $after];
        });
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
