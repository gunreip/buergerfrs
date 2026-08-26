<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class FindingInspector
{
    /**
     * Build a focused inspection payload for deciding which finding details belong
     * directly in the graph and which should move into tooltips later.
     *
     * @param  array<string, mixed>|null  $mainRow
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $originRows
     * @return array<string, mixed>
     */
    public static function inspect(int $findingId, ?array $mainRow, Collection|array $originRows): array
    {
        $originRow = collect($originRows)
            ->first(static fn(array $row): bool => (string) ($row['first_root'] ?? '') === 'finding #' . $findingId);
        $preview = TimelineChainGraphData::fromTimelineChain($mainRow, collect(), collect($originRows));
        $previewMerges = collect(data_get($preview, 'render_preview.merges', []));
        $renderedAs = null;

        foreach ($previewMerges as $mergeIndex => $merge) {
            if (str_contains(json_encode($merge, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '', 'finding ID #' . $findingId)) {
                $renderedAs = [
                    'side' => data_get($merge, 'side'),
                    'strang' => data_get($merge, 'component'),
                    'component_counter' => $mergeIndex + 1,
                    'extension_count' => data_get($merge, 'extension_count', 0),
                ];
                break;
            }
        }

        $finding = null;
        if (Schema::hasTable('translation_workbench_findings')) {
            $query = DB::table('translation_workbench_findings as findings')
                ->leftJoin('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
                ->where('findings.id', $findingId);

            $finding = $query->first([
                'findings.id',
                'findings.status',
                'findings.suggested_key',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.literal_text',
                'findings.literal_text_suggested',
                'findings.namespace',
                'findings.group',
                'findings.path_key',
                'findings.kind',
                'findings.function_name',
                'findings.raw_expression',
                'findings.source_line',
                'findings.first_seen_at',
                'findings.last_seen_at',
                'findings.scan_count',
                'findings.created_at',
                'findings.updated_at',
                'source_files.path as source_path',
                'source_files.source_root',
                'source_files.source_area',
            ]);
        }

        $sharedCandidate = Schema::hasTable('translation_workbench_shared_key_candidates')
            ? DB::table('translation_workbench_shared_key_candidates')
            ->where('finding_id', $findingId)
            ->orderByDesc('updated_at')
            ->first([
                'id',
                'key_id',
                'matched_key_id',
                'current_translation_key',
                'suggested_shared_translation_key',
                'matched_review_count',
                'matched_finding_count',
                'confidence',
                'status',
                'first_seen_at',
                'last_seen_at',
                'created_at',
                'updated_at',
            ])
            : null;

        $reviews = Schema::hasTable('translation_workbench_reviews')
            ? DB::table('translation_workbench_reviews')
            ->where('finding_id', $findingId)
            ->orWhereJsonContains('meta->selected_finding_ids', $findingId)
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'key_id', 'finding_id', 'review_type', 'decision', 'reviewed_at', 'created_at'])
            ->map(static fn(object $row): array => (array) $row)
            ->all()
            : [];

        $timelineEvents = Schema::hasTable('translation_workbench_timeline_events')
            ? DB::table('translation_workbench_timeline_events')
            ->where('finding_id', $findingId)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'key_id', 'finding_id', 'review_id', 'event_type', 'created_at'])
            ->map(static fn(object $row): array => (array) $row)
            ->all()
            : [];

        $translationKeys = collect([
            data_get($finding, 'suggested_key'),
            data_get($finding, 'found_translation_key'),
            data_get($sharedCandidate, 'current_translation_key'),
            data_get($sharedCandidate, 'suggested_shared_translation_key'),
            data_get($mainRow, 'translation_key'),
        ])
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();

        $langValues = Schema::hasTable('translation_workbench_lang_values') && $translationKeys->isNotEmpty()
            ? DB::table('translation_workbench_lang_values')
            ->whereIn('translation_key', $translationKeys->all())
            ->orderBy('translation_key')
            ->orderBy('locale')
            ->limit(12)
            ->get(['id', 'locale', 'translation_key', 'value', 'status', 'locale_role', 'updated_at'])
            ->map(static fn(object $row): array => (array) $row)
            ->all()
            : [];

        return [
            'finding_id' => $findingId,
            'finding' => $finding ? (array) $finding : null,
            'origin_row' => $originRow,
            'rendered_as' => $renderedAs,
            'shared_candidate' => $sharedCandidate ? (array) $sharedCandidate : null,
            'reviews' => $reviews,
            'timeline_events' => $timelineEvents,
            'lang_values' => $langValues,
            'related_translation_keys' => $translationKeys->all(),
        ];
    }
}
