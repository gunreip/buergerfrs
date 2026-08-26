<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class MergeOutcomes
{
    /**
     * Collect the current state of origin findings before turning inactive
     * origins into branch strangs.
     *
     * @param  array<string, mixed>  $mainRow
     * @param  Collection<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function from(array $mainRow, Collection $originRows): array
    {
        $rows = $originRows->values();
        $findingIds = $rows
            ->map(static fn(array $row): ?int => self::findingIdFromRoot((string) ($row['first_root'] ?? '')))
            ->filter()
            ->unique()
            ->values();

        if ($rows->isEmpty() || $findingIds->isEmpty() || ! Schema::hasTable('translation_workbench_findings')) {
            return [
                'summary' => [
                    'total' => $rows->count(),
                    'source_active' => 0,
                    'source_inactive' => 0,
                    'unknown' => $rows->count(),
                    'branch_candidates' => 0,
                ],
                'rows' => [],
            ];
        }

        $findings = DB::table('translation_workbench_findings as findings')
            ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->whereIn('findings.id', $findingIds->all())
            ->get([
                'findings.id',
                'findings.status',
                'findings.suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'findings.updated_at',
                'source_files.path as source_path',
            ])
            ->keyBy('id');
        $sharedCandidates = Schema::hasTable('translation_workbench_shared_key_candidates')
            ? DB::table('translation_workbench_shared_key_candidates')
            ->whereIn('finding_id', $findingIds->all())
            ->orderByDesc('updated_at')
            ->get([
                'id',
                'finding_id',
                'key_id',
                'matched_key_id',
                'current_translation_key',
                'suggested_shared_translation_key',
                'status',
                'last_seen_at',
                'updated_at',
            ])
            ->groupBy('finding_id')
            ->map(static fn(Collection $candidates): object => $candidates->first())
            : collect();
        $keyLookups = collect([
            $mainRow['translation_key'] ?? null,
            ...$rows->pluck('first_origin_key')->all(),
        ])
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();
        $keys = Schema::hasTable('translation_workbench_keys') && $keyLookups->isNotEmpty()
            ? DB::table('translation_workbench_keys')
            ->whereIn('translation_key', $keyLookups->all())
            ->orWhereIn('suggested_key', $keyLookups->all())
            ->get(['id', 'translation_key', 'suggested_key', 'status', 'review_status', 'updated_at'])
            ->flatMap(static fn(object $key): array => array_filter([
                trim((string) $key->translation_key) => $key,
                trim((string) $key->suggested_key) => $key,
            ]))
            : collect();

        $outcomeRows = $rows
            ->map(static function (array $row, int $index) use ($findings, $sharedCandidates, $keys, $mainRow): array {
                $findingId = self::findingIdFromRoot((string) ($row['first_root'] ?? ''));
                $finding = $findingId ? $findings->get($findingId) : null;
                $candidate = $findingId ? $sharedCandidates->get($findingId) : null;
                $originKey = trim((string) ($row['first_origin_key'] ?? ''));
                $originKeyRow = $originKey !== '' ? $keys->get($originKey) : null;
                $targetKey = trim((string) ($mainRow['translation_key'] ?? ''));
                $findingStatus = (string) ($finding->status ?? 'unknown');
                $sharedStatus = (string) ($candidate->status ?? 'none');
                $originKeyStatus = (string) ($originKeyRow->status ?? 'unknown');
                $sourceActive = $finding !== null && $findingStatus === 'active';
                $branchCandidate = $finding === null || ! $sourceActive;
                $outcomeGroup = self::mergeOutcomeGroup($findingStatus, $sharedStatus, $originKeyStatus);

                return [
                    'index' => $index + 1,
                    'side' => $index % 2 === 0 ? 'left' : 'right',
                    'finding_id' => $findingId,
                    'origin_key' => $originKey,
                    'target_key' => $targetKey,
                    'finding_status' => $findingStatus,
                    'origin_key_status' => $originKeyStatus,
                    'origin_key_review_status' => (string) ($originKeyRow->review_status ?? 'unknown'),
                    'shared_candidate_id' => $candidate->id ?? null,
                    'shared_candidate_status' => $sharedStatus,
                    'matched_key_id' => $candidate->matched_key_id ?? null,
                    'first_seen_at' => LabelFormatter::graphTimestampLabel($row['first_timestamp'] ?? $finding?->first_seen_at ?? null),
                    'last_seen_at' => LabelFormatter::graphTimestampLabel($row['last_timestamp'] ?? $finding?->last_seen_at ?? $candidate?->last_seen_at ?? null),
                    'source_path' => (string) ($finding->source_path ?? $row['source_path'] ?? ''),
                    'outcome' => $finding === null
                        ? 'unknown finding'
                        : ($sourceActive ? 'source active' : 'source inactive'),
                    'outcome_group' => $outcomeGroup,
                    'branch_hint' => $branchCandidate,
                ];
            })
            ->values();
        $groups = $outcomeRows
            ->countBy('outcome_group')
            ->sortKeys()
            ->all();

        return [
            'summary' => [
                'total' => $outcomeRows->count(),
                'source_active' => $outcomeRows->where('outcome', 'source active')->count(),
                'source_inactive' => $outcomeRows->where('outcome', 'source inactive')->count(),
                'unknown' => $outcomeRows->where('outcome', 'unknown finding')->count(),
                'branch_candidates' => $outcomeRows->where('branch_hint', true)->count(),
                'branch_candidate_findings' => $outcomeRows
                    ->where('branch_hint', true)
                    ->pluck('finding_id')
                    ->filter()
                    ->unique()
                    ->count(),
                'ended_after_merge_findings' => $outcomeRows
                    ->where('outcome_group', 'ended after merge')
                    ->pluck('finding_id')
                    ->filter()
                    ->unique()
                    ->count(),
                'ended_after_merge_rows' => $outcomeRows
                    ->where('outcome_group', 'ended after merge')
                    ->count(),
                'groups' => $groups,
            ],
            'rows' => $outcomeRows->all(),
        ];
    }

    private static function mergeOutcomeGroup(string $findingStatus, string $sharedStatus, string $originKeyStatus): string
    {
        if ($findingStatus === 'active' && in_array($sharedStatus, ['obsolete', 'accepted', 'applied'], true)) {
            return 'arrived at shared key';
        }

        if ($findingStatus === 'active' && $originKeyStatus === 'obsolete') {
            return 'active source, obsolete origin key';
        }

        if ($findingStatus !== 'active' && $sharedStatus === 'obsolete') {
            return 'ended after merge';
        }

        if ($findingStatus !== 'active') {
            return 'ended before target';
        }

        if (in_array($sharedStatus, ['pending', 'open'], true)) {
            return 'shared review pending';
        }

        return 'needs review';
    }

    private static function findingIdFromRoot(string $root): ?int
    {
        return preg_match('/finding\s+#(\d+)/i', $root, $matches) === 1
            ? (int) $matches[1]
            : null;
    }
}
