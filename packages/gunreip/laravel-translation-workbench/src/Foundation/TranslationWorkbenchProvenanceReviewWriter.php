<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Records provenance decisions against a resolved report row.
 * Does not restore literals, reassign keys or publish files. Callers resolve report context; writes require the current Admin or Super-Admin role.
 */
class TranslationWorkbenchProvenanceReviewWriter
{
    public const DECISIONS = [
        'mark_as_valid_existing_key',
        'needs_literal_restore',
        'needs_key_review',
        'ignore_for_now',
    ];

    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    /** @param array<string, mixed> $row */
    public function save(array $row, ?TranslationWorkbenchFinding $finding, ?TranslationWorkbenchKey $key, string $decision): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        if (! in_array($decision, self::DECISIONS, true)) {
            throw new \InvalidArgumentException('Unsupported provenance review decision.');
        }

        $oldValues = [
            'state' => $row['state'] ?? null,
            'translation_key' => $row['translation_key'] ?? null,
            'source_lang_value_exists' => $row['source_lang_value_exists'] ?? false,
            'active_usage_count' => $row['active_usage_count'] ?? 0,
            'reviewed_usage_count' => $row['reviewed_usage_count'] ?? 0,
        ];
        $newValues = [
            'decision' => $decision,
            'reviewed_at' => now()->toISOString(),
            'reviewed_by_user_id' => Auth::id(),
        ];

        DB::transaction(function () use ($finding, $key, $row, $decision, $oldValues, $newValues): void {
            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => $finding?->id,
                'review_type' => 'suspicious_key_provenance',
                'decision' => $decision,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:suspicious-keys',
                    'source_signature' => $row['source_signature'] ?? null,
                    'source_path' => $row['source_path'] ?? null,
                    'source_line' => $row['source_line'] ?? null,
                    'translation_key' => $row['translation_key'] ?? null,
                    'suggested_key' => $row['suggested_key'] ?? null,
                    'raw_expression' => $row['raw_expression'] ?? null,
                    'literal_text_suggested' => $row['literal_text_suggested'] ?? null,
                    'source_lang_value' => $row['source_lang_value'] ?? null,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'suspicious_key_reviewed',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:suspicious-keys',
                    'decision' => $decision,
                    'source_signature' => $row['source_signature'] ?? null,
                    'translation_key' => $row['translation_key'] ?? null,
                ],
            );
        });

    }
}
