<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Retire only keys whose links were explicitly reassigned to one surviving owner. */
class TranslationWorkbenchUnusedKeyRetirement
{
    private function snapshot(string $text): array
    {
        $keys = DB::table('translation_workbench_keys')->where('translation_key', $text)->orderBy('id')->get();
        $links = DB::table('translation_workbench_key_findings')->whereIn('key_id', $keys->pluck('id'))->orderBy('id')->get();
        $result = ['text' => $text, 'keys' => $keys, 'links' => $links,
            'findings' => DB::table('translation_workbench_findings')->whereIn('id', $links->pluck('finding_id'))->orderBy('id')->get()];
        foreach (['reviews', 'key_values', 'dynamic_key_values'] as $table) {
            $result[$table] = DB::table('translation_workbench_'.$table)->whereIn('key_id', $keys->pluck('id'))->orderBy('id')->get();
        }
        return json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    private function candidates(array $snapshot): array
    {
        $keys = collect($snapshot['keys'])->keyBy('id');
        $reviews = collect($snapshot['reviews'])->keyBy('id');
        $findings = collect($snapshot['findings'])->keyBy('id');
        $result = [];
        foreach ($keys as $key) {
            if ($key['status'] === 'obsolete' || $key['is_dynamic_key']) { continue; }
            if (collect($snapshot['key_values'])->contains('key_id', $key['id'])
                || collect($snapshot['dynamic_key_values'])->contains('key_id', $key['id'])) { continue; }
            $links = collect($snapshot['links'])->where('key_id', $key['id']);
            if ($links->isEmpty()) { continue; }
            $targets = [];
            foreach ($links as $link) {
                $meta = json_decode($link['meta'] ?? '{}', true) ?? [];
                $review = $reviews->get($meta['assignment_review_id'] ?? null);
                $finding = $findings->get($link['finding_id']);
                $decision = json_decode($finding['meta'] ?? '{}', true)['explicit_key_assignment'] ?? [];
                $target = $keys->get($review['key_id'] ?? null);
                if ($link['status'] !== 'obsolete' || ($meta['obsolete_reason'] ?? null) !== 'explicit_finding_assignment'
                    || ! $review || $review['decision'] !== 'finding_assignment_selected' || $review['finding_id'] !== $link['finding_id']
                    || ! $target || $target['status'] === 'obsolete' || $target['is_dynamic_key'] || $target['id'] === $key['id']
                    || ($decision['review_id'] ?? null) !== $review['id'] || ($decision['key_id'] ?? null) !== $target['id']
                    || collect($snapshot['links'])->where('finding_id', $link['finding_id'])->where('status', 'active')->pluck('key_id')->all() !== [$target['id']]) {
                    continue 2;
                }
                $targets[] = $target['id'];
            }
            $targets = array_values(array_unique($targets));
            if (count($targets) === 1) {
                $result[] = ['key_id' => $key['id'], 'target_id' => $targets[0], 'finding_ids' => $links->pluck('finding_id')->unique()->values()->all()];
            }
        }
        return $result;
    }

    public function preview(string $text): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        if (trim($text) === '') { return []; }
        $snapshot = $this->snapshot($text);
        return array_map(fn ($candidate) => [...$candidate, 'proof' => Crypt::encryptString(json_encode($snapshot, JSON_THROW_ON_ERROR))], $this->candidates($snapshot));
    }

    public function retire(int $keyId, string $proof): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        try { $expected = json_decode(Crypt::decryptString($proof), true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable) { $this->fail(); }
        DB::transaction(function () use ($keyId, $expected) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_keys, translation_workbench_key_findings, translation_workbench_findings, translation_workbench_reviews, translation_workbench_key_values, translation_workbench_dynamic_key_values IN SHARE ROW EXCLUSIVE MODE');
            }
            $current = $this->snapshot($expected['text']);
            $candidate = collect($this->candidates($current))->firstWhere('key_id', $keyId);
            if ($current !== $expected || ! $candidate) { $this->fail(); }
            $key = TranslationWorkbenchKey::findOrFail($keyId);
            $old = $key->getAttributes();
            $review = TranslationWorkbenchReview::create(['key_id' => $keyId, 'review_type' => 'key_retirement',
                'decision' => 'unassigned_key_retired', 'old_values' => ['status' => $key->status],
                'new_values' => ['status' => 'obsolete', 'canonical_key_id' => $candidate['target_id']],
                'meta' => ['source' => 'workbench-unused-key-retirement', 'finding_ids' => $candidate['finding_ids']],
                'reviewed_by_user_id' => Auth::id(), 'reviewed_at' => now()]);
            $key->forceFill(['status' => 'obsolete', 'meta' => [...($key->meta ?? []),
                'reconciled_canonical_key_id' => $candidate['target_id'], 'retirement_review_id' => $review->id]])->save();
            app(TranslationWorkbenchTimelineRecorder::class)->record('unassigned_key_retired', key: $key, review: $review,
                oldValues: $old, newValues: $key->getAttributes(), context: ['source' => 'workbench-unused-key-retirement']);
        });
    }

    private function fail(): never
    {
        throw ValidationException::withMessages(['translationKeyValue' => __('The key assignments changed. Reopen the inspection before archiving the key record.')]);
    }
}
