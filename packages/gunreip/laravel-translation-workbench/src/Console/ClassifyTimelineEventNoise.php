<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ClassifyTimelineEventNoise.php

// php artisan translation-workbench:classify-timeline-event-noise
// php artisan translation-workbench:classify-timeline-event-noise --chain-id=594
// php artisan translation-workbench:classify-timeline-event-noise --write

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineChain;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineEvent;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('translation-workbench:classify-timeline-event-noise
    {--write : Mark candidate events as dead_dev_event. Without this option the command only reports candidates.}
    {--threshold=50 : Minimum dynamic_source_classification_changed count before a chain is considered noisy.}
    {--chain-id=* : Limit the analysis to one or more timeline-chain IDs.}')]
#[Description('Detect and optionally classify historical high-volume timeline event noise without changing event_type.')]
class ClassifyTimelineEventNoise extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Required Translation Workbench timeline tables are missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport(summary: ['error' => 'missing_required_tables']);

            return self::FAILURE;
        }

        $write = (bool) $this->option('write');
        $hasClassificationColumn = Schema::hasColumn('translation_workbench_timeline_events', 'event_classification');

        if ($write && ! $hasClassificationColumn) {
            $this->error('Column translation_workbench_timeline_events.event_classification is missing. Run the event-classification migration first.');
            $this->writeTranslationWorkbenchReport(summary: ['error' => 'missing_event_classification_column']);

            return self::FAILURE;
        }

        $threshold = max(1, (int) $this->option('threshold'));
        $chainIds = $this->chainIds();
        $rows = $this->candidateRows($threshold, $chainIds);
        $candidateEventIds = $rows
            ->flatMap(static fn(array $row): array => $row['candidate_event_ids'])
            ->unique()
            ->values();
        $marked = 0;

        if ($write && $candidateEventIds->isNotEmpty()) {
            $marked = DB::table('translation_workbench_timeline_events')
                ->whereIn('id', $candidateEventIds->all())
                ->where('event_type', 'dynamic_source_classification_changed')
                ->where('event_classification', TranslationWorkbenchTimelineEvent::CLASSIFICATION_NORMAL)
                ->update([
                    'event_classification' => TranslationWorkbenchTimelineEvent::CLASSIFICATION_DEAD_DEV_EVENT,
                    'updated_at' => now(),
                ]);
        }

        $summary = [
            'mode' => $write ? 'write' : 'dry_run',
            'threshold' => $threshold,
            'chain_ids' => $chainIds,
            'classification_column_available' => $hasClassificationColumn,
            'noisy_chains' => $rows->count(),
            'candidate_events' => $rows->sum('candidate_dead_dev_events'),
            'unique_candidate_events' => $candidateEventIds->count(),
            'marked_events' => $marked,
            'classification' => TranslationWorkbenchTimelineEvent::CLASSIFICATION_DEAD_DEV_EVENT,
        ];

        $this->components->info('Translation Workbench timeline event noise classification finished.');
        $this->line('Mode: ' . $summary['mode']);
        $this->line('Noisy chains: ' . number_format((int) $summary['noisy_chains']));
        $this->line('Candidate events: ' . number_format((int) $summary['candidate_events']));
        $this->line('Unique candidate events: ' . number_format((int) $summary['unique_candidate_events']));

        if (! $write) {
            $this->warn('Dry run/report only: no timeline events were marked. Use --write after reviewing the JSON report.');
        } else {
            $this->line('Marked events: ' . number_format($marked));
        }

        $this->writeTranslationWorkbenchReport(
            summary: $summary,
            planSummary: [
                'rows' => $rows
                    ->map(static fn(array $row): array => collect($row)->except('candidate_event_ids')->all())
                    ->values()
                    ->all(),
            ],
        );

        return self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_timeline_chains',
            'translation_workbench_timeline_events',
        ])->every(static fn(string $table): bool => Schema::hasTable($table));
    }

    /**
     * @return array<int, int>
     */
    private function chainIds(): array
    {
        return collect((array) $this->option('chain-id'))
            ->map(static fn(mixed $chainId): int => (int) $chainId)
            ->filter(static fn(int $chainId): bool => $chainId > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $chainIds
     * @return Collection<int, array<string, mixed>>
     */
    private function candidateRows(int $threshold, array $chainIds): Collection
    {
        return TranslationWorkbenchTimelineChain::query()
            ->when($chainIds !== [], static fn($query) => $query->whereIn('id', $chainIds))
            ->orderBy('id')
            ->get()
            ->map(fn(TranslationWorkbenchTimelineChain $chain): ?array => $this->candidateRow($chain, $threshold))
            ->filter()
            ->values();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function candidateRow(TranslationWorkbenchTimelineChain $chain, int $threshold): ?array
    {
        $eventSummary = collect($chain->timeline_event_summary ?? [])
            ->mapWithKeys(static fn(mixed $count, string|int $eventType): array => [(string) $eventType => (int) $count]);
        $classificationChangedCount = (int) $eventSummary->get('dynamic_source_classification_changed', 0);

        if ($classificationChangedCount < $threshold) {
            return null;
        }

        $referenceCount = max(
            (int) $eventSummary->get('dynamic_source_classified', 0),
            (int) $eventSummary->get('dynamic_runtime_source_link_suggested', 0),
        );
        $retainedCount = max(1, $referenceCount);
        $eventIds = collect($chain->timeline_event_ids ?? [])
            ->map(static fn(mixed $eventId): int => (int) $eventId)
            ->filter(static fn(int $eventId): bool => $eventId > 0)
            ->values();

        if ($eventIds->isEmpty()) {
            return null;
        }

        $classificationEventIds = DB::table('translation_workbench_timeline_events')
            ->whereIn('id', $eventIds->all())
            ->where('event_type', 'dynamic_source_classification_changed')
            ->orderBy('created_at')
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn(mixed $eventId): int => (int) $eventId)
            ->values();

        if ($classificationEventIds->count() < $threshold) {
            return null;
        }

        $candidateEventIds = $classificationEventIds
            ->skip($retainedCount)
            ->values();

        if ($candidateEventIds->isEmpty()) {
            return null;
        }

        return [
            'timeline_chain_id' => (int) $chain->id,
            'chain_type' => (string) $chain->chain_type,
            'chain_status' => (string) $chain->chain_status,
            'translation_key' => (string) $chain->translation_key,
            'root_key_id' => $chain->root_key_id ? (int) $chain->root_key_id : null,
            'root_finding_id' => $chain->root_finding_id ? (int) $chain->root_finding_id : null,
            'classification_changed_events' => $classificationEventIds->count(),
            'retained_events' => $retainedCount,
            'candidate_dead_dev_events' => $candidateEventIds->count(),
            'candidate_event_ids' => $candidateEventIds->all(),
        ];
    }
}
