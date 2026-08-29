<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Facades\DB;

final class ClassificationNoiseAnalyzer
{
    /**
     * Read-only diagnostic report for historical high-volume classification churn.
     *
     * This does not mutate timeline events. A later cleanup workflow should use a
     * separate event-classification column instead of rewriting event_type.
     *
     * @param  array<string, mixed>  $mainRow
     * @return array<string, mixed>|null
     */
    public static function report(
        array $mainRow,
        int $classificationChangedEventCount,
        int $retainedClassificationEventCount,
        int $potentialDeadDevEventCount,
    ): ?array {
        if ($potentialDeadDevEventCount <= 0) {
            return null;
        }

        $eventIds = ValueNormalizer::integerList($mainRow['timeline_event_ids'] ?? []);

        if ($eventIds === []) {
            return null;
        }

        $summary = DB::table('translation_workbench_timeline_events')
            ->whereIn('id', $eventIds)
            ->where('event_type', 'dynamic_source_classification_changed')
            ->selectRaw('count(*) as total')
            ->selectRaw('min(created_at) as first_seen')
            ->selectRaw('max(created_at) as last_seen')
            ->selectRaw('count(distinct key_id) as key_count')
            ->selectRaw('count(distinct finding_id) as finding_count')
            ->selectRaw('count(distinct context::text) as context_count')
            ->selectRaw('count(distinct new_values::text) as state_variant_count')
            ->first();

        if ($summary === null) {
            return null;
        }

        $topSources = DB::table('translation_workbench_timeline_events')
            ->whereIn('id', $eventIds)
            ->where('event_type', 'dynamic_source_classification_changed')
            ->selectRaw("context->>'source_path' as source_path")
            ->selectRaw("context->>'source_line' as source_line")
            ->selectRaw('count(*) as total')
            ->selectRaw('count(distinct new_values::text) as state_variant_count')
            ->selectRaw('min(created_at) as first_seen')
            ->selectRaw('max(created_at) as last_seen')
            ->groupBy('source_path', 'source_line')
            ->orderByDesc('total')
            ->limit(3)
            ->get()
            ->map(static fn(object $row): array => [
                'source_path' => (string) ($row->source_path ?? ''),
                'source_line' => (string) ($row->source_line ?? ''),
                'total' => (int) $row->total,
                'state_variant_count' => (int) $row->state_variant_count,
                'first_seen' => LabelFormatter::graphTimestampLabel($row->first_seen ?? null),
                'last_seen' => LabelFormatter::graphTimestampLabel($row->last_seen ?? null),
            ])
            ->all();

        return [
            'event_type' => 'dynamic_source_classification_changed',
            'total' => (int) ($summary->total ?? $classificationChangedEventCount),
            'retained' => $retainedClassificationEventCount,
            'candidate_dead_dev_events' => $potentialDeadDevEventCount,
            'first_seen' => LabelFormatter::graphTimestampLabel($summary->first_seen ?? null),
            'last_seen' => LabelFormatter::graphTimestampLabel($summary->last_seen ?? null),
            'key_count' => (int) ($summary->key_count ?? 0),
            'finding_count' => (int) ($summary->finding_count ?? 0),
            'context_count' => (int) ($summary->context_count ?? 0),
            'state_variant_count' => (int) ($summary->state_variant_count ?? 0),
            'top_sources' => $topSources,
        ];
    }
}
