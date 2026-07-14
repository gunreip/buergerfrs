<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKeyFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchSourceFile;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Gunreip\TranslationWorkbench\Scanner\TranslationKeyPartsFactory;
use Gunreip\TranslationWorkbench\Support\SourcePathSegmentFactory;
use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TranslationWorkbenchFoundationSyncer
{
    public function __construct(
        private readonly TranslationFingerprintFactory $fingerprintFactory,
        private readonly TranslationKeyPartsFactory $keyPartsFactory,
        private readonly TranslationKeySegmentFactory $keySegmentFactory,
        private readonly SourcePathSegmentFactory $sourcePathSegmentFactory,
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {}

    /**
     * @param  Collection<int, DiscoveredTranslation>  $items
     * @return array<string, int>
     */
    public function sync(Collection $items, bool $truncate = false): array
    {
        $summary = [
            'found' => $items->count(),
            'truncated' => 0,
            'source_files_created' => 0,
            'source_files_updated' => 0,
            'findings_created' => 0,
            'findings_updated' => 0,
            'keys_created' => 0,
            'keys_updated' => 0,
            'relations_created' => 0,
            'relations_updated' => 0,
            'timeline_events_created' => 0,
        ];
        $now = now();

        DB::transaction(function () use ($items, $truncate, $now, &$summary): void {
            $sourceFiles = [];

            if ($truncate) {
                $summary['truncated'] = $this->truncateFoundationTables();
            }

            foreach ($items as $item) {
                if (! array_key_exists($item->sourcePath, $sourceFiles)) {
                    $sourceFileResult = $this->syncSourceFile($item, $now);
                    $sourceFiles[$item->sourcePath] = $sourceFileResult['source_file'];
                    $summary[$sourceFileResult['result']]++;
                    $summary['timeline_events_created'] += $sourceFileResult['timeline_events_created'];
                }

                $findingResult = $this->syncFinding($item, $sourceFiles[$item->sourcePath], $now);
                $summary[$findingResult['result']]++;
                $summary['timeline_events_created'] += $findingResult['timeline_events_created'];

                $keyResult = $this->syncKey($item, $now);

                if ($keyResult !== null) {
                    $summary[$keyResult['result']]++;
                    $summary['timeline_events_created'] += $keyResult['timeline_events_created'];

                    $relationResult = $this->syncKeyFinding(
                        $keyResult['key'],
                        $findingResult['finding'],
                    );
                    $summary[$relationResult['result']]++;
                    $summary['timeline_events_created'] += $relationResult['timeline_events_created'];
                }
            }
        });

        return $summary;
    }

    /**
     * @return array{result: string, source_file: TranslationWorkbenchSourceFile, timeline_events_created: int}
     */
    private function syncSourceFile(DiscoveredTranslation $item, mixed $now): array
    {
        $sourceFile = TranslationWorkbenchSourceFile::query()
            ->where('path', $item->sourcePath)
            ->first();

        $attributes = [
            ...$this->sourcePathSegmentFactory->fromPath($item->sourcePath),
            'source_type' => $item->sourceType,
            'extension' => pathinfo($item->sourcePath, PATHINFO_EXTENSION) ?: null,
            'status' => 'active',
            'last_seen_at' => $now,
        ];

        if (! $sourceFile) {
            $sourceFile = TranslationWorkbenchSourceFile::query()->create([
                ...$attributes,
                'path' => $item->sourcePath,
                'first_seen_at' => $now,
                'scan_count' => 1,
            ]);

            $this->timelineRecorder->record(
                eventType: 'source_file_discovered',
                newValues: $sourceFile->only([
                    'id',
                    'path',
                    'source_root',
                    'source_area',
                    'package_vendor',
                    'package_name',
                    'path_domain',
                    'path_section',
                    'path_context',
                    'path_scope',
                    'path_extra',
                    'filename',
                    'source_type',
                    'extension',
                    'status',
                    'first_seen_at',
                ]),
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'source_file_id' => $sourceFile->id,
                    'source_path' => $sourceFile->path,
                ],
            );

            return [
                'result' => 'source_files_created',
                'source_file' => $sourceFile,
                'timeline_events_created' => 1,
            ];
        }

        $oldValues = $sourceFile->only(array_keys($attributes));
        $changed = $this->changedValues($oldValues, $attributes, ['last_seen_at']);

        $sourceFile->forceFill([
            ...$attributes,
            'scan_count' => ((int) $sourceFile->scan_count) + 1,
        ])->save();

        if ($changed !== []) {
            $this->timelineRecorder->record(
                eventType: 'source_file_changed',
                oldValues: $this->onlyKeys($oldValues, array_keys($changed)),
                newValues: $changed,
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'source_file_id' => $sourceFile->id,
                    'source_path' => $sourceFile->path,
                    'changed_fields' => array_keys($changed),
                ],
            );
        }

        return [
            'result' => 'source_files_updated',
            'source_file' => $sourceFile,
            'timeline_events_created' => $changed !== [] ? 1 : 0,
        ];
    }

    /**
     * @return array{result: string, finding: TranslationWorkbenchFinding, timeline_events_created: int}
     */
    private function syncFinding(
        DiscoveredTranslation $item,
        TranslationWorkbenchSourceFile $sourceFile,
        mixed $now,
    ): array {
        $keyParts = $this->keyPartsFactory->fromKey($item->suggestedKey);
        $attributes = [
            'source_file_id' => $sourceFile->id,
            'source_signature' => $item->sourceSignature,
            'source_fingerprint' => $item->sourceFingerprint,
            'expression_fingerprint' => $item->expressionFingerprint,
            'semantic_fingerprint' => $item->semanticFingerprint,
            'source_line' => $item->sourceLine,
            'kind' => $item->kind,
            'function_name' => $item->functionName,
            'raw_expression' => $item->rawExpression,
            'literal_text' => $item->literalText,
            'literal_text_suggested' => $item->literalTextSuggested,
            'found_translation_key' => $item->translationKey,
            'existing_key' => $item->existingKey,
            'suggested_key' => $item->suggestedKey,
            'namespace' => $keyParts['namespace'],
            'group' => $keyParts['group'],
            'path_key' => $keyParts['path_key'],
            'scope' => $keyParts['scope'],
            'dynamic_scope' => $this->dynamicScope($item),
            'entry_type' => $item->entryType,
            'candidate_type' => $item->candidateType,
            'candidate_reason' => $item->candidateReason,
            'status' => 'active',
            'last_seen_at' => $now,
            'meta' => $item->meta,
        ];

        $finding = TranslationWorkbenchFinding::query()
            ->where('fingerprint', $item->fingerprint)
            ->first();

        if (! $finding) {
            $finding = TranslationWorkbenchFinding::query()->create([
                ...$attributes,
                'fingerprint' => $item->fingerprint,
                'first_seen_at' => $now,
                'scan_count' => 1,
            ]);

            $this->timelineRecorder->recordFindingEvent(
                finding: $finding,
                eventType: 'finding_discovered',
                newValues: $finding->only([
                    'id',
                    'source_file_id',
                    'fingerprint',
                    'source_signature',
                    'source_line',
                    'kind',
                    'function_name',
                    'literal_text',
                    'literal_text_suggested',
                    'found_translation_key',
                    'existing_key',
                    'suggested_key',
                    'namespace',
                    'group',
                    'path_key',
                    'scope',
                    'dynamic_scope',
                    'entry_type',
                    'candidate_type',
                    'candidate_reason',
                    'status',
                ]),
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'source_path' => $item->sourcePath,
                ],
            );

            return [
                'result' => 'findings_created',
                'finding' => $finding,
                'timeline_events_created' => 1,
            ];
        }

        $oldValues = $finding->only(array_keys($attributes));
        $changed = $this->changedValues($oldValues, $attributes, ['last_seen_at']);

        $finding->forceFill([
            ...$attributes,
            'scan_count' => ((int) $finding->scan_count) + 1,
        ])->save();

        if ($changed !== []) {
            $this->timelineRecorder->recordFindingEvent(
                finding: $finding,
                eventType: 'finding_changed',
                oldValues: $this->onlyKeys($oldValues, array_keys($changed)),
                newValues: $changed,
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'source_path' => $item->sourcePath,
                    'changed_fields' => array_keys($changed),
                ],
            );
        }

        return [
            'result' => 'findings_updated',
            'finding' => $finding,
            'timeline_events_created' => $changed !== [] ? 1 : 0,
        ];
    }

    /**
     * @return array{result: string, key: TranslationWorkbenchKey, timeline_events_created: int}|null
     */
    private function syncKey(DiscoveredTranslation $item, mixed $now): ?array
    {
        $keyCandidate = $item->translationKey ?: $item->suggestedKey;

        if ($keyCandidate === null || $keyCandidate === '') {
            return null;
        }

        $keyParts = $this->keyPartsFactory->fromKey($item->suggestedKey);
        $keySegments = $this->keySegmentFactory->fromKey($item->suggestedKey);
        $fingerprint = $this->fingerprintFactory->signature([
            'foundation-key',
            $item->translationKey ?: '',
            $item->suggestedKey ?: '',
        ]);
        $attributes = [
            'translation_key' => $item->translationKey,
            'suggested_key' => $item->suggestedKey,
            'namespace' => $keyParts['namespace'],
            'group' => $keyParts['group'],
            'path_key' => $keyParts['path_key'],
            'scope' => $keyParts['scope'],
            ...$keySegments,
            'key_type' => str_starts_with($item->kind, 'dynamic') ? 'dynamic_candidate' : 'static_candidate',
            'meta' => [
                'source' => 'foundation_sync',
                'candidate_type' => $item->candidateType,
                'candidate_reason' => $item->candidateReason,
            ],
        ];

        $key = TranslationWorkbenchKey::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        if (! $key) {
            $key = TranslationWorkbenchKey::query()->create([
                ...$attributes,
                'fingerprint' => $fingerprint,
                'status' => 'open',
                'review_status' => 'pending',
            ]);

            $this->timelineRecorder->recordKeyEvent(
                key: $key,
                eventType: 'key_candidate_discovered',
                newValues: $key->only([
                    'id',
                    'fingerprint',
                    'translation_key',
                    'suggested_key',
                    'namespace',
                    'group',
                    'path_key',
                    'scope',
                    'key_segment_domain',
                    'key_segment_section',
                    'key_segment_context',
                    'key_segment_extra',
                    'key_segment_name',
                    'key_type',
                    'status',
                    'review_status',
                ]),
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'source_path' => $item->sourcePath,
                    'source_line' => $item->sourceLine,
                ],
            );

            return [
                'result' => 'keys_created',
                'key' => $key,
                'timeline_events_created' => 1,
            ];
        }

        $oldValues = $key->only(array_keys($attributes));
        $changed = $this->changedValues($oldValues, $attributes);

        $key->forceFill($attributes)->save();

        if ($changed !== []) {
            $this->timelineRecorder->recordKeyEvent(
                key: $key,
                eventType: 'key_candidate_changed',
                oldValues: $this->onlyKeys($oldValues, array_keys($changed)),
                newValues: $changed,
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'source_path' => $item->sourcePath,
                    'source_line' => $item->sourceLine,
                    'changed_fields' => array_keys($changed),
                ],
            );
        }

        return [
            'result' => 'keys_updated',
            'key' => $key,
            'timeline_events_created' => $changed !== [] ? 1 : 0,
        ];
    }

    private function syncKeyFinding(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
    ): array {
        $relation = TranslationWorkbenchKeyFinding::query()
            ->where('key_id', $key->id)
            ->where('finding_id', $finding->id)
            ->where('relation_type', 'candidate')
            ->first();

        $attributes = [
            'status' => 'active',
            'meta' => [
                'source' => 'foundation_sync',
            ],
        ];

        if (! $relation) {
            $relation = TranslationWorkbenchKeyFinding::query()->create([
                ...$attributes,
                'key_id' => $key->id,
                'finding_id' => $finding->id,
                'relation_type' => 'candidate',
            ]);

            $this->timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_created',
                newValues: $relation->only([
                    'id',
                    'key_id',
                    'finding_id',
                    'relation_type',
                    'status',
                ]),
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                ],
            );

            return [
                'result' => 'relations_created',
                'timeline_events_created' => 1,
            ];
        }

        $oldValues = $relation->only(array_keys($attributes));
        $changed = $this->changedValues($oldValues, $attributes);

        $relation->forceFill($attributes)->save();

        if ($changed !== []) {
            $this->timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_changed',
                oldValues: $this->onlyKeys($oldValues, array_keys($changed)),
                newValues: $changed,
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'changed_fields' => array_keys($changed),
                ],
            );
        }

        return [
            'result' => 'relations_updated',
            'timeline_events_created' => $changed !== [] ? 1 : 0,
        ];
    }

    private function truncateFoundationTables(): int
    {
        $tables = [
            'translation_workbench_timeline_events',
            'translation_workbench_reviews',
            'translation_workbench_dynamic_key_values',
            'translation_workbench_key_values',
            'translation_workbench_key_findings',
            'translation_workbench_keys',
            'translation_workbench_findings',
            'translation_workbench_source_files',
        ];
        $rowCount = collect($tables)
            ->sum(static fn(string $table): int => (int) DB::table($table)->count());

        match (DB::getDriverName()) {
            'pgsql' => DB::statement('TRUNCATE TABLE translation_workbench_timeline_events, translation_workbench_reviews, translation_workbench_dynamic_key_values, translation_workbench_key_values, translation_workbench_key_findings, translation_workbench_keys, translation_workbench_findings, translation_workbench_source_files RESTART IDENTITY CASCADE'),
            'mysql', 'mariadb' => $this->truncateMysqlTables($tables),
            default => $this->deleteTables($tables),
        };

        return $rowCount;
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function truncateMysqlTables(array $tables): void
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
    private function deleteTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->delete();
        }
    }

    private function dynamicScope(DiscoveredTranslation $item): ?string
    {
        $scope = $item->meta['dynamic_scope'] ?? $item->meta['dynamic_option_context']['scope'] ?? null;

        return is_string($scope) && trim($scope) !== ''
            ? trim($scope)
            : null;
    }

    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     * @param  array<int, string>  $ignoredKeys
     * @return array<string, mixed>
     */
    private function changedValues(array $oldValues, array $newValues, array $ignoredKeys = []): array
    {
        return collect($newValues)
            ->reject(static fn(mixed $_value, string $key): bool => in_array($key, $ignoredKeys, true))
            ->filter(fn(mixed $value, string $key): bool => $this->normalizeComparable($oldValues[$key] ?? null) !== $this->normalizeComparable($value))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    private function onlyKeys(array $values, array $keys): array
    {
        return collect($keys)
            ->mapWithKeys(static fn(string $key): array => [$key => $values[$key] ?? null])
            ->all();
    }

    private function normalizeComparable(mixed $value): mixed
    {
        if (is_array($value)) {
            ksort($value);

            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }
}
