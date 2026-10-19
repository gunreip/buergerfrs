<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Historical reactivation evidence is read, never restored into the original event. */
class TranslationWorkbenchConsolidationEvidence
{
    public function all(): Collection
    {
        $evidence = TranslationWorkbenchKeyFinding::query()->where('meta->source', 'duplicate_key_consolidation')->get();
        $events = TranslationWorkbenchTimelineEvent::query()
            ->where('event_type', 'key_finding_relation_changed')
            ->where('old_values->meta->source', 'duplicate_key_consolidation')
            ->where('new_values->status', 'active')->orderBy('id')->get();
        foreach ($events as $event) {
            $old = $event->old_values ?? [];
            $new = $event->new_values ?? [];
            $meta = $old['meta'] ?? [];
            if (! in_array($old['status'] ?? null, ['obsolete', 'merged'], true)
                || ($new['meta']['source'] ?? null) !== 'foundation_sync'
                || ($event->context['source'] ?? null) !== 'translation-workbench:sync-foundation'
                || (int) ($meta['duplicate_key_id'] ?? 0) !== (int) $event->key_id
                || ! $event->finding_id || ! ($meta['canonical_key_id'] ?? null)
                || (int) $meta['canonical_key_id'] === (int) $event->key_id) {
                continue;
            }
            $links = TranslationWorkbenchKeyFinding::where('key_id', $event->key_id)
                ->where('finding_id', $event->finding_id)->where('relation_type', 'candidate')->where('status', 'active')->get();
            if ($links->count() !== 1 || ($links->first()->meta['source'] ?? null) !== 'foundation_sync') {
                continue;
            }
            // No later human review or non-scan action may be silently undone.
            if (DB::table('translation_workbench_reviews')->where('key_id', $event->key_id)
                ->where('created_at', '>=', $event->created_at)->exists()) {
                continue;
            }
            $later = TranslationWorkbenchTimelineEvent::where('key_id', $event->key_id)->where('id', '>', $event->id)->get();
            if ($later->contains(fn ($row) => $row->review_id !== null || $row->created_by_user_id !== null
                || (($row->context['source'] ?? null) !== 'translation-workbench:sync-foundation' && ! $this->isShapeClassification($row)))) {
                continue;
            }
            // Carry exact evidence into the signed plan and its fresh comparison.
            $evidence->push(new TranslationWorkbenchKeyFinding([
                'finding_id' => $event->finding_id,
                'meta' => $meta,
                'evidence_event_id' => $event->id,
                'evidence_event_hash' => hash('sha256', json_encode($event->getAttributes(), JSON_THROW_ON_ERROR)),
            ]));
        }

        return $evidence;
    }
    private function isShapeClassification(TranslationWorkbenchTimelineEvent $event): bool
    {
        $old = $event->old_values ?? [];
        $new = $event->new_values ?? [];
        $key = DB::table('translation_workbench_keys')->where('id', $event->key_id)->value('translation_key');

        return $event->event_type === 'lang_node_type_classified'
            && ($event->context['source'] ?? null) === 'translation-workbench:classify-lang-node-types'
            && array_diff(array_keys($old), ['lang_node_type', 'translation_key']) === []
            && array_diff(array_keys($new), ['lang_node_type', 'translation_key']) === []
            && isset($new['lang_node_type'], $new['translation_key'])
            && $new['translation_key'] === $key
            && (! isset($old['translation_key']) || $old['translation_key'] === $key);
    }

}
