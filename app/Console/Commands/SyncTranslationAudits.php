<?php

// app/Console/Commands/SyncTranslationAudits.php

namespace App\Console\Commands;

use App\Models\TranslationKey;
use App\Models\TranslationUsage;
use App\Models\TranslationValue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

#[Signature('translations:sync-audits')]
#[Description('Sync translation audit JSON files into translation management tables.')]
/**
 * Synchronizes translation audit outputs into translation management tables.
 */
class SyncTranslationAudits extends Command
{
    private const DEFAULT_LOCALES = ['de', 'en'];

    /**
     * @var array<int, string>
     */
    private array $seenUsageFingerprints = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $langValues = $this->loadLangValues();
        $locales = $this->loadLocales($langValues);

        $seenFingerprints = [];
        $this->seenUsageFingerprints = [];

        $counters = [
            'ok' => 0,
            'missing' => 0,
            'obsolete' => 0,
            'native' => 0,
            'dynamic' => 0,
            'invalid' => 0,
            'values' => 0,
            'usages' => 0,
        ];

        foreach ($this->readList('compare', 'ok') as $entry) {
            $key = $this->syncKeyEntry(
                fingerprint: $this->fingerprint('key:' . $entry['full_key']),
                key: $entry['full_key'],
                status: 'ok',
                classification: 'key',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['ok']++;

            foreach ($entry['usages'] ?? [] as $usage) {
                $this->syncUsage($key, $usage, 'key');
                $counters['usages']++;
            }

            foreach ($locales as $locale) {
                $this->syncValue(
                    translationKey: $key,
                    locale: $locale,
                    value: $langValues[$entry['full_key']][$locale] ?? null,
                    status: array_key_exists($locale, $langValues[$entry['full_key']] ?? []) ? 'ok' : 'missing',
                );

                $counters['values']++;
            }
        }

        foreach ($this->readList('compare', 'missing') as $entry) {
            $key = $this->syncKeyEntry(
                fingerprint: $this->fingerprint('key:' . $entry['full_key']),
                key: $entry['full_key'],
                status: 'missing',
                classification: 'key',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['missing']++;

            foreach ($entry['usages'] ?? [] as $usage) {
                $this->syncUsage($key, $usage, 'key');
                $counters['usages']++;
            }

            foreach ($locales as $locale) {
                $this->syncValue(
                    translationKey: $key,
                    locale: $locale,
                    value: $langValues[$entry['full_key']][$locale] ?? null,
                    status: array_key_exists($locale, $langValues[$entry['full_key']] ?? []) ? 'ok' : 'missing',
                );

                $counters['values']++;
            }
        }

        foreach ($this->readList('compare', 'obsolete') as $entry) {
            $key = $this->syncKeyEntry(
                fingerprint: $this->fingerprint('key:' . $entry['full_key']),
                key: $entry['full_key'],
                status: 'obsolete',
                classification: 'key',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['obsolete']++;

            foreach ($locales as $locale) {
                $this->syncValue(
                    translationKey: $key,
                    locale: $locale,
                    value: $langValues[$entry['full_key']][$locale] ?? null,
                    status: array_key_exists($locale, $langValues[$entry['full_key']] ?? []) ? 'obsolete' : 'missing',
                );

                $counters['values']++;
            }
        }

        foreach ($this->readList('compare', 'native') as $entry) {
            $fingerprint = $this->fingerprint(implode('|', [
                'native',
                $entry['file'] ?? '',
                $entry['line'] ?? '',
                $entry['function'] ?? '',
                $entry['value'] ?? '',
            ]));

            $key = $this->syncKeyEntry(
                fingerprint: $fingerprint,
                key: null,
                status: 'native',
                classification: 'native',
                suggestedKey: $entry['suggested_key'] ?? null,
                nativeText: $entry['value'] ?? null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['native']++;

            $this->syncUsage($key, $entry, 'native');
            $counters['usages']++;
        }

        foreach ($this->readList('compare', 'dynamic') as $entry) {
            $fingerprint = $this->fingerprint(implode('|', [
                'dynamic',
                $entry['file'] ?? '',
                $entry['line'] ?? '',
                $entry['function'] ?? '',
                $entry['raw'] ?? '',
            ]));

            $key = $this->syncKeyEntry(
                fingerprint: $fingerprint,
                key: null,
                status: 'dynamic',
                classification: 'dynamic',
                suggestedKey: $entry['suggested_key'] ?? null,
                nativeText: $entry['value'] ?? null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['dynamic']++;

            $this->syncUsage($key, $entry, 'dynamic');
            $counters['usages']++;
        }

        foreach ($this->readInvalidEntries() as $entry) {
            $fingerprint = $this->fingerprint(implode('|', [
                'invalid',
                $entry['scope'] ?? '',
                $entry['file'] ?? '',
                $entry['line'] ?? '',
                $entry['raw'] ?? '',
                $entry['reason'] ?? '',
            ]));

            $key = $this->syncKeyEntry(
                fingerprint: $fingerprint,
                key: $entry['full_key'] ?? null,
                status: 'invalid',
                classification: 'invalid',
                suggestedKey: $this->suggestKeyForExistingKey($entry['full_key'] ?? null),
                nativeText: $entry['value'] ?? null,
                now: $now,
            );

            $seenFingerprints[] = $key->fingerprint;
            $counters['invalid']++;

            if (($entry['file'] ?? null) !== null) {
                $this->syncUsage($key, $entry, 'invalid');
                $counters['usages']++;
            }
        }

        $staleUsageCount = $this->markStaleAuditUsages($this->seenUsageFingerprints, $now);
        $staleCount = $this->markStaleAuditKeys($seenFingerprints, $now);
        $mergedDuplicateKeyCount = $this->normalizeDuplicateKeyRows($now);
        $supersededNativeCount = $this->normalizeNativeRowsSupersededByKeys($now);
        $normalizedNonKeyObsoleteCount = $this->normalizeLegacyNonKeyObsoleteStatuses($now);

        $this->components->info('Translation audits synced.');

        $this->table(
            ['Status', 'Count'],
            [
                ['OK keys', $counters['ok']],
                ['Missing keys', $counters['missing']],
                ['Obsolete keys', $counters['obsolete']],
                ['Native entries', $counters['native']],
                ['Dynamic entries', $counters['dynamic']],
                ['Invalid entries', $counters['invalid']],
                ['Values synced', $counters['values']],
                ['Usages synced', $counters['usages']],
                ['Stale audit usages marked', $staleUsageCount],
                ['Stale audit keys marked', $staleCount],
                ['Duplicate key rows merged', $mergedDuplicateKeyCount],
                ['Native rows superseded by key', $supersededNativeCount],
                ['Legacy non-key obsolete normalized', $normalizedNonKeyObsoleteCount],
            ],
        );

        $this->logRunCompletedActivity($counters, $staleUsageCount, $staleCount, $locales);

        return self::SUCCESS;
    }

    private function syncKeyEntry(
        string $fingerprint,
        ?string $key,
        string $status,
        string $classification,
        ?string $suggestedKey,
        ?string $nativeText,
        mixed $now,
    ): TranslationKey {
        $translationKey = $this->resolveTranslationKeyForSync(
            fingerprint: $fingerprint,
            incomingKey: $key,
            incomingClassification: $classification,
        );

        $wasExisting = $translationKey->exists;
        $oldStatus = $translationKey->status;
        $oldClassification = $translationKey->classification;
        $oldNativeText = $translationKey->native_text;
        $oldObsoleteAt = $translationKey->obsolete_at;

        if (! $translationKey->exists) {
            $translationKey->first_seen_at = $now;
        }

        $resolvedKey = $key;

        if (($resolvedKey === null || trim($resolvedKey) === '') && $translationKey->exists) {
            $existingKey = trim((string) ($translationKey->key ?? ''));

            if ($existingKey !== '') {
                // Preserve manually assigned keys when incoming audit records represent
                // native/dynamic entries that do not carry a concrete translation key.
                $resolvedKey = $existingKey;
            }
        }

        $resolvedClassification = $this->resolveClassificationForSync(
            currentClassification: $oldClassification,
            incomingClassification: $classification,
            currentNativeText: $oldNativeText,
            incomingNativeText: $nativeText,
            incomingKey: $key,
            resolvedKey: $resolvedKey,
        );

        $resolvedStatus = $this->resolveStatusForSync(
            currentStatus: $oldStatus,
            incomingStatus: $status,
            incomingClassification: $classification,
            incomingKey: $key,
            resolvedKey: $resolvedKey,
        );

        $resolvedObsoleteAt = $resolvedStatus === 'obsolete'
            ? ($translationKey->obsolete_at ?? $now)
            : null;

        $attributes = [
            'fingerprint' => $fingerprint,
            'key' => $resolvedKey,
            'namespace' => $this->namespaceFromKey($resolvedKey ?? $suggestedKey),
            'group' => $this->groupFromKey($resolvedKey ?? $suggestedKey),
            'status' => $resolvedStatus,
            'classification' => $resolvedClassification,
            'source' => 'audit',
            'suggested_key' => $suggestedKey,
            'last_seen_at' => $now,
            'obsolete_at' => $resolvedObsoleteAt,
        ];

        if ($oldStatus === 'obsolete' && $resolvedStatus !== 'obsolete') {
            $attributes['workflow_status'] = 'open';
            $attributes['reviewed_at'] = null;
            $attributes['reviewed_by_user_id'] = null;
            $attributes['review_note'] = null;
        }

        if ($nativeText !== null) {
            $attributes['native_text'] = $nativeText;
        }

        $translationKey->fill($attributes);

        $translationKey->save();

        if ($resolvedClassification === 'key' && $resolvedKey !== null && trim($resolvedKey) !== '') {
            $translationKey = $this->mergeDuplicateKeyRows($translationKey, $resolvedKey, $now);
        }

        if ($wasExisting && $oldStatus === 'obsolete' && $resolvedStatus !== 'obsolete') {
            $this->createAuditEvent([
                'translation_key_id' => $translationKey->id,
                'entity_type' => 'translation_key',
                'event_type' => 'reactivated',
                'old_fingerprint' => $translationKey->fingerprint,
                'new_fingerprint' => $translationKey->fingerprint,
                'old_key' => $translationKey->getOriginal('key'),
                'new_key' => $translationKey->key,
                'old_status' => $oldStatus,
                'new_status' => $resolvedStatus,
                'reason' => 'audit_key_seen_again_in_latest_sync',
                'context' => [
                    'old_obsolete_at' => $oldObsoleteAt ? (string) $oldObsoleteAt : null,
                ],
            ]);
        }

        if ($wasExisting && $nativeText !== null && $oldNativeText !== $nativeText) {
            $this->createAuditEvent([
                'translation_key_id' => $translationKey->id,
                'entity_type' => 'translation_key',
                'event_type' => $oldNativeText === null ? 'native_text_filled' : 'native_text_changed',
                'old_fingerprint' => $translationKey->fingerprint,
                'new_fingerprint' => $translationKey->fingerprint,
                'old_key' => $translationKey->key,
                'new_key' => $translationKey->key,
                'old_value' => $oldNativeText,
                'new_value' => $nativeText,
                'old_status' => $oldStatus,
                'new_status' => $translationKey->status,
                'reason' => 'native_text_synced_from_audit_source',
            ]);
        }

        return $translationKey;
    }

    private function resolveTranslationKeyForSync(
        string $fingerprint,
        ?string $incomingKey,
        string $incomingClassification,
    ): TranslationKey {
        $existingByFingerprint = TranslationKey::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($existingByFingerprint) {
            return $existingByFingerprint;
        }

        $normalizedKey = trim((string) $incomingKey);

        if ($incomingClassification === 'key' && $normalizedKey !== '') {
            $existingByKey = TranslationKey::query()
                ->where('classification', 'key')
                ->where('key', $normalizedKey)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            if ($existingByKey) {
                return $existingByKey;
            }
        }

        return new TranslationKey([
            'fingerprint' => $fingerprint,
        ]);
    }

    private function mergeDuplicateKeyRows(TranslationKey $primary, string $resolvedKey, mixed $now): TranslationKey
    {
        $duplicates = TranslationKey::query()
            ->where('classification', 'key')
            ->where('key', $resolvedKey)
            ->whereKeyNot($primary->id)
            ->orderBy('id')
            ->get();

        if ($duplicates->isEmpty()) {
            return $primary;
        }

        foreach ($duplicates as $duplicate) {
            $this->mergeDuplicateValuesIntoPrimary($primary, $duplicate);

            TranslationUsage::query()
                ->where('translation_key_id', $duplicate->id)
                ->update(['translation_key_id' => $primary->id]);

            DB::table('translation_audit_events')
                ->where('translation_key_id', $duplicate->id)
                ->update(['translation_key_id' => $primary->id]);

            $primary->forceFill([
                'first_seen_at' => $this->earlierTimestamp($primary->first_seen_at, $duplicate->first_seen_at),
                'last_seen_at' => $this->laterTimestamp($primary->last_seen_at, $duplicate->last_seen_at ?? $now),
                'native_text' => $this->preferNonEmptyString($primary->native_text, $duplicate->native_text),
                'suggested_key' => $this->preferNonEmptyString($primary->suggested_key, $duplicate->suggested_key),
                'workflow_status' => $this->preferOpenWorkflowStatus($primary->workflow_status, $duplicate->workflow_status),
                'reviewed_at' => $primary->workflow_status === 'reviewed'
                    ? $this->laterTimestamp($primary->reviewed_at, $duplicate->reviewed_at)
                    : null,
                'reviewed_by_user_id' => $primary->workflow_status === 'reviewed'
                    ? ($primary->reviewed_by_user_id ?? $duplicate->reviewed_by_user_id)
                    : null,
                'review_note' => $primary->workflow_status === 'reviewed'
                    ? $this->preferNonEmptyString($primary->review_note, $duplicate->review_note)
                    : null,
            ])->save();

            $duplicate->delete();
        }

        return $primary->refresh();
    }

    private function mergeDuplicateValuesIntoPrimary(TranslationKey $primary, TranslationKey $duplicate): void
    {
        $duplicateValues = TranslationValue::query()
            ->where('translation_key_id', $duplicate->id)
            ->orderBy('id')
            ->get();

        foreach ($duplicateValues as $duplicateValue) {
            $primaryValue = TranslationValue::query()
                ->where('translation_key_id', $primary->id)
                ->where('locale', $duplicateValue->locale)
                ->first();

            if (! $primaryValue) {
                $duplicateValue->translation_key_id = $primary->id;
                $duplicateValue->save();

                continue;
            }

            $primaryValue->forceFill([
                'value' => $this->preferNonEmptyString($primaryValue->value, $duplicateValue->value),
                'status' => $this->preferValueStatus($primaryValue->status, $duplicateValue->status),
                'source' => $this->preferManualSource($primaryValue->source, $duplicateValue->source),
                'reviewed_at' => $this->laterTimestamp($primaryValue->reviewed_at, $duplicateValue->reviewed_at),
                'reviewed_by_user_id' => $primaryValue->reviewed_by_user_id ?? $duplicateValue->reviewed_by_user_id,
            ])->save();

            $duplicateValue->delete();
        }
    }

    private function preferNonEmptyString(?string $preferred, ?string $fallback): ?string
    {
        $preferredValue = trim((string) ($preferred ?? ''));

        if ($preferredValue !== '') {
            return $preferred;
        }

        $fallbackValue = trim((string) ($fallback ?? ''));

        return $fallbackValue !== '' ? $fallback : $preferred;
    }

    private function preferValueStatus(?string $primaryStatus, ?string $duplicateStatus): string
    {
        $priority = [
            'ok' => 4,
            'obsolete' => 3,
            'missing' => 2,
            'dynamic' => 1,
            'invalid' => 0,
        ];

        $left = $priority[$primaryStatus ?? ''] ?? -1;
        $right = $priority[$duplicateStatus ?? ''] ?? -1;

        return $left >= $right
            ? (string) ($primaryStatus ?? 'missing')
            : (string) ($duplicateStatus ?? 'missing');
    }

    private function preferManualSource(?string $primarySource, ?string $duplicateSource): string
    {
        if (($primarySource ?? '') === 'manual' || ($duplicateSource ?? '') !== 'manual') {
            return (string) ($primarySource ?? 'audit');
        }

        return 'manual';
    }

    private function preferOpenWorkflowStatus(?string $primaryWorkflowStatus, ?string $duplicateWorkflowStatus): string
    {
        return ($primaryWorkflowStatus ?? 'open') === 'open' || ($duplicateWorkflowStatus ?? 'open') !== 'open'
            ? (string) ($primaryWorkflowStatus ?? 'open')
            : (string) ($duplicateWorkflowStatus ?? 'open');
    }

    private function earlierTimestamp(mixed $left, mixed $right): mixed
    {
        if ($left === null) {
            return $right;
        }

        if ($right === null) {
            return $left;
        }

        return $left <= $right ? $left : $right;
    }

    private function laterTimestamp(mixed $left, mixed $right): mixed
    {
        if ($left === null) {
            return $right;
        }

        if ($right === null) {
            return $left;
        }

        return $left >= $right ? $left : $right;
    }

    private function normalizeDuplicateKeyRows(mixed $now): int
    {
        $duplicateKeys = TranslationKey::query()
            ->where('classification', 'key')
            ->whereNotNull('key')
            ->where('key', '!=', '')
            ->groupBy('key')
            ->havingRaw('count(*) > 1')
            ->pluck('key');

        $mergedCount = 0;

        foreach ($duplicateKeys as $resolvedKey) {
            $rows = TranslationKey::query()
                ->where('classification', 'key')
                ->where('key', $resolvedKey)
                ->orderByRaw("case when status = 'ok' then 0 when status = 'missing' then 1 when status = 'obsolete' then 2 else 3 end")
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get();

            $primary = $rows->first();

            if (! $primary || $rows->count() < 2) {
                continue;
            }

            $mergedCount += $rows->count() - 1;
            $this->mergeDuplicateKeyRows($primary, (string) $resolvedKey, $now);
        }

        return $mergedCount;
    }

    private function normalizeNativeRowsSupersededByKeys(mixed $now): int
    {
        $nativeRows = TranslationKey::query()
            ->where('classification', 'native')
            ->where('workflow_status', 'open')
            ->whereNotNull('suggested_key')
            ->where('suggested_key', '!=', '')
            ->get();

        $count = 0;

        foreach ($nativeRows as $nativeRow) {
            $matchedKey = TranslationKey::query()
                ->where('classification', 'key')
                ->where('key', $nativeRow->suggested_key)
                ->orderByRaw("case when status = 'ok' then 0 when status = 'missing' then 1 when status = 'obsolete' then 2 else 3 end")
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            if (! $matchedKey) {
                continue;
            }

            $oldWorkflowStatus = (string) ($nativeRow->workflow_status ?? 'open');

            $nativeRow->forceFill([
                'workflow_status' => 'reviewed',
                'reviewed_at' => $nativeRow->reviewed_at ?? $now,
                'reviewed_by_user_id' => $nativeRow->reviewed_by_user_id,
                'review_note' => 'superseded_by_key_sync',
            ])->save();

            $this->createAuditEvent([
                'translation_key_id' => $nativeRow->id,
                'entity_type' => 'translation_key',
                'event_type' => 'workflow_status_changed',
                'old_fingerprint' => $nativeRow->fingerprint,
                'new_fingerprint' => $nativeRow->fingerprint,
                'old_key' => $nativeRow->key,
                'new_key' => $nativeRow->key,
                'old_value' => $oldWorkflowStatus,
                'new_value' => 'reviewed',
                'old_status' => $nativeRow->status,
                'new_status' => $nativeRow->status,
                'reason' => 'native_row_superseded_by_key_sync',
                'context' => [
                    'suggested_key' => $nativeRow->suggested_key,
                    'superseding_translation_key_id' => $matchedKey->id,
                    'superseding_key' => $matchedKey->key,
                    'superseding_status' => $matchedKey->status,
                    'old_workflow_status' => $oldWorkflowStatus,
                    'new_workflow_status' => 'reviewed',
                ],
            ]);

            $count++;
        }

        return $count;
    }

    private function resolveClassificationForSync(
        ?string $currentClassification,
        string $incomingClassification,
        ?string $currentNativeText,
        ?string $incomingNativeText,
        ?string $incomingKey,
        ?string $resolvedKey,
    ): string {
        $incomingKeyIsEmpty = $incomingKey === null || trim($incomingKey) === '';
        $resolvedKeyIsSet = $resolvedKey !== null && trim($resolvedKey) !== '';

        if (
            in_array($incomingClassification, ['native', 'dynamic'], true)
            && $incomingKeyIsEmpty
            && $resolvedKeyIsSet
        ) {
            return 'key';
        }

        if (
            $currentClassification === 'key'
            && $incomingClassification !== 'key'
            && $resolvedKeyIsSet
        ) {
            return 'key';
        }

        if (
            $currentClassification === 'backfill_by_translation'
            && $incomingClassification === 'key'
            && $incomingNativeText === null
            && $currentNativeText !== null
            && trim($currentNativeText) !== ''
        ) {
            return $currentClassification;
        }

        return $incomingClassification;
    }

    private function resolveStatusForSync(
        ?string $currentStatus,
        string $incomingStatus,
        string $incomingClassification,
        ?string $incomingKey,
        ?string $resolvedKey,
    ): string {
        $currentStatus = trim((string) ($currentStatus ?? ''));
        $incomingKeyIsEmpty = $incomingKey === null || trim($incomingKey) === '';
        $resolvedKeyIsSet = $resolvedKey !== null && trim($resolvedKey) !== '';
        $keyStatuses = ['ok', 'missing', 'obsolete'];

        if (
            in_array($incomingClassification, ['native', 'dynamic'], true)
            && $incomingKeyIsEmpty
            && $resolvedKeyIsSet
        ) {
            if (in_array($currentStatus, $keyStatuses, true)) {
                return $currentStatus;
            }

            return 'missing';
        }

        return $incomingStatus;
    }

    private function syncValue(
        TranslationKey $translationKey,
        string $locale,
        ?string $value,
        string $status,
    ): void {
        $translationValue = TranslationValue::query()->firstOrNew([
            'translation_key_id' => $translationKey->id,
            'locale' => $locale,
        ]);

        if ($translationValue->source !== 'manual') {
            $translationValue->fill([
                'value' => $value,
                'status' => $status,
                'source' => 'audit',
            ]);

            $translationValue->save();
        }
    }

    private function syncUsage(TranslationKey $translationKey, array $entry, string $classification): void
    {
        $raw = $entry['raw'] ?? null;
        $file = (string) ($entry['file'] ?? '');
        $line = $entry['line'] ?? null;
        $function = $entry['function'] ?? null;

        $fingerprint = $this->fingerprint(implode('|', [
            $this->usageIdentity($translationKey),
            $file,
            $line ?? '',
            $function ?? '',
        ]));

        $translationUsage = TranslationUsage::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        $eventType = null;
        $oldFingerprint = null;
        $oldFile = null;
        $oldLine = null;
        $oldReason = null;

        if (! $translationUsage) {
            $translationUsage = TranslationUsage::query()
                ->where('translation_key_id', $translationKey->id)
                ->where('classification', $classification)
                ->where('function', $function)
                ->where(function ($query) use ($raw) {
                    if ($raw === null) {
                        $query
                            ->whereNull('raw')
                            ->orWhereNull('original_raw');

                        return;
                    }

                    $query
                        ->where('raw', $raw)
                        ->orWhere('original_raw', $raw);
                })
                ->first();

            if ($translationUsage) {
                $oldFingerprint = $translationUsage->fingerprint;
                $oldFile = $translationUsage->file;
                $oldLine = $translationUsage->line;
                $oldReason = $translationUsage->reason;

                $eventType = ($oldFile !== $file || $oldLine !== $line)
                    ? 'moved'
                    : 'fingerprint_changed';

                $translationUsage->fingerprint = $fingerprint;
            }
        }

        if (! $translationUsage) {
            $translationUsage = new TranslationUsage([
                'fingerprint' => $fingerprint,
            ]);
        }

        if (! $translationUsage->exists || blank($translationUsage->original_raw)) {
            $translationUsage->original_raw = $raw;
        }

        if ($translationUsage->exists && $translationUsage->reason === 'stale_audit_usage_not_seen_in_latest_sync') {
            $eventType ??= 'reactivated';
            $oldFingerprint ??= $translationUsage->fingerprint;
            $oldFile ??= $translationUsage->file;
            $oldLine ??= $translationUsage->line;
            $oldReason ??= $translationUsage->reason;
        }

        $translationUsage->fill([
            'translation_key_id' => $translationKey->id,
            'file' => $file,
            'line' => $line,
            'function' => $function,
            'classification' => $classification,
            'reason' => $entry['reason'] ?? null,
            'raw' => $raw,
        ]);

        $translationUsage->save();

        if ($eventType !== null) {
            $this->createAuditEvent([
                'translation_key_id' => $translationKey->id,
                'translation_usage_id' => $translationUsage->id,
                'entity_type' => 'translation_usage',
                'event_type' => $eventType,
                'old_fingerprint' => $oldFingerprint,
                'new_fingerprint' => $translationUsage->fingerprint,
                'old_file' => $oldFile,
                'new_file' => $translationUsage->file,
                'old_line' => $oldLine,
                'new_line' => $translationUsage->line,
                'old_key' => $translationKey->getOriginal('key'),
                'new_key' => $translationKey->key,
                'old_value' => $oldReason,
                'new_value' => $translationUsage->reason,
                'old_status' => null,
                'new_status' => null,
                'reason' => match ($eventType) {
                    'moved' => 'same_usage_seen_at_different_code_position',
                    'reactivated' => 'stale_usage_seen_again_in_latest_sync',
                    default => 'same_usage_seen_with_different_fingerprint',
                },
                'context' => [
                    'raw' => $raw,
                    'function' => $function,
                    'classification' => $classification,
                ],
            ]);
        }

        $this->seenUsageFingerprints[] = $fingerprint;
    }

    private function usageIdentity(TranslationKey $translationKey): string
    {
        $identity = $translationKey->key
            ?: $translationKey->suggested_key
            ?: $translationKey->fingerprint;

        return (string) $identity;
    }

    private function markStaleAuditUsages(array $seenFingerprints, mixed $now): int
    {
        if ($seenFingerprints === []) {
            return 0;
        }

        $staleUsages = TranslationUsage::query()
            ->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints)), 'and')
            ->where(function ($query) {
                $query
                    ->whereNull('reason')
                    ->orWhere('reason', '!=', 'stale_audit_usage_not_seen_in_latest_sync');
            })
            ->get();

        foreach ($staleUsages as $usage) {
            $this->createAuditEvent([
                'translation_key_id' => $usage->translation_key_id,
                'translation_usage_id' => $usage->id,
                'entity_type' => 'translation_usage',
                'event_type' => 'stale_marked',
                'old_fingerprint' => $usage->fingerprint,
                'new_fingerprint' => $usage->fingerprint,
                'old_file' => $usage->file,
                'new_file' => $usage->file,
                'old_line' => $usage->line,
                'new_line' => $usage->line,
                'old_value' => $usage->reason,
                'new_value' => 'stale_audit_usage_not_seen_in_latest_sync',
                'reason' => 'usage_not_seen_in_latest_audit_sync',
                'context' => [
                    'raw' => $usage->raw,
                    'original_raw' => $usage->original_raw,
                    'function' => $usage->function,
                    'classification' => $usage->classification,
                ],
            ]);

            $usage->forceFill([
                'reason' => 'stale_audit_usage_not_seen_in_latest_sync',
                'updated_at' => $now,
            ])->save();
        }

        return $staleUsages->count();
    }

    private function markStaleAuditKeys(array $seenFingerprints, mixed $now): int
    {
        if ($seenFingerprints === []) {
            return 0;
        }

        $staleKeys = TranslationKey::query()
            ->where('source', 'audit')
            ->where('classification', 'key')
            ->whereNotNull('key', 'and')
            ->whereNotIn('fingerprint', array_values(array_unique($seenFingerprints)), 'and')
            ->where(function ($query) {
                $query
                    ->where('status', '!=', 'obsolete')
                    ->orWhereNull('obsolete_at');
            })
            ->get();

        foreach ($staleKeys as $key) {
            $this->createAuditEvent([
                'translation_key_id' => $key->id,
                'entity_type' => 'translation_key',
                'event_type' => 'stale_marked',
                'old_fingerprint' => $key->fingerprint,
                'new_fingerprint' => $key->fingerprint,
                'old_key' => $key->key,
                'new_key' => $key->key,
                'old_value' => $key->native_text,
                'new_value' => $key->native_text,
                'old_status' => $key->status,
                'new_status' => 'obsolete',
                'reason' => 'key_not_seen_in_latest_audit_sync',
                'context' => [
                    'classification' => $key->classification,
                    'suggested_key' => $key->suggested_key,
                    'last_seen_at' => $key->last_seen_at ? (string) $key->last_seen_at : null,
                ],
            ]);

            $key->forceFill([
                'status' => 'obsolete',
                'obsolete_at' => $key->obsolete_at ?? $now,
                'updated_at' => $now,
            ])->save();
        }

        return $staleKeys->count();
    }

    private function normalizeLegacyNonKeyObsoleteStatuses(mixed $now): int
    {
        $legacyRows = TranslationKey::query()
            ->where('source', 'audit')
            ->where('status', 'obsolete')
            ->where(function ($query) {
                $query
                    ->whereNull('key')
                    ->orWhereIn('classification', ['native', 'dynamic', 'invalid']);
            })
            ->get();

        foreach ($legacyRows as $key) {
            $newStatus = match ($key->classification) {
                'dynamic' => 'dynamic',
                'invalid' => 'invalid',
                default => 'native',
            };

            $this->createAuditEvent([
                'translation_key_id' => $key->id,
                'entity_type' => 'translation_key',
                'event_type' => 'legacy_status_normalized',
                'old_fingerprint' => $key->fingerprint,
                'new_fingerprint' => $key->fingerprint,
                'old_key' => $key->key,
                'new_key' => $key->key,
                'old_value' => $key->native_text,
                'new_value' => $key->native_text,
                'old_status' => $key->status,
                'new_status' => $newStatus,
                'reason' => 'non_key_audit_entry_removed_from_obsolete_bucket',
                'context' => [
                    'classification' => $key->classification,
                    'workflow_status' => $key->workflow_status,
                ],
            ]);

            $key->forceFill([
                'status' => $newStatus,
                'obsolete_at' => null,
                'workflow_status' => 'open',
                'reviewed_at' => null,
                'reviewed_by_user_id' => null,
                'review_note' => null,
                'updated_at' => $now,
            ])->save();
        }

        return $legacyRows->count();
    }

    private function createAuditEvent(array $payload): void
    {
        DB::table('translation_audit_events')->insert([
            'translation_key_id' => $payload['translation_key_id'] ?? null,
            'translation_usage_id' => $payload['translation_usage_id'] ?? null,
            'entity_type' => $payload['entity_type'],
            'event_type' => $payload['event_type'],
            'old_fingerprint' => $payload['old_fingerprint'] ?? null,
            'new_fingerprint' => $payload['new_fingerprint'] ?? null,
            'old_file' => $payload['old_file'] ?? null,
            'new_file' => $payload['new_file'] ?? null,
            'old_line' => $payload['old_line'] ?? null,
            'new_line' => $payload['new_line'] ?? null,
            'old_key' => $payload['old_key'] ?? null,
            'new_key' => $payload['new_key'] ?? null,
            'old_value' => $payload['old_value'] ?? null,
            'new_value' => $payload['new_value'] ?? null,
            'old_status' => $payload['old_status'] ?? null,
            'new_status' => $payload['new_status'] ?? null,
            'reason' => $payload['reason'] ?? null,
            'context' => isset($payload['context'])
                ? json_encode($payload['context'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readList(string $section, string $name): array
    {
        $path = storage_path('audits/translations/' . $section . '/' . $name . '.json');

        if (! File::isFile($path)) {
            return [];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload) || ! array_is_list($payload)) {
            return [];
        }

        return $payload;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readInvalidEntries(): array
    {
        $path = storage_path('audits/translations/compare/invalid.json');

        if (! File::isFile($path)) {
            return [];
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return [];
        }

        $entries = [];

        foreach (($payload['code'] ?? []) as $entry) {
            if (is_array($entry)) {
                $entry['scope'] = 'code';
                $entries[] = $entry;
            }
        }

        foreach (($payload['lang'] ?? []) as $entry) {
            if (is_array($entry)) {
                $entry['scope'] = 'lang';
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    private function loadLangValues(): array
    {
        $values = [];

        foreach ($this->readList('lang', 'keys') as $entry) {
            $fullKey = (string) ($entry['full_key'] ?? '');
            $locale = (string) ($entry['locale'] ?? '');
            $file = (string) ($entry['file'] ?? '');
            $key = (string) ($entry['key'] ?? '');

            if ($fullKey === '' || $locale === '' || $file === '' || $key === '') {
                continue;
            }

            $absolutePath = base_path($file);

            if (! File::isFile($absolutePath)) {
                continue;
            }

            $payload = require $absolutePath;

            if (! is_array($payload)) {
                continue;
            }

            $value = Arr::get($payload, $key);

            if (is_array($value)) {
                continue;
            }

            $values[$fullKey][$locale] = $value === null ? null : (string) $value;
        }

        return $values;
    }

    /**
     * @param  array<string, array<string, string|null>>  $langValues
     * @return array<int, string>
     */
    private function loadLocales(array $langValues): array
    {
        $locales = [];

        $localeSummary = $this->readList('lang', 'locales');

        foreach ($localeSummary as $entry) {
            if (($entry['locale'] ?? null) !== null) {
                $locales[] = (string) $entry['locale'];
            }
        }

        foreach ($langValues as $valuesByLocale) {
            foreach (array_keys($valuesByLocale) as $locale) {
                $locales[] = $locale;
            }
        }

        $locales = array_values(array_unique($locales));
        sort($locales);

        return $locales === [] ? self::DEFAULT_LOCALES : $locales;
    }

    private function suggestKeyForExistingKey(?string $key): ?string
    {
        if ($key === null) {
            return null;
        }

        $suggestedKey = str($key)
            ->trim()
            ->replace('\\', '.')
            ->replace('/', '.')
            ->replace('-', '_')
            ->replaceMatches('/(?<!^)[A-Z]/', '_$0')
            ->lower()
            ->replaceMatches('/[^a-z0-9_.]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->replaceMatches('/\.+/', '.')
            ->replaceMatches('/(^|\\.)_+/', '$1')
            ->replaceMatches('/_+(\\.|$)/', '$1')
            ->trim('._')
            ->toString();

        return $suggestedKey !== '' ? $suggestedKey : null;
    }

    private function namespaceFromKey(?string $key): ?string
    {
        if ($key === null || ! str_contains($key, '.')) {
            return $key;
        }

        return str($key)->before('.')->toString();
    }

    private function groupFromKey(?string $key): ?string
    {
        if ($key === null || ! str_contains($key, '.')) {
            return null;
        }

        $segments = explode('.', $key);

        return count($segments) >= 2 ? $segments[1] : null;
    }

    private function fingerprint(string $value): string
    {
        return hash('sha256', $value);
    }

    /**
     * @param array<string, int> $counters
     * @param array<int, string> $locales
     */
    private function logRunCompletedActivity(array $counters, int $staleUsageCount, int $staleCount, array $locales): void
    {
        try {
            activity('translations')
                ->event('translations.audit.sync.completed')
                ->withProperties([
                    'command' => $this->getName(),
                    'summary' => [
                        'locales' => $locales,
                        'counters' => $counters,
                        'stale_usage_marked' => $staleUsageCount,
                        'stale_keys_marked' => $staleCount,
                    ],
                ])
                ->log('Translation audit sync completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: ' . $exception->getMessage());
        }
    }
}
