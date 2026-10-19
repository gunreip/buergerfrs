<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Illuminate\Support\Facades\DB;
use LogicException;

/** Explicit retirement of reviewed stale suggestions; preserves snapshots and review decisions. */
class TranslationWorkbenchRuntimeSuggestionRetirement
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timeline) {}

    /** Requests identify candidate, finding and consumer IDs plus the reviewed snapshot hash. */
    public function process(array $requests, bool $apply = false): array
    {
        return DB::transaction(function () use ($requests, $apply): array {
            $result = [];
            $seen = [];
            foreach ($requests as $request) {
                $require = static function (bool $ok, string $reason): void {
                    if (! $ok) { throw new LogicException('Runtime suggestion retirement: '.$reason); }
                };
                $id = $request['candidate_id'];
                $require(! isset($seen[$id]), 'duplicate request');
                $seen[$id] = true;
                $candidate = DB::table('translation_workbench_dynamic_source_candidates')->where('id', $id)->lockForUpdate()->first();
                $require($candidate !== null && $candidate->review_status === 'pending'
                    && $candidate->candidate_source_type === 'related_dynamic_source', 'not an unreviewed runtime suggestion');
                $require($candidate->finding_id === $request['finding_id']
                    && $candidate->candidate_reference === 'dynamic_source:'.$request['consumer_source_id']
                    && hash('sha256', $candidate->candidate_values) === $request['snapshot_hash'], 'reviewed identity or snapshot changed');
                $finding = TranslationWorkbenchFinding::whereKey($candidate->finding_id)->lockForUpdate()->first();
                $require($finding !== null && $finding->status === 'obsolete', 'finding is not obsolete');
                $consumer = DB::table('translation_workbench_dynamic_sources')->where('id', $request['consumer_source_id'])->lockForUpdate()->first();
                $require($consumer !== null && $consumer->finding_id === $finding->id, 'consumer identity changed');
                $require(! DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)->where('status', 'active')->exists(), 'finding still has active key links');
                $meta = json_decode($candidate->meta ?? '{}', true, flags: JSON_THROW_ON_ERROR);
                $proof = ['reason' => 'obsolete_consumer_finding', 'finding_id' => $finding->id, 'consumer_source_id' => $consumer->id,
                    'snapshot_hash' => $request['snapshot_hash']];
                $already = $candidate->status === 'obsolete' && ($meta['reviewed_retirement'] ?? null) === $proof;
                $require($already || ($candidate->status === 'active' && ! isset($meta['reviewed_retirement'])), 'unexpected retirement state');
                if ($apply && ! $already) {
                    DB::table('translation_workbench_dynamic_source_candidates')->where('id', $id)->update([
                        'status' => 'obsolete', 'meta' => json_encode([...$meta, 'reviewed_retirement' => $proof], JSON_THROW_ON_ERROR), 'updated_at' => now(),
                    ]);
                    $this->timeline->record(eventType: 'dynamic_runtime_suggestion_retired',
                        key: $candidate->key_id ? TranslationWorkbenchKey::find($candidate->key_id) : null, finding: $finding,
                        oldValues: ['candidate_id' => $id, 'status' => $candidate->status],
                        newValues: ['candidate_id' => $id, 'status' => 'obsolete'], context: $proof);
                }
                $result[] = ['candidate_id' => $id, 'status' => $already ? 'already_retired' : ($apply ? 'retired' : 'ready_to_retire')];
            }
            return $result;
        });
    }
}
