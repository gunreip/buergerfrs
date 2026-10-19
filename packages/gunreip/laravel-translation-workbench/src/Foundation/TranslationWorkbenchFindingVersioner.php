<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\DB;

/** An unambiguous move creates a successor, never rewrites the old source position. */
class TranslationWorkbenchFindingVersioner
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timeline) {}

    /** Follow only reciprocal, explicitly recorded source-position succession. */
    public static function lineageIds(int $id): array
    {
        $ids = [];
        $current = TranslationWorkbenchFinding::find($id);
        while ($current && ! in_array($current->id, $ids, true)) {
            $ids[] = $current->id;
            $previous = TranslationWorkbenchFinding::find($current->meta['predecessor_finding_id'] ?? 0);
            if (! $previous || $previous->status !== 'obsolete' || ($previous->meta['successor_finding_id'] ?? null) !== $current->id) { break; }
            $current = $previous;
        }
        return $ids;
    }

    public function move(TranslationWorkbenchFinding $finding, array $position, string $source): array
    {
        return DB::transaction(function () use ($finding, $position, $source) {
            $finding = TranslationWorkbenchFinding::whereKey($finding->id)->lockForUpdate()->firstOrFail();
            if ($finding->status !== 'active' || isset($finding->meta['successor_finding_id'])) {
                throw new \RuntimeException('Only a current finding can receive a source-position successor.');
            }
            $position = array_intersect_key($position, array_flip(['source_line', 'source_column', 'source_signature', 'source_fingerprint']));
            if (! isset($position['source_signature'], $position['source_line'])
                || ((int) $finding->source_line === (int) $position['source_line'] && $finding->source_column === ($position['source_column'] ?? null))) {
                throw new \RuntimeException('Finding versioning requires a changed source position.');
            }
            $links = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->orderBy('id')->get();
            $before = $finding->getAttributes();
            $next = $finding->replicate();
            $next->forceFill([...$position, 'first_seen_at' => now(), 'last_seen_at' => now(), 'scan_count' => 0,
                'meta' => [...($finding->meta ?? []), 'source_column' => $position['source_column'] ?? null, 'predecessor_finding_id' => $finding->id,
                    'root_finding_id' => $finding->meta['root_finding_id'] ?? $finding->id]]);
            $finding->forceFill(['status' => 'obsolete'])->save();
            $next->save();
            $finding->forceFill(['meta' => [...($finding->meta ?? []), 'successor_finding_id' => $next->id,
                'obsolete_reason' => 'source_position_superseded']])->save();
            $recorder = $this->timeline;
            $count = 0;
            foreach ($links as $link) {
                $old = $link->getAttributes();
                $new = $link->replicate();
                $new->finding_id = $next->id;
                $new->meta = [...($link->meta ?? []), 'predecessor_link_id' => $link->id];
                $new->save();
                $link->forceFill(['status' => 'obsolete', 'meta' => [...($link->meta ?? []),
                    'obsolete_reason' => 'source_position_superseded', 'successor_link_id' => $new->id]])->save();
                $key = TranslationWorkbenchKey::findOrFail($link->key_id);
                $recorder->record('finding_assignment_inherited', key: $key, finding: $next, oldValues: $old,
                    newValues: $new->getAttributes(), context: ['source' => $source, 'predecessor_finding_id' => $finding->id]);
                $count++;
            }
            if ($decision = $next->meta['explicit_key_assignment'] ?? null) {
                $review = TranslationWorkbenchReview::create(['key_id' => $decision['key_id'], 'finding_id' => $next->id,
                    'review_type' => 'finding_assignment', 'decision' => 'finding_assignment_selected',
                    'old_values' => ['predecessor_finding_id' => $finding->id, 'review_id' => $decision['review_id']],
                    'new_values' => $decision,
                    'meta' => ['source' => $source, 'inherited_from_finding_id' => $finding->id, 'inherited_from_review_id' => $decision['review_id']],
                    'reviewed_at' => now()]);
                $next->forceFill(['meta' => [...$next->meta, 'explicit_key_assignment' => [...$decision, 'review_id' => $review->id]]])->save();
            }
            $context = ['source' => $source, 'predecessor_finding_id' => $finding->id, 'successor_finding_id' => $next->id];
            $recorder->recordFindingEvent($finding, 'finding_superseded', $before, $finding->getAttributes(), $context);
            $recorder->recordFindingEvent($next, 'finding_position_successor_created', $finding->only(['source_line', 'source_column']), $next->getAttributes(), $context);
            return ['finding' => $next, 'timeline_events_created' => $count + 2, 'relations_obsoleted' => $links->count()];
        });
    }
}
