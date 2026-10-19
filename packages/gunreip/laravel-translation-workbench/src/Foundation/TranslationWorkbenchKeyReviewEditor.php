<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Authorized boundary for resolved single-key reviews.
 * Classification attributes remain constructed by trusted application code, not request payloads.
 * This is not yet a complete action-specific validation or revision protocol.
 */
class TranslationWorkbenchKeyReviewEditor
{
    public function __construct(
        private readonly TranslationWorkbenchEditorAccess $access,
        private readonly TranslationWorkbenchKeyReviewWriter $writer,
    ) {}

    public function saveDecision(int $keyId, int $findingId, array $attributes, string $reviewType, string $decision): bool
    {
        $this->access->authorize();

        return DB::transaction(function () use ($keyId, $findingId, $attributes, $reviewType, $decision): bool {
            [$key, $finding] = $this->access->assignment($keyId, $findingId);
            $decisions = match ($reviewType) {
                'translation_key' => ['translation_key_updated'],
                'classification' => ['ui_key_confirmed', 'ui_key_unconfirmed', 'ui_candidate_rejected', 'ui_candidate_rejection_removed', 'dynamic_key_confirmed', 'dynamic_key_unconfirmed', 'dynamic_candidate_rejected', 'dynamic_candidate_rejection_removed'],
                'candidate_classification' => ['dynamic_multi_confirmed', 'dynamic_multi_unconfirmed'],
                'dynamic_classification' => ['dynamic_multi_confirmed', 'dynamic_single_confirmed'],
                default => [],
            };
            if (! in_array($decision, $decisions, true)) {
                throw ValidationException::withMessages(['review' => __('This review decision is not supported for the selected action.')]);
            }
            if ($reviewType === 'translation_key') {
                $translationKey = $attributes['translation_key'] ?? null;
                Validator::make(['translationKeyValue' => $translationKey], [
                    'translationKeyValue' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*)+$/'],
                ])->validate();
                if (app(TranslationWorkbenchLangNodeClassifier::class)->reviewCandidate($translationKey, $keyId)['is_blocked']) {
                    throw ValidationException::withMessages(['translationKeyValue' => __('A container path cannot be saved as a scalar translation key.')]);
                }
                $attributes = app(TranslationWorkbenchKeyAttributes::class)->forKey($translationKey, $key);
            }

            // Use current finding metadata, never a caller-supplied review snapshot.
            return $this->writer->saveDecision($key, $finding, $finding, $attributes, $reviewType, $decision);
        });
    }

    public function acceptSuggestedKey(int $keyId, int $findingId): bool
    {
        $this->access->authorize();

        return DB::transaction(function () use ($keyId, $findingId): bool {
            [$key, $finding] = $this->access->assignment($keyId, $findingId);
            $suggestedKey = trim((string) $finding->suggested_key);

            // Preserve the existing suggestion acceptance policy, distinct from manual renaming.
            return $this->writer->acceptSuggestedKey($key, $finding, $suggestedKey);
        });
    }
}
