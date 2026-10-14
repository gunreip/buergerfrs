<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Moves resolved scalar candidates to nested key paths; never rewrites source or language files.
 * Callers resolve candidate eligibility. Writes require the current Admin or Super-Admin role.
 * Context and execution share target rules.
 */
class TranslationWorkbenchScalarTransformWriter
{
    public function __construct(private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder) {}

    /** @param array<int, array<string, mixed>> $rows Included candidate rows from the current context. */
    public function transform(array $rows): int
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();

        $changed = 0;
        $timelineRecorder = $this->timelineRecorder;

        DB::transaction(function () use ($rows, $timelineRecorder, &$changed): void {
            $targets = [];
            foreach ($rows as $row) {
                $target = $row['target_translation_key'] ?? null;
                if (! $this->isValidTranslationKey($target)
                    || isset($targets[$target])
                    || $this->scalarTransformTargetConflicts((string) ($row['translation_key'] ?? ''), $target) !== []) {
                    throw ValidationException::withMessages(['scalarTransformTargetPath' => __('Resolve invalid target keys, duplicate targets or existing lang-value conflicts for included rows before transforming scalar keys.')]);
                }
                $targets[$target] = true;
            }

            foreach ($rows as $row) {
                $oldTranslationKey = $this->nullableString($row['translation_key'] ?? null);
                $newTranslationKey = $this->nullableString($row['target_translation_key'] ?? null);

                if ($oldTranslationKey === null || $newTranslationKey === null || $oldTranslationKey === $newTranslationKey) {
                    continue;
                }

                $keys = TranslationWorkbenchKey::query()
                    ->where('translation_key', $oldTranslationKey)
                    ->get();

                foreach ($keys as $key) {
                    $oldValues = $key->only(['translation_key', 'namespace', 'group', 'path_key', 'scope', 'key_segment_domain', 'key_segment_section', 'key_segment_context', 'key_segment_extra', 'key_segment_name', 'is_ui_key', 'key_type', 'review_status']);
                    $attributes = [
                        ...app(TranslationWorkbenchKeyAttributes::class)->forKey($newTranslationKey, $key),
                        'review_status' => 'reviewed',
                        'reviewed_at' => now(),
                        'reviewed_by_user_id' => Auth::id(),
                    ];

                    $key->forceFill($attributes)->save();

                    $newValues = $key->only(array_keys($oldValues));
                    $review = TranslationWorkbenchReview::query()->create([
                        'key_id' => $key->id,
                        'finding_id' => null,
                        'review_type' => 'scalar_key_transform',
                        'decision' => 'scalar_key_transformed_to_array',
                        'old_values' => $oldValues,
                        'new_values' => $newValues,
                        'meta' => [
                            'source' => 'translation-workbench:scalar-review',
                            'lang_value_id' => $row['id'],
                        ],
                        'reviewed_by_user_id' => Auth::id(),
                        'reviewed_at' => now(),
                    ]);

                    $timelineRecorder->recordReviewEvent(
                        review: $review,
                        eventType: 'scalar_key_transformed_to_array',
                        oldValues: $oldValues,
                        newValues: $newValues,
                        context: [
                            'source' => 'translation-workbench:scalar-review',
                            'lang_value_id' => $row['id'],
                        ],
                    );
                }

                $langValueChanges = $this->rekeyScalarLangValues($oldTranslationKey, $newTranslationKey);

                $timelineRecorder->record(
                    eventType: 'scalar_lang_values_rekeyed',
                    oldValues: [
                        'translation_key' => $oldTranslationKey,
                    ],
                    newValues: [
                        'translation_key' => $newTranslationKey,
                        ...$langValueChanges,
                    ],
                    context: [
                        'source' => 'translation-workbench:scalar-review',
                        'lang_value_id' => $row['id'],
                    ],
                );

                $changed++;
            }
        });

        return $changed;
    }

    public function context(array $candidateRows, array $includedLangValueIds, string $targetPath): array
    {
        $targetPath = $this->normalizedScalarTransformTargetPath($targetPath);
        $includedIds = collect($includedLangValueIds)
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $rows = collect($candidateRows)
            ->map(function (array $row) use ($targetPath, $includedIds): array {
                $targetKey = $this->scalarTransformTargetKeyForRow($row, $targetPath);
                $row['is_included'] = $includedIds->contains((int) ($row['id'] ?? 0));
                $row['target_translation_key'] = $targetKey;
                $row['target_is_valid'] = $this->isValidTranslationKey($targetKey);
                $row['target_conflicts'] = $this->scalarTransformTargetConflicts((string) $row['translation_key'], $targetKey);

                return $row;
            });

        $includedRows = $rows->where('is_included', true)->values();

        $targetCounts = $includedRows
            ->pluck('target_translation_key')
            ->filter()
            ->countBy();

        $rows = $rows
            ->map(function (array $row) use ($targetCounts): array {
                $row['target_is_duplicate'] = (bool) ($row['is_included'] ?? false)
                    && (int) ($targetCounts[$row['target_translation_key']] ?? 0) > 1;

                return $row;
            })
            ->values();

        $includedRows = $rows->where('is_included', true)->values();

        return [
            'rows' => $rows->all(),
            'target_path' => $targetPath,
            'selected_count' => $rows->count(),
            'included_count' => $includedRows->count(),
            'invalid_count' => $includedRows->where('target_is_valid', false)->count(),
            'duplicate_target_count' => $includedRows->where('target_is_duplicate', true)->count(),
            'conflict_count' => $includedRows->filter(static fn (array $row): bool => count($row['target_conflicts'] ?? []) > 0)->count(),
        ];
    }

    private function normalizedScalarTransformTargetPath(string $targetPath): ?string
    {
        $targetPath = $this->nullableString($targetPath);

        if ($targetPath === null) {
            return null;
        }

        return collect(explode('.', $targetPath))
            ->map(static fn (string $segment): string => Str::of($segment)->trim()->lower()->replace(' ', '_')->toString())
            ->filter(static fn (string $segment): bool => $segment !== '')
            ->implode('.');
    }

    private function scalarTransformTargetKeyForRow(array $row, ?string $targetPath): string
    {
        $leaf = $this->nullableString($row['leaf'] ?? null)
            ?? str((string) ($row['source_value'] ?? ''))->slug('_')->toString();

        return collect([$targetPath, $leaf])
            ->filter(static fn (?string $segment): bool => filled($segment))
            ->implode('.');
    }

    private function isValidTranslationKey(?string $translationKey): bool
    {
        return $translationKey !== null
            && preg_match('/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*)+$/', $translationKey) === 1
            && substr_count($translationKey, '.') >= 2;
    }

    private function scalarTransformTargetConflicts(string $oldTranslationKey, ?string $newTranslationKey): array
    {
        if ($newTranslationKey === null || ! Schema::hasTable('translation_workbench_lang_values')) {
            return [];
        }

        return TranslationWorkbenchLangValue::query()
            ->where('translation_key', $newTranslationKey)
            ->where('translation_key', '<>', $oldTranslationKey)
            ->orderBy('locale')
            ->limit(10)
            ->get(['id', 'locale', 'status', 'value'])
            ->map(static fn (TranslationWorkbenchLangValue $value): array => [
                'id' => (int) $value->id,
                'locale' => (string) $value->locale,
                'status' => (string) $value->status,
                'value' => $value->value,
            ])
            ->all();
    }

    private function rekeyScalarLangValues(string $oldTranslationKey, string $newTranslationKey): array
    {
        if (! Schema::hasTable('translation_workbench_lang_values') || $oldTranslationKey === $newTranslationKey) {
            return [
                'migrated' => [],
                'obsoleted' => [],
                'migrated_count' => 0,
                'obsoleted_count' => 0,
            ];
        }

        $newParts = app(TranslationWorkbenchTranslationWriter::class)->keyParts($newTranslationKey);
        $rows = TranslationWorkbenchLangValue::query()
            ->where('translation_key', $oldTranslationKey)
            ->where('status', '<>', 'obsolete')
            ->orderBy('id')
            ->get();

        $migrated = [];
        $obsoleted = [];
        $now = now();

        foreach ($rows as $row) {
            $target = TranslationWorkbenchLangValue::query()
                ->where('locale', $row->locale)
                ->where('namespace', $newParts['namespace'])
                ->where('lang_key', $newParts['lang_key'])
                ->first();

            $oldValues = $row->only(['id', 'locale', 'namespace', 'lang_key', 'translation_key', 'source_path', 'status']);

            if ($target && $target->id !== $row->id) {
                $row->forceFill([
                    'status' => 'obsolete',
                    'last_seen_at' => $now,
                    'meta' => [
                        ...(is_array($row->meta) ? $row->meta : []),
                        'obsolete_reason' => 'scalar_transform_target_exists',
                        'rekeyed_to_translation_key' => $newTranslationKey,
                        'rekeyed_to_lang_value_id' => $target->id,
                        'previous_translation_key' => $oldTranslationKey,
                        'previous_namespace' => $oldValues['namespace'] ?? null,
                        'previous_lang_key' => $oldValues['lang_key'] ?? null,
                    ],
                ])->save();

                $obsoleted[] = [
                    'id' => (int) $row->id,
                    'locale' => (string) $row->locale,
                    'target_id' => (int) $target->id,
                    'target_status' => (string) $target->status,
                ];

                continue;
            }

            $row->forceFill([
                'namespace' => $newParts['namespace'],
                'lang_key' => $newParts['lang_key'],
                'translation_key' => $newTranslationKey,
                'source_path' => 'lang/'.$row->locale.'/'.$newParts['namespace'].'.php',
                'last_seen_at' => $now,
                'meta' => [
                    ...(is_array($row->meta) ? $row->meta : []),
                    'source' => 'translation-workbench:scalar-review',
                    'previous_translation_key' => $oldTranslationKey,
                    'previous_namespace' => $oldValues['namespace'] ?? null,
                    'previous_lang_key' => $oldValues['lang_key'] ?? null,
                ],
            ])->save();

            $migrated[] = [
                'id' => (int) $row->id,
                'locale' => (string) $row->locale,
                'old_namespace' => $oldValues['namespace'] ?? null,
                'old_lang_key' => $oldValues['lang_key'] ?? null,
                'new_namespace' => $newParts['namespace'],
                'new_lang_key' => $newParts['lang_key'],
            ];
        }

        return [
            'migrated' => $migrated,
            'obsoleted' => $obsoleted,
            'migrated_count' => count($migrated),
            'obsoleted_count' => count($obsoleted),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
