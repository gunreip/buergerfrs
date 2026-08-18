<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ScanTranslationWorkbench.php

// php artisan translation-workbench:scan
// php artisan translation-workbench:scan --dry-run
// php artisan translation-workbench:scan --truncate
// php artisan translation-workbench:scan --truncate --force-truncate
// php artisan translation-workbench:scan --mark-obsolete
// php artisan translation-workbench:scan --paths=resources/views/components

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\ConfirmsTranslationWorkbenchTruncate;
use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEntry;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEvent;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOccurrence;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('translation-workbench:scan
    {--paths= : Comma-separated relative paths to scan. Defaults to config translation-workbench.paths.}
    {--dry-run : Scan and report only; do not write database rows.}
    {--truncate : Truncate translation workbench tables before writing scanned entries.}
    {--force-truncate : Skip the interactive safety confirmation for --truncate.}
    {--mark-obsolete : Mark previously seen but now missing entries as obsolete.}')]
#[Description('Scan code for translation-capable literals and collect them in the translation workbench tables.')]
class ScanTranslationWorkbench extends Command
{
    use ConfirmsTranslationWorkbenchTruncate;
    use WritesTranslationWorkbenchReports;

    public function handle(TranslationWorkbenchScanner $scanner): int
    {
        $paths = $this->paths();
        $dryRun = (bool) $this->option('dry-run');
        $truncate = (bool) $this->option('truncate');
        $markObsolete = (bool) $this->option('mark-obsolete');
        $now = now();

        if (! $this->confirmTranslationWorkbenchTruncate(
            $truncate,
            $dryRun,
            'The --truncate option will delete Translation Workbench scan tables before writing scanned entries.',
            'force-truncate',
            [
                'scope' => 'scan',
                'tables' => [
                    'translation_workbench_option_discoveries',
                    'translation_workbench_dynamic_values',
                    'translation_workbench_values',
                    'translation_workbench_occurrences',
                    'translation_workbench_events',
                    'translation_workbench_entries',
                ],
            ],
        )) {
            return self::FAILURE;
        }

        $items = $scanner->scan($paths);
        $summary = [
            'found' => $items->count(),
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'commented_out' => $items
                ->filter(static fn(DiscoveredTranslation $item): bool => ($item->meta['source_context'] ?? null) === 'commented_out')
                ->count(),
            'replaced' => 0,
            'obsolete' => 0,
            'truncated' => 0,
            'occurrences_created' => 0,
            'occurrences_updated' => 0,
            'occurrences_unchanged' => 0,
            'occurrences_stale' => 0,
        ];

        if (! $dryRun) {
            DB::transaction(function () use ($items, $truncate, $markObsolete, $now, &$summary): void {
                $seenSignatures = [];
                $seenFingerprints = [];

                if ($truncate) {
                    $summary['truncated'] = $this->truncateWorkbenchTables();
                }

                foreach ($items as $item) {
                    $seenSignatures[] = $item->sourceSignature;
                    $seenFingerprints[] = $item->fingerprint;
                    $syncResult = $this->syncItem($item, $now);
                    $summary[$syncResult['result']]++;
                    $summary['replaced'] += $syncResult['replaced'];

                    $occurrenceResult = $this->syncOccurrence($item, $syncResult['entry'], $now);
                    $summary[$occurrenceResult]++;
                }

                if ($markObsolete) {
                    $summary['obsolete'] = $this->markMissingEntriesObsolete($seenFingerprints, $now);
                    $summary['occurrences_stale'] = $this->markMissingOccurrencesStale($seenSignatures, $now);
                }
            });
        }

        $this->components->info('Translation workbench scan finished.');
        $this->line('Items found: ' . number_format($summary['found']));
        $this->line('Commented out: ' . number_format($summary['commented_out'] ?? 0));

        if ($dryRun) {
            $this->warn('Dry run only: no database rows were written.');

            if ($truncate) {
                $this->warn('The --truncate option was ignored because --dry-run is active.');
            }
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
     * @return array<int, string>|null
     */
    private function paths(): ?array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths === '') {
            return null;
        }

        return collect(explode(',', $paths))
            ->map(static fn(string $path): string => trim($path))
            ->filter()
            ->values()
            ->all();
    }

    private function truncateWorkbenchTables(): int
    {
        $tables = [
            'translation_workbench_option_discoveries',
            'translation_workbench_dynamic_values',
            'translation_workbench_values',
            'translation_workbench_occurrences',
            'translation_workbench_events',
            'translation_workbench_entries',
        ];

        $rowCount = collect($tables)
            ->sum(static fn(string $table): int => (int) DB::table($table)->count());

        match (DB::getDriverName()) {
            'pgsql' => DB::statement('TRUNCATE TABLE translation_workbench_option_discoveries, translation_workbench_dynamic_values, translation_workbench_values, translation_workbench_occurrences, translation_workbench_events, translation_workbench_entries RESTART IDENTITY CASCADE'),
            'mysql', 'mariadb' => $this->truncateMysqlWorkbenchTables($tables),
            default => $this->deleteWorkbenchTables($tables),
        };

        return $rowCount;
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function truncateMysqlWorkbenchTables(array $tables): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function deleteWorkbenchTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->delete();
        }
    }

    /**
     * @return array{result: string, entry: TranslationWorkbenchEntry, replaced: int}
     */
    private function syncItem(DiscoveredTranslation $item, mixed $now): array
    {
        $attributes = $item->toEntryAttributes();
        $isEmptyLiteralEntry = $this->isEmptyLiteralEntry($item);
        $isCommentedOutEntry = $this->isCommentedOutEntry($item);

        if ($isEmptyLiteralEntry) {
            $attributes['meta'] = [
                ...($attributes['meta'] ?? []),
                'ignored_reason' => 'empty_literal_translation_call',
                'translation_relevant' => false,
            ];
        }

        if ($isCommentedOutEntry) {
            $attributes['meta'] = [
                ...($attributes['meta'] ?? []),
                'commented_out_reason' => 'source_expression_inside_comment',
                'translation_relevant' => false,
            ];
        }

        $entry = TranslationWorkbenchEntry::query()
            ->where('fingerprint', $item->fingerprint)
            ->first();

        if (! $entry) {
            $previousEntry = $this->previousEntryFor($item);
            $entry = TranslationWorkbenchEntry::query()->create([
                'previous_entry_id' => $previousEntry?->id,
                ...$attributes,
                'status' => match (true) {
                    $isEmptyLiteralEntry => 'obsolete',
                    $isCommentedOutEntry => 'commented_out',
                    default => 'open',
                },
                'review_status' => 'pending',
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'scan_count' => 1,
            ]);

            $this->recordEvent($entry, 'discovered', null, $attributes, [
                'source' => 'translation-workbench:scan',
            ]);

            $replaced = $previousEntry
                ? $this->markEntryReplaced($previousEntry, $entry, $now)
                : 0;

            return ['result' => 'created', 'entry' => $entry, 'replaced' => $replaced];
        }

        $stableAttributes = $this->stableEntryAttributes($attributes, $entry);
        $nextStatus = match (true) {
            $isEmptyLiteralEntry => 'obsolete',
            $isCommentedOutEntry => 'commented_out',
            $entry->status === 'obsolete' || $entry->status === 'commented_out' => 'open',
            default => $entry->status,
        };
        $oldValues = $entry->only(array_keys($stableAttributes));
        $changed = collect($stableAttributes)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        $entry->forceFill([
            ...$stableAttributes,
            'status' => $nextStatus,
            'last_seen_at' => $now,
            'scan_count' => ((int) $entry->scan_count) + 1,
        ])->save();

        if ($changed !== []) {
            $this->recordEvent($entry, 'changed', $oldValues, $stableAttributes, [
                'source' => 'translation-workbench:scan',
                'changed_fields' => array_keys($changed),
            ]);

            return ['result' => 'updated', 'entry' => $entry, 'replaced' => 0];
        }

        return ['result' => 'unchanged', 'entry' => $entry, 'replaced' => 0];
    }

    private function isEmptyLiteralEntry(DiscoveredTranslation $item): bool
    {
        return $item->kind === 'literal'
            && $item->literalText !== null
            && trim($item->literalText) === '';
    }

    private function isCommentedOutEntry(DiscoveredTranslation $item): bool
    {
        return ($item->meta['source_context'] ?? null) === 'commented_out';
    }

    private function previousEntryFor(DiscoveredTranslation $item): ?TranslationWorkbenchEntry
    {
        if ($item->suggestedKey === null || $item->suggestedKey === '') {
            return null;
        }

        return TranslationWorkbenchEntry::query()
            ->where('fingerprint', '!=', $item->fingerprint)
            ->where('suggested_key', $item->suggestedKey)
            ->whereNull('replaced_by_entry_id')
            ->orderByDesc('last_seen_at')
            ->orderByDesc('id')
            ->first();
    }

    private function markEntryReplaced(TranslationWorkbenchEntry $previousEntry, TranslationWorkbenchEntry $newEntry, mixed $now): int
    {
        $oldValues = $previousEntry->only(['status', 'replaced_by_entry_id', 'last_seen_at']);

        $previousEntry->forceFill([
            'status' => 'obsolete',
            'replaced_by_entry_id' => $newEntry->id,
            'last_seen_at' => $previousEntry->last_seen_at ?? $now,
        ])->save();

        $this->recordEvent($previousEntry, 'replaced_by_entry', $oldValues, [
            'status' => 'obsolete',
            'replaced_by_entry_id' => $newEntry->id,
            'last_seen_at' => $previousEntry->last_seen_at,
        ], [
            'source' => 'translation-workbench:scan',
            'replacement_entry_id' => $newEntry->id,
        ]);

        $this->recordEvent($newEntry, 'replaces_entry', null, [
            'previous_entry_id' => $previousEntry->id,
        ], [
            'source' => 'translation-workbench:scan',
            'previous_entry_id' => $previousEntry->id,
        ]);

        return 1;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function stableEntryAttributes(array $attributes, TranslationWorkbenchEntry $entry): array
    {
        $stableAttributes = collect($attributes)
            ->except([
                'source_signature',
                'source_type',
                'source_path',
                'source_line',
                'function_name',
                'raw_expression',
                'translation_key',
                'translation_key_source',
                'suggested_key',
                'namespace',
                'group',
            ])
            ->all();

        if ($this->shouldRefreshScannerKeyMetadata($entry)) {
            $stableAttributes = [
                ...$stableAttributes,
                ...collect($attributes)
                    ->only(['suggested_key', 'namespace', 'group'])
                    ->all(),
            ];
        }

        return $stableAttributes;
    }

    private function shouldRefreshScannerKeyMetadata(TranslationWorkbenchEntry $entry): bool
    {
        return blank($entry->translation_key)
            && blank($entry->translation_key_source)
            && ! (bool) $entry->is_ui_key;
    }

    private function syncOccurrence(DiscoveredTranslation $item, ?TranslationWorkbenchEntry $entry, mixed $now): string
    {
        if (! $entry) {
            return 'occurrences_unchanged';
        }

        $attributes = $item->toOccurrenceAttributes();
        $occurrence = TranslationWorkbenchOccurrence::query()
            ->where('source_signature', $item->sourceSignature)
            ->first();

        if (! $occurrence) {
            $occurrence = TranslationWorkbenchOccurrence::query()->create([
                'entry_id' => $entry->id,
                ...$attributes,
                'status' => 'active',
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'scan_count' => 1,
            ]);

            $this->recordEvent($entry, 'occurrence_discovered', null, [
                'occurrence_id' => $occurrence->id,
                ...$attributes,
            ], [
                'source' => 'translation-workbench:scan',
            ]);

            return 'occurrences_created';
        }

        $oldValues = $occurrence->only([
            'entry_id',
            ...array_keys($attributes),
            'status',
        ]);
        $newValues = [
            'entry_id' => $entry->id,
            ...$attributes,
            'status' => 'active',
        ];
        $changed = collect($newValues)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        $occurrence->forceFill([
            ...$newValues,
            'last_seen_at' => $now,
            'scan_count' => ((int) $occurrence->scan_count) + 1,
        ])->save();

        if ($changed !== []) {
            $this->recordEvent($entry, 'occurrence_changed', $oldValues, $newValues, [
                'source' => 'translation-workbench:scan',
                'occurrence_id' => $occurrence->id,
                'changed_fields' => array_keys($changed),
            ]);

            return 'occurrences_updated';
        }

        return 'occurrences_unchanged';
    }

    /**
     * @param  array<int, string>  $seenFingerprints
     */
    private function markMissingEntriesObsolete(array $seenFingerprints, mixed $now): int
    {
        $count = 0;

        TranslationWorkbenchEntry::query()
            ->where('status', '!=', 'obsolete')
            ->when($seenFingerprints !== [], fn($query) => $query->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints))))
            ->orderBy('id')
            ->each(function (TranslationWorkbenchEntry $entry) use ($now, &$count): void {
                $oldValues = $entry->only(['status', 'last_seen_at']);

                $entry->forceFill([
                    'status' => 'obsolete',
                    'last_seen_at' => $entry->last_seen_at ?? $now,
                ])->save();

                $this->recordEvent($entry, 'marked_obsolete', $oldValues, [
                    'status' => 'obsolete',
                    'last_seen_at' => $entry->last_seen_at,
                ], [
                    'source' => 'translation-workbench:scan',
                ]);

                $count++;
            });

        return $count;
    }

    /**
     * @param  array<int, string>  $seenSignatures
     */
    private function markMissingOccurrencesStale(array $seenSignatures, mixed $now): int
    {
        $count = 0;

        TranslationWorkbenchOccurrence::query()
            ->where('status', '!=', 'stale')
            ->when($seenSignatures !== [], fn($query) => $query->whereNotIn('source_signature', array_values(array_unique($seenSignatures))))
            ->orderBy('id')
            ->each(function (TranslationWorkbenchOccurrence $occurrence) use ($now, &$count): void {
                $oldValues = $occurrence->only(['status', 'last_seen_at']);

                $occurrence->forceFill([
                    'status' => 'stale',
                    'last_seen_at' => $occurrence->last_seen_at ?? $now,
                ])->save();

                $this->recordEvent($occurrence->entry, 'occurrence_marked_stale', $oldValues, [
                    'occurrence_id' => $occurrence->id,
                    'status' => 'stale',
                    'last_seen_at' => $occurrence->last_seen_at,
                ], [
                    'source' => 'translation-workbench:scan',
                ]);

                $count++;
            });

        return $count;
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $context
     */
    private function recordEvent(
        TranslationWorkbenchEntry $entry,
        string $eventType,
        ?array $oldValues,
        ?array $newValues,
        ?array $context,
    ): void {
        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'context' => $context,
            'created_by' => auth()->id(),
        ]);
    }
}
