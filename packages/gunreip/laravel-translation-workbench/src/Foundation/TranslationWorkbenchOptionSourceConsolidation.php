<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Illuminate\Support\Facades\DB;
use LogicException;

/** Retain reviewed duplicate records as history; never delete values or merge reviews. */
class TranslationWorkbenchOptionSourceConsolidation
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timeline) {}

    public function process(array $requests, bool $apply = false): array
    {
        return DB::transaction(function () use ($requests, $apply): array {
            $result = []; $seen = [];
            foreach ($requests as $request) {
                $require = static function (bool $condition, string $reason) use ($request): void {
                    if (! $condition) { throw new LogicException('Duplicate source '.$request['source_id'].': '.$reason); }
                };
                $old = DB::table('translation_workbench_dynamic_sources')->where('id', $request['source_id'])->when($apply, fn ($q) => $q->lockForUpdate())->first();
                $keep = DB::table('translation_workbench_dynamic_sources')->where('id', $request['canonical_source_id'])->when($apply, fn ($q) => $q->lockForUpdate())->first();
                $require(! isset($seen[$request['source_id']]) && $request['source_id'] !== $request['canonical_source_id'], 'duplicate or self-referencing request');
                $seen[$request['source_id']] = true;
                $require($old !== null && $keep !== null && $keep->status === 'active', 'missing or inactive canonical source');
                $meta = json_decode($old->meta ?? '{}', true, flags: JSON_THROW_ON_ERROR);
                $already = $old->status === 'obsolete' && ($meta['superseded_by_source_id'] ?? null) === $keep->id;
                $require($old->status === 'active' || $already, 'unexpected historical status');
                foreach (['key_id', 'option_discovery_id', 'source_path', 'source_type'] as $field) {
                    $require($old->{$field} === $keep->{$field}, 'identity differs: '.$field);
                }
                $require($old->source_type === 'hardcoded_public_array' && $old->key_id === $request['key_id'], 'unreviewed source type/key');
                $previous = TranslationWorkbenchFinding::with('sourceFile')->find($old->finding_id);
                $target = TranslationWorkbenchFinding::with(['sourceFile', 'keys' => fn ($q) => $q->where('translation_workbench_key_findings.status', 'active')->where('translation_workbench_keys.status', '<>', 'obsolete')])->find($keep->finding_id);
                $require($previous !== null && $previous->status === 'obsolete' && $target !== null && $target->status === 'active'
                    && $target->sourceFile?->status === 'active' && $target->keys->count() === 1 && $target->keys->sole()->id === $keep->key_id, 'finding/key identity differs');
                $require($previous->sourceFile?->path === $keep->source_path && $target->sourceFile->path === $keep->source_path, 'finding paths differ');
                $require(is_file(base_path($keep->source_path)) && hash_file('sha256', base_path($keep->source_path)) === $request['file_hash'], 'source file changed');
                $observation = TranslationWorkbenchOptionDiscovery::find($keep->option_discovery_id);
                $assignment = $observation?->meta['foundation_assignment'] ?? [];
                $require(($assignment['status'] ?? null) === 'matched' && ($assignment['finding_id'] ?? null) === $keep->finding_id
                    && ($assignment['key_id'] ?? null) === $keep->key_id, 'canonical observation is not assigned');
                $normalize = static function (array $v): array { ksort($v); return $v; };
                foreach ([$old, $keep] as $source) {
                    $values = DB::table('translation_workbench_dynamic_source_values')->where('dynamic_source_id', $source->id)->where('status', 'active')->pluck('native_label', 'value_key')->all();
                    $require($values !== [] && $normalize($values) === $normalize($request['options']), 'source values differ');
                }
                $require($normalize($observation->options ?? []) === $normalize($request['options']), 'observation values differ');
                $links = DB::table('translation_workbench_dynamic_source_candidates')->where(function ($q) use ($old) {
                    $q->where('dynamic_source_id', $old->id)->orWhere('candidate_reference', 'dynamic_source:'.$old->id)->orWhere('meta->related_source_id', $old->id);
                })->when($apply, fn ($q) => $q->lockForUpdate())->get();
                $require($links->pluck('id')->sort()->values()->all() === collect(array_keys($request['candidate_replacements']))->map(fn ($v) => (int) $v)->sort()->values()->all(), 'candidate references changed');
                foreach ($links as $link) {
                    $replacement = DB::table('translation_workbench_dynamic_source_candidates')->where('id', $request['candidate_replacements'][$link->id])->when($apply, fn ($q) => $q->lockForUpdate())->first();
                    $linkMeta = json_decode($link->meta ?? '{}', true, flags: JSON_THROW_ON_ERROR);
                    $require($replacement !== null && $replacement->status === 'active' && $replacement->review_status === 'confirmed'
                        && $link->review_status === 'confirmed' && $link->candidate_source_type === 'related_dynamic_source'
                        && $replacement->candidate_source_type === 'related_dynamic_source'
                        && $link->candidate_reference === 'dynamic_source:'.$old->id && $replacement->candidate_reference === 'dynamic_source:'.$keep->id
                        && $link->dynamic_source_id === $replacement->dynamic_source_id && $link->key_id === $keep->key_id && $replacement->key_id === $keep->key_id
                        && $link->finding_id === $old->finding_id && $replacement->finding_id === $keep->finding_id
                        && $normalize(json_decode($link->candidate_values, true, flags: JSON_THROW_ON_ERROR)) === $normalize(json_decode($replacement->candidate_values, true, flags: JSON_THROW_ON_ERROR)), 'confirmed candidate replacement differs');
                    $require($already ? ($link->status === 'obsolete' && ($linkMeta['superseded_by_candidate_id'] ?? null) === $replacement->id) : $link->status === 'active', 'candidate retirement state differs');
                    if ($apply && ! $already) {
                        DB::table('translation_workbench_dynamic_source_candidates')->where('id', $link->id)->update([
                            'status' => 'obsolete', 'meta' => json_encode([...$linkMeta, 'superseded_by_candidate_id' => $replacement->id], JSON_THROW_ON_ERROR), 'updated_at' => now(),
                        ]);
                    }
                }
                if ($apply && ! $already) {
                    DB::table('translation_workbench_dynamic_sources')->where('id', $old->id)->update([
                        'status' => 'obsolete', 'meta' => json_encode([...$meta, 'superseded_by_source_id' => $keep->id], JSON_THROW_ON_ERROR), 'updated_at' => now(),
                    ]);
                    $this->timeline->record(eventType: 'dynamic_option_duplicate_retired', key: $target->keys->sole(), finding: $target,
                        oldValues: ['source_id' => $old->id, 'status' => $old->status, 'meta' => $meta, 'candidates' => $links->map(fn ($row) => (array) $row)->all()],
                        newValues: ['source_id' => $old->id, 'status' => 'obsolete', 'superseded_by_source_id' => $keep->id, 'candidate_replacements' => $request['candidate_replacements']],
                        context: ['reviewed_file_hash' => $request['file_hash'], 'values_and_reviews_preserved' => true]);
                }
                $result[] = ['source_id' => $old->id, 'canonical_source_id' => $keep->id, 'status' => $already ? 'already_consolidated' : ($apply ? 'consolidated' : 'ready'), 'candidate_replacements' => $request['candidate_replacements']];
            }
            return $result;
        });
    }
}
