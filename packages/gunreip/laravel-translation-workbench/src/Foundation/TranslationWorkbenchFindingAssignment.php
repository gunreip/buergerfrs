<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Explicit finding-level choice; never merges keys or moves their values. */
class TranslationWorkbenchFindingAssignment
{
    private function snapshot(int $id): array
    {
        $finding = TranslationWorkbenchFinding::findOrFail($id);
        $links = TranslationWorkbenchKeyFinding::where('finding_id', $id)->where('status', 'active')->orderBy('id')->get();
        $keys = TranslationWorkbenchKey::whereIn('id', $links->pluck('key_id'))->orderBy('id')->get();
        $file = DB::table('translation_workbench_source_files')->find($finding->source_file_id);
        $path = $file ? base_path($file->path) : null;
        $reviews = TranslationWorkbenchReview::whereIn('key_id', $keys->pluck('id'))
            ->where(fn ($query) => $query->where('finding_id', $id)->orWhereJsonContains('meta->selected_finding_ids', $id))
            ->orderBy('id')->get();
        return ['finding' => $finding->getAttributes(), 'file' => $file ? (array) $file : null,
            'file_hash' => $path && is_file($path) ? hash_file('sha256', $path) : null,
            'links' => $links->map->getAttributes()->all(), 'keys' => $keys->map->getAttributes()->all(),
            'reviews' => $reviews->map->getAttributes()->all()];
    }

    public function preview(int $id): ?array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $snapshot = $this->snapshot($id);
        $keys = collect($snapshot['keys']);
        if (($snapshot['file']['status'] ?? null) !== 'active' || ! $snapshot['file_hash']
            || $snapshot['finding']['status'] !== 'active' || $snapshot['finding']['kind'] !== 'key'
            || $keys->count() < 2 || $keys->contains(fn ($key) => $key['status'] === 'obsolete' || $key['is_dynamic_key'])
            || $keys->pluck('translation_key')->unique()->all() !== [$snapshot['finding']['found_translation_key']]) {
            return null;
        }
        $literal = (string) ($snapshot['finding']['literal_text'] ?? '');
        $locale = \App\Support\Locale\LocaleCode::normalize((string) config('translation-workbench.source_locale', 'en')) ?: 'en';
        $text = $literal;
        $textSource = trim($literal) !== '' ? 'literal' : 'missing';
        if ($textSource === 'missing') {
            $values = DB::table('translation_workbench_lang_values')
                ->where('translation_key', $snapshot['finding']['found_translation_key'])
                ->where('locale', $locale)->where('status', 'active')->where('value_type', 'string')
                ->pluck('value')->filter(fn ($value) => is_string($value) && trim($value) !== '')->unique()->values();
            if ($values->count() === 1) {
                $text = $values->first();
                $textSource = 'source_translation';
            } elseif ($values->count() > 1) {
                $textSource = 'conflict';
            }
        }
        return ['finding_id' => $id, 'literal_text' => $literal,
            'display_text' => $text, 'text_source' => $textSource, 'source_locale' => $locale, 'keys' => $keys->map(fn ($key) => ['id' => $key['id'], 'suggested_key' => $key['suggested_key'],
            'review_evidence' => $this->reviewEvidence($snapshot['reviews'], $key['id'], $id)])->all(),
            'proof' => Crypt::encryptString(json_encode($snapshot, JSON_THROW_ON_ERROR))];
    }

    public function savedDecision(int $id): ?array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $finding = TranslationWorkbenchFinding::findOrFail($id);
        $decision = $finding->meta['explicit_key_assignment'] ?? null;
        if (! $decision) { return null; }
        $review = TranslationWorkbenchReview::where('id', $decision['review_id'])
            ->where('key_id', $decision['key_id'])->where('finding_id', $id)
            ->where('decision', 'finding_assignment_selected')->first();
        $key = TranslationWorkbenchKey::find($decision['key_id']);
        $links = TranslationWorkbenchKeyFinding::where('finding_id', $id)->where('status', 'active')->pluck('key_id')->all();
        $pending = $finding->meta['pending_key_separation'] ?? null;
        $keyMatches = $key?->translation_key === $decision['translation_key']
            || ($pending && $pending['key_id'] === $decision['key_id']
                && $pending['source_key'] === $decision['translation_key'] && $key?->translation_key === $pending['target_key']);
        return ['finding_id' => $id, 'key_id' => $decision['key_id'], 'review_id' => $decision['review_id'],
            'reviewed_at' => $review?->reviewed_at?->format('Y-m-d H:i:s'),
            'active' => $review !== null && $finding->status === 'active' && $key !== null && $key->status !== 'obsolete'
                && $links === [$key->id] && $keyMatches
                && $finding->expression_fingerprint === $decision['expression_fingerprint']];
    }

    /** Collapse repeated rows of one bulk operation, retaining all review references. */
    private function reviewEvidence(array $reviews, int $keyId, int $findingId): array
    {
        $evidence = [];
        foreach ($reviews as $row) {
            if ($row['key_id'] !== $keyId) { continue; }
            $meta = json_decode($row['meta'] ?? '{}', true, 512, JSON_THROW_ON_ERROR) ?? [];
            $values = json_decode($row['new_values'] ?? '{}', true, 512, JSON_THROW_ON_ERROR) ?? [];
            $bulk = $row['decision'] === 'translation_key_bulk_equalized';
            $group = $bulk && ! empty($meta['operation_id'])
                ? 'bulk:'.$meta['operation_id'].':'.json_encode([$values['translation_key'] ?? null, $row['reviewed_at']], JSON_THROW_ON_ERROR)
                : 'review:'.$row['id'];
            $evidence[$group] ??= ['review_ids' => [], 'direct' => false, 'bulk' => $bulk,
                'decision' => $row['decision'], 'translation_key' => $values['translation_key'] ?? null,
                'reviewed_at' => $row['reviewed_at']];
            $evidence[$group]['review_ids'][] = $row['id'];
            $evidence[$group]['direct'] = $evidence[$group]['direct'] || $row['finding_id'] === $findingId;
        }
        return array_values($evidence);
    }

    public function choose(int $id, int $keyId, string $proof): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        try { $expected = json_decode(Crypt::decryptString($proof), true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable) { $this->fail(); }
        DB::transaction(function () use ($id, $keyId, $expected) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE');
            }
            $current = $this->snapshot($id);
            if ($current !== $expected || ! $this->preview($id) || ! in_array($keyId, array_column($current['keys'], 'id'), true)) { $this->fail(); }
            $finding = TranslationWorkbenchFinding::findOrFail($id);
            $key = TranslationWorkbenchKey::findOrFail($keyId);
            $decision = ['key_id' => $keyId, 'expression_fingerprint' => $finding->expression_fingerprint, 'translation_key' => $key->translation_key];
            $review = TranslationWorkbenchReview::create(['key_id' => $keyId, 'finding_id' => $id,
                'review_type' => 'finding_assignment', 'decision' => 'finding_assignment_selected',
                'old_values' => ['key_ids' => array_column($current['keys'], 'id')], 'new_values' => $decision,
                'meta' => ['source' => 'workbench-finding-assignment'], 'reviewed_by_user_id' => Auth::id(), 'reviewed_at' => now()]);
            $recorder = app(TranslationWorkbenchTimelineRecorder::class);
            foreach (TranslationWorkbenchKeyFinding::where('finding_id', $id)->where('status', 'active')->where('key_id', '!=', $keyId)->get() as $link) {
                $old = $link->getAttributes();
                $link->forceFill(['status' => 'obsolete', 'meta' => [...($link->meta ?? []), 'obsolete_reason' => 'explicit_finding_assignment', 'assignment_review_id' => $review->id]])->save();
                $recorder->record('key_assignment_reconciled', key: TranslationWorkbenchKey::findOrFail($link->key_id), finding: $finding, review: $review,
                    oldValues: $old, newValues: $link->getAttributes(), context: ['source' => 'workbench-finding-assignment']);
            }
            $finding->forceFill(['meta' => [...($finding->meta ?? []), 'explicit_key_assignment' => [...$decision, 'review_id' => $review->id]]])->save();
            $recorder->recordReviewEvent($review, 'finding_assignment_selected', $review->old_values, $decision);
            if (hash_file('sha256', base_path($current['file']['path'])) !== $current['file_hash']) { $this->fail(); }
        });
    }

    public function assignedKey(TranslationWorkbenchFinding $finding, DiscoveredTranslation $item): ?TranslationWorkbenchKey
    {
        $decision = $finding->meta['explicit_key_assignment'] ?? null;
        if (! $decision) { return null; }
        $key = TranslationWorkbenchKey::find($decision['key_id']);
        $links = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all();
        if (! $key || $key->status === 'obsolete' || $links !== [$key->id]
            || $item->expressionFingerprint !== $decision['expression_fingerprint']
            || $item->translationKey !== $decision['translation_key'] || $key->translation_key !== $decision['translation_key']) {
            throw new \InvalidArgumentException('The explicit finding assignment changed; review it before scanning again.');
        }
        return $key;
    }

    private function fail(): never
    {
        throw ValidationException::withMessages(['translationKeyValue' => __('The assignment changed. Reopen the inspection before choosing a key.')]);
    }
}
