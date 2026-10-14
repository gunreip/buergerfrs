<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Persists resolved dynamic-source links and their history atomically.
 * Callers resolve eligible sources. Writes require the current Admin or Super-Admin role.
 * Option snapshots belong to the link;
 * translations and original source values are not changed. Unlinking retains inactive links.
 */
class TranslationWorkbenchDynamicSourceLinkWriter
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    /**
     * @param  array<int, array<string, mixed>>  $runtimeSources
     * @param  array<string, mixed>  $relatedSource
     */
    public function confirm(object $selectedFinding, array $runtimeSources, array $relatedSource): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        if ($runtimeSources === [] || empty($relatedSource['id'])) {
            throw new \InvalidArgumentException('Runtime sources and a related dynamic source are required.');
        }

        $runtimeSources = collect($runtimeSources);
        $key = $selectedFinding->key_id
            ? TranslationWorkbenchKey::query()->find($selectedFinding->key_id)
            : null;
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);

        $createdOrUpdated = [];
        $oldValues = [];

        DB::transaction(function () use ($runtimeSources, $relatedSource, $selectedFinding, $key, $finding, &$createdOrUpdated, &$oldValues): void {
            foreach ($runtimeSources as $runtimeSource) {
                $candidateReference = 'dynamic_source:'.$relatedSource['id'];
                $candidateValues = collect($relatedSource['values'] ?? [])
                    ->mapWithKeys(static fn (array $value): array => [
                        $value['value_key'] => $value['native_label'],
                    ])
                    ->all();
                $attributes = [
                    'key_id' => $runtimeSource['key_id'] ?? $selectedFinding->key_id,
                    'finding_id' => $relatedSource['finding_id'] ?? $selectedFinding->id,
                    'suggested_key' => $relatedSource['suggested_key'] ?? $selectedFinding->suggested_key,
                    'dynamic_scope' => $relatedSource['dynamic_scope'] ?? $selectedFinding->dynamic_scope,
                    'source_expression' => $relatedSource['source_expression'] ?? null,
                    'candidate_source_type' => 'related_dynamic_source',
                    'candidate_reference' => $candidateReference,
                    'candidate_values_count' => (int) ($relatedSource['values_count'] ?? 0),
                    'candidate_values' => json_encode($candidateValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'confidence' => 'manual',
                    'review_status' => 'confirmed',
                    'status' => 'active',
                    'meta' => json_encode([
                        'source' => 'translation-workbench:dynamic-review-modal',
                        'runtime_source_id' => $runtimeSource['id'],
                        'runtime_suggested_key' => $runtimeSource['suggested_key'] ?? null,
                        'related_source_id' => $relatedSource['id'],
                        'related_suggested_key' => $relatedSource['suggested_key'] ?? null,
                        'review_finding_id' => $selectedFinding->id,
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ];

                $existing = DB::table('translation_workbench_dynamic_source_candidates')
                    ->where('dynamic_source_id', $runtimeSource['id'])
                    ->where('candidate_source_type', 'related_dynamic_source')
                    ->where('candidate_reference', $candidateReference)
                    ->first();

                if (! $existing) {
                    $candidateId = DB::table('translation_workbench_dynamic_source_candidates')->insertGetId([
                        'dynamic_source_id' => $runtimeSource['id'],
                        ...$attributes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $createdOrUpdated[] = [
                        'id' => $candidateId,
                        'runtime_source_id' => $runtimeSource['id'],
                        'related_source_id' => $relatedSource['id'],
                        'review_status' => 'confirmed',
                    ];

                    continue;
                }

                $oldValues[] = [
                    'id' => $existing->id,
                    'review_status' => $existing->review_status,
                    'status' => $existing->status,
                    'candidate_reference' => $existing->candidate_reference,
                ];

                DB::table('translation_workbench_dynamic_source_candidates')
                    ->where('id', $existing->id)
                    ->update([
                        ...$attributes,
                        'updated_at' => now(),
                    ]);

                $createdOrUpdated[] = [
                    'id' => (int) $existing->id,
                    'runtime_source_id' => $runtimeSource['id'],
                    'related_source_id' => $relatedSource['id'],
                    'review_status' => 'confirmed',
                ];
            }

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'dynamic_source_link',
                'decision' => 'runtime_options_link_confirmed',
                'old_values' => $oldValues,
                'new_values' => $createdOrUpdated,
                'meta' => [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'dynamic_source_link_confirmed',
                oldValues: $oldValues,
                newValues: $createdOrUpdated,
                context: [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
            );
        });

    }

    /**
     * @param  array<int, array<string, mixed>>  $runtimeSources
     * @param  array<string, mixed>  $relatedSource
     */
    public function unlink(object $selectedFinding, array $runtimeSources, array $relatedSource): bool
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        if ($runtimeSources === [] || empty($relatedSource['id'])) {
            throw new \InvalidArgumentException('Runtime sources and a related dynamic source are required.');
        }

        $runtimeSources = collect($runtimeSources);
        $key = $selectedFinding->key_id
            ? TranslationWorkbenchKey::query()->find($selectedFinding->key_id)
            : null;
        $finding = TranslationWorkbenchFinding::query()->find($selectedFinding->id);
        $candidateReference = 'dynamic_source:'.$relatedSource['id'];
        $runtimeSourceIds = $runtimeSources
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
        $oldValues = [];
        $newValues = [];

        DB::transaction(function () use ($runtimeSourceIds, $candidateReference, $relatedSource, $runtimeSources, $key, $finding, &$oldValues, &$newValues): void {
            $candidates = DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('dynamic_source_id', $runtimeSourceIds)
                ->where('candidate_source_type', 'related_dynamic_source')
                ->where('candidate_reference', $candidateReference)
                ->where('status', 'active')
                ->get();

            if ($candidates->isEmpty()) {
                return;
            }

            $oldValues = $candidates
                ->map(static fn (object $candidate): array => [
                    'id' => (int) $candidate->id,
                    'runtime_source_id' => (int) $candidate->dynamic_source_id,
                    'related_source_id' => (int) $relatedSource['id'],
                    'review_status' => (string) $candidate->review_status,
                    'status' => (string) $candidate->status,
                ])
                ->all();

            DB::table('translation_workbench_dynamic_source_candidates')
                ->whereIn('id', $candidates->pluck('id')->all())
                ->update([
                    'review_status' => 'unlinked',
                    'status' => 'inactive',
                    'updated_at' => now(),
                ]);

            $newValues = $candidates
                ->map(static fn (object $candidate): array => [
                    'id' => (int) $candidate->id,
                    'runtime_source_id' => (int) $candidate->dynamic_source_id,
                    'related_source_id' => (int) $relatedSource['id'],
                    'review_status' => 'unlinked',
                    'status' => 'inactive',
                ])
                ->all();

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'dynamic_source_link',
                'decision' => 'runtime_options_link_unlinked',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'dynamic_source_link_unlinked',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:dynamic-review-modal',
                    'runtime_source_count' => $runtimeSources->count(),
                    'related_source_id' => $relatedSource['id'],
                ],
            );
        });

        return $newValues !== [];
    }
}
