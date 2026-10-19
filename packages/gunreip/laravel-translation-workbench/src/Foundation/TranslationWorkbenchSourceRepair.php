<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Illuminate\Support\Facades\DB;

/** Builds and applies only freshly reproducible, explicitly scoped repairs. */
class TranslationWorkbenchSourceRepair
{
    public function __construct(private readonly TranslationWorkbenchSourceReconciliation $reconciliation,
        private readonly TranslationWorkbenchTimelineRecorder $timeline) {}

    public function plan(?array $paths = null): array
    {
        $report = $this->reconciliation->plan($paths);
        $actions = [];
        foreach ($report['rows'] as $row) {
            if (in_array($row['state'], ['moved', 'position_refined'], true)) {
                $finding = TranslationWorkbenchFinding::findOrFail($row['finding_ids'][0]);
                $actions[] = ['type' => $row['state'] === 'position_refined' ? 'refine_position' : 'move', 'id' => $finding->id,
                    'before' => $finding->getAttributes(), 'after' => $row['position'],
                    'files' => [$row['path'] => hash_file('sha256', base_path($row['path']))]];
            }
        }
        $rows = collect($report['rows'])->filter(fn ($r) => in_array($r['state'], ['unchanged', 'moved', 'position_refined']))
            ->keyBy(fn ($r) => $r['finding_ids'][0]);
        $evidence = app(TranslationWorkbenchConsolidationEvidence::class)->all();
        foreach ($evidence->groupBy(fn ($link) => $link->meta['duplicate_key_id'] ?? 0) as $duplicateId => $decisions) {
            $targets = $decisions->map(fn ($link) => (int) ($link->meta['canonical_key_id'] ?? 0))->unique();
            if (! $duplicateId || $targets->count() !== 1 || ! $targets->first() || (int) $duplicateId === $targets->first()) {
                continue;
            }
            $source = TranslationWorkbenchKey::find($duplicateId);
            $target = TranslationWorkbenchKey::find($targets->first());
            if (! $source || ! $target || $source->status === 'obsolete' || $target->status === 'obsolete'
                || ! $source->translation_key || $source->translation_key !== $target->translation_key
                || $source->review_status === 'reviewed' || $target->is_dynamic_key || $source->is_dynamic_key) {
                continue;
            }
            // Per-owner translations need a separate value decision; never move them implicitly.
            if (DB::table('translation_workbench_key_values')->where('key_id', $source->id)->exists()
                || DB::table('translation_workbench_dynamic_key_values')->where('key_id', $source->id)->exists()) {
                continue;
            }
            $links = TranslationWorkbenchKeyFinding::where('key_id', $source->id)->orderBy('id')->get();
            $active = $links->where('status', 'active');
            if ($active->isEmpty()) {
                continue;
            }
            $files = [];
            $safe = true;
            foreach ($active as $link) {
                $row = $rows->get($link->finding_id);
                $destination = TranslationWorkbenchKeyFinding::where('key_id', $target->id)->where('finding_id', $link->finding_id)->where('status', 'active')->get();
                if (! $row || $row['scanned_key'] !== $target->translation_key || $destination->count() !== 1
                    || $link->relation_type !== 'candidate' || $destination->first()->relation_type !== 'candidate') {
                    $safe = false;
                    break;
                }
                // Each retired link needs its own evidence, not just a decision for some other occurrence.
                if (! $decisions->contains(fn ($decision) => $decision->finding_id === $link->finding_id)) {
                    $safe = false;
                    break;
                }
                $files[$row['path']] = hash_file('sha256', base_path($row['path']));
            }
            if ($safe) {
                $actions[] = ['type' => 'consolidate', 'id' => $source->id, 'target' => $target->id,
                    'before' => ['source' => $source->getAttributes(), 'target' => $target->getAttributes(),
                        'links' => $links->map->getAttributes()->all(), 'decisions' => $decisions->map->getAttributes()->values()->all()],
                    'link_ids' => $active->pluck('id')->values()->all(), 'files' => $files];
            }
        }
        // Do not execute chains/cycles where another planned action retires a destination.
        $retired = collect($actions)->where('type', 'consolidate')->pluck('id')->all();
        $actions = array_values(array_filter($actions, fn ($a) => $a['type'] !== 'consolidate' || ! in_array($a['target'], $retired, true)));

        return ['version' => 1, 'paths' => $paths, 'summary' => $report['summary'], 'actions' => $actions];
    }

    public function apply(array $plan): array
    {
        if (($plan['version'] ?? null) !== 1 || ! isset($plan['actions'])) {
            throw new \InvalidArgumentException('Unsupported repair plan.');
        }
        return DB::transaction(function () use ($plan): array {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_reviews, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
            }
            $fresh = $this->plan($plan['paths']);
            $available = collect($fresh['actions'])->keyBy(fn ($a) => $a['type'].':'.$a['id']);
            $results = [];
            foreach ($plan['actions'] as $action) {
                $identity = $action['type'].':'.$action['id'];
                if ($available->get($identity) !== $action) {
                    $results[] = ['action' => $identity, 'state' => 'skipped', 'reason' => 'Changed, ambiguous or already applied; create a fresh plan.'];
                    continue;
                }
                foreach ($action['files'] as $path => $hash) {
                    if (! is_file(base_path($path)) || hash_file('sha256', base_path($path)) !== $hash) {
                        throw new \RuntimeException('Source changed during repair; transaction rolled back: '.$path);
                    }
                }
                if (in_array($action['type'], ['move', 'refine_position'], true)) {
                    $finding = TranslationWorkbenchFinding::findOrFail($action['id']);
                    if ($action['type'] === 'move') {
                        (new TranslationWorkbenchFindingVersioner($this->timeline))->move($finding, $action['after'], 'translation-workbench:reconcile-sources');
                    } else {
                        $old = $finding->only(array_keys($action['after']));
                        $finding->forceFill($action['after'])->save();
                        $this->timeline->recordFindingEvent($finding, 'finding_position_reconciled', $old, $action['after'], ['source' => 'translation-workbench:reconcile-sources']);
                    }
                } else {
                    $key = TranslationWorkbenchKey::findOrFail($action['id']);
                    foreach ($action['link_ids'] as $id) {
                        $link = TranslationWorkbenchKeyFinding::findOrFail($id);
                        $old = $link->getAttributes();
                        $link->forceFill(['status' => 'merged'])->save();
                        $this->timeline->record('key_assignment_reconciled', key: $key,
                            finding: TranslationWorkbenchFinding::findOrFail($link->finding_id),
                            oldValues: $old, newValues: $link->getAttributes(),
                            context: ['source' => 'translation-workbench:reconcile-sources', 'canonical_key_id' => $action['target']]);
                    }
                    $old = $key->only(['status', 'meta']);
                    $key->forceFill(['status' => 'obsolete', 'meta' => [...($key->meta ?? []),
                        'reconciled_canonical_key_id' => $action['target']]])->save();
                    $this->timeline->recordKeyEvent($key, 'key_consolidation_reconciled', $old, $key->only(['status', 'meta']), ['source' => 'translation-workbench:reconcile-sources']);
                }
                $results[] = ['action' => $identity, 'state' => 'applied'];
            }
            // Catch source edits during the DB writes as well. No file is written by this operation.
            foreach ($plan['actions'] as $action) {
                if (! collect($results)->contains(fn ($r) => $r['action'] === $action['type'].':'.$action['id'] && $r['state'] === 'applied')) {
                    continue;
                }
                foreach ($action['files'] as $path => $hash) {
                    if (! is_file(base_path($path)) || hash_file('sha256', base_path($path)) !== $hash) {
                        throw new \RuntimeException('Source changed during repair; transaction rolled back: '.$path);
                    }
                }
            }
            return $results;
        });
    }
}
