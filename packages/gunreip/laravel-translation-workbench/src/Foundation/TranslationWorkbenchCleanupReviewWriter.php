<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyInventory;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/** Stores cleanup reviews and status changes atomically; never publishes language files. */
class TranslationWorkbenchCleanupReviewWriter
{
    public const DECISIONS = ['keep', 'needs_review', 'obsolete'];

    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    public function save(TranslationWorkbenchKeyInventory $inventory, string $sourceLocale, string $decision): int
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        $decision = trim($decision);
        if (! in_array($decision, self::DECISIONS, true)) {
            throw new \InvalidArgumentException('Unsupported language cleanup decision.');
        }

        $translationKey = trim((string) $inventory->translation_key);
        $key = TranslationWorkbenchKey::query()
            ->where('translation_key', $translationKey)
            ->orWhere('suggested_key', $translationKey)
            ->first();

        $langValuesBefore = TranslationWorkbenchLangValue::query()
            ->where('translation_key', $translationKey)
            ->orderBy('locale')
            ->orderBy('id')
            ->get()
            ->map(fn (TranslationWorkbenchLangValue $langValue): array => $langValue->only([
                'id',
                'locale',
                'namespace',
                'lang_key',
                'translation_key',
                'value',
                'status',
            ]))
            ->all();

        $obsoleteCount = 0;

        DB::transaction(function () use ($decision, $inventory, $key, $translationKey, $sourceLocale, $langValuesBefore, &$obsoleteCount): void {
            if ($decision === 'obsolete') {
                $obsoleteCount = TranslationWorkbenchLangValue::query()
                    ->where('translation_key', $translationKey)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'obsolete',
                        'last_seen_at' => now(),
                        'updated_at' => now(),
                    ]);

                $this->refreshLangCleanupInventoryCounts($inventory, $sourceLocale);
            }

            $langValuesAfter = TranslationWorkbenchLangValue::query()
                ->where('translation_key', $translationKey)
                ->orderBy('locale')
                ->orderBy('id')
                ->get()
                ->map(fn (TranslationWorkbenchLangValue $langValue): array => $langValue->only([
                    'id',
                    'locale',
                    'namespace',
                    'lang_key',
                    'translation_key',
                    'value',
                    'status',
                ]))
                ->all();

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => null,
                'review_type' => 'lang_cleanup',
                'decision' => $decision,
                'old_values' => [
                    'inventory' => $inventory->only([
                        'id',
                        'translation_key',
                        'namespace',
                        'group',
                        'candidate_for_lang_delete',
                        'finding_active_count',
                        'finding_commented_out_count',
                        'finding_obsolete_count',
                        'relation_active_count',
                    ]),
                    'lang_values' => $langValuesBefore,
                ],
                'new_values' => [
                    'decision' => $decision,
                    'obsolete_lang_values_count' => $obsoleteCount,
                    'inventory' => $inventory->fresh()?->only([
                        'id',
                        'translation_key',
                        'source_value_active_count',
                        'source_value_obsolete_count',
                        'target_value_active_count',
                        'target_value_obsolete_count',
                        'candidate_for_lang_delete',
                    ]),
                    'lang_values' => $langValuesAfter,
                ],
                'meta' => [
                    'source' => 'translation-workbench:lang-cleanup',
                    'inventory_id' => $inventory->id,
                    'translation_key' => $translationKey,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: match ($decision) {
                    'keep' => 'lang_value_kept',
                    'obsolete' => 'lang_value_marked_obsolete',
                    default => 'lang_cleanup_needs_review',
                },
                oldValues: $review->old_values,
                newValues: $review->new_values,
                context: $review->meta,
            );
        });

        return $obsoleteCount;
    }

    /**
     * Marks only the selected source locale obsolete; target locales and files remain untouched.
     * Returns null when missing, false when already obsolete, true when changed.
     */
    public function markSourceObsolete(string $translationKey, string $sourceLocale): ?bool
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        $translationKey = trim($translationKey);
        if ($translationKey === '') {
            return null;
        }

        $langValue = TranslationWorkbenchLangValue::query()
            ->where('locale', $sourceLocale)
            ->where('translation_key', $translationKey)
            ->first();

        if (! $langValue) {
            return null;
        }

        if ($langValue->status === 'obsolete') {
            return false;
        }

        $key = TranslationWorkbenchKey::query()
            ->where('translation_key', $translationKey)
            ->orWhere('suggested_key', $translationKey)
            ->first();

        DB::transaction(function () use ($langValue, $key, $translationKey, $sourceLocale): void {
            $oldValues = $langValue->only([
                'id',
                'locale',
                'namespace',
                'lang_key',
                'translation_key',
                'value',
                'status',
            ]);

            $langValue->forceFill([
                'status' => 'obsolete',
                'last_seen_at' => now(),
            ])->save();

            $newValues = $langValue->only([
                'id',
                'locale',
                'namespace',
                'lang_key',
                'translation_key',
                'value',
                'status',
            ]);

            $review = TranslationWorkbenchReview::query()->create([
                'key_id' => $key?->id,
                'finding_id' => null,
                'review_type' => 'lang_value_obsolete',
                'decision' => 'source_value_marked_obsolete',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => [
                    'source' => 'translation-workbench:export-report',
                    'locale' => $sourceLocale,
                    'translation_key' => $translationKey,
                    'lang_value_id' => $langValue->id,
                ],
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $this->timelineRecorder->recordReviewEvent(
                review: $review,
                eventType: 'source_lang_value_marked_obsolete',
                oldValues: $oldValues,
                newValues: $newValues,
                context: [
                    'source' => 'translation-workbench:export-report',
                    'locale' => $sourceLocale,
                    'translation_key' => $translationKey,
                    'lang_value_id' => $langValue->id,
                ],
            );
        });

        return true;
    }

    private function refreshLangCleanupInventoryCounts(TranslationWorkbenchKeyInventory $inventory, string $sourceLocale): void
    {
        $row = DB::table('translation_workbench_lang_values')
            ->where('translation_key', (string) $inventory->translation_key)
            ->selectRaw("SUM(CASE WHEN locale = ? AND status = 'active' THEN 1 ELSE 0 END) as source_active", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale = ? AND status = 'obsolete' THEN 1 ELSE 0 END) as source_obsolete", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale = ? AND status = 'deleted' THEN 1 ELSE 0 END) as source_deleted", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale <> ? AND status = 'active' THEN 1 ELSE 0 END) as target_active", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale <> ? AND status = 'obsolete' THEN 1 ELSE 0 END) as target_obsolete", [$sourceLocale])
            ->selectRaw("SUM(CASE WHEN locale <> ? AND status = 'deleted' THEN 1 ELSE 0 END) as target_deleted", [$sourceLocale])
            ->selectRaw('COUNT(DISTINCT locale) as locale_count')
            ->first();

        $sourceActive = (int) ($row->source_active ?? 0);
        $sourceObsolete = (int) ($row->source_obsolete ?? 0);
        $sourceDeleted = (int) ($row->source_deleted ?? 0);
        $targetActive = (int) ($row->target_active ?? 0);
        $targetObsolete = (int) ($row->target_obsolete ?? 0);
        $targetDeleted = (int) ($row->target_deleted ?? 0);
        $langValueCount = $sourceActive + $sourceObsolete + $sourceDeleted + $targetActive + $targetObsolete + $targetDeleted;

        $inventory->forceFill([
            'source_value_active_count' => $sourceActive,
            'source_value_obsolete_count' => $sourceObsolete,
            'source_value_deleted_count' => $sourceDeleted,
            'target_value_active_count' => $targetActive,
            'target_value_obsolete_count' => $targetObsolete,
            'target_value_deleted_count' => $targetDeleted,
            'lang_file_locale_count' => (int) ($row->locale_count ?? 0),
            'has_lang_values' => $langValueCount > 0,
            'candidate_for_lang_delete' => (bool) $inventory->is_orphaned_lang_value && ($sourceActive > 0 || $targetActive > 0),
        ])->save();
    }
}
