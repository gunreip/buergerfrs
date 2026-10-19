<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Consume only a recorded, position-verified write; never infer a rename from text similarity. */
class TranslationWorkbenchCodeUpdateTransition
{
    public function matches(string $path, Collection $items, Collection $stored): array
    {
        $active = $stored->where('status', 'active')->keyBy('id');
        $events = DB::table('translation_workbench_timeline_events')->where('event_type', 'code_update_applied')
            ->whereIn('finding_id', $active->keys())->orderByDesc('id')->get()->unique('finding_id');
        $matches = [];
        foreach ($events as $event) {
            $old = json_decode($event->old_values ?? '{}', true) ?? [];
            $new = json_decode($event->new_values ?? '{}', true) ?? [];
            $finding = $active[$event->finding_id];
            if (($new['replacement_scope'] ?? null) !== 'single_verified_source_position' || empty($new['source_hash'])
                || ($old['raw_expression'] ?? null) !== $finding->raw_expression
                || ($new['new_expression'] ?? null) === $finding->raw_expression) { continue; }
            $key = TranslationWorkbenchKey::find($event->key_id);
            $candidates = $items->filter(fn ($item) => $item->rawExpression === ($new['new_expression'] ?? null)
                && $item->translationKey === ($new['translation_key'] ?? null)
                && $item->sourceLine === ($new['updated_source_line'] ?? null)
                && $item->sourceColumn === ($new['source_column'] ?? null)
                && ($item->meta['source_context'] ?? null) !== 'commented_out');
            $links = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)
                ->where('status', 'active')->pluck('key_id')->all();
            if (! is_file(base_path($path)) || hash_file('sha256', base_path($path)) !== $new['source_hash']
                || ($new['source_path'] ?? null) !== $path || ! $key || $key->status === 'obsolete'
                || $key->translation_key !== ($new['translation_key'] ?? null) || $links !== [$key->id]
                || $candidates->count() !== 1) { $this->fail($finding->id); }
            $item = $candidates->first();
            if (isset($matches[$item->sourceSignature]) || $active->contains(fn ($other) => $other->id !== $finding->id
                && $other->source_signature === $item->sourceSignature)) { $this->fail($finding->id); }
            $matches[$item->sourceSignature] = ['state' => 'code_updated', 'finding_ids' => [$finding->id],
                'source_signature' => $item->sourceSignature, 'event_id' => $event->id, 'key_id' => $key->id];
        }
        return $matches;
    }

    public function assignedKey(TranslationWorkbenchFinding $finding, object $item): ?TranslationWorkbenchKey
    {
        $assignment = $finding->meta['code_update_assignment'] ?? null;
        if (! $assignment) { return null; }
        $key = TranslationWorkbenchKey::find($assignment['key_id']);
        $links = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all();
        if (! $key || $key->status === 'obsolete' || $links !== [$key->id]
            || $key->translation_key !== $assignment['translation_key'] || $item->translationKey !== $assignment['translation_key']
            || $item->expressionFingerprint !== $assignment['expression_fingerprint']) { $this->fail($finding->id); }
        return $key;
    }

    private function fail(int $findingId): never
    {
        throw new \RuntimeException('The recorded code update for finding #'.$findingId.' no longer matches its source or assignment. Review the transition before scanning again.');
    }
}
