<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DetectDuplicateCandidates.php

// php artisan translation-workbench:detect-duplicates
// php artisan translation-workbench:detect-duplicates --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchDuplicateCandidate;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEntry;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('translation-workbench:detect-duplicates
    {--dry-run : Report duplicate groups without writing candidate rows.}')]
#[Description('Detect possible duplicate translation workbench entries from narrow diagnostic fingerprints.')]
class DetectDuplicateCandidates extends Command
{
    use WritesTranslationWorkbenchReports;

    public function __construct(private readonly TranslationFingerprintFactory $fingerprintFactory)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! Schema::hasTable('translation_workbench_duplicate_candidates')) {
            $this->error('The duplicate candidates table is missing. Run the workbench migrations first.');

            /**
             * Shared raw-data report.
             *
             * The report structure is centralized in WritesTranslationWorkbenchReports.
             * Do not add command-specific raw_data fields here or change the report
             * contract silently; discuss report contract changes first.
             */
            $this->writeTranslationWorkbenchReport();

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $entries = TranslationWorkbenchEntry::query()
            ->where('status', '!=', 'obsolete')
            ->get([
                'id',
                'kind',
                'entry_type',
                'candidate_type',
                'source_path',
                'source_line',
                'raw_expression',
                'literal_text',
                'literal_text_suggested',
                'translation_key',
                'suggested_key',
                'source_fingerprint',
                'expression_fingerprint',
                'semantic_fingerprint',
            ]);
        $candidateRows = collect()
            ->merge($this->rowsForFingerprint($entries, 'source_fingerprint', 'same_source', 'high'))
            ->merge($this->rowsForFingerprint($entries, 'expression_fingerprint', 'same_expression', 'medium'))
            ->merge($this->rowsForFingerprint($entries, 'semantic_fingerprint', 'same_semantic', 'medium'))
            ->merge($this->strongDuplicateRows($entries))
            ->values();

        if (! $dryRun) {
            DB::transaction(function () use ($candidateRows): void {
                TranslationWorkbenchDuplicateCandidate::query()->delete();

                $candidateRows
                    ->chunk(500)
                    ->each(static function (Collection $rows): void {
                        TranslationWorkbenchDuplicateCandidate::query()->insert($rows->all());
                    });
            });
        }

        $summary = $candidateRows
            ->groupBy('duplicate_type')
            ->map(static fn(Collection $rows): int => $rows->count())
            ->all();

        $this->components->info('Translation workbench duplicate detection finished.');
        $this->line('Candidate rows: ' . number_format($candidateRows->count()));

        if ($dryRun) {
            $this->warn('Dry run only: no duplicate candidate rows were written.');
        }

        /**
         * Shared raw-data report.
         *
         * The report structure is centralized in WritesTranslationWorkbenchReports.
         * Do not add command-specific raw_data fields here or change the report
         * contract silently; discuss report contract changes first.
         */
        $this->writeTranslationWorkbenchReport();

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, TranslationWorkbenchEntry>  $entries
     * @return Collection<int, array<string, mixed>>
     */
    private function rowsForFingerprint(Collection $entries, string $column, string $type, string $confidence): Collection
    {
        return $entries
            ->filter(static fn(TranslationWorkbenchEntry $entry): bool => filled($entry->{$column}))
            ->groupBy(static fn(TranslationWorkbenchEntry $entry): string => (string) $entry->{$column})
            ->filter(static fn(Collection $group): bool => $group->count() > 1)
            ->flatMap(fn(Collection $group, string $fingerprint): Collection => $this->rowsForGroup($group, $type, $fingerprint, $confidence))
            ->values();
    }

    /**
     * @param  Collection<int, TranslationWorkbenchEntry>  $entries
     * @return Collection<int, array<string, mixed>>
     */
    private function strongDuplicateRows(Collection $entries): Collection
    {
        return $entries
            ->filter(static fn(TranslationWorkbenchEntry $entry): bool => filled($entry->source_fingerprint) && filled($entry->expression_fingerprint) && filled($entry->semantic_fingerprint))
            ->groupBy(static fn(TranslationWorkbenchEntry $entry): string => implode('|', [
                $entry->source_fingerprint,
                $entry->expression_fingerprint,
                $entry->semantic_fingerprint,
            ]))
            ->filter(static fn(Collection $group): bool => $group->count() > 1)
            ->flatMap(fn(Collection $group, string $fingerprint): Collection => $this->rowsForGroup($group, 'strong_duplicate', $this->fingerprintFactory->signature([$fingerprint]), 'high'))
            ->values();
    }

    /**
     * @param  Collection<int, TranslationWorkbenchEntry>  $group
     * @return Collection<int, array<string, mixed>>
     */
    private function rowsForGroup(Collection $group, string $type, string $groupFingerprint, string $confidence): Collection
    {
        $now = now();
        $entryIds = $group
            ->pluck('id')
            ->map(static fn(int|string $id): int => (int) $id)
            ->sort()
            ->values()
            ->all();

        return $group
            ->map(static function (TranslationWorkbenchEntry $entry) use ($type, $groupFingerprint, $confidence, $entryIds, $now): array {
                return [
                    'entry_id' => $entry->id,
                    'duplicate_type' => $type,
                    'group_fingerprint' => $groupFingerprint,
                    'confidence' => $confidence,
                    'group_size' => count($entryIds),
                    'matched_entry_ids' => json_encode($entryIds),
                    'meta' => json_encode([
                        'source_path' => $entry->source_path,
                        'source_line' => $entry->source_line,
                        'kind' => $entry->kind,
                        'entry_type' => $entry->entry_type,
                        'candidate_type' => $entry->candidate_type,
                        'raw_expression' => $entry->raw_expression,
                        'literal_text' => $entry->literal_text,
                        'literal_text_suggested' => $entry->literal_text_suggested,
                        'translation_key' => $entry->translation_key,
                        'suggested_key' => $entry->suggested_key,
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values();
    }
}
