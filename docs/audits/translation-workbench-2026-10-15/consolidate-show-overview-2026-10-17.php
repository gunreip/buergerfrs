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

$build = static function (): array {
    $source = TranslationWorkbenchKey::findOrFail(4517);
    $target = TranslationWorkbenchKey::findOrFail(225);
    if ($source->status === 'obsolete' && ($source->meta['reconciled_canonical_key_id'] ?? null) === 225
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
    if (TranslationWorkbenchKey::where('translation_key', $source->translation_key)->where('status', '!=', 'obsolete')->count() !== 2
        || TranslationWorkbenchKey::where('meta->reconciled_canonical_key_id', $source->id)->exists()) {
        throw new RuntimeException('Additional owners or redirects require review.');
    }
    $links = TranslationWorkbenchKeyFinding::where('key_id', $source->id)->orderBy('id')->get();
    $active = $links->where('status', 'active');
    if ($active->pluck('finding_id')->sort()->values()->all() !== [2948, 4334, 4361, 4423, 4845]) {
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
    return ['source' => $source->getAttributes(), 'target' => $target->getAttributes(),
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
        $result = DB::transaction(function () use ($build, $hash, $plan) {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_lang_values, translation_workbench_reviews, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
            $fresh = $build();
            if (($fresh['state'] ?? null) === 'already_applied') { return $fresh; }
            if ($fresh !== $plan) { throw new RuntimeException('Stale plan; no changes applied.'); }
            $linkIds = collect($plan['links'])->where('status', 'active')->pluck('id')->all();
            $excluded = ['keys' => [4517], 'key_findings' => $linkIds, 'findings' => [], 'key_values' => [],
                'dynamic_key_values' => [], 'lang_values' => [], 'reviews' => []];
            $before = collect($excluded)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            $timelineBefore = DB::table('translation_workbench_timeline_events')->count();
            $timeline = app(TranslationWorkbenchTimelineRecorder::class);
            $key = TranslationWorkbenchKey::findOrFail(4517);
            $context = ['source' => 'audited-show-overview-owner-reconciliation', 'canonical_key_id' => 225,
                'reason' => 'Scanner-created pending duplicate; unique reviewed owner already linked to every current occurrence.'];
            foreach ($linkIds as $id) {
                $link = TranslationWorkbenchKeyFinding::findOrFail($id);
                $old = $link->getAttributes();
                $link->forceFill(['status' => 'merged'])->save();
                $timeline->record('key_assignment_reconciled', key: $key,
                    finding: TranslationWorkbenchFinding::findOrFail($link->finding_id),
                    oldValues: $old, newValues: $link->getAttributes(), context: $context);
            }
            $old = $key->only(['status', 'meta']);
            $key->forceFill(['status' => 'obsolete', 'meta' => [...($key->meta ?? []), 'reconciled_canonical_key_id' => 225]])->save();
            $timeline->recordKeyEvent($key, 'key_consolidation_reconciled', $old, $key->only(['status', 'meta']), $context);
            $after = collect($excluded)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            if ($before !== $after) { throw new RuntimeException('Unrelated records changed; rollback.'); }
            foreach ($plan['files'] as $path => $expected) {
                if (hash_file('sha256', base_path($path)) !== $expected) { throw new RuntimeException('Source changed; rollback.'); }
            }
            return ['state' => 'applied', 'retired_key' => 4517, 'canonical_key' => 225, 'merged_link_ids' => $linkIds,
                'timeline_events_created' => DB::table('translation_workbench_timeline_events')->count() - $timelineBefore,
                'unchanged_hashes' => $after];
        });
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}
