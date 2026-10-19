<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Explicit ownership decision for one scalar key text. Shared values and source files are retained. */
class TranslationWorkbenchKeyConsolidation
{
    private function snapshot(string $text): array
    {
        $keys = TranslationWorkbenchKey::where('translation_key', $text)->orderBy('id')->get();
        $links = DB::table('translation_workbench_key_findings')->whereIn('key_id', $keys->pluck('id'))->orderBy('id')->get();
        $findings = DB::table('translation_workbench_findings')->whereIn('id', $links->pluck('finding_id'))->orderBy('id')->get();
        $files = DB::table('translation_workbench_source_files')->whereIn('id', $findings->pluck('source_file_id'))->orderBy('id')->get();
        $hashes = [];
        foreach ($files as $file) {
            $hashes[$file->path] = is_file(base_path($file->path)) ? hash_file('sha256', base_path($file->path)) : null;
        }
        $result = ['text' => $text, 'keys' => $keys->map->getAttributes()->all(), 'findings' => $findings, 'files' => $files, 'hashes' => $hashes,
            // Include outside assignments as well: never silently absorb another task.
            'links' => DB::table('translation_workbench_key_findings')->whereIn('finding_id', $findings->pluck('id'))->orderBy('id')->get(),
            'shared_values' => DB::table('translation_workbench_lang_values')->where('translation_key', $text)->orderBy('id')->get()];
        foreach (['reviews', 'key_values', 'dynamic_key_values'] as $table) {
            $result[$table] = DB::table('translation_workbench_'.$table)->whereIn('key_id', $keys->pluck('id'))->orderBy('id')->get();
        }
        return json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    public function preview(string $text): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $snapshot = $this->snapshot($text);
        $owners = collect($snapshot['keys'])->where('status', '!=', 'obsolete')->values();
        $ids = $owners->pluck('id')->all();
        $links = collect($snapshot['links'])->whereIn('key_id', $ids)->where('status', 'active');
        $findings = collect($snapshot['findings'])->whereIn('id', $links->pluck('finding_id'))->values();
        $issues = [];
        if (trim($text) === '' || count($ids) < 2 || $owners->contains(fn ($key) => (bool) $key['is_dynamic_key'])) {
            $issues[] = __('Choose a translation key shared by at least two active scalar key records.');
        }
        if (collect($snapshot['key_values'])->whereIn('key_id', $ids)->isNotEmpty()
            || collect($snapshot['dynamic_key_values'])->whereIn('key_id', $ids)->isNotEmpty()) {
            $issues[] = __('These records have their own stored values. Resolve their value ownership before merging; no values will be overwritten.');
        }
        $scanned = [];
        foreach ($findings as $finding) {
            $meta = json_decode($finding['meta'] ?? '{}', true) ?? [];
            $file = collect($snapshot['files'])->firstWhere('id', $finding['source_file_id']);
            $outside = collect($snapshot['links'])->where('finding_id', $finding['id'])->where('status', 'active')->whereNotIn('key_id', $ids)->isNotEmpty();
            if ($outside || $finding['status'] !== 'active' || $finding['kind'] !== 'key'
                || $finding['found_translation_key'] !== $text || isset($meta['pending_key_separation']) || isset($meta['code_update_assignment'])
                || ! $file || $file['status'] !== 'active' || ! $snapshot['hashes'][$file['path']]) {
                $issues[] = __('Finding #:id needs source or assignment review first.', ['id' => $finding['id']]);
                continue;
            }
            $scanned[$file['path']] ??= app(TranslationWorkbenchScanner::class)->scanFile(base_path($file['path']));
            if ($scanned[$file['path']]->where('sourceSignature', $finding['source_signature'])->count() !== 1) {
                $issues[] = __('Finding #:id no longer matches the current source. Scan and review it first.', ['id' => $finding['id']]);
            }
        }
        $literals = $findings->pluck('literal_text');
        return ['text' => $text, 'owners' => $owners->all(), 'findings' => $findings->map(function ($finding) use ($snapshot, $links) {
            return [...$finding, 'path' => collect($snapshot['files'])->firstWhere('id', $finding['source_file_id'])['path'] ?? '',
                'key_ids' => $links->where('finding_id', $finding['id'])->pluck('key_id')->unique()->values()->all()];
        })->all(), 'values' => $snapshot['shared_values'], 'reviews' => $snapshot['reviews'],
            'own_values' => $snapshot['key_values'],
            'source_locale' => \App\Support\Locale\LocaleCode::normalize((string) config('translation-workbench.source_locale', 'en')) ?: 'en',
            'same_literals' => $owners->every(fn ($key) => $links->contains('key_id', $key['id'])) && $literals->count() > 1 && $literals->every(fn ($text) => is_string($text) && trim($text) !== '') && $literals->uniqueStrict()->count() === 1,
            'issues' => array_values(array_unique($issues)),
            'proof' => $issues === [] ? Crypt::encryptString(json_encode($snapshot, JSON_THROW_ON_ERROR)) : null];
    }

    public function merge(int $targetId, string $proof): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        try { $expected = json_decode(Crypt::decryptString($proof), true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable) { $this->fail(); }
        DB::transaction(function () use ($targetId, $expected) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE');
            }
            $current = $this->snapshot($expected['text']);
            $plan = $this->preview($expected['text']);
            if ($expected !== $current || ! $plan['proof'] || ! in_array($targetId, array_column($plan['owners'], 'id'), true)) { $this->fail(); }
            $target = TranslationWorkbenchKey::findOrFail($targetId);
            $retired = array_values(array_diff(array_column($plan['owners'], 'id'), [$targetId]));
            $review = TranslationWorkbenchReview::create(['key_id' => $targetId, 'review_type' => 'key_consolidation',
                'decision' => 'key_ownership_consolidated', 'old_values' => ['owner_ids' => array_column($plan['owners'], 'id')],
                'new_values' => ['canonical_key_id' => $targetId, 'archived_key_ids' => $retired],
                'meta' => ['source' => 'workbench-key-consolidation', 'finding_ids' => array_column($plan['findings'], 'id')],
                'reviewed_by_user_id' => Auth::id(), 'reviewed_at' => now()]);
            $recorder = app(TranslationWorkbenchTimelineRecorder::class);
            foreach ($plan['findings'] as $row) {
                $finding = TranslationWorkbenchFinding::findOrFail($row['id']);
                foreach (TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('status', 'active')->whereIn('key_id', $retired)->get() as $link) {
                    $old = $link->getAttributes();
                    $link->forceFill(['status' => 'obsolete', 'meta' => [...($link->meta ?? []), 'obsolete_reason' => 'explicit_key_consolidation', 'consolidation_review_id' => $review->id]])->save();
                    $recorder->record('key_assignment_reconciled', key: TranslationWorkbenchKey::findOrFail($link->key_id), finding: $finding, review: $review, oldValues: $old, newValues: $link->getAttributes());
                }
                $active = TranslationWorkbenchKeyFinding::where('finding_id', $finding->id)->where('key_id', $targetId)->where('status', 'active')->get();
                if ($active->count() > 1) { $this->fail(); }
                if ($active->isEmpty()) {
                    // A link may be reassigned by an explicit decision; the finding itself is never reactivated.
                    $link = TranslationWorkbenchKeyFinding::firstOrNew(['key_id' => $targetId, 'finding_id' => $finding->id, 'relation_type' => 'candidate']);
                    $old = $link->exists ? $link->getAttributes() : [];
                    $link->forceFill(['status' => 'active', 'meta' => [...($link->meta ?? []), 'consolidation_review_id' => $review->id]])->save();
                    $recorder->record('key_assignment_reconciled', key: $target, finding: $finding, review: $review, oldValues: $old, newValues: $link->getAttributes());
                }
                $decision = ['key_id' => $targetId, 'expression_fingerprint' => $finding->expression_fingerprint, 'translation_key' => $target->translation_key];
                $assignmentReview = TranslationWorkbenchReview::create(['key_id' => $targetId, 'finding_id' => $finding->id,
                    'review_type' => 'finding_assignment', 'decision' => 'finding_assignment_selected',
                    'old_values' => ['key_ids' => $row['key_ids']], 'new_values' => $decision,
                    'meta' => ['source' => 'workbench-key-consolidation', 'consolidation_review_id' => $review->id],
                    'reviewed_by_user_id' => Auth::id(), 'reviewed_at' => now()]);
                $old = $finding->getAttributes();
                $finding->forceFill(['meta' => [...($finding->meta ?? []), 'explicit_key_assignment' => [
                    'key_id' => $targetId, 'review_id' => $assignmentReview->id, 'expression_fingerprint' => $finding->expression_fingerprint, 'translation_key' => $target->translation_key]]])->save();
                $recorder->record('key_assignment_reconciled', key: $target, finding: $finding, review: $review, oldValues: $old, newValues: $finding->getAttributes());
            }
            foreach ($current['keys'] as $row) {
                $meta = json_decode($row['meta'] ?? '{}', true) ?? [];
                if (! in_array($row['id'], $retired, true) && ! in_array($meta['reconciled_canonical_key_id'] ?? null, $retired, true)) { continue; }
                $key = TranslationWorkbenchKey::findOrFail($row['id']);
                $old = $key->getAttributes();
                $key->forceFill(['status' => 'obsolete', 'meta' => [...($key->meta ?? []), 'reconciled_canonical_key_id' => $targetId, 'consolidation_review_id' => $review->id]])->save();
                $recorder->record('key_ownership_consolidated', key: $key, review: $review, oldValues: $old, newValues: $key->getAttributes());
            }
            $recorder->recordReviewEvent($review, 'key_ownership_consolidated', $review->old_values, $review->new_values);
            foreach ($current['hashes'] as $path => $hash) {
                $actual = is_file(base_path($path)) ? hash_file('sha256', base_path($path)) : null;
                if ($actual !== $hash) { $this->fail(); }
            }
        });
    }

    private function fail(): never
    {
        throw ValidationException::withMessages(['ownershipTargetKeyId' => __('The conflict or source changed. Reopen the conflict dialog and review the current preview.')]);
    }
}
