<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class TwGraphDataDrivenDatasets extends Component
{
    public ?int $timelineChainId = null;

    /**
     * @var array<int, array{id: int, label: string}>
     */
    public array $datasetHistory = [];

    public ?string $selectedHistoryId = null;

    public int $reloadTick = 0;

    public function mount(): void
    {
        $this->timelineChainId ??= $this->randomTimelineChainId();
        $this->selectedHistoryId = $this->timelineChainId !== null ? (string) $this->timelineChainId : null;

        if ($this->timelineChainId !== null) {
            $this->rememberDatasetId($this->timelineChainId);
        }
    }

    public function randomDataset(): void
    {
        $this->timelineChainId = $this->randomTimelineChainId();
        $this->selectedHistoryId = $this->timelineChainId !== null ? (string) $this->timelineChainId : null;
        $this->reloadTick++;

        if ($this->timelineChainId !== null) {
            $this->rememberDatasetId($this->timelineChainId);
        }
    }

    public function reloadDataset(): void
    {
        $this->reloadTick++;

        if ($this->timelineChainId !== null) {
            $this->rememberDatasetId($this->timelineChainId);
        }
    }

    public function updatedSelectedHistoryId(mixed $value): void
    {
        $id = (int) $value;

        if ($id > 0) {
            $this->timelineChainId = $id;
            $this->selectedHistoryId = (string) $id;
            $this->reloadTick++;
            $this->rememberDatasetId($id);
        }
    }

    public function render()
    {
        $mainRow = $this->timelineChainMainRow();

        return view('translation-workbench::livewire.tw-graph.data-driven.datasets', [
            'mainRow' => $mainRow,
            'rootRows' => $mainRow ? $this->timelineChainRootRows($mainRow) : [],
            'originRows' => $mainRow ? $this->timelineChainOriginRows($mainRow) : [],
            'datasetHistory' => $this->datasetHistory,
            'reloadTick' => $this->reloadTick,
        ]);
    }

    private function randomTimelineChainId(): ?int
    {
        if (! Schema::hasTable('translation_workbench_timeline_chains')) {
            return null;
        }

        $id = DB::table('translation_workbench_timeline_chains')
            ->inRandomOrder()
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    private function rememberDatasetId(int $id): void
    {
        if (! Schema::hasTable('translation_workbench_timeline_chains')) {
            return;
        }

        $row = DB::table('translation_workbench_timeline_chains')
            ->where('id', $id)
            ->first(['id', 'translation_key', 'chain_type', 'chain_status']);

        if (! $row) {
            return;
        }

        $label = '#' . $id . ' · '
            . str((string) $row->chain_type)->headline() . ' · '
            . str((string) $row->chain_status)->headline() . ' · '
            . (string) $row->translation_key;

        $this->datasetHistory = collect($this->datasetHistory)
            ->reject(static fn(array $entry): bool => (int) ($entry['id'] ?? 0) === $id)
            ->prepend([
                'id' => $id,
                'label' => $label,
            ])
            ->take(5)
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function timelineChainMainRow(): ?array
    {
        if (! Schema::hasTable('translation_workbench_timeline_chains')) {
            return null;
        }

        $row = DB::table('translation_workbench_timeline_chains')
            ->where('id', $this->timelineChainId)
            ->first([
                'id',
                'translation_key',
                'chain_type',
                'chain_status',
                'root_key_id',
                'root_finding_id',
                'key_count',
                'finding_count',
                'active_finding_count',
                'obsolete_finding_count',
                'commented_out_finding_count',
                'review_count',
                'timeline_event_count',
                'lang_value_count',
                'shared_candidate_count',
                'bulk_review_count',
                'key_ids',
                'finding_ids',
                'review_ids',
                'lang_value_ids',
                'related_translation_keys',
                'relation_summary',
                'lang_value_summary',
                'timeline_event_summary',
                'timeline_event_ids',
                'meta',
                'first_seen_at',
                'last_seen_at',
                'created_at',
                'updated_at',
            ]);

        if (! $row) {
            return null;
        }

        return [
            'id' => (int) $row->id,
            'translation_key' => (string) $row->translation_key,
            'chain_type' => (string) $row->chain_type,
            'chain_status' => (string) $row->chain_status,
            'root_key_id' => $row->root_key_id !== null ? (int) $row->root_key_id : null,
            'root_finding_id' => $row->root_finding_id !== null ? (int) $row->root_finding_id : null,
            'key_count' => (int) $row->key_count,
            'finding_count' => (int) $row->finding_count,
            'active_finding_count' => (int) $row->active_finding_count,
            'obsolete_finding_count' => (int) $row->obsolete_finding_count,
            'commented_out_finding_count' => (int) $row->commented_out_finding_count,
            'review_count' => (int) $row->review_count,
            'timeline_event_count' => (int) $row->timeline_event_count,
            'lang_value_count' => (int) $row->lang_value_count,
            'shared_candidate_count' => (int) $row->shared_candidate_count,
            'bulk_review_count' => (int) $row->bulk_review_count,
            'key_ids' => $this->decodeJsonArray($row->key_ids ?? null),
            'finding_ids' => $this->decodeJsonArray($row->finding_ids ?? null),
            'review_ids' => $this->decodeJsonArray($row->review_ids ?? null),
            'lang_value_ids' => $this->decodeJsonArray($row->lang_value_ids ?? null),
            'related_translation_keys' => $this->decodeJsonArray($row->related_translation_keys ?? null),
            'relation_summary' => $this->decodeJsonArray($row->relation_summary ?? null),
            'lang_value_summary' => $this->decodeJsonArray($row->lang_value_summary ?? null),
            'timeline_event_summary' => $this->decodeJsonArray($row->timeline_event_summary ?? null),
            'timeline_event_ids' => $this->decodeJsonArray($row->timeline_event_ids ?? null),
            'meta' => $this->decodeJsonArray($row->meta ?? null),
            'first_seen_at' => $row->first_seen_at,
            'last_seen_at' => $row->last_seen_at,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<int, array<string, mixed>>
     */
    private function timelineChainRootRows(array $mainRow): array
    {
        $translationKey = (string) $mainRow['translation_key'];
        $trunk = ! empty($mainRow['root_key_id'])
            ? 'key #' . $mainRow['root_key_id']
            : __('No root key');
        $rows = collect();

        if (! empty($mainRow['root_key_id'])) {
            $rows->push([
                'timestamp' => $mainRow['updated_at'],
                'trunk' => $trunk,
                'branch' => __('Root'),
                'translation_key' => $translationKey,
                'event' => __('Current canonical root'),
                'state' => str((string) $mainRow['chain_type'])->headline() . ' / ' . str((string) $mainRow['chain_status'])->headline(),
                'color' => $this->timelineGraphColor('root_event', 'green'),
                'branch_color' => $this->timelineGraphColor('root_event', 'green'),
            ]);
        }

        if (! empty($mainRow['root_key_id']) && Schema::hasTable('translation_workbench_keys')) {
            $key = DB::table('translation_workbench_keys')->find((int) $mainRow['root_key_id']);

            if ($key) {
                $rows->push([
                    'timestamp' => $key->created_at,
                    'trunk' => $trunk,
                    'branch' => __('Root key'),
                    'translation_key' => $translationKey,
                    'event' => __('Key created'),
                    'state' => (string) $key->status,
                    'color' => $this->timelineGraphColor('key_event', 'violet'),
                    'branch_color' => $this->timelineGraphColor('key_event', 'violet'),
                ]);

                if ($key->reviewed_at) {
                    $rows->push([
                        'timestamp' => $key->reviewed_at,
                        'trunk' => $trunk,
                        'branch' => __('Root key'),
                        'translation_key' => $translationKey,
                        'event' => __('Key reviewed'),
                        'state' => (string) $key->review_status,
                        'color' => $this->timelineGraphColor('key_reviewed_event', 'green'),
                        'branch_color' => $this->timelineGraphColor('key_event', 'violet'),
                    ]);
                }

                if ($key->updated_at && (string) $key->updated_at !== (string) $key->created_at) {
                    $rows->push([
                        'timestamp' => $key->updated_at,
                        'trunk' => $trunk,
                        'branch' => __('Root key'),
                        'translation_key' => $translationKey,
                        'event' => __('Key updated'),
                        'state' => (string) $key->review_status,
                        'color' => $this->timelineGraphColor('key_updated_event', 'cyan'),
                        'branch_color' => $this->timelineGraphColor('key_event', 'violet'),
                    ]);
                }
            }
        }

        if (! empty($mainRow['root_finding_id']) && Schema::hasTable('translation_workbench_findings')) {
            $finding = DB::table('translation_workbench_findings')->find((int) $mainRow['root_finding_id']);

            if ($finding) {
                $rows->push([
                    'timestamp' => $finding->created_at,
                    'trunk' => $trunk,
                    'branch' => 'finding #' . $finding->id,
                    'translation_key' => $translationKey,
                    'event' => __('Finding created'),
                    'state' => (string) $finding->status,
                    'color' => $this->timelineGraphColor('finding_event', 'sky'),
                    'branch_color' => $this->timelineGraphColor('finding_event', 'sky'),
                ]);
            }
        }

        if (Schema::hasTable('translation_workbench_lang_values')) {
            DB::table('translation_workbench_lang_values')
                ->where('translation_key', $translationKey)
                ->orderBy('locale')
                ->get(['id', 'locale', 'status', 'created_at', 'updated_at'])
                ->each(function (object $langValue) use ($rows, $translationKey, $trunk): void {
                    $rows->push([
                        'timestamp' => $langValue->updated_at ?: $langValue->created_at,
                        'trunk' => $trunk,
                        'branch' => 'lang value #' . $langValue->id,
                        'translation_key' => $translationKey,
                        'event' => __('Lang value'),
                        'state' => trim((string) $langValue->locale . ' / ' . (string) $langValue->status),
                        'color' => $langValue->status === 'active'
                            ? $this->timelineGraphColor('lang_value_active_event', 'emerald')
                            : $this->timelineGraphColor('lang_value_inactive_event', 'zinc'),
                        'branch_color' => $langValue->status === 'active'
                            ? $this->timelineGraphColor('lang_value_active_event', 'emerald')
                            : $this->timelineGraphColor('lang_value_inactive_event', 'zinc'),
                    ]);
                });
        }

        if (! empty($mainRow['root_key_id']) && Schema::hasTable('translation_workbench_reviews')) {
            DB::table('translation_workbench_reviews')
                ->where('key_id', (int) $mainRow['root_key_id'])
                ->when(
                    ! empty($mainRow['root_finding_id']),
                    fn($query) => $query->where('finding_id', (int) $mainRow['root_finding_id']),
                )
                ->whereIn('decision', [
                    'translation_key_updated',
                    'translation_key_bulk_equalized',
                    'translation_values_saved',
                ])
                ->orderByDesc('reviewed_at')
                ->limit(12)
                ->get(['id', 'review_type', 'decision', 'reviewed_at', 'created_at'])
                ->each(function (object $review) use ($rows, $translationKey, $trunk): void {
                    $rows->push([
                        'timestamp' => $review->reviewed_at ?: $review->created_at,
                        'trunk' => $trunk,
                        'branch' => 'review #' . $review->id,
                        'translation_key' => $translationKey,
                        'event' => str((string) $review->decision)->replace('_', ' ')->headline()->toString(),
                        'state' => (string) $review->review_type,
                        'color' => $this->timelineGraphColor('review_event', 'amber'),
                        'branch_color' => $this->timelineGraphColor('review_event', 'amber'),
                    ]);
                });
        }

        return $rows
            ->filter(static fn(array $row): bool => filled($row['timestamp'] ?? null))
            ->unique(static fn(array $row): string => implode('|', [
                (string) $row['timestamp'],
                (string) $row['trunk'],
                (string) $row['branch'],
                (string) $row['event'],
                (string) $row['state'],
            ]))
            ->sortByDesc('timestamp')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<int, array<string, mixed>>
     */
    private function timelineChainOriginRows(array $mainRow): array
    {
        if (empty($mainRow['root_key_id']) || ! Schema::hasTable('translation_workbench_findings')) {
            return [];
        }

        $translationKey = (string) $mainRow['translation_key'];
        $trunk = 'key #' . $mainRow['root_key_id'];
        $bulkReviews = Schema::hasTable('translation_workbench_reviews')
            ? DB::table('translation_workbench_reviews')
                ->where('key_id', (int) $mainRow['root_key_id'])
                ->where('decision', 'translation_key_bulk_equalized')
                ->orderBy('reviewed_at')
                ->get(['id', 'finding_id', 'meta', 'reviewed_at', 'created_at'])
            : collect();

        $selectedFindingIds = $bulkReviews
            ->flatMap(function (object $review): array {
                $meta = $this->decodeJsonArray($review->meta ?? null);
                $ids = collect($meta['selected_finding_ids'] ?? [])
                    ->map(static fn(mixed $id): int => (int) $id)
                    ->filter(static fn(int $id): bool => $id > 0)
                    ->values()
                    ->all();

                if (! empty($review->finding_id)) {
                    $ids[] = (int) $review->finding_id;
                }

                return $ids;
            })
            ->unique()
            ->values();

        $findings = $selectedFindingIds->isNotEmpty()
            ? $this->timelineChainFindings($selectedFindingIds->all())
            : collect();

        $edgeFindings = collect([
            $findings->sortBy(fn(object $finding): string => (string) ($finding->first_seen_at ?: $finding->created_at))->first(),
            $findings->sortByDesc(fn(object $finding): string => (string) ($finding->first_seen_at ?: $finding->created_at))->first(),
        ])
            ->filter()
            ->unique('id')
            ->values();

        $bulkReviewByFindingId = collect();

        $bulkReviews->each(function (object $review) use ($bulkReviewByFindingId): void {
            $meta = $this->decodeJsonArray($review->meta ?? null);
            $ids = collect($meta['selected_finding_ids'] ?? [])
                ->map(static fn(mixed $id): int => (int) $id)
                ->filter(static fn(int $id): bool => $id > 0);

            if (! empty($review->finding_id)) {
                $ids->push((int) $review->finding_id);
            }

            $ids
                ->unique()
                ->each(static function (int $id) use ($bulkReviewByFindingId, $review): void {
                    if (! $bulkReviewByFindingId->has($id)) {
                        $bulkReviewByFindingId->put($id, $review);
                    }
                });
        });

        $bulkOriginRows = $edgeFindings
            ->map(function (object $finding) use ($bulkReviewByFindingId, $translationKey, $trunk): array {
                $root = 'finding #' . $finding->id;
                $literal = trim((string) ($finding->literal_text ?? $finding->literal_text_suggested ?? ''));
                $source = trim((string) ($finding->source_path ?? ''));
                $source .= ! empty($finding->source_line) ? ':' . (string) $finding->source_line : '';
                $bulkReview = $bulkReviewByFindingId->get((int) $finding->id);

                return [
                    'trunk' => $trunk,
                    'context' => $literal !== '' ? $literal : $source,
                    'source_path' => $source,
                    'translation_key' => $translationKey,
                    'first_timestamp' => $finding->first_seen_at ?: $finding->created_at,
                    'first_root' => $root,
                    'first_origin_key' => (string) ($finding->suggested_key ?? ''),
                    'first_event' => __('First seen as single finding'),
                    'first_state' => (string) $finding->status,
                    'first_color' => $this->timelineGraphColor('finding_event', 'sky'),
                    'last_timestamp' => $bulkReview?->reviewed_at ?: $bulkReview?->created_at,
                    'last_root' => $root,
                    'last_origin_key' => (string) ($finding->suggested_key ?? ''),
                    'last_event' => __('Last single state before shared key'),
                    'last_state' => $bulkReview ? ('review #' . $bulkReview->id) : __('No bulk review found'),
                    'last_color' => $bulkReview
                        ? $this->timelineGraphColor('review_event', 'amber')
                        : $this->timelineGraphColor('fallback', 'zinc'),
                ];
            })
            ->filter(static fn(array $row): bool => filled($row['first_timestamp'] ?? null) || filled($row['last_timestamp'] ?? null));

        return $bulkOriginRows
            ->merge($this->timelineChainSharedOriginRows($mainRow, $trunk))
            ->unique(static fn(array $row): string => implode('|', [
                (string) ($row['first_root'] ?? ''),
                (string) ($row['first_origin_key'] ?? ''),
                (string) ($row['last_state'] ?? ''),
            ]))
            ->sortBy('first_timestamp')
            ->values()
            ->all();
    }

    private function timelineChainSharedOriginRows(array $mainRow, string $trunk): Collection
    {
        $translationKey = (string) ($mainRow['translation_key'] ?? '');
        $sharedCandidates = collect(data_get($mainRow, 'meta.shared_candidates', []))
            ->filter(static fn(mixed $candidate): bool => is_array($candidate))
            ->values();

        if ($sharedCandidates->isEmpty()) {
            return collect();
        }

        $findingsById = $this->timelineChainFindings(
            $sharedCandidates
                ->pluck('finding_id')
                ->filter()
                ->all()
        )->keyBy('id');

        return $sharedCandidates
            ->map(function (array $candidate) use ($findingsById, $translationKey, $trunk): ?array {
                $findingId = (int) ($candidate['finding_id'] ?? 0);
                $finding = $findingId > 0 ? $findingsById->get($findingId) : null;
                $originKey = collect([
                    $candidate['current_translation_key'] ?? null,
                    $candidate['matched_translation_key'] ?? null,
                    $finding?->suggested_key ?? null,
                ])
                    ->map(static fn(mixed $key): string => trim((string) $key))
                    ->first(static fn(string $key): bool => $key !== '' && $key !== $translationKey);

                if (! filled($originKey)) {
                    return null;
                }

                $literal = trim((string) ($finding?->literal_text ?? $finding?->literal_text_suggested ?? ''));
                $source = trim((string) ($finding?->source_path ?? ''));
                $source .= ! empty($finding?->source_line) ? ':' . (string) $finding->source_line : '';

                return [
                    'trunk' => $trunk,
                    'context' => $literal !== '' ? $literal : $source,
                    'source_path' => $source,
                    'translation_key' => $translationKey,
                    'first_timestamp' => $finding?->first_seen_at ?: $finding?->created_at ?: ($mainRow['first_seen_at'] ?? null),
                    'first_root' => $findingId > 0 ? ('finding #' . $findingId) : ('candidate #' . (string) ($candidate['id'] ?? '?')),
                    'first_origin_key' => $originKey,
                    'first_event' => __('First seen as shared candidate origin'),
                    'first_state' => (string) ($finding?->status ?? $candidate['status'] ?? ''),
                    'first_color' => $this->timelineGraphColor('finding_event', 'sky'),
                    'last_timestamp' => $finding?->last_seen_at ?: $finding?->updated_at ?: ($mainRow['updated_at'] ?? null),
                    'last_root' => 'key #' . (string) ($candidate['matched_key_id'] ?? $mainRow['root_key_id'] ?? '?'),
                    'last_origin_key' => $originKey,
                    'last_event' => __('Mapped to shared key'),
                    'last_state' => 'candidate #' . (string) ($candidate['id'] ?? '?') . ' / ' . (string) ($candidate['status'] ?? ''),
                    'last_color' => $this->timelineGraphColor('merge', 'amber'),
                ];
            })
            ->filter()
            ->values();
    }

    private function timelineChainFindings(array $findingIds): Collection
    {
        $ids = collect($findingIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter(static fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return DB::table('translation_workbench_findings as findings')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->whereIn('findings.id', $ids->all())
            ->get([
                'findings.id',
                'findings.status',
                'findings.suggested_key',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'findings.created_at',
                'findings.updated_at',
                'source_files.path as source_path',
                'findings.source_line',
            ]);
    }

    /**
     * @return array<mixed>
     */
    private function decodeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function timelineGraphColor(string $key, string $fallback): string
    {
        return Defaults::dataDrivenString(
            'colors.' . $key,
            Defaults::graphString('colors.' . $key, $fallback),
        );
    }
}
