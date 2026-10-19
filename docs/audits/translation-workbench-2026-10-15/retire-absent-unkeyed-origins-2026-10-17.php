<?php

// Audited removed literal origins only. No findings, source files or translation values are changed.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Illuminate\Support\Facades\DB;

$specs = json_decode(file_get_contents(__DIR__.'/absent-unkeyed-origins-2026-10-17.json'), true, 512, JSON_THROW_ON_ERROR);
$build = static function () use ($specs): array {
    $paths = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
        ->whereIn('f.id', array_column($specs, 'finding_id'))->pluck('s.path')->unique()->sort()->values()->all();
    $files = collect([...$paths, ...array_column($specs, 'path')])->unique()->sort()->mapWithKeys(fn ($p) => [$p => is_file(base_path($p)) ? hash_file('sha256', base_path($p)) : null])->all();
    $report = app(TranslationWorkbenchSourceReconciliation::class)->plan($paths);
    $rows = collect($report['rows'])->filter(fn ($r) => in_array($r['state'], ['unchanged', 'position_refined'], true) && count($r['finding_ids']) === 1)->keyBy(fn ($r) => $r['finding_ids'][0]);
    $actions = [];
    foreach ($specs as $spec) {
        $key = TranslationWorkbenchKey::findOrFail($spec['key_id']);
        $link = TranslationWorkbenchKeyFinding::findOrFail($spec['link_id']);
        $finding = TranslationWorkbenchFinding::findOrFail($spec['finding_id']);
        if ($key->status === 'obsolete' && ($key->meta['obsolete_reason'] ?? null) === 'historical_source_literal_removed'
            && $link->status === 'obsolete' && ($link->meta['obsolete_reason'] ?? null) === 'historical_source_literal_removed'
            && ! $key->keyFindings()->where('status', 'active')->exists()) { continue; }
        if (! is_file(base_path($spec['path'])) || collect(config('translation-workbench.exclude_paths'))->contains(fn ($p) => $spec['path'] === $p || str_starts_with($spec['path'], $p.'/'))) {
            throw new RuntimeException('Expected an available, included source file.');
        }
        $items = app(\Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner::class)->scanFile(base_path($spec['path']));
        if ($items->contains(fn ($i) => $i->kind === 'literal' && $i->literalText === $spec['literal'] && ($i->meta['source_context'] ?? null) !== 'commented_out')) {
            throw new RuntimeException('Literal is still present in the original source.');
        }
        $event = DB::table('translation_workbench_timeline_events')->find($spec['creation_event_id']);
        $context = json_decode($event?->context ?? '{}', true, 512, JSON_THROW_ON_ERROR);
        $origin = str_replace('\\/', '/', $context['source_path'] ?? '');
        if (str_contains($origin, '\\u')) { $origin = json_decode('"'.$origin.'"', true, 512, JSON_THROW_ON_ERROR); }
        if (! $event || $event->key_id !== $key->id || $event->event_type !== 'key_candidate_discovered'
            || ($context['source'] ?? null) !== 'translation-workbench:sync-foundation' || $origin !== $spec['path']
            || $key->suggested_key !== $spec['suggested_key'] || $key->translation_key !== null
            || $key->status !== 'open' || $key->review_status !== 'pending' || $key->reviewed_at !== null || $key->is_dynamic_key
            || $link->status !== 'active' || $link->key_id !== $key->id || $link->finding_id !== $finding->id
            || $link->relation_type !== 'candidate' || ($link->meta['source'] ?? null) !== 'foundation_sync'
            || $key->keyFindings()->where('status', 'active')->count() !== 1
            || $finding->status !== 'active' || ! $rows->has($finding->id)
            || $rows[$finding->id]['path'] === $origin || $finding->suggested_key === $key->suggested_key) {
            throw new RuntimeException('The reviewed removed-literal evidence changed for key '.$key->id);
        }
        foreach (['key_values', 'dynamic_key_values', 'reviews'] as $table) {
            if (DB::table('translation_workbench_'.$table)->where('key_id', $key->id)->exists()) { throw new RuntimeException('Independent key values/review found.'); }
        }
        $owners = DB::table('translation_workbench_key_findings as l')->join('translation_workbench_keys as k', 'k.id', '=', 'l.key_id')
            ->where('l.finding_id', $finding->id)->where('l.status', 'active')->where('k.status', 'open')
            ->where('k.suggested_key', $finding->suggested_key)->whereNull('k.translation_key')->get(['l.id', 'k.id as key_id']);
        if ($owners->count() !== 1) { throw new RuntimeException('No unique retained owner for finding '.$finding->id); }
        $actions[] = ['key' => $key->getAttributes(), 'link' => $link->getAttributes(), 'finding' => $finding->getAttributes(),
            'retained_owner' => (array) $owners->sole(), 'origin_event' => (array) $event];
    }
    return ['files' => $files, 'exclusions' => config('translation-workbench.exclude_paths'), 'actions' => $actions];
};
$hash = static function (string $table, array $except = []): string {
    $h = hash_init('sha256');
    foreach (DB::table('translation_workbench_'.$table)->whereNotIn('id', $except)->orderBy('id')->cursor() as $r) { hash_update($h, json_encode($r, JSON_THROW_ON_ERROR)); }
    return hash_final($h);
};
try {
    if ($argc === 1) {
        $result = DB::transaction(function () use ($build) { DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY'); return $build(); });
    } else {
        if ($argc !== 3 || $argv[1] !== '--apply') { throw new InvalidArgumentException('Use --apply PLAN.json.'); }
        $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
        $result = DB::transaction(function () use ($plan, $build, $hash) {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_lang_values, translation_workbench_reviews, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
            $fresh = $build();
            if ($fresh['actions'] === []) { return ['state' => 'already_applied']; }
            if ($fresh !== $plan) { throw new RuntimeException('Stale plan; no changes applied.'); }
            $keyIds = array_column(array_column($plan['actions'], 'key'), 'id');
            $linkIds = array_column(array_column($plan['actions'], 'link'), 'id');
            $except = ['keys' => $keyIds, 'key_findings' => $linkIds, 'findings' => [], 'source_files' => [], 'key_values' => [], 'dynamic_key_values' => [], 'lang_values' => [], 'reviews' => []];
            $before = collect($except)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            $timeline = app(TranslationWorkbenchTimelineRecorder::class); $eventIds = [];
            foreach ($plan['actions'] as $a) {
                $key = TranslationWorkbenchKey::findOrFail($a['key']['id']);
                $link = TranslationWorkbenchKeyFinding::findOrFail($a['link']['id']);
                $context = ['source' => 'audited-removed-literal-origin-retirement', 'origin_event_id' => $a['origin_event']['id'], 'retained_key_id' => $a['retained_owner']['key_id']];
                $old = $link->getAttributes();
                $link->forceFill(['status' => 'obsolete', 'meta' => [...($link->meta ?? []), 'obsolete_reason' => 'historical_source_literal_removed']])->save();
                $eventIds[] = $timeline->record('key_assignment_reconciled', key: $key, finding: TranslationWorkbenchFinding::findOrFail($link->finding_id), oldValues: $old, newValues: $link->getAttributes(), context: $context)->id;
                $old = $key->only(['status', 'meta']);
                $key->forceFill(['status' => 'obsolete', 'meta' => [...($key->meta ?? []), 'obsolete_reason' => 'historical_source_literal_removed']])->save();
                $eventIds[] = $timeline->record('key_candidate_obsoleted', key: $key, oldValues: $old, newValues: $key->only(['status', 'meta']), context: $context)->id;
            }
            $after = collect($except)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            if ($before !== $after) { throw new RuntimeException('Unrelated records changed; rollback.'); }
            foreach ($plan['files'] as $p => $value) {
                if ((is_file(base_path($p)) ? hash_file('sha256', base_path($p)) : null) !== $value) { throw new RuntimeException('Source changed; rollback.'); }
            }
            return ['state' => 'applied', 'retired_key_ids' => $keyIds, 'retired_link_ids' => $linkIds, 'timeline_event_ids' => $eventIds, 'unchanged_hashes' => $after];
        });
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
