<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Persistence for resolved single-key review actions.
 * Callers still enforce authorization, action-specific eligibility and container/key validation.
 * Manual edits rekey existing values; accepting a suggestion retains its existing snapshot-only behavior.
 * Ambiguous source ownership and occupied target values require an explicit conflict decision.
 */
class TranslationWorkbenchKeyReviewWriter
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    /** @param array<string, mixed> $attributes */
    public function saveDecision(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        object $selectedFinding,
        array $attributes,
        string $reviewType,
        string $decision,
    ): bool {
        $trackedAttributes = array_values(array_unique([
            ...array_keys($attributes),
            'review_status',
            'reviewed_at',
            'reviewed_by_user_id',
        ]));

        $oldValues = $key->only($trackedAttributes);

        $attributes = [
            ...$attributes,
            'review_status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
        ];

        $changedValues = collect($attributes)
            ->filter(static fn (mixed $value, string $attribute): bool => ($oldValues[$attribute] ?? null) != $value)
            ->all();

        if ($changedValues === []) {
            return false;
        }

        DB::transaction(function () use ($key, $finding, $selectedFinding, $oldValues, $changedValues, $reviewType, $decision): void {
            if (array_key_exists('translation_key', $changedValues)) {
                if (DB::getDriverName() === 'pgsql') {
                    DB::statement('LOCK TABLE translation_workbench_keys, translation_workbench_lang_values IN SHARE ROW EXCLUSIVE MODE');
                }
                $current = TranslationWorkbenchKey::query()->findOrFail($key->id);
                if ($current->translation_key !== ($oldValues['translation_key'] ?? null)) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['translationKeyValue' => __('The key changed while editing. Refresh the editor before renaming it.')]);
                }
                $oldKey = $this->nullableString($oldValues['translation_key'] ?? null);
                if ($oldKey !== null && count(TranslationWorkbenchKeyOwnership::ids($oldKey)) > 1) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['translationKeyValue' => __('Multiple active records share the source key. Resolve their assignments before moving its translations; nothing was changed.')]);
                }
            }
            $key->forceFill($changedValues)->save();

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'review_type' => $reviewType,
                'decision' => $decision,
                'old_values' => collect($oldValues)->only(array_keys($changedValues))->all(),
                'new_values' => $changedValues,
                'meta' => [
                    'source' => 'translation-workbench:review-modal',
                    'candidate_type' => $selectedFinding->candidate_type,
                    'candidate_reason' => $selectedFinding->candidate_reason,
                    'finding_kind' => $selectedFinding->kind,
                    'finding_entry_type' => $selectedFinding->entry_type,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: $decision,
                oldValues: collect($oldValues)->only(array_keys($changedValues))->all(),
                newValues: $changedValues,
                context: [
                    'source' => 'translation-workbench:review-modal',
                    'candidate_type' => $selectedFinding->candidate_type,
                    'candidate_reason' => $selectedFinding->candidate_reason,
                ],
            );

            if (array_key_exists('translation_key', $changedValues)) {
                $this->rekeyLangValuesForTranslationKey(
                    key: $key,
                    finding: $finding,
                    oldTranslationKey: $this->nullableString($oldValues['translation_key'] ?? null),
                    newTranslationKey: $this->nullableString($changedValues['translation_key'] ?? null),
                );
            }
        });

        return true;
    }

    public function acceptSuggestedKey(TranslationWorkbenchKey $key, TranslationWorkbenchFinding $finding, string $suggestedKey): bool
    {
        $suggestedKey = trim($suggestedKey);
        if ($suggestedKey === '' || (trim((string) $key->translation_key) === $suggestedKey && $key->review_status === 'reviewed')) {
            return false;
        }

        $attributes = [
            ...app(TranslationWorkbenchKeyAttributes::class)->forKey($suggestedKey, $key),
            'review_status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
        ];
        $trackedAttributes = array_values(array_unique([
            ...array_keys($attributes),
        ]));

        DB::transaction(function () use ($key, $finding, $suggestedKey, $attributes, $trackedAttributes): void {
            $oldValues = $key->only([
                ...$trackedAttributes,
                'review_status',
                'reviewed_at',
                'reviewed_by_user_id',
            ]);

            $key->forceFill($attributes)->save();

            $newValues = $key->only([
                ...$trackedAttributes,
                'review_status',
                'reviewed_at',
                'reviewed_by_user_id',
            ]);

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'review_type' => 'translation_key',
                'decision' => 'suggested_key_accepted',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:review-modal',
                    'suggested_key' => $suggestedKey,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'suggested_key_accepted',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:review-modal',
                ],
            );
        });

        return true;
    }

    private function rekeyLangValuesForTranslationKey(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
        ?string $oldTranslationKey,
        ?string $newTranslationKey,
    ): void {
        if (
            ! Schema::hasTable('translation_workbench_lang_values') ||
            $oldTranslationKey === null ||
            $newTranslationKey === null ||
            $oldTranslationKey === $newTranslationKey
        ) {
            return;
        }

        $newParts = app(TranslationWorkbenchTranslationWriter::class)->keyParts($newTranslationKey);
        $rows = TranslationWorkbenchLangValue::query()
            ->where('translation_key', $oldTranslationKey)
            ->where('status', '<>', 'obsolete')
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $now = now();
        $migrated = [];
        $obsoleted = [];

        foreach ($rows as $row) {
            $target = TranslationWorkbenchLangValue::query()
                ->where('locale', $row->locale)
                ->where('namespace', $newParts['namespace'])
                ->where('lang_key', $newParts['lang_key'])
                ->first();

            $oldRowValues = $row->only(['id', 'locale', 'namespace', 'lang_key', 'translation_key', 'status']);

            if ($target && $target->id !== $row->id) {
                throw \Illuminate\Validation\ValidationException::withMessages(['translationKeyValue' => __('The target key already has a value for :locale. Review the conflict explicitly; no values were moved or marked obsolete.', ['locale' => $row->locale])]);
            }

            $row->forceFill([
                'namespace' => $newParts['namespace'],
                'lang_key' => $newParts['lang_key'],
                'translation_key' => $newTranslationKey,
                'source_path' => 'lang/'.$row->locale.'/'.$newParts['namespace'].'.php',
                'last_seen_at' => $now,
                'meta' => [
                    ...(is_array($row->meta) ? $row->meta : []),
                    'source' => 'translation-workbench:translation-key-review',
                    'previous_translation_key' => $oldTranslationKey,
                    'previous_namespace' => $oldRowValues['namespace'] ?? null,
                    'previous_lang_key' => $oldRowValues['lang_key'] ?? null,
                ],
            ])->save();

            $migrated[] = [
                'id' => $row->id,
                'locale' => $row->locale,
                'old_namespace' => $oldRowValues['namespace'] ?? null,
                'old_lang_key' => $oldRowValues['lang_key'] ?? null,
                'new_namespace' => $newParts['namespace'],
                'new_lang_key' => $newParts['lang_key'],
            ];
        }

        $this->timelineRecorder->recordKeyFindingEvent(
            key: $key,
            finding: $finding,
            eventType: 'translation_lang_values_rekeyed',
            oldValues: [
                'translation_key' => $oldTranslationKey,
            ],
            newValues: [
                'translation_key' => $newTranslationKey,
                'migrated' => $migrated,
                'obsoleted' => $obsoleted,
            ],
            context: [
                'source' => 'translation-workbench:translation-key-review',
                'migrated_count' => count($migrated),
                'obsoleted_count' => count($obsoleted),
            ],
        );
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
