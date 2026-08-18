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
use Illuminate\Support\Facades\Schema;

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
    public function sync(Collection $items, bool $truncate = false, bool $markObsolete = false): array
    {
        $summary = [
            'found' => $items->count(),
            'truncated' => 0,
            'source_files_created' => 0,
            'source_files_updated' => 0,
            'findings_created' => 0,
            'findings_updated' => 0,
            'findings_commented_out' => 0,
            'findings_obsoleted' => 0,
            'keys_created' => 0,
            'keys_updated' => 0,
            'keys_obsoleted' => 0,
            'relations_created' => 0,
            'relations_updated' => 0,
            'relations_commented_out' => 0,
            'relations_obsoleted' => 0,
            'timeline_events_created' => 0,
        ];
        $now = now();

        DB::transaction(function () use ($items, $truncate, $markObsolete, $now, &$summary): void {
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
                $obsoletedFindings = $this->markSupersededFindingDuplicates($findingResult['finding'], $item, $now);
                $summary['findings_obsoleted'] += $obsoletedFindings;
                $summary['timeline_events_created'] += $obsoletedFindings;

                if ($this->isCommentedOutFinding($item)) {
                    $commentedOutCleanup = $this->commentOutFindingRelations($findingResult['finding']);
                    $summary['relations_commented_out'] += $commentedOutCleanup['relations_commented_out'];
                    $summary['timeline_events_created'] += $commentedOutCleanup['timeline_events_created'];
                    $summary['findings_commented_out']++;

                    continue;
                }

                if ($this->isEmptyLiteralFinding($item)) {
                    $emptyLiteralCleanup = $this->obsoleteEmptyLiteralRelations($findingResult['finding']);
                    $summary['relations_obsoleted'] += $emptyLiteralCleanup['relations_obsoleted'];
                    $summary['keys_obsoleted'] += $emptyLiteralCleanup['keys_obsoleted'];
                    $summary['timeline_events_created'] += $emptyLiteralCleanup['timeline_events_created'];

                    continue;
                }

                $keyResult = $this->syncKey($item, $now);

                if ($keyResult !== null) {
                    $summary[$keyResult['result']]++;
                    $summary['timeline_events_created'] += $keyResult['timeline_events_created'];

                    $relationResult = $this->syncKeyFinding(
                        $keyResult['key'],
                        $findingResult['finding'],
                    );
                    $summary[$relationResult['result']]++;
                    $summary['relations_obsoleted'] += $relationResult['relations_obsoleted'] ?? 0;
                    $summary['timeline_events_created'] += $relationResult['timeline_events_created'];
                }
            }

            if ($markObsolete && ! $truncate) {
                $obsoleteResult = $this->markMissingFoundationRowsObsolete($items, $now);
                $summary['findings_obsoleted'] += $obsoleteResult['findings_obsoleted'];
                $summary['relations_obsoleted'] += $obsoleteResult['relations_obsoleted'];
                $summary['keys_obsoleted'] += $obsoleteResult['keys_obsoleted'];
                $summary['timeline_events_created'] += $obsoleteResult['timeline_events_created'];
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
        $isEmptyLiteralFinding = $this->isEmptyLiteralFinding($item);
        $isCommentedOutFinding = $this->isCommentedOutFinding($item);
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
            'dynamic_data_state' => $this->dynamicDataState($item),
            'entry_type' => $item->entryType,
            'candidate_type' => $item->candidateType,
            'candidate_reason' => $item->candidateReason,
            'status' => match (true) {
                $isEmptyLiteralFinding => 'obsolete',
                $isCommentedOutFinding => 'commented_out',
                default => 'active',
            },
            'last_seen_at' => $now,
            'meta' => [
                ...$item->meta,
                ...($isEmptyLiteralFinding ? [
                    'ignored_reason' => 'empty_literal_translation_call',
                    'translation_relevant' => false,
                ] : []),
                ...($isCommentedOutFinding ? [
                    'commented_out_reason' => 'source_expression_inside_comment',
                    'translation_relevant' => false,
                ] : []),
            ],
        ];

        if (! $this->hasFindingDynamicDataStateColumn()) {
            unset($attributes['dynamic_data_state']);
        }

        $finding = TranslationWorkbenchFinding::query()
            ->where('source_signature', $item->sourceSignature)
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
                    'dynamic_data_state',
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

        if (
            array_key_exists('dynamic_data_state', $attributes)
            && $finding->dynamic_data_state === 'structured'
            && $attributes['dynamic_data_state'] !== 'structured'
        ) {
            $attributes['dynamic_data_state'] = 'structured';
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
        if ($this->isEmptyLiteralFinding($item)) {
            return null;
        }

        if ($item->entryType === 'dynamic_numeric' || $item->kind === 'dynamic_numeric') {
            return null;
        }

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
            'dynamic_data_state' => $this->dynamicDataState($item),
            'meta' => [
                'source' => 'foundation_sync',
                'candidate_type' => $item->candidateType,
                'candidate_reason' => $item->candidateReason,
            ],
        ];

        if (! $this->hasKeyDynamicDataStateColumn()) {
            unset($attributes['dynamic_data_state']);
        }

        $key = $this->reviewedKeyForTranslationKey($item->translationKey, $fingerprint)
            ?? TranslationWorkbenchKey::query()
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
                    'dynamic_data_state',
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

        if ((string) $key->fingerprint !== $fingerprint) {
            return [
                'result' => 'keys_updated',
                'key' => $key,
                'timeline_events_created' => 0,
            ];
        }

        if (
            array_key_exists('dynamic_data_state', $attributes)
            && $key->dynamic_data_state === 'structured'
            && $attributes['dynamic_data_state'] !== 'structured'
        ) {
            $attributes['dynamic_data_state'] = 'structured';
        }

        if (
            array_key_exists('dynamic_data_state', $attributes)
            && $attributes['dynamic_data_state'] === null
            && ((bool) $key->is_dynamic_key || (bool) $key->is_dynamic_multi)
        ) {
            $attributes['dynamic_data_state'] = 'unstructured';
        }

        if ($key->review_status === 'reviewed' && filled($key->translation_key)) {
            $reviewedTranslationKey = (string) $key->translation_key;

            $attributes = [
                ...$attributes,
                'translation_key' => $reviewedTranslationKey,
                'key_type' => $key->key_type,
                ...$this->keyPartsFactory->fromKey($reviewedTranslationKey),
                ...$this->keySegmentFactory->fromKey($reviewedTranslationKey),
            ];
        }

        if ($key->status === 'obsolete') {
            $attributes['status'] = 'open';
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

    private function isEmptyLiteralFinding(DiscoveredTranslation $item): bool
    {
        return $item->kind === 'literal'
            && $item->literalText !== null
            && trim($item->literalText) === '';
    }

    private function isCommentedOutFinding(DiscoveredTranslation $item): bool
    {
        return ($item->meta['source_context'] ?? null) === 'commented_out';
    }

    /**
     * @return array{relations_commented_out: int, timeline_events_created: int}
     */
    private function commentOutFindingRelations(TranslationWorkbenchFinding $finding): array
    {
        $result = [
            'relations_commented_out' => 0,
            'timeline_events_created' => 0,
        ];

        $relations = TranslationWorkbenchKeyFinding::query()
            ->with('key')
            ->where('finding_id', $finding->id)
            ->where('status', 'active')
            ->get();

        foreach ($relations as $relation) {
            $key = $relation->key;
            $oldRelationValues = $relation->only(['status', 'meta']);

            $relation->forceFill([
                'status' => 'commented_out',
                'meta' => [
                    ...($relation->meta ?? []),
                    'commented_out_reason' => 'source_expression_inside_comment',
                ],
            ])->save();

            if (! $key) {
                continue;
            }

            $this->timelineRecorder->recordKeyFindingEvent(
                key: $key,
                finding: $finding,
                eventType: 'key_finding_relation_commented_out',
                oldValues: $oldRelationValues,
                newValues: [
                    'status' => 'commented_out',
                    'commented_out_reason' => 'source_expression_inside_comment',
                ],
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'reason' => 'source_expression_inside_comment',
                ],
            );

            $result['relations_commented_out']++;
            $result['timeline_events_created']++;
        }

        return $result;
    }

    private function reviewedKeyForTranslationKey(?string $translationKey, string $fingerprint): ?TranslationWorkbenchKey
    {
        $translationKey = trim((string) $translationKey);

        if ($translationKey === '') {
            return null;
        }

        return TranslationWorkbenchKey::query()
            ->where('translation_key', $translationKey)
            ->where('status', 'open')
            ->where('review_status', 'reviewed')
            ->where('fingerprint', '!=', $fingerprint)
            ->orderByDesc('is_ui_key')
            ->orderBy('id')
            ->first();
    }

    /**
     * @return array{relations_obsoleted: int, keys_obsoleted: int, timeline_events_created: int}
     */
    private function obsoleteEmptyLiteralRelations(TranslationWorkbenchFinding $finding): array
    {
        $result = [
            'relations_obsoleted' => 0,
            'keys_obsoleted' => 0,
            'timeline_events_created' => 0,
        ];

        $relations = TranslationWorkbenchKeyFinding::query()
            ->with('key')
            ->where('finding_id', $finding->id)
            ->where('status', 'active')
            ->get();

        foreach ($relations as $relation) {
            $key = $relation->key;
            $oldRelationValues = $relation->only(['status', 'meta']);

            $relation->forceFill([
                'status' => 'obsolete',
                'meta' => [
                    ...($relation->meta ?? []),
                    'obsolete_reason' => 'empty_literal_translation_call',
                ],
            ])->save();

            if ($key) {
                $this->timelineRecorder->recordKeyFindingEvent(
                    key: $key,
                    finding: $finding,
                    eventType: 'key_finding_relation_obsoleted',
                    oldValues: $oldRelationValues,
                    newValues: [
                        'status' => 'obsolete',
                        'obsolete_reason' => 'empty_literal_translation_call',
                    ],
                    context: [
                        'source' => 'translation-workbench:sync-foundation',
                        'reason' => 'empty_literal_translation_call',
                    ],
                );

                $result['timeline_events_created']++;
                $result['relations_obsoleted']++;

                if ($this->keyHasNoActiveNonObsoleteFindings($key)) {
                    $oldKeyValues = $key->only(['status', 'meta']);

                    $key->forceFill([
                        'status' => 'obsolete',
                        'meta' => [
                            ...($key->meta ?? []),
                            'obsolete_reason' => 'empty_literal_translation_call',
                        ],
                    ])->save();

                    $this->timelineRecorder->recordKeyEvent(
                        key: $key,
                        eventType: 'key_candidate_obsoleted',
                        oldValues: $oldKeyValues,
                        newValues: [
                            'status' => 'obsolete',
                            'obsolete_reason' => 'empty_literal_translation_call',
                        ],
                        context: [
                            'source' => 'translation-workbench:sync-foundation',
                            'reason' => 'empty_literal_translation_call',
                            'finding_id' => $finding->id,
                        ],
                    );

                    $result['timeline_events_created']++;
                    $result['keys_obsoleted']++;
                }
            }
        }

        return $result;
    }

    private function keyHasNoActiveNonObsoleteFindings(TranslationWorkbenchKey $key): bool
    {
        return ! TranslationWorkbenchKeyFinding::query()
            ->join('translation_workbench_findings', 'translation_workbench_findings.id', '=', 'translation_workbench_key_findings.finding_id')
            ->where('translation_workbench_key_findings.key_id', $key->id)
            ->where('translation_workbench_key_findings.status', 'active')
            ->where('translation_workbench_findings.status', '!=', 'obsolete')
            ->exists();
    }

    private function markSupersededFindingDuplicates(
        TranslationWorkbenchFinding $finding,
        DiscoveredTranslation $item,
        mixed $now,
    ): int {
        if ($item->suggestedKey === null || $item->suggestedKey === '' || $item->rawExpression === null || $item->rawExpression === '') {
            return 0;
        }

        $duplicates = TranslationWorkbenchFinding::query()
            ->where('id', '!=', $finding->id)
            ->where('source_file_id', $finding->source_file_id)
            ->where('source_line', $item->sourceLine)
            ->where('raw_expression', $item->rawExpression)
            ->where('suggested_key', $item->suggestedKey)
            ->where('status', 'active')
            ->get();

        foreach ($duplicates as $duplicate) {
            $oldValues = $duplicate->only(['status']);

            $duplicate->forceFill([
                'status' => 'obsolete',
                'last_seen_at' => $now,
            ])->save();

            $this->timelineRecorder->recordFindingEvent(
                finding: $duplicate,
                eventType: 'finding_superseded',
                oldValues: $oldValues,
                newValues: [
                    'status' => 'obsolete',
                    'superseded_by_finding_id' => $finding->id,
                ],
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'reason' => 'same_source_expression_reclassified',
                    'source_path' => $item->sourcePath,
                    'source_line' => $item->sourceLine,
                    'suggested_key' => $item->suggestedKey,
                ],
            );
        }

        return $duplicates->count();
    }

    /**
     * @param  Collection<int, DiscoveredTranslation>  $items
     * @return array{findings_obsoleted: int, relations_obsoleted: int, keys_obsoleted: int, timeline_events_created: int}
     */
    private function markMissingFoundationRowsObsolete(Collection $items, mixed $now): array
    {
        $result = [
            'findings_obsoleted' => 0,
            'relations_obsoleted' => 0,
            'keys_obsoleted' => 0,
            'timeline_events_created' => 0,
        ];
        $sourcePaths = $items
            ->pluck('sourcePath')
            ->filter()
            ->unique()
            ->values();
        $seenSourceSignatures = $items
            ->pluck('sourceSignature')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($sourcePaths->isEmpty()) {
            return $result;
        }

        $sourceFileIds = TranslationWorkbenchSourceFile::query()
            ->whereIn('path', $sourcePaths->all())
            ->pluck('id');

        if ($sourceFileIds->isEmpty()) {
            return $result;
        }

        $missingFindingsQuery = TranslationWorkbenchFinding::query()
            ->whereIn('source_file_id', $sourceFileIds->all())
            ->where('status', 'active');

        if ($seenSourceSignatures !== []) {
            $missingFindingsQuery->whereNotIn('source_signature', $seenSourceSignatures);
        }

        $missingFindings = $missingFindingsQuery->get();

        foreach ($missingFindings as $finding) {
            $oldFindingValues = $finding->only(['status', 'meta']);

            $finding->forceFill([
                'status' => 'obsolete',
                'last_seen_at' => $now,
                'meta' => [
                    ...($finding->meta ?? []),
                    'obsolete_reason' => 'not_seen_in_latest_foundation_sync',
                ],
            ])->save();

            $this->timelineRecorder->recordFindingEvent(
                finding: $finding,
                eventType: 'finding_obsoleted',
                oldValues: $oldFindingValues,
                newValues: [
                    'status' => 'obsolete',
                    'obsolete_reason' => 'not_seen_in_latest_foundation_sync',
                ],
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'reason' => 'not_seen_in_latest_foundation_sync',
                ],
            );

            $result['findings_obsoleted']++;
            $result['timeline_events_created']++;

            $relations = TranslationWorkbenchKeyFinding::query()
                ->with('key')
                ->where('finding_id', $finding->id)
                ->where('status', 'active')
                ->get();

            foreach ($relations as $relation) {
                $oldRelationValues = $relation->only(['status', 'meta']);

                $relation->forceFill([
                    'status' => 'obsolete',
                    'meta' => [
                        ...($relation->meta ?? []),
                        'obsolete_reason' => 'finding_not_seen_in_latest_foundation_sync',
                    ],
                ])->save();

                if ($relation->key) {
                    $this->timelineRecorder->recordKeyFindingEvent(
                        key: $relation->key,
                        finding: $finding,
                        eventType: 'key_finding_relation_obsoleted',
                        oldValues: $oldRelationValues,
                        newValues: [
                            'status' => 'obsolete',
                            'obsolete_reason' => 'finding_not_seen_in_latest_foundation_sync',
                        ],
                        context: [
                            'source' => 'translation-workbench:sync-foundation',
                            'reason' => 'finding_not_seen_in_latest_foundation_sync',
                        ],
                    );

                    $result['relations_obsoleted']++;
                    $result['timeline_events_created']++;

                    if ($this->keyHasNoActiveNonObsoleteFindings($relation->key)) {
                        $oldKeyValues = $relation->key->only(['status', 'meta']);

                        $relation->key->forceFill([
                            'status' => 'obsolete',
                            'meta' => [
                                ...($relation->key->meta ?? []),
                                'obsolete_reason' => 'no_active_findings_after_foundation_sync',
                            ],
                        ])->save();

                        $this->timelineRecorder->recordKeyEvent(
                            key: $relation->key,
                            eventType: 'key_candidate_obsoleted',
                            oldValues: $oldKeyValues,
                            newValues: [
                                'status' => 'obsolete',
                                'obsolete_reason' => 'no_active_findings_after_foundation_sync',
                            ],
                            context: [
                                'source' => 'translation-workbench:sync-foundation',
                                'reason' => 'no_active_findings_after_foundation_sync',
                                'finding_id' => $finding->id,
                            ],
                        );

                        $result['keys_obsoleted']++;
                        $result['timeline_events_created']++;
                    }
                }
            }
        }

        return $result;
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

            $cleanup = $this->obsoleteObsoleteSiblingKeyRelations($key, $finding);

            return [
                'result' => 'relations_created',
                'relations_obsoleted' => $cleanup['relations_obsoleted'],
                'timeline_events_created' => 1 + $cleanup['timeline_events_created'],
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

        $cleanup = $this->obsoleteObsoleteSiblingKeyRelations($key, $finding);

        return [
            'result' => 'relations_updated',
            'relations_obsoleted' => $cleanup['relations_obsoleted'],
            'timeline_events_created' => ($changed !== [] ? 1 : 0) + $cleanup['timeline_events_created'],
        ];
    }

    /**
     * @return array{relations_obsoleted: int, timeline_events_created: int}
     */
    private function obsoleteObsoleteSiblingKeyRelations(
        TranslationWorkbenchKey $key,
        TranslationWorkbenchFinding $finding,
    ): array {
        $result = [
            'relations_obsoleted' => 0,
            'timeline_events_created' => 0,
        ];

        $relations = TranslationWorkbenchKeyFinding::query()
            ->with('key')
            ->where('finding_id', $finding->id)
            ->where('relation_type', 'candidate')
            ->where('status', 'active')
            ->where('key_id', '!=', $key->id)
            ->get()
            ->filter(static fn(TranslationWorkbenchKeyFinding $relation): bool => $relation->key?->status === 'obsolete');

        foreach ($relations as $relation) {
            $oldRelationValues = $relation->only(['status', 'meta']);

            $relation->forceFill([
                'status' => 'obsolete',
                'meta' => [
                    ...($relation->meta ?? []),
                    'obsolete_reason' => 'active_finding_relinked_to_non_obsolete_key',
                    'replacement_key_id' => $key->id,
                ],
            ])->save();

            $this->timelineRecorder->recordKeyFindingEvent(
                key: $relation->key,
                finding: $finding,
                eventType: 'key_finding_relation_obsoleted',
                oldValues: $oldRelationValues,
                newValues: [
                    'status' => 'obsolete',
                    'obsolete_reason' => 'active_finding_relinked_to_non_obsolete_key',
                    'replacement_key_id' => $key->id,
                ],
                context: [
                    'source' => 'translation-workbench:sync-foundation',
                    'reason' => 'active_finding_relinked_to_non_obsolete_key',
                    'replacement_key_id' => $key->id,
                ],
            );

            $result['relations_obsoleted']++;
            $result['timeline_events_created']++;
        }

        return $result;
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

    private function dynamicDataState(DiscoveredTranslation $item): ?string
    {
        if ($item->entryType === 'dynamic_numeric' || $item->kind === 'dynamic_numeric') {
            return null;
        }

        $isDynamic = $item->candidateType === 'dynamic'
            || $item->entryType === 'dynamic'
            || str_starts_with($item->kind, 'dynamic')
            || $this->dynamicScope($item) !== null;

        return $isDynamic ? 'unstructured' : null;
    }

    private function hasFindingDynamicDataStateColumn(): bool
    {
        static $hasColumn = null;

        return $hasColumn ??= Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state');
    }

    private function hasKeyDynamicDataStateColumn(): bool
    {
        static $hasColumn = null;

        return $hasColumn ??= Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state');
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
