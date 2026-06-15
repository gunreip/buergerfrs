<?php

// app/Console/Commands/TranslationsAuditLangBallast.php

namespace App\Console\Commands;

use App\Models\TranslationLangBallastDecision;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('translations:audit-lang-ballast')]
#[Description('Audit lang/* translation entries that no longer match the current translation database state.')]
class TranslationsAuditLangBallast extends Command
{
    private const OUTPUT_DIR = 'audits/translations/lang-ballast';

    private const PREVIEW_LIMIT = 20;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Audit lang ballast');
        $this->line('Source: lang/*');
        $this->line('Mode: audit only, no files are modified');

        $langEntries = $this->collectLangEntries();
        $exportableEntries = $this->collectExportableEntries();
        $knownDbEntries = $this->collectKnownDbEntries();

        $langEntryIndex = $langEntries
            ->keyBy(fn(array $entry): string => $this->exportableEntryKey(
                locale: (string) $entry['locale'],
                key: (string) $entry['key'],
            ));

        $matchedEntries = $langEntries
            ->filter(function (array $entry) use ($exportableEntries): bool {
                return $exportableEntries->has($this->exportableEntryKey(
                    locale: (string) $entry['locale'],
                    key: (string) $entry['key'],
                ));
            })
            ->values();

        $ballastEntries = $langEntries
            ->reject(function (array $entry) use ($exportableEntries): bool {
                return $exportableEntries->has($this->exportableEntryKey(
                    locale: (string) $entry['locale'],
                    key: (string) $entry['key'],
                ));
            })
            ->map(fn(array $entry): array => $this->enrichBallastEntry($entry, $knownDbEntries))
            ->values();

        $missingFromLangEntries = $exportableEntries
            ->reject(static fn(array $entry, string $entryKey): bool => $langEntryIndex->has($entryKey))
            ->values();

        $ballastEntries = $ballastEntries
            ->map(fn(array $entry): array => $this->withCandidateIdentity($entry))
            ->values();

        $missingFromLangEntries = $missingFromLangEntries
            ->map(fn(array $entry): array => $this->withCandidateIdentity($entry))
            ->values();

        $decisionIndex = $this->collectDecisionIndex(
            ballastEntries: $ballastEntries,
            missingFromLangEntries: $missingFromLangEntries,
        );

        $ballastEntries = $ballastEntries
            ->map(fn(array $entry): array => $this->enrichDecisionState($entry, $decisionIndex))
            ->values();

        $missingFromLangEntries = $missingFromLangEntries
            ->map(fn(array $entry): array => $this->enrichDecisionState($entry, $decisionIndex))
            ->values();

        $subLanguageBaseDuplicateEntries = $this->collectSubLanguageBaseDuplicateEntries();

        $decisionSummary = $this->buildDecisionSummary(
            ballastEntries: $ballastEntries,
            missingFromLangEntries: $missingFromLangEntries,
        );

        $actionEntries = $this->buildActionEntries(
            ballastEntries: $ballastEntries,
            missingFromLangEntries: $missingFromLangEntries,
        );

        $actionFileEntries = $this->buildActionFileEntries($actionEntries);

        $reconciliationSummary = $this->buildReconciliationSummary(
            langEntries: $langEntries,
            exportableEntries: $exportableEntries,
            matchedEntries: $matchedEntries,
            ballastEntries: $ballastEntries,
            missingFromLangEntries: $missingFromLangEntries,
        );

        $databaseSummary = $this->buildDatabaseSummary(
            knownDbEntries: $knownDbEntries,
            exportableEntries: $exportableEntries,
        );

        $localeReconciliation = $this->buildReconciliationByLocale(
            langEntries: $langEntries,
            exportableEntries: $exportableEntries,
            matchedEntries: $matchedEntries,
            ballastEntries: $ballastEntries,
            missingFromLangEntries: $missingFromLangEntries,
        );

        $payload = [
            'generated_at' => now()->toISOString(),
            'source' => 'lang/*',
            'mode' => 'audit',
            'summary' => [
                'lang_entries' => $langEntries->count(),
                'exportable_entries' => $exportableEntries->count(),
                'matched_entries' => $matchedEntries->count(),
                'ballast_entries' => $ballastEntries->count(),
                'ballast_not_found_in_db_entries' => $ballastEntries
                    ->where('reason_detail', 'no_matching_db_translation_value')
                    ->count(),
                'ballast_known_not_exportable_entries' => $ballastEntries
                    ->where('db_state', 'known_not_exportable')
                    ->count(),
                'ballast_reviewed_obsolete_entries' => $ballastEntries
                    ->where('reason_detail', 'reviewed_obsolete_key')
                    ->count(),
                'ballast_value_status_not_ok_entries' => $ballastEntries
                    ->where('reason_detail', 'translation_value_status_not_ok')
                    ->count(),
                'ballast_empty_value_entries' => $ballastEntries
                    ->where('reason_detail', 'empty_translation_value')
                    ->count(),
                'sub_language_base_duplicate_entries' => $subLanguageBaseDuplicateEntries->count(),
                'missing_from_lang_export_required_entries' => $missingFromLangEntries->count(),
                'lang_file_cleanup_candidate_entries' => $ballastEntries
                    ->where('lang_file_action_candidate', 'remove')
                    ->count(),
                'lang_file_remove_candidate_entries' => $ballastEntries
                    ->where('lang_file_action_candidate', 'remove')
                    ->count(),
                'lang_file_review_candidate_entries' => $ballastEntries
                    ->where('lang_file_action_candidate', 'review')
                    ->count(),
                'missing_from_lang_entries' => $missingFromLangEntries->count(),
                'lang_file_add_candidate_entries' => $missingFromLangEntries
                    ->where('lang_file_action_candidate', 'add')
                    ->count(),
                'action_file_remove_candidate_files' => count($actionFileEntries['remove_from_lang'] ?? []),
                'action_file_add_candidate_files' => count($actionFileEntries['add_to_lang'] ?? []),
                'action_file_review_candidate_files' => count($actionFileEntries['review'] ?? []),
                'net_file_surplus_entries' => $reconciliationSummary['net_file_surplus_entries'],
                'decision_open_entries' => $decisionSummary['open_entries'],
                'decision_reviewed_entries' => $decisionSummary['reviewed_entries'],
                'decision_approved_entries' => $decisionSummary['approved_entries'],
                'decision_ignored_entries' => $decisionSummary['ignored_entries'],
                'decision_with_existing_decision_entries' => $decisionSummary['with_decision_entries'],
                'decision_without_existing_decision_entries' => $decisionSummary['without_decision_entries'],
                'preview_limit' => self::PREVIEW_LIMIT,
                'decisions' => $decisionSummary,
                'reconciliation' => $reconciliationSummary,
                'database' => $databaseSummary,
                'by_locale' => $localeReconciliation,
            ],
            'items' => [
                'ballast' => $ballastEntries->all(),
                'missing_from_lang' => $missingFromLangEntries->all(),
                'sub_language_base_duplicates' => $subLanguageBaseDuplicateEntries->all(),
            ],
            'actions' => $actionEntries,
            'action_files' => $actionFileEntries,
        ];

        $this->writeAuditFiles($payload);

        $this->info('Lang ballast audit finished');
        $this->line('Lang entries: ' . $langEntries->count());
        $this->line('Exportable entries: ' . $exportableEntries->count());
        $this->line('Matched entries: ' . $matchedEntries->count());
        $this->line('Ballast entries: ' . $ballastEntries->count());
        $this->line('Missing from lang entries: ' . $missingFromLangEntries->count());
        $this->line('Net file surplus entries: ' . $reconciliationSummary['net_file_surplus_entries']);
        $this->line('Decision open entries: ' . $decisionSummary['open_entries']);
        $this->line('Decision approved entries: ' . $decisionSummary['approved_entries']);

        return self::SUCCESS;
    }

    /**
     * Collect flattened entries from existing lang/* PHP and JSON files.
     *
     * Terminology used by this audit:
     * - namespace: the lang file name without extension, e.g. ui for lang/de/ui.php;
     * - group: the first key level inside the namespace, e.g. actions for ui.actions.save;
     * - key: the DB/application translation key, e.g. ui.actions.save;
     * - suggested_key: the original generated key proposal stored in the DB, when available;
     * - file_key: the relative key inside the concrete lang file, e.g. actions.save.
     *
     * Deactivated locales are intentionally still scanned. Locale activation is not a cleanup
     * criterion; the audit only reports differences between lang files and the DB/export state.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function collectLangEntries(): Collection
    {
        $entries = collect();

        if (! File::isDirectory(lang_path())) {
            return $entries;
        }

        foreach (File::directories(lang_path()) as $localeDirectory) {
            $locale = $this->normalizeLocale(basename($localeDirectory));

            if ($locale === '') {
                continue;
            }

            foreach (File::files($localeDirectory) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $path = $file->getRealPath();

                if (! is_string($path) || $this->isParkedLangFile($path)) {
                    continue;
                }

                $namespace = pathinfo($path, PATHINFO_FILENAME);
                $payload = require $path;

                if (! is_array($payload)) {
                    continue;
                }

                foreach ($this->flattenLangPayload($payload) as $fileKey => $value) {
                    $key = $namespace . '.' . (string) $fileKey;

                    $entries->push([
                        'type' => 'file_obsolete',
                        'locale' => $locale,
                        'namespace' => $namespace,
                        'group' => $this->groupFromKey($key, $namespace),
                        'key' => $key,
                        'suggested_key' => null,
                        'file_key' => (string) $fileKey,
                        'value' => is_scalar($value) ? (string) $value : null,
                        'file' => $this->relativePath($path),
                        'reason' => 'lang_entry_not_exportable_from_current_database_state',
                    ]);
                }
            }
        }

        foreach (File::files(lang_path()) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $path = $file->getRealPath();

            if (! is_string($path) || $this->isParkedLangFile($path)) {
                continue;
            }

            $locale = $this->normalizeLocale(pathinfo($path, PATHINFO_FILENAME));

            if ($locale === '') {
                continue;
            }

            $payload = json_decode((string) File::get($path), true);

            if (! is_array($payload)) {
                continue;
            }

            foreach ($payload as $key => $value) {
                $entries->push([
                    'type' => 'json_file_obsolete',
                    'locale' => $locale,
                    'namespace' => '*',
                    'group' => '*',
                    'key' => (string) $key,
                    'suggested_key' => null,
                    'file_key' => (string) $key,
                    'value' => is_scalar($value) ? (string) $value : null,
                    'file' => $this->relativePath($path),
                    'reason' => 'json_lang_entry_not_exportable_from_current_database_state',
                ]);
            }
        }

        return $entries
            ->sortBy([
                ['locale', 'asc'],
                ['group', 'asc'],
                ['key', 'asc'],
            ])
            ->values();
    }

    /**
     * Build the set of entries that would currently be exported from the DB.
     *
     * This intentionally mirrors the conservative export semantics: only real keys with non-empty,
     * ok translation values are considered exportable. Reviewed obsolete keys are excluded.
     *
     * The returned collection is keyed by locale + DB/application key so file ballast and DB-only
     * entries can be compared without losing the project terminology around key/suggested_key.
     *
     * @return Collection<string, array<string, mixed>>
     */
    private function collectExportableEntries(): Collection
    {
        return DB::table('translation_values')
            ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
            ->whereNotNull('translation_keys.key')
            ->where('translation_keys.key', '!=', '')
            ->where('translation_values.status', 'ok')
            ->whereNotNull('translation_values.value')
            ->where('translation_values.value', '!=', '')
            ->where(function ($query): void {
                $query
                    ->whereNull('translation_values.is_base_duplicate')
                    ->orWhere('translation_values.is_base_duplicate', false);
            })
            ->where(function ($query): void {
                $query
                    ->where('translation_keys.status', '!=', 'obsolete')
                    ->orWhere('translation_keys.workflow_status', '!=', 'reviewed')
                    ->orWhereNull('translation_keys.workflow_status');
            })
            ->get([
                'translation_keys.id as translation_key_id',
                'translation_values.id as translation_value_id',
                'translation_values.locale',
                'translation_values.status as value_status',
                'translation_values.is_base_duplicate',
                'translation_keys.namespace',
                'translation_keys.group',
                'translation_keys.key',
                'translation_keys.suggested_key',
                'translation_keys.status as key_status',
                'translation_keys.workflow_status',
                'translation_values.value',
            ])
            ->mapWithKeys(function (object $row): array {
                $entry = $this->dbReportEntryFromRow(
                    row: $row,
                    type: 'missing_from_lang',
                    reason: 'db_exportable_entry_missing_from_lang_files',
                );

                return [
                    $this->exportableEntryKey(
                        locale: (string) $entry['locale'],
                        key: (string) $entry['key'],
                    ) => [
                        ...$entry,
                        'db_state' => 'exportable',
                        'should_be_in_lang' => true,
                        'reason_detail' => 'db_exportable_entry_missing_from_lang_file',
                        'lang_file_expected' => true,
                        'lang_file_action_candidate' => 'add',
                        'lang_file_action_reason' => 'db_exportable_entry_missing_from_lang_file',
                    ],
                ];
            });
    }

    /**
     * Build the set of all known DB entries, including entries that are not exportable.
     *
     * @return Collection<string, array<string, mixed>>
     */
    private function collectKnownDbEntries(): Collection
    {
        return DB::table('translation_values')
            ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
            ->whereNotNull('translation_keys.key')
            ->where('translation_keys.key', '!=', '')
            ->get([
                'translation_keys.id as translation_key_id',
                'translation_values.id as translation_value_id',
                'translation_values.locale',
                'translation_values.status as value_status',
                'translation_values.is_base_duplicate',
                'translation_keys.namespace',
                'translation_keys.group',
                'translation_keys.key',
                'translation_keys.suggested_key',
                'translation_keys.status as key_status',
                'translation_keys.workflow_status',
                'translation_values.value',
            ])
            ->mapWithKeys(function (object $row): array {
                $entry = $this->dbReportEntryFromRow(
                    row: $row,
                    type: 'known_db_entry',
                    reason: 'db_translation_value_known',
                );

                return [
                    $this->exportableEntryKey(
                        locale: (string) $entry['locale'],
                        key: (string) $entry['key'],
                    ) => $entry,
                ];
            });
    }

    /**
     * Build row-based sub-language base duplicates.
     *
     * These entries are intentionally not exportable and must not become add_to_lang candidates.
     * They remain visible so redundant sub-language values can be reviewed and cleaned up later.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function collectSubLanguageBaseDuplicateEntries(): Collection
    {
        return DB::table('translation_values')
            ->join('translation_keys', 'translation_keys.id', '=', 'translation_values.translation_key_id')
            ->whereNotNull('translation_keys.key')
            ->where('translation_keys.key', '!=', '')
            ->where('translation_values.status', 'ok')
            ->whereNotNull('translation_values.value')
            ->where('translation_values.value', '!=', '')
            ->where('translation_values.is_base_duplicate', true)
            ->get([
                'translation_keys.id as translation_key_id',
                'translation_values.id as translation_value_id',
                'translation_values.locale',
                'translation_values.status as value_status',
                'translation_values.is_base_duplicate',
                'translation_keys.namespace',
                'translation_keys.group',
                'translation_keys.key',
                'translation_keys.suggested_key',
                'translation_keys.status as key_status',
                'translation_keys.workflow_status',
                'translation_values.value',
            ])
            ->map(function (object $row): array {
                $entry = $this->dbReportEntryFromRow(
                    row: $row,
                    type: 'sub_language_base_duplicate',
                    reason: 'sub_language_value_matches_main_language_value',
                );

                return [
                    ...$entry,
                    'db_state' => 'known_not_exportable',
                    'should_be_in_lang' => false,
                    'reason_detail' => 'sub_language_base_duplicate',
                    'lang_file_expected' => false,
                    'lang_file_action_candidate' => 'review',
                    'lang_file_action_reason' => 'sub_language_base_duplicate',
                ];
            })
            ->sortBy([
                ['locale', 'asc'],
                ['namespace', 'asc'],
                ['group', 'asc'],
                ['key', 'asc'],
            ])
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function dbReportEntryFromRow(object $row, string $type, string $reason): array
    {
        $locale = $this->normalizeLocale((string) ($row->locale ?? ''));
        $key = trim((string) ($row->key ?? ''));
        $namespace = trim((string) ($row->namespace ?? '')) ?: $this->namespaceFromKey($key);
        $group = trim((string) ($row->group ?? '')) ?: $this->groupFromKey($key, $namespace);
        $fileKey = $this->fileKeyFromKey($key, $namespace);
        $value = is_scalar($row->value ?? null) ? (string) $row->value : null;

        $entry = [
            'type' => $type,
            'translation_key_id' => (int) ($row->translation_key_id ?? 0) > 0
                ? (int) ($row->translation_key_id ?? 0)
                : null,
            'translation_value_id' => (int) ($row->translation_value_id ?? 0) > 0
                ? (int) ($row->translation_value_id ?? 0)
                : null,
            'locale' => $locale,
            'namespace' => $namespace,
            'group' => $group,
            'key' => $key,
            'suggested_key' => trim((string) ($row->suggested_key ?? '')) ?: null,
            'file_key' => $fileKey,
            'value' => $value,
            'file' => $this->expectedLangFileFor($locale, $namespace),
            'key_status' => trim((string) ($row->key_status ?? '')) ?: null,
            'workflow_status' => trim((string) ($row->workflow_status ?? '')) ?: null,
            'value_status' => trim((string) ($row->value_status ?? '')) ?: null,
            'is_base_duplicate' => (bool) ($row->is_base_duplicate ?? false),
            'reason' => $reason,
        ];

        $entry['should_be_in_lang'] = $this->dbEntryShouldBeInLang($entry);
        $entry['db_state'] = $entry['should_be_in_lang'] ? 'exportable' : 'known_not_exportable';
        $entry['reason_detail'] = $entry['should_be_in_lang']
            ? 'db_entry_exportable'
            : $this->dbStateReasonDetail($entry);
        $entry['lang_file_expected'] = $entry['should_be_in_lang'];
        $entry['lang_file_action_candidate'] = $entry['should_be_in_lang'] ? 'keep' : 'remove';
        $entry['lang_file_action_reason'] = $entry['reason_detail'];

        return $entry;
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function dbEntryShouldBeInLang(array $entry): bool
    {
        return (string) ($entry['key'] ?? '') !== ''
            && (string) ($entry['value_status'] ?? '') === 'ok'
            && ($entry['value'] ?? null) !== null
            && (string) ($entry['value'] ?? '') !== ''
            && ! (bool) ($entry['is_base_duplicate'] ?? false)
            && ! (
                (string) ($entry['key_status'] ?? '') === 'obsolete'
                && (string) ($entry['workflow_status'] ?? '') === 'reviewed'
            );
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function dbStateReasonDetail(array $entry): string
    {
        if (
            (string) ($entry['key_status'] ?? '') === 'obsolete'
            && (string) ($entry['workflow_status'] ?? '') === 'reviewed'
        ) {
            return 'reviewed_obsolete_key';
        }

        if ((string) ($entry['value_status'] ?? '') !== 'ok') {
            return 'translation_value_status_not_ok';
        }

        if (($entry['value'] ?? null) === null || (string) ($entry['value'] ?? '') === '') {
            return 'empty_translation_value';
        }

        if ((bool) ($entry['is_base_duplicate'] ?? false)) {
            return 'sub_language_base_duplicate';
        }

        return 'known_db_entry_not_exportable';
    }

    /**
     * @param array<string, mixed> $entry
     * @param Collection<string, array<string, mixed>> $knownDbEntries
     *
     * @return array<string, mixed>
     */
    private function enrichBallastEntry(array $entry, Collection $knownDbEntries): array
    {
        $dbEntry = $knownDbEntries->get($this->exportableEntryKey(
            locale: (string) $entry['locale'],
            key: (string) $entry['key'],
        ));

        if (is_array($dbEntry)) {
            return [
                ...$entry,
                'translation_key_id' => $dbEntry['translation_key_id'] ?? null,
                'translation_value_id' => $dbEntry['translation_value_id'] ?? null,
                'suggested_key' => $entry['suggested_key'] ?? $dbEntry['suggested_key'] ?? null,
                'key_status' => $dbEntry['key_status'] ?? null,
                'workflow_status' => $dbEntry['workflow_status'] ?? null,
                'value_status' => $dbEntry['value_status'] ?? null,
                'db_state' => $dbEntry['db_state'] ?? 'known_not_exportable',
                'should_be_in_lang' => $dbEntry['should_be_in_lang'] ?? false,
                'reason_detail' => $dbEntry['reason_detail'] ?? 'known_db_entry_not_exportable',
                'lang_file_expected' => $dbEntry['lang_file_expected'] ?? false,
                'lang_file_action_candidate' => $dbEntry['lang_file_action_candidate'] ?? 'review',
                'lang_file_action_reason' => $dbEntry['lang_file_action_reason'] ?? 'known_db_entry_not_exportable',
            ];
        }

        return [
            ...$entry,
            'translation_key_id' => null,
            'translation_value_id' => null,
            'key_status' => null,
            'workflow_status' => null,
            'value_status' => null,
            'db_state' => 'not_found',
            'should_be_in_lang' => false,
            'reason_detail' => 'no_matching_db_translation_value',
            'lang_file_expected' => false,
            'lang_file_action_candidate' => 'review',
            'lang_file_action_reason' => 'no_matching_db_translation_value',
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $ballastEntries
     * @param Collection<int, array<string, mixed>> $missingFromLangEntries
     *
     * @return Collection<string, TranslationLangBallastDecision>
     */
    private function collectDecisionIndex(Collection $ballastEntries, Collection $missingFromLangEntries): Collection
    {
        $candidateHashes = $ballastEntries
            ->merge($missingFromLangEntries)
            ->pluck('candidate_hash')
            ->map(static fn(mixed $value): string => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($candidateHashes->isEmpty()) {
            return collect();
        }

        return TranslationLangBallastDecision::query()
            ->whereIn('candidate_hash', $candidateHashes->all())
            ->get()
            ->keyBy('candidate_hash');
    }

    /**
     * @param array<string, mixed> $entry
     *
     * @return array<string, mixed>
     */
    private function withCandidateIdentity(array $entry): array
    {
        $value = is_scalar($entry['value'] ?? null)
            ? (string) $entry['value']
            : '';
        $valueHash = hash('sha256', $value);
        $actionCandidate = $this->normalizedActionCandidate($entry);

        $candidateHash = hash('sha256', implode('|', [
            $this->normalizeLocale((string) ($entry['locale'] ?? '')),
            trim((string) ($entry['namespace'] ?? '')),
            trim((string) ($entry['group'] ?? '')),
            trim((string) ($entry['key'] ?? '')),
            trim((string) ($entry['file'] ?? '')),
            trim((string) ($entry['file_key'] ?? '')),
            $valueHash,
            $actionCandidate,
        ]));

        return [
            ...$entry,
            'action_candidate' => $actionCandidate,
            'value_hash' => $valueHash,
            'candidate_hash' => $candidateHash,
        ];
    }

    /**
     * @param array<string, mixed> $entry
     * @param Collection<string, TranslationLangBallastDecision> $decisionIndex
     *
     * @return array<string, mixed>
     */
    private function enrichDecisionState(array $entry, Collection $decisionIndex): array
    {
        $candidateHash = trim((string) ($entry['candidate_hash'] ?? ''));
        $decision = $candidateHash !== '' ? $decisionIndex->get($candidateHash) : null;

        if (! $decision instanceof TranslationLangBallastDecision) {
            return [
                ...$entry,
                'decision_exists' => false,
                'decision_id' => null,
                'decision_status' => TranslationLangBallastDecision::STATUS_OPEN,
                'decision_note' => null,
                'decision_reviewed_at' => null,
                'decision_reviewed_by_user_id' => null,
            ];
        }

        return [
            ...$entry,
            'decision_exists' => true,
            'decision_id' => $decision->id,
            'decision_status' => $decision->decision_status,
            'decision_note' => $decision->decision_note,
            'decision_reviewed_at' => $decision->reviewed_at?->toISOString(),
            'decision_reviewed_by_user_id' => $decision->reviewed_by_user_id,
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $ballastEntries
     * @param Collection<int, array<string, mixed>> $missingFromLangEntries
     *
     * @return array<string, int>
     */
    private function buildDecisionSummary(Collection $ballastEntries, Collection $missingFromLangEntries): array
    {
        $entries = $ballastEntries
            ->merge($missingFromLangEntries)
            ->values();

        return [
            'total_entries' => $entries->count(),
            'with_decision_entries' => $entries
                ->where('decision_exists', true)
                ->count(),
            'without_decision_entries' => $entries
                ->where('decision_exists', false)
                ->count(),
            'open_entries' => $entries
                ->where('decision_status', TranslationLangBallastDecision::STATUS_OPEN)
                ->count(),
            'reviewed_entries' => $entries
                ->where('decision_status', TranslationLangBallastDecision::STATUS_REVIEWED)
                ->count(),
            'approved_entries' => $entries
                ->where('decision_status', TranslationLangBallastDecision::STATUS_APPROVED)
                ->count(),
            'ignored_entries' => $entries
                ->where('decision_status', TranslationLangBallastDecision::STATUS_IGNORED)
                ->count(),
        ];
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function normalizedActionCandidate(array $entry): string
    {
        $action = trim((string) (
            $entry['action_candidate']
            ?? $entry['lang_file_action_candidate']
            ?? $entry['action']
            ?? 'review'
        ));

        return match ($action) {
            'remove', TranslationLangBallastDecision::ACTION_REMOVE_FROM_LANG => TranslationLangBallastDecision::ACTION_REMOVE_FROM_LANG,
            'add', TranslationLangBallastDecision::ACTION_ADD_TO_LANG => TranslationLangBallastDecision::ACTION_ADD_TO_LANG,
            default => TranslationLangBallastDecision::ACTION_REVIEW,
        };
    }

    /**
     * @param Collection<int, array<string, mixed>> $ballastEntries
     * @param Collection<int, array<string, mixed>> $missingFromLangEntries
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function buildActionEntries(Collection $ballastEntries, Collection $missingFromLangEntries): array
    {
        $removeFromLang = $ballastEntries
            ->where('lang_file_action_candidate', 'remove')
            ->map(fn(array $entry): array => $this->actionEntryFromAuditEntry($entry))
            ->values();

        $addToLang = $missingFromLangEntries
            ->where('lang_file_action_candidate', 'add')
            ->map(fn(array $entry): array => $this->actionEntryFromAuditEntry($entry))
            ->values();

        $review = $ballastEntries
            ->where('lang_file_action_candidate', 'review')
            ->merge($missingFromLangEntries->where('lang_file_action_candidate', 'review'))
            ->map(fn(array $entry): array => $this->actionEntryFromAuditEntry($entry))
            ->values();

        return [
            'remove_from_lang' => $removeFromLang->all(),
            'add_to_lang' => $addToLang->all(),
            'review' => $review->all(),
        ];
    }

    /**
     * @param array<string, mixed> $entry
     *
     * @return array<string, mixed>
     */
    private function actionEntryFromAuditEntry(array $entry): array
    {
        return [
            'action' => $entry['action_candidate'] ?? $entry['lang_file_action_candidate'] ?? 'review',
            'action_candidate' => $entry['action_candidate'] ?? null,
            'value_hash' => $entry['value_hash'] ?? null,
            'candidate_hash' => $entry['candidate_hash'] ?? null,
            'decision_exists' => $entry['decision_exists'] ?? false,
            'decision_id' => $entry['decision_id'] ?? null,
            'decision_status' => $entry['decision_status'] ?? TranslationLangBallastDecision::STATUS_OPEN,
            'decision_note' => $entry['decision_note'] ?? null,
            'decision_reviewed_at' => $entry['decision_reviewed_at'] ?? null,
            'decision_reviewed_by_user_id' => $entry['decision_reviewed_by_user_id'] ?? null,
            'translation_key_id' => $entry['translation_key_id'] ?? null,
            'translation_value_id' => $entry['translation_value_id'] ?? null,
            'locale' => $entry['locale'] ?? null,
            'file' => $entry['file'] ?? null,
            'namespace' => $entry['namespace'] ?? null,
            'group' => $entry['group'] ?? null,
            'key' => $entry['key'] ?? null,
            'suggested_key' => $entry['suggested_key'] ?? null,
            'file_key' => $entry['file_key'] ?? null,
            'value' => $entry['value'] ?? null,
            'db_state' => $entry['db_state'] ?? null,
            'key_status' => $entry['key_status'] ?? null,
            'workflow_status' => $entry['workflow_status'] ?? null,
            'value_status' => $entry['value_status'] ?? null,
            'is_base_duplicate' => $entry['is_base_duplicate'] ?? null,
            'should_be_in_lang' => $entry['should_be_in_lang'] ?? null,
            'lang_file_expected' => $entry['lang_file_expected'] ?? null,
            'lang_file_action_reason' => $entry['lang_file_action_reason'] ?? null,
            'reason_detail' => $entry['reason_detail'] ?? null,
        ];
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $actionEntries
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function buildActionFileEntries(array $actionEntries): array
    {
        return [
            'remove_from_lang' => $this->actionFileEntriesFromActions($actionEntries['remove_from_lang'] ?? []),
            'add_to_lang' => $this->actionFileEntriesFromActions($actionEntries['add_to_lang'] ?? []),
            'review' => $this->actionFileEntriesFromActions($actionEntries['review'] ?? []),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $actions
     *
     * @return array<int, array<string, mixed>>
     */
    private function actionFileEntriesFromActions(array $actions): array
    {
        return collect($actions)
            ->groupBy(fn(array $entry): string => trim((string) ($entry['file'] ?? '')) ?: 'unknown')
            ->map(function (Collection $entries, string $file): array {
                return [
                    'file' => $file,
                    'entries' => $entries->count(),
                    'locales' => $entries
                        ->pluck('locale')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'namespaces' => $entries
                        ->pluck('namespace')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'groups' => $entries
                        ->pluck('group')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'translation_key_ids' => $entries
                        ->pluck('translation_key_id')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'translation_value_ids' => $entries
                        ->pluck('translation_value_id')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                    'actions' => $entries
                        ->pluck('action')
                        ->filter()
                        ->countBy()
                        ->sortKeys()
                        ->all(),
                    'decision_statuses' => $entries
                        ->pluck('decision_status')
                        ->filter()
                        ->countBy()
                        ->sortKeys()
                        ->all(),
                    'reason_details' => $entries
                        ->pluck('reason_detail')
                        ->filter()
                        ->countBy()
                        ->sortKeys()
                        ->all(),
                    'keys_sample' => $entries
                        ->pluck('key')
                        ->filter()
                        ->unique()
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('file')
            ->values()
            ->all();
    }

    /**
     * @param Collection<int, array<string, mixed>> $langEntries
     * @param Collection<string, array<string, mixed>> $exportableEntries
     * @param Collection<int, array<string, mixed>> $matchedEntries
     * @param Collection<int, array<string, mixed>> $ballastEntries
     * @param Collection<int, array<string, mixed>> $missingFromLangEntries
     *
     * @return array<string, int>
     */
    private function buildReconciliationSummary(
        Collection $langEntries,
        Collection $exportableEntries,
        Collection $matchedEntries,
        Collection $ballastEntries,
        Collection $missingFromLangEntries,
    ): array {
        return [
            'lang_entries' => $langEntries->count(),
            'db_exportable_entries' => $exportableEntries->count(),
            'matched_entries' => $matchedEntries->count(),
            'file_only_entries' => $ballastEntries->count(),
            'db_only_entries' => $missingFromLangEntries->count(),
            'lang_entries_minus_db_exportable_entries' => $langEntries->count() - $exportableEntries->count(),
            'net_file_surplus_entries' => $ballastEntries->count() - $missingFromLangEntries->count(),
        ];
    }

    /**
     * @param Collection<string, array<string, mixed>> $knownDbEntries
     * @param Collection<string, array<string, mixed>> $exportableEntries
     *
     * @return array<string, mixed>
     */
    private function buildDatabaseSummary(Collection $knownDbEntries, Collection $exportableEntries): array
    {
        $knownDbEntryValues = $knownDbEntries->values();

        return [
            'translation_keys_total' => DB::table('translation_keys')->count(),
            'translation_keys_by_status' => $this->countDatabaseColumnValues('translation_keys', 'status'),
            'translation_keys_by_workflow_status' => $this->countDatabaseColumnValues('translation_keys', 'workflow_status'),
            'translation_values_total' => DB::table('translation_values')->count(),
            'translation_values_by_status' => $this->countDatabaseColumnValues('translation_values', 'status'),
            'known_locale_key_entries' => $knownDbEntries->count(),
            'exportable_locale_key_entries' => $exportableEntries->count(),
            'known_not_exportable_locale_key_entries' => $knownDbEntryValues
                ->where('db_state', 'known_not_exportable')
                ->count(),
            'reviewed_obsolete_locale_key_entries' => $knownDbEntryValues
                ->where('reason_detail', 'reviewed_obsolete_key')
                ->count(),
            'value_status_not_ok_locale_key_entries' => $knownDbEntryValues
                ->where('reason_detail', 'translation_value_status_not_ok')
                ->count(),
            'empty_value_locale_key_entries' => $knownDbEntryValues
                ->where('reason_detail', 'empty_translation_value')
                ->count(),
            'sub_language_base_duplicate_locale_key_entries' => $this->collectSubLanguageBaseDuplicateEntries()->count(),
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $langEntries
     * @param Collection<string, array<string, mixed>> $exportableEntries
     * @param Collection<int, array<string, mixed>> $matchedEntries
     * @param Collection<int, array<string, mixed>> $ballastEntries
     * @param Collection<int, array<string, mixed>> $missingFromLangEntries
     *
     * @return array<string, array<string, int>>
     */
    private function buildReconciliationByLocale(
        Collection $langEntries,
        Collection $exportableEntries,
        Collection $matchedEntries,
        Collection $ballastEntries,
        Collection $missingFromLangEntries,
    ): array {
        $locales = collect([
            ...$langEntries->pluck('locale')->all(),
            ...$exportableEntries->values()->pluck('locale')->all(),
        ])
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return $locales
            ->mapWithKeys(function (string $locale) use (
                $langEntries,
                $exportableEntries,
                $matchedEntries,
                $ballastEntries,
                $missingFromLangEntries,
            ): array {
                $langLocaleEntries = $langEntries->where('locale', $locale);
                $exportableLocaleEntries = $exportableEntries
                    ->values()
                    ->where('locale', $locale);
                $matchedLocaleEntries = $matchedEntries->where('locale', $locale);
                $ballastLocaleEntries = $ballastEntries->where('locale', $locale);
                $missingLocaleEntries = $missingFromLangEntries->where('locale', $locale);

                return [
                    $locale => [
                        'lang_entries' => $langLocaleEntries->count(),
                        'db_exportable_entries' => $exportableLocaleEntries->count(),
                        'matched_entries' => $matchedLocaleEntries->count(),
                        'file_only_entries' => $ballastLocaleEntries->count(),
                        'db_only_entries' => $missingLocaleEntries->count(),
                        'net_file_surplus_entries' => $ballastLocaleEntries->count() - $missingLocaleEntries->count(),
                    ],
                ];
            })
            ->all();
    }

    /**
     * @return array<string, int>
     */
    private function countDatabaseColumnValues(string $table, string $column): array
    {
        return DB::table($table)
            ->select($column, DB::raw('COUNT(*) as aggregate'))
            ->groupBy($column)
            ->orderBy($column)
            ->get()
            ->mapWithKeys(function (object $row) use ($column): array {
                $value = trim((string) ($row->{$column} ?? ''));

                return [
                    $value !== '' ? $value : 'null' => (int) ($row->aggregate ?? 0),
                ];
            })
            ->all();
    }

    private function exportableEntryKey(string $locale, string $key): string
    {
        return $this->normalizeLocale($locale) . '|' . trim($key);
    }

    private function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', trim($locale)));
    }

    private function namespaceFromKey(string $key): string
    {
        $segments = explode('.', trim($key));

        return trim((string) ($segments[0] ?? ''));
    }

    private function groupFromKey(string $key, string $namespace): ?string
    {
        $segments = explode('.', trim($key));
        $namespace = trim($namespace);

        if ($namespace !== '' && ($segments[0] ?? null) === $namespace) {
            return isset($segments[1]) && trim((string) $segments[1]) !== ''
                ? trim((string) $segments[1])
                : null;
        }

        return isset($segments[0]) && trim((string) $segments[0]) !== ''
            ? trim((string) $segments[0])
            : null;
    }

    private function fileKeyFromKey(string $key, string $namespace): string
    {
        $key = trim($key);
        $namespace = trim($namespace);

        if ($namespace !== '' && str_starts_with($key, $namespace . '.')) {
            return substr($key, strlen($namespace) + 1);
        }

        return $key;
    }

    private function expectedLangFileFor(string $locale, string $namespace): string
    {
        $locale = $this->normalizeLocale($locale);
        $namespace = trim($namespace);

        if ($locale === '') {
            return 'lang/*';
        }

        if ($namespace === '' || $namespace === '*') {
            return 'lang/' . $locale . '.json';
        }

        return 'lang/' . $locale . '/' . $namespace . '.php';
    }

    /**
     * @return array<string, mixed>
     */
    private function flattenLangPayload(array $items, string $prefix = ''): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            $segment = (string) $key;
            $fullKey = $prefix !== '' ? $prefix . '.' . $segment : $segment;

            if (is_array($value)) {
                $result += $this->flattenLangPayload($value, $fullKey);

                continue;
            }

            $result[$fullKey] = $value;
        }

        return $result;
    }

    private function isParkedLangFile(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return str_contains($filename, 'xxx')
            || str_contains($filename, 'yyy')
            || str_contains($filename, 'zzz');
    }

    private function relativePath(string $path): string
    {
        $basePath = base_path();

        return str_starts_with($path, $basePath . DIRECTORY_SEPARATOR)
            ? substr($path, strlen($basePath) + 1)
            : $path;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function writeAuditFiles(array $payload): void
    {
        $directory = storage_path(self::OUTPUT_DIR);

        File::ensureDirectoryExists($directory);

        $summary = $payload['summary'] ?? [];

        File::put(
            $directory . '/summary.json',
            json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        );

        File::put(
            $directory . '/full.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        );

        $items = is_array($payload['items'] ?? null)
            ? $payload['items']
            : [];

        $actions = is_array($payload['actions'] ?? null)
            ? $payload['actions']
            : [];

        $actionFiles = is_array($payload['action_files'] ?? null)
            ? $payload['action_files']
            : [];

        File::put(
            $directory . '/preview.json',
            json_encode([
                ...$payload,
                'items' => [
                    'ballast' => collect($items['ballast'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'missing_from_lang' => collect($items['missing_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'sub_language_base_duplicates' => collect($items['sub_language_base_duplicates'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
                'actions' => [
                    'remove_from_lang' => collect($actions['remove_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'add_to_lang' => collect($actions['add_to_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'review' => collect($actions['review'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
                'action_files' => [
                    'remove_from_lang' => collect($actionFiles['remove_from_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'add_to_lang' => collect($actionFiles['add_to_lang'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                    'review' => collect($actionFiles['review'] ?? [])
                        ->take(self::PREVIEW_LIMIT)
                        ->values()
                        ->all(),
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        );
    }
}
