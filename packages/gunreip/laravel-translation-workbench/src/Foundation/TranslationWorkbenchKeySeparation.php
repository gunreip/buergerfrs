<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Separate one existing task, retaining shared values at the old key. No file writes. */
class TranslationWorkbenchKeySeparation
{
    private function snapshot(int $id, string $target): array
    {
        $key = TranslationWorkbenchKey::findOrFail($id);
        $links = DB::table('translation_workbench_key_findings')->where('key_id', $id)->orderBy('id')->get();
        $findings = DB::table('translation_workbench_findings')->whereIn('id', $links->where('status', 'active')->pluck('finding_id'))->orderBy('id')->get();
        $files = DB::table('translation_workbench_source_files')->whereIn('id', $findings->pluck('source_file_id'))->orderBy('id')->get();
        $hashes = [];
        foreach ($files as $file) { $hashes[$file->path] = is_file(base_path($file->path)) ? hash_file('sha256', base_path($file->path)) : null; }
        $result = ['key' => $key->getAttributes(), 'target' => $target, 'findings' => $findings, 'files' => $files, 'hashes' => $hashes,
            'links' => DB::table('translation_workbench_key_findings')->whereIn('finding_id', $findings->pluck('id'))->orderBy('id')->get(),
            'owners' => DB::table('translation_workbench_keys')->whereIn('translation_key', [$key->translation_key, $target])->orderBy('id')->get(),
            'shared_values' => DB::table('translation_workbench_lang_values')->whereIn('translation_key', [$key->translation_key, $target])->orderBy('id')->get()];
        foreach (['key_values', 'dynamic_key_values', 'reviews'] as $table) {
            $result[$table] = DB::table('translation_workbench_'.$table)->where('key_id', $id)->orderBy('id')->get();
        }
        $result['published_target_files'] = [];
        $result['published_target_occupied'] = false;
        if (preg_match('/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*){2,}$/', $target)) {
            [$group, $leaf] = explode('.', $target, 2);
            foreach (glob(lang_path('*/'.$group.'.php')) ?: [] as $path) {
                $result['published_target_files'][$path] = hash_file('sha256', $path);
                $value = (static fn ($file) => require $file)($path);
                foreach (explode('.', $leaf) as $segment) {
                    if (! is_array($value)) { $result['published_target_occupied'] = true; continue 2; }
                    if (! array_key_exists($segment, $value)) { continue 2; }
                    $value = $value[$segment];
                }
                $result['published_target_occupied'] = true;
            }
        }
        return json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    public function preview(int $id, int $findingId, string $target): ?array
    {
        $plan = app(TranslationWorkbenchKeyConflictInspector::class)->separation($id, $findingId, $target);
        if (! $plan) { return null; }
        $snapshot = $this->snapshot($id, trim($target));
        if ($snapshot['published_target_occupied']) {
            $plan['issues'][] = __('The target or a parent path already exists in a language file. Choose a free key.');
        }
        if ($snapshot['key_values'] || $snapshot['dynamic_key_values']) {
            $plan['issues'][] = __('This key has its own stored values. Review them before separating the task.');
        }
        foreach ($snapshot['findings'] as $finding) {
            $ids = collect($snapshot['links'])->where('finding_id', $finding['id'])->where('status', 'active')->pluck('key_id')->all();
            $file = collect($snapshot['files'])->firstWhere('id', $finding['source_file_id']);
            $meta = json_decode($finding['meta'] ?? '{}', true) ?? [];
            if ($ids !== [$id] || ! in_array($finding['kind'], ['key', 'literal'], true) || $finding['status'] !== 'active'
                || ! $file || $file['status'] !== 'active' || ! $snapshot['hashes'][$file['path']]
                || isset($meta['pending_key_separation']) || isset($meta['code_update_assignment'])) {
                $plan['issues'][] = __('Finding #:id needs assignment or transition review first.', ['id' => $finding['id']]);
                continue;
            }
            $current = app(TranslationWorkbenchScanner::class)->scanFile(base_path($file['path']));
            if ($current->where('sourceSignature', $finding['source_signature'])->count() !== 1) {
                $plan['issues'][] = __('Finding #:id no longer matches the current source. Scan and review it first.', ['id' => $finding['id']]);
            }
        }
        // No target ownership, even archived, is silently reused. Parent leaf values also block.
        $occupied = collect($snapshot['owners'])->contains('translation_key', trim($target));
        $segments = explode('.', trim($target));
        array_pop($segments);
        $parents = [];
        while (count($segments) > 1) { $parents[] = implode('.', $segments); array_pop($segments); }
        if ($occupied || TranslationWorkbenchKeyOwnership::activeKeys()->whereIn('ownership_keys.translation_key', $parents)->exists()
            || DB::table('translation_workbench_lang_values')->whereIn('translation_key', $parents)->where('status', 'active')->exists()) {
            $plan['issues'][] = __('The target or a parent path is already occupied. Choose a free key.');
        }
        $plan['proof'] = $plan['issues'] === [] ? Crypt::encryptString(json_encode($snapshot, JSON_THROW_ON_ERROR)) : null;
        return $plan;
    }

    public function separate(int $id, int $findingId, string $target, string $proof): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        try { $expected = json_decode(Crypt::decryptString($proof), true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable) { $this->fail(); }
        DB::transaction(function () use ($id, $findingId, $target, $expected) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values, translation_workbench_key_values, translation_workbench_dynamic_key_values, translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE');
            }
            $current = $this->snapshot($id, trim($target));
            $plan = $this->preview($id, $findingId, $target);
            if ($current !== $expected || ! $plan || ! $plan['proof']) { $this->fail(); }
            $key = TranslationWorkbenchKey::findOrFail($id);
            $old = $key->getAttributes();
            $review = TranslationWorkbenchReview::create(['key_id' => $id, 'finding_id' => $findingId,
                'review_type' => 'key_separation', 'decision' => 'translation_task_separated',
                'old_values' => ['translation_key' => $key->translation_key],
                'new_values' => ['translation_key' => trim($target), 'value_policy' => 'retain_shared_values_without_copy'],
                'meta' => ['source' => 'workbench-key-separation', 'finding_ids' => array_column($current['findings'], 'id')],
                'reviewed_by_user_id' => Auth::id(), 'reviewed_at' => now()]);
            $key->forceFill([...app(TranslationWorkbenchKeyAttributes::class)->forKey(trim($target), $key),
                'review_status' => 'reviewed', 'reviewed_at' => now(), 'reviewed_by_user_id' => Auth::id()])->save();
            foreach ($current['findings'] as $row) {
                $finding = TranslationWorkbenchFinding::findOrFail($row['id']);
                $finding->forceFill(['meta' => [...($finding->meta ?? []), 'pending_key_separation' => [
                    'key_id' => $id, 'review_id' => $review->id, 'source_key' => $finding->found_translation_key,
                    'target_key' => trim($target), 'expression_fingerprint' => $finding->expression_fingerprint,
                ]]])->save();
            }
            app(TranslationWorkbenchTimelineRecorder::class)->record('translation_task_separated', key: $key, review: $review,
                oldValues: $old, newValues: $key->getAttributes(), context: ['source' => 'workbench-key-separation', 'value_policy' => 'retain_shared_values_without_copy']);
            foreach ($current['hashes'] as $path => $hash) {
                if (! is_file(base_path($path)) || hash_file('sha256', base_path($path)) !== $hash) { $this->fail(); }
            }
            if ($this->snapshot($id, trim($target))['published_target_files'] !== $current['published_target_files']) { $this->fail(); }
        });
    }

    public function assignedKey(TranslationWorkbenchFinding $finding, object $item): ?TranslationWorkbenchKey
    {
        $pending = $finding->meta['pending_key_separation'] ?? null;
        if (! $pending) { return null; }
        $key = TranslationWorkbenchKey::find($pending['key_id']);
        $ids = DB::table('translation_workbench_key_findings')->where('finding_id', $finding->id)->where('status', 'active')->pluck('key_id')->all();
        if (! $key || $key->status === 'obsolete' || $ids !== [$key->id] || $key->translation_key !== $pending['target_key']
            || $item->translationKey !== $pending['source_key'] || $item->expressionFingerprint !== $pending['expression_fingerprint']) { $this->fail(); }
        return $key;
    }

    private function fail(): never
    {
        throw ValidationException::withMessages(['translationKeyValue' => __('The separation no longer matches its reviewed source or assignments. Reopen the inspection and review it again.')]);
    }
}
