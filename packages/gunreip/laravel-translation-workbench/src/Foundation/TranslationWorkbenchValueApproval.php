<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/** Explicit approval of a stored value snapshot; does not publish or migrate existing reviews. */
class TranslationWorkbenchValueApproval
{
    public function preview(string $kind, int $valueId, int $keyId, int $findingId): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        [$key, $finding] = app(TranslationWorkbenchEditorAccess::class)->assignment($keyId, $findingId);
        $invalid = static function (): never {
            throw ValidationException::withMessages(['valueApproval' => __('The stored value needs an active, reviewed and unambiguous owner before approval.')]);
        };
        if ($key->status === 'obsolete' || $key->review_status !== 'reviewed' || $finding->status !== 'active'
            || DB::table('translation_workbench_source_files')->where('id', $finding->source_file_id)->value('status') !== 'active'
            || app(TranslationWorkbenchDynamicKeyAssignment::class)->conflicts([$findingId])->isNotEmpty()) {
            $invalid();
        }
        $table = match ($kind) {
            'scalar' => 'translation_workbench_lang_values',
            'option' => 'translation_workbench_dynamic_key_values',
            default => throw new \InvalidArgumentException('Unknown translation value kind.'),
        };
        if (! Schema::hasColumn($table, 'value_revision')) {
            throw ValidationException::withMessages(['valueApproval' => __('Value approval requires the value revision migration.')]);
        }
        $value = DB::table($table)->where('id', $valueId)->first();
        if (! $value || $value->status !== 'active' || ! is_string($value->value) || trim($value->value) === '') {
            $invalid();
        }
        if ($kind === 'scalar') {
            $parts = app(TranslationWorkbenchTranslationWriter::class)->keyParts((string) $key->translation_key);
            if ($value->translation_key !== $key->translation_key || $value->namespace !== $parts['namespace'] || $value->lang_key !== $parts['lang_key']) {
                $invalid();
            }
        } elseif ((int) $value->key_id !== $keyId) {
            $invalid();
        }
        $snapshot = [
            'kind' => $kind, 'value_id' => $valueId, 'key_id' => $keyId, 'finding_id' => $findingId,
            'translation_key' => $key->translation_key, 'locale' => $value->locale,
            'value_key' => $kind === 'option' ? $value->value_key : null,
            'value_revision' => (int) $value->value_revision,
            'value' => $value->value, 'value_type' => $value->value_type ?? 'string',
            'native_label' => $value->native_label ?? null, 'status' => $value->status,
        ];

        return ['snapshot' => $snapshot, 'fingerprint' => hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR))];
    }

    /** Inspect stored values only. Historical reviewed_at never implies revision approval. */
    public function inspect(string $kind, int $keyId, int $findingId, array $locales): array
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        [$key] = app(TranslationWorkbenchEditorAccess::class)->assignment($keyId, $findingId);
        $table = match ($kind) {
            'scalar' => 'translation_workbench_lang_values',
            'option' => 'translation_workbench_dynamic_key_values',
            default => throw new \InvalidArgumentException('Unknown translation value kind.'),
        };
        if (! Schema::hasColumn($table, 'value_revision')) {
            throw ValidationException::withMessages(['valueApproval' => __('Value approval requires the value revision migration.')]);
        }
        $query = DB::table($table)->whereIn('locale', $locales)->where('status', 'active')
            ->whereNotNull('value')->whereRaw("TRIM(value) <> ''");
        $kind === 'scalar'
            ? $query->where('translation_key', $key->translation_key)
            : $query->where('key_id', $keyId);

        return $query->orderBy('locale')->orderBy('id')->get()->map(function ($value) use ($kind, $keyId, $findingId): array {
            $preview = $this->preview($kind, (int) $value->id, $keyId, $findingId);
            $review = $this->latestReview($kind, (int) $value->id, $keyId, $findingId);

            return $preview + [
                'state' => $review === null ? 'unapproved' : (($review->meta['fingerprint'] ?? null) === $preview['fingerprint'] ? 'approved' : 'changed'),
                'legacy_reviewed_at' => $value->reviewed_at ?? null,
            ];
        })->all();
    }

    public function approve(string $kind, int $valueId, int $keyId, int $findingId, string $expectedFingerprint): TranslationWorkbenchReview
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        return DB::transaction(function () use ($kind, $valueId, $keyId, $findingId, $expectedFingerprint): TranslationWorkbenchReview {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('LOCK TABLE translation_workbench_source_files, translation_workbench_findings, translation_workbench_keys, translation_workbench_key_findings, translation_workbench_lang_values, translation_workbench_dynamic_key_values, translation_workbench_reviews IN SHARE ROW EXCLUSIVE MODE');
            }
            $preview = $this->preview($kind, $valueId, $keyId, $findingId);
            if (! hash_equals($expectedFingerprint, $preview['fingerprint'])) {
                throw ValidationException::withMessages(['valueApproval' => __('The translation changed after the approval preview. Review the current value before approving.')]);
            }
            $previous = $this->latestReview($kind, $valueId, $keyId, $findingId);
            if (($previous?->meta['fingerprint'] ?? null) === $preview['fingerprint']) {
                return $previous;
            }
            $review = TranslationWorkbenchReview::create([
                'key_id' => $keyId, 'finding_id' => $findingId, 'review_type' => 'translation_value',
                'decision' => 'translation_value_approved', 'old_values' => $previous?->new_values,
                'new_values' => $preview['snapshot'],
                'meta' => ['kind' => $kind, 'value_id' => $valueId, 'fingerprint' => $preview['fingerprint']],
                'reviewed_at' => now(), 'reviewed_by_user_id' => Auth::id(),
            ]);
            app(TranslationWorkbenchTimelineRecorder::class)->recordReviewEvent(
                $review, 'translation_value_approved', $review->old_values, $review->new_values,
                ['source' => 'translation-workbench:value-approval', 'kind' => $kind, 'value_id' => $valueId],
            );

            return $review;
        }, 3);
    }

    public function isApproved(string $kind, int $valueId, int $keyId, int $findingId): bool
    {
        $preview = $this->preview($kind, $valueId, $keyId, $findingId);

        return ($this->latestReview($kind, $valueId, $keyId, $findingId)?->meta['fingerprint'] ?? null) === $preview['fingerprint'];
    }

    private function latestReview(string $kind, int $valueId, int $keyId, int $findingId): ?TranslationWorkbenchReview
    {
        return TranslationWorkbenchReview::where('review_type', 'translation_value')->where('decision', 'translation_value_approved')
            ->where('key_id', $keyId)->where('finding_id', $findingId)
            ->where('meta->kind', $kind)->where('meta->value_id', $valueId)->latest('id')->first();
    }
}
