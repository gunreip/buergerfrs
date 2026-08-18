<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineChain;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TranslationWorkbenchTimelineChainCollector
{
    /**
     * @return array{summary: array<string, mixed>, rows: Collection<int, array<string, mixed>>}
     */
    public function collect(bool $sync = false): array
    {
        $now = now();
        $rows = $this->chainRows($now);
        $summary = [
            'chain_rows' => $rows->count(),
            'single_rows' => $rows->where('chain_type', 'single')->count(),
            'shared_rows' => $rows->where('chain_type', 'shared')->count(),
            'bulk_rows' => $rows->where('chain_type', 'bulk')->count(),
            'moved_rows' => $rows->where('chain_type', 'moved')->count(),
            'stale_marked' => 0,
            'synced' => $sync,
        ];

        if ($sync) {
            DB::transaction(function () use ($rows, $now, &$summary): void {
                $seen = $rows->pluck('chain_key')->all();

                foreach ($rows as $row) {
                    $existing = TranslationWorkbenchTimelineChain::query()
                        ->where('chain_key', $row['chain_key'])
                        ->first();

                    if ($existing) {
                        $existing->forceFill([
                            ...$row,
                            'first_seen_at' => $existing->first_seen_at ?? $row['first_seen_at'],
                            'scan_count' => ((int) $existing->scan_count) + 1,
                        ])->save();

                        continue;
                    }

                    TranslationWorkbenchTimelineChain::query()->create($row);
                }

                $summary['stale_marked'] = TranslationWorkbenchTimelineChain::query()
                    ->where('chain_status', 'active')
                    ->whereNotIn('chain_key', $seen)
                    ->update([
                        'chain_status' => 'stale',
                        'last_seen_at' => $now,
                        'updated_at' => $now,
                    ]);
            });
        }

        return [
            'summary' => $summary,
            'rows' => $rows,
        ];
    }

    public function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_timeline_chains',
            'translation_workbench_keys',
            'translation_workbench_key_findings',
            'translation_workbench_findings',
            'translation_workbench_reviews',
            'translation_workbench_timeline_events',
            'translation_workbench_lang_values',
            'translation_workbench_shared_key_candidates',
        ])->every(static fn(string $table): bool => Schema::hasTable($table));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function chainRows(mixed $now): Collection
    {
        $translationKeys = $this->translationKeys();
        $keys = $this->keysByTranslationKey();
        $relations = $this->relationsByTranslationKey();
        $reviews = $this->reviewsByTranslationKey();
        $timelineEvents = $this->timelineEventsByTranslationKey();
        $langValues = $this->langValuesByTranslationKey();
        $sharedCandidates = $this->sharedCandidatesByTranslationKey();
        $movedRelations = $this->movedRelationsByTranslationKey();

        return $translationKeys
            ->map(function (string $translationKey) use (
                $now,
                $keys,
                $relations,
                $reviews,
                $timelineEvents,
                $langValues,
                $sharedCandidates,
                $movedRelations,
            ): array {
                $normalizedKey = $this->normalizeTranslationKey($translationKey);
                $keyRows = collect($keys->get($normalizedKey, []));
                $relationRows = collect($relations->get($normalizedKey, []));
                $reviewRows = collect($reviews->get($normalizedKey, []));
                $timelineRows = collect($timelineEvents->get($normalizedKey, []));
                $langValueRows = collect($langValues->get($normalizedKey, []));
                $sharedRows = collect($sharedCandidates->get($normalizedKey, []));
                $movedRows = collect($movedRelations->get($normalizedKey, []));
                $findingIds = $relationRows
                    ->pluck('finding_id')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();
                $reviewIds = $reviewRows
                    ->pluck('id')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();
                $timelineEventIds = $timelineRows
                    ->pluck('id')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();
                $langValueIds = $langValueRows
                    ->pluck('id')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();
                $relationStatusCounts = $relationRows
                    ->unique(static fn(array $row): string => implode(':', [
                        $row['key_id'] ?? '',
                        $row['finding_id'] ?? '',
                        $row['relation_type'] ?? '',
                        $row['relation_status'] ?? '',
                    ]))
                    ->countBy('relation_status')
                    ->all();
                $findingStatusCounts = $relationRows
                    ->unique(static fn(array $row): string => implode(':', [
                        $row['finding_id'] ?? '',
                        $row['finding_status'] ?? '',
                    ]))
                    ->countBy('finding_status')
                    ->all();
                $reviewDecisionCounts = $reviewRows
                    ->countBy('decision')
                    ->all();
                $timelineEventCounts = $timelineRows
                    ->countBy('event_type')
                    ->all();
                $langValueSummary = $langValueRows
                    ->groupBy('locale')
                    ->map(static fn(Collection $rows): array => $rows->countBy('status')->all())
                    ->all();
                $relatedKeys = $this->relatedTranslationKeys($translationKey, $sharedRows, $movedRows);
                $bulkReviewCount = (int) ($reviewDecisionCounts['translation_key_bulk_equalized'] ?? 0);
                $chainType = $this->chainType($sharedRows, $bulkReviewCount, $movedRows);
                $rootKey = $keyRows->sortBy('id')->first();
                $activeRelation = $relationRows
                    ->where('relation_status', 'active')
                    ->sortBy('finding_id')
                    ->first();

                return [
                    'chain_key' => $this->chainKey($translationKey),
                    'translation_key' => $translationKey,
                    'normalized_translation_key' => $normalizedKey,
                    'namespace' => (string) ($rootKey['namespace'] ?? Str::before($translationKey, '.')),
                    'group' => (string) ($rootKey['group'] ?? $this->groupFromTranslationKey($translationKey)),
                    'chain_type' => $chainType,
                    'chain_status' => $activeRelation ? 'active' : 'inactive',
                    'root_key_id' => isset($rootKey['id']) ? (int) $rootKey['id'] : null,
                    'root_finding_id' => isset($activeRelation['finding_id']) ? (int) $activeRelation['finding_id'] : null,
                    'key_count' => $keyRows->count(),
                    'finding_count' => $findingIds->count(),
                    'active_finding_count' => (int) ($findingStatusCounts['active'] ?? 0),
                    'obsolete_finding_count' => (int) ($findingStatusCounts['obsolete'] ?? 0),
                    'commented_out_finding_count' => (int) ($findingStatusCounts['commented_out'] ?? 0),
                    'review_count' => $reviewIds->count(),
                    'timeline_event_count' => $timelineEventIds->count(),
                    'lang_value_count' => $langValueIds->count(),
                    'shared_candidate_count' => $sharedRows->count(),
                    'bulk_review_count' => $bulkReviewCount,
                    'key_ids' => $keyRows->pluck('id')->filter()->unique()->sort()->values()->all(),
                    'finding_ids' => $findingIds->all(),
                    'review_ids' => $reviewIds->all(),
                    'timeline_event_ids' => $timelineEventIds->all(),
                    'lang_value_ids' => $langValueIds->all(),
                    'related_translation_keys' => $relatedKeys,
                    'relation_summary' => [
                        'relations' => $relationStatusCounts,
                        'findings' => $findingStatusCounts,
                    ],
                    'lang_value_summary' => $langValueSummary,
                    'timeline_event_summary' => $timelineEventCounts,
                    'meta' => [
                        'review_decisions' => $reviewDecisionCounts,
                        'shared_candidates' => $sharedRows->values()->all(),
                        'moved_relations' => $movedRows->values()->all(),
                    ],
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                    'scan_count' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function translationKeys(): Collection
    {
        return collect()
            ->merge(DB::table('translation_workbench_keys')->whereNotNull('translation_key')->pluck('translation_key'))
            ->merge(DB::table('translation_workbench_lang_values')->whereNotNull('translation_key')->pluck('translation_key'))
            ->merge(DB::table('translation_workbench_shared_key_candidates')->whereNotNull('current_translation_key')->pluck('current_translation_key'))
            ->merge(DB::table('translation_workbench_shared_key_candidates')->whereNotNull('suggested_shared_translation_key')->pluck('suggested_shared_translation_key'))
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter(static fn(string $key): bool => $key !== '')
            ->unique(static fn(string $key): string => mb_strtolower($key))
            ->sort()
            ->values();
    }

    private function keysByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_keys')
            ->whereNotNull('translation_key')
            ->get(['id', 'translation_key', 'suggested_key', 'namespace', 'group', 'status', 'review_status', 'is_ui_key', 'is_dynamic_key', 'is_dynamic_multi'])
            ->groupBy(fn(object $row): string => $this->normalizeTranslationKey((string) $row->translation_key))
            ->map(static fn(Collection $rows): array => $rows->map(static fn(object $row): array => (array) $row)->values()->all());
    }

    private function relationsByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_keys as keys')
            ->join('translation_workbench_key_findings as key_findings', 'key_findings.key_id', '=', 'keys.id')
            ->join('translation_workbench_findings as findings', 'findings.id', '=', 'key_findings.finding_id')
            ->whereNotNull('keys.translation_key')
            ->get([
                'keys.translation_key',
                'key_findings.key_id',
                'key_findings.finding_id',
                'key_findings.relation_type',
                'key_findings.status as relation_status',
                'findings.status as finding_status',
                'findings.source_signature',
                'findings.source_file_id',
                'findings.source_line',
            ])
            ->groupBy(fn(object $row): string => $this->normalizeTranslationKey((string) $row->translation_key))
            ->map(static fn(Collection $rows): array => $rows->map(static fn(object $row): array => (array) $row)->values()->all());
    }

    private function reviewsByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_reviews as reviews')
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'reviews.key_id')
            ->whereNotNull('keys.translation_key')
            ->get([
                'reviews.id',
                'reviews.key_id',
                'reviews.finding_id',
                'reviews.review_type',
                'reviews.decision',
                'reviews.reviewed_at',
                'keys.translation_key',
            ])
            ->groupBy(fn(object $row): string => $this->normalizeTranslationKey((string) $row->translation_key))
            ->map(static fn(Collection $rows): array => $rows->map(static fn(object $row): array => (array) $row)->values()->all());
    }

    private function timelineEventsByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_timeline_events as timeline_events')
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'timeline_events.key_id')
            ->whereNotNull('keys.translation_key')
            ->get([
                'timeline_events.id',
                'timeline_events.key_id',
                'timeline_events.finding_id',
                'timeline_events.review_id',
                'timeline_events.event_type',
                'timeline_events.created_at',
                'keys.translation_key',
            ])
            ->groupBy(fn(object $row): string => $this->normalizeTranslationKey((string) $row->translation_key))
            ->map(static fn(Collection $rows): array => $rows->map(static fn(object $row): array => (array) $row)->values()->all());
    }

    private function langValuesByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_lang_values')
            ->whereNotNull('translation_key')
            ->get(['id', 'translation_key', 'locale', 'namespace', 'lang_key', 'status'])
            ->groupBy(fn(object $row): string => $this->normalizeTranslationKey((string) $row->translation_key))
            ->map(static fn(Collection $rows): array => $rows->map(static fn(object $row): array => (array) $row)->values()->all());
    }

    private function sharedCandidatesByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_shared_key_candidates as candidates')
            ->leftJoin('translation_workbench_keys as keys', 'keys.id', '=', 'candidates.key_id')
            ->leftJoin('translation_workbench_keys as matched_keys', 'matched_keys.id', '=', 'candidates.matched_key_id')
            ->get([
                'candidates.id',
                'candidates.finding_id',
                'candidates.key_id',
                'candidates.matched_key_id',
                'candidates.current_translation_key',
                'candidates.suggested_shared_translation_key',
                'candidates.status',
                'keys.translation_key as key_translation_key',
                'matched_keys.translation_key as matched_translation_key',
            ])
            ->flatMap(function (object $row): array {
                $keys = collect([
                    $row->current_translation_key,
                    $row->suggested_shared_translation_key,
                    $row->key_translation_key,
                    $row->matched_translation_key,
                ])
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->filter(static fn(string $key): bool => $key !== '')
                    ->unique()
                    ->values();

                return $keys->map(fn(string $key): array => [
                    'map_key' => $this->normalizeTranslationKey($key),
                    'id' => (int) $row->id,
                    'finding_id' => $row->finding_id !== null ? (int) $row->finding_id : null,
                    'key_id' => $row->key_id !== null ? (int) $row->key_id : null,
                    'matched_key_id' => $row->matched_key_id !== null ? (int) $row->matched_key_id : null,
                    'current_translation_key' => (string) ($row->current_translation_key ?? ''),
                    'suggested_shared_translation_key' => (string) ($row->suggested_shared_translation_key ?? ''),
                    'matched_translation_key' => (string) ($row->matched_translation_key ?? ''),
                    'status' => (string) $row->status,
                ])->all();
            })
            ->groupBy('map_key')
            ->map(static fn(Collection $rows): array => $rows->map(static function (array $row): array {
                unset($row['map_key']);

                return $row;
            })->values()->all());
    }

    private function movedRelationsByTranslationKey(): Collection
    {
        return DB::table('translation_workbench_lang_values')
            ->whereNotNull('meta')
            ->get(['id', 'translation_key', 'locale', 'meta'])
            ->flatMap(function (object $row): array {
                $meta = is_string($row->meta) ? json_decode($row->meta, true) : (array) $row->meta;
                $previousTranslationKey = trim((string) ($meta['previous_translation_key'] ?? ''));
                $rekeyedToTranslationKey = trim((string) ($meta['rekeyed_to_translation_key'] ?? ''));

                if ($previousTranslationKey === '' && $rekeyedToTranslationKey === '') {
                    return [];
                }

                $keys = collect([
                    $row->translation_key,
                    $previousTranslationKey,
                    $rekeyedToTranslationKey,
                ])
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->filter(static fn(string $key): bool => $key !== '')
                    ->unique()
                    ->values();

                return $keys->map(fn(string $key): array => [
                    'map_key' => $this->normalizeTranslationKey($key),
                    'lang_value_id' => (int) $row->id,
                    'locale' => (string) $row->locale,
                    'translation_key' => (string) $row->translation_key,
                    'previous_translation_key' => $previousTranslationKey,
                    'rekeyed_to_translation_key' => $rekeyedToTranslationKey,
                ])->all();
            })
            ->groupBy('map_key')
            ->map(static fn(Collection $rows): array => $rows->map(static function (array $row): array {
                unset($row['map_key']);

                return $row;
            })->values()->all());
    }

    /**
     * @return array<int, string>
     */
    private function relatedTranslationKeys(string $translationKey, Collection $sharedRows, Collection $movedRows): array
    {
        return collect([$translationKey])
            ->merge($sharedRows->pluck('current_translation_key'))
            ->merge($sharedRows->pluck('suggested_shared_translation_key'))
            ->merge($sharedRows->pluck('matched_translation_key'))
            ->merge($movedRows->pluck('translation_key'))
            ->merge($movedRows->pluck('previous_translation_key'))
            ->merge($movedRows->pluck('rekeyed_to_translation_key'))
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter(static fn(string $key): bool => $key !== '')
            ->unique(static fn(string $key): string => mb_strtolower($key))
            ->values()
            ->all();
    }

    private function chainType(Collection $sharedRows, int $bulkReviewCount, Collection $movedRows): string
    {
        if ($bulkReviewCount > 0) {
            return 'bulk';
        }

        if ($sharedRows->isNotEmpty()) {
            return 'shared';
        }

        if ($movedRows->isNotEmpty()) {
            return 'moved';
        }

        return 'single';
    }

    private function chainKey(string $translationKey): string
    {
        return hash('sha256', $this->normalizeTranslationKey($translationKey));
    }

    private function normalizeTranslationKey(string $translationKey): string
    {
        return mb_strtolower(trim($translationKey));
    }

    private function groupFromTranslationKey(string $translationKey): ?string
    {
        $parts = explode('.', $translationKey);

        return count($parts) > 1 ? $parts[1] : null;
    }
}
