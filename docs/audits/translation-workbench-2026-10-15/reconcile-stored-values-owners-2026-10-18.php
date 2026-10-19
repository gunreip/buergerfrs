<?php

// Audited one-off repair. Default: read-only plan. Apply: --apply /path/to/plan.json.
require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchSourceReconciliation;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchTimelineRecorder;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Illuminate\Support\Facades\DB;

$sourceId = (int) ($argv[1] ?? 0);
$specs = [4826 => ['finding' => 6716, 'review' => 913], 4827 => ['finding' => 6731, 'review' => 914]];
if (! isset($specs[$sourceId])) { fwrite(STDERR, "Use KEY_ID [--apply PLAN.json].\n"); exit(1); }
$spec = $specs[$sourceId];
array_splice($argv, 1, 1);
$argc = count($argv);
$build = static function () use ($sourceId, $spec): array {
    $source = TranslationWorkbenchKey::findOrFail($sourceId);
    $target = TranslationWorkbenchKey::findOrFail(321);
    if ($source->status === 'obsolete' && ($source->meta['reconciled_canonical_key_id'] ?? null) === 321
        && ! $source->keyFindings()->where('status', 'active')->exists()) {
        return ['state' => 'already_applied'];
    }
    if ($source->status !== 'open' || $source->review_status !== 'pending' || $source->reviewed_at !== null
        || $target->status !== 'open' || $target->review_status !== 'reviewed'
        || ! $source->translation_key || $source->translation_key !== $target->translation_key
        || $source->is_dynamic_key || $target->is_dynamic_key) {
        throw new RuntimeException('Owner decision changed; do not consolidate.');
    }
    foreach (['key_values', 'dynamic_key_values', 'reviews'] as $table) {
        if (DB::table('translation_workbench_'.$table)->where('key_id', $source->id)->exists()) {
            throw new RuntimeException('Duplicate has independent values or reviews.');
        }
    }
    if (TranslationWorkbenchKey::where('translation_key', $source->translation_key)->where('status', '!=', 'obsolete')->whereNotIn('id', [321, 4826, 4827])->exists()
        || TranslationWorkbenchKey::where('meta->reconciled_canonical_key_id', $source->id)->exists()) {
        throw new RuntimeException('Additional owners or redirects require review.');
    }
    $review = DB::table('translation_workbench_reviews')->where('id', $spec['review'])->first();
    $decision = $review ? json_decode($review->new_values, true, 512, JSON_THROW_ON_ERROR) : [];
    $reviewMeta = $review ? json_decode($review->meta, true, 512, JSON_THROW_ON_ERROR) : [];
    if (! $review || $review->key_id !== 321 || $review->finding_id !== $spec['finding']
        || $review->decision !== 'translation_key_bulk_equalized'
        || ($decision['translation_key'] ?? null) !== $target->translation_key
        || ($reviewMeta['selected_finding_ids'] ?? []) !== [6716, 6731]) {
        throw new RuntimeException('Explicit finding-level decision is missing or changed.');
    }
    $links = TranslationWorkbenchKeyFinding::where('key_id', $source->id)->orderBy('id')->get();
    $active = $links->where('status', 'active');
    if ($active->pluck('finding_id')->sort()->values()->all() !== [$spec['finding']]) {
        throw new RuntimeException('The audited set of findings changed.');
    }
    $findings = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
        ->whereIn('f.id', $active->pluck('finding_id'))->orderBy('f.id')->get(['f.*', 's.path']);
    $paths = $findings->pluck('path')->unique()->values()->all();
    $report = app(TranslationWorkbenchSourceReconciliation::class)->plan($paths);
    $destinations = [];
    foreach ($active as $link) {
        $row = collect($report['rows'])->first(fn ($r) => $r['finding_ids'] === [$link->finding_id]);
        $dest = TranslationWorkbenchKeyFinding::where('key_id', $target->id)->where('finding_id', $link->finding_id)->where('status', 'active')->get();
        if (! $row || ! in_array($row['state'], ['unchanged', 'position_refined'], true)
            || $row['scanned_key'] !== $target->translation_key || $dest->count() !== 1
            || $link->relation_type !== 'candidate' || $dest->first()->relation_type !== 'candidate'
            || $findings->firstWhere('id', $link->finding_id)->status !== 'active') {
            throw new RuntimeException('Current source or canonical assignment is not unambiguous.');
        }
        $destinations[] = $dest->first()->getAttributes();
    }
    return ['review_evidence' => (array) $review, 'source' => $source->getAttributes(), 'target' => $target->getAttributes(),
        'links' => $links->map->getAttributes()->all(), 'destinations' => $destinations,
        'findings' => $findings->map(fn ($r) => (array) $r)->all(),
        'files' => collect($paths)->mapWithKeys(fn ($p) => [$p => hash_file('sha256', base_path($p))])->all()];
};
$hash = static function (string $table, array $excluded = []): string {
    $h = hash_init('sha256');
    foreach (DB::table('translation_workbench_'.$table)->whereNotIn('id', $excluded)->orderBy('id')->cursor() as $r) {
        hash_update($h, json_encode($r, JSON_THROW_ON_ERROR));
    }
    return hash_final($h);
};
try {
    if ($argc === 1) {
        $result = DB::transaction(function () use ($build) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
            return $build();
        });
    } else {
        if ($argc !== 3 || $argv[1] !== '--apply') { throw new InvalidArgumentException('Use --apply PLAN.json.'); }
        $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
        $result = DB::transaction(function () use ($build, $hash, $plan, $sourceId, $spec) {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_lang_values, translation_workbench_reviews, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
            $fresh = $build();
            if (($fresh['state'] ?? null) === 'already_applied') { return $fresh; }
            if ($fresh !== $plan) { throw new RuntimeException('Stale plan; no changes applied.'); }
            $linkIds = collect($plan['links'])->where('status', 'active')->pluck('id')->all();
            $excluded = ['keys' => [$sourceId], 'key_findings' => $linkIds, 'findings' => [], 'key_values' => [],
                'dynamic_key_values' => [], 'lang_values' => [], 'reviews' => []];
            $before = collect($excluded)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            $timelineBefore = DB::table('translation_workbench_timeline_events')->count();
            $timeline = app(TranslationWorkbenchTimelineRecorder::class);
            $key = TranslationWorkbenchKey::findOrFail($sourceId);
            $context = ['source' => 'audited-stored-values-owner-reconciliation', 'canonical_key_id' => 321,
                'review_id' => $spec['review'], 'reason' => 'Scanner-created duplicate; explicit bulk decision covers this finding and the current source matches.'];
            foreach ($linkIds as $id) {
                $link = TranslationWorkbenchKeyFinding::findOrFail($id);
                $old = $link->getAttributes();
                $link->forceFill(['status' => 'merged'])->save();
                $timeline->record('key_assignment_reconciled', key: $key,
                    finding: TranslationWorkbenchFinding::findOrFail($link->finding_id),
                    oldValues: $old, newValues: $link->getAttributes(), context: $context);
            }
            $old = $key->only(['status', 'meta']);
            $key->forceFill(['status' => 'obsolete', 'meta' => [...($key->meta ?? []), 'reconciled_canonical_key_id' => 321]])->save();
            $timeline->recordKeyEvent($key, 'key_consolidation_reconciled', $old, $key->only(['status', 'meta']), $context);
            $after = collect($excluded)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            if ($before !== $after) { throw new RuntimeException('Unrelated records changed; rollback.'); }
            foreach ($plan['files'] as $path => $expected) {
                if (hash_file('sha256', base_path($path)) !== $expected) { throw new RuntimeException('Source changed; rollback.'); }
            }
            return ['state' => 'applied', 'retired_key' => $sourceId, 'canonical_key' => 321, 'merged_link_ids' => $linkIds,
                'timeline_events_created' => DB::table('translation_workbench_timeline_events')->count() - $timelineBefore,
                'unchanged_hashes' => $after];
        });
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}
