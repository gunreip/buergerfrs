<?php

// Read-only by default. Retire only historically misbound links with live independent owners.
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
    $keys = DB::table('translation_workbench_keys')->orderBy('id')->get()->keyBy('id');
    $findings = DB::table('translation_workbench_findings as f')->join('translation_workbench_source_files as s', 's.id', '=', 'f.source_file_id')
        ->where('f.status', 'active')->orderBy('f.id')->get(['f.*', 's.path as source_path'])->keyBy('id');
    $links = DB::table('translation_workbench_key_findings')->where('status', 'active')->orderBy('id')->get();
    $groups = $links->whereIn('finding_id', $findings->keys())->groupBy('finding_id')->filter(fn ($g) => $g->count() > 1
        && $g->every(fn ($l) => $keys[$l->key_id]->translation_key === null));
    $keyIds = $groups->flatten(1)->pluck('key_id')->unique();
    $blocked = collect();
    foreach (['reviews', 'key_values', 'dynamic_key_values'] as $table) {
        $blocked = $blocked->merge(DB::table('translation_workbench_'.$table)->whereIn('key_id', $keyIds)->pluck('key_id'));
    }
    $candidateFindings = $links->whereIn('key_id', $keyIds)->pluck('finding_id')->merge($groups->keys())->unique();
    $paths = $findings->only($candidateFindings->all())->pluck('source_path')->unique()->sort()->values();
    $files = $paths->mapWithKeys(fn ($p) => [$p => is_file(base_path($p)) ? hash_file('sha256', base_path($p)) : null])->all();
    $report = app(TranslationWorkbenchSourceReconciliation::class)->plan($paths->all());
    $confirmed = collect($report['rows'])->filter(fn ($r) => in_array($r['state'], ['unchanged', 'position_refined'], true)
        && count($r['finding_ids']) === 1)->keyBy(fn ($r) => $r['finding_ids'][0]);
    $events = DB::table('translation_workbench_timeline_events')->whereIn('finding_id', $groups->keys())
        ->where('event_type', 'finding_changed')->orderBy('id')->get()->groupBy('finding_id');
    $actions = []; $deferred = [];
    foreach ($groups as $findingId => $group) {
        $finding = $findings[$findingId];
        $owners = $group->filter(fn ($l) => $keys[$l->key_id]->suggested_key === $finding->suggested_key);
        $eligible = $confirmed->has($findingId) && $finding->kind === 'literal' && $owners->count() === 1
            && $group->every(fn ($l) => $keys[$l->key_id]->status === 'open' && $keys[$l->key_id]->review_status === 'pending'
                && $keys[$l->key_id]->reviewed_at === null && ! $keys[$l->key_id]->is_dynamic_key
                && ! $blocked->contains($l->key_id) && $l->relation_type === 'candidate');
        foreach ($group as $link) {
            if ($owners->contains('id', $link->id)) { continue; }
            $key = $keys[$link->key_id];
            $history = ($events->get($findingId) ?? collect())->filter(function ($event) use ($key) {
                $old = json_decode($event->old_values ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                $new = json_decode($event->new_values ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                $context = json_decode($event->context ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                return ($context['source'] ?? null) === 'translation-workbench:sync-foundation'
                    && ($old['suggested_key'] ?? null) === $key->suggested_key
                    && isset($old['source_file_id'], $new['source_file_id']) && $old['source_file_id'] !== $new['source_file_id'];
            });
            $witnesses = $links->where('key_id', $key->id)->filter(function ($other) use ($finding, $key, $findings, $confirmed) {
                $f = $findings->get($other->finding_id);
                return $f && $f->id !== $finding->id && $f->source_file_id !== $finding->source_file_id
                    && $other->relation_type === 'candidate' && $f->suggested_key === $key->suggested_key
                    // Formatting of __() can differ between files; keep the literal and function exact.
                    && $f->kind === 'literal' && $f->literal_text === $finding->literal_text && $f->function_name === $finding->function_name
                    && $confirmed->has($f->id);
            });
            if (! $eligible || $history->isEmpty() || $witnesses->isEmpty()
                || (json_decode($link->meta ?? '{}', true)['source'] ?? null) !== 'foundation_sync') {
                $deferred[] = ['link_id' => $link->id, 'finding_id' => $findingId, 'key_id' => $key->id,
                    'reason' => ! $eligible ? 'Owner/source/review requires inspection' : ($history->isEmpty() ? 'No historic cross-file reuse evidence' : 'No independent confirmed current finding')];
                continue;
            }
            $actions[] = ['link' => (array) $link, 'finding' => (array) $finding, 'key' => (array) $key,
                'retained_link' => (array) $owners->sole(), 'retained_key' => (array) $keys[$owners->sole()->key_id],
                'witness_links' => $witnesses->map(fn ($r) => (array) $r)->values()->all(),
                'witness_findings' => $witnesses->map(fn ($r) => (array) $findings[$r->finding_id])->values()->all(),
                'history_ids' => $history->pluck('id')->all()];
        }
    }
    foreach ($files as $p => $hash) {
        if ((is_file(base_path($p)) ? hash_file('sha256', base_path($p)) : null) !== $hash) { throw new RuntimeException('Source changed during planning.'); }
    }
    return ['version' => 1, 'groups' => $groups->count(), 'files' => $files, 'actions' => $actions, 'deferred' => $deferred];
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
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY'); return $build();
        });
    } else {
        if ($argc !== 3 || $argv[1] !== '--apply') { throw new InvalidArgumentException('Use --apply PLAN.json.'); }
        $plan = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
        $result = DB::transaction(function () use ($build, $hash, $plan) {
            DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_lang_values, translation_workbench_reviews, translation_workbench_timeline_events IN SHARE ROW EXCLUSIVE MODE');
            if ($build() !== $plan) { throw new RuntimeException('Stale plan; no changes applied.'); }
            $ids = array_column(array_column($plan['actions'], 'link'), 'id');
            $excluded = ['source_files' => [], 'findings' => [], 'keys' => [], 'key_findings' => $ids,
                'key_values' => [], 'dynamic_key_values' => [], 'lang_values' => [], 'reviews' => []];
            $before = collect($excluded)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            $timeline = app(TranslationWorkbenchTimelineRecorder::class);
            $eventIds = [];
            foreach ($plan['actions'] as $a) {
                $link = TranslationWorkbenchKeyFinding::findOrFail($a['link']['id']);
                $old = $link->getAttributes();
                $link->forceFill(['status' => 'obsolete', 'meta' => [...($link->meta ?? []),
                    'obsolete_reason' => 'historical_cross_file_finding_reuse', 'retained_key_id' => $a['retained_key']['id']]])->save();
                $event = $timeline->record('key_assignment_reconciled', key: TranslationWorkbenchKey::findOrFail($link->key_id),
                    finding: TranslationWorkbenchFinding::findOrFail($link->finding_id), oldValues: $old, newValues: $link->getAttributes(),
                    context: ['source' => 'audited-unkeyed-source-link-reconciliation', 'reason' => 'historical_cross_file_finding_reuse',
                        'retained_key_id' => $a['retained_key']['id'], 'independent_finding_ids' => array_column($a['witness_findings'], 'id'),
                        'history_event_ids' => $a['history_ids']]);
                $eventIds[] = $event->id;
            }
            $after = collect($excluded)->map(fn ($ids, $table) => $hash($table, $ids))->all();
            if ($before !== $after) { throw new RuntimeException('Unrelated records changed; rollback.'); }
            foreach ($plan['files'] as $p => $expected) {
                if ((is_file(base_path($p)) ? hash_file('sha256', base_path($p)) : null) !== $expected) { throw new RuntimeException('Source changed; rollback.'); }
            }
            return ['state' => 'applied', 'retired_link_ids' => $ids, 'affected_finding_ids' => array_values(array_unique(array_column(array_column($plan['actions'], 'finding'), 'id'))),
                'timeline_event_ids' => $eventIds, 'unchanged_hashes' => $after];
        });
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage().PHP_EOL); exit(1); }
