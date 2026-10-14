<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Stores conflict decisions only; applying source changes remains a separate operation. */
class TranslationWorkbenchConflictReviewWriter
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    public function save(TranslationWorkbenchFinding $finding, TranslationWorkbenchKey $key, string $decision, string $note): bool
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        $decision = $this->normalizedCodeUpdateConflictDecision($decision);
        $oldReview = $this->latestCodeUpdateConflictReview($finding->id, $key->id);
        $oldValues = [
            'decision' => $oldReview?->decision,
            'note' => $oldReview?->meta['note'] ?? null,
        ];
        $newValues = [
            'decision' => $decision,
            'note' => trim($note),
        ];

        if ($oldValues == $newValues) {
            return false;
        }

        DB::transaction(function () use ($finding, $key, $decision, $oldValues, $newValues): void {
            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'review_type' => 'code_update_conflict',
                'decision' => $decision,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:code-update-plan',
                    'note' => $newValues['note'],
                    'finding_id' => $finding->id,
                    'key_id' => $key->id,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'code_update_conflict_reviewed',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:code-update-plan',
                    'decision' => $decision,
                ],
            );
        });

        return true;
    }

    public function latestCodeUpdateConflictReview(int $findingId, int $keyId): ?TranslationWorkbenchReview
    {
        if (! Schema::hasTable('translation_workbench_reviews')) {
            return null;
        }

        return TranslationWorkbenchReview::query()
            ->where('review_type', 'code_update_conflict')
            ->where('finding_id', $findingId)
            ->where('key_id', $keyId)
            ->latest('id')
            ->first();
    }

    public function normalizedCodeUpdateConflictDecision(string $decision): string
    {
        return in_array($decision, [
            'duplicate_confirmed_same_key',
            'existing_key_should_be_replaced',
            'duplicate_dynamic_manual_workflow',
            'ignore_for_now',
        ], true)
            ? $decision
            : 'ignore_for_now';
    }
}
